<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Penjualan;
use App\Models\ItemPenjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(SearchRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $keyword = $request->input('search');

        $sales = Penjualan::query()
            ->when($user && optional($user->role)->name === 'kasir', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('penjualan.index', compact('sales'));
    }

    public function create(Request $request)
    {
        $sale = Penjualan::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'status'  => 'OPEN'
            ],
            [
                'total_pembayaran'  => 0,
                'metode_pembayaran' => 'CASH'
            ]
        );

        return redirect()->route('penjualan.edit', $sale->id);
    }

    public function store(Request $request)
    {
        Penjualan::create([
            'user_id'           => Auth::id(),
            'total_pembayaran'  => $request->input('total_pembayaran', 0),
            'metode_pembayaran' => $request->input('metode_pembayaran', 'CASH'),
            'status'            => $request->input('status', 'OPEN'),
        ]);

        return redirect()->route('penjualan.index')
            ->with('success', 'Penjualan berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $penjualan = Penjualan::with(['itemPenjualan.produk', 'user'])->findOrFail($id);

        return view('penjualan.show', compact('penjualan'));
    }

    public function edit(Request $request, Penjualan $penjualan)
    {
        $sale = $penjualan;
        $sale->load('itemPenjualan.produk');

        $keyword = $request->input('search');
        $products = Produk::when($keyword, function ($query) use ($keyword) {
            $query->where('nama', 'like', '%' . $keyword . '%');
        })->orderBy('nama')->get();

        $mode = 'edit';

        return view('penjualan.pos', compact('sale', 'products', 'mode'));
    }

    public function addItem(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'product_id' => 'required|exists:produk,id',
            'qty'        => 'required|integer|min:1'
        ]);

        $produk = Produk::findOrFail($request->product_id);

        if ($produk->stok < $request->qty) {
            return back()->with('errors', 'Stok produk tidak mencukupi!');
        }

        DB::transaction(function () use ($penjualan, $produk, $request) {
            $item = ItemPenjualan::where('penjualan_id', $penjualan->id)
                ->where('produk_id', $produk->id)
                ->first();

            if ($item) {
                $newQty = $item->kuantitas + $request->qty;
                $item->update([
                    'kuantitas' => $newQty,
                    'subtotal'  => $newQty * $produk->harga_jual
                ]);
            } else {
                ItemPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id'    => $produk->id,
                    'kuantitas'    => $request->qty,
                    'harga_satuan' => $produk->harga_jual,
                    'subtotal'     => $request->qty * $produk->harga_jual
                ]);
            }

            $total = $penjualan->itemPenjualan()->sum('subtotal');
            $penjualan->update(['total_pembayaran' => $total]);
        });

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function removeItem(ItemPenjualan $itemPenjualan)
    {
        $penjualan = $itemPenjualan->penjualan;

        DB::transaction(function () use ($itemPenjualan, $penjualan) {
            $itemPenjualan->delete();

            $total = $penjualan->itemPenjualan()->sum('subtotal');
            $penjualan->update(['total_pembayaran' => $total]);
        });

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Update specified resource (PROSES CHECKOUT)
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'payment_method' => 'required|in:CASH,QRIS,TRANSFER,Cash,QRIS,Transfer',
            'bayar'          => 'required|numeric|min:0'
        ]);

        if ($penjualan->status !== 'OPEN') {
            return back()->with('errors', 'Transaksi sudah diproses!');
        }

        if ($penjualan->itemPenjualan()->count() === 0) {
            return back()->with('errors', 'Keranjang masih kosong!');
        }

        $total = $penjualan->itemPenjualan()->sum('subtotal');

        if ($request->bayar < $total) {
            return back()->with('errors', 'Uang pembayaran kurang!');
        }

        DB::transaction(function () use ($penjualan, $request, $total) {
            foreach ($penjualan->itemPenjualan as $item) {
                if ($item->produk) {
                    $item->produk->decrement('stok', $item->kuantitas);
                }
            }

            $penjualan->update([
                'metode_pembayaran' => strtoupper($request->input('payment_method')),
                'total_pembayaran'  => $total,
                'status'            => 'COMPLETED'
            ]);
        });

        // Menyimpan nominal bayar dan kembalian sementara di session
        session([
            'bayar_' . $penjualan->id => $request->bayar,
            'kembalian_' . $penjualan->id => $request->bayar - $total
        ]);

        return redirect()
            ->route('penjualan.index')
            ->with('success', 'Transaksi berhasil diproses!')
            ->with('cetak_id', $penjualan->id);
    }

    /**
     * Tampilan Struk Transaksi
     */
    public function cetakStruk(Penjualan $penjualan)
    {
        $penjualan->load(['itemPenjualan.produk', 'user']);

        $bayar = session('bayar_' . $penjualan->id, $penjualan->total_pembayaran);
        $kembalian = session('kembalian_' . $penjualan->id, 0);

        return view('penjualan.struk', compact('penjualan', 'bayar', 'kembalian'));
    }

    public function destroy(Penjualan $penjualan)
    {
        if (method_exists($this, 'authorize')) {
            $this->authorize('delete', $penjualan);
        }

        if ($penjualan->status !== 'OPEN') {
            return redirect()
                ->route('penjualan.index')
                ->with('errors', 'Transaksi yang sudah selesai tidak bisa dibatalkan!');
        }

        DB::transaction(function () use ($penjualan) {
            $penjualan->itemPenjualan()->delete();
            $penjualan->delete();
        });

        return redirect()
            ->route('penjualan.index')
            ->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
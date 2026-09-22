<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Produk;
use App\Models\Jenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ProdukController extends Controller
{
    public function index(SearchRequest $request)
    {
        $keyword = $request->input('search');

        $query = Produk::with(['user', 'jenis']);

        if ($keyword) {
            $query->where('nama', 'like', '%' . $keyword . '%');
        }

        $products = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('produk.index', compact('products'));
    }

    public function create()
    {
        $jenisList = Jenis::all();
        $produk = new Produk(); // Dipassing agar _form.blade.php tidak error saat mode Create
        return view('produk.create', compact('jenisList', 'produk'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('products', 'public');
        }

        Produk::create($validated);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Produk $produk)
    {
        $jenisList = Jenis::all();
        return view('produk.edit', compact('produk', 'jenisList'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $this->validateProduct($request);

        if ($request->hasFile('foto')) {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $validated['foto'] = $request->file('foto')->store('products', 'public');
        }

        $produk->update($validated);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Produk $produk)
    {
        try {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }

            $produk->delete();

            return redirect()
                ->route('produk.index')
                ->with('success', 'Produk berhasil dihapus!');

        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1451)) {
                return redirect()
                    ->route('produk.index')
                    ->with('error', 'Produk tidak dapat dihapus karena sudah terikat transaksi!');
            }

            return redirect()
                ->route('produk.index')
                ->with('error', 'Gagal menghapus produk.');
        }
    }

    /**
     * Helper method internal untuk validasi input
     */
    private function validateProduct(Request $request): array
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_id'      => 'required|exists:jenis,id',
            'jenis_makanan' => 'nullable|string|max:255',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ((float) $validated['harga_beli'] > (float) $validated['harga_jual']) {
            throw ValidationException::withMessages([
                'harga_jual' => 'Harga jual tidak boleh lebih rendah dari harga pokok.',
            ]);
        }

        return $validated;
    }
}
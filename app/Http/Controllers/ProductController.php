<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware auth untuk semua method kecuali index dan show
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Menampilkan daftar produk
     * Bisa diakses oleh guest, user, admin, toko
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Query produk dengan relasi toko dan images
        $query = Product::with(['toko', 'images']);

        if ($search) {
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        // Jika belum login atau role user (guest), tampilkan produk umum
        if (!$user || $user->hasRole('user')) {
            $products = $query->paginate(12);
            return view('user.products.index', compact('products'));
        }

        // Jika admin, tampilkan semua produk di dashboard toko/admin
        if ($user->hasRole('admin')) {
            $products = $query->paginate(10);
            return view('toko.products.dashboard', compact('products'));
        }

        // Jika toko, hanya tampilkan produk milik toko tersebut
        if ($user->hasRole('toko')) {
            $toko = $user->toko;

            if (!$toko) {
                $products = collect(); // kosong jika toko belum ada
                return view('toko.products.dashboard', compact('products'));
            }

            $query = Product::where('toko_id', $toko->id)->with('images');

            if ($search) {
                $query->where('nama', 'LIKE', "%{$search}%");
            }

            $products = $query->paginate(10);
            return view('toko.products.dashboard', compact('products'));
        }

        // Role lain dilarang akses
        abort(403);
    }

    /**
     * Menampilkan detail produk
     * Bisa diakses guest/user tanpa login
     */
    public function show($id)
    {
        $product = Product::with(['toko', 'images'])->findOrFail($id);

        $user = Auth::user();

        // Jika admin/toko, tampilkan view detail khusus (bisa ada tombol edit dll)
        if ($user && ($user->hasRole('admin') || $user->hasRole('toko'))) {
            return view('toko.products.show', compact('product'));
        }

        // Untuk guest/user biasa, tampilkan view read-only
        return view('detail-product', compact('product'));
    }

    // Fungsi create hanya untuk admin/toko
    public function create()
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && !$user->hasRole('toko'))) {
            abort(403, 'Unauthorized.');
        }

        if ($user->hasRole('toko') && !$user->toko) {
            return redirect()->route('toko.profile.create')->with('error', 'Silakan buat toko terlebih dahulu.');
        }

        $toko = $user->hasRole('admin') ? Toko::all() : $user->toko;

        return view('toko.products.create', compact('toko'));
    }

    // Store produk hanya admin/toko
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && !$user->hasRole('toko'))) {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url',
            'toko_id' => 'nullable|exists:toko,id',
            'toko_nama' => 'nullable|string|max:255',
            'toko_alamat' => 'nullable|string',
        ]);

        $tokoId = null;

        if ($user->hasRole('admin')) {
            if ($request->filled('toko_id')) {
                $tokoId = $request->toko_id;
            } elseif ($request->filled('toko_nama')) {
                $newToko = Toko::create([
                    'user_id' => $user->id,
                    'nama' => $request->toko_nama,
                    'alamat' => $request->toko_alamat ?? '',
                ]);
                $tokoId = $newToko->id;
            } else {
                return back()->withErrors(['toko_id' => 'Pilih toko yang ada atau isi nama toko baru.'])->withInput();
            }
        }

        if ($user->hasRole('toko')) {
            if (!$user->toko) {
                $request->validate([
                    'toko_nama' => 'required|string|max:255',
                    'toko_alamat' => 'required|string',
                ]);
                $newToko = Toko::create([
                    'user_id' => $user->id,
                    'nama' => $request->toko_nama,
                    'alamat' => $request->toko_alamat,
                ]);
                $tokoId = $newToko->id;
            } else {
                $tokoId = $user->toko->id;
            }
        }

        if (!$tokoId) {
            return back()->withErrors(['toko_id' => 'Toko tidak valid.'])->withInput();
        }

        $product = Product::create([
            'toko_id' => $tokoId,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gambar', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        if ($request->filled('image_urls')) {
            foreach ($request->image_urls as $url) {
                if (!empty($url)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $url,
                    ]);
                }
            }
        }

        return redirect('/toko/products')->with('success', 'Produk berhasil disimpan');
    }

    // Edit produk admin/toko
    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $user = Auth::user();

        if ($user && ($user->hasRole('admin') || ($user->hasRole('toko') && $product->toko_id === optional($user->toko)->id))) {
            return view('toko.products.edit', compact('product'));
        }

        abort(403, 'Unauthorized.');
    }

    // Update produk admin/toko
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        if (!(
            $user &&
            ($user->hasRole('admin') ||
            ($user->hasRole('toko') && $product->toko_id === optional($user->toko)->id))
        )) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'images.*' => 'nullable|image|max:2048',
            'image_urls.*' => 'nullable|url',
        ]);

        $product->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gambar', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        if ($request->filled('image_urls')) {
            foreach ($request->image_urls as $url) {
                if (!empty($url)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $url,
                    ]);
                }
            }
        }

        return redirect('/toko/products')->with('success', 'Produk berhasil diperbarui');
    }

    // Hapus produk admin/toko
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && !($user->hasRole('toko') && $product->toko_id == optional($user->toko)->id))) {
            abort(403);
        }

        $product->delete();

        return redirect('/toko/products')->with('success', 'Produk berhasil dihapus');
    }

    // Hapus gambar produk admin/toko
    public function deleteImage($productId, $imageId)
    {
        $product = Product::findOrFail($productId);
        $image = ProductImage::findOrFail($imageId);
        $user = Auth::user();

        if ($user && ($user->hasRole('admin') || ($user->hasRole('toko') && $product->toko_id == optional($user->toko)->id))) {
            $image->delete();
            return redirect('/toko/products')->with('success', 'Gambar berhasil dihapus');
        }

        abort(403);
    }
public function apiIndex()
{
    $products = Product::with(['toko', 'images'])->paginate(12);
    return response()->json($products);
}

public function apiShow($id)
{
    $product = Product::with(['toko', 'images'])->findOrFail($id);
    return response()->json($product);
}

public function apiStore(Request $request)
{
    $validated = $request->validate([
        'nama' => 'required|string',
        'deskripsi' => 'nullable|string',
        'harga' => 'required|numeric',
        'toko_id' => 'nullable|exists:toko,id',
        // Tambahkan validasi gambar jika diperlukan
    ]);
    $product = Product::create($validated);
    // Bisa tambahkan upload gambar di sini
    return response()->json(['message' => 'Produk dibuat', 'product' => $product], 201);
}

public function apiUpdate($id, Request $request)
{
    $product = Product::findOrFail($id);
    $validated = $request->validate([
        'nama' => 'required|string',
        'deskripsi' => 'nullable|string',
        'harga' => 'required|numeric',
    ]);
    $product->update($validated);
    return response()->json(['message' => 'Produk diperbarui', 'product' => $product]);
}

public function apiDelete($id)
{
    $product = Product::findOrFail($id);
    $product->delete();
    return response()->json(['message' => 'Produk dihapus']);
}
}

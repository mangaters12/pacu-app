<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    // Tidak menggunakan middleware auth, jadi bebas diakses semua

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Product::with(['toko', 'images']);

        if ($search) {
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        $products = $query->paginate(12);

        return view('home', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with(['toko', 'images'])->findOrFail($id);

        $relatedProductsFromSameStore = Product::with('images')
            ->where('toko_id', $product->toko_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        $relatedProductsFromOtherStores = Product::with('toko', 'images')
            ->where('toko_id', '!=', $product->toko_id)
            ->limit(8)
            ->get();

        return view('detail-product', compact('product', 'relatedProductsFromSameStore', 'relatedProductsFromOtherStores'));
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        // Pastikan User model punya relasi cartItems
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        // Hitung total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->harga * $item->quantity;
        }

        return view('checkout', compact('cartItems', 'total'));
    }

    public function checkoutSingle(Product $product)
    {
        // Tampilkan checkout untuk produk tunggal
        return view('checkout-single', compact('product'));
    }

    // API Endpoints
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
}

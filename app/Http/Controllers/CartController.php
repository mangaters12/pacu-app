<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menampilkan halaman keranjang
    public function index()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();
        return view('cart', compact('cartItems'));
    }

    // Tambah produk ke keranjang
    public function tambah(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $cart = Cart::firstOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id],
            ['quantity' => 0]
        );

        $cart->increment('quantity');

        return back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    // Hapus item dari keranjang
    public function destroy($id)
    {
        $item = Cart::findOrFail($id);
        if ($item->user_id == auth()->id()) {
            $item->delete();
        }
        return redirect()->route('cart')->with('success', 'Item berhasil dihapus.');
    }

    // *** Checkout produk tunggal menggunakan view 'checkout' ***
    public function checkoutSingle(Product $product)
    {
        $user = Auth::user();

        // Buat collection 'cartItems' berisi 1 item produk dengan quantity 1
        $cartItems = collect([
            (object)[
                'product' => $product,
                'quantity' => 1,
            ]
        ]);

        $total = $product->harga;

        return view('checkout', compact('cartItems', 'total', 'user'));
    }

    // Proses checkout produk tunggal
    public function processCheckoutSingle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'alamat' => 'required|string|max:1000',
            'lat' => 'nullable|string|max:50',
            'long' => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        if ($quantity > $product->stock) {
            return redirect()->back()->withInput()->with('error', 'Stok produk tidak cukup.');
        }

        try {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');

            DB::transaction(function () use ($product, $quantity, $request, $paymentProofPath) {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'total_price' => $product->harga * $quantity,
                    'shipping_address' => $request->alamat,
                    'lat' => $request->lat,
                    'long' => $request->long,
                ]);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->harga,
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'payment_proof' => $paymentProofPath,
                    'status' => 'pending',
                ]);

                $product->decrement('stock', $quantity);
            });

            return redirect()->route('orders.success')->with('success', 'Order dan pembayaran berhasil dikirim, tunggu verifikasi.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat proses checkout. Silakan coba lagi.');
        }
    }

    // Tampilkan halaman checkout seluruh keranjang
    public function checkout()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        $user = Auth::user();

        // Hitung total harga seluruh item di keranjang
        $total = $cartItems->sum(fn($item) => $item->product->harga * $item->quantity);

        return view('checkout', compact('cartItems', 'total', 'user'));
    }

    // Proses checkout seluruh keranjang sekaligus
    public function processCheckout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'alamat' => 'required|string|max:1000',
            'lat' => 'nullable|string|max:50',
            'long' => 'nullable|string|max:50',
        ]);

        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        try {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');

            DB::transaction(function () use ($cartItems, $request, $paymentProofPath) {
                $total = $cartItems->sum(fn($item) => $item->product->harga * $item->quantity);

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'total_price' => $total,
                    'shipping_address' => $request->alamat,
                    'lat' => $request->lat,
                    'long' => $request->long,
                ]);

                foreach ($cartItems as $item) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->harga,
                    ]);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'payment_proof' => $paymentProofPath,
                    'status' => 'pending',
                ]);

                // Kosongkan keranjang user setelah checkout
                Cart::where('user_id', auth()->id())->delete();
            });

            return redirect()->route('orders.success')->with('success', 'Order dan pembayaran berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat proses checkout. Silakan coba lagi.');
        }
    }

    // Tambahkan fungsi untuk update lokasi user (jika diperlukan)
    public function updateLocation(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string|max:1000',
            'lat' => 'required|string|max:50',
            'long' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $user->update([
            'alamat' => $request->alamat,
            'lat' => $request->lat,
            'long' => $request->long,
        ]);

        return response()->json(['message' => 'Lokasi berhasil diperbarui.']);
    }

    // fungsi lainnya tetap sama (increase, decrease, remove, API, dll.)
    public function increase($id)
    {
        $item = Cart::findOrFail($id);
        $item->quantity += 1;
        $item->save();

        return redirect()->back()->with('success', 'Jumlah produk ditambahkan.');
    }

    public function decrease($id)
    {
        $item = Cart::findOrFail($id);
        if ($item->quantity > 1) {
            $item->quantity -= 1;
            $item->save();
            return redirect()->back()->with('success', 'Jumlah produk dikurangi.');
        }
        return redirect()->back()->with('warning', 'Minimal 1 produk.');
    }

    public function remove($id)
    {
        $item = Cart::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }

    // API methods tetap sama...
}

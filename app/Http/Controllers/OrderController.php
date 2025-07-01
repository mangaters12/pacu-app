<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List semua order (admin bisa semua, user dan toko cuma miliknya)
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $orders = Order::with(['user', 'orderDetails.product.images'])
                ->orderBy('id', 'asc')
                ->paginate(10);
        } else {
            $orders = Order::with(['orderDetails.product.images'])
                ->where('user_id', $user->id)
                ->orderBy('id', 'asc')
                ->paginate(10);
        }

        return view('orders.dashboard', compact('orders'));
    }

    // Detail order
    public function show($id)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $order = Order::with(['user', 'orderDetails.product.images'])->findOrFail($id);
        } else {
            $order = Order::with(['orderDetails.product.images'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        return view('orders.show', compact('order'));
    }

    // Form buat order baru
    public function create()
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('toko')) {
            abort(403);
        }

        $products = Product::with('images')->get();

        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalPrice += $product->harga * $item['quantity'];
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => $totalPrice,
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->harga,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => '',
                'payment_proof' => null,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('payments.show', $order->id)
                ->with('success', 'Order berhasil dibuat, silakan lanjut ke pembayaran');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal membuat order: ' . $e->getMessage()]);
        }
    }

    // Form edit order (hanya untuk admin)
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $order = Order::with('orderDetails')->findOrFail($id);
        $products = Product::with('images')->get();

        return view('orders.edit', compact('order', 'products'));
    }

    // Update order (admin only)
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);

            // Hapus detail lama
            OrderDetail::where('order_id', $order->id)->delete();

            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalPrice += $product->harga * $item['quantity'];

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->harga,
                ]);
            }

            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();

            return redirect()->route('orders.dashboard')->with('success', 'Order berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal update order: ' . $e->getMessage()]);
        }
    }

    // Update status order (admin/toko)
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('toko')) {
            abort(403, 'Unauthorized');
        }

        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,dikemas,dikirim,sampai tujuan,batal',
        ]);

        // Aturan status update dari admin/toko
        if ($order->status == 'pending' && $request->status != 'dikemas') {
            return back()->withErrors(['error' => 'Status pending hanya bisa diubah menjadi dikemas']);
        }

        if ($order->status == 'dikemas' && $request->status != 'batal') {
            return back()->withErrors(['error' => 'Status dikemas hanya bisa diubah menjadi batal']);
        }

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Status order berhasil diperbarui');
    }

    // Fungsi ambil order oleh kurir
    public function takeOrder(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('kurir')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order = Order::where('status', 'dikemas')
            ->whereNull('kurir_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Tidak ada order tersedia saat ini.'], 404);
        }

        // Assign kurir dan ubah status jadi dikirim
        $order->kurir_id = $user->id;
        $order->status = 'dikirim';
        $order->save();

        // Simulasi otomatis update status sampai tujuan (disarankan pakai queue/cron di production)
        dispatch(function () use ($order) {
            sleep(10);
            $order->refresh();
            if ($order->status == 'dikirim') {
                $order->status = 'sampai tujuan';
                $order->save();
            }
        })->afterResponse();

        return response()->json(['message' => 'Order berhasil diambil dan status diubah menjadi dikirim', 'order_id' => $order->id]);
    }

    // Hapus order (admin)
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->back()->with('success', 'Order berhasil dihapus');
    }

    // Hitung biaya kurir berdasarkan jarak (opsional)
    public function calculateCourierFee($distanceInKm)
    {
        $tarifPer4Kilo = 2000; // Rp 2.000 per 4 km
        $units = ceil($distanceInKm / 4);
        $fee = $units * $tarifPer4Kilo;
        return $fee;
    }

    // --- API Endpoints ---

    public function apiIndex()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $orders = Order::with(['user', 'orderDetails.product.images'])->paginate(10);
        } else {
            $orders = Order::with(['orderDetails.product.images'])
                ->where('user_id', $user->id)
                ->paginate(10);
        }

        return response()->json($orders);
    }

    public function apiShow($id)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $order = Order::with(['user', 'orderDetails.product.images'])->findOrFail($id);
        } else {
            $order = Order::with(['orderDetails.product.images'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        return response()->json($order);
    }

    public function apiStore(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalPrice += $product->harga * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->harga,
            ]);
        }

        // Buat payment status pending
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => '',
            'payment_proof' => null,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Order berhasil dibuat', 'order' => $order], 201);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Helper method cek role admin atau toko
    private function isAdminOrToko()
    {
        $user = Auth::user();
        return $user->hasRole('admin') || $user->hasRole('toko');
    }

    // Daftar pembayaran
    public function index()
    {
        $user = Auth::user();

        if ($this->isAdminOrToko()) {
            // Admin dan toko bisa lihat semua pembayaran
            $payments = Payment::with('order.user')->orderBy('id', 'desc')->paginate(10);
        } else {
            // User biasa hanya bisa lihat pembayaran miliknya
            $payments = Payment::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }) // Tidak ada titik koma di sini
            ->with('order.user')
            ->orderBy('id', 'desc')
            ->paginate(10);
        }

        return view('payments.dashboard', compact('payments'));
    }

    // Form buat pembayaran baru (admin & toko)
    public function create()
    {
        if (!$this->isAdminOrToko()) {
            abort(403, 'Unauthorized');
        }

        $orders = Order::where('status', 'pending')->get();

        return view('payments.create', compact('orders'));
    }

    // Simpan pembayaran baru (admin & toko)
    public function store(Request $request)
    {
        if (!$this->isAdminOrToko()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|in:pending,confirmed,rejected',
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        Payment::create([
            'order_id' => $request->order_id,
            'payment_method' => $request->payment_method,
            'payment_proof' => $path,
            'status' => $request->status,
        ]);

        return redirect()->route('payments.dashboard')->with('success', 'Pembayaran berhasil ditambahkan');
    }

    // Detail pembayaran dan form upload
    public function show($orderId)
    {
        $user = Auth::user();

        if ($this->isAdminOrToko()) {
            $order = Order::with(['orderDetails.product'])->findOrFail($orderId);
        } else {
            $order = Order::with(['orderDetails.product'])
                ->where('id', $orderId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.dashboard')->with('info', 'Order ini sudah diproses atau pembayaran sudah dilakukan.');
        }

        $payment = Payment::where('order_id', $order->id)->first();

        return view('payments.show', compact('order', 'payment'));
    }

    // Form edit pembayaran (admin & toko)
    public function edit($id)
    {
        if (!$this->isAdminOrToko()) {
            abort(403, 'Unauthorized');
        }

        $payment = Payment::findOrFail($id);
        $orders = Order::all();

        return view('payments.edit', compact('payment', 'orders'));
    }

    // Update pembayaran (admin & toko)
    public function update(Request $request, $id)
    {
        if (!$this->isAdminOrToko()) {
            abort(403, 'Unauthorized');
        }

        $payment = Payment::findOrFail($id);

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|in:pending,confirmed,rejected',
        ]);

        if ($request->hasFile('payment_proof')) {
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $path = $request->file('payment_proof')->store('payments', 'public');
        } else {
            $path = $payment->payment_proof;
        }

        $payment->update([
            'order_id' => $request->order_id,
            'payment_method' => $request->payment_method,
            'payment_proof' => $path,
            'status' => $request->status,
        ]);

        return redirect()->route('payments.dashboard')->with('success', 'Pembayaran berhasil diperbarui');
    }

    // Hapus pembayaran
    public function destroy($id)
    {
        if (!$this->isAdminOrToko()) {
            abort(403, 'Unauthorized');
        }

        $payment = Payment::findOrFail($id);

        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $payment->delete();

        return redirect()->route('payments.dashboard')->with('success', 'Pembayaran berhasil dihapus');
    }

    // Proses upload bukti bayar dan update status dari user/toko
    public function processPayment(Request $request, $orderId)
    {
        $user = Auth::user();

        $order = Order::findOrFail($orderId);

        if ($user->id !== $order->user_id && !$this->isAdminOrToko()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('payments', 'public');
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => $request->payment_method,
                'payment_proof' => $path,
                'status' => 'pending',
            ]
        );

        $order->update(['status' => 'paid']);

        return redirect()->route('orders.dashboard')->with('success', 'Pembayaran berhasil diunggah, tunggu konfirmasi.');
    }

    // --- API Endpoints ---

    public function apiIndex()
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('toko')) {
            $payments = Payment::with('order.user')->orderBy('id', 'desc')->paginate(10);
        } else {
            $payments = Payment::whereHas('order', function($q) use ($user) {
                $q->where('user_id', $user->id);
            }) // Tidak ada titik koma di sini
            ->with('order.user')
            ->paginate(10);
        }

        return response()->json($payments);
    }

    public function apiShow($id)
    {
        $payment = Payment::with('order.user')->findOrFail($id);
        return response()->json($payment);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|max:2048',
            'status' => 'required|string|in:pending,confirmed,rejected',
        ]);
        $path = $request->file('payment_proof')->store('payments', 'public');
        $payment = Payment::create([
            'order_id' => $validated['order_id'],
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $path,
            'status' => $validated['status'],
        ]);
        return response()->json(['message' => 'Pembayaran dibuat', 'payment' => $payment], 201);
    }

    public function apiUpdate($id, Request $request)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'payment_proof' => 'nullable|image|max:2048',
            'status' => 'required|string|in:pending,confirmed,rejected',
        ]);

        if ($request->hasFile('payment_proof')) {
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $path = $request->file('payment_proof')->store('payments', 'public');
        } else {
            $path = $payment->payment_proof;
        }

        $payment->update([
            'order_id' => $validated['order_id'],
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $path,
            'status' => $validated['status'],
        ]);

        return response()->json(['message' => 'Pembayaran diperbarui', 'payment' => $payment]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Tampilkan daftar order milik user yang login
    public function index()
    {
        $user = Auth::user();

        // Ambil order milik user, termasuk detail produk & payment
        $orders = Order::with(['orderDetails.product.images', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Override status order berdasarkan status payment
        foreach ($orders as $order) {
            if ($order->payment) {
                switch ($order->payment->status) {
                    case 'confirmed':
                        $order->status = 'Dikemas';
                        break;
                    case 'pending':
                        $order->status = 'pending';
                        break;
                    case 'rejected':
                        $order->status = 'dibatalkan';
                        break;
                    // Tambahkan kondisi lain jika perlu
                    default:
                        // Biarkan status order asli
                        break;
                }
            }
        }

        return view('order', compact('orders'));
    }

    // Method untuk mendapatkan daftar order user via API
    public function apiUserOrders()
    {
        $user = auth()->user();
        $orders = Order::with(['orderDetails.product.images', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json($orders);
    }

    // Method untuk mendapatkan detail order tertentu via API
    public function apiUserOrderDetail($id)
    {
        $user = auth()->user();
        $order = Order::with(['orderDetails.product.images', 'payment'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        return response()->json($order);
    }

    // Tampilkan detail order milik user yang login
    public function show($id)
    {
        $user = Auth::user();

        $order = Order::with(['orderDetails.product.images', 'payment'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Override status order sesuai payment status
        if ($order->payment) {
            switch ($order->payment->status) {
                case 'confirmed':
                    $order->status = 'dikemas';
                    break;
                case 'pending':
                    $order->status = 'pending';
                    break;
                case 'rejected':
                    $order->status = 'dibatalkan';
                    break;
                default:
                    break;
            }
        }

        return view('order_detail', compact('order'));
    }
}

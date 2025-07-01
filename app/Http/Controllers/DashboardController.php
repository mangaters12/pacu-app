<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Toko; // Pastikan model Toko di-import

class DashboardController extends Controller
{
    public function toko()
    {
        $user = Auth::user();

        // Jika user role 'toko' dan belum punya toko, otomatis bikin toko
        if ($user->role === 'toko' && !$user->toko) {
            $toko = Toko::create([
                'user_id' => $user->id,
                'nama' => 'Toko ' . $user->name, // Bisa disesuaikan
                'alamat' => 'Alamat default', // Bisa disesuaikan
            ]);
            // Refresh relasi user agar update
            $user->refresh();
        }

        // Jika user punya toko, tampilkan dashboard
        if ($user->toko) {
            $toko = $user->toko;
            $products = $toko->products;
            $customers = $toko->customers;

            return view('toko.products.dashboard', compact('toko', 'products', 'customers'));
        }

        // Untuk role lain, redirect ke halaman lain
        return redirect()->route('home')->with('error', 'Akses tidak diizinkan.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // Hapus middleware auth di constructor agar bisa diakses publik
    public function __construct()
    {
        // $this->middleware('auth'); // jangan pakai ini
    }

    public function index()
    {
        $user = Auth::user();

        if ($user) {  // user sudah login
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('toko')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('user')) {
                return redirect()->route('user.home');
            }
        }

        // jika guest / belum login, tampilkan halaman home publik
        return view('home');
    }
}

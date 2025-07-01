<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Cart; // ⬅️ Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Share variabel $toko ke semua view jika user adalah toko
        view()->composer('*', function ($view) {
            if (Auth::check() && Auth::user()->hasRole('toko')) {
                $view->with('toko', Auth::user()->toko);
            }
        });

        // Share variabel $cartItems ke semua view jika user login
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
            } else {
                $cartItems = collect(); // Kosong jika belum login
            }
            $view->with('cartItems', $cartItems);
        });
    }
}

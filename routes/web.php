<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderUserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==== AUTHENTICATION ROUTES ====
// Route default auth (login, register, password reset, etc)
Auth::routes();

// Logout global
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==== PUBLIC ROUTES ====
// Root redirect ke home
Route::get('/', fn() => redirect()->route('home'))->name('root');
// Homepage produk untuk user biasa
Route::get('/home', [UserProductController::class, 'index'])->name('home');
// Detail produk untuk user
Route::get('/products/{id}', [UserProductController::class, 'show'])->name('detail-product');

// ==== KURIR REGISTRASI & LOGIN (public) ====
Route::get('/kurir/register', [KurirController::class, 'registerKurirForm'])->name('kurir.registerForm');
Route::post('/kurir/register', [KurirController::class, 'registerKurir'])->name('kurir.register');

Route::get('/kurir/login', [KurirController::class, 'loginForm'])->name('kurir.loginForm');
Route::post('/kurir/login', [KurirController::class, 'login'])->name('kurir.login');
Route::post('/kurir/logout', [KurirController::class, 'logout'])->name('kurir.logout');

Route::middleware(['auth', 'role:kurir'])->group(function () {
    Route::get('/kurir/orderan', [KurirController::class, 'orderanKurir'])->name('kurir.orderan-kurir');
    Route::post('/kurir/orderan/take/{order}', [KurirController::class, 'takeOrder'])->name('kurir.orders.take');
    Route::post('/kurir/logout', [KurirController::class, 'logout'])->name('kurir.logout');
});


// ==== USER AUTHENTICATED ROUTES (user umum dan toko) ====
Route::middleware(['auth'])->group(function () {

    // Keranjang belanja
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart');
    Route::post('/keranjang/tambah', [CartController::class, 'tambah'])->name('cart.tambah');
    Route::delete('/keranjang/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Halaman checkout semua keranjang
       Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

       // Halaman checkout produk tunggal
       Route::get('/checkout/{product}', [CartController::class, 'checkoutSingle'])->name('checkout.single');

       // Proses checkout produk tunggal
       Route::post('/checkout/process', [CartController::class, 'processCheckoutSingle'])->name('checkout.process');

       // Proses checkout seluruh keranjang sekaligus
       Route::post('/checkout/process-all', [CartController::class, 'processCheckout'])->name('checkout.processAll');
Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Order User
    Route::get('/my-orders', [OrderUserController::class, 'index'])->name('orders.user.index');
    Route::get('/my-orders/{id}', [OrderUserController::class, 'show'])->name('orders.user.show');

    // Dashboard per role
    Route::get('/toko', [DashboardController::class, 'toko'])->middleware('role:toko')->name('toko.dashboard');
    Route::get('/user', [DashboardController::class, 'user'])->middleware('role:user')->name('user.dashboard');

    // Produk & Customer khusus toko
    Route::resource('/products', ProductController::class)->middleware('role:toko');
    Route::resource('/customers', CustomerController::class)->middleware('role:toko');

    // ADMIN ONLY ROUTES
    Route::middleware(['role:admin'])->group(function () {
        // Dashboard admin
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        Route::prefix('admin')->group(function () {

            // Manajemen user oleh admin
            Route::prefix('users')->group(function () {
                Route::get('/', [AdminController::class, 'users'])->name('admin.users');
                Route::get('/create', [AdminController::class, 'createUser'])->name('admin.user-create');
                Route::post('/', [AdminController::class, 'storeUser'])->name('admin.store');
                Route::get('/{user}', [AdminController::class, 'showUser'])->name('admin.show-user');
                Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('admin.edit-user');
                Route::put('/{user}', [AdminController::class, 'updateUser'])->name('admin.update');
                Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('admin.destroy');
            });

            // Manajemen toko
            Route::get('/stores', [AdminController::class, 'stores'])->name('admin.stores');
            Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

            // Manajemen Kurir oleh admin
            Route::get('/kurir', [KurirController::class, 'indexKurirs'])->name('kurir.index');
            Route::get('/kurir/create', [KurirController::class, 'create'])->name('kurir.create');
            Route::post('/kurir', [KurirController::class, 'store'])->name('kurir.store');
            Route::get('/kurir/{id}', [KurirController::class, 'show'])->name('kurir.show');
            Route::get('/kurir/{id}/edit', [KurirController::class, 'edit'])->name('kurir.edit');
            Route::put('/kurir/{id}', [KurirController::class, 'update'])->name('kurir.update');
            Route::delete('/kurir/{id}', [KurirController::class, 'destroy'])->name('kurir.destroy');
        });
    });

    // TOKO + ADMIN ROUTES
    Route::prefix('toko')->middleware('role:admin,toko')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('toko.dashboard');
        Route::get('/products/create', [ProductController::class, 'create'])->name('toko.create');
        Route::post('/products', [ProductController::class, 'store'])->name('toko.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('toko.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('toko.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('toko.destroy');
        Route::delete('/products/{productId}/image/{imageId}', [ProductController::class, 'deleteImage'])->name('toko.image.delete');
    });

    Route::prefix('payments')->middleware('role:admin,toko')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payments.dashboard');
        Route::get('/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/{order}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/{order}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/{order}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/{order}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Manajemen orders (toko/admin)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.dashboard');
        Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/take', [OrderController::class, 'takeOrder'])->name('orders.take');
    });
});



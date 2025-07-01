<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controller
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderUserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProductController;
use App\Http\Controllers\KurirController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Auth endpoints
Route::post('/login', [LoginController::class, 'apiLogin']);
Route::post('/register', [RegisterController::class, 'apiRegister']);

// Logout (protected route)
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Public routes - bisa diakses tanpa auth
Route::get('/products', [UserProductController::class, 'apiIndex']);
Route::get('/products/{id}', [UserProductController::class, 'apiShow']);

// Routes protected by Sanctum auth
Route::middleware('auth:sanctum')->group(function () {
    // Produk (admin/toko)
    Route::get('/admin/products', [ProductController::class, 'apiIndex']);
    Route::get('/admin/products/{id}', [ProductController::class, 'apiShow']);
    Route::post('/admin/products', [ProductController::class, 'apiStore']);
    Route::put('/admin/products/{id}', [ProductController::class, 'apiUpdate']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'apiDelete']);

    // Cart
    Route::get('/cart', [CartController::class, 'apiIndex']);
    Route::post('/cart', [CartController::class, 'apiTambah']);
    Route::delete('/cart/{id}', [CartController::class, 'apiDestroy']);

    // Order
    Route::get('/orders', [OrderController::class, 'apiIndex']);
    Route::get('/orders/{id}', [OrderController::class, 'apiShow']);
    Route::post('/orders', [OrderController::class, 'apiStore']);

    // User orders
    Route::get('/user/orders', [OrderUserController::class, 'apiUserOrders']);
    Route::get('/user/orders/{id}', [OrderUserController::class, 'apiUserOrderDetail']);

    // Payment
    Route::get('/payments', [PaymentController::class, 'apiIndex']);
    Route::get('/payments/{id}', [PaymentController::class, 'apiShow']);
    Route::post('/payments', [PaymentController::class, 'apiStore']);
    Route::put('/payments/{id}', [PaymentController::class, 'apiUpdate']);
    Route::delete('/payments/{id}', [PaymentController::class, 'apiDelete']);

    // Kurir
    Route::get('/kurirs', [KurirController::class, 'apiIndex']);
    Route::get('/kurirs/{id}', [KurirController::class, 'apiShow']);
    Route::post('/kurirs', [KurirController::class, 'apiStore']);
    Route::put('/kurirs/{id}', [KurirController::class, 'apiUpdate']);
    Route::delete('/kurirs/{id}', [KurirController::class, 'apiDelete']);
});

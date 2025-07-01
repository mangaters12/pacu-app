<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Membuat instance aplikasi Laravel dengan konfigurasi dasar
return Application::configure(basePath: dirname(__DIR__)) // Mengatur base path aplikasi
    ->withRouting( // Mengatur rute aplikasi
        web: __DIR__.'/../routes/web.php', // File rute web
        commands: __DIR__.'/../routes/console.php', // File rute console
        health: '/up', // Endpoint health check
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Menambahkan alias middleware custom
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class, // Middleware role
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Pengaturan penanganan exception (kosong di sini)
    })->create(); // Membuat instance aplikasi

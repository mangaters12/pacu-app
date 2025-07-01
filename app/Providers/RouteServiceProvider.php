<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->mapApiRoutes();
        // Jika ada route web, bisa juga ditambahkan
        // $this->mapWebRoutes();
    }

    /**
     * Map API routes.
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->app->getNamespace() . 'Http\Controllers')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Map web routes.
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->app->getNamespace() . 'Http\Controllers')
            ->group(base_path('routes/web.php'));
    }
}

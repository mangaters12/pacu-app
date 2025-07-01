<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        if ($request->getMethod() === "OPTIONS") {
            return response()->json('OK', 200)
                             ->header('Access-Control-Allow-Origin', '*')
                             ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                             ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With')
                             ->header('Access-Control-Allow-Credentials', 'true');
        }

        $response = $next($request);

        return $response->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With')
                        ->header('Access-Control-Allow-Credentials', 'true');
    }
}

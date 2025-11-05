<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Hsts
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Add HSTS header in production
        if (app()->environment('production') && $request->secure()) {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}


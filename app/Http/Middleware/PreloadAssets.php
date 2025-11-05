<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreloadAssets
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Add preload hints for critical assets
        if (method_exists($response, 'header')) {
            $response->header('Link', '<' . asset('brand/chamaconnect-logo.svg') . '>; rel=preload; as=image', false);
        }
        
        return $response;
    }
}


<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CorrelationId
{
    public function handle(Request $request, Closure $next)
    {
        $cid = $request->headers->get('X-Request-Id') ?: (string) Str::uuid();
        // Add to request for downstream usage
        $request->headers->set('X-Request-Id', $cid);
        // Add to logs
        Log::withContext(['correlation_id' => $cid]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $cid);
        return $response;
    }
}



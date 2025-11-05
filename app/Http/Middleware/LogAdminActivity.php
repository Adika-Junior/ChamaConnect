<?php

namespace App\Http\Middleware;

use App\Models\AdminActivity;
use Closure;
use Illuminate\Http\Request;

class LogAdminActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->user() && $request->user()->isAdmin() && in_array($request->method(), ['POST','PUT','PATCH','DELETE'])) {
            AdminActivity::create([
                'actor_id' => $request->user()->id,
                'action' => $request->route()?->getName() ?? $request->path(),
                'target_type' => $request->route()?->getControllerClass(),
                'target_id' => null,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'metadata' => [
                    'inputs' => $request->except(['password','password_confirmation','_token']),
                    'status' => $response->getStatusCode(),
                ],
            ]);
        }

        return $response;
    }
}



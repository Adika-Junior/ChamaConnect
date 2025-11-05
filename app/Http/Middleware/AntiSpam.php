<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AntiSpam
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            // Simple honeypot: bots often fill hidden fields
            $hp = $request->input('_hp');
            if (!empty($hp)) {
                abort(422, 'Spam detected');
            }

            // Basic min submit time check if provided (client can set _ts at render)
            $ts = (int) $request->input('_ts', 0);
            if ($ts > 0) {
                $elapsed = time() - $ts;
                if ($elapsed < 3) {
                    abort(429, 'Too fast; please try again');
                }
            }
        }

        return $next($request);
    }
}



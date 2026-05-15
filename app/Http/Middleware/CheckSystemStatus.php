<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $systemEnabled = Setting::get('system_enabled', '1');

        if ($systemEnabled == '0') {
            // Allow admin to bypass? No, let's just block student routes.
            // If the route is NOT an admin route, block it.
            if (!$request->is('admin/*')) {
                return response()->view('errors.system_locked', [], 503);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log in production for debugging
        if (config('app.env') === 'production') {
            Log::info('Auth Debug', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'is_authenticated' => Auth::check(),
                'user_id' => Auth::id(),
                'user_email' => Auth::user()?->email,
                'user_roles' => Auth::user()?->getRoleNames()->toArray(),
                'session_id' => session()->getId(),
                'has_session' => session()->has('_token'),
                'cookies' => array_keys($request->cookies->all()),
            ]);
        }
        
        return $next($request);
    }
}
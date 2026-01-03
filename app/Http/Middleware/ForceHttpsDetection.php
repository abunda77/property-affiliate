<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsDetection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force HTTPS when enabled via configuration
        if (config('app.force_https', false)) {
            // Detect HTTPS from proxy headers (Cloudflare, load balancers, etc.)
            if ($request->header('x-forwarded-proto') === 'https' || 
                $request->header('cf-visitor') === '{"scheme":"https"}' ||
                $request->server('HTTP_X_FORWARDED_PROTO') === 'https') {
                
                $request->server->set('HTTPS', 'on');
                $request->server->set('SERVER_PORT', 443);
                $request->server->set('REQUEST_SCHEME', 'https');
            }
            
            // Redirect to HTTPS if not already using it
            if (!$request->secure() && 
                $request->header('x-forwarded-proto') !== 'https' &&
                $request->server('HTTP_X_FORWARDED_PROTO') !== 'https') {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }
        
        return $next($request);
    }
}
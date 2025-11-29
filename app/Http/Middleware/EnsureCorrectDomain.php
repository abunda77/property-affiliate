<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorrectDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedDomains = [
            'yourdomain.com',
            'www.yourdomain.com',
            'property-affiliate.test',
            'pams.produkmastah.com',
            'localhost',
            '127.0.0.1',
        ];

        $currentDomain = $request->getHost();
        
        // Allow if domain is in allowed list or if we're in local development
        if (in_array($currentDomain, $allowedDomains) || 
            app()->environment('local') || 
            str_contains($currentDomain, 'localhost') ||
            filter_var($currentDomain, FILTER_VALIDATE_IP)) {
            return $next($request);
        }

        // Log unauthorized domain access
        \Illuminate\Support\Facades\Log::warning('Unauthorized domain access attempt', [
            'domain' => $currentDomain,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        // Return 404 for unauthorized domains
        abort(404);
    }
}
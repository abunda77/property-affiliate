<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;

class AffiliateTrackingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip ALL processing for Livewire requests to prevent interference
        if ($request->is('livewire/*')) {

            return $next($request);
        }



        $affiliateId = null;

        // Check for 'ref' query parameter
        if ($request->has('ref')) {
            $affiliate = User::where('affiliate_code', $request->query('ref'))
                ->active()
                ->first();

            if ($affiliate) {
                $affiliateId = $affiliate->id;
                // Set cookie with 30-day expiration (43200 minutes)
                Cookie::queue('affiliate_id', $affiliateId, 43200);
                

            }
        }
        // Read affiliate_id from existing cookie if no ref parameter
        // Check multiple ways to get cookie value for compatibility
        $cookieValue = null;
        
        // Standard way
        if ($request->cookie('affiliate_id')) {
            $cookieValue = $request->cookie('affiliate_id');
        }
        // Alternative way for test environment
        elseif ($request->hasCookie('affiliate_id')) {
            $cookieValue = $request->cookie('affiliate_id');
        }
        // Check raw cookie data
        elseif (isset($_COOKIE['affiliate_id'])) {
            $cookieValue = $_COOKIE['affiliate_id'];
        }
        
        if ($cookieValue !== null && $cookieValue !== '') {
            $affiliateId = (int) $cookieValue;
            

        }

        // Record visit if we have an affiliate ID
        if ($affiliateId) {

            $this->recordVisit($affiliateId, $request);

        }



        return $next($request);
    }

    /**
     * Record a visit for tracking purposes.
     *
     * @param  int  $affiliateId
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function recordVisit(int $affiliateId, Request $request): void
    {
        try {
            // Skip tracking for Livewire update requests
            if ($request->is('livewire/*')) {

                return;
            }



            // Extract visitor IP
            $visitorIp = $request->ip();

            // Extract device type (mobile vs desktop)
            $userAgent = $request->userAgent();
            $device = $this->detectDevice($userAgent);

            // Extract browser
            $browser = $this->detectBrowser($userAgent);

            // Get current URL
            $url = $request->fullUrl();

            // Extract property_id from URL if on property page
            $propertyId = $this->extractPropertyId($request);



            // Create Visit record
            \App\Models\Visit::create([
                'affiliate_id' => $affiliateId,
                'property_id' => $propertyId,
                'visitor_ip' => $visitorIp,
                'device' => $device,
                'browser' => $browser,
                'url' => $url,
            ]);


        } catch (\Exception $e) {
            // Log the error but don't break the request
            Log::error('Failed to record affiliate visit: ' . $e->getMessage(), [
                'affiliate_id' => $affiliateId,
                'url' => $request->fullUrl(),
                'exception' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Detect device type from user agent.
     *
     * @param  string|null  $userAgent
     * @return string
     */
    private function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }

        $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
        
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return 'mobile';
            }
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent.
     *
     * @param  string|null  $userAgent
     * @return string
     */
    private function detectBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }



        $browsers = [
            'Edg/' => 'Edge',  // More specific pattern for Edge
            'Edge' => 'Edge',
            'Edg' => 'Edge',
            'Chrome' => 'Chrome',
            'Safari' => 'Safari',
            'Firefox' => 'Firefox',
            'Opera' => 'Opera',
            'OPR' => 'Opera',
            'MSIE' => 'Internet Explorer',
            'Trident' => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $browser) {
            if (stripos($userAgent, $key) !== false) {

                return $browser;
            }
        }


        return 'unknown';
    }

    /**
     * Extract property ID from the request if on a property page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int|null
     */
    private function extractPropertyId(Request $request): ?int
    {
        // Ensure we have a route before trying to access parameters
        if (!$request->route()) {
            return null;
        }

        // Check if the route has a property parameter
        $property = $request->route('property');
        
        if ($property instanceof \App\Models\Property) {
            return $property->id;
        }

        // If property is a string (slug), try to find the property
        if (is_string($property) && !empty($property)) {
            $propertyModel = \App\Models\Property::where('slug', $property)->first();
            return $propertyModel?->id;
        }

        // Check for property_id in route parameters
        $propertyId = $request->route('property_id');
        if ($propertyId && is_numeric($propertyId)) {
            return (int) $propertyId;
        }

        // Check for slug parameter and try to find property
        $slug = $request->route('slug');
        if ($slug && is_string($slug)) {
            $propertyModel = \App\Models\Property::where('slug', $slug)->first();
            return $propertyModel?->id;
        }

        return null;
    }
}

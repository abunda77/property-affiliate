# Technical Problem & Solution: 404 Not Found on Domain Access

## Problem Summary

Laravel application (PAMS - Property Affiliate Management System) returns **404 Not Found** when accessed via domain `https://pams.produkmastah.com`, but works correctly when accessed via server IP address.

## Environment

-   **Framework**: Laravel 12.x
-   **PHP**: 8.3.17
-   **Web Server**: OpenLiteSpeed
-   **Proxy**: Cloudflare Tunnel
-   **Domain**: pams.produkmastah.com
-   **Server OS**: Linux

## Symptoms

1. ✅ Application works when accessed via IP: `http://SERVER_IP`
2. ❌ Returns 404 when accessed via domain: `https://pams.produkmastah.com`
3. ❌ All routes return 404 (homepage, `/properties`, etc.)
4. ✅ Static PHP files work (e.g., `phpinfo.php`)
5. ✅ Laravel bootstrap works correctly

## Root Cause Analysis

### Initial Investigation

Created debug files to isolate the issue:

```php
// public/debug.php - Test Laravel bootstrap
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $app->handle($request);
// Result: Status 302 (redirect working) ✅
```

```php
// public/route-test.php - Test route matching
$request = \Illuminate\Http\Request::create('/properties', 'GET');
$response = $app->handle($request);
// Result: Status 404 (route not matched) ❌
```

### Key Findings

1. **Laravel Core**: Working correctly ✅
2. **Routes**: Registered properly ✅
3. **Views**: Files exist ✅
4. **Rewrite Rules**: `.htaccess` exists but not being processed ❌

### Diagnosis Steps

#### Step 1: Verify HTTPS Detection

**Issue**: Laravel was generating HTTP URLs instead of HTTPS behind Cloudflare proxy.

**Test**:

```php
// Check if request is detected as secure
$request->isSecure(); // Returns: false ❌
$request->getScheme(); // Returns: http ❌
```

**Solution**: Implemented trusted proxies and HTTPS detection middleware.

#### Step 2: Check Rewrite Rules

**Issue**: OpenLiteSpeed was not processing `.htaccess` file.

**Test**:

```bash
# Direct access to index.php works
https://pams.produkmastah.com/index.php/properties ✅

# Clean URL doesn't work
https://pams.produkmastah.com/properties ❌
```

**Conclusion**: Rewrite rules not being applied.

## Solutions Implemented

### 1. Configure Trusted Proxies for Cloudflare

**File**: `app/Http/Middleware/TrustProxies.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies = [
        // Cloudflare IP ranges
        '103.21.244.0/22',
        '103.22.200.0/22',
        '104.16.0.0/13',
        '108.162.192.0/18',
        '162.158.0.0/15',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '188.114.96.0/20',
        '190.93.240.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        // Trust all proxies (for development)
        '*'
    ];

    protected $headers = Request::HEADER_X_FORWARDED_FOR |
                        Request::HEADER_X_FORWARDED_HOST |
                        Request::HEADER_X_FORWARDED_PORT |
                        Request::HEADER_X_FORWARDED_PROTO |
                        Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

**Register in**: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->trustProxies(at: [
        \App\Http\Middleware\TrustProxies::class,
    ]);
})
```

### 2. Force HTTPS Detection

**File**: `app/Http/Middleware/ForceHttpsDetection.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsDetection
{
    public function handle(Request $request, Closure $next): Response
    {
        // Force HTTPS detection for Cloudflare
        if ($request->header('x-forwarded-proto') === 'https' ||
            $request->header('cf-visitor') === '{"scheme":"https"}' ||
            $request->server('HTTP_X_FORWARDED_PROTO') === 'https') {

            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
            $request->server->set('REQUEST_SCHEME', 'https');
        }

        return $next($request);
    }
}
```

**Register in**: `bootstrap/app.php`

```php
$middleware->web(prepend: [
    \App\Http\Middleware\ForceHttpsDetection::class,
]);
```

### 3. Force HTTPS URL Generation

**File**: `app/Providers/AppServiceProvider.php`

```php
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    // Force HTTPS in production
    if (config('app.env') === 'production' ||
        request()->header('x-forwarded-proto') === 'https') {
        URL::forceScheme('https');
    }

    // ... rest of boot method
}
```

### 4. Update Environment Configuration

**File**: `.env`

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pams.produkmastah.com

SESSION_DOMAIN=.produkmastah.com
SANCTUM_STATEFUL_DOMAINS=pams.produkmastah.com,localhost,127.0.0.1
```

### 5. Optimize .htaccess for OpenLiteSpeed

**File**: `public/.htaccess`

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle Cloudflare HTTPS detection
    RewriteCond %{HTTP:X-Forwarded-Proto} https
    RewriteRule .* - [E=HTTPS:on]

    # Redirect Trailing Slashes If Not A Folder
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>
```

### 6. **PRIMARY SOLUTION**: Enable OpenLiteSpeed .htaccess Auto Load

**This was the critical fix that resolved the 404 issue.**

#### OpenLiteSpeed Configuration:

1. Access OpenLiteSpeed WebAdmin (usually `https://server-ip:7080`)
2. Navigate to: **Virtual Hosts** → Select your virtual host
3. Go to **Rewrite** tab
4. Click **Edit** on **Rewrite Control**
5. Set the following:
    - **Enable Rewrite**: `Yes`
    - **Auto Load from .htaccess**: `Yes` ✅ **CRITICAL**
6. Click **Save**
7. Perform **Graceful Restart**

#### Why This Was Necessary:

Unlike Apache, OpenLiteSpeed does **not** automatically process `.htaccess` files by default. The rewrite rules must be explicitly enabled at the Virtual Host level.

**Before fix**:

```
Request: /properties
↓
OpenLiteSpeed: No rewrite rules applied
↓
Looking for: /home/pams.produkmastah.com/property-affiliate/public/properties
↓
Result: 404 Not Found ❌
```

**After fix**:

```
Request: /properties
↓
OpenLiteSpeed: Apply .htaccess rewrite rules
↓
Rewrite to: /index.php
↓
Laravel Router: Match route /properties
↓
Result: 200 OK ✅
```

## Verification Steps

### 1. Test HTTPS Detection

```bash
curl -H "X-Forwarded-Proto: https" https://pams.produkmastah.com/
# Should return HTTPS URLs in response
```

### 2. Test Route Resolution

```bash
# Test homepage
curl https://pams.produkmastah.com/
# Expected: 302 redirect to /properties

# Test properties route
curl https://pams.produkmastah.com/properties
# Expected: 200 OK with property catalog page
```

### 3. Verify Rewrite Rules

```bash
# Check if clean URLs work
curl -I https://pams.produkmastah.com/properties
# Expected: HTTP/2 200

# Check if index.php is hidden
curl -I https://pams.produkmastah.com/index.php/properties
# Expected: HTTP/2 200 (fallback should still work)
```

## Cache Clearing

After all configuration changes:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Results

✅ All routes accessible via domain
✅ HTTPS properly detected and enforced
✅ Clean URLs working (no `/index.php` in URL)
✅ Cloudflare proxy integration working
✅ Session and cookies working correctly
✅ Security headers applied

## Lessons Learned

1. **OpenLiteSpeed ≠ Apache**: OpenLiteSpeed requires explicit configuration to process `.htaccess` files
2. **Cloudflare Proxy**: Always configure trusted proxies when behind Cloudflare
3. **HTTPS Detection**: Behind proxies, HTTPS detection requires special middleware
4. **Debug Systematically**: Create test files to isolate issues (Laravel vs web server vs proxy)
5. **Check Web Server Config**: Don't assume `.htaccess` will work automatically

## Prevention for Future Deployments

### Deployment Checklist:

-   [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
-   [ ] Configure `APP_URL` with correct domain
-   [ ] Enable OpenLiteSpeed rewrite: **Auto Load from .htaccess = Yes**
-   [ ] Configure trusted proxies if behind Cloudflare/proxy
-   [ ] Set correct `SESSION_DOMAIN`
-   [ ] Clear all caches after deployment
-   [ ] Test all major routes
-   [ ] Verify HTTPS detection
-   [ ] Check security headers

## Related Documentation

-   [Laravel Trusted Proxies](https://laravel.com/docs/12.x/requests#configuring-trusted-proxies)
-   [OpenLiteSpeed Rewrite Rules](https://openlitespeed.org/kb/rewrite-rules/)
-   [Cloudflare IP Ranges](https://www.cloudflare.com/ips/)

## Contact

For issues related to this deployment, refer to `DEPLOYMENT_NOTES.md` or contact the development team.

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-29  
**Status**: Resolved ✅

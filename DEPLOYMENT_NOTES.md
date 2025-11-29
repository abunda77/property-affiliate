# Deployment Notes - PAMS

## Production Configuration

### Environment

-   **APP_ENV**: production
-   **APP_DEBUG**: false
-   **APP_URL**: https://pams.produkmastah.com

### OpenLiteSpeed Configuration

**Important:** OpenLiteSpeed requires manual configuration for Laravel to work properly.

#### Required Settings:

1. **Virtual Host → Rewrite Tab**

    - Enable Rewrite: `Yes`
    - Auto Load from .htaccess: `Yes` ✅ (Critical!)

2. **Document Root**: `/path/to/property-affiliate/public`

3. After configuration changes, always do **Graceful Restart**

### Cloudflare Integration

-   Trusted proxies configured for Cloudflare IP ranges
-   HTTPS detection via `X-Forwarded-Proto` header
-   Session domain: `.produkmastah.com`

### Key Middleware

1. **TrustProxies** - Trusts Cloudflare proxies
2. **ForceHttpsDetection** - Ensures HTTPS detection works behind Cloudflare
3. **AffiliateTrackingMiddleware** - Tracks affiliate visits

### Cache Management

After deployment or configuration changes:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Common Issues & Solutions

#### Issue: 404 Not Found on all routes

**Solution:** Enable "Auto Load from .htaccess" in OpenLiteSpeed Virtual Host configuration

#### Issue: Mixed content (HTTP/HTTPS)

**Solution:** Already configured with ForceHttpsDetection middleware and AppServiceProvider

#### Issue: Session/Cookie not working

**Solution:** Check SESSION_DOMAIN in .env matches your domain

### Security Headers

Configured in `.htaccess`:

-   X-Content-Type-Options: nosniff
-   X-Frame-Options: DENY
-   X-XSS-Protection: 1; mode=block

### File Permissions

```bash
chmod -R 755 /path/to/property-affiliate
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## Troubleshooting Commands

```bash
# Check routes
php artisan route:list

# Check config
php artisan config:show app.url

# Clear all caches
php artisan optimize:clear

# Check logs
tail -f storage/logs/laravel.log
```

## Last Updated

2025-11-29 - Initial production deployment

## Known Issues & Solutions

### Issue: 403 Access Denied after login (Production only)

**Symptoms:**

-   Login works on local server
-   Production shows 403 after successful login
-   User is authenticated but cannot access dashboard

**Root Cause:**
Session cookies not being set correctly due to HTTPS/proxy configuration.

**Solution:**

1. Ensure `.env` has these settings:

```env
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

2. Clear all caches:

```bash
php artisan optimize:clear
php artisan config:cache
```

3. Clear browser cookies and test in Incognito mode

4. Verify cookie is set with Secure flag in browser DevTools

## Deployment Script

Use `deploy-to-server.sh` for automated deployment:

```bash
chmod +x deploy-to-server.sh
./deploy-to-server.sh
```

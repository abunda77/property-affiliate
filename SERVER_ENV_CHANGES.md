# Server Environment Configuration Changes

## Problem

403 Access Denied after login on production server (works fine on local).

## Root Cause

Session cookies not being set correctly due to HTTPS/proxy configuration.

## Solution

Update `.env` file on **production server** with these settings:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pams.produkmastah.com

# Session Configuration (CRITICAL for HTTPS)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Sanctum (for API/SPA)
SANCTUM_STATEFUL_DOMAINS=pams.produkmastah.com,localhost,127.0.0.1
```

## Additional Steps

1. **Clear all caches on server:**

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

2. **Regenerate permissions:**

```bash
php artisan shield:generate --all
php artisan shield:super-admin --user=2
```

3. **Clear browser cookies** for `pams.produkmastah.com`

4. **Test in Incognito mode** to ensure fresh session

## Verification

After applying changes, test:

1. Login at `https://pams.produkmastah.com/admin`
2. Should redirect to dashboard instead of 403
3. Check browser DevTools → Application → Cookies
    - Should see session cookie with `Secure` flag
    - Should see `SameSite=Lax`

## If Still Not Working

Check server logs:

```bash
tail -f storage/logs/laravel.log
```

Look for session-related errors or authentication failures.

# Troubleshooting: 403 Access Denied After Login

## Problem Summary

After successful login at `https://pams.produkmastah.com/admin`, user gets **403 Access Denied** instead of being redirected to dashboard.

**Symptoms:**

-   Login works perfectly on local server
-   Production server shows 403 after authentication
-   User credentials are correct
-   User has super_admin role

## Root Cause

Session cookies are not being persisted correctly on production due to HTTPS/proxy configuration mismatch.

## Solution Steps

### Step 1: Verify Configuration

Access `https://pams.produkmastah.com/check-session-config.php` to verify all settings.

Expected configuration:

-   ✓ APP_ENV = production
-   ✓ APP_DEBUG = false
-   ✓ SESSION_SECURE_COOKIE = true
-   ✓ SESSION_HTTP_ONLY = true
-   ✓ SESSION_SAME_SITE = lax
-   ✓ HTTPS detected = yes

### Step 2: Update .env on Server

Ensure these settings in production `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pams.produkmastah.com

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

SANCTUM_STATEFUL_DOMAINS=pams.produkmastah.com,localhost,127.0.0.1
```

### Step 3: Clear Caches on Server

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Regenerate Permissions

```bash
php artisan shield:generate --all
php artisan shield:super-admin --user=2
```

### Step 5: Clear Browser Data

1. Open browser DevTools (F12)
2. Go to Application → Cookies
3. Delete all cookies for `pams.produkmastah.com`
4. Close DevTools
5. Try login in **Incognito/Private mode**

### Step 6: Verify Session Cookie

After login attempt, check browser DevTools → Application → Cookies:

Expected cookie attributes:

-   Name: `laravel_session` or similar
-   Domain: `pams.produkmastah.com`
-   Path: `/`
-   Secure: ✓ (checked)
-   HttpOnly: ✓ (checked)
-   SameSite: `Lax`

If cookie is missing or doesn't have Secure flag, session won't persist.

## Automated Deployment

Use the deployment script:

```bash
# Upload deploy-to-server.sh to server
chmod +x deploy-to-server.sh
./deploy-to-server.sh
```

This script will:

1. Update environment configuration
2. Clear all caches
3. Cache configuration
4. Regenerate permissions
5. Set up super admin
6. Publish Livewire assets
7. Set correct file permissions

## Debug Logging

Check Laravel logs for authentication issues:

```bash
tail -f storage/logs/laravel.log
```

Look for entries with:

-   `Auth Debug` - Shows authentication state
-   `is_authenticated` - Should be `true` after login
-   `user_roles` - Should include `super_admin`

## Common Issues

### Issue 1: Cookie Not Set

**Symptom:** No session cookie in browser after login

**Cause:** `SESSION_SECURE_COOKIE` not set to `true` for HTTPS

**Fix:** Update `.env` and clear config cache

### Issue 2: HTTPS Not Detected

**Symptom:** `session.secure` is `false` even with HTTPS

**Cause:** Proxy headers not trusted

**Fix:** Verify `TrustProxies` middleware is registered and includes Cloudflare IPs

### Issue 3: Session Expires Immediately

**Symptom:** Login succeeds but immediately logged out

**Cause:** Session domain mismatch or SameSite policy

**Fix:** Set `SESSION_DOMAIN=null` and `SESSION_SAME_SITE=lax`

### Issue 4: 403 Despite Valid Session

**Symptom:** Session exists but still 403

**Cause:** Missing permissions or role

**Fix:** Run `php artisan shield:super-admin --user=2`

## Verification Checklist

-   [ ] `.env` has correct production settings
-   [ ] `php artisan config:cache` executed
-   [ ] Browser cookies cleared
-   [ ] Tested in Incognito mode
-   [ ] Session cookie has Secure flag
-   [ ] User has super_admin role
-   [ ] Permissions regenerated
-   [ ] Logs show `is_authenticated: true`

## If Still Not Working

1. **Check OpenLiteSpeed configuration:**

    - Ensure PHP session handling is enabled
    - Verify document root is `/path/to/public`

2. **Check file permissions:**

    ```bash
    chmod -R 755 storage bootstrap/cache
    chmod -R 775 storage/logs
    ```

3. **Test session manually:**

    ```bash
    php artisan tinker
    >>> session()->put('test', 'value');
    >>> session()->get('test');
    ```

4. **Check database sessions table:**

    ```bash
    php artisan tinker
    >>> DB::table('sessions')->count();
    ```

5. **Contact hosting provider:**
    - Ask about session handling
    - Verify HTTPS headers are passed correctly
    - Check for any security restrictions

## Related Files

-   `app/Http/Middleware/TrustProxies.php` - Proxy configuration
-   `app/Http/Middleware/ForceHttpsDetection.php` - HTTPS detection
-   `app/Http/Middleware/CheckUserStatus.php` - User status check
-   `app/Http/Middleware/DebugAuth.php` - Authentication debugging
-   `config/session.php` - Session configuration
-   `public/check-session-config.php` - Configuration checker

## Success Criteria

After applying all fixes:

1. Login at `https://pams.produkmastah.com/admin`
2. Redirected to dashboard (not 403)
3. Can access all admin resources
4. Session persists across page refreshes
5. Logout works correctly

---

**Last Updated:** 2025-11-29  
**Status:** In Progress - Awaiting server configuration verification

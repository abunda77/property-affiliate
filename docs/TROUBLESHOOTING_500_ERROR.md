# Troubleshooting: 500 Server Error

## Problem: Random 500 Server Errors

### Symptoms

-   Application randomly shows 500 Server Error page
-   Error message: "RecursiveDirectoryIterator::\_\_construct(): Argument #1 ($directory) cannot be empty"
-   Happens intermittently, especially on Livewire requests
-   Debug info shows error from `barryvdh\laravel-debugbar`

### Root Cause

**Laravel Debugbar** storage path is empty in `.env` configuration:

```env
DEBUGBAR_STORAGE_PATH=
```

When Debugbar tries to store request data for debugging, it attempts to scan an empty directory path, causing the RecursiveDirectoryIterator error.

### Solution

#### 1. Fix .env Configuration

Update `.env` file:

```env
DEBUGBAR_STORAGE_PATH=storage/debugbar
```

#### 2. Create Storage Directory

```bash
mkdir storage/debugbar
```

Or on Windows:

```cmd
mkdir storage\debugbar
```

#### 3. Clear Configuration Cache

```bash
php artisan config:clear
php artisan cache:clear
```

#### 4. Verify Fix

```bash
php artisan config:show debugbar.storage.path
# Should output: storage/debugbar
```

### Prevention

The `storage/debugbar` directory is now created with a `.gitignore` file to ensure it exists in all environments.

### Alternative: Disable Debugbar Storage

If you don't need Debugbar's storage feature (for AJAX request debugging), you can disable it:

```env
DEBUGBAR_STORAGE_ENABLED=false
```

### Production Recommendation

**Disable Debugbar completely in production:**

```env
# .env.production
APP_DEBUG=false
DEBUGBAR_ENABLED=false
```

Debugbar should only be enabled in local/development environments as it:

-   Exposes sensitive application data
-   Adds performance overhead
-   Can cause storage issues if misconfigured

### Related Files

-   `.env` - Environment configuration
-   `config/debugbar.php` - Debugbar configuration
-   `storage/debugbar/` - Debugbar storage directory

### Common Debugbar Issues

#### Issue: Debugbar not showing

**Check:**

1. `APP_DEBUG=true` in `.env`
2. `DEBUGBAR_ENABLED=true` in `.env`
3. Clear config cache: `php artisan config:clear`

#### Issue: Debugbar causing performance issues

**Solution:**

1. Disable slow collectors in `.env`:

```env
DEBUGBAR_COLLECTORS_EVENTS=false
DEBUGBAR_COLLECTORS_MODELS=false
DEBUGBAR_COLLECTORS_CACHE=false
```

2. Or disable completely:

```env
DEBUGBAR_ENABLED=false
```

#### Issue: Debugbar showing on production

**Solution:**
Ensure production `.env` has:

```env
APP_DEBUG=false
DEBUGBAR_ENABLED=false
```

### Stack Trace Reference

If you see this in logs:

```
ValueError: RecursiveDirectoryIterator::__construct(): Argument #1 ($directory) cannot be empty
at vendor/symfony/finder/Iterator/RecursiveDirectoryIterator.php:43
#3 vendor/barryvdh/laravel-debugbar/src/Storage/FilesystemStorage.php:72
```

This confirms the Debugbar storage path issue.

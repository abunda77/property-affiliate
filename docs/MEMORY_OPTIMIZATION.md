# Memory Optimization Guide

## Problem: Memory Exhausted Error

**Error Message:**

```
Allowed memory size of 134217728 bytes exhausted (tried to allocate 45125632 bytes)
```

**Meaning:** PHP tried to use more than 128MB of memory (server limit).

## Solutions Applied

### 1. Increased PHP Memory Limit

**File:** `public/.htaccess`

```apache
php_value memory_limit 256M
php_value upload_max_filesize 20M
php_value post_max_size 25M
php_value max_execution_time 300
```

**Alternative:** Edit `php.ini` on server:

```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
```

### 2. Optimized Database Queries

**PropertyCatalog.php:**

-   Limited columns with `select()`
-   Eager load only first image: `with(['media' => fn($q) => $q->limit(1)])`
-   Prevents N+1 query problem

### 3. Laravel Optimizations

Run these commands on server:

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## Deployment Steps

1. **Upload changes to server**
2. **Run deployment script:**

    ```bash
    bash deploy-to-server.sh
    ```

3. **Restart PHP-FPM (if available):**

    ```bash
    sudo systemctl restart php8.3-fpm
    ```

4. **Clear OPcache (if enabled):**
    ```bash
    php artisan optimize:clear
    ```

## Monitoring

**Check memory usage:**

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Check PHP memory limit
php -i | grep memory_limit

# Monitor server resources
top
htop
```

## Prevention

1. **Always eager load relationships** to avoid N+1 queries
2. **Use pagination** for large datasets
3. **Limit media loading** in list views
4. **Use chunking** for batch operations:

    ```php
    Property::chunk(100, function ($properties) {
        // Process in batches
    });
    ```

5. **Monitor slow queries:**
    ```php
    // In AppServiceProvider
    DB::listen(function ($query) {
        if ($query->time > 1000) {
            Log::warning('Slow query', [
                'sql' => $query->sql,
                'time' => $query->time
            ]);
        }
    });
    ```

## If Problem Persists

1. **Check server PHP configuration:**

    ```bash
    php -i | grep -E "memory_limit|max_execution_time"
    ```

2. **Contact hosting provider** to increase limits

3. **Consider upgrading hosting plan** if consistently hitting limits

4. **Profile memory usage:**
    ```php
    // Add to problematic code
    Log::info('Memory usage', [
        'current' => memory_get_usage(true) / 1024 / 1024 . 'MB',
        'peak' => memory_get_peak_usage(true) / 1024 / 1024 . 'MB'
    ]);
    ```

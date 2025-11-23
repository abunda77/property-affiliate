# Performance Optimization Guide

This document outlines the performance optimizations implemented in PAMS and how to configure them for production.

## Query Optimization

### Eager Loading
All Filament resources and Livewire components now use eager loading to prevent N+1 queries:

- **PropertyCatalog**: Eager loads `media` relationship
- **PropertyDetail**: Eager loads `media` relationship
- **LeadsTable**: Eager loads `property` relationship
- **PropertiesTable**: Eager loads `media` relationship
- **AffiliatePropertiesTable**: Eager loads `media` relationship

### Database Indexes
The following indexes are already in place:

**users table:**
- `affiliate_code` (indexed)
- `status` (indexed)

**properties table:**
- `slug` (indexed, unique)
- `status` (indexed)
- `price` (indexed)
- Fulltext index on `title`, `location`, `description`

**leads table:**
- `affiliate_id` (indexed, foreign key)
- `property_id` (indexed, foreign key)
- `status` (indexed)
- `created_at` (indexed)

**visits table:**
- `affiliate_id` (indexed, foreign key)
- `property_id` (indexed, foreign key)
- `created_at` (indexed)

### Optimized Analytics Queries
The `AnalyticsService` uses direct database queries with aggregations instead of Eloquent relationships for better performance:

- `getTopProperties()`: Uses JOIN and GROUP BY for efficient aggregation
- All analytics methods use database-level aggregations

## Caching Strategy

### Cache Configuration

**Development:**
```env
CACHE_STORE=database
```

**Production (Recommended):**
```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
```

### Cache Durations

1. **Property Listings**: 5 minutes (300 seconds)
   - Cached per filter combination
   - Automatically cleared when properties are updated

2. **Affiliate Analytics**: 15 minutes (900 seconds)
   - Cached per affiliate and date range
   - Key format: `affiliate_metrics_{affiliate_id}_{start_date}_{end_date}`

3. **Global Analytics**: 15 minutes (900 seconds)
   - Cached per date range
   - Key format: `global_metrics_{start_date}_{end_date}`

4. **Sitemap**: 24 hours (86400 seconds)
   - Regenerated automatically when properties are updated
   - Manual regeneration: `php artisan sitemap:generate`

### Cache Invalidation

Cache is automatically cleared when:
- Properties are created, updated, or deleted (via PropertyObserver)
- Sitemap is regenerated after property changes

To manually clear cache:
```bash
php artisan cache:clear
```

## Frontend Optimization

### Asset Minification

Vite is configured to minify JavaScript and CSS in production:

```javascript
// vite.config.js
build: {
    minify: 'terser',
    terserOptions: {
        compress: {
            drop_console: true, // Remove console.log in production
        },
    },
}
```

Build production assets:
```bash
npm run build
```

### Code Splitting

Vendor code is split into a separate chunk for better caching:

```javascript
rollupOptions: {
    output: {
        manualChunks: {
            vendor: ['alpinejs'],
        },
    },
}
```

### Image Optimization

Images are optimized using Spatie Media Library with multiple conversions:

**Conversions:**
- `thumb`: 300x300px (WebP + JPEG)
- `medium`: 800x600px (WebP + JPEG)
- `large`: 1920x1080px (WebP + JPEG)

**Lazy Loading:**
All images use `loading="lazy"` attribute for deferred loading.

**Responsive Images:**
Images use `<picture>` element with multiple sources and sizes:

```html
<picture>
    <source type="image/webp" srcset="..." sizes="...">
    <source type="image/jpeg" srcset="..." sizes="...">
    <img src="..." loading="lazy" decoding="async">
</picture>
```

### Livewire Optimization

**Lazy Loading:**
PropertyCatalog component uses `#[Lazy]` attribute for deferred loading with skeleton placeholder.

**Debouncing:**
Search and filter inputs use `wire:model.live.debounce.500ms` to reduce server requests.

## CDN Configuration (Optional)

For production, consider using a CDN for static assets:

### CloudFlare Setup

1. Add your domain to CloudFlare
2. Enable Auto Minify for HTML, CSS, and JS
3. Enable Brotli compression
4. Set cache rules:
   - Images: Cache for 1 month
   - CSS/JS: Cache for 1 week
   - HTML: Cache for 5 minutes

### Laravel Configuration

Update `config/filesystems.php` to use S3 or compatible storage:

```php
'disks' => [
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
    ],
],
```

Update `.env`:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
AWS_URL=https://your-cdn-url.com
```

## Performance Monitoring

### Laravel Telescope (Development)

Install Telescope for development monitoring:
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Production Monitoring

Consider using:
- **New Relic**: Application performance monitoring
- **DataDog**: Infrastructure and application monitoring
- **Sentry**: Error tracking and performance monitoring

## Performance Checklist

### Before Deployment

- [ ] Run `npm run build` to minify assets
- [ ] Configure Redis for cache storage
- [ ] Set `CACHE_STORE=redis` in production `.env`
- [ ] Enable OPcache in PHP configuration
- [ ] Configure CDN for static assets (optional)
- [ ] Set up database connection pooling
- [ ] Enable Gzip/Brotli compression in web server
- [ ] Configure HTTP/2 in web server

### After Deployment

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan sitemap:generate`
- [ ] Verify Redis connection: `php artisan tinker` → `Cache::get('test')`
- [ ] Test page load times with browser DevTools
- [ ] Monitor cache hit rates

## Expected Performance Improvements

With all optimizations enabled:

- **Property Catalog**: 40-60% faster load time (with cache)
- **Analytics Dashboard**: 50-70% faster (with cache)
- **Database Queries**: 30-50% reduction in query count (eager loading)
- **Asset Load Time**: 30-40% reduction (minification + code splitting)
- **Image Load Time**: 50-60% reduction (lazy loading + WebP)

## Troubleshooting

### Cache Not Working

Check Redis connection:
```bash
redis-cli ping
# Should return: PONG
```

Check Laravel cache:
```bash
php artisan tinker
Cache::put('test', 'value', 60);
Cache::get('test');
# Should return: "value"
```

### Slow Queries

Enable query logging in development:
```php
// In AppServiceProvider boot()
DB::listen(function ($query) {
    if ($query->time > 100) { // Log queries over 100ms
        Log::warning('Slow query', [
            'sql' => $query->sql,
            'time' => $query->time,
        ]);
    }
});
```

### High Memory Usage

Check for N+1 queries using Laravel Debugbar:
```bash
composer require barryvdh/laravel-debugbar --dev
```

## Additional Resources

- [Laravel Performance Best Practices](https://laravel.com/docs/11.x/deployment#optimization)
- [Livewire Performance Tips](https://livewire.laravel.com/docs/performance)
- [Spatie Media Library Optimization](https://spatie.be/docs/laravel-medialibrary/v11/responsive-images/getting-started-with-responsive-images)

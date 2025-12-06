# Dokumentasi Fitur Terbaru Laravel 11

> **Sumber Dokumentasi**: Context7 - Laravel Official Documentation  
> **Tanggal**: 6 Desember 2024  
> **Versi**: Laravel 11.x

---

## 📋 Daftar Isi

1. [Streamlined Application Structure](#1-streamlined-application-structure)
2. [Per-Second Rate Limiting](#2-per-second-rate-limiting)
3. [Health Routing](#3-health-routing)
4. [Model Casts Method](#4-model-casts-method)
5. [Once Method - Prevent Memory Leaks](#5-once-method---prevent-memory-leaks)
6. [Conditional Migrations](#6-conditional-migrations)
7. [Laravel Pennant - Feature Flags](#7-laravel-pennant---feature-flags)
8. [Batched Jobs Enhancement](#8-batched-jobs-enhancement)
9. [HTTP Client Retry Enhancement](#9-http-client-retry-enhancement)
10. [Publish Package Migrations](#10-publish-package-migrations)

---

## 1. Streamlined Application Structure

### 🎯 Ringkasan
Laravel 11 memperkenalkan struktur aplikasi yang lebih ramping dan sederhana, mengurangi boilerplate code dan file konfigurasi yang tidak perlu.

### ✨ Perubahan Utama
- Middleware yang lebih sederhana
- Struktur service provider yang lebih slim
- Konfigurasi default yang lebih minimal
- File bootstrap yang lebih efisien

### 💡 Keuntungan
- ✅ Lebih mudah untuk pemula
- ✅ Mengurangi kompleksitas awal
- ✅ Lebih cepat untuk memulai project baru
- ✅ Tetap fleksibel untuk customization

---

## 2. Per-Second Rate Limiting

### 🎯 Ringkasan
Laravel 11 menambahkan kemampuan untuk membatasi request **per detik**, bukan hanya per menit. Ini sangat berguna untuk API yang membutuhkan kontrol lebih granular.

### 📋 Basic Rate Limiting

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
}
```

### 📋 Multiple Rate Limits

Anda bisa menerapkan multiple rate limits sekaligus:

```php
RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(500),  // Global limit
        Limit::perMinute(3)->by($request->input('email')),  // Per email
    ];
});
```

### 📋 Global Rate Limiter

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(1000);
    });
}
```

### 💡 Use Cases
- API throttling yang lebih presisi
- Perlindungan terhadap brute force attacks
- Kontrol bandwidth untuk public endpoints
- Rate limiting berbeda untuk user tiers (free vs premium)

### ✨ Keuntungan
- ✅ Kontrol lebih granular
- ✅ Kombinasi multiple limits
- ✅ Segmentasi berdasarkan user/IP/email
- ✅ Mudah dikonfigurasi

---

## 3. Health Routing

### 🎯 Ringkasan
Laravel 11 menyediakan built-in health check routing untuk monitoring aplikasi. Ini sangat berguna untuk load balancers, monitoring systems, dan orchestration tools.

### 📋 Basic Implementation

```php
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
    ]);
});
```

### 📋 Advanced Health Check

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

Route::get('/health', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();
        $db_status = 'ok';
    } catch (\Exception $e) {
        $db_status = 'error';
    }

    try {
        // Check cache connection
        Cache::store()->get('health_check');
        $cache_status = 'ok';
    } catch (\Exception $e) {
        $cache_status = 'error';
    }

    return response()->json([
        'status' => ($db_status === 'ok' && $cache_status === 'ok') ? 'healthy' : 'unhealthy',
        'checks' => [
            'database' => $db_status,
            'cache' => $cache_status,
        ],
        'timestamp' => now(),
    ]);
});
```

### 💡 Use Cases
- Kubernetes liveness/readiness probes
- Load balancer health checks
- Uptime monitoring services
- CI/CD deployment verification

### ✨ Keuntungan
- ✅ Mudah diintegrasikan dengan monitoring tools
- ✅ Customizable check logic
- ✅ Standard endpoint untuk health checks
- ✅ Built-in support

---

## 4. Model Casts Method

### 🎯 Ringkasan
Laravel 11 memperkenalkan method `casts()` sebagai alternatif dari property `$casts`. Ini memberikan lebih banyak fleksibilitas dan type safety.

### 📋 Property Lama (Masih Didukung)

```php
class User extends Model
{
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];
}
```

### 📋 Method Baru (Recommended)

```php
class User extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }
}
```

### 📋 Enum Casting

```php
use App\Enums\ServerStatus;

class Server extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ServerStatus::class,
        ];
    }
}
```

### 📋 Advanced Example

```php
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'options' => AsArrayObject::class,
            'tags' => AsCollection::class,
            'metadata' => 'array',
        ];
    }
}
```

### ✨ Keuntungan
- ✅ Better IDE autocomplete
- ✅ Type safety
- ✅ Lebih mudah dipahami
- ✅ Konsisten dengan method-based configuration

---

## 5. Once Method - Prevent Memory Leaks

### 🎯 Ringkasan
Method `once()` memastikan bahwa closure hanya dieksekusi sekali dan hasilnya di-cache untuk pemanggilan berikutnya. Ini mencegah memory leaks dalam long-running processes.

### 📋 Basic Usage

```php
use Illuminate\Support\Facades\Once;

public function getExpensiveData()
{
    return Once::once(function () {
        // This will only run once
        return $this->loadDataFromAPI();
    });
}
```

### 💡 Use Cases
- Long-running queue workers
- Octane applications
- Data caching dalam single request
- Expensive calculations

### ✨ Keuntungan
- ✅ Prevent memory leaks
- ✅ Automatic caching
- ✅ Simple API
- ✅ Safe for long-running processes

---

## 6. Conditional Migrations

### 🎯 Ringkasan
Laravel 11 memungkinkan Anda untuk skip migrations secara kondisional menggunakan method `shouldRun()`.

### 📋 Implementation

```php
use App\Models\Flights;
use Laravel\Pennant\Feature;

/**
 * Determine if this migration should run.
 */
public function shouldRun(): bool
{
    return Feature::active(Flights::class);
}
```

### 📋 Example Use Cases

```php
// Skip migration in testing environment
public function shouldRun(): bool
{
    return ! app()->environment('testing');
}

// Run only if feature is enabled
public function shouldRun(): bool
{
    return config('features.new_dashboard');
}

// Run based on custom condition
public function shouldRun(): bool
{
    return DB::table('settings')->where('key', 'enable_feature')->value('value') === 'true';
}
```

### ✨ Keuntungan
- ✅ Feature flag support
- ✅ Environment-specific migrations
- ✅ Conditional rollouts
- ✅ Flexible deployment strategies

---

## 7. Laravel Pennant - Feature Flags

### 🎯 Ringkasan
Laravel Pennant adalah package official untuk mengelola feature flags di aplikasi Laravel. Sekarang built-in di Laravel 11.

### 📋 Installation & Setup

```bash
php artisan vendor:publish --provider="Laravel\Pennant\PennantServiceProvider"
```

### 📋 Define Feature Flag

```php
use Illuminate\Support\Arr;
use Laravel\Pennant\Feature;

Feature::define('purchase-button', fn () => Arr::random([
    'blue-sapphire',
    'seafoam-green',
    'tart-orange',
]));
```

### 📋 Check Feature Flag

```php
use Laravel\Pennant\Feature;

// Simple check
if (Feature::active('new-api')) {
    // New API is enabled
}

// For specific user
if (Feature::for($user)->active('beta-features')) {
    // Show beta features
}
```

### 📋 Middleware Integration

```php
use App\Http\Middleware\EnsureFeaturesAreActive;

Route::get('/api/servers', function () {
    // ...
})->middleware(EnsureFeaturesAreActive::using('new-api', 'servers-api'));
```

### 📋 Testing Feature Flags

```php
use Laravel\Pennant\Feature;

public function test_purchase_button_color()
{
    // Override feature in test
    Feature::define('purchase-button', fn () => 'blue-sapphire');
    
    $response = $this->get('/products');
    
    $response->assertSee('blue-sapphire');
}
```

### 💡 Use Cases
- A/B testing
- Gradual feature rollouts
- Beta features
- Kill switches
- User-specific features

### ✨ Keuntungan
- ✅ Built-in Laravel support
- ✅ Per-user feature flags
- ✅ Easy testing
- ✅ Database-backed
- ✅ Middleware support

---

## 8. Batched Jobs Enhancement

### 🎯 Ringkasan
Command `make:job` sekarang mendukung flag `--batched` untuk automatically scaffold jobs dengan `Batchable` trait.

### 📋 Generate Batched Job

```bash
php artisan make:job ProcessPodcast --batched
```

### 📋 Generated Code

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPodcast implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        // Process podcast...
    }
}
```

### 📋 Dispatch Batch

```php
use App\Jobs\ProcessPodcast;
use Illuminate\Support\Facades\Bus;

$batch = Bus::batch([
    new ProcessPodcast($podcast1),
    new ProcessPodcast($podcast2),
    new ProcessPodcast($podcast3),
])->then(function (Batch $batch) {
    // All jobs completed successfully...
})->catch(function (Batch $batch, Throwable $e) {
    // First batch job failure detected...
})->finally(function (Batch $batch) {
    // The batch has finished executing...
})->dispatch();
```

### ✨ Keuntungan
- ✅ Faster scaffolding
- ✅ Consistent implementation
- ✅ Built-in cancellation support
- ✅ Batch progress tracking

---

## 9. HTTP Client Retry Enhancement

### 🎯 Ringkasan
HTTP Client sekarang mendukung dynamic retry delays menggunakan closure untuk implementasi exponential backoff atau custom retry logic.

### 📋 Dynamic Retry with Closure

```php
use Exception;

$response = Http::retry(3, function (int $attempt, Exception $exception) {
    return $attempt * 100; // 100ms, 200ms, 300ms
})->post('https://api.example.com/data');
```

### 📋 Exponential Backoff

```php
use Exception;

$response = Http::retry(5, function (int $attempt, Exception $exception) {
    // Exponential backoff: 1s, 2s, 4s, 8s, 16s
    return pow(2, $attempt - 1) * 1000;
})->get('https://api.example.com/data');
```

### 📋 Conditional Retry

```php
use Exception;
use Illuminate\Http\Client\RequestException;

$response = Http::retry(3, function (int $attempt, Exception $exception) {
    // Only retry on specific errors
    if ($exception instanceof RequestException && $exception->response->status() === 429) {
        // Rate limited, wait longer
        return 5000 * $attempt;
    }
    
    return 1000; // Default 1 second
})->get('https://api.example.com/data');
```

### ✨ Keuntungan
- ✅ Exponential backoff support
- ✅ Custom retry logic
- ✅ Exception-based decisions
- ✅ Better API resilience

---

## 10. Publish Package Migrations

### 🎯 Ringkasan
Package developers sekarang bisa menggunakan `publishesMigrations()` method untuk publish migrations dengan automatic timestamp handling.

### 📋 Package Service Provider

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->publishesMigrations([
        __DIR__.'/../database/migrations' => database_path('migrations'),
    ]);
}
```

### 📋 Publish Migrations

```bash
php artisan vendor:publish --tag=migrations
```

Laravel automatically:
- Updates migration filenames dengan current timestamps
- Prevents migration conflicts
- Maintains migration order

### ✨ Keuntungan
- ✅ Automatic timestamp management
- ✅ No manual filename updates
- ✅ Prevents conflicts
- ✅ Easier package development

---

## 🎯 Kesimpulan

Laravel 11 membawa improvement signifikan dalam:

1. **Simplicity** - Struktur aplikasi yang lebih streamlined
2. **Performance** - Rate limiting per-second dan HTTP client enhancements
3. **Developer Experience** - Model casts method, batched jobs scaffolding
4. **Modern Features** - Feature flags dengan Pennant, conditional migrations
5. **Monitoring** - Health routing untuk production readiness
6. **Reliability** - Once method untuk prevent memory leaks

### 🚀 Upgrade ke Laravel 11

```bash
composer require laravel/framework:^11.0
php artisan migrate
php artisan optimize:clear
```

### 📚 Resources Tambahan

- [Official Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Laravel 11 Release Notes](https://laravel.com/docs/11.x/releases)
- [Laravel News](https://laravel-news.com)
- [Laracasts](https://laracasts.com)

---

**Terakhir diperbarui**: 6 Desember 2024  
**Dokumentasi dibuat menggunakan**: Context7 MCP Server & Laravel Official Documentation

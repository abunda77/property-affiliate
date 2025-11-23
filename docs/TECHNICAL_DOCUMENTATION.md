# PAMS Technical Documentation

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Database Schema](#database-schema)
3. [API Endpoints](#api-endpoints)
4. [Authentication & Authorization](#authentication--authorization)
5. [Core Services](#core-services)
6. [Event System](#event-system)
7. [Middleware](#middleware)
8. [Configuration](#configuration)
9. [Deployment](#deployment)
10. [Development Guide](#development-guide)

---

## System Architecture

### Technology Stack

**Backend**:
- PHP 8.3+
- Laravel 12.x
- MySQL 8.0+

**Frontend**:
- Livewire 3.x
- Alpine.js 3.x
- Tailwind CSS 4.x
- Vite 6.x

**Admin Panel**:
- FilamentPHP v4
- Filament Shield v4 (RBAC)
- Filament Spatie Media Library Plugin v4

**Key Libraries**:
- Laravel Sanctum 4.2 (API authentication)
- Laravel Scout 10.22 (search)
- Spatie Laravel Permission 6.23 (roles & permissions)
- Spatie Laravel Media Library 11.17 (media management)
- Spatie Laravel Settings 3.5 (dynamic settings)
- Spatie Laravel Sitemap 7.3 (SEO)

### Application Layers

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Filament, Livewire, Blade Templates)  │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│         Application Layer               │
│  (Controllers, Middleware, Services)    │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│           Domain Layer                  │
│     (Models, Events, Policies)          │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│         Infrastructure Layer            │
│  (Database, Cache, External APIs)       │
└─────────────────────────────────────────┘
```

### Directory Structure

```
app/
├── Console/Commands/      # Artisan commands
├── Enums/                 # Enum classes
├── Events/                # Event classes
├── Filament/              # Filament resources & widgets
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Helpers/               # Helper functions
├── Http/
│   ├── Controllers/       # HTTP controllers
│   ├── Middleware/        # Custom middleware
│   └── Requests/          # Form requests
├── Listeners/             # Event listeners
├── Livewire/              # Livewire components
├── Models/                # Eloquent models
├── Notifications/         # Notification classes
├── Observers/             # Model observers
├── Policies/              # Authorization policies
├── Providers/             # Service providers
├── Rules/                 # Validation rules
├── Services/              # Business logic services
└── Settings/              # Settings classes
```

---

## Database Schema

### Entity Relationship Diagram

```
users (1) ──────< (N) visits
users (1) ──────< (N) leads
properties (1) ──< (N) visits
properties (1) ──< (N) leads
properties (1) ──< (N) media
```

### Tables

#### users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20),
    affiliate_code VARCHAR(50) UNIQUE,
    status ENUM('pending', 'active', 'blocked') DEFAULT 'pending',
    profile_photo VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_affiliate_code (affiliate_code),
    INDEX idx_status (status)
);
```

**Key Fields**:
- `affiliate_code`: Unique 8-character code for tracking
- `status`: User account status (pending/active/blocked)
- `whatsapp`: For lead notifications

#### properties
```sql
CREATE TABLE properties (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    price BIGINT UNSIGNED NOT NULL,
    location TEXT NOT NULL,
    description TEXT,
    features JSON,
    specs JSON,
    status ENUM('draft', 'published', 'sold') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_price (price),
    FULLTEXT idx_search (title, location, description)
);
```

**Key Fields**:
- `slug`: SEO-friendly URL identifier
- `features`: JSON array of feature strings
- `specs`: JSON object of key-value specifications
- `status`: Visibility control (draft/published/sold)

#### leads
```sql
CREATE TABLE leads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id BIGINT UNSIGNED NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    status ENUM('new', 'follow_up', 'survey', 'closed', 'lost') DEFAULT 'new',
    notes TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (affiliate_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    INDEX idx_affiliate_id (affiliate_id),
    INDEX idx_property_id (property_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

**Key Fields**:
- `affiliate_id`: Nullable - lead may not have affiliate attribution
- `status`: Lead lifecycle tracking
- `notes`: Affiliate's conversation notes

#### visits
```sql
CREATE TABLE visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id BIGINT UNSIGNED NOT NULL,
    property_id BIGINT UNSIGNED NULL,
    visitor_ip VARCHAR(45),
    device VARCHAR(50),
    browser VARCHAR(50),
    url TEXT,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (affiliate_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL,
    INDEX idx_affiliate_id (affiliate_id),
    INDEX idx_property_id (property_id),
    INDEX idx_created_at (created_at)
);
```

**Key Fields**:
- `property_id`: Nullable - visit may be to catalog page
- `visitor_ip`: For analytics and fraud detection
- `device`, `browser`: For device breakdown analytics

### Relationships

**User Model**:
```php
public function visits(): HasMany
public function leads(): HasMany
```

**Property Model**:
```php
public function leads(): HasMany
public function visits(): HasMany
public function media(): MorphMany  // Spatie Media Library
```

**Lead Model**:
```php
public function affiliate(): BelongsTo
public function property(): BelongsTo
```

**Visit Model**:
```php
public function affiliate(): BelongsTo
public function property(): BelongsTo
```

---

## API Endpoints

### Authentication

#### POST /api/login
Authenticate user and receive access token.

**Request**:
```json
{
    "email": "affiliate@example.com",
    "password": "password123"
}
```

**Response** (200):
```json
{
    "success": true,
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "affiliate@example.com",
        "affiliate_code": "ABC12345",
        "status": "active"
    }
}
```

**Response** (401):
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

#### POST /api/logout
Revoke current access token.

**Headers**:
```
Authorization: Bearer {token}
```

**Response** (200):
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### GET /api/user
Get authenticated user information.

**Headers**:
```
Authorization: Bearer {token}
```

**Response** (200):
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "affiliate@example.com",
    "affiliate_code": "ABC12345",
    "status": "active",
    "whatsapp": "+6281234567890"
}
```

### API Usage Example

```php
// Login
$response = Http::post('https://yourdomain.com/api/login', [
    'email' => 'affiliate@example.com',
    'password' => 'password123'
]);

$token = $response->json('token');

// Authenticated request
$user = Http::withToken($token)
    ->get('https://yourdomain.com/api/user')
    ->json();
```

**Rate Limiting**: 60 requests per minute per IP

**Token Expiration**: Tokens expire after 30 days of inactivity

---

## Authentication & Authorization

### Roles

**Super Admin**:
- Full system access
- Property CRUD
- User management
- System settings
- All analytics

**Affiliate**:
- Own dashboard
- Own leads
- Own analytics
- Profile settings
- Property viewing (read-only)

### Permissions

Managed by Filament Shield with Spatie Permission:

```php
// Super Admin permissions
'view_any_property', 'create_property', 'update_property', 'delete_property',
'view_any_user', 'create_user', 'update_user', 'delete_user',
'view_any_lead', 'update_any_lead',
'view_settings', 'update_settings'

// Affiliate permissions
'view_own_leads', 'update_own_leads',
'view_own_analytics',
'view_properties', 'generate_links',
'update_own_profile'
```

### Policy Examples

**PropertyPolicy**:
```php
public function viewAny(User $user): bool
{
    return $user->hasRole('super_admin') || $user->hasRole('affiliate');
}

public function create(User $user): bool
{
    return $user->hasRole('super_admin');
}

public function update(User $user, Property $property): bool
{
    return $user->hasRole('super_admin');
}
```

**LeadPolicy**:
```php
public function viewAny(User $user): bool
{
    return true; // Filtered by scope
}

public function view(User $user, Lead $lead): bool
{
    return $user->hasRole('super_admin') || 
           $lead->affiliate_id === $user->id;
}

public function update(User $user, Lead $lead): bool
{
    return $user->hasRole('super_admin') || 
           $lead->affiliate_id === $user->id;
}
```

### Middleware Stack

```php
// Web routes
Route::middleware(['web', 'affiliate.tracking'])->group(function () {
    // Public routes with tracking
});

// Admin routes
Route::middleware(['web', 'auth', 'check.user.status'])->group(function () {
    // Authenticated routes
});

// API routes
Route::middleware(['api', 'auth:sanctum'])->group(function () {
    // API routes
});
```

---

## Core Services

### AnalyticsService

**Purpose**: Calculate performance metrics for affiliates and admins

**Key Methods**:
```php
public function getAffiliateMetrics(User $affiliate, Carbon $startDate, Carbon $endDate): array
{
    return [
        'total_visits' => $this->getTotalVisits($affiliate, $startDate, $endDate),
        'total_leads' => $this->getTotalLeads($affiliate, $startDate, $endDate),
        'conversion_rate' => $this->getConversionRate($affiliate, $startDate, $endDate),
        'device_breakdown' => $this->getDeviceBreakdown($affiliate, $startDate, $endDate),
        'top_properties' => $this->getTopProperties($affiliate, $startDate, $endDate),
    ];
}

public function getGlobalMetrics(Carbon $startDate, Carbon $endDate): array
{
    // Similar structure for all affiliates combined
}
```

**Usage**:
```php
$service = app(AnalyticsService::class);
$metrics = $service->getAffiliateMetrics(
    auth()->user(),
    now()->startOfMonth(),
    now()->endOfMonth()
);
```

### GoWAService

**Purpose**: Send WhatsApp notifications via GoWA API

**Configuration**:
```php
// config/services.php
'gowa' => [
    'username' => env('GOWA_USERNAME'),
    'password' => env('GOWA_PASSWORD'),
    'api_url' => env('GOWA_API_URL', 'https://api.gowa.id/v1'),
],
```

**Key Methods**:
```php
public function sendMessage(string $phone, string $message): bool
{
    try {
        $response = Http::withBasicAuth(
            config('services.gowa.username'),
            config('services.gowa.password')
        )->post(config('services.gowa.api_url') . '/send-message', [
            'phone' => $this->formatPhone($phone),
            'message' => $message
        ]);
        
        return $response->successful();
    } catch (\Exception $e) {
        Log::error('GoWA API Error', [
            'phone' => $phone,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

private function formatPhone(string $phone): string
{
    // Ensure format: +62XXXXXXXXXX
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    return '+' . $phone;
}
```

**Usage**:
```php
$service = app(GoWAService::class);
$success = $service->sendMessage(
    '+6281234567890',
    'Halo, ada prospek baru!'
);
```

### SeoService

**Purpose**: Generate SEO meta tags for property pages

**Key Methods**:
```php
public function generateMetaTags(Property $property): array
{
    return [
        'title' => $this->generateTitle($property),
        'description' => $this->generateDescription($property),
        'keywords' => $this->generateKeywords($property),
        'og:title' => $property->title,
        'og:description' => Str::limit($property->description, 200),
        'og:image' => $property->getFirstMediaUrl('images'),
        'og:url' => route('property.show', $property->slug),
        'og:type' => 'website',
    ];
}

private function generateTitle(Property $property): string
{
    return sprintf(
        '%s - %s | %s',
        $property->title,
        $property->location,
        config('app.name')
    );
}
```

**Usage**:
```php
$service = app(SeoService::class);
$metaTags = $service->generateMetaTags($property);

// In Blade template
@foreach($metaTags as $name => $content)
    <meta name="{{ $name }}" content="{{ $content }}">
@endforeach
```

### PromotionalPackageService

**Purpose**: Generate downloadable promotional materials

**Key Methods**:
```php
public function generatePackage(Property $property, User $affiliate): string
{
    $zipPath = storage_path('app/temp/promo-' . $property->slug . '-' . time() . '.zip');
    $zip = new ZipArchive();
    
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        // Add images
        foreach ($property->getMedia('images') as $media) {
            $zip->addFile($media->getPath(), 'images/' . $media->file_name);
        }
        
        // Add description file
        $description = $this->generateDescriptionFile($property, $affiliate);
        $zip->addFromString('description.txt', $description);
        
        $zip->close();
    }
    
    return $zipPath;
}
```

### HtmlSanitizerService

**Purpose**: Sanitize HTML content to prevent XSS

**Key Methods**:
```php
public function sanitize(string $html): string
{
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'p,br,strong,em,u,a[href],ul,ol,li,h1,h2,h3');
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}
```

---

## Event System

### Events

#### LeadCreated
```php
class LeadCreated
{
    public function __construct(
        public Lead $lead
    ) {}
}
```

**Dispatched**: When contact form is submitted

**Listeners**: `SendLeadNotification`

### Listeners

#### SendLeadNotification
```php
class SendLeadNotification
{
    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;
        
        // Send to affiliate
        if ($lead->affiliate) {
            $this->sendToAffiliate($lead);
        }
        
        // Send confirmation to visitor
        $this->sendToVisitor($lead);
    }
    
    private function sendToAffiliate(Lead $lead): void
    {
        $message = sprintf(
            "Halo, ada prospek baru atas nama %s untuk properti %s. Segera follow up!",
            $lead->name,
            $lead->property->title
        );
        
        app(GoWAService::class)->sendMessage(
            $lead->affiliate->whatsapp,
            $message
        );
    }
}
```

**Registration** (AppServiceProvider):
```php
Event::listen(
    LeadCreated::class,
    SendLeadNotification::class
);
```

---

## Middleware

### AffiliateTrackingMiddleware

**Purpose**: Track visitor activity and set affiliate attribution cookie

**Logic**:
```php
public function handle(Request $request, Closure $next)
{
    // Check for ref parameter
    if ($request->has('ref')) {
        $affiliate = User::where('affiliate_code', $request->ref)
            ->where('status', UserStatus::ACTIVE)
            ->first();
            
        if ($affiliate) {
            // Set cookie for 30 days
            Cookie::queue('affiliate_id', $affiliate->id, 43200);
            $this->recordVisit($affiliate->id, $request);
        }
    } 
    // Check for existing cookie
    elseif ($affiliateId = $request->cookie('affiliate_id')) {
        $this->recordVisit($affiliateId, $request);
    }
    
    return $next($request);
}

private function recordVisit(int $affiliateId, Request $request): void
{
    Visit::create([
        'affiliate_id' => $affiliateId,
        'property_id' => $this->extractPropertyId($request),
        'visitor_ip' => $request->ip(),
        'device' => $this->detectDevice($request),
        'browser' => $request->userAgent(),
        'url' => $request->fullUrl(),
    ]);
}
```

**Registration** (bootstrap/app.php):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\AffiliateTrackingMiddleware::class,
    ]);
})
```

### CheckUserStatus

**Purpose**: Prevent blocked users from accessing system

**Logic**:
```php
public function handle(Request $request, Closure $next)
{
    if (auth()->check() && auth()->user()->status === UserStatus::BLOCKED) {
        auth()->logout();
        return redirect()->route('login')
            ->with('error', 'Your account has been blocked.');
    }
    
    return $next($request);
}
```

---

## Configuration

### Environment Variables

```bash
# Application
APP_NAME="PAMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pams
DB_USERNAME=pams_user
DB_PASSWORD=secure_password

# GoWA API
GOWA_USERNAME=your_username
GOWA_PASSWORD=your_password
GOWA_API_URL=https://api.gowa.id/v1

# Google Analytics
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

# Scout
SCOUT_DRIVER=database

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Key Configuration Files

**config/services.php**:
```php
'gowa' => [
    'username' => env('GOWA_USERNAME'),
    'password' => env('GOWA_PASSWORD'),
    'api_url' => env('GOWA_API_URL', 'https://api.gowa.id/v1'),
],
```

**config/scout.php**:
```php
'driver' => env('SCOUT_DRIVER', 'database'),
```

**config/filament-shield.php**:
```php
'super_admin' => [
    'enabled' => true,
    'name' => 'super_admin',
],
```

---

## Deployment

See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) for detailed deployment instructions.

### Quick Deployment Checklist

- [ ] Server requirements met (PHP 8.3+, MySQL 8.0+)
- [ ] Clone repository
- [ ] Install dependencies (`composer install --no-dev`)
- [ ] Configure `.env` file
- [ ] Generate app key (`php artisan key:generate`)
- [ ] Run migrations (`php artisan migrate --force`)
- [ ] Seed initial data (`php artisan db:seed`)
- [ ] Link storage (`php artisan storage:link`)
- [ ] Build assets (`npm run build`)
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up SSL certificate
- [ ] Configure queue worker
- [ ] Set up cron jobs
- [ ] Test critical features

---

## Development Guide

### Setup Development Environment

1. **Clone Repository**:
   ```bash
   git clone https://github.com/yourorg/pams.git
   cd pams
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup Database**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start Development Server**:
   ```bash
   composer dev
   # Or manually:
   # php artisan serve
   # php artisan queue:listen
   # npm run dev
   ```

### Running Tests

```bash
# All tests
php artisan test

# Specific test
php artisan test --filter=PropertySearchTest

# With coverage
php artisan test --coverage
```

### Code Style

```bash
# Fix code style
./vendor/bin/pint

# Check specific file
./vendor/bin/pint app/Models/User.php
```

### Common Commands

```bash
# Generate migration
php artisan make:migration create_table_name

# Generate model
php artisan make:model ModelName -mf

# Generate controller
php artisan make:controller ControllerName

# Generate Filament resource
php artisan make:filament-resource ResourceName

# Clear caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize
```

---

*Last Updated: November 2025*
*Version: 1.0*

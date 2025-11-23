# Project Structure

## Directory Organization

### `/app` - Application Core

#### `/app/Console`
- Artisan commands (e.g., `GenerateSitemap.php`)

#### `/app/Enums`
- Enum classes for type-safe constants (e.g., `UserStatus`, `PropertyStatus`, `LeadStatus`)

#### `/app/Events`
- Event classes (e.g., `LeadCreated.php`)

#### `/app/Filament`
- **`/Pages`**: Custom Filament pages (`Settings.php`, `ProfileSettings.php`)
- **`/Resources`**: Filament resource definitions organized by entity
  - `/AffiliateProperties`: Affiliate property views
  - `/Leads`: Lead management
  - `/Properties`: Property CRUD
  - `/Users`: User management
  - Each resource has subdirectories: `/Pages`, `/Schemas`, `/Tables`
- **`/Widgets`**: Dashboard widgets (analytics, stats, charts)

#### `/app/Helpers`
- Helper functions (e.g., `SettingsHelper.php`)

#### `/app/Http`
- **`/Controllers`**: HTTP controllers (e.g., `AffiliatePromoController.php`)
- **`/Middleware`**: Custom middleware (`AffiliateTrackingMiddleware.php`, `CheckUserStatus.php`)

#### `/app/Listeners`
- Event listeners (e.g., `SendLeadNotification.php`)

#### `/app/Livewire`
- Livewire components for frontend interactivity
  - `PropertyCatalog.php`, `PropertyDetail.php`, `ContactForm.php`

#### `/app/Models`
- Eloquent models: `User`, `Property`, `Lead`, `Visit`
- Models use traits: `HasFactory`, `HasRoles`, `InteractsWithMedia`, `Searchable`

#### `/app/Notifications`
- Notification classes (e.g., `AffiliateApprovedNotification.php`)

#### `/app/Observers`
- Model observers (e.g., `PropertyObserver.php` for sitemap updates)

#### `/app/Policies`
- Authorization policies: `UserPolicy`, `PropertyPolicy`, `LeadPolicy`
- Policies enforce role-based access control

#### `/app/Providers`
- Service providers: `AppServiceProvider`, `Filament/AdminPanelProvider`
- Register observers, event listeners, view composers

#### `/app/Services`
- Business logic services:
  - `AnalyticsService.php`: Metrics and reporting
  - `GoWAService.php`: WhatsApp API integration
  - `PromotionalPackageService.php`: Promotional features
  - `SeoService.php`: SEO utilities

#### `/app/Settings`
- Spatie Settings classes (e.g., `GeneralSettings.php`)

### `/bootstrap`
- `app.php`: Application bootstrap with middleware, routing, scheduling
- `/cache`: Framework cache files

### `/config`
- Configuration files: `app.php`, `database.php`, `filament-shield.php`, `permission.php`, etc.

### `/database`
- **`/factories`**: Model factories for testing
- **`/migrations`**: Database schema migrations
- **`/seeders`**: Database seeders (`RoleSeeder`, `SuperAdminSeeder`, `DatabaseSeeder`)

### `/docs`
- Project documentation (e.g., `RBAC_SETUP.md`)

### `/public`
- Public assets: `/css`, `/js`, `/fonts`, `/build` (compiled assets)
- Entry point: `index.php`

### `/resources`
- **`/css`**: Source CSS files
- **`/js`**: Source JavaScript files
- **`/views`**: Blade templates
  - `/filament`: Filament custom views
  - `/layouts`: Layout templates (`app.blade.php`)
  - `/livewire`: Livewire component views
  - `/properties`: Property views

### `/routes`
- `web.php`: Web routes
- `console.php`: Console commands

### `/storage`
- **`/app`**: Application storage
- **`/framework`**: Framework cache, sessions, views
- **`/logs`**: Application logs
- **`/media-library`**: Spatie Media Library uploads

### `/tests`
- **`/Feature`**: Feature tests (e.g., `PropertySearchTest.php`, `LeadManagementTest.php`)
- **`/Unit`**: Unit tests (e.g., `AnalyticsServiceTest.php`)

## Key Architectural Patterns

### Model Conventions
- Use Eloquent relationships: `hasMany`, `belongsTo`
- Implement scopes for common queries (e.g., `scopePublished`, `scopeActive`)
- Cast JSON columns to arrays/objects
- Use enums for status fields

### Service Layer
- Business logic extracted to service classes
- Services injected via constructor or resolved from container
- Services handle external API integrations

### Event-Driven Architecture
- Events dispatched on key actions (e.g., `LeadCreated`)
- Listeners handle side effects (e.g., sending notifications)
- Registered in `AppServiceProvider`

### Middleware
- `AffiliateTrackingMiddleware`: Tracks visitors and sets cookies
- Applied globally to web routes in `bootstrap/app.php`

### Observers
- `PropertyObserver`: Regenerates sitemap on property changes
- Registered in `AppServiceProvider`

### Filament Resource Organization
- Resources split into subdirectories: `/Pages`, `/Schemas`, `/Tables`
- Schemas define form fields
- Tables define table columns and filters
- Pages handle CRUD operations

### Policy-Based Authorization
- All authorization logic in policy classes
- Policies automatically discovered by Laravel
- Used by Filament Shield for resource access control

## Naming Conventions

- **Models**: Singular PascalCase (e.g., `Property`, `User`)
- **Controllers**: PascalCase with `Controller` suffix (e.g., `AffiliatePromoController`)
- **Services**: PascalCase with `Service` suffix (e.g., `AnalyticsService`)
- **Migrations**: Snake_case with timestamp prefix (e.g., `2025_11_21_create_leads_table`)
- **Views**: Kebab-case (e.g., `property-catalog.blade.php`)
- **Routes**: Kebab-case (e.g., `/properties/show`, `/ref/{code}`)
- **Database Tables**: Plural snake_case (e.g., `properties`, `leads`, `visits`)
- **Database Columns**: Snake_case (e.g., `affiliate_code`, `created_at`)

## JSON Column Usage

Models use JSON columns for flexible data:
- `Property::$features`: Array of feature strings
- `Property::$specs`: Key-value object for specifications
- Cast to `array` or `json` in model `casts()` method

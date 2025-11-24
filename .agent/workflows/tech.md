---
description: Technology Stack
---

# Technology Stack

## Core Framework

- **PHP**: 8.2+ (8.3+ recommended)
- **Laravel**: 12.x
- **Database**: MySQL 8.0+ / MariaDB / SQLite (dev)

## Frontend Stack

- **Livewire**: 3.x (interactive UI components)
- **Alpine.js**: 3.x (lightweight JavaScript framework)
- **Tailwind CSS**: 4.x (utility-first CSS)
- **Vite**: 6.x (build tool)

## Admin Panel

- **FilamentPHP**: v4 (admin dashboard framework)
- **Filament Shield**: v4 (RBAC with Spatie Permission integration)
- **Filament Spatie Media Library Plugin**: v4
- **Filament Spatie Settings Plugin**: v4

## Key Libraries

### Backend
- `laravel/sanctum`: ^4.2 (API authentication)
- `laravel/scout`: ^10.22 (search functionality)
- `spatie/laravel-permission`: ^6.23 (roles & permissions)
- `spatie/laravel-medialibrary`: ^11.17 (media management)
- `spatie/laravel-settings`: ^3.5 (dynamic settings)
- `spatie/laravel-sitemap`: ^7.3 (SEO sitemap generation)

### Development
- `laravel/pint`: ^1.13 (code style fixer)
- `phpunit/phpunit`: ^11.5.3 (testing)

## External Integrations

- **GoWA API**: WhatsApp messaging gateway (Basic Auth)
- **Google Analytics**: Embedded analytics tracking

## Common Commands

### Development
```bash
# Start development server with queue, logs, and vite
composer dev

# Or manually:
php artisan serve
php artisan queue:listen
npm run dev
```

### Database
```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Seed specific seeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SuperAdminSeeder
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PropertySearchTest
```

### Code Quality
```bash
# Fix code style with Laravel Pint
./vendor/bin/pint

# Run Pint on specific file
./vendor/bin/pint app/Models/User.php
```

### Assets
```bash
# Build for production
npm run build

# Development with hot reload
npm run dev
```

### Filament Shield
```bash
# Generate permissions for all resources
php artisan shield:generate --all

# Assign super admin role to user
php artisan shield:super-admin --user=1

# Show all permissions
php artisan permission:show

# Clear permission cache
php artisan permission:cache-reset
```

### SEO & Sitemap
```bash
# Generate sitemap (runs daily at 2:00 AM via scheduler)
php artisan sitemap:generate
```

### Storage
```bash
# Create symbolic link for storage
php artisan storage:link
```

## Build System

- **Package Manager**: Composer (PHP), NPM (JavaScript)
- **Asset Bundler**: Vite with Laravel plugin
- **CSS Framework**: Tailwind CSS 4.x with Vite plugin
- **Code Style**: Laravel Pint (PSR-12 based)

## Environment Configuration

Key `.env` variables:
- `DB_*`: Database credentials
- `GOWA_USERNAME`, `GOWA_PASSWORD`, `GOWA_API_URL`: WhatsApp API
- `GOOGLE_ANALYTICS_ID`: Analytics tracking ID
- `SCOUT_DRIVER`: Search driver (database/meilisearch/algolia)

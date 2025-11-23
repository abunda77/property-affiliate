# Property Affiliate Management System (PAMS)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat&logo=php)](https://php.net)
[![FilamentPHP](https://img.shields.io/badge/Filament-4.x-FFAA00?style=flat)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat)](https://livewire.laravel.com)

A web-based property catalog platform with affiliate tracking capabilities. PAMS enables property owners to distribute listings through a network of affiliates, each equipped with unique tracking links and performance analytics.

## Features

- **Affiliate Tracking**: Cookie-based visitor tracking with unique referral codes (30-day retention)
- **Lead Management**: Capture and manage property inquiries with affiliate attribution
- **Real-time Notifications**: WhatsApp integration via GoWA API for instant lead alerts
- **Hybrid Analytics**: Internal database tracking + Google Analytics embedding
- **Role-Based Access**: Super Admin (full control) and Affiliate (own data only) roles
- **Smart Search**: Laravel Scout implementation for fast property search
- **Media Management**: Optimized image handling with multiple conversions (webp, jpg)
- **SEO Optimized**: Dynamic sitemaps and meta tags for better search visibility

## Tech Stack

- **Backend**: Laravel 12.x, PHP 8.3+
- **Frontend**: Livewire 3.x, Alpine.js, Tailwind CSS 4.x
- **Admin Panel**: FilamentPHP v4 with Shield (RBAC)
- **Database**: MySQL 8.0+ / MariaDB
- **Key Libraries**: Spatie Media Library, Spatie Permission, Laravel Scout, Laravel Sanctum

## Requirements

- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL 8.0+ / MariaDB
- Web Server (Nginx/Apache/OpenLiteSpeed)

## Installation

```bash
# Clone repository
git clone <repository-url>
cd pams

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env, then run migrations
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Build assets
npm run build
```

## Development

```bash
# Start all services (server, queue, logs, vite)
composer dev

# Or run individually:
php artisan serve
php artisan queue:listen
npm run dev
```

## Configuration

### GoWA API (WhatsApp Integration)
Add to `.env`:
```env
GOWA_USERNAME=your_username
GOWA_PASSWORD=your_password
GOWA_API_URL=https://api.gowa.id/v1
```

### Google Analytics
Add to `.env`:
```env
GOOGLE_ANALYTICS_ID=UA-XXXXXXXXX-X
```

## Default Credentials

After seeding, use these credentials:
- **Super Admin**: `admin@pams.test` / `password`
- **Affiliate**: `affiliate@pams.test` / `password`

## Common Commands

```bash
# Database
php artisan migrate:fresh --seed

# Testing
php artisan test

# Code style
./vendor/bin/pint

# Permissions
php artisan shield:generate --all
php artisan permission:cache-reset

# Sitemap
php artisan sitemap:generate
```

## Project Structure

- `/app/Models` - Eloquent models (User, Property, Lead, Visit)
- `/app/Services` - Business logic (AnalyticsService, GoWAService)
- `/app/Filament` - Admin panel resources, pages, and widgets
- `/app/Livewire` - Frontend interactive components
- `/app/Policies` - Authorization policies
- `/database/migrations` - Database schema
- `/resources/views` - Blade templates

## Documentation

- [RBAC Setup](docs/RBAC_SETUP.md) - Role-based access control guide
- [Admin Credentials](ADMIN_CREDENTIALS.md) - Default login information

## License

This project is proprietary software. All rights reserved.

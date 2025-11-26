# Property Affiliate Management System (PAMS)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat&logo=php)](https://php.net)
[![FilamentPHP](https://img.shields.io/badge/Filament-4.x-FFAA00?style=flat)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat)](https://livewire.laravel.com)

A web-based property catalog platform with affiliate tracking capabilities. PAMS enables property owners to distribute listings through a network of affiliates, each equipped with unique tracking links and performance analytics.

## Features

### Core Features
- **Affiliate Tracking**: Cookie-based visitor tracking with unique referral codes (30-day retention)
- **Lead Management**: Capture and manage property inquiries with affiliate attribution
  - Lead status tracking: New, Follow Up, Survey, Closed, Lost
  - Automated WhatsApp notifications for new leads
- **Real-time Notifications**: WhatsApp integration via GoWA API for instant lead alerts
- **Hybrid Analytics**: Internal database tracking + Google Analytics embedding
- **Smart Search**: Laravel Scout implementation for fast property search
- **SEO Optimized**: Dynamic sitemaps and meta tags for better search visibility

### User Management
- **User Status System**: Active, Pending, and Blocked user states
- **Email Verification**: Built-in email verification for new registrations
- **Profile Management**: Users can update their profile, biodata, and contact information
- **Role-Based Access Control (RBAC)**: 
  - Super Admin: Full system control
  - Affiliate: Access to own data and performance metrics

### Property Management
- **Listing Types**: Support for Sale and Rent properties
- **Property Status**: Draft, Published, Sold, Rented
- **Media Management**: Optimized image handling with multiple conversions (webp, jpg)
  - Thumbnail (300x300), Medium (800x600), Large (1920x1080)
- **Rich Property Details**: Features, specifications, location, and pricing

### Settings & Configuration
- **General Settings**: 
  - Logo & Branding (logo, favicon)
  - SEO Configuration (meta title, description, keywords)
  - Contact Information (email, WhatsApp)
  - Legal Documents (Terms & Conditions, Privacy Policy, Disclaimer, About Us)
  - GoWA API Configuration
- **Backup System**: Database backup with logging (manual and scheduled)

### Dashboard & Analytics
- **Affiliate Widgets**:
  - Stats Overview (visits, leads, conversion rate)
  - Performance Chart (time-series data)
  - Affiliate Status Widget
- **Admin Widgets**:
  - Global Stats Overview
  - Global Performance Chart
  - Top Affiliates Leaderboard
  - Top Properties by Performance
  - Recent Activity Feed
  - Google Analytics Integration

### Frontend Features
- **Modern Property Catalog**: Premium design with advanced filtering
- **Property Detail Pages**: Comprehensive property information display
- **Contact Forms**: Livewire-powered contact forms with validation
- **Responsive Design**: Mobile-first approach with Tailwind CSS 4.x

## Tech Stack

- **Backend**: Laravel 12.x, PHP 8.3+
- **Frontend**: Livewire 3.x, Alpine.js, Tailwind CSS 4.x
- **Admin Panel**: FilamentPHP v4 with Shield (RBAC)
- **Database**: MySQL 8.0+ / MariaDB
- **Key Libraries**: 
  - Spatie Media Library (image handling)
  - Spatie Permission (RBAC)
  - Spatie Laravel Settings (configuration management)
  - Spatie Laravel Sitemap (SEO)
  - Laravel Scout (search functionality)
  - Laravel Sanctum (API authentication)
  - Dedoc Scramble (API documentation)

## Models & Enums

### User Status (UserStatus Enum)
- `PENDING` - Awaiting admin approval
- `ACTIVE` - Approved and active affiliate
- `BLOCKED` - Account blocked by admin

### Property Status (PropertyStatus Enum)
- `DRAFT` - Not yet published
- `PUBLISHED` - Live and visible to public
- `SOLD` - Property has been sold
- `RENTED` - Property has been rented

### Lead Status (LeadStatus Enum)
- `NEW` - Initial inquiry
- `FOLLOW_UP` - Being followed up
- `SURVEY` - Survey scheduled/completed
- `CLOSED` - Successfully closed
- `LOST` - Opportunity lost

### Property Listing Types
- `SALE` - Property for sale
- `RENT` - Property for rent

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
# This will also seed the database with default roles, permissions, and settings
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Build assets
npm run build
```

## Troubleshooting

### Missing Settings Table
If you encounter `Base table or view not found: 1146 Table 'settings' doesn't exist`, run:
```bash
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"
php artisan migrate
```

### Permission Errors
If you see 403 Forbidden errors in the admin panel, ensure permissions are synced:
```bash
php artisan shield:generate --all
php artisan permission:cache-reset
```

### Images Not Loading
Ensure the storage link is correctly created and permissions are set:
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
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

### Core Application
- `/app/Models` - Eloquent models
  - `User.php` - User model with affiliate functionality and status management
  - `Property.php` - Property listings with media library integration
  - `Lead.php` - Lead tracking with status workflow
  - `Visit.php` - Visitor tracking for analytics
  - `BackupLog.php` - Database backup logging
  
- `/app/Services` - Business logic layer
  - `AnalyticsService.php` - Analytics data aggregation and processing
  - `GoWAService.php` - WhatsApp notification integration
  
- `/app/Settings` - Application settings
  - `GeneralSettings.php` - System-wide configuration (Spatie Settings)

### Filament Admin Panel
- `/app/Filament/Resources` - CRUD resources for admin panel
  - `UserResource.php` - User management
  - `PropertyResource.php` - Property management
  - `LeadResource.php` - Lead management
  - `VisitResource.php` - Visit tracking
  
- `/app/Filament/Pages` - Custom admin pages
  - `Settings.php` - General settings management
  - `ProfileSettings.php` - User profile management
  - `BackupDatabase.php` - Database backup utility
  - `ApiDocumentation.php` - API documentation page
  - `Auth/` - Custom authentication pages
  
- `/app/Filament/Widgets` - Dashboard widgets
  - **Affiliate Widgets**:
    - `AffiliateStatsOverviewWidget.php` - Affiliate performance metrics
    - `AffiliatePerformanceChartWidget.php` - Time-series performance data
    - `AffiliateStatusWidget.php` - User status overview
  - **Admin Widgets**:
    - `GlobalStatsOverviewWidget.php` - System-wide statistics
    - `GlobalPerformanceChartWidget.php` - Overall performance trends
    - `TopAffiliatesWidget.php` - Top performing affiliates
    - `TopPropertiesWidget.php` - Most viewed/inquired properties
    - `RecentActivityWidget.php` - Recent system activity
    - `GoogleAnalyticsWidget.php` - Google Analytics integration

### Frontend Components
- `/app/Livewire` - Interactive frontend components
  - `PropertyCatalog.php` - Property listing with search and filters
  - `PropertyDetail.php` - Individual property display
  - `ContactForm.php` - Lead capture form

### Authorization & Policies
- `/app/Policies` - Authorization policies
  - Resource-based access control
  - Role-based permissions via Filament Shield

### Database
- `/database/migrations` - Database schema definitions
- `/database/seeders` - Sample data and initial setup
- `/database/factories` - Model factories for testing

### Views & Templates
- `/resources/views` - Blade templates
  - `/filament` - Filament customizations
  - `/livewire` - Livewire component views
  - `/layouts` - Application layouts

## Documentation

- [RBAC Setup](docs/RBAC_SETUP.md) - Role-based access control guide
- [Admin Credentials](ADMIN_CREDENTIALS.md) - Default login information
- [API Documentation](API_DOCUMENTATION.md) - API endpoints and usage
- [Scramble Installation](SCRAMBLE_INSTALLATION_SUMMARY.md) - API documentation setup

## Recent Updates

### November 2025

#### User Management Enhancements
- ✅ Added user status system (Active, Pending, Blocked)
- ✅ Implemented email verification for new registrations
- ✅ Added profile management page with biodata field
- ✅ Block user login notification on login page
- ✅ User approval workflow for affiliate registration

#### Settings & Configuration
- ✅ Added comprehensive settings page with multiple sections:
  - Logo & Branding (logo upload, favicon)
  - SEO Configuration (meta tags)
  - Contact Information
  - Legal Documents (Terms, Privacy Policy, Disclaimer, About Us)
  - GoWA API Configuration
- ✅ Dynamic logo and favicon display across all pages
- ✅ Database backup system with logging (manual and scheduled)

#### UI/UX Improvements
- ✅ Modernized property catalog with premium design
- ✅ Enhanced property detail page with hero section
- ✅ Professional button styling with hover effects
- ✅ Improved contact form with Livewire validation
- ✅ Responsive design updates for mobile devices

#### Dashboard & Analytics
- ✅ Added Affiliate Status Widget (Active/Pending users)
- ✅ Enhanced Google Analytics Widget with Filament styling
- ✅ Fixed affiliate lead count calculation
- ✅ Improved performance charts and metrics

#### Property Management
- ✅ Added listing type field (Sale/Rent)
- ✅ Updated property table with badge colors
- ✅ Added property infolist with view action
- ✅ Fixed property features display

#### Security & Authentication
- ✅ Custom registration page with email verification
- ✅ Email verification prompt page
- ✅ User status check middleware
- ✅ Enhanced RBAC with Filament Shield

#### Developer Experience
- ✅ API documentation with Scramble
- ✅ Improved code organization
- ✅ Enhanced error handling
- ✅ Better logging and debugging tools

## License

This project is proprietary software. All rights reserved.

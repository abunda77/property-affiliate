# Admin Credentials

## Super Admin Account

**Email:** erieputranto@gmail.com  
**Name:** Erie Administrator  
**Role:** super_admin  
**Panel:** admin  
**Login URL:** http://localhost:8007/admin/login

## Permissions Generated

Total permissions generated: **58 permissions**

### Resources with Permissions:

-   Lead (Create, View, ViewAny, Update, Delete, ForceDelete, ForceDeleteAny, Restore, RestoreAny, Replicate, Reorder)
-   Property (Create, View, ViewAny, Update, Delete, ForceDelete, ForceDeleteAny, Restore, RestoreAny, Replicate, Reorder)
-   User (Create, View, ViewAny, Update, Delete, ForceDelete, ForceDeleteAny, Restore, RestoreAny, Replicate, Reorder)
-   Role (Create, View, ViewAny, Update, Delete, ForceDelete, ForceDeleteAny, Restore, RestoreAny, Replicate, Reorder)

### Widgets with Permissions:

-   AffiliatePerformanceChartWidget (View)
-   AffiliateStatsOverviewWidget (View)
-   TopPropertiesWidget (View)

## Shield Commands

```bash
# Generate permissions for all resources
php artisan shield:generate --all

# Create super admin
php artisan shield:super-admin

# Show all permissions
php artisan permission:show
```

## Notes

-   Super admin memiliki akses penuh ke semua resources dan widgets
-   Password sudah di-set saat pembuatan akun
-   Semua permissions sudah di-assign ke role super_admin

## Fresh Seeding

### Fresh Setup

-   php artisan migrate:fresh
-   shield:setup [--fresh] [--tenant=] [--force] [--starred]
-   shield:install {panel} [--tenant]

-   php artisan db:seed
-   php artisan shield:generate --all
-   php artisan shield:super-admin
-   php artisan migrate:fresh;
-   php artisan db:seed;
-   php artisan shield:generate --all;
-   php artisan shield:super-admin

-   php artisan db:seed --class=PermissionSeeder
-   php artisan db:seed --class=RoleSeeder
-   php artisan db:seed --class=SuperAdminSeeder
-   php artisan db:seed --class=UserSeeder
-   php artisan db:seed --class=PropertySeeder
-   php artisan db:seed --class=LeadSeeder
-   php artisan db:seed --class=VisitSeeder
-   php artisan db:seed --class=LegalDocumentsSeeder

# Permision folder and file

-   chown -R pamsp6170:pamsp6170 storage bootstrap/cache
-   chown -R pamsp6170:pamsp6170 sitemap.xml
-   chmod -R 775 storage bootstrap/cache

# Akrtifkan Service

-   buat service queue-work systemctl
-   systemctl start queue:work
-   systemctl enable queue:work

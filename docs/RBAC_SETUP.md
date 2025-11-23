# Role-Based Access Control (RBAC) Setup

## Overview

The Property Affiliate Management System (PAMS) uses Filament Shield with Spatie Laravel Permission to implement role-based access control. This document explains the roles, permissions, and policies configured in the system.

## Roles

### 1. Super Admin
- **Role Name**: `super_admin`
- **Access Level**: Full system access
- **Permissions**: All permissions granted
- **Capabilities**:
  - Manage all properties (create, read, update, delete)
  - Manage all users (create, read, update, delete, approve, block)
  - Manage all leads (view, update, delete)
  - Access all dashboard widgets and analytics
  - Configure system settings
  - Manage roles and permissions

### 2. Affiliate
- **Role Name**: `affiliate`
- **Access Level**: Limited to own data
- **Permissions**: 
  - `view_any_lead` - View lead list
  - `view_lead` - View individual leads (own only)
  - `update_lead` - Update lead status and notes (own only)
  - `page_Dashboard` - Access dashboard
  - `widget_AccountWidget` - View account widget
- **Capabilities**:
  - View own dashboard with analytics
  - View and manage own leads
  - Update own profile
  - Generate tracking links
  - Download promotional materials

## Policies

### PropertyPolicy
**Location**: `app/Policies/PropertyPolicy.php`

All property management operations are restricted to Super Admin only:
- `viewAny()` - Super Admin only
- `view()` - Super Admin only
- `create()` - Super Admin only
- `update()` - Super Admin only
- `delete()` - Super Admin only
- `restore()` - Super Admin only
- `forceDelete()` - Super Admin only

### UserPolicy
**Location**: `app/Policies/UserPolicy.php`

User management is restricted to Super Admin, with exception for profile updates:
- `viewAny()` - Super Admin only
- `view()` - Super Admin only
- `create()` - Super Admin only
- `update()` - Super Admin can update any user, Affiliates can update own profile
- `delete()` - Super Admin only
- `restore()` - Super Admin only
- `forceDelete()` - Super Admin only

### LeadPolicy
**Location**: `app/Policies/LeadPolicy.php`

Lead management allows both Super Admin and Affiliates with scope restrictions:
- `viewAny()` - Super Admin and Affiliates (filtered to own leads)
- `view()` - Super Admin can view all, Affiliates can view own only
- `create()` - Super Admin only (public form creates leads without auth)
- `update()` - Super Admin can update all, Affiliates can update own only
- `delete()` - Super Admin only
- `restore()` - Super Admin only
- `forceDelete()` - Super Admin only

## Configuration Files

### Shield Configuration
**Location**: `config/filament-shield.php`

Key settings:
- `super_admin.enabled` - Set to `true`
- `super_admin.name` - Set to `super_admin`
- `auth_provider_model` - Set to `App\Models\User`
- `panel_user.enabled` - Set to `true`

### Permission Configuration
**Location**: `config/permission.php`

Uses default Spatie Permission configuration with:
- Guard: `web`
- Table names: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

## Database Tables

### Roles Table
Stores role definitions:
- `id` - Primary key
- `name` - Role name (super_admin, affiliate)
- `guard_name` - Guard name (web)
- `created_at`, `updated_at` - Timestamps

### Permissions Table
Stores permission definitions:
- `id` - Primary key
- `name` - Permission name (e.g., view_any_lead)
- `guard_name` - Guard name (web)
- `created_at`, `updated_at` - Timestamps

### Model Has Roles Table
Links users to roles:
- `role_id` - Foreign key to roles
- `model_type` - Model class (App\Models\User)
- `model_id` - User ID

### Role Has Permissions Table
Links roles to permissions:
- `permission_id` - Foreign key to permissions
- `role_id` - Foreign key to roles

## Seeders

### RoleSeeder
**Location**: `database/seeders/RoleSeeder.php`

Creates the two main roles and assigns permissions:
- Creates `super_admin` role with all permissions
- Creates `affiliate` role with limited permissions
- Run with: `php artisan db:seed --class=RoleSeeder`

### SuperAdminSeeder
**Location**: `database/seeders/SuperAdminSeeder.php`

Creates test users for development:
- Super Admin: `admin@pams.test` / `password`
- Affiliate: `affiliate@pams.test` / `password`
- Run with: `php artisan db:seed --class=SuperAdminSeeder`

## User Model Integration

The User model includes the `HasRoles` trait from Spatie Permission:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

This provides methods like:
- `$user->hasRole('super_admin')` - Check if user has role
- `$user->assignRole('affiliate')` - Assign role to user
- `$user->removeRole('affiliate')` - Remove role from user
- `$user->can('view_any_lead')` - Check if user has permission

## Artisan Commands

### Generate Permissions
```bash
php artisan shield:generate --all --option=policies_and_permissions
```
Generates permissions and policies for all Filament resources.

### Assign Super Admin
```bash
php artisan shield:super-admin --user=1
```
Assigns super_admin role to a user by ID.

### Show Permissions
```bash
php artisan permission:show
```
Displays a table of all roles and their permissions.

### Clear Permission Cache
```bash
php artisan permission:cache-reset
```
Clears the permission cache after making changes.

## Testing Access Control

### Test Super Admin Access
1. Login as `admin@pams.test`
2. Verify access to:
   - Property management
   - User management
   - All leads
   - System settings
   - Role management

### Test Affiliate Access
1. Login as `affiliate@pams.test`
2. Verify access to:
   - Own dashboard
   - Own leads only
   - Own profile settings
3. Verify NO access to:
   - Property management
   - User management
   - Other affiliates' leads
   - System settings

## Troubleshooting

### Permission Denied Errors
If users get 403 errors:
1. Check if user has correct role: `$user->roles`
2. Check if role has correct permissions: `$role->permissions`
3. Clear permission cache: `php artisan permission:cache-reset`
4. Verify policy methods return correct boolean values

### Policies Not Working
1. Ensure policies follow naming convention: `ModelPolicy`
2. Verify policies are in `app/Policies` directory
3. Check if User model has `HasRoles` trait
4. Verify Shield plugin is registered in panel provider

### Missing Permissions
1. Run `php artisan shield:generate --all` to regenerate
2. Run `php artisan db:seed --class=RoleSeeder` to reassign
3. Check `php artisan permission:show` to verify

## Security Best Practices

1. **Never hardcode role checks** - Always use policies and permissions
2. **Use gates for complex logic** - Define custom gates in AuthServiceProvider
3. **Scope queries by user** - Filter data based on user role in controllers
4. **Validate role assignments** - Only Super Admin can assign roles
5. **Audit role changes** - Log when roles are assigned or removed
6. **Regular permission reviews** - Periodically review and update permissions

## Future Enhancements

Potential improvements for the RBAC system:
1. Add commission-based permissions for affiliates
2. Implement team/organization-level roles
3. Add permission for viewing other affiliates' performance
4. Create custom permissions for specific features
5. Implement time-based role assignments
6. Add audit logging for permission changes

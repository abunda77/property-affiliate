# Troubleshooting: Registration & Email Verification

## Problem: User Cannot Login After Email Verification

### Symptoms

-   User successfully registers and receives verification email
-   User clicks verification link and email is verified
-   User tries to login but gets "These credentials do not match our records" error
-   User data exists in database with `email_verified_at` timestamp

### Root Causes

#### 1. Missing Role Assignment

New users are created with `status = PENDING` and no role assigned. After email verification, the user status remained PENDING without the required `affiliate` role, preventing login access to the admin panel.

#### 2. Double Password Hashing (CRITICAL)

The registration process was hashing the password twice:

-   First in `Register::handleRegistration()` with `Hash::make()`
-   Second by User model's `'password' => 'hashed'` cast

This caused authentication to always fail because the stored password hash was incorrect.

### Solution Implemented

#### 1. Fixed Double Password Hashing

**Problem:** Password was hashed twice causing authentication failure.

**Fix:** Removed `Hash::make()` from `Register::handleRegistration()` and let the User model's `'password' => 'hashed'` cast handle it automatically.

**File:** `app/Filament/Pages/Auth/Register.php`

```php
protected function handleRegistration(array $data): User
{
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'whatsapp' => $data['whatsapp'],
        'password' => $data['password'], // Don't hash here, User model will handle it
        'status' => UserStatus::PENDING,
    ]);

    event(new Registered($user));

    return $user;
}
```

#### 2. Auto-Approval After Email Verification

Created `ApproveUserAfterEmailVerification` listener that:

-   Listens to `Illuminate\Auth\Events\Verified` event
-   Automatically approves user (changes status from PENDING to ACTIVE)
-   Generates unique affiliate code
-   Assigns `affiliate` role to the user

**File:** `app/Listeners/ApproveUserAfterEmailVerification.php`

```php
public function handle(Verified $event): void
{
    $user = $event->user;

    if ($user instanceof User && $user->status === UserStatus::PENDING) {
        // Approve user and generate affiliate code
        $user->approve();

        // Assign affiliate role
        if (!$user->hasRole('affiliate')) {
            $user->assignRole('affiliate');
        }
    }
}
```

#### 3. Event Registration

Registered the listener in `AppServiceProvider`:

```php
Event::listen(Verified::class, ApproveUserAfterEmailVerification::class);
```

### User Registration Flow (After Fix)

1. User fills registration form (name, email, whatsapp, password)
2. User created with `status = PENDING` (no role yet)
3. Verification email sent automatically
4. User clicks verification link
5. **Auto-approval triggered:**
    - Status changed to `ACTIVE`
    - Affiliate code generated (e.g., "ABC12345")
    - `affiliate` role assigned
6. User can now login and access affiliate dashboard

### Manual Approval (Alternative)

If you prefer manual approval workflow:

1. Remove auto-approval listener from `AppServiceProvider`
2. Super Admin must manually approve users via:
    - Admin Panel → Users → Select user → "Approve" action
    - Or bulk approve: Select multiple users → "Bulk Approve"

### Testing

To test the registration flow:

```bash
# 1. Register new user via /admin/register
# 2. Check email for verification link (or check logs in local env)
# 3. Click verification link
# 4. Check user in database:
php artisan tinker
>>> $user = User::where('email', 'test@example.com')->first();
>>> $user->status; // Should be "active"
>>> $user->affiliate_code; // Should have code like "ABC12345"
>>> $user->getRoleNames(); // Should include "affiliate"
```

### Related Files

-   `app/Filament/Pages/Auth/Register.php` - Custom registration page
-   `app/Filament/Pages/Auth/EmailVerificationPrompt.php` - Email verification prompt
-   `app/Listeners/ApproveUserAfterEmailVerification.php` - Auto-approval listener
-   `app/Models/User.php` - User model with MustVerifyEmail interface
-   `app/Enums/UserStatus.php` - User status enum (PENDING, ACTIVE, BLOCKED)

### Fixing Existing Users (Production)

If you have existing users affected by the double hashing bug, use this command:

```bash
# Reset password for specific user
php artisan users:fix-passwords --email=user@example.com

# Reset passwords for ALL users (use with caution!)
php artisan users:fix-passwords --all
```

This will set a temporary password `TempPassword123!` for affected users. Inform them to:

1. Login with the temporary password
2. Change password immediately via Profile Settings

### Common Issues

#### Issue: User still can't login after verification

**Check:**

1. User has `email_verified_at` timestamp
2. User status is `ACTIVE` (not PENDING)
3. User has `affiliate` role assigned
4. User is not BLOCKED

```bash
php artisan tinker
>>> $user = User::where('email', 'user@example.com')->first();
>>> $user->email_verified_at; // Should have timestamp
>>> $user->status->value; // Should be "active"
>>> $user->getRoleNames(); // Should include "affiliate"
```

#### Issue: Verification email not sent

**Check:**

1. Mail configuration in `.env`
2. Queue is running (if using queue for emails)
3. Check `storage/logs/laravel.log` for errors

```bash
# Test email configuration
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

#### Issue: Listener not triggered

**Check:**

1. Cache is cleared: `php artisan optimize:clear`
2. Listener is registered in `AppServiceProvider`
3. Check logs for listener execution

### Security Considerations

-   Email verification is required before approval
-   Users cannot access admin panel until email is verified AND approved
-   Blocked users are immediately logged out (via `CheckUserStatus` middleware)
-   All new users start as PENDING for security

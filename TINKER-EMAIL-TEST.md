# Test Email Verifikasi - Laravel Tinker Commands

## Cara 1: Jalankan script lengkap

### PowerShell (Windows):
```powershell
Get-Content tinker-test-email.php | php artisan tinker
```

### Bash/Linux/Mac:
```bash
php artisan tinker < tinker-test-email.php
```

## Cara 2: Copy-paste command berikut ke tinker

### 1. Buat user baru dan kirim email verifikasi
```php
$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    'whatsapp' => '+628123456789',
    'status' => \App\Enums\UserStatus::PENDING,
]);

event(new \Illuminate\Auth\Events\Registered($user));
```

### 2. Kirim ulang email verifikasi ke user tertentu
```php
$user = \App\Models\User::where('email', 'test@example.com')->first();
$user->sendEmailVerificationNotification();
```

### 3. Cek apakah user sudah verified
```php
$user = \App\Models\User::where('email', 'test@example.com')->first();
$user->hasVerifiedEmail(); // true/false
```

### 4. Verify email secara manual (tanpa klik link)
```php
$user = \App\Models\User::where('email', 'test@example.com')->first();
$user->markEmailAsVerified();
```

### 5. Lihat semua user yang belum verified
```php
\App\Models\User::whereNull('email_verified_at')->get(['name', 'email', 'created_at']);
```

### 6. Lihat semua user yang sudah verified
```php
\App\Models\User::whereNotNull('email_verified_at')->get(['name', 'email', 'email_verified_at']);
```

### 7. Hapus user test
```php
\App\Models\User::where('email', 'test@example.com')->delete();
```

## Catatan:
- Pastikan mail server sudah running (MailHog/Mailpit di port 1025)
- Cek email di http://localhost:8025
- Untuk production, pastikan konfigurasi SMTP sudah benar di .env

# Force HTTPS Configuration

## Overview
Aplikasi ini dilengkapi dengan konfigurasi `FORCE_HTTPS` yang memungkinkan Anda memaksa penggunaan HTTPS saat running di server production.

## Cara Mengaktifkan

### 1. Update File `.env` di Server Production

Tambahkan atau ubah konfigurasi berikut di file `.env` Anda:

```env
FORCE_HTTPS=true
```

### 2. Pastikan APP_URL Menggunakan HTTPS

```env
APP_URL=https://yourdomain.com
```

### 3. Clear Cache (Jika Diperlukan)

Setelah mengubah konfigurasi, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

## Cara Kerja

Middleware `ForceHttpsDetection` akan:

1. **Mendeteksi HTTPS dari Proxy Headers**
   - Mendukung Cloudflare (`cf-visitor`)
   - Mendukung load balancers standar (`x-forwarded-proto`)
   - Mendukung reverse proxy (`HTTP_X_FORWARDED_PROTO`)

2. **Mengatur Server Variables**
   - Set `HTTPS` = 'on'
   - Set `SERVER_PORT` = 443
   - Set `REQUEST_SCHEME` = 'https'

3. **Redirect Otomatis ke HTTPS**
   - Jika request masih menggunakan HTTP, akan otomatis redirect ke HTTPS dengan status code 301 (Permanent Redirect)

## Penggunaan di Environment Berbeda

### Development (Local)
```env
FORCE_HTTPS=false
APP_URL=http://localhost
```

### Staging
```env
FORCE_HTTPS=true
APP_URL=https://staging.yourdomain.com
```

### Production
```env
FORCE_HTTPS=true
APP_URL=https://yourdomain.com
```

## Troubleshooting

### Infinite Redirect Loop
Jika terjadi infinite redirect loop, pastikan:
- Load balancer/proxy Anda mengirim header `X-Forwarded-Proto: https`
- SSL/TLS termination dilakukan di load balancer/proxy
- Middleware `TrustProxies` sudah dikonfigurasi dengan benar

### Mixed Content Warning
Jika masih ada mixed content warning:
1. Pastikan semua asset menggunakan `asset()` helper
2. Gunakan `secure_asset()` untuk asset yang harus HTTPS
3. Periksa external resources (CDN, API, dll) sudah menggunakan HTTPS

## File yang Terkait

- `app/Http/Middleware/ForceHttpsDetection.php` - Middleware utama
- `config/app.php` - Konfigurasi `force_https`
- `bootstrap/app.php` - Registrasi middleware
- `.env` - Environment variables

## Catatan Penting

⚠️ **Jangan aktifkan `FORCE_HTTPS=true` di local development** kecuali Anda sudah setup SSL certificate untuk localhost.

✅ **Selalu test di staging environment** sebelum deploy ke production.

🔒 **Pastikan SSL certificate valid** sebelum mengaktifkan force HTTPS.

# Web Server Configuration untuk PAMS

## Untuk OpenLiteSpeed

### 1. Virtual Host Configuration

```
Document Root: /path/to/property-affiliate/public
Index Files: index.php, index.html
```

### 2. Rewrite Rules

Pastikan rewrite rules aktif di OpenLiteSpeed:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

### 3. PHP Configuration

-   PHP Version: 8.2 atau 8.3
-   Extensions yang diperlukan:
    -   php-mysql
    -   php-mbstring
    -   php-xml
    -   php-curl
    -   php-zip
    -   php-gd
    -   php-fileinfo

### 4. Directory Permissions

```bash
chmod -R 755 /path/to/property-affiliate
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## Untuk Cloudflare Tunnel

### 1. Tunnel Configuration

```yaml
tunnel: your-tunnel-id
credentials-file: /path/to/credentials.json

ingress:
    - hostname: pams.produkmastah.com
      service: http://localhost:8080 # atau port yang digunakan OpenLiteSpeed
    - service: http_status:404
```

### 2. SSL/TLS Settings di Cloudflare

-   SSL/TLS encryption mode: **Flexible** atau **Full**
-   Always Use HTTPS: **Off** (untuk testing)

## Troubleshooting Steps

1. **Test direct access ke server:**

    ```bash
    curl -H "Host: pams.produkmastah.com" http://SERVER_IP/debug.php
    ```

2. **Test Laravel routing:**

    ```bash
    curl -H "Host: pams.produkmastah.com" http://SERVER_IP/route-test.php
    ```

3. **Check web server logs:**

    - OpenLiteSpeed: `/usr/local/lsws/logs/error.log`
    - Laravel: `storage/logs/laravel.log`

4. **Test dengan IP langsung:**
    ```bash
    curl http://SERVER_IP/debug.php
    ```

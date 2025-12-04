# Queue Worker Setup untuk Production

## Masalah

WhatsApp notifications tidak terkirim karena queue worker tidak berjalan di server.

## Solusi

### Opsi 1: Systemd Service (Recommended)

1. Buat file `/etc/systemd/system/laravel-queue-pams.service`:

```ini
[Unit]
Description=Laravel Queue PAMS
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=3
WorkingDirectory=/home/pams.produkmastah.com/public_html
EnvironmentFile=/home/pams.produkmastah.com/public_html/.env
ExecStart=/usr/local/lsws/lsphp83/bin/php /home/pams.produkmastah.com/public_html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=60
StandardOutput=append:/home/pams.produkmastah.com/public_html/storage/logs/queue-worker.log
StandardError=append:/home/pams.produkmastah.com/public_html/storage/logs/queue-worker-error.log

[Install]
WantedBy=multi-user.target
```

2. Set permissions untuk file service:

```bash
sudo chmod 644 /etc/systemd/system/laravel-queue-pams.service
```

3. Reload systemd dan enable service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue-pams
sudo systemctl start laravel-queue-pams
```

4. Check status:

```bash
sudo systemctl status laravel-queue-pams
```

5. Useful commands:

```bash
# Stop service
sudo systemctl stop laravel-queue-pams

# Restart service
sudo systemctl restart laravel-queue-pams

# View logs
sudo journalctl -u laravel-queue-pams -f

# View recent logs
sudo journalctl -u laravel-queue-pams -n 100
```

### Opsi 2: Cron Job (Fallback)

Jika tidak bisa setup supervisor/systemd, gunakan cron untuk process queue setiap menit:

```bash
* * * * * cd /path/to/your/project && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

**Note**: Opsi ini kurang efisien karena akan start/stop worker setiap menit.

### Opsi 3: Ubah ke Sync Queue (Tidak Recommended)

Jika tidak bisa setup queue worker sama sekali, ubah di `.env`:

```env
QUEUE_CONNECTION=sync
```

**Warning**: Ini akan membuat notifikasi dikirim secara synchronous, yang bisa memperlambat response time form submission.

## Monitoring Queue

### Check failed jobs:

```bash
php artisan queue:failed
```

### Retry failed jobs:

```bash
php artisan queue:retry all
```

### Clear failed jobs:

```bash
php artisan queue:flush
```

### Monitor queue in real-time:

```bash
php artisan queue:work --verbose
```

## Troubleshooting

### Queue worker tidak process jobs:

1. Check apakah worker berjalan: `ps aux | grep "queue:work"`
2. Check database table `jobs` untuk pending jobs
3. Check `failed_jobs` table untuk jobs yang gagal
4. Check log di `storage/logs/laravel.log`

### Restart queue worker setelah deploy:

```bash
# Restart via artisan (graceful restart)
php artisan queue:restart

# Atau restart service langsung
sudo systemctl restart laravel-queue-pams
```

## Setup di Server Production

### Langkah-langkah Setup:

1. **SSH ke server**

2. **Buat file service** (sebagai root atau dengan sudo):

```bash
sudo nano /etc/systemd/system/laravel-queue-pams.service
```

3. **Copy paste konfigurasi** dari Opsi 1 di atas, pastikan path sudah benar:

    - `/home/pams.produkmastah.com/public_html` → path project Anda
    - `/usr/local/lsws/lsphp83/bin/php` → path PHP Anda

4. **Pastikan file .env ada** dan readable:

```bash
ls -la /home/pams.produkmastah.com/public_html/.env
```

5. **Pastikan storage/logs writable**:

```bash
sudo chown -R www-data:www-data /home/pams.produkmastah.com/public_html/storage
sudo chmod -R 775 /home/pams.produkmastah.com/public_html/storage
```

6. **Enable dan start service**:

```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue-pams
sudo systemctl start laravel-queue-pams
```

7. **Verify service berjalan**:

```bash
sudo systemctl status laravel-queue-pams
```

Output yang benar akan menunjukkan `active (running)`.

8. **Monitor logs real-time**:

```bash
sudo journalctl -u laravel-queue-pams -f
```

### Troubleshooting Service:

**Service gagal start:**

```bash
# Check error detail
sudo journalctl -u laravel-queue-pams -n 50

# Check PHP path
which php
/usr/local/lsws/lsphp83/bin/php --version

# Test manual
cd /home/pams.produkmastah.com/public_html
/usr/local/lsws/lsphp83/bin/php artisan queue:work --once
```

**Permission issues:**

```bash
# Fix ownership
sudo chown -R www-data:www-data /home/pams.produkmastah.com/public_html/storage
sudo chown -R www-data:www-data /home/pams.produkmastah.com/public_html/bootstrap/cache
```

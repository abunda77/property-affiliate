# PAMS Deployment Guide

## Table of Contents
1. [Server Requirements](#server-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Initial Server Setup](#initial-server-setup)
4. [Application Deployment](#application-deployment)
5. [Web Server Configuration](#web-server-configuration)
6. [SSL Certificate Setup](#ssl-certificate-setup)
7. [Queue Worker Setup](#queue-worker-setup)
8. [Cron Jobs Configuration](#cron-jobs-configuration)
9. [Post-Deployment Verification](#post-deployment-verification)
10. [Maintenance & Updates](#maintenance--updates)

---

## Server Requirements

### Minimum Requirements

**Server**:
- Ubuntu 22.04 LTS or similar
- 2 CPU cores
- 4GB RAM
- 40GB SSD storage
- Stable internet connection

**Software**:
- PHP 8.3 or higher
- MySQL 8.0 or MariaDB 10.6+
- Nginx 1.18+ or Apache 2.4+
- Redis 6.0+ (for cache and queue)
- Composer 2.x
- Node.js 18+ and NPM
- Git

**PHP Extensions**:
```
php8.3-cli
php8.3-fpm
php8.3-mysql
php8.3-mbstring
php8.3-xml
php8.3-curl
php8.3-zip
php8.3-gd
php8.3-redis
php8.3-intl
php8.3-bcmath
```

### Recommended Production Setup

- 4 CPU cores
- 8GB RAM
- 100GB SSD storage
- CDN for static assets
- Database replication
- Automated backups
- Monitoring tools

---

## Pre-Deployment Checklist

### Code Preparation

- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Dependencies updated
- [ ] Environment variables documented
- [ ] Database migrations tested
- [ ] Seeders prepared
- [ ] Assets compiled for production

### Infrastructure

- [ ] Domain name registered
- [ ] DNS configured
- [ ] Server provisioned
- [ ] SSH access configured
- [ ] Firewall rules set
- [ ] SSL certificate ready
- [ ] Backup solution in place

### Third-Party Services

- [ ] GoWA API credentials obtained
- [ ] Google Analytics account created
- [ ] Email service configured (if applicable)
- [ ] CDN configured (if applicable)

---

## Initial Server Setup

### 1. Update System

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. Install PHP 8.3

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-redis \
    php8.3-intl php8.3-bcmath

# Verify installation
php -v
```

### 3. Install MySQL

```bash
# Install MySQL
sudo apt install -y mysql-server

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql
```

```sql
CREATE DATABASE pams CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pams_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON pams.* TO 'pams_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Install Redis

```bash
sudo apt install -y redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
# Set: supervised systemd

# Restart Redis
sudo systemctl restart redis
sudo systemctl enable redis

# Test Redis
redis-cli ping
# Should return: PONG
```

### 5. Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version
```

### 6. Install Node.js and NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify
node -v
npm -v
```

### 7. Create Application User

```bash
sudo adduser --disabled-password --gecos "" pams
sudo usermod -aG www-data pams
```

---

## Application Deployment

### 1. Clone Repository

```bash
# Switch to application user
sudo su - pams

# Clone repository
cd /var/www
git clone https://github.com/yourorg/pams.git
cd pams

# Set correct permissions
sudo chown -R pams:www-data /var/www/pams
sudo chmod -R 755 /var/www/pams
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci

# Build assets
npm run build
```

### 3. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

**Required .env Configuration**:
```bash
APP_NAME="PAMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pams
DB_USERNAME=pams_user
DB_PASSWORD=your_secure_password

GOWA_USERNAME=your_gowa_username
GOWA_PASSWORD=your_gowa_password
GOWA_API_URL=https://api.gowa.id/v1

GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Create super admin (if not seeded)
php artisan shield:super-admin --user=1
```

### 6. Link Storage

```bash
php artisan storage:link
```

### 7. Set Permissions

```bash
sudo chown -R pams:www-data /var/www/pams
sudo chmod -R 755 /var/www/pams
sudo chmod -R 775 /var/www/pams/storage
sudo chmod -R 775 /var/www/pams/bootstrap/cache
```

### 8. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Web Server Configuration

### Nginx Configuration

Create Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/pams
```

**Configuration**:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/pams/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Enable Site**:
```bash
sudo ln -s /etc/nginx/sites-available/pams /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Apache Configuration (Alternative)

Create Apache configuration file:

```bash
sudo nano /etc/apache2/sites-available/pams.conf
```

**Configuration**:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/pams/public

    <Directory /var/www/pams/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pams_error.log
    CustomLog ${APACHE_LOG_DIR}/pams_access.log combined
</VirtualHost>
```

**Enable Site**:
```bash
sudo a2enmod rewrite
sudo a2ensite pams.conf
sudo systemctl restart apache2
```

---

## SSL Certificate Setup

### Using Let's Encrypt (Recommended)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate (Nginx)
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Or for Apache
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

**Nginx will be automatically configured with SSL. Verify**:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Manual SSL Configuration

If using custom certificate:

```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # ... rest of configuration
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

---

## Queue Worker Setup

### Using Supervisor (Recommended)

```bash
# Install Supervisor
sudo apt install -y supervisor

# Create worker configuration
sudo nano /etc/supervisor/conf.d/pams-worker.conf
```

**Configuration**:
```ini
[program:pams-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pams/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=pams
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pams/storage/logs/worker.log
stopwaitsecs=3600
```

**Start Worker**:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pams-worker:*

# Check status
sudo supervisorctl status
```

### Using Systemd (Alternative)

```bash
sudo nano /etc/systemd/system/pams-worker.service
```

**Configuration**:
```ini
[Unit]
Description=PAMS Queue Worker
After=network.target

[Service]
Type=simple
User=pams
WorkingDirectory=/var/www/pams
ExecStart=/usr/bin/php /var/www/pams/artisan queue:work redis --sleep=3 --tries=3
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

**Start Service**:
```bash
sudo systemctl daemon-reload
sudo systemctl enable pams-worker
sudo systemctl start pams-worker
sudo systemctl status pams-worker
```

---

## Cron Jobs Configuration

```bash
# Edit crontab for pams user
sudo crontab -u pams -e
```

**Add Cron Jobs**:
```cron
# Laravel Scheduler (runs every minute)
* * * * * cd /var/www/pams && php artisan schedule:run >> /dev/null 2>&1

# Generate sitemap daily at 2 AM
0 2 * * * cd /var/www/pams && php artisan sitemap:generate >> /dev/null 2>&1

# Clear old logs weekly
0 0 * * 0 cd /var/www/pams && find storage/logs -name "*.log" -mtime +30 -delete
```

**Verify Cron Jobs**:
```bash
sudo crontab -u pams -l
```

---

## Post-Deployment Verification

### 1. Test Website Access

```bash
# Test HTTP (should redirect to HTTPS)
curl -I http://yourdomain.com

# Test HTTPS
curl -I https://yourdomain.com
```

### 2. Test Database Connection

```bash
cd /var/www/pams
php artisan tinker
```

```php
>>> DB::connection()->getPdo();
>>> \App\Models\User::count();
```

### 3. Test Queue Worker

```bash
# Check worker status
sudo supervisorctl status pams-worker:*

# Test queue
php artisan tinker
```

```php
>>> dispatch(function() { Log::info('Queue test'); });
>>> exit
```

Check logs:
```bash
tail -f storage/logs/worker.log
tail -f storage/logs/laravel.log
```

### 4. Test Cron Jobs

```bash
# Manually run scheduler
php artisan schedule:run

# Check logs
tail -f storage/logs/laravel.log
```

### 5. Test Critical Features

- [ ] Login as Super Admin
- [ ] Create test property
- [ ] Upload images
- [ ] View public property page
- [ ] Test affiliate tracking link
- [ ] Submit contact form
- [ ] Verify WhatsApp notification
- [ ] Check analytics dashboard
- [ ] Test search functionality

### 6. Performance Check

```bash
# Check page load time
curl -o /dev/null -s -w 'Total: %{time_total}s\n' https://yourdomain.com

# Check database queries
php artisan tinker
```

```php
>>> DB::enableQueryLog();
>>> \App\Models\Property::with('media')->published()->get();
>>> DB::getQueryLog();
```

---

## Maintenance & Updates

### Regular Maintenance Tasks

**Daily**:
```bash
# Check error logs
tail -100 /var/www/pams/storage/logs/laravel.log

# Check queue status
sudo supervisorctl status

# Monitor disk space
df -h
```

**Weekly**:
```bash
# Clear old logs
find /var/www/pams/storage/logs -name "*.log" -mtime +7 -delete

# Optimize database
php artisan optimize

# Check failed jobs
php artisan queue:failed
```

**Monthly**:
```bash
# Update dependencies
composer update
npm update

# Run tests
php artisan test

# Database backup
mysqldump -u pams_user -p pams > backup_$(date +%Y%m%d).sql
```

### Deploying Updates

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Pull latest code
git pull origin main

# 3. Update dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Clear and rebuild caches
php artisan optimize:clear
php artisan optimize

# 6. Restart queue workers
sudo supervisorctl restart pams-worker:*

# 7. Disable maintenance mode
php artisan up
```

### Zero-Downtime Deployment

For production systems, consider using:
- Laravel Envoyer
- Deployer
- Custom deployment scripts with symlinks

### Rollback Procedure

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Revert to previous version
git reset --hard HEAD~1

# 3. Restore database (if needed)
mysql -u pams_user -p pams < backup_previous.sql

# 4. Rebuild caches
php artisan optimize:clear
php artisan optimize

# 5. Restart workers
sudo supervisorctl restart pams-worker:*

# 6. Disable maintenance mode
php artisan up
```

---

## Monitoring & Logging

### Application Logs

```bash
# Real-time log monitoring
tail -f /var/www/pams/storage/logs/laravel.log

# Search for errors
grep "ERROR" /var/www/pams/storage/logs/laravel.log

# Check specific date
grep "2025-11-23" /var/www/pams/storage/logs/laravel.log
```

### Web Server Logs

```bash
# Nginx
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# Apache
tail -f /var/log/apache2/access.log
tail -f /var/log/apache2/error.log
```

### System Monitoring

```bash
# CPU and Memory
htop

# Disk usage
df -h
du -sh /var/www/pams/*

# MySQL processes
mysqladmin -u root -p processlist

# Redis info
redis-cli info
```

### Recommended Monitoring Tools

- **Uptime Monitoring**: UptimeRobot, Pingdom
- **Error Tracking**: Sentry, Bugsnag
- **Performance**: New Relic, DataDog
- **Server Monitoring**: Netdata, Prometheus

---

## Security Hardening

### Firewall Configuration

```bash
# Install UFW
sudo apt install -y ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable

# Check status
sudo ufw status
```

### Fail2Ban Setup

```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Configure
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local

# Enable and start
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### Additional Security Measures

- [ ] Disable root SSH login
- [ ] Use SSH keys instead of passwords
- [ ] Keep system updated
- [ ] Regular security audits
- [ ] Strong database passwords
- [ ] Limit database access to localhost
- [ ] Regular backups
- [ ] Monitor access logs

---

## Backup Strategy

### Database Backup

```bash
# Create backup script
sudo nano /usr/local/bin/backup-pams-db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pams"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

mysqldump -u pams_user -p'password' pams | gzip > $BACKUP_DIR/pams_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "pams_*.sql.gz" -mtime +30 -delete
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-pams-db.sh

# Add to crontab
sudo crontab -e
```

```cron
# Daily database backup at 3 AM
0 3 * * * /usr/local/bin/backup-pams-db.sh
```

### File Backup

```bash
# Backup storage directory
tar -czf /var/backups/pams/storage_$(date +%Y%m%d).tar.gz /var/www/pams/storage

# Backup to remote server (optional)
rsync -avz /var/backups/pams/ user@backup-server:/backups/pams/
```

---

## Troubleshooting Deployment Issues

### Issue: 500 Internal Server Error

**Check**:
```bash
# Application logs
tail -100 /var/www/pams/storage/logs/laravel.log

# Web server logs
tail -100 /var/log/nginx/error.log

# PHP-FPM logs
tail -100 /var/log/php8.3-fpm.log
```

**Common Causes**:
- File permissions incorrect
- .env file missing or misconfigured
- Database connection failed
- PHP extensions missing

### Issue: Queue Not Processing

**Check**:
```bash
# Worker status
sudo supervisorctl status pams-worker:*

# Worker logs
tail -f /var/www/pams/storage/logs/worker.log

# Failed jobs
php artisan queue:failed
```

**Solutions**:
```bash
# Restart workers
sudo supervisorctl restart pams-worker:*

# Retry failed jobs
php artisan queue:retry all
```

### Issue: Assets Not Loading

**Check**:
```bash
# Verify storage link
ls -la /var/www/pams/public/storage

# Rebuild assets
npm run build

# Check permissions
ls -la /var/www/pams/public
```

---

## Support & Resources

- **Technical Documentation**: `/docs/TECHNICAL_DOCUMENTATION.md`
- **User Guides**: `/docs/USER_GUIDE_*.md`
- **Troubleshooting**: `/docs/TROUBLESHOOTING_GUIDE.md`
- **Laravel Documentation**: https://laravel.com/docs
- **FilamentPHP Documentation**: https://filamentphp.com/docs

---

*Last Updated: November 2025*
*Version: 1.0*

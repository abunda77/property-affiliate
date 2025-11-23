# Environment Configuration Guide

## Overview

This document provides detailed information about all environment variables used in PAMS (Property Affiliate Management System).

---

## Application Settings

### APP_NAME
**Type**: String  
**Default**: `"PAMS"`  
**Description**: Application name displayed in UI and emails  
**Example**: `APP_NAME="Property Affiliate System"`

### APP_ENV
**Type**: String  
**Values**: `local`, `staging`, `production`  
**Default**: `production`  
**Description**: Application environment  
**Example**: `APP_ENV=production`

### APP_DEBUG
**Type**: Boolean  
**Values**: `true`, `false`  
**Default**: `false`  
**Description**: Enable debug mode (shows detailed errors)  
**Warning**: NEVER set to `true` in production  
**Example**: `APP_DEBUG=false`

### APP_URL
**Type**: URL  
**Description**: Base URL of the application  
**Example**: `APP_URL=https://yourdomain.com`

### APP_KEY
**Type**: String (Base64)  
**Description**: Encryption key (auto-generated)  
**Generate**: `php artisan key:generate`  
**Example**: `APP_KEY=base64:abc123...`

---

## Database Configuration

### DB_CONNECTION
**Type**: String  
**Values**: `mysql`, `pgsql`, `sqlite`  
**Default**: `mysql`  
**Description**: Database driver  
**Example**: `DB_CONNECTION=mysql`

### DB_HOST
**Type**: String/IP  
**Default**: `127.0.0.1`  
**Description**: Database server hostname  
**Example**: `DB_HOST=127.0.0.1`

### DB_PORT
**Type**: Integer  
**Default**: `3306` (MySQL), `5432` (PostgreSQL)  
**Description**: Database server port  
**Example**: `DB_PORT=3306`

### DB_DATABASE
**Type**: String  
**Description**: Database name  
**Example**: `DB_DATABASE=pams`

### DB_USERNAME
**Type**: String  
**Description**: Database username  
**Example**: `DB_USERNAME=pams_user`

### DB_PASSWORD
**Type**: String  
**Description**: Database password  
**Security**: Use strong password  
**Example**: `DB_PASSWORD=secure_password_here`

---

## GoWA API Configuration

### GOWA_USERNAME
**Type**: String  
**Required**: Yes (for WhatsApp notifications)  
**Description**: GoWA API username  
**Obtain**: From GoWA account  
**Example**: `GOWA_USERNAME=your_username`

### GOWA_PASSWORD
**Type**: String  
**Required**: Yes (for WhatsApp notifications)  
**Description**: GoWA API password  
**Obtain**: From GoWA account  
**Example**: `GOWA_PASSWORD=your_password`

### GOWA_API_URL
**Type**: URL  
**Default**: `https://api.gowa.id/v1`  
**Description**: GoWA API endpoint  
**Example**: `GOWA_API_URL=https://api.gowa.id/v1`

---

## Google Analytics

### GOOGLE_ANALYTICS_ID
**Type**: String  
**Required**: No (optional)  
**Description**: Google Analytics tracking ID  
**Format**: `G-XXXXXXXXXX` or `UA-XXXXXXXXX-X`  
**Obtain**: From Google Analytics account  
**Example**: `GOOGLE_ANALYTICS_ID=G-ABC123XYZ`

---

## Cache Configuration

### CACHE_DRIVER
**Type**: String  
**Values**: `file`, `redis`, `memcached`, `database`  
**Default**: `redis`  
**Recommended**: `redis` for production  
**Description**: Cache storage driver  
**Example**: `CACHE_DRIVER=redis`

---

## Session Configuration

### SESSION_DRIVER
**Type**: String  
**Values**: `file`, `cookie`, `database`, `redis`  
**Default**: `redis`  
**Recommended**: `redis` for production  
**Description**: Session storage driver  
**Example**: `SESSION_DRIVER=redis`

### SESSION_LIFETIME
**Type**: Integer (minutes)  
**Default**: `120`  
**Description**: Session lifetime in minutes  
**Example**: `SESSION_LIFETIME=120`

---

## Queue Configuration

### QUEUE_CONNECTION
**Type**: String  
**Values**: `sync`, `database`, `redis`, `sqs`  
**Default**: `redis`  
**Recommended**: `redis` for production  
**Description**: Queue driver  
**Example**: `QUEUE_CONNECTION=redis`

---

## Redis Configuration

### REDIS_HOST
**Type**: String/IP  
**Default**: `127.0.0.1`  
**Description**: Redis server hostname  
**Example**: `REDIS_HOST=127.0.0.1`

### REDIS_PASSWORD
**Type**: String  
**Default**: `null`  
**Description**: Redis password (if authentication enabled)  
**Example**: `REDIS_PASSWORD=redis_password`

### REDIS_PORT
**Type**: Integer  
**Default**: `6379`  
**Description**: Redis server port  
**Example**: `REDIS_PORT=6379`

---

## Mail Configuration

### MAIL_MAILER
**Type**: String  
**Values**: `smtp`, `sendmail`, `mailgun`, `ses`, `postmark`  
**Default**: `smtp`  
**Description**: Mail driver  
**Example**: `MAIL_MAILER=smtp`

### MAIL_HOST
**Type**: String  
**Description**: SMTP server hostname  
**Example**: `MAIL_HOST=smtp.mailtrap.io`

### MAIL_PORT
**Type**: Integer  
**Common**: `587` (TLS), `465` (SSL), `25` (plain)  
**Description**: SMTP server port  
**Example**: `MAIL_PORT=587`

### MAIL_USERNAME
**Type**: String  
**Description**: SMTP username  
**Example**: `MAIL_USERNAME=your_username`

### MAIL_PASSWORD
**Type**: String  
**Description**: SMTP password  
**Example**: `MAIL_PASSWORD=your_password`

### MAIL_ENCRYPTION
**Type**: String  
**Values**: `tls`, `ssl`, `null`  
**Default**: `tls`  
**Description**: SMTP encryption method  
**Example**: `MAIL_ENCRYPTION=tls`

### MAIL_FROM_ADDRESS
**Type**: Email  
**Description**: Default sender email address  
**Example**: `MAIL_FROM_ADDRESS=noreply@yourdomain.com`

### MAIL_FROM_NAME
**Type**: String  
**Description**: Default sender name  
**Example**: `MAIL_FROM_NAME="${APP_NAME}"`

---

## Scout Configuration

### SCOUT_DRIVER
**Type**: String  
**Values**: `database`, `meilisearch`, `algolia`  
**Default**: `database`  
**Description**: Search driver  
**Example**: `SCOUT_DRIVER=database`

### SCOUT_PREFIX
**Type**: String  
**Optional**: Yes  
**Description**: Index prefix for Scout  
**Example**: `SCOUT_PREFIX=pams_`

---

## Filesystem Configuration

### FILESYSTEM_DISK
**Type**: String  
**Values**: `local`, `public`, `s3`  
**Default**: `public`  
**Description**: Default filesystem disk  
**Example**: `FILESYSTEM_DISK=public`

### AWS_ACCESS_KEY_ID
**Type**: String  
**Required**: If using S3  
**Description**: AWS access key  
**Example**: `AWS_ACCESS_KEY_ID=your_key`

### AWS_SECRET_ACCESS_KEY
**Type**: String  
**Required**: If using S3  
**Description**: AWS secret key  
**Example**: `AWS_SECRET_ACCESS_KEY=your_secret`

### AWS_DEFAULT_REGION
**Type**: String  
**Required**: If using S3  
**Description**: AWS region  
**Example**: `AWS_DEFAULT_REGION=us-east-1`

### AWS_BUCKET
**Type**: String  
**Required**: If using S3  
**Description**: S3 bucket name  
**Example**: `AWS_BUCKET=your-bucket-name`

---

## Logging Configuration

### LOG_CHANNEL
**Type**: String  
**Values**: `stack`, `single`, `daily`, `slack`, `syslog`  
**Default**: `stack`  
**Description**: Logging channel  
**Example**: `LOG_CHANNEL=daily`

### LOG_LEVEL
**Type**: String  
**Values**: `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, `emergency`  
**Default**: `debug`  
**Recommended Production**: `error`  
**Description**: Minimum log level  
**Example**: `LOG_LEVEL=error`

---

## Broadcasting Configuration

### BROADCAST_DRIVER
**Type**: String  
**Values**: `log`, `pusher`, `redis`, `null`  
**Default**: `log`  
**Description**: Broadcasting driver  
**Example**: `BROADCAST_DRIVER=log`

---

## Complete .env Example

### Development Environment

```bash
# Application
APP_NAME="PAMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pams
DB_USERNAME=root
DB_PASSWORD=

# GoWA API (use test credentials)
GOWA_USERNAME=test_username
GOWA_PASSWORD=test_password
GOWA_API_URL=https://api.gowa.id/v1

# Google Analytics (optional in dev)
GOOGLE_ANALYTICS_ID=

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Redis (not required for local)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (use Mailtrap for testing)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pams.test
MAIL_FROM_NAME="${APP_NAME}"

# Scout
SCOUT_DRIVER=database

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Production Environment

```bash
# Application
APP_NAME="PAMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pams
DB_USERNAME=pams_user
DB_PASSWORD=strong_secure_password_here

# GoWA API
GOWA_USERNAME=your_production_username
GOWA_PASSWORD=your_production_password
GOWA_API_URL=https://api.gowa.id/v1

# Google Analytics
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=redis_secure_password
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailserver.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Scout
SCOUT_DRIVER=database

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error

# Filesystem (if using S3)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

---

## Security Best Practices

### Password Security

1. **Use Strong Passwords**:
   - Minimum 16 characters
   - Mix of uppercase, lowercase, numbers, symbols
   - Use password generator

2. **Never Commit .env**:
   - .env is in .gitignore
   - Never commit to version control
   - Use .env.example as template

3. **Rotate Credentials**:
   - Change passwords regularly
   - Rotate API keys quarterly
   - Update after team member departure

### Environment-Specific Settings

**Development**:
- `APP_DEBUG=true` (for debugging)
- `LOG_LEVEL=debug` (verbose logging)
- `QUEUE_CONNECTION=sync` (immediate execution)
- Use test API credentials

**Staging**:
- `APP_DEBUG=false`
- `LOG_LEVEL=info`
- Mirror production settings
- Use separate database

**Production**:
- `APP_DEBUG=false` (CRITICAL)
- `LOG_LEVEL=error`
- Use Redis for cache/session/queue
- Strong passwords everywhere
- Enable all security features

---

## Validation Checklist

Before deploying, verify:

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_URL` matches actual domain
- [ ] Database credentials correct
- [ ] GoWA API credentials valid
- [ ] Redis configured (if using)
- [ ] Mail settings tested
- [ ] Google Analytics ID correct
- [ ] All passwords are strong
- [ ] No sensitive data in version control
- [ ] .env file permissions: 600
- [ ] Backup of .env file stored securely

---

## Troubleshooting

### Issue: Application Key Missing

**Error**: "No application encryption key has been specified"

**Solution**:
```bash
php artisan key:generate
```

### Issue: Database Connection Failed

**Check**:
1. Database server is running
2. Credentials are correct
3. Database exists
4. User has proper permissions

**Test**:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue: Redis Connection Failed

**Check**:
1. Redis server is running: `redis-cli ping`
2. Host and port correct
3. Password correct (if set)

**Test**:
```bash
redis-cli
> AUTH your_password
> PING
```

### Issue: Mail Not Sending

**Check**:
1. SMTP credentials correct
2. Port and encryption match
3. Firewall allows outbound SMTP

**Test**:
```bash
php artisan tinker
>>> Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });
```

---

## Additional Resources

- **Laravel Configuration**: https://laravel.com/docs/configuration
- **Environment Variables**: https://laravel.com/docs/configuration#environment-configuration
- **Security Best Practices**: https://laravel.com/docs/deployment#optimization

---

*Last Updated: November 2025*
*Version: 1.0*

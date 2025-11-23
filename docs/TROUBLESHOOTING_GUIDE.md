# PAMS Troubleshooting Guide

## Table of Contents
1. [Authentication & Access Issues](#authentication--access-issues)
2. [Property Management Issues](#property-management-issues)
3. [Affiliate Tracking Issues](#affiliate-tracking-issues)
4. [Lead Management Issues](#lead-management-issues)
5. [WhatsApp Notification Issues](#whatsapp-notification-issues)
6. [Dashboard & Analytics Issues](#dashboard--analytics-issues)
7. [Image & Media Issues](#image--media-issues)
8. [Search & Performance Issues](#search--performance-issues)
9. [Email & Notification Issues](#email--notification-issues)
10. [System Errors](#system-errors)

---

## Authentication & Access Issues

### Cannot Login - Invalid Credentials

**Symptoms**: Login fails with "Invalid credentials" error

**Possible Causes**:
- Incorrect email or password
- Account not yet approved
- Account blocked by admin
- Caps Lock enabled

**Solutions**:
1. Verify email address is correct (check for typos)
2. Ensure password is correct (case-sensitive)
3. Check Caps Lock is off
4. Try password reset: Click "Forgot Password"
5. Contact admin to verify account status
6. Check email for approval notification

**Prevention**:
- Use password manager
- Save correct email address
- Wait for approval email before attempting login

---

### Account Pending - Cannot Access Dashboard

**Symptoms**: Login successful but redirected or access denied

**Possible Causes**:
- Account still in "Pending" status
- Awaiting admin approval
- Not yet assigned affiliate role

**Solutions**:
1. Check email for approval notification
2. Contact admin to request approval
3. Verify registration was completed
4. Wait for admin to process approval (usually 24-48 hours)

**For Admins**:
1. Go to Users list
2. Filter by Status: Pending
3. Review and approve user
4. System automatically assigns role and sends notification

---

### 403 Forbidden Error

**Symptoms**: "You don't have permission to access this resource"

**Possible Causes**:
- Insufficient permissions for requested action
- Trying to access admin-only features as affiliate
- Trying to access another user's data
- Role not properly assigned

**Solutions**:
1. Verify you're logged in with correct account
2. Check your role (Super Admin vs Affiliate)
3. Affiliates can only access own data
4. Contact admin if you believe you should have access

**For Admins**:
1. Verify user has correct role assigned
2. Check Filament Shield permissions
3. Run: `php artisan permission:cache-reset`
4. Reassign role if needed

---

### Session Expired

**Symptoms**: Logged out unexpectedly, "Session expired" message

**Possible Causes**:
- Inactive for extended period
- Browser cookies cleared
- Session configuration issue

**Solutions**:
1. Simply log in again
2. Enable "Remember Me" checkbox
3. Check browser cookie settings (must allow cookies)
4. Clear browser cache and try again

**For Admins**:
1. Check session configuration in `.env`
2. Verify `SESSION_DRIVER` is set correctly
3. Ensure session storage is writable
4. Check session lifetime setting

---

## Property Management Issues

### Property Not Appearing in Public Catalog

**Symptoms**: Property created but not visible on website

**Possible Causes**:
- Property status is "Draft" or "Sold"
- Property not saved properly
- Cache not cleared
- Database issue

**Solutions**:
1. **Check Status**: Edit property, verify status is "Published"
2. **Clear Cache**: 
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```
3. **Verify Save**: Ensure "Save" was clicked after creation
4. **Check Database**: Verify property exists in database
5. **Test Direct URL**: Try accessing `/p/{slug}` directly

**For Admins**:
1. Check property in database: `properties` table
2. Verify `status` column = 'published'
3. Check for any validation errors in logs
4. Ensure property has required fields filled

---

### Slug Already Exists Error

**Symptoms**: Cannot save property, "Slug already exists" error

**Possible Causes**:
- Another property has same title
- Slug manually set to duplicate value
- Database constraint violation

**Solutions**:
1. **Change Title**: Modify property title slightly
2. **Manual Slug**: Set custom slug in slug field
3. **Check Existing**: Search for properties with similar names
4. **Add Suffix**: System should auto-add suffix, if not working contact admin

**For Admins**:
1. Check `properties` table for duplicate slugs
2. Verify slug generation logic in `PropertyObserver`
3. Ensure unique constraint on `slug` column
4. Manually update slug if needed

---

### Images Not Uploading

**Symptoms**: Image upload fails or shows error

**Possible Causes**:
- File too large (exceeds limit)
- Invalid file type
- Storage permission issue
- Disk space full

**Solutions**:
1. **Check File Size**: Reduce image size (max 10MB recommended)
2. **Check Format**: Use JPG, PNG, or WebP only
3. **Try Different Image**: Test with known-good image
4. **Clear Browser Cache**: Sometimes helps with upload issues

**For Admins**:
1. Check `upload_max_filesize` in `php.ini`
2. Check `post_max_size` in `php.ini`
3. Verify storage permissions: `chmod -R 775 storage`
4. Check disk space: `df -h`
5. Review error logs for specific error
6. Verify storage link exists: `php artisan storage:link`

---

## Affiliate Tracking Issues

### Tracking Links Not Recording Visits

**Symptoms**: Clicks on affiliate links not showing in dashboard

**Possible Causes**:
- Cookies disabled in browser
- Affiliate code incorrect
- Middleware not running
- Database connection issue
- Affiliate account not active

**Solutions**:
1. **Enable Cookies**: Ensure browser allows cookies
2. **Test Link**: Copy link exactly as generated
3. **Don't Modify**: Don't change the `?ref=` parameter
4. **Test Incognito**: Try in private/incognito mode
5. **Wait**: Data may take a few minutes to appear
6. **Check Status**: Verify account status is "Active"

**For Admins**:
1. Verify middleware is registered in `bootstrap/app.php`
2. Check `visits` table for records
3. Test with known affiliate code
4. Review middleware logic in `AffiliateTrackingMiddleware`
5. Check error logs for exceptions
6. Verify database connection

**Debug Steps**:
```bash
# Check if visits are being recorded
php artisan tinker
>>> \App\Models\Visit::latest()->take(5)->get()

# Check specific affiliate
>>> \App\Models\User::where('affiliate_code', 'ABC123')->first()
```

---

### Cookie Not Persisting

**Symptoms**: Tracking works initially but not on return visits

**Possible Causes**:
- Browser clearing cookies
- Cookie expiration issue
- Domain mismatch
- HTTPS/HTTP mismatch

**Solutions**:
1. **Check Browser Settings**: Ensure cookies aren't auto-deleted
2. **Test 30-Day Window**: Cookie should last 30 days
3. **Same Domain**: Ensure all visits are to same domain
4. **HTTPS**: Use HTTPS consistently

**For Admins**:
1. Verify cookie configuration in middleware
2. Check cookie expiration: 43200 minutes = 30 days
3. Ensure `SESSION_DOMAIN` in `.env` is correct
4. Test cookie persistence manually
5. Check for cookie conflicts

---

### Wrong Affiliate Attribution

**Symptoms**: Leads attributed to wrong affiliate

**Possible Causes**:
- Multiple affiliate links clicked
- Cookie overwritten
- Shared device/browser
- Logic error in attribution

**Solutions**:
1. **Last Click Wins**: System attributes to most recent affiliate link
2. **Clear Cookies**: Test with fresh browser session
3. **Use Private Browsing**: For testing different affiliates
4. **Check Timeline**: Review visit history for lead

**For Admins**:
1. Review attribution logic in middleware
2. Check `visits` table for visitor IP
3. Verify lead creation logic in `ContactForm`
4. Consider implementing first-click attribution if needed
5. Add logging to track attribution decisions

---

## Lead Management Issues

### Leads Not Appearing in Dashboard

**Symptoms**: Contact form submitted but lead not visible

**Possible Causes**:
- Form validation failed
- Database error
- Permission issue
- Wrong affiliate viewing

**Solutions**:
1. **Check Filters**: Remove all filters in leads list
2. **Verify Submission**: Check if success message appeared
3. **Check Email**: Look for notification email
4. **Right Account**: Ensure logged in as correct affiliate

**For Admins**:
1. Check `leads` table directly
2. Review form validation in `ContactForm`
3. Check error logs for exceptions
4. Verify lead creation logic
5. Test form submission manually

**Debug Steps**:
```bash
# Check recent leads
php artisan tinker
>>> \App\Models\Lead::latest()->take(5)->get()

# Check specific affiliate's leads
>>> \App\Models\User::find(1)->leads
```

---

### Cannot Update Lead Status

**Symptoms**: Status dropdown doesn't save changes

**Possible Causes**:
- Permission issue
- Validation error
- JavaScript error
- Database constraint

**Solutions**:
1. **Refresh Page**: Try reloading
2. **Check Permissions**: Verify you can edit leads
3. **Try Different Status**: Test with another status value
4. **Check Browser Console**: Look for JavaScript errors

**For Admins**:
1. Verify lead policy allows status updates
2. Check enum values in `LeadStatus`
3. Review validation rules
4. Check for database constraints
5. Test status update manually

---

### WhatsApp Button Not Working

**Symptoms**: "Click to WA" button doesn't open WhatsApp

**Possible Causes**:
- WhatsApp not installed
- Phone number format incorrect
- Browser blocking popup
- JavaScript error

**Solutions**:
1. **Install WhatsApp**: Ensure WhatsApp or WhatsApp Web available
2. **Allow Popups**: Enable popups for the site
3. **Check Number**: Verify phone number is valid
4. **Try Manual**: Copy number and open WhatsApp manually

**For Admins**:
1. Verify phone number format in database
2. Check WhatsApp link generation logic
3. Test with known-good phone number
4. Review JavaScript console for errors

---

## WhatsApp Notification Issues

### Notifications Not Sending

**Symptoms**: Affiliates not receiving WhatsApp messages for new leads

**Possible Causes**:
- GoWA API credentials incorrect
- API service down
- Insufficient credits
- Phone number format wrong
- Event not firing

**Solutions**:
1. **Check Credentials**: Verify GoWA username/password in Settings
2. **Test Connection**: Use "Test Connection" button in Settings
3. **Check Credits**: Log into GoWA account, verify balance
4. **Phone Format**: Ensure format is +62XXXXXXXXXX
5. **Check Logs**: Review error logs for API failures

**For Admins**:
1. Verify GoWA configuration in Settings
2. Test API manually:
   ```bash
   php artisan tinker
   >>> app(\App\Services\GoWAService::class)->sendMessage('+6281234567890', 'Test')
   ```
3. Check event listener is registered
4. Review `SendLeadNotification` listener
5. Check error logs: `storage/logs/laravel.log`
6. Verify event is being dispatched

**Debug Steps**:
```bash
# Test GoWA service
php artisan tinker
>>> $service = app(\App\Services\GoWAService::class)
>>> $service->sendMessage('+6281234567890', 'Test message')

# Check if events are firing
>>> event(new \App\Events\LeadCreated(\App\Models\Lead::first()))
```

---

### Delayed Notifications

**Symptoms**: Notifications arrive late or not in real-time

**Possible Causes**:
- Queue not running
- Queue worker stopped
- High queue backlog
- API rate limiting

**Solutions**:
1. **Check Queue**: Ensure queue worker is running
2. **Restart Queue**: 
   ```bash
   php artisan queue:restart
   php artisan queue:listen
   ```
3. **Check Backlog**: Review failed jobs
4. **Monitor**: Watch queue in real-time

**For Admins**:
1. Verify queue configuration in `.env`
2. Ensure queue worker is running (supervisor/systemd)
3. Check failed jobs: `php artisan queue:failed`
4. Retry failed jobs: `php artisan queue:retry all`
5. Monitor queue: `php artisan queue:work --verbose`

---

### Wrong Message Content

**Symptoms**: Notification message has incorrect information

**Possible Causes**:
- Template error
- Variable not populated
- Data mismatch

**Solutions**:
1. **Report Issue**: Contact admin with screenshot
2. **Check Lead Data**: Verify lead information is correct

**For Admins**:
1. Review message template in `SendLeadNotification`
2. Verify variable substitution
3. Check lead relationships (property, affiliate)
4. Test with sample data
5. Update template if needed

---

## Dashboard & Analytics Issues

### Dashboard Widgets Not Loading

**Symptoms**: Blank widgets or loading spinner forever

**Possible Causes**:
- Database query error
- Permission issue
- Cache problem
- JavaScript error

**Solutions**:
1. **Refresh Page**: Try hard refresh (Ctrl+F5)
2. **Clear Cache**: Clear browser cache
3. **Try Different Browser**: Test in another browser
4. **Check Internet**: Verify connection is stable

**For Admins**:
1. Clear application cache:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   ```
2. Check error logs
3. Test widget queries manually
4. Verify database connection
5. Check widget permissions

---

### Incorrect Analytics Data

**Symptoms**: Numbers don't match expectations or seem wrong

**Possible Causes**:
- Date range filter applied
- Viewing wrong affiliate's data
- Data not synced yet
- Calculation error

**Solutions**:
1. **Check Date Range**: Verify date filter settings
2. **Check Account**: Ensure viewing correct affiliate
3. **Wait for Sync**: Data may take a few minutes
4. **Compare Sources**: Cross-reference with other data

**For Admins**:
1. Verify analytics calculations in `AnalyticsService`
2. Test queries manually
3. Check for data integrity issues
4. Review aggregation logic
5. Compare with raw database counts

**Debug Steps**:
```bash
# Manual verification
php artisan tinker
>>> $affiliate = \App\Models\User::find(1)
>>> $affiliate->visits()->count()
>>> $affiliate->leads()->count()
>>> # Compare with dashboard numbers
```

---

### Google Analytics Not Showing

**Symptoms**: Google Analytics widget blank or not loading

**Possible Causes**:
- Analytics ID not configured
- Invalid Analytics ID
- Tracking code not installed
- Insufficient permissions

**Solutions**:
1. **Check Settings**: Verify Google Analytics ID in Settings
2. **Verify Format**: Should be like G-XXXXXXXXXX
3. **Check Permissions**: Ensure you have access to GA account
4. **Wait for Data**: New sites need 24-48 hours for data

**For Admins**:
1. Verify `GOOGLE_ANALYTICS_ID` in `.env`
2. Check tracking code in layout template
3. Test with Google Tag Assistant
4. Verify GA property is active
5. Check widget embed code

---

## Image & Media Issues

### Images Not Displaying

**Symptoms**: Broken image icons or missing images

**Possible Causes**:
- Storage link missing
- File permissions wrong
- File deleted
- Wrong path

**Solutions**:
1. **Refresh Page**: Try hard refresh
2. **Check URL**: Right-click image, check URL
3. **Clear Cache**: Clear browser cache

**For Admins**:
1. Create storage link:
   ```bash
   php artisan storage:link
   ```
2. Check file permissions:
   ```bash
   chmod -R 775 storage
   chown -R www-data:www-data storage
   ```
3. Verify files exist in `storage/app/public`
4. Check media library configuration
5. Review error logs

---

### Image Upload Fails

**Symptoms**: Upload button doesn't work or shows error

**Possible Causes**:
- File too large
- Wrong file type
- Storage full
- Permission issue

**Solutions**:
1. **Reduce Size**: Compress image before upload
2. **Check Format**: Use JPG, PNG, or WebP
3. **Try Different File**: Test with another image
4. **Check Limit**: Max file size shown in upload area

**For Admins**:
1. Check PHP upload limits:
   ```bash
   php -i | grep upload_max_filesize
   php -i | grep post_max_size
   ```
2. Increase if needed in `php.ini`
3. Check disk space: `df -h`
4. Verify storage permissions
5. Check error logs for specific error

---

### Images Not Optimizing

**Symptoms**: Images not converted to WebP or thumbnails missing

**Possible Causes**:
- Image processing library missing
- Queue not running
- Configuration error

**Solutions**:
1. **Wait**: Processing may take a few minutes
2. **Check Queue**: Ensure queue worker running

**For Admins**:
1. Verify GD or Imagick installed:
   ```bash
   php -m | grep -i gd
   php -m | grep -i imagick
   ```
2. Check media library config
3. Test conversion manually
4. Review queue jobs
5. Check error logs

---

## Search & Performance Issues

### Search Not Working

**Symptoms**: Search returns no results or errors

**Possible Causes**:
- Scout not configured
- Index not built
- Database driver issue
- Query syntax error

**Solutions**:
1. **Try Different Terms**: Test with simple keywords
2. **Check Spelling**: Verify search terms are correct
3. **Clear Filters**: Remove any active filters
4. **Refresh Page**: Try reloading

**For Admins**:
1. Verify Scout configuration:
   ```bash
   php artisan config:cache
   ```
2. Check `SCOUT_DRIVER` in `.env`
3. Test search manually:
   ```bash
   php artisan tinker
   >>> \App\Models\Property::search('villa')->get()
   ```
4. Rebuild index if using Meilisearch/Algolia
5. Check fulltext index on database

---

### Slow Page Load

**Symptoms**: Pages take long time to load

**Possible Causes**:
- N+1 query problem
- Large images
- Cache not working
- Database slow

**Solutions**:
1. **Clear Cache**: Clear browser cache
2. **Check Internet**: Verify connection speed
3. **Try Different Time**: May be server load issue
4. **Report Issue**: Contact admin with specific page

**For Admins**:
1. Enable query logging to find N+1 issues
2. Check database indexes
3. Verify cache is working:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```
4. Optimize images
5. Enable Redis for caching
6. Review performance guide: `docs/PERFORMANCE_OPTIMIZATION.md`

---

## Email & Notification Issues

### Not Receiving Emails

**Symptoms**: No email notifications (approval, password reset, etc.)

**Possible Causes**:
- Email in spam folder
- Mail server not configured
- Wrong email address
- Queue not running

**Solutions**:
1. **Check Spam**: Look in spam/junk folder
2. **Check Email**: Verify email address is correct
3. **Wait**: Emails may be delayed
4. **Whitelist**: Add sender to contacts

**For Admins**:
1. Verify mail configuration in `.env`
2. Test email sending:
   ```bash
   php artisan tinker
   >>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); })
   ```
3. Check mail logs
4. Verify queue is running
5. Test with different email provider

---

### Email Content Wrong

**Symptoms**: Email has incorrect information or formatting

**Possible Causes**:
- Template error
- Variable not populated
- Markdown rendering issue

**Solutions**:
1. **Report Issue**: Contact admin with screenshot

**For Admins**:
1. Review email templates in `resources/views/emails`
2. Check notification classes
3. Test email rendering
4. Verify variable substitution
5. Update templates as needed

---

## System Errors

### 500 Internal Server Error

**Symptoms**: White page with "500 Internal Server Error"

**Possible Causes**:
- PHP error
- Database connection failed
- Configuration error
- File permission issue

**Solutions**:
1. **Refresh Page**: Try reloading
2. **Try Later**: May be temporary issue
3. **Report Issue**: Contact admin immediately

**For Admins**:
1. Check error logs: `storage/logs/laravel.log`
2. Enable debug mode temporarily (`.env`):
   ```
   APP_DEBUG=true
   ```
3. Check database connection
4. Verify file permissions
5. Check PHP error log
6. Review recent code changes

---

### 404 Not Found

**Symptoms**: "Page not found" error

**Possible Causes**:
- Wrong URL
- Route not defined
- Resource deleted
- Slug changed

**Solutions**:
1. **Check URL**: Verify URL is correct
2. **Try Home**: Go to homepage and navigate from there
3. **Clear Cache**: Clear browser cache
4. **Search**: Use search to find resource

**For Admins**:
1. Verify route exists in `routes/web.php`
2. Check if resource was deleted
3. Review route caching:
   ```bash
   php artisan route:clear
   php artisan route:cache
   ```
4. Check for slug changes

---

### Database Connection Error

**Symptoms**: "Could not connect to database" error

**Possible Causes**:
- Database server down
- Wrong credentials
- Network issue
- Max connections reached

**Solutions**:
1. **Wait**: May be temporary issue
2. **Report Issue**: Contact admin immediately

**For Admins**:
1. Check database server is running
2. Verify credentials in `.env`
3. Test connection:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo()
   ```
4. Check database server logs
5. Verify network connectivity
6. Check max connections limit

---

### Queue Jobs Failing

**Symptoms**: Jobs in failed_jobs table, notifications not sending

**Possible Causes**:
- Code error
- Database issue
- External API failure
- Timeout

**Solutions**:
**For Admins**:
1. View failed jobs:
   ```bash
   php artisan queue:failed
   ```
2. Check error message
3. Fix underlying issue
4. Retry failed jobs:
   ```bash
   php artisan queue:retry all
   ```
5. Monitor queue:
   ```bash
   php artisan queue:work --verbose
   ```

---

## General Troubleshooting Steps

### Step 1: Identify the Issue
- What exactly is not working?
- When did it start?
- Does it happen consistently?
- What error messages appear?

### Step 2: Gather Information
- Take screenshots
- Note exact error messages
- Record steps to reproduce
- Check browser console (F12)
- Note your role and permissions

### Step 3: Try Basic Fixes
- Refresh the page
- Clear browser cache
- Try different browser
- Log out and log back in
- Check internet connection

### Step 4: Check Logs (Admins)
```bash
# Application logs
tail -f storage/logs/laravel.log

# Web server logs
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log

# PHP logs
tail -f /var/log/php-fpm/error.log
```

### Step 5: Contact Support
If issue persists, contact support with:
- Description of issue
- Steps to reproduce
- Screenshots
- Error messages
- Your role (Super Admin/Affiliate)
- Browser and OS information

---

## Preventive Maintenance

### For Admins

**Daily**:
- Monitor error logs
- Check queue status
- Verify backups completed

**Weekly**:
- Review failed jobs
- Check disk space
- Monitor performance metrics
- Review user feedback

**Monthly**:
- Update dependencies
- Review security patches
- Optimize database
- Clean old logs
- Test critical features

**Quarterly**:
- Full system audit
- Performance optimization
- Security review
- Backup restoration test

---

## Emergency Contacts

**Technical Support**: [To be provided]
**System Administrator**: [To be provided]
**Hosting Provider**: [To be provided]
**GoWA Support**: [To be provided]

---

## Useful Commands (For Admins)

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Check system status
php artisan about

# Run health checks
php artisan health:check

# View logs
tail -f storage/logs/laravel.log

# Database backup
php artisan backup:run

# Test email
php artisan tinker
>>> Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); })

# Test WhatsApp
php artisan tinker
>>> app(\App\Services\GoWAService::class)->sendMessage('+6281234567890', 'Test')

# Check queue
php artisan queue:work --verbose

# Restart queue
php artisan queue:restart
```

---

*Last Updated: November 2025*
*Version: 1.0*

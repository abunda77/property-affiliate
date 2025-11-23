# Super Admin User Guide

## Table of Contents
1. [Getting Started](#getting-started)
2. [Property Management](#property-management)
3. [User Management](#user-management)
4. [Lead Management](#lead-management)
5. [System Settings](#system-settings)
6. [Analytics Dashboard](#analytics-dashboard)
7. [Common Workflows](#common-workflows)
8. [Troubleshooting](#troubleshooting)

---

## Getting Started

### Accessing the Admin Panel

1. Navigate to your PAMS domain: `https://yourdomain.com/admin`
2. Enter your Super Admin credentials
3. You'll be redirected to the main dashboard

### Dashboard Overview

The Super Admin dashboard displays:
- **Global Statistics**: Total traffic, leads, and active affiliates
- **Performance Charts**: Traffic and conversion trends
- **Top Affiliates**: Best performing affiliates by lead count
- **Recent Activity**: Latest leads and property views
- **Google Analytics**: Embedded analytics charts

---

## Property Management

### Creating a New Property

1. Click **Properties** in the sidebar
2. Click **New Property** button
3. Fill in the required fields:
   - **Title**: Property name (e.g., "Luxury Villa in Bali")
   - **Price**: Property price in IDR
   - **Location**: Full address or area
   - **Description**: Detailed property description (supports rich text)
   - **Status**: Select Draft, Published, or Sold
4. Add **Features** (click "Add item" for each feature):
   - Swimming Pool
   - 3 Bedrooms
   - Garden
   - etc.
5. Add **Specifications** (key-value pairs):
   - Land Size: 500 m²
   - Building Size: 300 m²
   - Certificate: SHM
   - etc.
6. Upload **Images**:
   - Click the upload area or drag images
   - Multiple images supported
   - First image becomes the thumbnail
7. Click **Create** to save

**Note**: The system automatically generates a SEO-friendly slug from the title.

### Editing Properties

1. Go to **Properties** list
2. Click the **Edit** icon on any property
3. Modify fields as needed
4. Click **Save changes**

### Managing Property Images

- **Add Images**: Click "Add files" in the Images section
- **Reorder Images**: Drag and drop images to reorder
- **Delete Images**: Click the X icon on any image
- **Image Optimization**: System automatically creates optimized versions (WebP, thumbnails)

### Property Status Management

- **Draft**: Property is hidden from public catalog
- **Published**: Property appears in public catalog and affiliate links
- **Sold**: Property shows "SOLD" badge, excluded from affiliate links

### Deleting Properties

1. Select properties using checkboxes
2. Click **Bulk Actions** → **Delete**
3. Confirm deletion
4. All associated media files are automatically removed

---

## User Management

### Viewing User List

1. Click **Users** in the sidebar
2. View all registered users with their:
   - Name and email
   - Status (Pending, Active, Blocked)
   - Registration date
   - Assigned roles

### Approving New Affiliates

When users register as affiliates, they start in "Pending" status:

1. Go to **Users** list
2. Filter by **Status: Pending**
3. Review the user details
4. Select users to approve
5. Click **Bulk Actions** → **Approve Affiliates**
6. System automatically:
   - Generates unique affiliate code
   - Assigns Affiliate role
   - Sends welcome email with affiliate code
   - Changes status to Active

### Blocking/Unblocking Users

To block a user:
1. Find the user in the list
2. Click **Edit**
3. Change **Status** to **Blocked**
4. Click **Save**

Blocked users cannot log in to the system.

To unblock:
1. Change **Status** back to **Active**
2. Click **Save**

### Editing User Information

1. Click **Edit** on any user
2. Modify:
   - Name
   - Email
   - WhatsApp number
   - Profile photo
   - Status
3. Click **Save changes**

### Viewing User Activity

1. Click on a user to view details
2. See associated:
   - Total visits generated
   - Total leads received
   - Conversion rate
   - Recent activity

---

## Lead Management

### Viewing All Leads

1. Click **Leads** in the sidebar
2. View complete lead list with:
   - Visitor name and WhatsApp
   - Property name
   - Assigned affiliate
   - Status
   - Submission date

### Filtering Leads

Use filters to find specific leads:
- **Status**: New, Follow Up, Survey, Closed, Lost
- **Affiliate**: Filter by specific affiliate
- **Property**: Filter by property
- **Date Range**: Custom date filters

### Exporting Leads

1. Apply desired filters
2. Click **Export** button
3. Choose format (CSV, Excel)
4. Download file

### Lead Status Meanings

- **New**: Just submitted, awaiting first contact
- **Follow Up**: Initial contact made, needs follow-up
- **Survey**: Property viewing scheduled or completed
- **Closed**: Deal successfully closed
- **Lost**: Lead did not convert

---

## System Settings

### Accessing Settings

1. Click **Settings** in the sidebar
2. Configure system-wide options

### GoWA API Configuration

Configure WhatsApp notifications:

1. Go to **Settings** → **GoWA Integration**
2. Enter:
   - **Username**: Your GoWA username
   - **Password**: Your GoWA password
   - **API URL**: `https://api.gowa.id/v1` (or your custom endpoint)
3. Click **Test Connection** to verify
4. Click **Save**

**Troubleshooting**: If notifications fail, check:
- Credentials are correct
- API URL is accessible
- Account has sufficient credits

### Logo Management

1. Go to **Settings** → **Branding**
2. Click **Upload Logo**
3. Select image file (PNG, JPG, SVG)
4. Logo appears in:
   - Admin panel header
   - Public website header
   - Email notifications

### SEO Settings

Configure global SEO metadata:

1. Go to **Settings** → **SEO**
2. Set:
   - **Site Title**: Default title for pages
   - **Meta Description**: Default description
   - **Meta Keywords**: Comma-separated keywords
3. Click **Save**

**Note**: Individual property pages override these with property-specific SEO.

### Google Analytics Integration

1. Go to **Settings** → **Analytics**
2. Enter your **Google Analytics ID** (e.g., G-XXXXXXXXXX)
3. Click **Save**
4. Analytics widget appears on dashboard

---

## Analytics Dashboard

### Understanding Metrics

**Global Statistics Card**:
- **Total Traffic**: All visits across all affiliates
- **Total Leads**: All leads generated
- **Active Affiliates**: Users with active status
- **Conversion Rate**: (Total Leads / Total Traffic) × 100

**Performance Chart**:
- Line graph showing traffic and leads over time
- Toggle between daily, weekly, monthly views
- Hover for specific data points

**Top Affiliates Table**:
- Ranked by lead count
- Shows visits, leads, and conversion rate
- Click affiliate name to view details

**Recent Activity Feed**:
- Latest leads submitted
- Recent property views
- Real-time updates

### Filtering Analytics

1. Use **Date Range** picker at top
2. Select preset ranges:
   - Today
   - This Week
   - This Month
   - Last 30 Days
   - Custom Range
3. All widgets update automatically

### Exporting Reports

1. Set desired date range
2. Click **Export Report**
3. Choose format (PDF, Excel)
4. Report includes:
   - Summary statistics
   - Affiliate performance breakdown
   - Lead status distribution
   - Top properties

---

## Common Workflows

### Workflow 1: Onboarding a New Property

1. **Create Property**:
   - Add property details
   - Upload high-quality images
   - Set status to "Published"

2. **Verify SEO**:
   - Check auto-generated slug
   - Review meta description
   - Ensure images have alt text

3. **Test Public View**:
   - Visit property page: `/p/{slug}`
   - Verify all information displays correctly
   - Test contact form

4. **Notify Affiliates**:
   - Affiliates automatically see new property
   - They can generate tracking links immediately

### Workflow 2: Approving New Affiliates

1. **Review Registration**:
   - Check user details in Users list
   - Verify WhatsApp number format
   - Review profile information

2. **Approve User**:
   - Select user(s)
   - Use bulk approve action
   - System sends welcome email

3. **Verify Setup**:
   - Check affiliate code generated
   - Confirm role assigned
   - Test affiliate login

4. **Provide Training**:
   - Share Affiliate User Guide
   - Explain link generation
   - Show dashboard features

### Workflow 3: Managing Leads

1. **Monitor New Leads**:
   - Check dashboard for new leads
   - Review lead details
   - Verify affiliate attribution

2. **Follow Up**:
   - Leads automatically notify affiliates via WhatsApp
   - Monitor lead status updates
   - Track conversion progress

3. **Close Deals**:
   - Affiliate updates status to "Closed"
   - Review closed deals in reports
   - Calculate commissions if applicable

### Workflow 4: Monthly Reporting

1. **Set Date Range**: Previous month
2. **Review Metrics**:
   - Total traffic and leads
   - Conversion rates
   - Top performers
3. **Export Data**: Download Excel report
4. **Analyze Trends**: Compare to previous months
5. **Take Action**:
   - Reward top affiliates
   - Support underperforming affiliates
   - Optimize property listings

---

## Troubleshooting

### Issue: WhatsApp Notifications Not Sending

**Symptoms**: Affiliates not receiving lead notifications

**Solutions**:
1. Check GoWA API credentials in Settings
2. Verify API URL is correct
3. Test connection using "Test Connection" button
4. Check GoWA account credits
5. Review error logs: `/storage/logs/laravel.log`
6. Contact GoWA support if issue persists

### Issue: Property Images Not Displaying

**Symptoms**: Broken image links on property pages

**Solutions**:
1. Verify storage link exists: `php artisan storage:link`
2. Check file permissions on `/storage` directory
3. Ensure images uploaded successfully
4. Clear browser cache
5. Check media library configuration

### Issue: Affiliate Links Not Tracking

**Symptoms**: Visits not recorded when using affiliate links

**Solutions**:
1. Verify affiliate code is correct
2. Check cookie settings in browser (cookies must be enabled)
3. Test with different browsers
4. Review middleware configuration
5. Check visits table for records
6. Verify affiliate status is "Active"

### Issue: Search Not Working

**Symptoms**: Property search returns no results

**Solutions**:
1. Verify Laravel Scout is configured
2. Check database driver in `config/scout.php`
3. Ensure properties have "Published" status
4. Test with different search terms
5. Check fulltext index on properties table

### Issue: Dashboard Widgets Not Loading

**Symptoms**: Blank or error on dashboard

**Solutions**:
1. Clear application cache: `php artisan cache:clear`
2. Clear view cache: `php artisan view:clear`
3. Check database connection
4. Review error logs
5. Verify user has correct permissions

### Issue: Cannot Login

**Symptoms**: Login fails with correct credentials

**Solutions**:
1. Verify user status is "Active" (not Blocked or Pending)
2. Check email address is correct
3. Reset password if needed
4. Clear browser cookies
5. Try different browser
6. Check session configuration

### Getting Help

If you encounter issues not covered here:

1. **Check Logs**: `/storage/logs/laravel.log`
2. **Review Documentation**: Technical documentation in `/docs`
3. **Contact Support**: Provide:
   - Error message
   - Steps to reproduce
   - Screenshots
   - Log excerpts

---

## Best Practices

### Property Management
- Use high-quality images (minimum 1200px width)
- Write detailed, accurate descriptions
- Update property status promptly when sold
- Use consistent naming conventions
- Add comprehensive specifications

### User Management
- Review affiliate applications promptly
- Monitor affiliate performance regularly
- Block suspicious accounts immediately
- Keep contact information updated
- Communicate policy changes clearly

### Lead Management
- Monitor new leads daily
- Follow up on unresponsive affiliates
- Track conversion rates
- Analyze lead quality
- Provide feedback to affiliates

### System Maintenance
- Review error logs weekly
- Update settings as needed
- Monitor API usage and credits
- Backup database regularly
- Test critical features monthly

---

## Keyboard Shortcuts

- `Ctrl/Cmd + K`: Global search
- `Ctrl/Cmd + S`: Save form
- `Esc`: Close modal
- `Tab`: Navigate form fields
- `Enter`: Submit form

---

## Support Resources

- **Technical Documentation**: `/docs/TECHNICAL_DOCUMENTATION.md`
- **API Documentation**: `/docs/API_DOCUMENTATION.md`
- **RBAC Setup**: `/docs/RBAC_SETUP.md`
- **Performance Guide**: `/docs/PERFORMANCE_OPTIMIZATION.md`

---

*Last Updated: November 2025*
*Version: 1.0*

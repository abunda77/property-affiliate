# Common Workflows Guide

## Table of Contents
1. [Super Admin Workflows](#super-admin-workflows)
2. [Affiliate Workflows](#affiliate-workflows)
3. [System Maintenance Workflows](#system-maintenance-workflows)

---

## Super Admin Workflows

### Workflow 1: Onboarding a New Property

**Goal**: Add a new property to the system and make it available to affiliates

**Steps**:
1. **Prepare Materials**:
   - Gather property photos (high quality, 1200px+ width)
   - Compile property details (title, price, location, description)
   - List features and specifications
   - Verify all information is accurate

2. **Create Property**:
   - Navigate to Properties → New Property
   - Enter title (auto-generates slug)
   - Set price in IDR
   - Enter full location/address
   - Write detailed description using rich text editor
   - Set status to "Published"

3. **Add Features**:
   - Click "Add item" for each feature
   - Examples: "3 Bedrooms", "Swimming Pool", "Garden"
   - Add 5-10 key features

4. **Add Specifications**:
   - Add key-value pairs
   - Examples: Land Size: 500 m², Certificate: SHM
   - Include all technical details

5. **Upload Images**:
   - Upload 5-15 high-quality images
   - First image becomes thumbnail
   - Drag to reorder images
   - System auto-optimizes (WebP, thumbnails)

6. **Verify and Publish**:
   - Review all information
   - Click "Create"
   - Visit public page: `/p/{slug}`
   - Test contact form
   - Verify images load correctly

7. **Notify Affiliates** (Optional):
   - Send announcement about new property
   - Highlight key selling points
   - Encourage immediate promotion

**Time Required**: 15-20 minutes per property

---

### Workflow 2: Approving New Affiliates

**Goal**: Review and approve affiliate registrations

**Steps**:

1. **Review Pending Users**:
   - Navigate to Users
   - Filter by Status: Pending
   - Review each application:
     - Check name and email
     - Verify WhatsApp number format
     - Review profile information

2. **Approve Users**:
   - Select users to approve (checkboxes)
   - Click Bulk Actions → Approve Affiliates
   - System automatically:
     - Generates unique affiliate code
     - Assigns Affiliate role
     - Sends welcome email
     - Changes status to Active

3. **Verify Approval**:
   - Check user status changed to Active
   - Verify affiliate code generated
   - Confirm welcome email sent

4. **Follow Up** (Optional):
   - Send additional onboarding materials
   - Provide training resources
   - Schedule orientation call

**Time Required**: 5 minutes per affiliate

---

### Workflow 3: Managing Leads

**Goal**: Monitor and manage all leads in the system

**Steps**:

1. **Daily Lead Review**:
   - Navigate to Leads
   - Filter by Status: New
   - Review new leads from last 24 hours
   - Check affiliate attribution

2. **Monitor Follow-Up**:
   - Filter by Status: Follow Up
   - Check lead age (days since submission)
   - Identify stale leads (>7 days)
   - Contact affiliates about old leads

3. **Track Conversions**:
   - Filter by Status: Closed
   - Review successful conversions
   - Calculate conversion rates
   - Identify top performers

4. **Handle Issues**:
   - Check leads with no affiliate (null affiliate_id)
   - Manually assign if needed
   - Investigate tracking issues

5. **Generate Reports**:
   - Set date range (e.g., last month)
   - Export lead data
   - Analyze performance metrics
   - Share insights with team

**Frequency**: Daily for new leads, weekly for full review

---

### Workflow 4: Monthly Performance Review

**Goal**: Analyze system performance and optimize

**Steps**:

1. **Set Date Range**: Previous month

2. **Review Global Metrics**:
   - Total traffic across all affiliates
   - Total leads generated
   - Overall conversion rate
   - Active affiliate count

3. **Analyze Top Performers**:
   - Identify top 5 affiliates by leads
   - Review their strategies
   - Consider rewards/recognition

4. **Identify Issues**:
   - Find underperforming affiliates
   - Check for inactive affiliates
   - Review properties with low interest

5. **Optimize Properties**:
   - Update descriptions for low-performing properties
   - Add new photos if needed
   - Adjust pricing if appropriate
   - Mark sold properties

6. **Export and Share**:
   - Export monthly report
   - Share with stakeholders
   - Set goals for next month

**Time Required**: 1-2 hours monthly

---

### Workflow 5: System Configuration

**Goal**: Configure and maintain system settings

**Steps**:

1. **GoWA API Setup**:
   - Navigate to Settings → GoWA Integration
   - Enter username and password
   - Set API URL
   - Click "Test Connection"
   - Save if successful

2. **Branding Configuration**:
   - Upload company logo
   - Verify logo appears in header
   - Check logo on public pages

3. **SEO Settings**:
   - Set default site title
   - Write meta description
   - Add meta keywords
   - Save changes

4. **Google Analytics**:
   - Enter GA tracking ID
   - Verify tracking code in page source
   - Check GA widget on dashboard

5. **Test Configuration**:
   - Submit test lead
   - Verify WhatsApp notification
   - Check email notifications
   - Test all critical features

**Frequency**: Initial setup + as needed

---

## Affiliate Workflows

### Workflow 1: Daily Lead Management

**Goal**: Respond to and manage new leads effectively

**Steps**:

1. **Morning Check** (9:00 AM):
   - Login to dashboard
   - Check for new leads (red badge)
   - Review WhatsApp notifications

2. **Prioritize Leads**:
   - Sort by date (newest first)
   - Focus on "New" status leads
   - Note property interest

3. **First Contact** (Within 1 hour):
   - Click "Click to WA" button
   - Send professional greeting:
     ```
     Halo [Name], terima kasih atas minat Anda pada [Property]. 
     Saya [Your Name], siap membantu. Kapan waktu yang tepat 
     untuk saya menjelaskan detail properti ini?
     ```
   - Update status to "Follow Up"
   - Add notes about conversation

4. **Follow-Up Schedule**:
   - Set reminders for follow-ups
   - Send additional information
   - Answer questions promptly
   - Schedule property viewings

5. **Update Status**:
   - "Survey" when viewing scheduled
   - "Closed" when deal complete
   - "Lost" if lead doesn't convert
   - Always add notes

**Time Required**: 30-60 minutes daily

---

### Workflow 2: Weekly Content Creation

**Goal**: Create and share promotional content consistently

**Steps**:

1. **Monday - Plan Content**:
   - Review top performing properties
   - Select 3-5 properties to promote
   - Plan posting schedule

2. **Tuesday - Create Content**:
   - Download promotional materials for selected properties
   - Extract ZIP files
   - Review images and descriptions
   - Create social media posts

3. **Wednesday-Friday - Post Daily**:
   - Post one property per day
   - Share on multiple platforms:
     - Facebook (personal + groups)
     - Instagram (feed + stories)
     - WhatsApp status
   - Include tracking link
   - Engage with comments

4. **Weekend - Analyze**:
   - Review dashboard metrics
   - Check which posts performed best
   - Note successful strategies
   - Plan next week's content

**Time Required**: 5-7 hours weekly

---

### Workflow 3: Property Promotion Campaign

**Goal**: Launch focused campaign for specific property

**Steps**:

1. **Select Property**:
   - Choose high-value or new property
   - Review all details thoroughly
   - Understand unique selling points

2. **Download Materials**:
   - Click "Download Materi Promosi"
   - Extract ZIP file
   - Review all images and text

3. **Create Content Variations**:
   - Write 3-5 different post versions
   - Create urgency ("Limited time", "Hot property")
   - Highlight different features
   - Use various images

4. **Multi-Platform Launch**:
   - Day 1: Facebook post + groups
   - Day 2: Instagram feed + stories
   - Day 3: WhatsApp status + broadcasts
   - Day 4: Repeat on different groups
   - Day 5: Follow-up posts

5. **Monitor and Engage**:
   - Check dashboard for clicks
   - Respond to comments/questions
   - Share additional details
   - Follow up on leads immediately

6. **Analyze Results**:
   - Review total clicks
   - Count leads generated
   - Calculate conversion rate
   - Document what worked

**Duration**: 5-7 days per campaign

---

### Workflow 4: Monthly Performance Review

**Goal**: Analyze personal performance and improve

**Steps**:

1. **Set Date Range**: Last 30 days

2. **Review Metrics**:
   - Total clicks
   - Total leads
   - Conversion rate
   - Top properties

3. **Identify Patterns**:
   - Which properties performed best?
   - Which platforms drove most traffic?
   - What posting times worked best?
   - Which content style got most engagement?

4. **Set Goals**:
   - Target clicks for next month
   - Target leads for next month
   - Target conversion rate improvement
   - New platforms to try

5. **Adjust Strategy**:
   - Focus on high-performing properties
   - Double down on successful platforms
   - Try new content formats
   - Improve response time

**Time Required**: 1 hour monthly

---

## System Maintenance Workflows

### Workflow 1: Daily System Check (Admin)

**Goal**: Ensure system is running smoothly

**Steps**:

1. **Check Error Logs**:
   ```bash
   tail -100 storage/logs/laravel.log
   ```
   - Look for errors or warnings
   - Investigate any issues

2. **Verify Queue**:
   ```bash
   php artisan queue:work --once
   ```
   - Ensure queue is processing
   - Check for failed jobs

3. **Test Critical Features**:
   - Submit test lead
   - Verify WhatsApp notification
   - Check property page loads
   - Test affiliate tracking

4. **Monitor Performance**:
   - Check page load times
   - Review database queries
   - Monitor server resources

**Time Required**: 10-15 minutes daily

---

### Workflow 2: Weekly Maintenance (Admin)

**Goal**: Perform routine maintenance tasks

**Steps**:

1. **Clear Caches**:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:cache
   ```

2. **Review Failed Jobs**:
   ```bash
   php artisan queue:failed
   php artisan queue:retry all
   ```

3. **Check Disk Space**:
   ```bash
   df -h
   ```
   - Clean old logs if needed
   - Remove temporary files

4. **Update Sitemap**:
   ```bash
   php artisan sitemap:generate
   ```

5. **Database Optimization**:
   ```bash
   php artisan optimize
   ```

6. **Review User Feedback**:
   - Check support emails
   - Review reported issues
   - Plan fixes/improvements

**Time Required**: 30-45 minutes weekly

---

### Workflow 3: Monthly Backup and Update (Admin)

**Goal**: Maintain system security and data integrity

**Steps**:

1. **Backup Database**:
   ```bash
   php artisan backup:run
   ```
   - Verify backup completed
   - Test restoration (quarterly)

2. **Update Dependencies**:
   ```bash
   composer update
   npm update
   ```
   - Review changelog
   - Test after updates

3. **Security Review**:
   - Check for Laravel security updates
   - Review user permissions
   - Audit access logs
   - Update passwords if needed

4. **Performance Optimization**:
   - Review slow queries
   - Optimize database indexes
   - Check cache hit rates
   - Optimize images

5. **Documentation Update**:
   - Update user guides if features changed
   - Document any new workflows
   - Update troubleshooting guide

**Time Required**: 2-3 hours monthly

---

## Quick Reference Checklists

### New Property Checklist
- [ ] High-quality images uploaded (5-15)
- [ ] Title and description complete
- [ ] Price set correctly
- [ ] Location accurate
- [ ] Features added (5-10)
- [ ] Specifications complete
- [ ] Status set to "Published"
- [ ] Public page tested
- [ ] Contact form tested
- [ ] SEO verified

### New Affiliate Checklist
- [ ] Application reviewed
- [ ] Contact information verified
- [ ] User approved
- [ ] Affiliate code generated
- [ ] Welcome email sent
- [ ] Role assigned correctly
- [ ] Login tested
- [ ] Training materials provided

### Lead Follow-Up Checklist
- [ ] First contact within 1 hour
- [ ] Professional greeting sent
- [ ] Status updated
- [ ] Notes added
- [ ] Follow-up scheduled
- [ ] Additional info provided
- [ ] Questions answered
- [ ] Viewing scheduled (if applicable)
- [ ] Final status updated

### Content Posting Checklist
- [ ] Property selected
- [ ] Materials downloaded
- [ ] Post written
- [ ] Images selected
- [ ] Tracking link included
- [ ] Call-to-action added
- [ ] Posted on platform
- [ ] Engagement monitored
- [ ] Performance tracked

---

*Last Updated: November 2025*
*Version: 1.0*

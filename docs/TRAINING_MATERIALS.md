# PAMS Training Materials

## Overview

This document provides comprehensive training materials for PAMS (Property Affiliate Management System). It includes training outlines, presentation scripts, hands-on exercises, and Q&A resources.

---

## Table of Contents

1. [Super Admin Training](#super-admin-training)
2. [Affiliate Training](#affiliate-training)
3. [Hands-On Exercises](#hands-on-exercises)
4. [Common Questions & Answers](#common-questions--answers)
5. [Training Checklist](#training-checklist)
6. [Video Recording Guide](#video-recording-guide)

---

## Super Admin Training

### Session Duration: 2 hours

### Session Outline

**Part 1: System Overview (20 minutes)**
- Introduction to PAMS
- System architecture overview
- User roles and permissions
- Dashboard walkthrough

**Part 2: Property Management (30 minutes)**
- Creating properties
- Managing images
- Property status workflow
- SEO optimization

**Part 3: User Management (25 minutes)**
- Approving affiliates
- Managing user accounts
- Blocking/unblocking users
- Role assignment

**Part 4: Lead Management (20 minutes)**
- Viewing all leads
- Filtering and exporting
- Monitoring affiliate performance
- Lead status tracking

**Part 5: System Configuration (20 minutes)**
- GoWA API setup
- Google Analytics integration
- SEO settings
- Logo and branding

**Part 6: Analytics & Reporting (15 minutes)**
- Understanding metrics
- Generating reports
- Performance analysis
- Data export

**Q&A Session (10 minutes)**

---

### Detailed Training Script

#### Part 1: System Overview

**Script**:

"Welcome to PAMS - Property Affiliate Management System. This platform helps you manage property listings and distribute them through a network of affiliates.

As a Super Admin, you have complete control over:
- All properties in the system
- All user accounts (affiliates)
- All leads generated
- System-wide settings
- Global analytics

Let me show you the dashboard. [Navigate to dashboard]

Here you can see:
- Total traffic across all affiliates
- Total leads generated
- Number of active affiliates
- Performance charts
- Top performing affiliates
- Recent activity feed

The sidebar gives you access to all major features. Let's explore each one."

**Demo Actions**:
1. Login as Super Admin
2. Show dashboard overview
3. Explain each widget
4. Navigate through sidebar menu
5. Show notification area

---

#### Part 2: Property Management

**Script**:

"Properties are the core of the system. Let's create a new property together.

Click 'Properties' in the sidebar, then 'New Property'.

You'll need to fill in:
- Title: This auto-generates a URL-friendly slug
- Price: In Indonesian Rupiah
- Location: Full address or area
- Description: Use the rich text editor for formatting
- Status: Draft, Published, or Sold

Features are added as a list - click 'Add item' for each feature like 'Swimming Pool' or '3 Bedrooms'.

Specifications are key-value pairs like 'Land Size: 500 m²' or 'Certificate: SHM'.

For images, you can drag and drop or click to upload. The first image becomes the thumbnail. You can reorder by dragging.

The system automatically:
- Generates a unique slug
- Optimizes images (WebP conversion, thumbnails)
- Creates SEO meta tags
- Updates the sitemap

Let me create a sample property now."

**Demo Actions**:
1. Navigate to Properties
2. Click New Property
3. Fill in all fields
4. Add 3-5 features
5. Add 3-5 specifications
6. Upload 5 images
7. Set status to Published
8. Save property
9. View public page
10. Show auto-generated slug

**Practice Exercise**:
"Now it's your turn. Create a property with:
- Title: 'Modern Villa in Seminyak'
- Price: 5000000000
- Add at least 5 features
- Add at least 5 specifications
- Upload at least 3 images
- Set to Published"

---

#### Part 3: User Management

**Script**:

"User management is crucial for maintaining platform quality. When someone registers as an affiliate, they start in 'Pending' status.

Let's review the approval process:

1. Go to Users
2. Filter by Status: Pending
3. Review each application:
   - Check name and email
   - Verify WhatsApp number format
   - Review profile information

4. To approve:
   - Select users with checkboxes
   - Click 'Bulk Actions'
   - Select 'Approve Affiliates'

The system automatically:
- Generates a unique 8-character affiliate code
- Assigns the Affiliate role
- Sends a welcome email with their code
- Changes status to Active

Affiliates can now:
- Login to their dashboard
- Generate tracking links
- View their leads
- Access analytics

If you need to block a user:
- Click Edit on the user
- Change Status to Blocked
- Save

Blocked users cannot login. You can unblock by changing status back to Active."

**Demo Actions**:
1. Show pending users list
2. Review a pending user
3. Approve one user
4. Show welcome email (if possible)
5. Verify affiliate code generated
6. Show how to block a user
7. Explain role permissions

**Practice Exercise**:
"Approve the test affiliate account and verify:
- Affiliate code was generated
- Status changed to Active
- User can now login"

---

#### Part 4: Lead Management

**Script**:

"Leads are the lifeblood of the system. Every time a visitor submits a contact form, a lead is created.

In the Leads section, you can see:
- All leads across all affiliates
- Visitor name and WhatsApp
- Which property they're interested in
- Which affiliate gets credit
- Current status
- Submission date

You can filter by:
- Status (New, Follow Up, Survey, Closed, Lost)
- Specific affiliate
- Specific property
- Date range

Lead statuses mean:
- New: Just submitted, needs immediate attention
- Follow Up: Initial contact made
- Survey: Property viewing scheduled
- Closed: Deal successfully completed
- Lost: Lead didn't convert

You can export leads for reporting:
- Apply filters
- Click Export
- Choose CSV or Excel

Monitor affiliate performance by:
- Checking lead counts per affiliate
- Reviewing conversion rates
- Identifying top performers
- Following up on stale leads"

**Demo Actions**:
1. Navigate to Leads
2. Show all leads
3. Apply various filters
4. Show lead details
5. Explain status meanings
6. Export sample data
7. Show affiliate performance comparison

---

#### Part 5: System Configuration

**Script**:

"System settings control how PAMS operates. Let's configure each section.

**GoWA API Setup**:
This enables WhatsApp notifications. You'll need:
- Username from your GoWA account
- Password from your GoWA account
- API URL (usually https://api.gowa.id/v1)

Enter these in Settings → GoWA Integration, then click 'Test Connection' to verify.

**Branding**:
Upload your company logo here. It appears in:
- Admin panel header
- Public website header
- Email notifications

**SEO Settings**:
Set default values for:
- Site Title
- Meta Description
- Meta Keywords

Individual property pages override these with property-specific SEO.

**Google Analytics**:
Enter your GA tracking ID (format: G-XXXXXXXXXX). The tracking code is automatically added to all pages, and the dashboard widget shows your analytics."

**Demo Actions**:
1. Navigate to Settings
2. Show GoWA configuration
3. Test connection (if credentials available)
4. Upload sample logo
5. Configure SEO settings
6. Add Google Analytics ID
7. Verify tracking code in page source

**Practice Exercise**:
"Configure the system settings:
- Upload your company logo
- Set SEO title and description
- Add Google Analytics ID (if available)"

---

#### Part 6: Analytics & Reporting

**Script**:

"Analytics help you understand system performance and make data-driven decisions.

The dashboard shows:
- Total Traffic: All visits across all affiliates
- Total Leads: All leads generated
- Active Affiliates: Users with active status
- Conversion Rate: (Leads / Traffic) × 100

The performance chart shows trends over time. You can:
- Toggle between daily, weekly, monthly views
- Hover for specific data points
- Identify patterns and trends

Top Affiliates table shows:
- Ranked by lead count
- Visits, leads, and conversion rate for each
- Click name to view detailed affiliate analytics

Use date range filters to:
- View specific time periods
- Compare different periods
- Generate custom reports

Export reports by:
- Setting desired date range
- Clicking Export Report
- Choosing PDF or Excel format

Reports include:
- Summary statistics
- Affiliate performance breakdown
- Lead status distribution
- Top properties

Use this data to:
- Reward top performers
- Support underperforming affiliates
- Optimize property listings
- Plan marketing strategies"

**Demo Actions**:
1. Show dashboard metrics
2. Explain each metric
3. Use date range filter
4. Show performance chart
5. Review top affiliates
6. Export sample report
7. Open and review exported file

---

### Super Admin Quick Reference Card

**Daily Tasks**:
- [ ] Check new leads
- [ ] Review pending affiliate applications
- [ ] Monitor error logs
- [ ] Verify WhatsApp notifications working

**Weekly Tasks**:
- [ ] Review affiliate performance
- [ ] Update property listings
- [ ] Export lead reports
- [ ] Check system health

**Monthly Tasks**:
- [ ] Generate performance reports
- [ ] Review and optimize properties
- [ ] Analyze conversion rates
- [ ] Plan improvements

**Key Shortcuts**:
- Ctrl/Cmd + K: Global search
- Ctrl/Cmd + S: Save form

---

## Affiliate Training

### Session Duration: 1.5 hours

### Session Outline

**Part 1: Getting Started (15 minutes)**
- Account approval process
- First login
- Dashboard overview
- Understanding your affiliate code

**Part 2: Generating Tracking Links (20 minutes)**
- Why tracking links matter
- How to generate links
- Where to share links
- Best practices

**Part 3: Managing Leads (25 minutes)**
- Viewing your leads
- Contacting leads via WhatsApp
- Updating lead status
- Adding notes

**Part 4: Promotional Materials (15 minutes)**
- Downloading materials
- Using images and descriptions
- Creating social media posts
- Content ideas

**Part 5: Analytics & Performance (20 minutes)**
- Understanding metrics
- Reading your dashboard
- Improving conversion rate
- Setting goals

**Part 6: Profile Settings (10 minutes)**
- Updating your information
- Profile photo
- WhatsApp number

**Q&A Session (15 minutes)**

---

### Detailed Training Script

#### Part 1: Getting Started

**Script**:

"Welcome to PAMS! As an affiliate, you'll promote properties and earn commissions for leads you generate.

After registration, your account needs approval. You'll receive an email when approved with your unique affiliate code.

Your affiliate code is like your ID - it tracks all visitors who click your links. It's an 8-character code like 'ABC12345'.

Let me show you the dashboard. [Navigate to dashboard]

You'll see:
- Today's Performance: Clicks, leads, and conversion rate
- Monthly Performance Chart: Your traffic and leads over time
- Top Properties: Which properties perform best for you
- Device Breakdown: Mobile vs desktop traffic

This data helps you understand what's working and optimize your strategy."

**Demo Actions**:
1. Show affiliate dashboard
2. Explain each widget
3. Point out affiliate code location
4. Show date range filter
5. Demonstrate changing date ranges

---

#### Part 2: Generating Tracking Links

**Script**:

"Tracking links are how you get credit for your work. Every visitor who clicks your link is tracked for 30 days.

To generate a link:
1. Click 'My Properties' in sidebar
2. Find the property you want to promote
3. Click 'Copy Link Saya'
4. Link is copied to clipboard
5. Paste anywhere you want to share

Your link looks like:
https://yourdomain.com/p/villa-name?ref=ABC12345

The '?ref=ABC12345' part is your tracking code. Never remove or modify it!

You can also use:
https://yourdomain.com/ref/ABC12345

This redirects to the catalog with your tracking active.

Share your links on:
- Facebook posts and groups
- Instagram bio and stories
- WhatsApp status and chats
- Twitter/X posts
- TikTok descriptions
- Email newsletters
- Blog posts

Best practices:
✅ Always use your tracking link
✅ Include compelling descriptions
✅ Use high-quality images
✅ Target relevant audiences
✅ Share consistently

❌ Don't spam groups
❌ Don't use misleading information
❌ Don't modify the tracking code
❌ Don't share sold properties"

**Demo Actions**:
1. Navigate to My Properties
2. Generate tracking link
3. Show link format
4. Explain tracking parameter
5. Demonstrate alternative format
6. Show example social media post

**Practice Exercise**:
"Generate a tracking link for any property and:
- Copy the link
- Paste it in a text document
- Verify your affiliate code is in the URL"

---

#### Part 3: Managing Leads

**Script**:

"When someone submits a contact form using your link, you get a lead. You'll receive a WhatsApp notification immediately.

To view your leads:
1. Click 'Leads' in sidebar
2. See all your leads with:
   - Visitor name
   - WhatsApp number
   - Property they're interested in
   - Status
   - Submission date

Lead statuses:
- New: Just submitted - contact ASAP!
- Follow Up: Initial contact made
- Survey: Viewing scheduled
- Closed: Deal complete! 🎉
- Lost: Didn't convert

To contact a lead:
1. Find the lead in your list
2. Click 'Click to WA' button
3. WhatsApp Web opens
4. Send your message

First contact template:
'Halo [Name], terima kasih atas minat Anda pada [Property]. Saya [Your Name], siap membantu. Kapan waktu yang tepat untuk saya menjelaskan detail properti ini?'

After contacting:
1. Update status to 'Follow Up'
2. Add notes about conversation
3. Schedule next follow-up

Adding notes:
1. Click Edit on lead
2. Add notes in Notes field
3. Include: call summary, feedback, next steps
4. Save

Example note:
'2025-11-23: First call, interested in 3BR option. Scheduled viewing for 2025-11-25 at 10 AM. Budget confirmed at 2M.'

Tips for success:
- Respond within 1 hour
- Be professional and helpful
- Listen to their needs
- Follow up consistently
- Update status regularly
- Keep detailed notes"

**Demo Actions**:
1. Show leads list
2. Explain each column
3. Click 'Click to WA' button
4. Show WhatsApp Web opening
5. Demonstrate status update
6. Show how to add notes
7. Explain status workflow

**Practice Exercise**:
"Practice with a test lead:
- View lead details
- Click 'Click to WA' button
- Update status to 'Follow Up'
- Add a sample note"

---

#### Part 4: Promotional Materials

**Script**:

"We provide ready-to-use promotional materials for every property.

To download:
1. Go to 'My Properties'
2. Find the property
3. Click 'Download Materi Promosi'
4. ZIP file downloads
5. Extract the files

The package includes:
- All property images (high resolution)
- Description text file
- Your tracking link
- Social media optimized graphics

Using the materials:

**For Facebook**:
- Use high-quality images
- Copy description text
- Add your tracking link
- Include call-to-action
- Post in relevant groups

**For Instagram**:
- Use square/vertical images
- Write engaging caption
- Add relevant hashtags
- Put link in bio or story
- Use Instagram Stories

**For WhatsApp Status**:
- Use vertical images
- Add text overlay with key features
- Include your tracking link
- Post regularly

Content ideas:

Property Highlights:
'🏡 New Listing: [Property Name]
✨ Features: [List features]
💰 Price: [Price]
📍 Location: [Location]
🔗 Details: [Your Link]'

Urgency Posts:
'⚡ Limited Time Offer'
'🔥 Hot Property Alert'
'👀 Don't Miss This'

Lifestyle Posts:
'Imagine waking up here...'
'Your dream home awaits...'
'Invest in your future...'

Post consistently:
- At least once daily
- Mix different properties
- Vary your content style
- Test different times
- Engage with comments"

**Demo Actions**:
1. Navigate to My Properties
2. Click Download button
3. Show ZIP file
4. Extract and show contents
5. Open sample image
6. Show description file
7. Demonstrate creating social media post

**Practice Exercise**:
"Download promotional materials and:
- Extract the ZIP file
- Review all images
- Read the description
- Create a sample Facebook post
- Include your tracking link"

---

#### Part 5: Analytics & Performance

**Script**:

"Understanding your metrics helps you improve performance.

Key metrics:

**Clicks (Visits)**:
- Number of people who clicked your links
- Tracked for 30 days via cookies
- Includes return visits

**Leads**:
- Number of contact forms submitted
- Only counts leads attributed to you
- Within 30-day tracking window

**Conversion Rate**:
- Formula: (Leads / Clicks) × 100
- Industry average: 2-5%
- Higher is better!

**Device Breakdown**:
- Shows mobile vs desktop traffic
- Helps optimize your content
- Focus on dominant device type

To improve clicks:
- Share links more frequently
- Use multiple platforms
- Create engaging content
- Target relevant audiences
- Use eye-catching images

To improve conversion rate:
- Respond to leads quickly
- Provide detailed information
- Be professional and helpful
- Follow up consistently
- Build trust with prospects

Track what works:
- Monitor top properties
- Note which platforms drive traffic
- Test different posting times
- Analyze successful posts
- Replicate winning strategies

Set goals:
- Target clicks per month
- Target leads per month
- Target conversion rate
- Track progress weekly
- Adjust strategy as needed

Review performance:
- Daily: Check new leads
- Weekly: Review metrics
- Monthly: Analyze trends
- Quarterly: Set new goals"

**Demo Actions**:
1. Show dashboard metrics
2. Explain each metric
3. Calculate conversion rate
4. Show device breakdown
5. Demonstrate date range filter
6. Compare different time periods
7. Identify trends

**Practice Exercise**:
"Review your dashboard and:
- Note your current conversion rate
- Identify your top property
- Check device breakdown
- Set a goal for next month"

---

#### Part 6: Profile Settings

**Script**:

"Your profile information appears on property pages when visitors use your link. Keep it professional and complete.

To update your profile:
1. Click your name in top right
2. Select 'Profile Settings'
3. Update information
4. Save changes

**Name**:
- Your display name
- Shown on property pages
- Use real name or business name
- Keep it professional

**WhatsApp Number**:
- Used for lead notifications
- Format: +62812XXXXXXXX
- Include country code
- Verify number is active

**Profile Photo**:
- Click 'Upload Photo'
- Choose professional photo
- Minimum 400x400px
- Appears on property pages

Why profile matters:
- Builds trust with buyers
- Professional appearance
- Proper notifications
- Better conversion rates

Keep updated:
- Change WhatsApp if number changes
- Update photo periodically
- Maintain professional image"

**Demo Actions**:
1. Navigate to Profile Settings
2. Show each field
3. Demonstrate photo upload
4. Update sample information
5. Save changes
6. Show where profile appears on property page

**Practice Exercise**:
"Update your profile:
- Add/update your name
- Verify WhatsApp number
- Upload profile photo
- Save changes"

---

### Affiliate Quick Reference Card

**Daily Tasks**:
- [ ] Check for new leads
- [ ] Respond to WhatsApp notifications
- [ ] Follow up on pending leads
- [ ] Share at least one property link

**Weekly Tasks**:
- [ ] Review performance metrics
- [ ] Download new property materials
- [ ] Update lead statuses
- [ ] Plan next week's content

**Monthly Tasks**:
- [ ] Analyze monthly performance
- [ ] Identify top properties
- [ ] Adjust marketing strategy
- [ ] Set goals for next month

**Success Tips**:
- Respond to leads within 1 hour
- Post consistently, not just once
- Use multiple platforms
- Be professional and helpful
- Track what works and do more of it

---

## Hands-On Exercises

### Exercise 1: Complete Property Workflow (Super Admin)

**Objective**: Create, publish, and verify a property

**Steps**:
1. Create new property with all details
2. Upload 5 images
3. Add 5 features and 5 specifications
4. Set status to Published
5. Save property
6. View public page
7. Test contact form
8. Verify lead created
9. Check WhatsApp notification sent

**Success Criteria**:
- Property appears in public catalog
- All images display correctly
- Contact form works
- Lead appears in dashboard
- Notification sent successfully

---

### Exercise 2: Affiliate Approval Workflow (Super Admin)

**Objective**: Approve a new affiliate and verify setup

**Steps**:
1. Navigate to Users
2. Filter by Status: Pending
3. Review pending user
4. Approve user
5. Verify affiliate code generated
6. Check welcome email sent
7. Login as affiliate (if possible)
8. Verify dashboard access
9. Generate test tracking link

**Success Criteria**:
- User status changed to Active
- Affiliate code generated
- Welcome email sent
- User can login
- Can generate tracking links

---

### Exercise 3: Lead Management Workflow (Affiliate)

**Objective**: Manage a lead from submission to closed

**Steps**:
1. Receive lead notification
2. View lead in dashboard
3. Click 'Click to WA' button
4. Send first contact message
5. Update status to 'Follow Up'
6. Add notes about conversation
7. Schedule follow-up
8. Update status to 'Survey'
9. Add viewing notes
10. Update status to 'Closed'

**Success Criteria**:
- Lead contacted within 1 hour
- Status updated appropriately
- Detailed notes added
- Final status reflects outcome

---

### Exercise 4: Content Creation Workflow (Affiliate)

**Objective**: Create and share promotional content

**Steps**:
1. Select property to promote
2. Download promotional materials
3. Extract ZIP file
4. Review images and description
5. Create Facebook post
6. Create Instagram story
7. Create WhatsApp status
8. Include tracking link in all
9. Post content
10. Monitor dashboard for clicks

**Success Criteria**:
- Materials downloaded successfully
- Content created for 3 platforms
- Tracking link included correctly
- Content posted
- Clicks appear in dashboard

---

## Common Questions & Answers

### Super Admin Q&A

**Q: How do I reset an affiliate's password?**
A: Go to Users, click Edit on the user, and use the password reset option. The user will receive an email with reset instructions.

**Q: Can I delete a property that has leads?**
A: Yes, but leads will remain in the system. The property reference will show as deleted. Consider setting status to "Sold" instead.

**Q: How do I handle spam or fake leads?**
A: You can delete individual leads from the Leads list. Consider blocking the affiliate if they're generating fake leads.

**Q: What if WhatsApp notifications aren't sending?**
A: Check Settings → GoWA Integration. Verify credentials are correct and test the connection. Check your GoWA account credits.

**Q: How do I export all data?**
A: Use the Export function in each section (Properties, Users, Leads). For complete database backup, use MySQL dump.

**Q: Can I have multiple Super Admins?**
A: Yes, create additional users and assign them the Super Admin role using Filament Shield.

**Q: How do I change the website logo?**
A: Go to Settings → Branding and upload a new logo. It updates immediately across the site.

**Q: What's the difference between Draft and Sold status?**
A: Draft properties are hidden from everyone. Sold properties show a "SOLD" badge but are still visible.

---

### Affiliate Q&A

**Q: How long does my tracking cookie last?**
A: 30 days. If someone clicks your link today, you get credit for any lead they submit within 30 days.

**Q: What if someone clicks multiple affiliate links?**
A: The most recent affiliate link gets credit (last-click attribution).

**Q: Can I promote properties on paid ads?**
A: Check with your admin. Some programs allow it, others don't. Always follow the terms of service.

**Q: How do I know if my link is working?**
A: Click your own link in a private/incognito browser. You should see a visit in your dashboard within a few minutes.

**Q: What if I don't receive WhatsApp notifications?**
A: Check your Profile Settings. Verify your WhatsApp number is correct and includes the country code (+62).

**Q: Can I change my affiliate code?**
A: No, affiliate codes are permanent. They're used for tracking and cannot be changed.

**Q: How do commissions work?**
A: Contact your admin for commission details. This varies by program.

**Q: What if a lead doesn't respond?**
A: Follow up 2-3 times over a week. If still no response, mark as "Lost" and move on.

**Q: Can I see other affiliates' performance?**
A: No, you can only see your own data. Only Super Admins see all affiliate data.

**Q: How do I improve my conversion rate?**
A: Respond quickly, be professional, provide detailed information, follow up consistently, and target the right audience.

---

## Training Checklist

### Pre-Training Preparation

**For Trainer**:
- [ ] Review all training materials
- [ ] Prepare demo environment
- [ ] Create test accounts
- [ ] Prepare sample data
- [ ] Test all features
- [ ] Prepare presentation slides
- [ ] Set up screen recording
- [ ] Test audio/video equipment
- [ ] Prepare handouts
- [ ] Create exercise worksheets

**For Participants**:
- [ ] Confirm attendance
- [ ] Send pre-training materials
- [ ] Provide login credentials
- [ ] Share agenda
- [ ] Request questions in advance

### During Training

- [ ] Start recording
- [ ] Introduce yourself and participants
- [ ] Review agenda
- [ ] Set expectations
- [ ] Cover each section
- [ ] Demonstrate features
- [ ] Allow hands-on practice
- [ ] Answer questions
- [ ] Take breaks
- [ ] Summarize key points

### Post-Training

- [ ] Share recording
- [ ] Send training materials
- [ ] Provide reference guides
- [ ] Share contact information
- [ ] Schedule follow-up session
- [ ] Collect feedback
- [ ] Answer additional questions
- [ ] Provide ongoing support

---

## Video Recording Guide

### Recording Setup

**Equipment**:
- Screen recording software (OBS, Camtasia, Loom)
- Good microphone
- Quiet environment
- Stable internet connection

**Settings**:
- Resolution: 1920x1080 (1080p)
- Frame rate: 30 fps
- Audio: 44.1 kHz, stereo
- Format: MP4 (H.264)

### Recording Structure

**Introduction (2 minutes)**:
- Welcome message
- What viewers will learn
- How to use the video
- Where to find resources

**Main Content (Varies by topic)**:
- Clear, step-by-step demonstrations
- Explain what you're doing
- Show keyboard shortcuts
- Highlight important points
- Pause for emphasis

**Conclusion (1 minute)**:
- Recap key points
- Next steps
- Where to get help
- Thank viewers

### Video Topics to Record

**Super Admin Videos**:
1. System Overview (10 min)
2. Creating Properties (15 min)
3. Managing Users (10 min)
4. Lead Management (10 min)
5. System Configuration (15 min)
6. Analytics & Reporting (10 min)

**Affiliate Videos**:
1. Getting Started (10 min)
2. Generating Tracking Links (10 min)
3. Managing Leads (15 min)
4. Downloading Promotional Materials (10 min)
5. Understanding Analytics (10 min)
6. Profile Settings (5 min)

**Quick Tips Videos** (2-3 min each):
- How to reset password
- How to export data
- How to update profile photo
- How to test tracking links
- How to contact support

### Publishing Videos

**Platforms**:
- YouTube (unlisted or private)
- Vimeo
- Internal LMS
- Company intranet

**Organization**:
- Create playlists by role
- Add timestamps in description
- Include links to documentation
- Enable comments for questions
- Update as system changes

---

## Training Feedback Form

### Post-Training Survey

**Rate the following (1-5 scale)**:
- Overall training quality
- Trainer knowledge
- Pace of training
- Hands-on exercises
- Training materials
- Relevance to your role

**Open-ended questions**:
- What was most helpful?
- What needs improvement?
- What topics need more coverage?
- What questions remain unanswered?
- Additional comments or suggestions

---

## Ongoing Support

### Support Channels

**Documentation**:
- User guides
- Technical documentation
- Troubleshooting guide
- Common workflows

**Direct Support**:
- Email: support@yourdomain.com
- WhatsApp: +62XXXXXXXXXXX
- Phone: Available during business hours

**Self-Service**:
- Video tutorials
- FAQ section
- Knowledge base
- Community forum (if available)

### Follow-Up Training

**Schedule**:
- 1 week after initial training: Check-in call
- 1 month after: Refresher session
- Quarterly: Advanced features training
- As needed: New feature training

---

*Last Updated: November 2025*
*Version: 1.0*

# Implementation Plan

- [x] 1. Setup Laravel 12 project foundation and core dependencies









  - Install Laravel 12 with PHP 8.3 configuration
  - Configure database connection in .env file
  - Install FilamentPHP v4, Livewire 3, and Filament Shield packages
  - Install Spatie Media Library and Laravel Scout packages
  - Install Laravel Sanctum for future API authentication
  - Configure TailwindCSS and compile initial assets
  - _Requirements: All requirements depend on this foundation_

- [x] 2. Create database schema and migrations





  - [x] 2.1 Create users table migration with affiliate fields

    - Add columns: name, email, password, whatsapp, affiliate_code, status, profile_photo
    - Add indexes on affiliate_code and status columns
    - _Requirements: 2.1, 2.2, 2.5, 16.1, 16.2, 16.3, 16.4_
  

  - [x] 2.2 Create properties table migration with JSON columns

    - Add columns: title, slug, price, location, description, features (JSON), specs (JSON), status
    - Add indexes on slug, status, and price columns
    - Add fulltext index on title, location, description for search
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 17.1, 17.2, 18.1_
  

  - [x] 2.3 Create leads table migration with foreign keys

    - Add columns: affiliate_id, property_id, name, whatsapp, status, notes
    - Add foreign keys with appropriate cascade rules
    - Add indexes on affiliate_id, property_id, status, created_at
    - _Requirements: 7.2, 7.3, 7.4, 7.5, 10.1, 10.2, 10.4, 10.5_
  

  - [x] 2.4 Create visits table migration for tracking

    - Add columns: affiliate_id, property_id, visitor_ip, device, browser, url
    - Add foreign keys and indexes on affiliate_id, property_id, created_at
    - _Requirements: 6.2, 6.5_
  
  - [x] 2.5 Configure Spatie Media Library migrations


    - Run media library migration for media table
    - _Requirements: 1.2, 14.2_

- [x] 3. Implement core models with relationships and casts





  - [x] 3.1 Create User model with affiliate functionality


    - Define fillable fields and casts for status enum
    - Implement relationships: visits(), leads()
    - Create scopes: scopeAffiliates(), scopePending(), scopeActive()
    - Implement methods: generateAffiliateCode(), approve(), block()
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 16.5_
  

  - [x] 3.2 Create Property model with JSON handling

    - Define fillable fields and casts for features (array), specs (json), price (integer)
    - Implement HasMedia trait from Spatie Media Library
    - Implement relationships: leads(), visits()
    - Create scopes: scopePublished(), scopeAvailable()
    - Create accessor: getFormattedPriceAttribute()
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 17.1, 17.2, 17.3, 17.4, 17.5, 18.1, 18.2, 18.3, 18.4, 18.5_
  

  - [x] 3.3 Create Lead model with status enum

    - Define fillable fields and casts for LeadStatus enum
    - Implement relationships: affiliate(), property()
    - Implement status transition methods: markAsFollowUp(), markAsSurvey(), markAsClosed(), markAsLost()
    - _Requirements: 7.2, 7.3, 7.4, 7.5, 10.1, 10.2, 10.4, 10.5_
  

  - [x] 3.4 Create Visit model with tracking fields

    - Define fillable fields for tracking data
    - Implement relationships: affiliate(), property()
    - _Requirements: 6.2, 6.5_
  

  - [x] 3.5 Create enum classes for status fields

    - Create UserStatus enum (pending, active, blocked)
    - Create LeadStatus enum (new, follow_up, survey, closed, lost)
    - Create PropertyStatus enum (draft, published, sold)
    - _Requirements: 2.1, 7.4, 18.1_

- [x] 4. Setup Filament Shield for role-based access control



  - [x] 4.1 Install and configure Filament Shield


    - Run shield:install command
    - Generate roles and permissions
    - Create Super Admin and Affiliate roles
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [x] 4.2 Configure role permissions and policies


    - Assign property management permissions to Super Admin only
    - Assign user management permissions to Super Admin only
    - Configure affiliate permissions for dashboard and leads access
    - Create policies for Property, User, and Lead models
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_

- [x] 5. Build Property Management module (Admin Panel)




  - [x] 5.1 Create PropertyResource for Filament admin panel


    - Define form schema with text inputs, rich text editor for description
    - Implement JSON repeater for features array
    - Implement key-value repeater for specs JSON
    - Add SpatieMediaLibraryFileUpload for image management
    - Configure table columns: title, price, status with filters
    - Add validation rules: required fields, unique slug, minimum price
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 17.3, 17.4, 18.3_


  
  - [x] 5.2 Implement automatic slug generation

    - Create observer or model event to generate slug from title

    - Ensure slug uniqueness with incremental suffixes if needed
    - _Requirements: 1.5_
  
  - [x] 5.3 Configure image optimization settings


    - Define media collections with responsive image conversions
    - Set up image compression and WebP conversion
    - Configure thumbnail, medium, and large sizes
    - _Requirements: 1.2_

- [x] 6. Implement Affiliate Tracking middleware and logic




  - [x] 6.1 Create AffiliateTrackingMiddleware


    - Check for 'ref' query parameter in request
    - Lookup affiliate by code and set cookie with 30-day expiration
    - Read affiliate_id from existing cookie if no ref parameter
    - Call recordVisit() method to store visit data
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  
  - [x] 6.2 Implement visit recording logic


    - Extract visitor IP, device type, browser from request
    - Create Visit record with affiliate_id and tracking data
    - Handle property_id extraction from URL if on property page
    - _Requirements: 6.2, 6.5_
  
  - [x] 6.3 Register middleware in HTTP kernel


    - Add middleware to web middleware group
    - Ensure middleware runs before route handling
    - _Requirements: 6.1_
  
  - [x] 6.4 Create affiliate code generator utility


    - Generate unique 8-character alphanumeric codes
    - Check for uniqueness in database
    - Assign code to user on registration approval
    - _Requirements: 3.1, 3.2_

- [x] 7. Build User Management module (Admin Panel)




  - [x] 7.1 Create UserResource for Filament admin panel


    - Define form schema with name, email, whatsapp, status fields
    - Add profile photo upload field
    - Configure table with columns: name, email, status, created_at
    - Add filters for status (pending, active, blocked)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 16.1, 16.2, 16.3, 16.4, 16.5_
  
  - [x] 7.2 Implement user approval workflow


    - Create bulk action to approve pending users
    - Trigger affiliate code generation on approval
    - Assign Affiliate role on approval
    - Send welcome email with affiliate code
    - _Requirements: 2.1, 2.2, 2.5_
  
  - [x] 7.3 Implement user blocking functionality


    - Create action to block/unblock users
    - Prevent blocked users from logging in
    - _Requirements: 2.3, 2.4_

- [x] 8. Create public property catalog with Livewire




  - [x] 8.1 Create PropertyCatalog Livewire component


    - Implement search property with Laravel Scout integration
    - Add filter properties: location, price range, category
    - Implement sorting: newest, lowest price, highest price
    - Add pagination with 12 items per page
    - Query only published properties
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [x] 8.2 Design property catalog view template


    - Create responsive grid layout for property cards
    - Display property thumbnail, title, price, location on cards
    - Add filter sidebar with location and price inputs
    - Implement sort dropdown
    - Style with TailwindCSS for mobile and desktop
    - _Requirements: 4.1, 4.5, 20.1, 20.2, 20.3, 20.4, 20.5_
  

  - [x] 8.3 Create PropertyDetail Livewire component

    - Load property by slug with media eager loading
    - Return 404 if property not found or not published
    - Pass property data to view
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_
  
  - [x] 8.4 Design property detail page template


    - Create interactive photo gallery with lightbox
    - Display full description with formatted text
    - Render features as bulleted list from JSON array
    - Render specifications as table from JSON object
    - Embed Google Maps with property location
    - Add contact form section
    - Ensure responsive layout for all screen sizes
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 17.5, 20.1, 20.2, 20.3, 20.4, 20.5_

- [x] 9. Implement Lead Capture and Contact Form





  - [x] 9.1 Create ContactForm Livewire component


    - Add form fields: name (required), whatsapp (required with regex validation)
    - Read affiliate_id from cookie
    - Create Lead record on form submission
    - Dispatch LeadCreated event after lead creation
    - Display success message and reset form
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_
  
  - [x] 9.2 Create LeadCreated event class


    - Accept Lead model in constructor
    - Make event serializable for queue
    - _Requirements: 7.3, 8.1_
  
  - [x] 9.3 Design contact form UI


    - Create modal or inline form on property detail page
    - Style "Hubungi Saya" button prominently
    - Add loading state during submission
    - Display validation errors inline
    - _Requirements: 7.1, 7.2_

- [x] 10. Build WhatsApp notification system



  - [x] 10.1 Create GoWAService class


    - Read API key and URL from config/services.php
    - Implement sendMessage() method with HTTP client
    - Format phone numbers to international format
    - Wrap API calls in try-catch and log errors
    - Return boolean success status
    - _Requirements: 8.2, 8.3, 8.4_
  
  - [x] 10.2 Create SendLeadNotification listener


    - Listen to LeadCreated event
    - Send notification to affiliate if affiliate_id exists
    - Format message: "Halo, ada prospek baru atas nama [Nama] untuk properti [Properti]. Segera follow up!"
    - Send optional confirmation to visitor
    - Handle GoWA API failures gracefully without blocking
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_
  
  - [x] 10.3 Configure GoWA API credentials


    - Add GOWA_API_KEY and GOWA_API_URL to .env.example
    - Add configuration to config/services.php
    - _Requirements: 11.1_
  
  - [x] 10.4 Register event listener in EventServiceProvider


    - Map LeadCreated event to SendLeadNotification listener
    - _Requirements: 8.1_

- [x] 11. Create Affiliate Dashboard with analytics



  - [x] 11.1 Create AnalyticsService class


    - Implement getAffiliateMetrics() method with date range parameters
    - Calculate total visits from visits table
    - Calculate total leads from leads table
    - Calculate conversion rate (leads / visits * 100)
    - Get device breakdown (mobile vs desktop count)
    - Get top performing properties by visit count
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_
  
  - [x] 11.2 Create Filament dashboard widgets for affiliates


    - Create StatsOverviewWidget showing today's clicks, leads, conversion rate
    - Create ChartWidget for monthly performance trends
    - Create TableWidget for top properties
    - Filter all widgets to show only logged-in affiliate's data
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_
  
  - [x] 11.3 Implement date range filter for dashboard


    - Add date range picker to dashboard
    - Update all widgets when date range changes
    - Default to current month
    - _Requirements: 9.5_

- [x] 12. Build Lead Management interface for affiliates



  - [x] 12.1 Create LeadResource for Filament affiliate panel


    - Define table columns: visitor name, whatsapp, property name, status, created_at
    - Add "Click to WA" button that opens WhatsApp Web with pre-filled number
    - Add status dropdown for updating lead status
    - Add notes textarea for tracking conversations
    - Filter to show only leads assigned to logged-in affiliate
    - Add filters for lead status
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_
  

  - [x] 12.2 Implement lead status update functionality

    - Create custom action for status transitions
    - Validate status transitions (e.g., can't go from closed to new)
    - Log status changes with timestamp
    - _Requirements: 10.4_

- [x] 13. Create Link Generator functionality





  - [x] 13.1 Add link generation to affiliate property list


    - Display property catalog in affiliate panel
    - Add "Copy Link Saya" button to each property row
    - Generate URL format: domain.com/p/{slug}?ref={affiliate_code}
    - Copy generated link to clipboard using JavaScript
    - Show success toast notification
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  

  - [x] 13.2 Create referral landing page route

    - Create route: /ref/{affiliate_code}
    - Redirect to property catalog with affiliate tracking
    - Set affiliate cookie before redirect
    - _Requirements: 3.4_

- [x] 14. Implement promotional materials download




  - [x] 14.1 Create promotional package generator


    - Collect property images from media library
    - Generate text file with property description and affiliate link
    - Create social media optimized image with property details
    - Package files into ZIP archive
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_
  

  - [x] 14.2 Add download button to affiliate property list

    - Add "Download Materi Promosi" button to each property
    - Trigger package generation on click
    - Stream ZIP file download to browser
    - _Requirements: 14.1, 14.5_

- [x] 15. Build Super Admin Dashboard with global analytics





  - [x] 15.1 Create global analytics widgets


    - Create StatsOverviewWidget for total traffic, leads, active affiliates
    - Create ChartWidget embedding Google Analytics
    - Create TableWidget for top performing affiliates
    - Create ActivityWidget for recent leads and property views
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_
  
  - [x] 15.2 Configure Google Analytics integration


    - Add GOOGLE_ANALYTICS_ID to .env
    - Embed GA tracking script in layout
    - Create widget to embed GA dashboard charts
    - _Requirements: 12.1, 12.2_

- [x] 16. Implement System Settings management


  - [x] 16.1 Create SettingsResource for Filament admin panel


    - Create form for GoWA API key and URL
    - Add logo upload field
    - Add SEO settings: meta title, description, keywords
    - Store settings in database using spatie/laravel-settings or similar
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [x] 16.2 Apply settings throughout application


    - Use logo in header and footer
    - Apply SEO settings to all pages
    - Use GoWA credentials in notification service
    - _Requirements: 11.1, 11.2, 11.3, 11.5_

- [x] 17. Implement SEO optimization features





  - [x] 17.1 Create SeoService class


    - Implement generateMetaTags() for properties
    - Generate title: "[Property Name] - [Location] | [Site Name]"
    - Generate description from property description (limit 160 chars)
    - Generate keywords from features and location
    - Generate Open Graph tags with property image
    - _Requirements: 15.1, 15.2, 15.3, 15.4_
  
  - [x] 17.2 Apply SEO meta tags to property pages


    - Inject meta tags in property detail page head section
    - Add structured data (JSON-LD) for real estate listing
    - _Requirements: 15.1, 15.2, 15.3, 15.4_
  
  - [x] 17.3 Implement XML sitemap generation


    - Install spatie/laravel-sitemap package
    - Create GenerateSitemap command
    - Include all published property URLs 
    - Include static pages (home, catalog)
    - Schedule command to run daily
    - _Requirements: 15.5_
  


  - [x] 17.4 Optimize HTML structure for SEO





    - Use semantic HTML5 tags (header, nav, main, article, footer)
    - Implement proper heading hierarchy (h1, h2, h3)
    - Add alt text to all images
    - _Requirements: 15.5_

- [x] 18. Create Affiliate Profile Settings




  - [x] 18.1 Create profile settings page in affiliate panel


    - Add form fields: name, whatsapp, profile photo
    - Implement photo upload with preview
    - Add validation for required fields and phone format
    - Save changes to user record
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5_
  
  - [x] 18.2 Display affiliate info on property pages


    - Show affiliate name and photo in property page footer
    - Only display if property was accessed via affiliate link
    - _Requirements: 16.2_

- [x] 19. Configure Laravel Scout for property search



  - [x] 19.1 Setup Scout with database driver


    - Install Laravel Scout package
    - Configure database driver in config/scout.php
    - Add Searchable trait to Property model
    - Define searchable fields: title, location, description
    - _Requirements: 4.4_
  

  - [x] 19.2 Implement search functionality in catalog

    - Use Scout search() method in PropertyCatalog component
    - Highlight search terms in results
    - Handle empty search results gracefully
    - _Requirements: 4.4_

- [x] 20. Setup Laravel Sanctum for API authentication




  - [x] 20.1 Install and configure Sanctum


    - Run sanctum:install command
    - Publish configuration and migrations
    - Add HasApiTokens trait to User model
    - Configure token expiration
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5_
  
  - [x] 20.2 Create API authentication endpoints


    - Create /api/login endpoint returning access token
    - Create /api/logout endpoint revoking token
    - Create /api/user endpoint returning authenticated user
    - Protect routes with sanctum middleware
    - _Requirements: 19.1, 19.2, 19.3_
  

  - [x] 20.3 Document API endpoints

    - Create API documentation file
    - Document authentication flow
    - Document available endpoints and parameters
    - Provide example requests and responses
    - _Requirements: 19.5_

- [x] 21. Implement responsive design and mobile optimization



  - [x] 21.1 Create responsive layouts with TailwindCSS


    - Use responsive utility classes for all components
    - Test layouts at breakpoints: 320px, 768px, 1024px, 1920px
    - Implement mobile-first approach
    - _Requirements: 20.1, 20.2, 20.3, 20.4, 20.5_
  

  - [x] 21.2 Optimize touch interactions for mobile

    - Make buttons and links large enough for touch (min 44px)
    - Implement swipeable image gallery on property detail
    - Add touch-friendly navigation menu
    - _Requirements: 20.2_
  

  - [x] 21.3 Implement hamburger menu for mobile

    - Create collapsible navigation menu for screens < 768px
    - Add smooth animations for menu open/close
    - Ensure menu is accessible with keyboard
    - _Requirements: 20.4_
  
  - [x] 21.4 Optimize images for different screen sizes


    - Use srcset for responsive images
    - Serve WebP format with fallback
    - Lazy load images below the fold
    - _Requirements: 20.5_

- [x] 22. Create database seeders for development


  - [x] 22.1 Create UserSeeder


    - Seed Super Admin user with admin role
    - Seed 5 affiliate users with unique codes
    - Seed 3 pending users for approval testing
    - _Requirements: 2.1, 2.2, 2.5_
  


  - [x] 22.2 Create PropertySeeder

    - Seed 20 properties with varied data
    - Include properties in all statuses (draft, published, sold)
    - Generate realistic features and specs JSON
    - Attach sample images to properties
    - _Requirements: 1.1, 17.1, 17.2, 18.1_


  
  - [x] 22.3 Create LeadSeeder

    - Seed 50 leads distributed across affiliates
    - Include leads in various statuses

    - Associate leads with seeded properties
    - _Requirements: 7.3, 7.4, 10.1_
  
  - [x] 22.4 Create VisitSeeder

    - Seed 500 visits distributed across affiliates
    - Vary device types and timestamps
    - Associate visits with properties
    - _Requirements: 6.2, 9.1_

- [x] 23. Implement error handling and logging



  - [x] 23.1 Configure exception handler


    - Handle ValidationException with user-friendly messages
    - Handle AuthorizationException with 403 responses
    - Handle ModelNotFoundException with 404 pages
    - Log all unexpected errors with context
    - _Requirements: All requirements benefit from proper error handling_
  

  - [x] 23.2 Implement error logging for external APIs

    - Log GoWA API failures with lead context
    - Implement retry mechanism with exponential backoff
    - Send admin notification for repeated failures
    - _Requirements: 8.4_
  
  - [x] 23.3 Create custom error pages


    - Design 404 page with link back to catalog
    - Design 403 page explaining permission denied
    - Design 500 page with support contact info
    - _Requirements: All requirements_

- [ ] 24. Write tests for core functionality

  - [x] 24.1 Write unit tests for models



    - Test Property model relationships and scopes
    - Test User model affiliate code generation
    - Test Lead model status transitions
    - Test Visit model data recording
    - _Requirements: 1.1, 3.1, 7.4, 6.2_
  
  - [x] 24.2 Write feature tests for affiliate tracking








    - Test visit recording with ref parameter
    - Test cookie persistence and reading
    - Test visit attribution accuracy
    - Test handling of invalid affiliate codes
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  
  - [x] 24.3 Write feature tests for lead capture





    - Test lead creation from contact form
    - Test event dispatching on lead creation
    - Test WhatsApp notification sending
    - Test lead assignment to affiliate
    - _Requirements: 7.1, 7.2, 7.3, 8.1, 8.2_
  
  - [ ] 24.4 Write feature tests for authentication and authorization
    - Test user registration and approval flow
    - Test role-based access control
    - Test Super Admin can access all features
    - Test Affiliate can only access own data
    - _Requirements: 2.1, 2.2, 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [ ] 24.5 Write browser tests for public catalog
    - Test property search and filtering
    - Test property detail page rendering
    - Test contact form submission
    - Test responsive layout on mobile
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 5.1, 7.1, 20.1_

- [ ] 25. Performance optimization and caching
  - [ ] 25.1 Implement query optimization
    - Add eager loading to prevent N+1 queries
    - Add database indexes to frequently queried columns
    - Optimize analytics queries with aggregations
    - _Requirements: 9.1, 9.2, 9.3, 12.1_
  
  - [ ] 25.2 Implement caching strategy
    - Cache property listings for 5 minutes
    - Cache affiliate analytics for 15 minutes
    - Cache sitemap for 24 hours
    - Configure Redis for cache storage
    - _Requirements: 4.1, 9.1, 15.5_
  
  - [ ] 25.3 Optimize frontend assets
    - Minify JavaScript and CSS for production
    - Implement lazy loading for images
    - Use CDN for static assets
    - Optimize Livewire component loading
    - _Requirements: 20.1, 20.5_

- [ ] 26. Security hardening
  - [ ] 26.1 Implement input validation and sanitization
    - Validate all form inputs with Laravel Form Requests
    - Sanitize HTML content in property descriptions
    - Validate file uploads (type, size, content)
    - _Requirements: All requirements with user input_
  
  - [ ] 26.2 Configure rate limiting
    - Add rate limiting to contact form (5 submissions per hour)
    - Add rate limiting to API endpoints
    - Add rate limiting to login attempts
    - _Requirements: 7.1, 19.2_
  
  - [ ] 26.3 Implement security headers
    - Configure HTTPS enforcement
    - Add CSP headers
    - Add X-Frame-Options header
    - Configure CORS for API
    - _Requirements: All requirements_

- [ ] 27. Deployment preparation
  - [ ] 27.1 Create production environment configuration
    - Configure production .env.example
    - Set up database connection for production
    - Configure Redis for cache and queue
    - Set up S3 or compatible storage for media
    - _Requirements: All requirements_
  
  - [ ] 27.2 Configure web server
    - Create Nginx configuration file
    - Configure PHP-FPM settings
    - Set up SSL certificate
    - Configure domain and DNS
    - _Requirements: All requirements_
  
  - [ ] 27.3 Setup deployment pipeline
    - Configure CI/CD to run tests
    - Build production assets in pipeline
    - Deploy to staging environment first
    - Run migrations and seeders
    - Deploy to production with zero downtime
    - _Requirements: All requirements_
  
  - [ ] 27.4 Configure monitoring and logging
    - Set up application logging with daily rotation
    - Configure error tracking (Sentry or Bugsnag)
    - Set up uptime monitoring
    - Configure Google Analytics tracking
    - _Requirements: 12.1, 12.2_

- [ ] 28. Documentation and handover
  - [ ] 28.1 Create user documentation
    - Write Super Admin guide for property management
    - Write Affiliate guide for dashboard and lead management
    - Create troubleshooting guide
    - Document common workflows
    - _Requirements: All requirements_
  
  - [ ] 28.2 Create technical documentation
    - Document API endpoints and authentication
    - Document database schema and relationships
    - Document deployment process
    - Document environment configuration
    - _Requirements: All requirements_
  
  - [ ] 28.3 Conduct training session
    - Train Super Admin on system management
    - Train affiliates on dashboard usage
    - Provide Q&A session
    - Record training video for future reference
    - _Requirements: All requirements_

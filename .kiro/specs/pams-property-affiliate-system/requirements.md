# Requirements Document

## Introduction

The Property Affiliate Management System (PAMS) is a web-based property catalog platform designed to empower property owners and administrators to efficiently distribute property information through a network of affiliate agents. The system provides unique tracking links, self-service analytics dashboards, and ready-to-use digital marketing materials for each affiliate. The platform integrates real-time lead notifications via WhatsApp, hybrid analytics (internal database + Google Analytics), and advanced access control using role-based permissions.

## Glossary

- **PAMS**: Property Affiliate Management System - the complete web application
- **Affiliate**: A registered user who promotes properties using unique tracking links
- **Super Admin**: System administrator with full access to all features and configurations
- **Visitor**: Public user browsing the property catalog without authentication
- **Lead**: A prospective buyer who has expressed interest in a property
- **Tracking Link**: A unique URL containing an affiliate code for attribution
- **GoWA API**: WhatsApp gateway service for real-time notifications
- **Property Listing**: A published property available in the catalog
- **Affiliate Code**: Unique identifier assigned to each affiliate for tracking purposes
- **Cookie Tracking**: Browser cookie mechanism to persist affiliate attribution for 30 days
- **Landing Page**: Public-facing property detail page optimized for SEO

## Requirements

### Requirement 1

**User Story:** As a Super Admin, I want to manage property listings with complete CRUD operations, so that I can maintain an up-to-date property catalog for affiliates to promote.

#### Acceptance Criteria

1. THE PAMS SHALL provide a form interface to create new property listings with fields for title, price, location, features, specifications, and status
2. WHEN a Super Admin uploads property images, THE PAMS SHALL store and optimize images using Spatie Media Library
3. THE PAMS SHALL allow Super Admin to edit existing property listings and update any field including media attachments
4. THE PAMS SHALL enable Super Admin to delete property listings and remove all associated media files
5. THE PAMS SHALL generate a unique SEO-friendly slug from the property title for URL creation

### Requirement 2

**User Story:** As a Super Admin, I want to manage affiliate user accounts, so that I can control who can promote properties and maintain platform quality.

#### Acceptance Criteria

1. WHEN a new user registers as an affiliate, THE PAMS SHALL store the registration request in pending status
2. THE PAMS SHALL provide Super Admin with a list of pending affiliate registrations for review
3. THE PAMS SHALL allow Super Admin to approve pending affiliate accounts and activate their access
4. THE PAMS SHALL enable Super Admin to block or deactivate affiliate accounts that violate terms
5. THE PAMS SHALL assign the affiliate role with appropriate permissions when an account is approved

### Requirement 3

**User Story:** As an Affiliate, I want to generate unique tracking links for properties, so that I can promote them and receive attribution for leads generated.

#### Acceptance Criteria

1. WHEN an Affiliate views the property catalog, THE PAMS SHALL display a "Copy Link Saya" button for each property
2. WHEN an Affiliate clicks the copy button, THE PAMS SHALL generate a URL containing their unique affiliate code
3. THE PAMS SHALL format tracking links as "domain.com/p/property-slug?ref={affiliate_code}"
4. THE PAMS SHALL provide an alternative referral format as "domain.com/ref/{affiliate_code}"
5. THE PAMS SHALL copy the generated tracking link to the system clipboard

### Requirement 4

**User Story:** As a Visitor, I want to browse and search the property catalog, so that I can find properties that match my criteria.

#### Acceptance Criteria

1. THE PAMS SHALL display all published properties in a catalog view accessible to unauthenticated visitors
2. THE PAMS SHALL provide filter controls for location, price range, and property category
3. THE PAMS SHALL enable sorting by newest, lowest price, and highest price
4. WHEN a Visitor enters search terms, THE PAMS SHALL use Laravel Scout to return matching properties
5. THE PAMS SHALL display property cards showing thumbnail image, title, price, and location summary

### Requirement 5

**User Story:** As a Visitor, I want to view detailed property information, so that I can evaluate if the property meets my needs.

#### Acceptance Criteria

1. WHEN a Visitor clicks a property card, THE PAMS SHALL navigate to the property detail landing page
2. THE PAMS SHALL display an interactive photo gallery with all property images
3. THE PAMS SHALL render the complete property description with formatted text
4. THE PAMS SHALL display technical specifications from the JSON specs field in a structured format
5. THE PAMS SHALL embed a Google Maps view showing the property location

### Requirement 6

**User Story:** As the System, I want to track visitor activity when they access affiliate links, so that affiliates receive proper attribution for their marketing efforts.

#### Acceptance Criteria

1. WHEN a Visitor accesses a URL containing an affiliate code parameter, THE PAMS SHALL execute tracking middleware
2. THE PAMS SHALL record visit data including affiliate_id, visitor IP address, device type, browser, and URL to the visits table
3. THE PAMS SHALL create a browser cookie storing the affiliate_id with 30-day expiration
4. WHEN a Visitor with an existing affiliate cookie returns without a referral parameter, THE PAMS SHALL attribute the visit to the original affiliate
5. THE PAMS SHALL increment the visit count for the associated affiliate in analytics

### Requirement 7

**User Story:** As a Visitor, I want to submit my contact information for a property, so that I can receive follow-up from an agent.

#### Acceptance Criteria

1. THE PAMS SHALL display a "Hubungi Saya" button or contact form on each property detail page
2. THE PAMS SHALL require Visitor to provide name and WhatsApp number before submission
3. WHEN a Visitor submits the contact form, THE PAMS SHALL create a lead record with visitor details, property_id, and affiliate_id from cookie
4. THE PAMS SHALL set the lead status to "new" upon creation
5. WHEN no affiliate cookie exists, THE PAMS SHALL create the lead record with null affiliate_id

### Requirement 8

**User Story:** As an Affiliate, I want to receive instant WhatsApp notifications when a lead is generated, so that I can follow up quickly with potential buyers.

#### Acceptance Criteria

1. WHEN a lead is created with an associated affiliate_id, THE PAMS SHALL trigger a WhatsApp notification event
2. THE PAMS SHALL send a message to the Affiliate's WhatsApp number via GoWA API containing visitor name and property name
3. THE PAMS SHALL format the notification message as "Halo, ada prospek baru atas nama [Nama Visitor] untuk properti [Nama Properti]. Segera follow up!"
4. WHEN the GoWA API request fails, THE PAMS SHALL log the error and continue processing without blocking lead creation
5. THE PAMS SHALL send an optional confirmation message to the Visitor's WhatsApp number acknowledging their inquiry

### Requirement 9

**User Story:** As an Affiliate, I want to view my performance dashboard, so that I can monitor my traffic and lead generation metrics.

#### Acceptance Criteria

1. THE PAMS SHALL display a dashboard showing total clicks, leads, and conversion rate for the current day
2. THE PAMS SHALL render a monthly performance chart visualizing traffic and lead trends
3. THE PAMS SHALL calculate and display total commission earned if commission tracking is enabled
4. THE PAMS SHALL show a breakdown of visits by device type (mobile vs desktop)
5. THE PAMS SHALL provide date range filters to view historical performance data

### Requirement 10

**User Story:** As an Affiliate, I want to manage my leads, so that I can track follow-up progress and close deals.

#### Acceptance Criteria

1. THE PAMS SHALL display a table listing all leads assigned to the logged-in Affiliate
2. THE PAMS SHALL show lead details including visitor name, WhatsApp number, property name, status, and submission date
3. THE PAMS SHALL provide a "Click to WA" button that opens WhatsApp Web with the visitor's number pre-filled
4. THE PAMS SHALL allow Affiliate to update lead status through dropdown selection with options: new, follow_up, survey, closed, lost
5. THE PAMS SHALL enable Affiliate to add notes to each lead record for tracking conversation history

### Requirement 11

**User Story:** As a Super Admin, I want to configure system-wide settings, so that I can customize the platform for my organization.

#### Acceptance Criteria

1. THE PAMS SHALL provide a settings interface for Super Admin to input GoWA API key and endpoint URL
2. THE PAMS SHALL allow Super Admin to upload and update the website logo
3. THE PAMS SHALL enable Super Admin to configure global SEO settings including meta title, description, and keywords
4. THE PAMS SHALL store configuration values in the database or environment file
5. WHEN configuration is updated, THE PAMS SHALL apply changes immediately without requiring application restart

### Requirement 12

**User Story:** As a Super Admin, I want to view global analytics, so that I can understand overall platform performance.

#### Acceptance Criteria

1. THE PAMS SHALL display a Super Admin dashboard with total traffic across all affiliates
2. THE PAMS SHALL embed Google Analytics charts showing visitor trends and behavior
3. THE PAMS SHALL show aggregate metrics including total leads, conversion rate, and active affiliates
4. THE PAMS SHALL provide a breakdown of top-performing affiliates by lead count
5. THE PAMS SHALL display recent activity feed showing latest leads and property views

### Requirement 13

**User Story:** As a System, I want to implement role-based access control, so that users only access features appropriate to their role.

#### Acceptance Criteria

1. THE PAMS SHALL use Filament Shield to define roles: Super Admin and Affiliate
2. THE PAMS SHALL restrict property management features to Super Admin role only
3. THE PAMS SHALL restrict user management features to Super Admin role only
4. THE PAMS SHALL allow Affiliate role to access only their own dashboard, leads, and profile settings
5. WHEN a user attempts to access unauthorized resources, THE PAMS SHALL return a 403 forbidden response

### Requirement 14

**User Story:** As an Affiliate, I want to download promotional materials, so that I can effectively market properties on social media.

#### Acceptance Criteria

1. THE PAMS SHALL provide a "Download Materi Promosi" button on each property in the affiliate catalog
2. WHEN an Affiliate clicks the download button, THE PAMS SHALL generate a promotional package containing property images and description
3. THE PAMS SHALL format promotional materials optimized for social media platforms
4. THE PAMS SHALL include the Affiliate's tracking link in the promotional materials
5. THE PAMS SHALL deliver the promotional package as a downloadable ZIP file

### Requirement 15

**User Story:** As a Visitor, I want property pages to be SEO-optimized, so that I can discover properties through search engines.

#### Acceptance Criteria

1. THE PAMS SHALL generate unique meta titles for each property page including property name and location
2. THE PAMS SHALL create meta descriptions summarizing property features and price
3. THE PAMS SHALL implement Open Graph tags for social media sharing with property image and details
4. THE PAMS SHALL generate an XML sitemap including all published property URLs using Spatie Sitemap package
5. THE PAMS SHALL use semantic HTML structure with proper heading hierarchy for search engine crawlers

### Requirement 16

**User Story:** As an Affiliate, I want to update my profile information, so that my details appear correctly on property pages and communications.

#### Acceptance Criteria

1. THE PAMS SHALL provide a profile settings page for Affiliate users
2. THE PAMS SHALL allow Affiliate to upload and update their profile photo
3. THE PAMS SHALL enable Affiliate to edit their display name shown in property page footers
4. THE PAMS SHALL allow Affiliate to update their WhatsApp number for receiving lead notifications
5. WHEN profile is updated, THE PAMS SHALL validate required fields and save changes to the database

### Requirement 17

**User Story:** As a System, I want to store property features and specifications in flexible JSON format, so that different property types can have varying attributes.

#### Acceptance Criteria

1. THE PAMS SHALL store property features as a JSON array in the features column
2. THE PAMS SHALL store property specifications as a JSON object with key-value pairs in the specs column
3. WHEN Super Admin creates a property, THE PAMS SHALL provide a dynamic form to add multiple features
4. WHEN Super Admin creates a property, THE PAMS SHALL provide a key-value input interface for specifications
5. THE PAMS SHALL render features as a bulleted list and specifications as a table on the property detail page

### Requirement 18

**User Story:** As a Super Admin, I want to manage property status, so that I can control which properties are visible to the public.

#### Acceptance Criteria

1. THE PAMS SHALL support property status values: draft, published, and sold
2. THE PAMS SHALL display only properties with published status in the public catalog
3. THE PAMS SHALL allow Super Admin to change property status through a dropdown in the admin panel
4. WHEN a property status is changed to sold, THE PAMS SHALL display a "SOLD" badge on the property card
5. THE PAMS SHALL exclude draft and sold properties from affiliate link generation

### Requirement 19

**User Story:** As a System, I want to prepare API authentication infrastructure, so that future mobile applications can integrate securely.

#### Acceptance Criteria

1. THE PAMS SHALL install and configure Laravel Sanctum for API token authentication
2. THE PAMS SHALL provide API endpoints for user authentication returning access tokens
3. THE PAMS SHALL protect API routes using Sanctum middleware requiring valid tokens
4. THE PAMS SHALL implement token expiration and refresh mechanisms
5. THE PAMS SHALL document API endpoints and authentication flow for future development

### Requirement 20

**User Story:** As a Visitor, I want the website to be responsive, so that I can browse properties comfortably on any device.

#### Acceptance Criteria

1. THE PAMS SHALL render all public pages with responsive layouts adapting to screen sizes from 320px to 1920px width
2. THE PAMS SHALL optimize touch interactions for mobile devices including swipeable image galleries
3. THE PAMS SHALL ensure text remains readable without horizontal scrolling on mobile devices
4. THE PAMS SHALL adapt navigation menus to hamburger-style on screens smaller than 768px width
5. THE PAMS SHALL load appropriately sized images based on device screen resolution

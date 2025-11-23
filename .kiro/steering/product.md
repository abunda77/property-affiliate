# Product Overview

PAMS (Property Affiliate Management System) is a web-based property catalog platform with affiliate tracking capabilities. The system enables property owners/admins to distribute property listings through a network of affiliates, each equipped with unique tracking links and performance analytics.

## Core Purpose

Enable efficient property marketing through an affiliate network where:
- Affiliates promote properties using unique tracking URLs
- Visitors are tracked via cookies (30-day retention)
- Leads are captured and attributed to affiliates
- Real-time WhatsApp notifications are sent via GoWA API integration

## Key Features

- **Dynamic Landing Pages**: SEO-optimized property listings with responsive design
- **Affiliate Tracking**: Cookie-based visitor tracking with unique referral codes
- **Lead Management**: Capture and manage property inquiries with affiliate attribution
- **Real-time Notifications**: WhatsApp integration for instant lead alerts
- **Hybrid Analytics**: Internal database tracking + Google Analytics embedding
- **Role-Based Access**: Super Admin (full control) and Affiliate (own data only) roles
- **Smart Search**: Laravel Scout implementation for fast property search
- **Media Management**: Optimized image handling with multiple conversions (webp, jpg)

## User Roles

### Super Admin
- Full property CRUD operations
- User management (approve/block affiliates)
- Access to all leads and analytics
- System configuration (GoWA API, SEO settings)

### Affiliate
- Personal dashboard with performance metrics
- View and manage own leads
- Generate unique tracking links
- Download promotional materials
- Update own profile

### Public Visitor
- Browse property catalog with filters
- View property details with galleries
- Submit contact forms (tracked to affiliate)

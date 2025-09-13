# Our Restaurant Management System - Complete Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [System Architecture](#system-architecture)
3. [Functional Requirements](#functional-requirements)
4. [Non-Functional Requirements](#non-functional-requirements)
5. [User Roles & Permissions](#user-roles--permissions)
6. [Database Schema](#database-schema)
7. [API Endpoints](#api-endpoints)
8. [Security Features](#security-features)
9. [Deployment Information](#deployment-information)
10. [Technology Stack](#technology-stack)

---

## System Overview

**Our Restaurant** is a comprehensive web-based restaurant management system designed for university environments. The system facilitates online food ordering, delivery management, and restaurant operations through a modern, responsive web application.

### Key Features
- **Multi-role User Management**: Customer, Staff, Admin, and Super Admin roles
- **Order Management**: Complete order lifecycle from placement to delivery
- **Menu Management**: Dynamic meal and category management
- **Rating & Review System**: Customer feedback and staff responses
- **Alert System**: Issue reporting and resolution tracking
- **Support Ticket System**: Customer service and technical support
- **Real-time Notifications**: Email notifications for all major events
- **Google OAuth Integration**: Social login capabilities
- **Responsive Design**: Mobile-first approach with Tailwind CSS

---

## System Architecture

### Backend Architecture
- **Framework**: Laravel 12 (PHP 8.3+)
- **Database**: PostgreSQL (Production) / SQLite (Development)
- **Authentication**: Laravel Breeze with custom OTP verification
- **API**: RESTful API with Inertia.js for SPA-like experience
- **File Storage**: Laravel Storage with public disk for images

### Frontend Architecture
- **Framework**: Vue.js 3 with Composition API
- **Routing**: Inertia.js for seamless SPA navigation
- **Styling**: Tailwind CSS with custom components
- **Icons**: Font Awesome for professional iconography
- **State Management**: Vue.js reactive state with Inertia.js

### Deployment Architecture
- **Platform**: Render.com (Cloud hosting)
- **Container**: Docker with custom start.sh script
- **Database**: PostgreSQL (Render managed)
- **CDN**: Render's built-in CDN for static assets
- **SSL**: Automatic HTTPS with Let's Encrypt

---

## Functional Requirements

### 1. User Management
#### 1.1 User Registration & Authentication
- **Email/Password Registration**: Standard registration with email verification
- **Google OAuth**: Social login with profile completion
- **Email Verification**: OTP-based email verification (6-digit code)
- **Password Reset**: OTP-based password reset system
- **Account Locking**: Automatic account lock after 3 failed login attempts
- **Profile Completion**: Mandatory profile completion for Google users

#### 1.2 User Roles
- **Customer**: Order placement, rating, support tickets
- **Staff**: Order management, meal availability, alert handling
- **Admin**: Full restaurant management, user management, analytics
- **Super Admin**: System administration, admin management, system configuration

### 2. Menu Management
#### 2.1 Meal Categories
- **CRUD Operations**: Create, read, update, delete categories
- **Soft Delete**: Categories can be restored after deletion
- **Category Ordering**: Custom ordering for menu display

#### 2.2 Meals
- **CRUD Operations**: Complete meal management
- **Availability Toggle**: Real-time availability management
- **Image Upload**: Meal image management
- **Pricing**: Decimal pricing with currency formatting
- **Soft Delete**: Meals can be restored after deletion

### 3. Order Management
#### 3.1 Order Placement
- **Cart System**: Add/remove items with quantity management
- **Checkout Process**: Delivery location, payment method selection
- **Order Validation**: Stock availability and pricing validation
- **Order Confirmation**: Email notification with order details

#### 3.2 Order Processing
- **Status Management**: pending → confirmed → preparing → ready → delivered
- **Staff Assignment**: Orders can be assigned to specific staff
- **Order Tracking**: Real-time status updates
- **Cancellation System**: Customer-initiated cancellations with admin approval

### 4. Rating & Review System
#### 4.1 Customer Ratings
- **Star Rating**: 1-5 star rating system
- **Comment System**: Optional text comments
- **Order-based Rating**: Ratings linked to specific orders
- **Rating Validation**: Only delivered orders can be rated

#### 4.2 Staff Responses
- **Response Management**: Staff can respond to ratings
- **Notification System**: Email notifications for new ratings
- **Response Tracking**: Admin can view all rating responses

### 5. Alert System
#### 5.1 Issue Reporting
- **Alert Categories**: Food quality, delivery issues, service problems
- **Priority Levels**: High, Medium, Low priority alerts
- **Order Linking**: Alerts can be linked to specific orders
- **Status Tracking**: Open, In Progress, Resolved statuses

#### 5.2 Alert Management
- **Staff Assignment**: Alerts can be assigned to staff members
- **Resolution Tracking**: Detailed resolution notes
- **Notification System**: Email notifications for alert updates

### 6. Support Ticket System
#### 6.1 Ticket Submission
- **Guest Support**: Non-registered users can submit tickets
- **Registered User Support**: Enhanced support for registered users
- **Ticket Categories**: General, Technical, Billing, Feedback
- **File Attachments**: Support for image attachments

#### 6.2 Ticket Management
- **Admin Response**: Admin can respond to tickets
- **Status Management**: Open, In Progress, Closed statuses
- **Email Notifications**: Automated email responses
- **Ticket History**: Complete conversation history

### 7. Notification System
#### 7.1 Email Notifications
- **Order Notifications**: Order confirmation, status updates
- **Rating Notifications**: New rating alerts for staff
- **Alert Notifications**: Alert creation and resolution updates
- **Support Notifications**: Ticket submission and response notifications
- **System Notifications**: Account verification, password reset

#### 7.2 Email Templates
- **Professional Design**: Consistent branding with "Our Restaurant"
- **Font Awesome Icons**: Professional iconography
- **Responsive Layout**: Mobile-friendly email templates
- **Brand Colors**: Green theme (#16a34a) throughout

---

## Non-Functional Requirements

### 1. Performance Requirements
- **Page Load Time**: < 3 seconds for all pages
- **Database Queries**: Optimized with eager loading and indexes
- **Image Optimization**: Compressed images with lazy loading
- **Caching**: Laravel caching for frequently accessed data
- **CDN**: Static asset delivery through Render CDN

### 2. Security Requirements
- **Authentication**: Laravel's built-in authentication system
- **Authorization**: Role-based access control with policies
- **Password Security**: Strong password requirements (8+ chars, mixed case, numbers, symbols)
- **Account Locking**: Automatic lockout after failed attempts
- **OTP Security**: Time-limited OTP codes (10-15 minutes)
- **CSRF Protection**: Laravel's CSRF token system
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Input sanitization and output escaping

### 3. Scalability Requirements
- **Database Scaling**: PostgreSQL with proper indexing
- **Horizontal Scaling**: Stateless application design
- **Load Balancing**: Render's built-in load balancing
- **Resource Optimization**: Efficient memory and CPU usage

### 4. Reliability Requirements
- **Uptime**: 99.9% uptime target
- **Error Handling**: Comprehensive error handling and logging
- **Data Backup**: Automated database backups
- **Recovery**: Point-in-time recovery capabilities
- **Monitoring**: Application performance monitoring

### 5. Usability Requirements
- **Responsive Design**: Mobile-first responsive design
- **Accessibility**: WCAG 2.1 AA compliance
- **User Experience**: Intuitive navigation and workflows
- **Loading States**: Visual feedback for all operations
- **Error Messages**: Clear, actionable error messages

### 6. Compatibility Requirements
- **Browser Support**: Chrome, Firefox, Safari, Edge (latest 2 versions)
- **Mobile Support**: iOS Safari, Chrome Mobile, Samsung Internet
- **Email Client Support**: Gmail, Outlook, Apple Mail, Yahoo Mail
- **Screen Sizes**: 320px to 1920px width support

---

## User Roles & Permissions

### 1. Customer Role
#### Permissions
- View public menu
- Place orders
- View own order history
- Rate orders and meals
- Submit support tickets
- Create alerts for order issues
- Update profile information
- Cancel orders (with approval)

#### Restrictions
- Cannot access admin functions
- Cannot manage meals or categories
- Cannot view other users' orders
- Cannot access staff functions

### 2. Staff Role
#### Permissions
- View assigned orders
- Update order status
- Toggle meal availability
- Respond to ratings
- Handle assigned alerts
- View order details
- Submit support tickets
- Update own profile

#### Restrictions
- Cannot create/delete meals
- Cannot manage categories
- Cannot access admin functions
- Cannot view all orders (only assigned)

### 3. Admin Role
#### Permissions
- Full meal management (CRUD)
- Full category management (CRUD)
- View all orders
- Manage order statuses
- Assign orders to staff
- Handle all alerts
- Manage support tickets
- View all ratings and responses
- Manage staff accounts
- View analytics and reports

#### Restrictions
- Cannot manage super admin accounts
- Cannot access system-level functions
- Cannot modify core system settings

### 4. Super Admin Role
#### Permissions
- **Admin Management**: Create, edit, delete admin users
- **User Role Management**: Change user roles (customer, staff, admin)
- **System Configuration**: View system configuration and settings
- **Advanced Analytics**: Access comprehensive system analytics
- **System Logs**: View and manage system logs
- **System Monitoring**: Monitor system health and performance
- **All Admin Permissions**: Full access to all admin functions

#### Restrictions
- Cannot modify other super admin accounts
- Cannot change own role
- Cannot access production database directly

## Database Schema

### Core Tables

#### Users Table
```sql
- id (Primary Key)
- name (String)
- email (String, Unique)
- google_id (String, Nullable, Unique)
- email_verified_at (Timestamp, Nullable)
- password (String)
- phone (String, Nullable, Unique)
- permanent_location (String, Nullable)
- role (Enum: customer, staff, admin, super_admin)
- failed_login_attempts (Integer, Default: 0)
- last_failed_attempt (Timestamp, Nullable)
- locked_at (Timestamp, Nullable)
- remember_token (String, Nullable)
- deleted_at (Timestamp, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Meal Categories Table
```sql
- id (Primary Key)
- name (String)
- description (Text, Nullable)
- is_active (Boolean, Default: true)
- sort_order (Integer, Default: 0)
- deleted_at (Timestamp, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Meals Table
```sql
- id (Primary Key)
- name (String)
- price (Decimal: 8,2)
- category_id (Foreign Key)
- description (Text, Nullable)
- image_url (String, Nullable)
- is_available (Boolean, Default: true)
- deleted_at (Timestamp, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Orders Table
```sql
- id (Primary Key)
- user_id (Foreign Key)
- staff_id (Foreign Key, Nullable)
- total_price (Decimal: 8,2)
- status (Enum: pending, confirmed, preparing, ready, delivered, cancelled)
- payment_method (Enum: cash, mobile_money, card)
- delivery_location (String)
- staff_notes (Text, Nullable)
- deleted_at (Timestamp, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Order Items Table
```sql
- id (Primary Key)
- order_id (Foreign Key)
- meal_id (Foreign Key)
- quantity (Integer)
- price (Decimal: 8,2)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Ratings Table
```sql
- id (Primary Key)
- user_id (Foreign Key)
- order_id (Foreign Key)
- meal_id (Foreign Key)
- rating (Integer: 1-5)
- comment (Text, Nullable)
- staff_response (Text, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Alerts Table
```sql
- id (Primary Key)
- user_id (Foreign Key)
- order_id (Foreign Key, Nullable)
- title (String)
- description (Text)
- priority (Enum: low, medium, high)
- status (Enum: open, in_progress, resolved)
- assigned_to (Foreign Key, Nullable)
- resolution_notes (Text, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### Support Tickets Table
```sql
- id (Primary Key)
- user_id (Foreign Key, Nullable)
- name (String)
- email (String)
- subject (String)
- message (Text)
- is_registered (Boolean, Default: false)
- status (Enum: open, in_progress, closed)
- admin_response (Text, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### OTP Verifications Table
```sql
- id (Primary Key)
- user_id (Foreign Key)
- type (Enum: email_verification, password_reset)
- otp (String)
- expires_at (Timestamp)
- is_used (Boolean, Default: false)
- created_at (Timestamp)
- updated_at (Timestamp)
```

---

## API Endpoints

### Authentication Endpoints
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout
- `GET /auth/google/redirect` - Google OAuth redirect
- `GET /auth/google/callback` - Google OAuth callback
- `POST /complete-profile` - Complete profile after Google login
- `POST /forgot-password` - Request password reset
- `POST /verify-password-otp` - Verify password reset OTP
- `POST /reset-password` - Reset password with OTP

### Menu Endpoints
- `GET /menu` - Public menu view
- `GET /admin/meals` - Admin meal management
- `POST /admin/meals` - Create new meal
- `PUT /admin/meals/{id}` - Update meal
- `DELETE /admin/meals/{id}` - Delete meal
- `POST /admin/meals/{id}/toggle` - Toggle meal availability

### Order Endpoints
- `GET /cart` - View cart
- `GET /checkout` - Checkout page
- `POST /checkout` - Place order
- `GET /my-orders` - Customer order history
- `POST /orders/{id}/cancel` - Cancel order
- `GET /admin/orders` - Admin order management
- `PUT /admin/orders/{id}` - Update order status

### Rating Endpoints
- `POST /ratings` - Submit rating
- `GET /admin/ratings` - View all ratings
- `PUT /admin/ratings/{id}` - Respond to rating

### Alert Endpoints
- `POST /alerts` - Create alert
- `GET /admin/alerts` - View all alerts
- `PUT /admin/alerts/{id}` - Update alert status

### Support Endpoints
- `POST /support-tickets` - Submit support ticket
- `GET /admin/support-tickets` - View all tickets
- `PUT /admin/support-tickets/{id}` - Respond to ticket

---

## Security Features

### 1. Authentication Security
- **Strong Password Requirements**: Minimum 8 characters with mixed case, numbers, and symbols
- **Account Lockout**: Automatic lockout after 3 failed login attempts
- **OTP Verification**: Time-limited OTP codes for email verification and password reset
- **Google OAuth**: Secure social login with profile completion requirement
- **Session Management**: Secure session handling with Laravel's built-in system

### 2. Authorization Security
- **Role-Based Access Control**: Granular permissions based on user roles
- **Policy-Based Authorization**: Laravel policies for model-level permissions
- **Middleware Protection**: Route-level access control
- **Profile Completion**: Mandatory profile completion for Google users

### 3. Data Security
- **Input Validation**: Comprehensive validation on all user inputs
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Laravel's CSRF token system
- **File Upload Security**: Validated file uploads with type restrictions

### 4. Communication Security
- **HTTPS**: All communications encrypted with SSL/TLS
- **Email Security**: Secure email templates with professional branding
- **OTP Security**: Time-limited, single-use OTP codes
- **Secure Headers**: Security headers for XSS and clickjacking protection

---

## Deployment Information

### Production Environment
- **Platform**: Render.com
- **URL**: https://the-restaurant-b5fz.onrender.com
- **Database**: PostgreSQL (Render managed)
- **Storage**: Render's persistent disk
- **SSL**: Automatic HTTPS with Let's Encrypt

### Deployment Process
1. **Git Integration**: Automatic deployment from GitHub
2. **Build Process**: Docker container build with start.sh script
3. **Database Migration**: Automatic migration on first deployment
4. **Asset Compilation**: Vite build process for frontend assets
5. **Environment Configuration**: Secure environment variable management

### Environment Variables
- `APP_URL`: Application URL
- `APP_ENV`: Environment (production/development)
- `DB_CONNECTION`: Database connection type
- `DB_HOST`: Database host
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password
- `GOOGLE_CLIENT_ID`: Google OAuth client ID
- `GOOGLE_CLIENT_SECRET`: Google OAuth client secret
- `GOOGLE_REDIRECT_URI`: Google OAuth redirect URI
- `MAIL_MAILER`: Email service provider
- `MAIL_HOST`: Email server host
- `MAIL_PORT`: Email server port
- `MAIL_USERNAME`: Email username
- `MAIL_PASSWORD`: Email password

---

## Technology Stack

### Backend Technologies
- **PHP 8.3+**: Modern PHP with latest features
- **Laravel 12**: Latest Laravel framework
- **PostgreSQL**: Robust relational database
- **Laravel Breeze**: Authentication scaffolding
- **Laravel Socialite**: OAuth integration
- **Laravel Notifications**: Email notification system

### Frontend Technologies
- **Vue.js 3**: Modern JavaScript framework
- **Inertia.js**: SPA-like experience without API
- **Tailwind CSS**: Utility-first CSS framework
- **Font Awesome**: Professional icon library
- **Vite**: Modern build tool

### Development Tools
- **Composer**: PHP dependency management
- **NPM**: Node.js package management
- **Git**: Version control
- **Docker**: Containerization
- **Laravel Mix**: Asset compilation

### Production Tools
- **Render.com**: Cloud hosting platform
- **PostgreSQL**: Production database
- **Let's Encrypt**: SSL certificate management
- **GitHub**: Code repository and CI/CD

---

## System Monitoring & Maintenance

### Performance Monitoring
- **Application Performance**: Render's built-in monitoring
- **Database Performance**: Query optimization and indexing
- **Error Tracking**: Laravel's error logging system
- **Uptime Monitoring**: Render's uptime monitoring

### Maintenance Tasks
- **Database Backups**: Automated daily backups
- **Log Rotation**: Automatic log file rotation
- **Security Updates**: Regular dependency updates
- **Performance Optimization**: Continuous performance monitoring

### Backup & Recovery
- **Database Backups**: Daily automated backups
- **Point-in-Time Recovery**: PostgreSQL's WAL-based recovery
- **Code Backups**: Git repository with multiple remotes
- **Configuration Backups**: Environment variable backups

---

This documentation provides a comprehensive overview of the Our Restaurant Management System, covering all aspects from architecture to deployment. The system is designed to be scalable, secure, and user-friendly, providing a complete solution for restaurant management in university environments.

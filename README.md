# KanunKuh

A Laravel 9 platform for managing programs, courses, registrations, payments, and member services with OTP authentication, administrative workflows, and user dashboards. The application targets Persian-language users with localized content and Jalali date handling.

## Highlights

- OTP-based authentication (SMS verification)
- Program and course catalogs with registration flows
- Membership approvals and payment request tracking
- Admin and user dashboards with role-based access
- Ticketing system with attachments and status workflow
- Notification system (site + SMS templates)
- Program reports with PDF export
- Excel exports for key admin datasets
- Blog and public content pages
- Sitemap generation

## Tech Stack

- **Backend:** Laravel 9, PHP ^8.0.2
- **Frontend:** Vite, Bootstrap 5, Sass, Axios
- **Database:** MySQL (default)
- **Key Packages:** Sanctum, DOMPDF, Maatwebsite Excel, Jalali (Morilog), Arcaptcha, Spatie Sitemap, SMS.ir SDK

## Core Domains

- Users, Profiles, Roles, Memberships
- Programs, Courses, Prerequisites, Files
- Registrations (program/course)
- Payments and approvals
- Tickets, Messages, Attachments
- Notifications and Templates
- Reports and Exports

## Getting Started

### Prerequisites

- PHP 8.0.2+
- Composer
- Node.js 16+ and npm
- MySQL

### Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Configure Environment

Populate the required variables in `.env` (see the list below), then run:

```bash
php artisan migrate
php artisan storage:link
npm run dev
php artisan serve
```

## Environment Variables

Minimum required:

```ini
APP_NAME=kanunkuh
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

SMSIR_API_KEY=your_smsir_api_key
ARCAPTCHA_SITE_KEY=your_arcaptcha_site_key
ARCAPTCHA_SECRET_KEY=your_arcaptcha_secret_key
```

Common optional variables:

```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="kanunkuh"
```

## Useful Commands

```bash
# Frontend assets
npm run dev
npm run build

# Backend
php artisan migrate
php artisan serve
```

## Folder Structure

```text
app/                Application logic (controllers, models, services)
config/             Laravel and package configuration
database/           Migrations, factories, seeders
resources/views/    Blade templates
routes/             Web and API routes
public/             Public assets
```

## Access Control

Role-based access is enforced through middleware:

- Public routes for browsing programs/courses, blog, and contact
- Authenticated user dashboard (profile, registrations, payments, tickets)
- Admin panel for approvals, content management, and reporting

## Localization

The app includes Persian language resources and Jalali date formatting for user-facing views.

## Notes

- SMS sending is disabled in the local environment by design.
- Payment flows are request-based with admin approval.


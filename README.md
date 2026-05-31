# CopyUS Shop

A B2B e-commerce platform for print-on-demand and custom product ordering, targeting European (primarily Catalan-speaking) markets. Built with **Laravel 13**, **TailwindCSS 4**, and **Alpine.js**.

---

## Features

- **Product Catalogue** — variants, bulk pricing tiers, reviews, wishlist
- **Stock States** — In Stock and Pre-order (24-48 h delivery) per product
- **Print-on-Demand** — dynamic print builder with customisable options, artwork upload, and volume pricing
- **B2B Company Accounts** — multi-user companies with role-based access and admin approval workflow
- **Email Verification** — combined welcome + verify email on registration; approval/rejection emails on admin action
- **Quotation System** — request and receive itemised custom quotes
- **Multi-Language** — Catalan, Spanish, and English (via Spatie Translatable)
- **AI Content Generation** — auto-generate product descriptions and SEO via Google Gemini or Anthropic Claude
- **Homepage Hero Slider** — dynamic image slider with per-locale text, managed from admin panel
- **Newsletter Subscriptions** — subscription form with deduplication, admin list, and CSV export
- **Welcome Popup** — highlights Digital Printing and Papeleria services on first visit
- **Admin Panel** — full CRUD, user approval, order management, reporting, PDF invoices, Excel exports
- **Support Tickets** — integrated customer support ticketing

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.3+) |
| Frontend | Blade, Alpine.js 3, TailwindCSS 4, Vite 8 |
| Database | SQLite (dev) / MySQL (prod) |
| Auth | Laravel Breeze (extended) + MustVerifyEmail |
| Media | Spatie Laravel MediaLibrary |
| i18n | Spatie Laravel Translatable |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| AI | Google Gemini 2.0 Flash / Anthropic Claude Haiku |

---

## Quick Start

```bash
git clone https://github.com/armannadim2/copyus_shop.git
cd copyus_shop

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install && npm run build
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000).

For full setup instructions see [docs/SETUP.md](docs/SETUP.md).

---

## Documentation

| Document | Description |
|---|---|
| [docs/SETUP.md](docs/SETUP.md) | Developer setup, environment variables, deployment |
| [docs/USER_MANUAL.md](docs/USER_MANUAL.md) | End-user guide for all features |
| [CLAUDE.md](CLAUDE.md) | AI coding agent context (Claude Code / Cursor) |

---

## User Roles

| Role | Access |
|---|---|
| Guest | Browse products, request quotes, contact, subscribe to newsletter |
| Registered (unverified) | Must verify email before account review begins |
| B2B (pending) | Email verified, waiting for admin approval |
| B2B (approved) | Full B2B features: company management, exclusive pricing, quotations |
| Admin | Full admin panel access |

---

## Project Structure

```
app/
  Http/Controllers/
    Admin/        # Admin panel (incl. hero slides, newsletter)
    Auth/         # Authentication, verification & B2B approval
    Shop/         # Storefront
  Models/         # 55+ Eloquent models
  Notifications/  # 17+ notification classes
  Services/       # PrintPriceCalculator, etc.
resources/
  lang/ca/ es/ en/  # app, validation, auth, passwords, pagination
  views/
    admin/        # Admin Blade views
    shop/         # Storefront Blade views
    auth/         # Auth + email verification views
    layouts/      # Shared layouts
routes/
  web.php         # All application routes
database/
  migrations/     # 75+ migrations
```

---

## Environment

Key variables in `.env`:

```env
APP_LOCALE=ca               # ca | es | en
SHOW_PRICES=true            # Hide prices from non-B2B guests
AI_PROVIDER=gemini          # gemini | anthropic
GEMINI_API_KEY=...
ANTHROPIC_API_KEY=...
MAIL_MAILER=smtp            # Required for verification & notification emails
MAIL_FROM_ADDRESS="noreply@copyus.es"
```

---

## License

Private — all rights reserved. Not open for public contribution without explicit permission.

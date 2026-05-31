# CLAUDE.md — CopyUS Shop

This file gives Claude Code the context it needs to work effectively in this codebase.

## Project Overview

**CopyUS Shop** is a B2B e-commerce platform for a print-on-demand and custom product ordering service, targeting European (primarily Catalan-speaking) markets. It is a monolithic Laravel 13 application with a Blade/Alpine.js/TailwindCSS frontend.

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.3+) |
| Frontend | Blade templates, Alpine.js 3, TailwindCSS 4, Vite 8 |
| Database | SQLite (dev) / MySQL (prod) — Eloquent ORM |
| Auth | Laravel Breeze (custom-extended) + `MustVerifyEmail` |
| Media | Spatie Laravel MediaLibrary |
| i18n | Spatie Laravel Translatable |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| AI | Google Gemini 2.0 Flash or Anthropic Claude Haiku (configurable via `AI_PROVIDER`) |
| Queue | Database driver |
| Sessions | Database driver |

## Key Commands

```bash
# Development
php artisan serve           # Start PHP dev server
npm run dev                 # Start Vite dev server (run alongside artisan serve)

# Database
php artisan migrate         # Run pending migrations
php artisan migrate:fresh --seed  # Wipe and reseed database
php artisan db:seed         # Run all seeders

# Build
npm run build               # Build frontend assets for production

# Code quality
./vendor/bin/pint           # Fix PHP code style (Laravel Pint)
php artisan test            # Run PHPUnit test suite

# Utilities
php artisan tinker          # Interactive REPL
php artisan queue:work      # Process queued jobs (required for email notifications)
php artisan storage:link    # Create public disk symlink (required for image uploads)
```

## Project Structure

```
app/
  Console/Commands/         # Artisan scheduled commands (abandoned-cart emails)
  Http/
    Controllers/
      Admin/                # Admin panel controllers (prefix: /admin)
      Auth/                 # Auth controllers (login, register, verify, approval)
      Shop/                 # Storefront controllers
    Middleware/             # AdminMiddleware, SetLocale, EnsureB2BApproved
  Models/                   # 55+ Eloquent models
  Notifications/            # 17+ notification classes
  Mail/                     # Mailable classes
  Services/                 # PrintPriceCalculator, etc.
  Rules/                    # Custom validation (FiscalIdentity)
  Exports/                  # Excel exports (OrdersExport)
config/
  shop.php                  # Custom config: SHOW_PRICES flag
database/
  migrations/               # 75+ migrations
  seeders/
resources/
  lang/
    ca/ es/ en/             # 5 lang files each: app, validation, auth, passwords, pagination
  views/
    admin/                  # Admin panel Blade views (incl. hero-slides/, newsletter/)
    shop/                   # Storefront Blade views
    auth/                   # Auth Blade views (incl. verify-email.blade.php)
    layouts/                # Shared layouts and partials
    components/             # Reusable Blade components
    emails/                 # Email templates
    pdf/                    # PDF templates (invoice)
  js/app.js                 # Vite entry: Alpine.js bootstrap
  css/app.css               # Vite entry: TailwindCSS
routes/
  web.php                   # All routes (public, auth, admin)
```

## User Roles & Access

| Role | Description | Middleware |
|---|---|---|
| `admin` | Full access to admin panel | `AdminMiddleware` |
| `approved` | B2B user with approved company | `EnsureB2BApproved` |
| `pending` | B2B user awaiting admin approval | Redirected to pending page |
| `rejected` | B2B user rejected | Shown rejection notice |
| Guest | Browse-only (no cart/checkout) | — |

Price visibility is controlled by `SHOW_PRICES=true/false` in `.env` / `config/shop.php`.

## Registration & Email Verification Flow

1. User registers → `RegistrationConfirmationNotification` is sent (combined welcome + verify link)
2. User is logged in and redirected to `/verify-email` prompt page
3. User clicks link in email → `VerifyEmailController` marks email verified → redirects to `/pending`
4. Admin approves/rejects → `UserApprovedNotification` / `UserRejectedNotification` sent automatically
5. `User` model implements `MustVerifyEmail`; `sendEmailVerificationNotification()` is overridden to use the custom notification

**Key auth routes:**
```
GET  /verify-email               → verification.notice (show prompt)
GET  /verify-email/{id}/{hash}   → verification.verify (process link)
POST /email/verification-notification → verification.send (resend)
GET  /forgot-password            → password.request
POST /forgot-password            → password.email
GET  /reset-password/{token}     → password.reset
POST /reset-password             → password.store
```

## Multi-Language

- Supported locales: `ca` (Catalan), `es` (Spanish), `en` (English)
- Translatable fields use `spatie/laravel-translatable` (JSON columns in DB)
- Models with translatable fields: `Product`, `Category`, `Brand`, `PrintTemplate`, `HeroSlide`
- Language switching: `GET /locale/{locale}` → `LocaleController`
- User locale preference stored in `users.locale` column
- Lang files per locale: `app.php`, `validation.php`, `auth.php`, `passwords.php`, `pagination.php`

## Stock System

Products use a **2-state enum** `stock_status` (not an integer quantity):

| Value | Meaning | Display |
|---|---|---|
| `in_stock` | Available immediately | Green badge |
| `pre_order` | Delivered within 24-48 hrs | Amber badge with notice |

- Both states allow adding to cart. Pre-order shows a delivery-time notice.
- Model scopes: `Product::inStock()`, `Product::preOrder()`
- Model accessors: `$product->is_in_stock`, `$product->is_pre_order`
- **No integer stock / no low-stock threshold** — the old `stock`, `low_stock_threshold`, `notify_low_stock` columns were removed.

## Hero Slider (Homepage)

The right-hand panel of the homepage hero is a dynamic slider managed through the admin panel.

- Model: `HeroSlide` — uses `HasTranslations` for `eyebrow` and `title` (JSON columns)
- Admin CRUD: `/admin/hero-slides` — create, edit, delete, toggle active, reorder (up/down)
- Fallback: if no active slides exist, the static `public/images/hero_1.png` is shown
- Auto-advances every 5 s via Alpine.js `init()` + `setInterval`
- Smooth cross-fade via CSS `opacity` transitions (not Alpine `x-show` transitions)

## Newsletter Subscriptions

- Model: `NewsletterSubscription` — email (unique), ip_address, is_active, timestamps
- Public endpoint: `POST /newsletter/subscribe` → returns JSON `{status: "success"|"duplicate"|"error"}`
- Re-activates a previously unsubscribed email instead of rejecting it
- Admin panel: `/admin/newsletter` — subscriber list with stats, filter, search, CSV export, delete
- Form on homepage uses Alpine.js `fetch()` — no page reload

## Welcome Popup

On the first homepage visit (once per 24 h, tracked via `localStorage`):
- Highlights two services: Digital Printing and Papeleria (marked as NEW)
- Dismisses on backdrop click, ✕ button, Escape, or CTA navigation
- Fully translated in ca/es/en via `popup_*` keys in `app.php`

## Database Conventions

- Soft deletes are used on select models — check for `SoftDeletes` trait before querying
- Media files are managed by Spatie MediaLibrary (not stored in model columns)
- Translatable JSON columns: `name`, `description`, `eyebrow`, `title` (depending on model)
- All migrations are timestamped and sequential; run `migrate:fresh` if you see FK conflicts
- Hero slide images stored in `public` disk under `hero_slides/` — requires `storage:link`

## AI Integration

Configured via `.env`:
```
AI_PROVIDER=gemini          # or 'anthropic'
GEMINI_API_KEY=...
GEMINI_MODEL=gemini-2.0-flash
ANTHROPIC_API_KEY=...
ANTHROPIC_MODEL=claude-haiku-4-5-20251001
```

The admin AI controller (`Admin/AdminAiController`) generates product descriptions and SEO content.

## Print-on-Demand System

The print subsystem is the most complex domain:

- `PrintTemplate` — defines a product type (business cards, mugs, t-shirts…) with options
- `PrintOption` / `PrintOptionValue` — customization axes (size, colour, finish…)
- `PrintQuantityTier` — volume-based pricing tiers per template
- `PrintCompatibilityRule` — which option combos are valid
- `PrintJob` — a customer's print order linked to a template + chosen options + artwork
- `PrintPriceCalculator` (Service) — given template + options + quantity → returns `PrintPriceResult`
- Artwork uploads go through Spatie MediaLibrary

## Route Groups

```
/                           Public (HomeController, ProductController, PageController…)
/newsletter/subscribe       Public POST (NewsletterController)
/impressio/{slug}           Print builder (PrintJobController)
/verify-email               Auth (email verification flow)
/cart                       Auth (CartController)
/orders                     Auth (OrderController)
/admin                      AdminMiddleware (all Admin/* controllers)
/admin/hero-slides          Admin — slider management
/admin/newsletter           Admin — subscriber management
/locale/{locale}            Public (LocaleController)
```

## Coding Conventions

- Controllers are thin — business logic lives in Services or Models
- Blade components are in `resources/views/components/`
- Flash messages use `session()->flash('success'/'error', '...')`
- Notifications extend `Illuminate\Notifications\Notification` and use `via(['mail'])` or `via(['mail','database'])`
- All admin routes are prefixed `/admin` and protected by `AdminMiddleware`
- Notification locale set via `->locale($user->locale ?? 'ca')` before dispatching

## Environment Flags

| Variable | Default | Effect |
|---|---|---|
| `SHOW_PRICES` | `true` | Show/hide prices for guest/non-B2B users |
| `AI_PROVIDER` | `gemini` | Switch AI backend for content generation |
| `APP_LOCALE` | `ca` | Default application locale |
| `MAIL_MAILER` | `log` | In dev, emails go to `storage/logs/laravel.log` |
| `QUEUE_CONNECTION` | `database` | Run `php artisan queue:work` to process jobs |

## Testing

```bash
php artisan test
php artisan test --filter TestName
```

Tests live in `tests/Feature/` and `tests/Unit/`. The default database for tests is an in-memory SQLite instance (configured in `phpunit.xml`).

## Common Gotchas

- **Vendors not committed** — `vendor/` is gitignored; run `composer install` before anything
- **Assets not built** — run `npm install && npm run build` (or `npm run dev`) before serving
- **Queue jobs pending** — all notification emails require the queue; run `php artisan queue:work`
- **Storage not linked** — hero slide images and product images need `php artisan storage:link`
- **Translatable fields** — always use `$model->getTranslation('name', 'ca')` for a specific locale; plain `$model->name` returns current app locale
- **Email verification** — `User` implements `MustVerifyEmail`; override `sendEmailVerificationNotification()` in User model if changing the verification email template
- **Stock is an enum** — `stock_status` is `in_stock` or `pre_order`; there is no integer quantity column
- **Fiscal validation** — `FiscalIdentity` rule validates Spanish CIF/NIF/VAT formats
- **Hero slides** — `eyebrow` and `title` are JSON (translatable); access per-locale with `$slide->getTranslation('title', 'ca', false)`
- **Newsletter dedup** — the subscribe endpoint re-activates soft-unsubscribed emails silently

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
| Auth | Laravel Breeze (custom-extended) |
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
php artisan queue:work      # Process queued jobs
```

## Project Structure

```
app/
  Console/Commands/       # Artisan scheduled commands (e.g. abandoned-cart emails)
  Http/
    Controllers/
      Admin/              # Admin panel controllers (prefix: /admin)
      Auth/               # Auth controllers (login, register, approval)
      Shop/               # Storefront controllers
    Middleware/           # AdminMiddleware, SetLocale, EnsureB2BApproved
  Models/                 # Eloquent models (50+)
  Notifications/          # 15+ notification classes
  Mail/                   # Mailable classes
  Services/               # PrintPriceCalculator, etc.
  Rules/                  # Custom validation (FiscalIdentity)
  Exports/                # Excel exports (OrdersExport)
config/
  shop.php                # Custom config: SHOW_PRICES flag
database/
  migrations/             # 70+ migrations
  seeders/
resources/
  views/
    admin/                # Admin panel Blade views
    shop/                 # Storefront Blade views
    auth/                 # Auth Blade views
    layouts/              # Shared layouts and partials
    components/           # Reusable Blade components
    emails/               # Email templates
    pdf/                  # PDF templates (invoice)
  js/app.js               # Vite entry: Alpine.js bootstrap
  css/app.css             # Vite entry: TailwindCSS
routes/
  web.php                 # All routes (public, auth, admin)
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

## Multi-Language

- Supported locales: `ca` (Catalan), `es` (Spanish), `en` (English)
- Translatable fields use `spatie/laravel-translatable` (JSON columns in DB)
- Models with translatable fields: `Product`, `Category`, `Brand`, `PrintTemplate`
- Language switching: `GET /locale/{locale}` → `LocaleController`
- User locale preference stored in `users.locale` column

## Database Conventions

- Soft deletes are used on select models — check for `SoftDeletes` trait before querying
- Media files are managed by Spatie MediaLibrary (not stored in model columns)
- Translatable JSON columns are named the same as their English equivalent (e.g., `name`, `description`)
- All migrations are timestamped and sequential; run `migrate:fresh` if you see FK conflicts

## AI Integration

Configured via `.env`:
```
AI_PROVIDER=gemini          # or 'anthropic'
GEMINI_API_KEY=...
GEMINI_MODEL=gemini-2.0-flash
ANTHROPIC_API_KEY=...
ANTHROPIC_MODEL=claude-haiku-4-5-20251001
```

The admin AI controller (`Admin/AdminAiController`) generates product descriptions and SEO content. To swap providers, change `AI_PROVIDER` only — the service layer handles routing.

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
/                    Public (HomeController, ProductController, PageController…)
/impressio/{slug}    Print builder (PrintJobController)
/cart                Auth (CartController)
/orders              Auth (OrderController)
/admin               AdminMiddleware (all Admin/* controllers)
/locale/{locale}     Public (LocaleController)
```

## Coding Conventions

- Controllers are thin — business logic lives in Services or Models
- Blade components are in `resources/views/components/`
- Flash messages use `session()->flash('success'/'error', '...')`
- Form requests for validation live alongside controllers in `app/Http/Requests/` (when present)
- Notifications extend `Illuminate\Notifications\Notification` and use `via(['database', 'mail'])`
- All admin routes are prefixed `/admin` and protected by `AdminMiddleware`

## Environment Flags

| Variable | Default | Effect |
|---|---|---|
| `SHOW_PRICES` | `true` | Show/hide prices for guest/non-B2B users |
| `AI_PROVIDER` | `gemini` | Switch AI backend for content generation |
| `APP_LOCALE` | `en` | Default application locale |
| `MAIL_MAILER` | `log` | In dev, all emails are written to `storage/logs/laravel.log` |
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
- **Queue jobs pending** — if notifications aren't sending, run `php artisan queue:work`
- **Translatable fields** — always pass the locale when reading: `$product->getTranslation('name', 'ca')`
- **Media uploads** — files go through Spatie MediaLibrary; don't write to `public/` directly
- **Fiscal validation** — `FiscalIdentity` rule validates Spanish CIF/NIF/VAT formats

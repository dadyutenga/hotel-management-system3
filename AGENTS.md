# AGENTS.md

## Commands

```bash
# Full dev environment (serve + queue + vite + reverb + logs)
composer dev

# Tests (uses SQLite in-memory automatically via phpunit.xml)
composer test                          # all tests
php artisan test --filter=TestName     # single test

# Formatting (Laravel Pint)
vendor/bin/pint                        # fix all
vendor/bin/pint --test                 # check only

# Frontend
npm run dev                            # Vite dev server
npm run build                          # production build

# Single-test-file shortcut
php artisan test tests/Feature/Accounting/
```

## Architecture

- **Laravel 12** / PHP 8.2+ / PostgreSQL (prod) / SQLite (tests)
- **Frontend**: Server-rendered Blade + Tailwind CSS 3.4 + Alpine.js 3.x (no SPA)
- **Real-time**: Laravel Reverb (WebSocket) — started by `composer dev`
- **Payments**: AzamPesa (TZS default currency)

### Module layout

Controllers are grouped in subdirectories by domain: `Accounting/`, `Store/`, `Restaurant/`, `Bartender/`, `Laundry/`, `Procurement/`, `Finance/`, `Manager/`, `Reception/`, `Admin/`. Views mirror this under `resources/views/`. Models are flat in `app/Models/` (92 models).

### Critical conventions

- **All models use UUID primary keys** via `App\Traits\HasUuid` (string, non-incrementing). Never use auto-incrementing integer IDs.
- **Soft deletes use `App\Traits\HasSoftDelete`**, NOT Laravel's built-in `SoftDeletes` trait. Always use the custom one.
- **Role middleware**: `middleware:role:admin,manager`. Role names are constants on `App\Models\Role` (e.g., `Role::ADMIN`). Comparison is case-insensitive via `Role::matches()`.
- **Currency**: Use `@currency($amount)` and `@currencySymbol` Blade directives. All views receive `$systemCurrency`, `$currencySymbol`, `$exchangeRate` automatically.
- **i18n**: English + Swahili (`resources/lang/{en,sw}/`). Missing Swahili keys fall back to English and log a warning.
- **Observers**: `BookingObserver` and `ReservationObserver` are registered in `AppServiceProvider`. Be aware of side effects when creating/updating these models.

### Role constants (12 roles)

`admin`, `manager`, `front_desk`, `supervisor`, `house_help`, `store_manager`, `store_keeper`, `restaurant_manager`, `waiter`, `bar_tender`, `laundry_manager`, `ACCOUNTANT`, `cashier`, `stock_controller`

Note: `ACCOUNTANT` constant is uppercase but role matching is case-insensitive.

### Key services

- `App\Services\AccountingService` — double-entry journal entries
- `App\Services\ReceiptService` — polymorphic receipts across all modules
- `App\Services\Payment\` — AzamPesa integration
- `App\Services\Billing\` — checkout and billing logic
- `App\Services\NotificationService` + `SmsService` — Africa's Talking SMS

## Testing

- PHPUnit 11 with SQLite `:memory:` (configured in `phpunit.xml` — no DB setup needed)
- Tests organized by module: `tests/Feature/{Accounting,Auth,Bartender,Procurement,Restaurant,Store}/`
- `composer test` runs `config:clear` then `artisan test`

## Gotchas

- `bug/` directory contains bug report markdown files, not PHP code
- `docs/` contains module implementation guides — read these before building within a module
- `tasks/` contains task specs (e.g., `azam-pesa.md`)
- Bug reports use a separate SQLite database (`database/bugs.sqlite`, gitignored)
- `config/hms_auth.php` controls passkey auth and staff session settings
- Queue connection defaults to `database` in dev; tests use `sync`

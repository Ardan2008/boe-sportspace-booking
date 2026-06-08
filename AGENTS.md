# BOE Space Reserve — Booking System

Laravel 12 app (PHP ^8.2) for facility/space booking. Tailwind 4 + Vite 7 frontend.
Codebase language: **Indonesian** (comments, error messages, route names).

## Quick start

```bash
composer setup              # full setup (install, .env, key, migrate, npm, build)
composer dev                # php artisan serve + queue:listen + npm run dev (via concurrently)
composer test               # artisan config:clear → artisan test (Pest)
npm run build               # vite build
npm run dev                 # vite dev server
```

## Testing

- **Pest PHP 4** (not PHPUnit directly). Tests go in `tests/Feature/` or `tests/Unit/`.
- Uses in-memory SQLite (`DB_DATABASE=:memory:`). No external services needed.
- Extends `Tests\TestCase` via `tests/Pest.php`.
- `RefreshDatabase` trait is **commented out** in `tests/Pest.php` — enable per-test if needed.
- Only `ExampleTest` files exist; all real tests are missing.

```bash
composer test               # runs all tests (config:clear → test)
php artisan test --filter=SomeTest  # single test
```

## Architecture

### Auth
- **Session-based** admin auth (no Breeze/Jetstream/Sanctum).
- **Passwords stored in plaintext** (`admins.password`). This is intentional.
- Admin login URL is dynamic: `/{ADMIN_LOGIN_SECRET}` — read from `config/services.php` via `ADMIN_LOGIN_SECRET` env var (not in `.env.example`; must be added manually).
- Middleware `admin.access` (aliased in `bootstrap/app.php`) checks session. Supports:
  - `admin.access:owner` — owner-only
  - `admin.access:can_edit` — owner or `can_edit=true` admin

### Booking lifecycle
`pending` → `confirmed` → `booked` → `completed`
(also `rejected` and `cancelled`)

- Pending bookings auto-cancel after `expired_at` passes (scheduled task runs every minute, defined in `routes/console.php`).
- Confirmed bookings from JAWA TIMUR province get 1-day expiry; others get 3 days.
- Check-in transitions `confirmed` → `booked`; check-out transitions `booked` → `completed`.

### Key files

| Path | Purpose |
|---|---|
| `routes/web.php` | All public & admin routes (271 lines, one file) |
| `routes/console.php` | Scheduled auto-expire task (every minute) |
| `bootstrap/app.php` | Middleware alias registration |
| `app/Http/Middleware/CheckAdminAccess.php` | Session-based admin guard |
| `app/Models/` | 12 models: Booking, Fasilitas, Admins, Penyewa, JadwalBlokir, AuditLog, User, etc. |

### Other config quirks
- Session driver: `database` (dev), overridden to `array` in tests
- Cache store: `database` (dev), overridden to `array` in tests
- Queue: `database` (dev), overridden to `sync` in tests
- DB: SQLite default, timezone `Asia/Jakarta`
- Google reCAPTCHA (`RECAPTCHA_SECRET_KEY`) verified only in `production` environment — also not in `.env.example`
- PDF receipts via `barryvdh/laravel-dompdf`
- Sends email on approve/reject (`ApproveBookingMail` / `RejectBookingMail`) — sent **synchronously** (not queued)
- `php artisan storage:link` required for public access to uploaded images

## Important conventions

- **No comments in code** — avoid adding PHP/BLADE/JS comments unless necessary.
- Use existing model relationships and patterns when extending functionality.
- All admin routes are inside `middleware(['admin.access'])` group.
- Audit logging via `AuditLog::catat()` on every state-changing action.

## UI & Frontend Rules (CRITICAL)
- **Alpine.js:** NEVER put multi-line logic inside HTML attributes. Always use a dedicated `<script>` tag at the bottom of the page and initialize components using `document.addEventListener('alpine:init', ...)`.
- **Blade Syntax:** Be careful with `{{ }}` and quotes. Always use single quotes `'` inside Alpine attributes if the attribute itself is wrapped in double quotes `"`.
- **Tailwind 4:** Use `w-full` for main containers. Ensure every `<div>` is closed.
- **Hybrid Dropdown:** Logic must be handled via an Alpine.js data object to prevent code leakage on the screen.

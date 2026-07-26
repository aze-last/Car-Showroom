# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel 12 + Livewire 4 vehicle showroom and auction house. Public users browse units and auctions; admins/staff manage inventory via a hidden admin panel (no public login links — admin access is via `/login` directly). Stack: PHP 8.2+, Livewire 4, Flux UI (free), Tailwind CSS 4, Fortify (auth), Socialite (Google OAuth), Pest 4 (tests), Vite.

## Commands

```bash
composer run dev        # serve + queue:listen + vite dev (concurrently)
composer run test       # config:clear + pint --test + php artisan test
composer run lint       # pint --parallel (auto-fix)
npm run build           # vite production build (fixes "Vite manifest" errors)

# Single test file / filter (preferred for iteration):
php artisan test --compact tests/Feature/UnitManagementTest.php
php artisan test --compact --filter=test_can_create_unit
```

Run `vendor/bin/pint --dirty` before finalizing changes.

Dev DB is MySQL (Laragon); tests run against sqlite (`database/testing.sqlite`, configured in `phpunit.xml`). Tests use `RefreshDatabase` per test class (not globally in `Pest.php`); most are class-based PHPUnit style extending `Tests\TestCase` even though Pest is installed — follow the existing style of the file you're editing.

Note: `vite.config.js` and `APP_URL` are pinned to a LAN IP (`192.168.254.194`) for device testing — don't "fix" this without asking.

## Architecture

### Roles & access control

Three tiers on the `User` model via boolean columns, not a roles package:
- `is_admin` — full admin panel access (`admin` middleware = `EnsureUserIsAdmin`)
- `is_employee` — staff; `$user->isStaff()` returns `is_admin || is_employee` (`staff` middleware)
- Regular users — "collectors" who bid in auctions and use `/garage`

Middleware aliases (`admin`, `staff`, `google`) are registered in `bootstrap/app.php` (Laravel 12 style — no HTTP Kernel). Inventory routes use policies (`UnitPolicy`, `UnitStatusLogPolicy`, gated on `isStaff()`); admin-only routes use the `admin` middleware group in `routes/web.php`.

Livewire components that need auth guards for collectors use the `App\Concerns\EnforcesCollectorAuthentication` trait (`redirectIfGuest()`, `redirectIfUnverified()`, `redirectIfGoogleRequiredForAuctions()`). Auction participation requires a linked Google account unless the user is staff (`User::canParticipateInAuctions()`).

### Google OAuth

`GoogleAuthController` → `GoogleAuthService::resolveUser()` handles all cases: login by `google_id`, linking to the currently authenticated user (emails must match), linking to an existing email, or creating a new auto-verified collector. Google-only users have `password = null` and `auth_provider` enum (`App\Enums\AuthProvider`).

### Unit status changes (critical invariants)

- Statuses are string constants `Unit::STATUS_AVAILABLE` / `Unit::STATUS_SOLD` — explicit set-state only, **never toggle logic**.
- All status changes go through `App\Services\UnitStatusService::setStatus()` which wraps a `DB::transaction` + `lockForUpdate()`, re-checks status inside the lock, is idempotent (no-op + message if already in target state), and logs to `unit_status_logs` via `UnitInventoryLogService` only when state actually changed (with request context: request id, reason, IP, user agent).
- QR route `/admin/units/{unit}/qr` requires `staff` + `signed` middleware + `viewQr` policy; generate URLs with `Unit::signedQrUrl()`.
- Guest walk-in sales capture `guest_name`, `guest_contact`, `handover_image_path` directly on the `Unit`.

### Public routing & models

- `Unit` uses ULIDs for public routes: `getRouteKeyName()` is `public_id` (auto-generated in `booted()`), so route-model binding everywhere resolves by ULID, not id.
- `Unit` has SoftDeletes; `images()` ordered by `sort_order`; `mainImage()` is the first image. Prices stored as integer PHP pesos; display via `Unit::formattedPrice()` (₱ + number_format, or "Price upon request" when `show_price` is false / price null).
- Auction domain: `Auction` (statuses include `live`/`active`, reserve/starting/current bid, winner + fallback user), `Bid`, `BidDeposit` (admin-verified proof of payment via `AdminDepositVerification`), `UserAuctionStrike`. Auction room UI uses `wire:poll` for real-time updates — there is no websocket/broadcast layer.

### Design layout presets

The public UI is theme-switchable at runtime via the `Setting` model — a cached K/V store (`Setting::get()`/`Setting::set()`, cache keys `setting.{key}`). `design_layout` (default `cinema`) selects the showroom preset: `resources/views/livewire/public/presets/{cinema,bmw_m,nintendo_2001}.blade.php`, included dynamically from `public-showroom.blade.php`. Unit detail pages branch on the same setting to `details_bmw_m` / `details_nintendo_2001` presets. Admins switch layouts at `/admin/customization` (`AdminCustomization`). When changing public UI, check which preset(s) the change applies to. `DESIGN.md` documents preset design tokens.

### Storage

- Store **relative paths only** (e.g. `units/{id}/file.jpg`), never full URLs; display with `Storage::url($path)`. Image upload logic lives in `UnitImageStorageService`. Switching disks must only require changing `FILESYSTEM_DISK` in `.env`.

### Livewire conventions

- Admin components are flat in `app/Livewire/` (`Admin*`); public-facing ones in `app/Livewire/Public/`; views mirror this in `resources/views/livewire/`.
- Settings pages use Livewire v4 single-file pages: `Route::livewire('settings/profile', 'pages::settings.profile')` in `routes/settings.php`.
- Fortify provides auth views/actions (`FortifyServiceProvider`, `app/Actions/Fortify/`); registration is a custom Livewire component at `/register`.

## Laravel Boost

This repo has Laravel Boost installed (MCP server) with detailed ecosystem guidelines in `.cursor/rules/laravel-boost.mdc` — follow them. Key points: use the `search-docs` Boost tool for version-specific Laravel/Livewire/Pest/Tailwind docs before making changes; use `tinker`/`database-query` Boost tools for debugging; use `php artisan make:*` (with `--no-interaction`) to create files; never call `env()` outside config files; prefer Eloquent relationships over raw queries; every change needs a test.

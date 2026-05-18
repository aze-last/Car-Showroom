# Car Showroom Project

A production-ready Laravel application for a public vehicle showroom with a hidden admin panel.

## Project Overview
- **Technology Stack:** Laravel 12, Livewire 4, Flux UI, Tailwind CSS 4, Pest (Testing).
- **Architecture:** Monolithic Laravel app using Livewire for interactive components.
- **Core Models:** `Unit` (Vehicles), `Category`, `UnitImage`, `UnitStatusLog`, `Inquiry`, `User`.
- **Key Features:**
    - Public showroom with category filters and search.
    - Admin panel for managing units, categories, and viewing logs.
    - QR code workflow for quick unit status updates (Available <-> Sold).
    - Status change logging with concurrency protection (DB locking).
    - Cloud-ready storage implementation (Local/S3).

## Building and Running

### Prerequisites
- PHP 8.2+
- Node.js & NPM
- Composer
- SQLite (or other supported DB)

### Setup Commands
```powershell
# Install dependencies, setup .env, generate key, migrate, and build assets
composer run setup

# Link storage for local file access
php artisan storage:link
```

### Development
```powershell
# Run server, queue, and vite in parallel
composer run dev
```

### Testing & Linting
```powershell
# Run all tests (Pest)
composer run test

# Run linting (Laravel Pint)
composer run lint
```

## Development Conventions

### Security
- **Strict No-Secrets Policy:** Never expose API keys or secrets in Blade/Livewire/JS. Use `.env` and `config/`.
- **Admin Access:** No public links to login. Access via `/login` directly. Admin routes protected by `auth` and `is_admin` gate.
- **Data Protection:** Use signed URLs for QR code actions.

### Storage
- Never remove anything if something needs to be removed tell the developer first!!! 
- Always use the `Storage` facade.
- Never hardcode `asset('storage/...')`. Use `Storage::url($path)`.
- Store relative paths in the database (e.g., `units/{unit_id}/filename.jpg`).

### Code Style
- **Status Changes:** Must use explicit set-state logic (`SET_SOLD`, `SET_AVAILABLE`), not toggles.
- **Database:** Use transactions and `lockForUpdate()` for status changes to prevent race conditions.
- **ULIDs:** `Unit` uses ULID (`public_id`) for public-facing URLs.

### Testing
- All feature changes must be verified with Pest tests.
- Focus on: Security (auth/gates), Concurrency (status logs), and Core CRUD.

## Important Routes
- `GET /` - Public Showroom
- `GET /login` - Admin Login
- `GET /admin` - Admin Dashboard
- `GET /admin/units/{unit}/qr` - QR Action Page (Signed URL)

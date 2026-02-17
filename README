You are a senior Laravel engineer. Build a production-ready Laravel app using:
- Laravel (latest stable)
- Livewire (latest stable)
- Laravel default authentication scaffolding already installed
- Testing framework: Pest

Implement a public vehicle showroom and a hidden admin panel. Follow all requirements exactly. Be secure, predictable, and testable.

ABSOLUTE SECURITY REQUIREMENT:
- Never expose API keys or secrets in client-side code (Blade, Livewire rendered HTML, JS).
- Store secrets only in .env and config files.
- If any external integrations are added (S3/Cloudinary/SMS/etc.), implement backend-only patterns: signed uploads, server proxy endpoints, webhook validation.
- Do not commit .env.
- Do not log secrets.

STORAGE REQUIREMENTS (DEV/DEMO MODE + CLOUD-READY):

The app must start using LOCAL storage for development/demo.

1) Default storage disk:
   - Use Laravel public disk.
   - .env must use: FILESYSTEM_DISK=public

2) Use Storage facade everywhere:
   - NEVER hardcode asset('storage/...') paths.
   - NEVER store full URLs in database.
   - Always store relative paths like:
       units/{unit_id}/filename.jpg

3) Display images using:
   - Storage::url($path)

4) Ensure `php artisan storage:link` is documented in README.

5) The implementation must be cloud-ready:
   - Switching to S3 or another disk must require ONLY changing FILESYSTEM_DISK in .env.
   - No refactoring of business logic should be required.
   - Do not couple image logic to local paths.

6) If in the future S3/Cloudinary is used:
   - Never expose storage keys client-side.
   - Use backend-controlled upload or signed upload flow.

CORE CONCEPTS:
- “Units” are items for sale (motorcycle, cars, vans, sportscars, and user-defined categories).
- Public users can browse only. No public login button/links.
- Admin login is accessible only by visiting /login directly.
- Units have slideshow photos (no 3D).
- Currency: Philippine Peso (PHP), formatted with ₱.
- SOLD status must be reversible (Available <-> Sold).
- Status change must use explicit set-state (SET_SOLD / SET_AVAILABLE), NOT toggle.
- QR code scanning must require authentication.
- Every status change must be logged.
- Prevent race conditions using database row locking.

PUBLIC SHOWROOM REQUIREMENTS:

Route: GET /
- Show showroom page:
  - Category filter chips (default: Motorcycle, Cars, Vans, Sportscars)
  - Search bar (search by unit name)
  - Units grid
- Each unit card:
  - Main photo (first image)
  - Name
  - Price in PHP (₱ format) or “Price upon request”
  - Status badge (Available / Sold)
- Clicking unit:
  - Show detail page or modal
  - Slideshow of images
  - Name, price, status, description

No login or admin links must appear publicly.

ADMIN PANEL REQUIREMENTS:

Routes protected by auth middleware:
- /admin
- /admin/units
- /admin/units/create
- /admin/units/{unit}/edit
- /admin/categories
- /admin/logs
- /admin/units/{unit}/qr

CATEGORY MANAGEMENT:
- Seed default categories.
- CRUD.
- Prevent deletion if category has units.

UNIT MANAGEMENT:
Fields:
- name (required)
- category_id (required)
- price_php (nullable integer)
- description (nullable text)
- status (AVAILABLE or SOLD, default AVAILABLE)
- show_price (boolean default true)
- SoftDeletes enabled

Features:
- Search by name
- Filter by category
- Multiple images
- Reorder images via sort_order
- QR code display + printable layout

QR WORKFLOW (LOGIN REQUIRED):

Route: GET /admin/units/{unit}/qr
- If guest, redirect to login.
- Show:
  - Unit info
  - Current status
  - Last changed by + timestamp
- Buttons:
  - Mark as SOLD (if AVAILABLE)
  - Mark as AVAILABLE (if SOLD)
- Require confirmation before committing.

Status change endpoint:
- Must use DB transaction + lockForUpdate().
- Re-check status inside transaction.
- If already in requested state, do nothing and return message.
- Insert log only if status actually changed.

RACE CONDITION PREVENTION:
- Use transaction + row locking.
- Explicit set-state logic (idempotent).
- No toggle logic.

STATUS LOGGING:

unit_status_logs table:
- unit_id
- user_id
- action (SET_SOLD or SET_AVAILABLE)
- from_status
- to_status
- ip_address (nullable)
- user_agent (nullable)
- timestamps

Logs page:
- Newest first
- Filter by unit, user, date range
- Show action, from->to, user, timestamp

DATABASE STRUCTURE:

categories:
- id
- name (unique)
- timestamps

units:
- id
- category_id (fk)
- name
- price_php (nullable integer)
- description (nullable text)
- status (AVAILABLE/SOLD)
- show_price boolean
- timestamps
- softDeletes

unit_images:
- id
- unit_id (fk)
- url (relative path only)
- sort_order (int)
- timestamps

unit_status_logs:
- id
- unit_id (fk)
- user_id (fk)
- action
- from_status
- to_status
- ip_address
- user_agent
- timestamps

ELOQUENT RELATIONSHIPS:
- Category hasMany Units
- Unit belongsTo Category
- Unit hasMany UnitImages (ordered by sort_order)
- Unit hasMany UnitStatusLogs (latest first)
- UnitStatusLog belongsTo Unit and User

FORMATTING:
- Store price as integer.
- Format display using PHP currency formatting (₱, thousands separator).

LIVEWIRE COMPONENTS:
- PublicShowroom
- UnitDetail
- AdminUnitsIndex
- AdminUnitForm
- AdminCategories
- AdminLogs
- AdminUnitQrAction

TESTING (Pest):

Write tests for:
1) Public access works.
2) Admin requires auth.
3) Authenticated can create categories and units.
4) Guests cannot change status.
5) Authenticated can SET_SOLD and SET_AVAILABLE.
6) Log created only when state changes.
7) Idempotent behavior (no duplicate logs if same state).
8) Concurrency-safe logic.

SEEDING:
- Seed default categories.

README MUST INCLUDE:
- Setup steps
- Run migrations + seed
- Run storage:link
- Access admin via /login then /admin
- QR workflow explanation
- Storage configuration explanation
- Security note: never expose API keys client-side.

Important:
- No reservation system.
- No toggle endpoint.
- Local storage first.
- Cloud-ready architecture.
- Strict security practices.

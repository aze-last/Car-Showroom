# Car Showroom Project

A production-ready Laravel application for a public vehicle showroom and premium auction house with a specialized admin command center.

## Agent Team (Source of Truth)
This project utilizes a specialized **Agent Team** for architectural orchestration, logic verification, and UI/UX auditing.
- **Location:** `.agent-team/`
- **Mandate:** The agent profiles in this folder are the **primary source of truth**. All changes must align with the objectives and rules defined in the agent files.
- **Coordination:** The `Coordinator` agent orchestrates all tasks and ensures end-to-end integration (Database -> Model -> UI) for every feature.

## Architecture & Logic (LATEST)
- **Technology Stack:** Laravel 12, Livewire 4, Flux UI (Pro), Tailwind CSS 4, Pest (Testing).
- **Core Models:** 
    - `Unit`: Vehicles with technical specs, status tracking, and public-facing ULIDs.
    - `Auction`: Timed events for units with bidding logic and reserve prices.
    - `Bid` & `BidDeposit`: Financial layer for auctions with admin verification workflow.
    - `Category`: Dynamic vehicle classification.
    - `Inquiry`: Lead generation system.
    - `Setting`: Global K-V store for shop and design configuration.
    - `User`: Roles: `Admin` (Full control), `Staff` (Inventory/QR), `User` (Collector/Garage).

## Design System (LOCKED)
The application follows a **"Premium White"** aesthetic with high-contrast elements.
- **Palette:** 
    - Primary: Pure White (`#ffffff`) backgrounds.
    - Secondary: `zinc-50` surfaces, `zinc-100` borders.
    - Text: `zinc-900` for primary typography.
    - Accents: `emerald-500` (Success/Available), `red-600` (Critical/Danger).
- **Typography:** Professional tracking-widest on headers (Instrument Sans / Hanken Grotesk).
- **Components:** 
    - Rounded-3xl (40px) corners for cards and main containers.
    - Rounded-xl for inputs and buttons.
    - **Pills:** Horizontal scrolling category pills on mobile with `no-scrollbar`.
- **Motion:** Staggered `animate-showroom-fade-up` (cubic-bezier) for list/grid entries.

## Key Features
- **Public Showroom:** Advanced filtering (Categories, Search, Sort) with multiple layout presets:
    - `Cinema`: High-impact hero with parallax and dynamic bento grid.
    - `Marketplace`: Utility-focused grid for large inventories.
    - `Minimalist`: Clean, editorial-style presentation.
- **Auction House:** Real-time bidding, Bid Deposit verification (admin-verified proof of payment), and "My Garage" for winning bids.
- **Vehicle Comparison:** Neutral side-by-side breakdown for up to 3 vehicles with a persistent tray.
- **Admin Command Center:**
    - **Inventory:** Full CRUD with QR code generation for quick status updates.
    - **Auctions:** Lot management, bid monitoring, and deposit approval.
    - **Customization:** Live toggle for layouts, palettes, and hero features.
    - **Security:** Signed URLs for QR actions, Concurrency protection (DB locking).

## Project Workflow
- **ULIDs:** `Unit` uses ULIDs for public routes (`/units/{ulid}`) to prevent ID scraping.
- **Status Changes:** Mandatory use of explicit state logic (`STATUS_AVAILABLE`, `STATUS_SOLD`). Changes are logged with request context.
- **Storage:** Relative paths only. Use `Storage::url($path)`. No hardcoded `public/` paths.
- **Testing:** Pest tests required for all logic changes.

## Commands
```powershell
# Setup environment
composer run setup

# Run dev environment
composer run dev

# Testing & Linting
composer run test
composer run lint
```

## Important Routes
- `GET /` - Public Showroom
- `GET /auction` - Auction Hall
- `GET /comparison` - Side-by-side Comparison
- `GET /admin` - Dashboard (Admin Only)
- `GET /admin/customization` - UI Configuration
- `GET /admin/units` - Inventory Management
- `GET /admin/deposits` - Bid Deposit Verification

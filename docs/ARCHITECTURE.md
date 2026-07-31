# Car Showroom — Architecture Documentation

## Overview
The Car Showroom admin panel is built on **Laravel 12**, **Livewire 4**, **Flux UI (Pro)**, and **Tailwind CSS 4**, using a **Dealership Operations Model** organized into 5 operational zones:

```
Admin Command Center
├── 1. Dashboard        (Overview & Operations HUD)
├── 2. Operations       (Inventory, Inquiries, Messages, Auctions, Deposits)
├── 3. People           (Customers CRM, Employee Accounts)
├── 4. Reports          (System Audit Trail & Logs)
└── 5. Settings         (System Master, Appearance Presets, Profile)
```

## Architectural Layers

### 1. Data Layer (Models & Database)
- **`Unit`**: Public vehicles with ULID keys (`public_id`), specs, status (`STATUS_AVAILABLE`, `STATUS_SOLD`), and handover metadata.
- **`Auction` & `Bid`**: Timed bidding events linked to units, starting bids, and reserve prices.
- **`BidDeposit`**: Financial qualification layer required for auction room access.
- **`ChatMessage` & `Inquiry`**: Collector inquiry messages and real-time threaded chat.
- **`UnitStatusLog`**: Immutable transaction log tracking status transitions, users, IP addresses, and reasons.
- **`Setting`**: Key-Value configuration store for shop identity, maps, and design presets.

### 2. Service & Business Logic Layer
- **`UnitStatusService`**: Transactional state machine with row-level DB locking for `setAvailable()` and `setSold()`.
- **`UnitInventoryLogService`**: Automated audit trail logger for creation, updates, and soft deletions.
- **`UnitImageStorageService`**: Relative storage manager for unit photos and handover proof.

### 3. Component & Presentation Layer
- **Livewire 4 Components**: Dynamic server-driven components with debounced models (`wire:model.live`).
- **Reusable Admin Components**: `<x-admin.card>`, `<x-admin.page-header>`, `<x-admin.stats-card>`, `<x-admin.status-badge>`, `<x-admin.empty-state>`.
- **Global Search**: `<livewire:admin-global-search />` providing instant `Ctrl+K` search across 5 entity domains.

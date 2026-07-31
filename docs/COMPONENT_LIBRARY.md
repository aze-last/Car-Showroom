# Admin Component Library Specification

## Reusable Component Index (`resources/views/components/admin/`)

| Component Tag | Blade Reference | Props | Purpose |
| :--- | :--- | :--- | :--- |
| `<x-admin.card>` | `card.blade.php` | `title`, `subtitle`, `$headerAction` | Standardized card container with custom header & padding |
| `<x-admin.page-header>` | `page-header.blade.php` | `title`, `subtitle`, `badge`, `badgeColor`, `$actions` | Page header toolbar with status badge and CTA buttons |
| `<x-admin.section-header>` | `section-header.blade.php` | `title`, `subtitle`, `$action` | Section heading with tracking-widest uppercase styling |
| `<x-admin.stats-card>` | `stats-card.blade.php` | `title`, `value`, `change`, `changeType`, `caption` | Revenue and volume KPI metric display card |
| `<x-admin.status-badge>` | `status-badge.blade.php` | `status`, `type`, `size` | Type-safe status pill (`available`, `sold`, `pending`, `approved`, `rejected`, `blocked`) |
| `<x-admin.empty-state>` | `empty-state.blade.php` | `title`, `description`, `icon`, `$action` | Dashed border empty container for tables and lists |

## Global Livewire Components

### 1. Global Search (`<livewire:admin-global-search />`)
- **Keybinding**: `Ctrl + K` / `Cmd + K`
- **Search Domains**: Vehicles, Collectors, Auctions, Staff Accounts, Inquiries.

### 2. Notification Bell (`<livewire:public.notification-bell />`)
- **Location**: Top Navigation Header.
- **Triggers**: Pending deposits, unread inquiry chats, expiring auction lots.

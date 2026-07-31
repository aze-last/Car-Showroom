# Admin Route & Security Map

## Complete Admin Route Directory

| Group | Route Name | HTTP Method | Path | Controller / Livewire | Middleware Guards |
| :--- | :--- | :---: | :--- | :--- | :--- |
| **Dashboard** | `admin.dashboard` | GET | `/admin` | `AdminDashboard` | `auth`, `verified`, `no_store`, `admin` |
| **Operations** | `admin.units.index` | GET | `/admin/units` | `AdminUnitsIndex` | `auth`, `verified`, `no_store`, `can:viewAny,Unit` |
| | `admin.units.create` | GET | `/admin/units/create` | `AdminUnitForm` | `auth`, `verified`, `no_store`, `can:create,Unit` |
| | `admin.units.edit` | GET | `/admin/units/{unit}/edit` | `AdminUnitForm` | `auth`, `verified`, `no_store`, `can:update,unit` |
| | `admin.units.qr` | GET | `/admin/units/{unit}/qr` | `AdminUnitQrAction` | `auth`, `verified`, `no_store`, `staff`, `signed`, `can:viewQr,unit` |
| | `admin.messages` | GET | `/admin/messages` | `AdminMessagesIndex` | `auth`, `verified`, `no_store` |
| | `admin.inquiries.index` | GET | `/admin/inquiries` | `AdminInquiriesIndex` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.categories.index` | GET | `/admin/categories` | `AdminCategories` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.auctions.index` | GET | `/admin/auctions` | `AdminAuctionsIndex` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.auctions.create` | GET | `/admin/auctions/create` | `AdminAuctionForm` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.auctions.edit` | GET | `/admin/auctions/{auction}/edit` | `AdminAuctionForm` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.deposits.index` | GET | `/admin/deposits` | `AdminDepositVerification` | `auth`, `verified`, `no_store`, `admin` |
| **People** | `admin.customers.index` | GET | `/admin/customers` | `AdminCustomersIndex` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.employees.index` | GET | `/admin/employees` | `AdminEmployees` | `auth`, `verified`, `no_store`, `owner` |
| **Reports** | `admin.logs.index` | GET | `/admin/logs` | `AdminLogs` | `auth`, `verified`, `no_store`, `can:viewAny,UnitStatusLog` |
| **Settings** | `admin.settings.shop` | GET | `/admin/settings/shop` | `AdminShopSettings` | `auth`, `verified`, `no_store`, `admin` |
| | `admin.customization` | GET | `/admin/customization` | `AdminCustomization` | `auth`, `verified`, `no_store`, `admin` |
| **Account** | `profile.edit` | GET | `/settings/profile` | `pages::settings.profile` | `auth` |
| | `user-password.edit` | GET | `/settings/password` | `pages::settings.password` | `auth`, `verified` |

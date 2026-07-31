# User Roles & Permission Matrix

## Role Hierarchy

```
Owner (is_admin = true, job_title = 'Owner')
  └── Admin (is_admin = true)
        └── Staff (is_employee = true, is_admin = false)
              └── Collector / User (is_employee = false, is_admin = false)
```

## Detailed Permissions Matrix

| Feature / Action | Staff | Admin | Owner | Gate / Policy |
| :--- | :---: | :---: | :---: | :--- |
| **View Operations Dashboard** | ❌ | ✅ | ✅ | `can:viewAny, Unit` |
| **View Active/Sold Inventory** | ✅ | ✅ | ✅ | `UnitPolicy::viewAny` |
| **Create / Edit Vehicle** | ✅ | ✅ | ✅ | `UnitPolicy::create`, `UnitPolicy::update` |
| **Soft Delete Vehicle (Trash)** | ❌ | ✅ | ✅ | `UnitPolicy::delete` |
| **Restore Vehicle from Trash** | ❌ | ✅ | ✅ | `UnitPolicy::restore` |
| **Scan QR & Process Handover** | ✅ | ✅ | ✅ | `UnitPolicy::changeStatus`, `signed` |
| **Category CRUD** | ❌ | ✅ | ✅ | `access-admin` |
| **Manage Auctions & Lots** | ❌ | ✅ | ✅ | `access-admin` |
| **Verify Bid Deposits** | ❌ | ✅ | ✅ | `access-admin` |
| **View Customer Registry** | ❌ | ✅ | ✅ | `access-admin` |
| **Block / Unblock Customer** | ❌ | ✅ | ✅ | `access-admin` |
| **Manage Employee Accounts** | ❌ | ❌ | ✅ | `access-owner` |
| **View Audit Trail Logs** | ❌ | ✅ | ✅ | `UnitStatusLogPolicy::viewAny` |
| **System Master Configuration** | ❌ | ✅ | ✅ | `access-admin` |

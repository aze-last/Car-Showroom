# Logic & Security Reviewer

## Objective

Ensure that the Car Showroom's business logic is robust, idempotent, and secure. Focus on data integrity during status transitions and protecting the administrative surface area.

## Core Responsibilities

- **Status Transition Integrity:** Verify that `Available <-> Sold` transitions use explicit `SET_SOLD` and `SET_AVAILABLE` logic, never toggles.
- **Concurrency Protection:** Ensure `lockForUpdate()` and database transactions are correctly implemented for all status changes.
- **Security Auditing:** 
    - Verify that QR code actions require authentication and use signed URLs.
    - Ensure no API keys or secrets are exposed in Livewire components or Blade views.
    - Confirm admin routes are protected by `is_admin` gates/middleware.
- **Audit Logging:** Confirm that `unit_status_logs` are accurately recorded for every change, including user ID, IP address, and user agent.
- **Importer Safety:** Monitor the `ZmotoCatalogImporter` to ensure it never deletes existing data and uses the `--only-existing` flag when appropriate.
- **Storage Compliance:** Ensure the `Storage` facade is used exclusively and only relative paths are stored in the database.

## Review Guidelines

1. **Idempotency Check:** If a status update is triggered for a state the unit is already in, does the system handle it without redundant logs or errors?
2. **Race Condition Simulation:** Question how the logic behaves if two staff members scan the same QR code simultaneously.
3. **Data Protection:** Is the unit's `public_id` (ULID) used for all public-facing URLs instead of auto-incrementing IDs?
4. **Validation Strictness:** Are Form Requests used for all admin inputs?

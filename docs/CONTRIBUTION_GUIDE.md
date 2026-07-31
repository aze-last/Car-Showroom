# Developer & Contribution Guide

## Local Development Workflow

### Setup Local Environment
```powershell
# Setup environment
composer run setup

# Run dev environment (Vite + Laravel dev server)
composer run dev
```

### Coding Standards & Quality Control

1. **Pint Code Formatting**:
   ```powershell
   composer run lint
   ```
2. **Pest Test Suite**:
   ```powershell
   composer run test
   ```

## Development Rules & Invariants

1. **Transactional Status Changes**:
   - **Never** call `$unit->update(['status' => 'sold'])` directly.
   - Always use `UnitStatusService::setSold()` or `setAvailable()` to ensure DB locks and `UnitStatusLog` creation.

2. **ULID Public Keys**:
   - Public unit routes (`/units/{unit}`) resolve via ULID (`public_id`).
   - Use `$unit->signedQrUrl()` for QR code action URLs.

3. **Re-usable Admin Components**:
   - Build new admin interfaces using the `<x-admin.*>` component library (`docs/COMPONENT_LIBRARY.md`).
   - Maintain the locked **"Premium White"** design system tokens (`docs/DESIGN_TOKENS.md`).

4. **Testing Requirements**:
   - All code changes must include corresponding Pest tests under `tests/Feature/`.

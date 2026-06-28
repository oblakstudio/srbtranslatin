# Slice 05: Selector Surface Cleanup

## Goal
Keep the legacy selector helper alive without depending on the dead singleton runtime.

## Implemented
- Added a compatibility renderer to `src/Modules/Translit/Services/Menu_Integration_Service.php`.
- Updated `lib/Utils/core.php` so `stl_script_selector()` delegates to the `src` container when available.
- Preserved legacy helper concepts:
  - `oneline` / `online`
  - `list`
  - `dropdown`
  - custom labels
  - `inactive_only`
  - custom separator

## Files
- `src/Modules/Translit/Services/Menu_Integration_Service.php`
- `src/Modules/Translit/Handlers/Menu_Integration_Handler.php`
- `lib/Utils/core.php`
- `tests/Unit/Translit/MenuIntegrationServiceTest.php`
- `tests/Unit/Translit/MenuIntegrationHandlerTest.php`

## Verification
- `vendor/bin/phpunit tests/Unit/Translit/MenuIntegrationServiceTest.php tests/Unit/Translit/MenuIntegrationHandlerTest.php`

## Status
Implemented. The helper now defers to `src` rendering when the `stl` container is live.

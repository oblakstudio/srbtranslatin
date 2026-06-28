# Slice 03: Shortcodes

## Goal
Restore legacy shortcode-based escape hatches for global transliteration.

## Implemented
- Added `src/Modules/Translit/Services/Shortcode_Service.php`.
- Added `src/Modules/Translit/Handlers/Shortcode_Handler.php`.
- Restored support for:
  - `stl_cyr`
  - `stl_cyrillic`
  - `stl_translit`
  - `stl_selective_output`
  - `stl_show`
- Integrated placeholder restoration into `Translit_Service::buffer_end()`.

## Files
- `src/Modules/Translit/Services/Shortcode_Service.php`
- `src/Modules/Translit/Handlers/Shortcode_Handler.php`
- `src/Modules/Translit/Services/Translit_Service.php`
- `src/Modules/Translit/Translit_Module.php`
- `tests/Unit/Translit/ShortcodeServiceTest.php`
- `tests/unit-bootstrap.php`

## Verification
- `vendor/bin/phpunit tests/Unit/Translit/ShortcodeServiceTest.php`

## Status
Implemented and covered by unit tests.

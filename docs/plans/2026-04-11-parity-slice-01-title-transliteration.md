# Slice 01: Title Transliteration

## Goal
Restore legacy title transliteration behavior in `src` for classic and document-title APIs.

## Implemented
- Added active `src/Modules/Translit/Handlers/Title_Handler.php`.
- Registered support for `wp_title`, `pre_get_document_title`, and `document_title_parts`.
- Gated title transliteration behind `advanced.fix_titles`.
- Kept classic-title `title-tag` short-circuit behavior.

## Files
- `src/Modules/Translit/Handlers/Title_Handler.php`
- `src/Modules/Translit/Services/Translit_Service.php`
- `tests/Unit/Translit/TitleHandlerTest.php`
- `tests/unit-bootstrap.php`

## Verification
- `vendor/bin/phpunit tests/Unit/Translit/TitleHandlerTest.php`

## Status
Implemented and covered by unit tests.

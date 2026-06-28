# Slice 02: Search Transliteration

## Goal
Restore Latin-to-Cyrillic Serbian search expansion and ordering in `src`.

## Implemented
- Added `src/Modules/Translit/Services/Search_Query_Service.php`.
- Added `src/Modules/Translit/Handlers/Search_Handler.php`.
- Ported the legacy `posts_search` and `posts_search_orderby` behavior into `src`.
- Kept main-query, Serbian-language, non-empty-search, and Latin-input gating.

## Files
- `src/Modules/Translit/Services/Search_Query_Service.php`
- `src/Modules/Translit/Handlers/Search_Handler.php`
- `src/Modules/Translit/Translit_Module.php`
- `tests/Unit/Translit/SearchQueryServiceTest.php`

## Verification
- `vendor/bin/phpunit tests/Unit/Translit/SearchQueryServiceTest.php`

## Status
Implemented and covered by unit tests.

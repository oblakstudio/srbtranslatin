# Slice 06: Media and Permalink Decision

## Goal
Track the media filename and permalink parity decision.

## Decision
- Media filename transliteration is active in the `src` runtime through `sanitize_file_name`.
- Script-specific media URL switching is active for whole-page output or content-only mode.
- Permalink transliteration is active behind `advanced.fix_permalinks` when the current locale does not already handle Serbian or Bosnian slugs.

## Implemented
- Re-enabled media settings after adding runtime support.
- Re-enabled permalink settings for locales that need plugin slug transliteration.
- Preserved locale-based disabling for Serbian and Bosnian locales where WordPress already changes permalinks automatically.

## Files
- `config/settings.php`
- `src/Modules/Translit/Handlers/Media_Handler.php`
- `src/Modules/Translit/Handlers/Permalink_Handler.php`
- `src/Modules/Translit/Services/Media_Service.php`
- `src/Modules/Translit/Services/Permalink_Service.php`
- `tests/Unit/Core/SettingsConfigTest.php`
- `tests/Unit/Translit/MediaServiceTest.php`
- `tests/Unit/Translit/PermalinkServiceTest.php`

## Verification
- `vendor/bin/phpunit --bootstrap tests/unit-bootstrap.php tests/Unit`

## Status
Implemented in the active `src` runtime.

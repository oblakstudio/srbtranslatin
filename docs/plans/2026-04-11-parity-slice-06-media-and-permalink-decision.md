# Slice 06: Media and Permalink Decision

## Goal
Make the current non-implementation of media filename and permalink transliteration explicit.

## Decision
- Media filename transliteration remains deferred.
- Permalink transliteration remains deferred.
- Both feature families stay documented as legacy behavior rather than active `src` runtime support.

## Implemented
- Added explicit warning/disabled UI state for media fields.
- Updated permalink field messaging to say the feature is legacy and not active in the current `src` runtime.
- Kept stored settings defaults for compatibility, but removed the appearance that these controls currently do work.

## Files
- `config/settings.php`
- `tests/Unit/Core/SettingsConfigTest.php`

## Verification
- `vendor/bin/phpunit tests/Unit/Core/SettingsConfigTest.php`

## Status
Decision implemented in the settings UI. Full media/permalink runtime parity is intentionally deferred.

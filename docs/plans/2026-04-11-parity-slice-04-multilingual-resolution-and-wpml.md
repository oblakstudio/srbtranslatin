# Slice 04: Multilingual Resolution and WPML

## Goal
Replace the null multilingual stub with real `src`-level WPML resolution and switcher support.

## Implemented
- Added `src/Modules/ML/Services/WPML_Language_Resolver.php`.
- Added `src/Modules/ML/Services/WPML_Service.php`.
- Added `src/Modules/ML/Handlers/WPML_Handler.php`.
- Updated `src/Modules/ML/ML_Module.php` to bind the WPML resolver when WPML is available.
- Added a settings default and settings field for `wpml.extend_ls`.

## Files
- `src/Modules/ML/ML_Module.php`
- `src/Modules/ML/Handlers/WPML_Handler.php`
- `src/Modules/ML/Services/WPML_Language_Resolver.php`
- `src/Modules/ML/Services/WPML_Service.php`
- `src/Common/Settings/Settings_Schema.php`
- `config/settings.php`
- `tests/Unit/ML/WPMLLanguageResolverTest.php`
- `tests/Unit/ML/WPMLServiceTest.php`

## Verification
- `vendor/bin/phpunit tests/Unit/ML/WPMLLanguageResolverTest.php tests/Unit/ML/WPMLServiceTest.php`

## Status
Implemented for WPML. TranslatePress and other multilingual integrations remain out of scope for this slice.

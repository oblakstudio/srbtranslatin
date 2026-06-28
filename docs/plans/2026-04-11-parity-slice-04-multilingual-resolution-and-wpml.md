# Slice 04: Multilingual Resolution and WPML

## Goal
Replace the null multilingual stub with real `src`-level WPML resolution and switcher support.

## Implemented
- Added `src/Modules/ML/Services/WPML_Language_Resolver.php`.
- Added `src/Modules/ML/Services/Polylang_Language_Resolver.php`.
- Added `src/Modules/ML/Services/TranslatePress_Language_Resolver.php`.
- Added `src/Modules/ML/Services/QtranslateX_Language_Resolver.php`.
- Added `src/Modules/ML/Services/Multilingual_Language_Resolver.php`.
- Added `src/Modules/ML/Services/WPML_Service.php`.
- Added `src/Modules/ML/Handlers/WPML_Handler.php`.
- Updated `src/Modules/ML/ML_Module.php` to bind active-language resolvers when WPML, Polylang, TranslatePress, or qTranslateX is available.
- Added a settings default and settings field for `wpml.extend_ls`.

## Files
- `src/Modules/ML/ML_Module.php`
- `src/Modules/ML/Handlers/WPML_Handler.php`
- `src/Modules/ML/Services/WPML_Language_Resolver.php`
- `src/Modules/ML/Services/Polylang_Language_Resolver.php`
- `src/Modules/ML/Services/TranslatePress_Language_Resolver.php`
- `src/Modules/ML/Services/QtranslateX_Language_Resolver.php`
- `src/Modules/ML/Services/Multilingual_Language_Resolver.php`
- `src/Modules/ML/Services/WPML_Service.php`
- `src/Common/Settings/Settings_Schema.php`
- `config/settings.php`
- `tests/Unit/ML/WPMLLanguageResolverTest.php`
- `tests/Unit/ML/AdditionalLanguageResolversTest.php`
- `tests/Unit/ML/WPMLServiceTest.php`

## Verification
- `vendor/bin/phpunit --bootstrap tests/unit-bootstrap.php tests/Unit`

## Status
Implemented for WPML switcher support and active-language locale resolution across WPML, Polylang, TranslatePress, and qTranslateX.

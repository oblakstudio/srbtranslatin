# Script Manager Initialization Design

## Summary
`Script_Manager` is the runtime state holder for translit decisions. It initializes on `srbtranslatin_init`, resolves script by precedence `query param -> cookie -> default`, resolves chosen language from one authoritative multilingual resolver, and falls back to the WordPress locale when no resolver is active.

## Architecture
- `Translit_Module` owns runtime script state and manager initialization.
- `ML_Module` is a top-level module that can provide one authoritative language resolver.
- `Support_Module` is a top-level module for builder/plugin integrations that consume resolved runtime state without determining language authority.

## Resolution Rules
- Accept only `cir` and `lat` as valid scripts.
- If a valid query param is present, it becomes the chosen script and updates the cookie.
- Otherwise use a valid cookie value.
- Otherwise use the configured default script, falling back internally to `cir` if config is invalid.
- Language comes from the injected resolver when present and non-empty, otherwise from `get_locale()`.

## Boundaries
- `Script_Manager` does not discover multilingual plugins directly.
- `ML_Module` owns conditional integration for WPML, Polylang, qTranslate, and similar plugins.
- `Support_Module` owns integrations like Elementor, Bricks, and WPBakery.

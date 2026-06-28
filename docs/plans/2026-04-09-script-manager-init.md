# Script Manager Initialization Implementation Plan

## Summary
Implement runtime translit state through `Script_Manager`, wire it into `srbtranslatin_init`, and split multilingual and support integrations into dedicated top-level modules.

## Tasks
1. Add translit contracts for authoritative language resolution and script-cookie persistence.
2. Implement `Script_Manager` with script precedence, language fallback, and read-only getters.
3. Replace placeholder `Translit_Module` hook logic with manager wiring and `srbtranslatin_init` initialization.
4. Add top-level `ML_Module` and `Support_Module` and import them from `App`.
5. Cover script precedence, cookie persistence, and language fallback with focused unit tests.

## Verification
- Run the unit suite against `tests/unit-bootstrap.php`.
- Confirm the app imports include `ML_Module` and `Support_Module`.
- Confirm manager behavior for query, cookie, default, and resolver fallback paths.

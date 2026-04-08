# Old Architecture

This document describes the legacy `lib/` architecture and the features it implemented before the newer `src/` module-based codebase. It is intended as a migration reference, not as a guarantee that all of this code is still booted by the current plugin entrypoint.

## High-Level Shape

The old plugin was centered around a singleton class, [`lib/SrbTransLatin.php`](/home/venom/Code/plugins/srbtranslatin/lib/SrbTransLatin.php), exposed through the global `STL()` helper in [`lib/Utils/core.php`](/home/venom/Code/plugins/srbtranslatin/lib/Utils/core.php).

Its responsibilities were split into a few coarse services:

- `Core\Script_Manager`: determine the active script (`cir` or `lat`) and whether transliteration should run.
- `Core\Engine`: perform transliteration and wire buffering/hooks for frontend, AJAX, and WooCommerce AJAX.
- `Shortcode\Shortcode_Manager`: register shortcodes that protect or override parts of the output from the global transliteration pass.
- `Core\Multi_Language`: detect WPML / Polylang / TranslatePress and expose the effective locale.
- Frontend helpers: search-query rewriting, title transliteration, menu selector injection.
- UI helpers: widget + template-based script selector rendering.

The current bootstrap in [`srbtranslatin.php`](/home/venom/Code/plugins/srbtranslatin/srbtranslatin.php) already loads the new `src/App.php` module system, so this file should be read as documentation of the old design rather than the current runtime path.

## Request and Script Detection

The legacy plugin treated script selection as request state plus locale state.

- `Core\Script_Manager` reads the requested script from a configurable query arg, defined by `general.url_param`, then persists it into the `stl_script` cookie.
- The selected script is later used to decide whether the page should stay in Cyrillic or be transliterated to Latin.
- `should_transliterate()` returns `true` only when the locale is one of `sr_RS`, `mk_MK`, or `bs_BA` and the selected script is `lat`.
- `SrbTransLatin::is_request()` categorized the current request as `admin`, `ajax`, `cron`, or `frontend`, and the engine used that to load different hook sets.

In practice, the old architecture assumed that source content was primarily Cyrillic and that Latin output was derived at runtime.

## Core Transliteration Engine

[`lib/Core/Engine.php`](/home/venom/Code/plugins/srbtranslatin/lib/Core/Engine.php) was the center of the old behavior.

- On `init`, it decided whether to attach frontend hooks, AJAX hooks, or WooCommerce AJAX hooks.
- On normal frontend requests it started output buffering from `wp_head` and feed head actions, then transliterated the full buffered response in `buffer_end()`.
- It also attached `gettext`, `ngettext`, `gettext_with_context`, and `ngettext_with_context`, so translated strings were transliterated in addition to raw HTML output.
- The actual conversion used `Oblak\Transliterator` through `convert_to_latin()` and `convert_to_cyrillic()`.

The buffering flow mattered because the plugin did not transliterate individual WordPress fields everywhere. Instead, it often let WordPress render normally and then converted the resulting output near the end of the request.

## AJAX Transliteration

This was one of the more unusual legacy features.

- When `advanced.fix_ajax` was enabled, standard WordPress AJAX responses were intercepted by starting an output buffer on `admin_init`.
- For WooCommerce `wc-ajax` requests, the plugin started the same output buffer on `template_redirect`, without consulting the `fix_ajax` setting.
- `ajax_buffer_end()` tried to detect JSON responses first.
- If the response decoded into an array, it recursively transliterated only string values and re-encoded the JSON with `wp_json_encode()`.
- If the response was not JSON, it treated the payload as plain text / markup and passed it through `convert_to_latin()`.

Important caveats from the old implementation:

- JSON objects and arrays were supported only through `json_decode(..., true)`, so the transliteration path was array-oriented.
- The recursive mapper changed values, not keys.
- WooCommerce AJAX was handled as a special case and did not appear to be gated by `advanced.fix_ajax`.

## Frontend Feature Set

The legacy frontend stack was broader than simple output buffering.

### Search Transliteration

[`lib/Frontend/Search_Query_Transliterator.php`](/home/venom/Code/plugins/srbtranslatin/lib/Frontend/Search_Query_Transliterator.php) rewrote WordPress search SQL when `advanced.fix_search` was enabled.

- It only ran on the main query.
- It only ran for Serbian pages.
- It only ran when the search string contained Latin characters.
- It extended the generated `WHERE` clause so Latin search terms would also match their Cyrillic transliterations in `post_title`, `post_excerpt`, and `post_content`.
- It also rewrote `posts_search_orderby` so result ranking considered both Latin and Cyrillic title/content matches.

This is not a simple text transform. It is effectively a custom fork of WordPress search parsing with transliterated alternatives added to the SQL.

### Title Transliteration

[`lib/Frontend/Title_Transliterator.php`](/home/venom/Code/plugins/srbtranslatin/lib/Frontend/Title_Transliterator.php) handled browser/document titles when `advanced.fix_titles` was enabled.

- It filtered `wp_title`.
- It filtered `pre_get_document_title`.
- It filtered `document_title_parts`.

The implementation had two branches:

- For classic themes without `title-tag`, it transliterated the string title on Latin pages.
- For modern title generation, it transliterated each title part unless the active script was Cyrillic.

### Navigation Menu Extension

[`lib/Frontend/Menu_Extender.php`](/home/venom/Code/plugins/srbtranslatin/lib/Frontend/Menu_Extender.php) could append a script switcher into a chosen registered nav-menu location.

- Controlled by the `menu` settings group.
- Only active for Serbian locale.
- Disabled when a multilingual plugin was active.
- Supported two output modes: `submenu` and `inline`.
- Built synthetic menu items whose URLs were the current URL plus the configured script query argument.

The associated settings lived in [`config/settings.php`](/home/venom/Code/plugins/srbtranslatin/config/settings.php): enable/disable extension, choose target menu location, choose selector type, and customize the root menu title.

## Selector UI: Widget, Templates, Helper

The old codebase exposed script-switching UI as reusable presentation helpers.

- [`lib/Utils/core.php`](/home/venom/Code/plugins/srbtranslatin/lib/Utils/core.php) provided `stl_script_selector()`, a template loader that rendered one of the selector templates from `templates/`.
- [`lib/Widget/Selector_Widget.php`](/home/venom/Code/plugins/srbtranslatin/lib/Widget/Selector_Widget.php) wrapped that helper as a WordPress widget.
- Available selector styles were effectively `oneline`, `list`, and `dropdown`.

This part of the legacy architecture has some rough edges worth preserving in migration notes:

- The widget option labels do not align cleanly with the actual template file names.
- The selector helper default contains `online` instead of `oneline`, which looks like a typo.
- [`templates/selector-dropdown.php`](/home/venom/Code/plugins/srbtranslatin/templates/selector-dropdown.php) appears incomplete/buggy, including variable-name mistakes.
- [`templates/selector-list.php`](/home/venom/Code/plugins/srbtranslatin/templates/selector-list.php) closes a `</ul>` without opening one.

So the feature existed, but parts of its rendering layer were fragile.

## Shortcodes and Output Escape Hatches

The output buffer would have been too blunt without opt-out controls, so the old architecture used shortcodes as preservation markers.

[`lib/Shortcode/Shortcode_Manager.php`](/home/venom/Code/plugins/srbtranslatin/lib/Shortcode/Shortcode_Manager.php) registered three shortcode classes and stored placeholder UUID => replacement text mappings. During full-page transliteration, `Engine::buffer_end()` transliterated the full buffer and then restored the protected shortcode fragments with `strtr()`.

Supported shortcodes:

- [`stl_cyr`] and [`stl_cyrillic`] via [`lib/Shortcode/Cyrilizator.php`](/home/venom/Code/plugins/srbtranslatin/lib/Shortcode/Cyrilizator.php): keep enclosed content in Cyrillic even on Latin pages.
- [`stl_translit`] via [`lib/Shortcode/Translator.php`](/home/venom/Code/plugins/srbtranslatin/lib/Shortcode/Translator.php): provide a custom Latin replacement for a Cyrillic source fragment.
- [`stl_selective_output`] and [`stl_show`] via [`lib/Shortcode/Selective_Output.php`](/home/venom/Code/plugins/srbtranslatin/lib/Shortcode/Selective_Output.php): render content only for a specific script.

This pattern is an important part of the old design. Transliteration was global by default, and shortcodes existed to protect specific fragments from that global pass.

## Multilingual Compatibility

[`lib/Core/Multi_Language.php`](/home/venom/Code/plugins/srbtranslatin/lib/Core/Multi_Language.php) detected whether TranslatePress, Polylang, or WPML was active and then delegated locale detection.

### WPML

[`lib/Language/WPML.php`](/home/venom/Code/plugins/srbtranslatin/lib/Language/WPML.php) was the clearest implemented compatibility layer.

- It extended the WPML language switcher through `icl_ls_languages`.
- If Serbian was the primary language, it split it into separate Cyrillic and Latin options.
- Those options were distinguished by different names, different active-state logic, and URLs augmented with the script query parameter.

### TranslatePress

[`lib/Language/TranslatePress.php`](/home/venom/Code/plugins/srbtranslatin/lib/Language/TranslatePress.php) contains broader compatibility logic:

- create a second `language_switcher` post for Serbian Latin,
- normalize requested language values back to `sr_RS`,
- customize names and flags,
- alter generated home URLs to include the script query arg.

However, the legacy bootstrap in [`lib/SrbTransLatin.php`](/home/venom/Code/plugins/srbtranslatin/lib/SrbTransLatin.php) only explicitly instantiated the WPML compatibility class. TranslatePress support existed in code, but from this snapshot it does not look fully wired into the main boot path.

## Settings Surface

The old settings schema lived in [`config/settings.php`](/home/venom/Code/plugins/srbtranslatin/config/settings.php). The notable legacy features exposed there were:

- `menu`: menu extension and selector configuration.
- `media`: transliterate upload filenames, keep script-specific filenames, choose separator, choose transliteration method.
- `wpml`: extend the WPML language switcher.
- `advanced.fix_permalinks`: transliterate permalinks.
- `advanced.fix_search`: transliterated search matching.
- `advanced.fix_ajax`: transliterated AJAX responses.
- `advanced.fix_titles`: transliterated titles.

Not every configured option was actually fully active in the old runtime:

- `fix_search`, `fix_ajax`, `fix_titles`, and WPML extension were clearly implemented.
- filename transliteration and permalink transliteration helpers existed in [`lib/Admin/Admin_Core.php`](/home/venom/Code/plugins/srbtranslatin/lib/Admin/Admin_Core.php), but the relevant hooks were commented out.
- image URL switching existed in `Engine::change_image_urls()`, but the guard around it was effectively disabled.

## Media and Permalink Intent

The old codebase clearly intended to support script-aware media and permalink handling, even though some parts were dormant.

- `Admin_Core::convert_filename_to_latin()` would transliterate uploaded filenames, optionally in cut-Latin mode.
- `Admin_Core::convert_permalink_to_latin()` would transliterate slugs/permalinks when enabled.
- `Engine::change_image_urls()` tried to swap `__cir` to `__lat` in `img.src` and `img.srcset`, suggesting an older convention of keeping script-specific media variants.

This is best understood as a partially implemented feature family rather than a fully active subsystem in the current snapshot.

## Migration Notes

When replacing or porting legacy behavior from `lib/` into `src/`, the main legacy capabilities worth preserving are:

- script detection via query arg + cookie,
- whole-response frontend transliteration,
- AJAX and WooCommerce AJAX transliteration,
- transliterated search SQL,
- title transliteration,
- selector UI generation,
- shortcode-based preservation/override of global transliteration,
- multilingual switcher integration, especially WPML.

Features that need explicit product decisions before migration:

- whether AJAX transliteration should remain output-buffer based,
- whether WooCommerce AJAX should still bypass the `fix_ajax` setting,
- whether dormant media/permalink features should be revived or dropped,
- whether selector widget/template bugs should be preserved for backward compatibility or treated as defects to fix.

<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

defined('ABSPATH') || define('ABSPATH', dirname(__DIR__) . '/');
defined('STL_PATH') || define('STL_PATH', dirname(__DIR__) . '/');
defined('STL_BASE') || define('STL_BASE', 'srbtranslatin/srbtranslatin.php');

$GLOBALS['stl_test_locale'] = 'en_US';
$GLOBALS['stl_test_nav_menus'] = [];
$GLOBALS['stl_test_is_admin'] = true;
$GLOBALS['stl_test_loaded_textdomains'] = [];
$GLOBALS['stl_test_registered_actions'] = [];
$GLOBALS['stl_test_registered_filters'] = [];
$GLOBALS['stl_test_registered_blocks'] = [];
$GLOBALS['stl_test_nav_menu_locations'] = [];
$GLOBALS['stl_test_options'] = [];
$GLOBALS['stl_test_theme_supports'] = [];
$GLOBALS['stl_test_registered_shortcodes'] = [];
$GLOBALS['stl_test_uuid_counter'] = 0;

if (! function_exists('__')) {
    function __(string $text, ?string $domain = null): string {
        return $text;
    }
}

if (! function_exists('_x')) {
    function _x(string $text, string $context, ?string $domain = null): string {
        return $text;
    }
}

if (! function_exists('get_locale')) {
    function get_locale(): string {
        return (string) ($GLOBALS['stl_test_locale'] ?? 'en_US');
    }
}

if (! function_exists('get_registered_nav_menus')) {
    /**
     * @return array<string,string>
     */
    function get_registered_nav_menus(): array {
        return (array) ($GLOBALS['stl_test_nav_menus'] ?? []);
    }
}

if (! function_exists('get_nav_menu_locations')) {
    /**
     * @return array<string,int>
     */
    function get_nav_menu_locations(): array {
        return (array) ($GLOBALS['stl_test_nav_menu_locations'] ?? []);
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool {
        return (bool) ($GLOBALS['stl_test_is_admin'] ?? true);
    }
}

if (! function_exists('get_option')) {
    /**
     * @param mixed $default
     * @return mixed
     */
    function get_option(string $option, mixed $default = false): mixed {
        return $GLOBALS['stl_test_options'][$option] ?? $default;
    }
}

if (! function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain(string $domain, bool $deprecated = false, string $path = ''): bool {
        $GLOBALS['stl_test_loaded_textdomains'][] = [
            'domain' => $domain,
            'deprecated' => $deprecated,
            'path' => $path,
        ];

        return true;
    }
}

if (! function_exists('sanitize_key')) {
    function sanitize_key(string $key): string {
        $key = strtolower($key);

        return preg_replace('/[^a-z0-9_\-]/', '', $key) ?? '';
    }
}

if (! function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $text): string {
        return trim($text);
    }
}

if (! function_exists('wp_unslash')) {
    /**
     * @param mixed $value
     * @return mixed
     */
    function wp_unslash(mixed $value): mixed {
        return $value;
    }
}

if (! function_exists('wp_json_encode')) {
    /**
     * @param mixed $value
     * @return string|false
     */
    function wp_json_encode(mixed $value, int $flags = 0, int $depth = 512): string|false {
        return json_encode($value, $flags | JSON_UNESCAPED_UNICODE, $depth);
    }
}

if (! function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): bool {
        $GLOBALS['stl_test_registered_actions'][] = [
            'hook' => $hook,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        ];

        return true;
    }
}

if (! function_exists('add_filter')) {
    function add_filter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): bool {
        $GLOBALS['stl_test_registered_filters'][] = [
            'hook' => $hook,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        ];

        return true;
    }
}

if (! function_exists('apply_filters')) {
    /**
     * @param mixed $value
     * @return mixed
     */
    function apply_filters(string $hook, mixed $value, mixed ...$args): mixed {
        return $value;
    }
}

if (! function_exists('is_user_logged_in')) {
    function is_user_logged_in(): bool {
        return false;
    }
}

if (! function_exists('wp_setup_nav_menu_item')) {
    /**
     * @param object $item Menu item object.
     * @return object
     */
    function wp_setup_nav_menu_item(object $item): object {
        return $item;
    }
}

if (! function_exists('register_block_type')) {
    /**
     * @param string $block_type Block name or metadata path.
     * @param array<string,mixed> $args Registration args.
     * @return array<string,mixed>
     */
    function register_block_type(string $block_type, array $args = []): array {
        $GLOBALS['stl_test_registered_blocks'][] = [
            'block_type' => $block_type,
            'args' => $args,
        ];

        return [
            'name' => $block_type,
            'args' => $args,
        ];
    }
}

if (! function_exists('add_shortcode')) {
    function add_shortcode(string $tag, callable $callback): bool {
        $GLOBALS['stl_test_registered_shortcodes'][$tag] = $callback;

        return true;
    }
}

if (! function_exists('shortcode_atts')) {
    /**
     * @param array<string,mixed> $pairs
     * @param array<string,mixed> $atts
     * @return array<string,mixed>
     */
    function shortcode_atts(array $pairs, array $atts, string $shortcode = ''): array {
        return array_merge($pairs, $atts);
    }
}

if (! function_exists('wp_generate_uuid4')) {
    function wp_generate_uuid4(): string {
        $GLOBALS['stl_test_uuid_counter']++;

        return 'stl-test-uuid-' . $GLOBALS['stl_test_uuid_counter'];
    }
}

if (! function_exists('current_theme_supports')) {
    function current_theme_supports(string $feature): bool {
        return (bool) ($GLOBALS['stl_test_theme_supports'][$feature] ?? false);
    }
}

if (! function_exists('wp_parse_args')) {
    /**
     * @param array<string,mixed> $args
     * @param array<string,mixed> $defaults
     * @return array<string,mixed>
     */
    function wp_parse_args(array $args, array $defaults = []): array {
        return array_merge($defaults, $args);
    }
}

if (! function_exists('locate_template')) {
    function locate_template(string $template_name): string {
        return '';
    }
}

if (! function_exists('esc_url')) {
    function esc_url(string $url): string {
        return htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (! function_exists('esc_html')) {
    function esc_html(string $text): string {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (! function_exists('wp_kses_post')) {
    function wp_kses_post(string $html): string {
        return $html;
    }
}

if (! function_exists('xwp_has')) {
    function xwp_has(string $id): bool {
        return 'stl' === $id && isset($GLOBALS['stl_test_selector_container']);
    }
}

if (! function_exists('xwp_app')) {
    function xwp_app(string $id): mixed {
        return $GLOBALS['stl_test_selector_container'] ?? null;
    }
}

if (! class_exists('WP_Widget')) {
    class WP_Widget {
    }
}

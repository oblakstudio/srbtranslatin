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
    function get_registered_nav_menus(): array {
        return (array) ($GLOBALS['stl_test_nav_menus'] ?? []);
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool {
        return (bool) ($GLOBALS['stl_test_is_admin'] ?? true);
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

if (! function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): bool {
        return true;
    }
}

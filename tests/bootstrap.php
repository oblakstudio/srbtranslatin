<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$_tests_dir = getenv('WP_TESTS_DIR');

if (false === $_tests_dir || '' === $_tests_dir) {
    $_tests_dir = dirname(__DIR__) . '/.cache/wp-tests/lib';
}

require_once $_tests_dir . '/includes/functions.php';

tests_add_filter(
    'muplugins_loaded',
    static function (): void {
        require_once dirname(__DIR__) . '/srbtranslatin.php';
    },
);

require_once $_tests_dir . '/includes/bootstrap.php';

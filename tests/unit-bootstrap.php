<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

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

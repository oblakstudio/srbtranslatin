<?php
/**
 * Plugin settings contract.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Common\Settings;

interface Plugin_Settings {
    public function get( string $group, string $key, mixed $fallback = null ): mixed;
}

<?php
/**
 * Admin settings context contract.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings;

interface Admin_Context {
    public function get_locale(): string;

    /**
     * @return array<string,string>
     */
    public function get_registered_nav_menus(): array;
}

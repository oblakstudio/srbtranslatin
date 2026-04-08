<?php
/**
 * WordPress-backed admin context.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings;

final class WordPress_Admin_Context implements Admin_Context {
    public function get_locale(): string {
        return (string) \get_locale();
    }

    public function get_registered_nav_menus(): array {
        return \get_registered_nav_menus();
    }
}

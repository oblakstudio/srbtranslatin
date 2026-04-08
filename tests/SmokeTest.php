<?php

declare(strict_types=1);

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

final class SmokeTest extends TestCase {
    public function test_wordpress_and_plugin_bootstrap_together(): void {
        $this->assertTrue(function_exists('add_action'));
        $this->assertTrue(defined('STL_VER'));
        $this->assertTrue(class_exists(STL\App::class));
    }
}

<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Common;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;

final class ArraySettingsTest extends TestCase {
    public function test_returns_saved_values_before_defaults(): void {
        $settings = new Array_Settings(
            [
                'default_script' => 'lat',
                'fix_ajax' => true,
            ],
        );

        self::assertSame('lat', $settings->get('general', 'default_script'));
        self::assertTrue($settings->get('advanced', 'fix_ajax'));
    }

    public function test_falls_back_to_schema_defaults_for_missing_values(): void {
        $settings = new Array_Settings([]);

        self::assertSame('cir', $settings->get('general', 'default_script'));
        self::assertSame('pismo', $settings->get('general', 'url_param'));
        self::assertTrue($settings->get('advanced', 'fix_search'));
        self::assertFalse($settings->get('advanced', 'fix_titles'));
    }

    public function test_uses_explicit_fallback_for_unknown_settings(): void {
        $settings = new Array_Settings([]);

        self::assertSame('fallback', $settings->get('unknown', 'missing', 'fallback'));
    }
}

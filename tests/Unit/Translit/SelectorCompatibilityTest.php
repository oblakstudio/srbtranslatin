<?php
/**
 * SelectorCompatibilityTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use Oblak\STL\Widget\Selector_Widget;
use PHPUnit\Framework\TestCase;
use STL\Translit\Handlers\Selector_Widget_Handler;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';
require_once dirname(__DIR__, 3) . '/lib/Utils/core.php';

/**
 * Test legacy selector compatibility surfaces.
 */
final class SelectorCompatibilityTest extends TestCase {
    public function test_stl_selector_alias_is_available(): void {
        self::assertTrue(\function_exists('stl_selector'));
    }

    public function test_stl_selector_delegates_to_script_selector_return_value(): void {
        $args = [
            'selector_type' => 'list',
            'active_script' => 'cir',
            'cir_link' => '/?pismo=cir',
            'lat_link' => '/?pismo=lat',
        ];

        self::assertSame(\stl_script_selector($args, false), \stl_selector($args, false));
    }

    public function test_selector_widget_handler_registers_legacy_widget(): void {
        $registered = [];
        $handler = new Selector_Widget_Handler(
            static function (string $widget_class) use (&$registered): void {
                $registered[] = $widget_class;
            }
        );

        $handler->register_widget();

        self::assertSame([Selector_Widget::class], $registered);
    }
}

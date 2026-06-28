<?php
/**
 * MenuIntegrationHandlerTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Handlers\Menu_Integration_Handler;
use STL\Translit\Services\Menu_Integration_Service;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test menu integration handler registration.
 */
final class MenuIntegrationHandlerTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_registered_filters'] = [];
    }

    /**
     * @return void
     */
    public function test_register_hooks_adds_classic_and_block_filters(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();
        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );
        $handler = new Menu_Integration_Handler(
            $service,
            static function ( string $hook, callable $callback, int $priority, int $accepted_args ): void {
                $GLOBALS['stl_test_registered_filters'][] = array(
                    'hook' => $hook,
                    'callback' => $callback,
                    'priority' => $priority,
                    'accepted_args' => $accepted_args,
                );
            }
        );

        $handler->register_hooks();

        $hooks = array_column( $GLOBALS['stl_test_registered_filters'], 'hook' );

        self::assertContains( 'wp_get_nav_menu_items', $hooks );
        self::assertContains( 'render_block_core/navigation', $hooks );
        self::assertNotContains( 'render_block', $hooks );
    }
}

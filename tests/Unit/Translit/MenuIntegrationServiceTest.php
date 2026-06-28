<?php
/**
 * MenuIntegrationServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Services\Menu_Integration_Service;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test menu integration service behavior.
 */
final class MenuIntegrationServiceTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_nav_menu_locations'] = [];
        $GLOBALS['stl_test_registered_blocks'] = [];
    }

    /**
     * @return void
     */
    public function test_inject_classic_menu_selector_adds_items_for_target_menu(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $GLOBALS['stl_test_nav_menu_locations'] = array( 'primary' => 55 );
        $menu = (object) array( 'term_id' => 55 );

        $items = array( (object) array( 'ID' => 1, 'title' => 'Home' ) );
        $result = $service->inject_classic_menu_selector( $items, $menu );

        self::assertCount( 3, $result );
        self::assertSame( 'Cyrillic', $result[1]->title );
        self::assertSame( 'Latin', $result[2]->title );
    }

    /**
     * @return void
     */
    public function test_inject_classic_menu_selector_skips_when_selector_is_disabled(): void {
        $manager = new Script_Manager( 'lat', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );
        $GLOBALS['stl_test_nav_menu_locations'] = array( 'primary' => 55 );
        $menu = (object) array( 'term_id' => 55 );

        $items = array( (object) array( 'ID' => 1, 'title' => 'Home' ) );
        $result = $service->inject_classic_menu_selector( $items, $menu );

        self::assertSame( $items, $result );
    }

    /**
     * @return void
     */
    public function test_inject_navigation_block_selector_appends_markup(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $result = $service->inject_navigation_block_selector( '<nav class="wp-block-navigation"></nav>', array() );

        self::assertStringContainsString( 'stl-block-selector', $result );
        self::assertStringContainsString( '?pismo=cir', $result );
        self::assertStringContainsString( '?pismo=lat', $result );
    }

    /**
     * @return void
     */
    public function test_inject_navigation_block_selector_skips_when_extension_is_disabled(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, false, 'primary', 'inline', 'Script' );

        $result = $service->inject_navigation_block_selector( '<nav class="wp-block-navigation"></nav>', array() );

        self::assertSame( '<nav class="wp-block-navigation"></nav>', $result );
    }

    /**
     * @return void
     */
    public function test_inject_navigation_block_selector_skips_when_selector_markup_is_already_present(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );
        $content = '<nav class="wp-block-navigation"><div class="stl-script-selector-block">selector</div></nav>';

        $result = $service->inject_navigation_block_selector( $content, array( 'blockName' => 'core/navigation' ) );

        self::assertSame( $content, $result );
    }

    /**
     * @return void
     */
    public function test_register_selector_block_registers_metadata_path_and_render_callback(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $service->register_selector_block();

        self::assertCount( 1, $GLOBALS['stl_test_registered_blocks'] );
        self::assertSame( STL_PATH . 'assets/blocks/script-selector/build', $GLOBALS['stl_test_registered_blocks'][0]['block_type'] );
        self::assertSame( array( $service, 'render_selector_block' ), $GLOBALS['stl_test_registered_blocks'][0]['args']['render_callback'] );
    }

    /**
     * @return void
     */
    public function test_render_selector_block_returns_selector_markup_when_enabled(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $result = $service->render_selector_block();

        self::assertStringContainsString( 'stl-script-selector-block', $result );
        self::assertStringContainsString( 'stl-script-link is-active', $result );
        self::assertStringContainsString( '?pismo=lat', $result );
    }

    /**
     * @return void
     */
    public function test_render_selector_block_ignores_menu_extension_flag_for_explicit_block(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, false, 'primary', 'inline', 'Script' );

        $result = $service->render_selector_block();

        self::assertStringContainsString( 'stl-script-selector-block', $result );
    }

    /**
     * @return void
     */
    public function test_render_selector_block_defaults_to_inline_when_menu_type_is_submenu(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'submenu', 'Script' );

        $result = $service->render_selector_block();

        self::assertStringContainsString( 'stl-block-selector stl-script-selector-block is-mode-inline', $result );
        self::assertStringNotContainsString( '<select', $result );
        self::assertStringNotContainsString( '<ul', $result );
    }

    /**
     * @return void
     */
    public function test_render_selector_block_uses_custom_labels(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $result = $service->render_selector_block(
            array(
                'cyrillicLabel' => 'Ћирилица',
                'latinLabel'    => 'Латиница',
            )
        );

        self::assertStringContainsString( 'Ћирилица', $result );
        self::assertStringContainsString( 'Латиница', $result );
    }

    /**
     * @return void
     */
    public function test_render_selector_block_supports_list_mode(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $result = $service->render_selector_block(
            array(
                'displayMode' => 'list',
            )
        );

        self::assertStringContainsString( 'is-mode-list', $result );
        self::assertStringContainsString( '<ul class="stl-script-selector-list">', $result );
        self::assertStringContainsString( '<li class="stl-script-selector-item">', $result );
    }

    /**
     * @return void
     */
    public function test_render_selector_block_supports_dropdown_mode(): void {
        $manager = new Script_Manager( 'both', 'cir', '', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $result = $service->render_selector_block(
            array(
                'displayMode' => 'dropdown',
            )
        );

        self::assertStringContainsString( 'is-mode-dropdown', $result );
        self::assertStringContainsString( '<select class="stl-script-selector-select"', $result );
        self::assertStringContainsString( 'onchange="window.location.href=this.value"', $result );
        self::assertStringContainsString( '<option value="/?pismo=cir" selected="selected">Cyrillic</option>', $result );
    }

    /**
     * @return void
     */
    public function test_render_compat_selector_supports_legacy_oneline_mode_and_inactive_only(): void {
        $manager = new Script_Manager( 'both', 'cir', 'cir', '', 'pismo' );
        $manager->initialize();

        $service = new Menu_Integration_Service( $manager, true, 'primary', 'inline', 'Script' );

        $result = $service->render_compat_selector(
            array(
                'selector_type' => 'oneline',
                'inactive_only' => true,
                'cir_caption'   => 'Ћирилица',
                'lat_caption'   => 'Latinica',
                'separator'     => '<span>|</span>',
            )
        );

        self::assertStringContainsString( '<div class="stl-script-selector">', $result );
        self::assertStringContainsString( 'Latinica', $result );
        self::assertStringNotContainsString( 'Ћирилица', $result );
        self::assertStringNotContainsString( '<span>|</span>', $result );
    }
}

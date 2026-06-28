<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Core;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Plugin_Settings;
use STL\Core\Core_Module;
use STL\Core\Services\Settings_Page_Factory;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Translit_Module;
use WPTechnix\WP_Settings_Builder\Settings_Builder;
use WPTechnix\WP_Settings_Builder\Settings_Page;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

final class CoreModuleTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_registered_actions'] = [];
        $this->resetBuilderInstances();
    }

    protected function tearDown(): void {
        $GLOBALS['stl_test_registered_actions'] = [];
        $this->resetBuilderInstances();

        parent::tearDown();
    }

    public function test_configure_registers_active_settings_services(): void {
        $config = Core_Module::configure();

        self::assertArrayHasKey('stl.settings', $config);
        self::assertArrayHasKey(Settings_Page::class, $config);
        self::assertArrayHasKey(Plugin_Settings::class, $config);
    }

    public function test_script_manager_can_be_resolved_without_settings_page_cycle(): void {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions(Core_Module::configure());
        $builder->addDefinitions(Translit_Module::configure());

        $container = $builder->build();
        $manager = $container->get(Script_Manager::class);

        self::assertInstanceOf(Script_Manager::class, $manager);
    }

    public function test_load_settings_initializes_injected_page(): void {
        $page = Settings_Page_Factory::make([
            'page' => [
                'option_name' => 'stl_settings',
                'slug' => 'stl-settings',
                'page_title' => 'Latinisation',
                'menu_title' => 'Settings',
                'parent_slug' => 'options-general.php',
                'capability' => 'manage_options',
            ],
            'tabs' => [],
            'sections' => [],
            'fields' => [],
        ]);

        (new Core_Module())->load_settings($page);

        $hooks = array_column($GLOBALS['stl_test_registered_actions'], 'hook');

        self::assertContains('admin_menu', $hooks);
        self::assertContains('admin_init', $hooks);
        self::assertContains('admin_enqueue_scripts', $hooks);
    }

    private function resetBuilderInstances(): void {
        $reflection = new \ReflectionClass(Settings_Builder::class);
        $property = $reflection->getProperty('instances');
        $property->setAccessible(true);
        $property->setValue([]);
    }
}

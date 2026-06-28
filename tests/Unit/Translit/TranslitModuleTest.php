<?php
/**
 * TranslitModuleTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Handlers\Media_Permalink_Handler;
use STL\Translit\Services\Media_Permalink_Service;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Translit_Service;
use STL\Translit\Translit_Module;
use XWP\DI\Decorators\Module;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test translit module wiring.
 */
final class TranslitModuleTest extends TestCase {
    /**
     * @return void
     */
    public function test_initialize_script_manager_initializes_runtime_state(): void {
        $manager = new Script_Manager('both', 'cir', 'lat', '', 'pismo');

        (new Translit_Module())->initialize_script_manager($manager);

        self::assertSame('lat', $manager->get_script());
        self::assertSame('query', $manager->get_script_source());
    }

    /**
     * @return void
     */
    public function test_configure_exposes_ajax_setting_for_translit_service(): void {
        $config = Translit_Module::configure();

        self::assertArrayHasKey(Translit_Service::class, $config);
        self::assertArrayHasKey(Media_Permalink_Service::class, $config);
    }

    /**
     * @return void
     */
    public function test_module_registers_media_permalink_handler(): void {
        $module = (new \ReflectionClass(Translit_Module::class))
            ->getAttributes(Module::class)[0]
            ->newInstance();

        self::assertContains(Media_Permalink_Handler::class, $module->handlers);
    }
}

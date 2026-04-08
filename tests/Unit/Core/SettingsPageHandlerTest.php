<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use STL\Core\Settings_Page_Config;
use STL\Core\Settings_Page_Handler;
use WPTechnix\WP_Settings_Builder\Settings_Builder;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

final class SettingsPageHandlerTest extends TestCase {
    protected function setUp(): void {
        $this->resetBuilderInstances();
        $GLOBALS['stl_test_loaded_textdomains'] = [];
    }

    protected function tearDown(): void {
        $this->resetBuilderInstances();
        $GLOBALS['stl_test_loaded_textdomains'] = [];

        parent::tearDown();
    }

    public function test_boot_registers_settings_page_from_config_and_loads_translations(): void {
        $handler = new Settings_Page_Handler(
            new Settings_Builder(),
            new Fake_Settings_Page_Config([
                'page' => [
                    'option_name' => 'stl_settings',
                    'slug' => 'stl-settings',
                    'page_title' => 'Latinisation',
                    'menu_title' => 'Settings',
                    'parent_slug' => 'options-general.php',
                    'capability' => 'manage_options',
                ],
                'tabs' => [
                    [
                        'id' => 'general',
                        'title' => 'General',
                        'icon' => 'dashicons-admin-generic',
                    ],
                ],
                'sections' => [
                    [
                        'id' => 'general',
                        'title' => 'General settings',
                        'description' => 'Main plugin settings',
                        'tab' => 'general',
                    ],
                ],
                'fields' => [
                    [
                        'id' => 'url_param',
                        'section' => 'general',
                        'type' => 'text',
                        'title' => 'URL Parameter',
                        'extras' => [
                            'default' => 'pismo',
                        ],
                    ],
                ],
            ]),
        );

        $page = $handler->boot();

        self::assertNotNull($page);
        self::assertCount(1, $GLOBALS['stl_test_loaded_textdomains']);
        self::assertSame('srbtranslatin', $GLOBALS['stl_test_loaded_textdomains'][0]['domain']);
        self::assertSame('srbtranslatin/languages', $GLOBALS['stl_test_loaded_textdomains'][0]['path']);
        self::assertSame(['general'], array_keys($this->readProperty($page, 'tabs')));
        self::assertSame(['general'], array_keys($this->readProperty($page, 'sections')));
        self::assertSame(['url_param'], array_keys($this->readProperty($page, 'fields')));
    }

    public function test_boot_reuses_existing_page_instance(): void {
        $handler = new Settings_Page_Handler(
            new Settings_Builder(),
            new Fake_Settings_Page_Config([
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
            ]),
        );

        $first = $handler->boot();
        $second = $handler->boot();

        self::assertSame($first, $second);
    }

    private function resetBuilderInstances(): void {
        $reflection = new \ReflectionClass(Settings_Builder::class);
        $property = $reflection->getProperty('instances');
        $property->setAccessible(true);
        $property->setValue([]);
    }

    /**
     * @return array<string,mixed>
     */
    private function readProperty(object $object, string $property): array {
        $reflection = new \ReflectionObject($object);
        $target = $reflection->getProperty($property);
        $target->setAccessible(true);

        return $target->getValue($object);
    }
}

final class Fake_Settings_Page_Config implements Settings_Page_Config {
    /**
     * @param array<string,mixed> $schema
     */
    public function __construct(
        private array $schema,
    ) {
    }

    public function get(): array {
        return $this->schema;
    }
}

<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use STL\Core\Services\Settings_Page_Factory;
use WPTechnix\WP_Settings_Builder\Settings_Builder;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

final class SettingsPageFactoryTest extends TestCase {
    protected function setUp(): void {
        $this->resetBuilderInstances();
    }

    protected function tearDown(): void {
        $this->resetBuilderInstances();

        parent::tearDown();
    }

    public function test_make_page_builds_settings_page_from_config(): void {
        $page = Settings_Page_Factory::make([
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
        ]);

        self::assertSame($page, Settings_Builder::get_instance('stl-settings'));
        self::assertSame(['general'], array_keys($this->readProperty($page, 'tabs')));
        self::assertSame(['general'], array_keys($this->readProperty($page, 'sections')));
        self::assertSame(['url_param'], array_keys($this->readProperty($page, 'fields')));
        self::assertSame('stl_settings', $this->readScalarProperty($page, 'option_name'));
        self::assertSame('stl-settings', $this->readScalarProperty($page, 'page_slug'));
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

    private function readScalarProperty(object $object, string $property): string {
        $reflection = new \ReflectionObject($object);
        $target = $reflection->getProperty($property);
        $target->setAccessible(true);

        return (string) $target->getValue($object);
    }
}

<?php
/**
 * MediaHandlerTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Handlers\Media_Handler;
use STL\Translit\Services\Media_Service;
use STL\Translit\Services\Script_Manager;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test media handler behavior and wiring.
 */
final class MediaHandlerTest extends TestCase {
    /**
     * @return void
     */
    public function test_transliterate_filename_delegates_to_service(): void {
        $handler = new Media_Handler($this->create_service());

        self::assertSame('cirilica.jpg', $handler->transliterate_filename('ћирилица.jpg'));
    }

    /**
     * @return void
     */
    public function test_rewrite_content_image_urls_delegates_to_service(): void {
        $handler = new Media_Handler($this->create_service());

        self::assertSame(
            '<img src="/uploads/photo-lat.jpg">',
            $handler->rewrite_content_image_urls('<img src="/uploads/photo-cir.jpg">')
        );
    }

    /**
     * @return void
     */
    public function test_should_rewrite_content_reads_media_transliteration_method(): void {
        $settings = new Array_Settings(
            array(
                'transliteration_method' => 'content',
            )
        );

        self::assertTrue(Media_Handler::should_rewrite_content($settings));
    }

    /**
     * @return void
     */
    public function test_transliterate_filename_uses_decorated_sanitize_file_name_filter(): void {
        $attributes = array_map(
            static fn($attribute) => $attribute->newInstance(),
            (new \ReflectionMethod(Media_Handler::class, 'transliterate_filename'))->getAttributes(Filter::class)
        );

        self::assertCount(1, $attributes);
        self::assertSame('sanitize_file_name', $attributes[0]->tag);
        self::assertSame(999, $attributes[0]->priority);
        self::assertSame(1, $attributes[0]->args);
    }

    /**
     * @return void
     */
    public function test_rewrite_content_image_urls_uses_decorated_the_content_filter(): void {
        $attributes = array_map(
            static fn($attribute) => $attribute->newInstance(),
            (new \ReflectionMethod(Media_Handler::class, 'rewrite_content_image_urls'))->getAttributes(Filter::class)
        );

        self::assertCount(1, $attributes);
        self::assertSame('the_content', $attributes[0]->tag);
        self::assertSame(9999, $attributes[0]->priority);
        self::assertSame(array(Media_Handler::class, 'should_rewrite_content'), $attributes[0]->conditional);
    }

    private function create_service(): Media_Service {
        return new Media_Service(
            $this->create_manager(),
            new Array_Settings(
                array(
                    'transliterate_uploads' => true,
                    'separate_uploads'      => true,
                )
            )
        );
    }

    private function create_manager(): Script_Manager {
        $manager = new Script_Manager(
            'both',
            'cir',
            'lat',
            '',
            'pismo',
            null,
            null,
            static fn(): string => 'sr_RS'
        );
        $manager->initialize();

        return $manager;
    }
}

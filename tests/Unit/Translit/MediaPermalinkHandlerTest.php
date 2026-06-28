<?php
/**
 * MediaPermalinkHandlerTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Handlers\Media_Permalink_Handler;
use STL\Translit\Services\Media_Permalink_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test media and permalink hook registration.
 */
final class MediaPermalinkHandlerTest extends TestCase {
    /**
     * @return void
     */
    public function test_sanitize_file_name_delegates_to_the_service(): void {
        self::assertTrue(class_exists(Media_Permalink_Handler::class));

        $service = new Media_Permalink_Service(new Array_Settings(['transliterate_uploads' => true]));
        $handler = new Media_Permalink_Handler($service);

        self::assertSame('Cirilica Sabac.jpg', $handler->sanitize_file_name('Ћирилица Шабац.jpg', 'Ћирилица Шабац.jpg'));
    }

    /**
     * @return void
     */
    public function test_sanitize_title_delegates_to_the_service(): void {
        self::assertTrue(class_exists(Media_Permalink_Handler::class));

        $service = new Media_Permalink_Service(new Array_Settings(['fix_permalinks' => true]));
        $handler = new Media_Permalink_Handler($service);

        self::assertSame('Cirilica Sabac', $handler->sanitize_title('Ћирилица Шабац', 'Ћирилица Шабац', 'save'));
    }

    /**
     * @return void
     */
    public function test_sanitize_file_name_uses_decorated_global_filter(): void {
        self::assertTrue(class_exists(Media_Permalink_Handler::class));

        $filter = (new \ReflectionMethod(Media_Permalink_Handler::class, 'sanitize_file_name'))
            ->getAttributes(Filter::class)[0]
            ->newInstance();

        self::assertSame('sanitize_file_name', $filter->tag);
        self::assertSame(999, $filter->priority);
        self::assertSame(Filter::CTX_GLOBAL, $filter->context);
        self::assertSame(2, $filter->args);
        self::assertSame(Action::INV_PROXIED, $filter->invoke);
    }

    /**
     * @return void
     */
    public function test_sanitize_title_uses_decorated_global_filter_before_default_slug_sanitizer(): void {
        self::assertTrue(class_exists(Media_Permalink_Handler::class));

        $filter = (new \ReflectionMethod(Media_Permalink_Handler::class, 'sanitize_title'))
            ->getAttributes(Filter::class)[0]
            ->newInstance();

        self::assertSame('sanitize_title', $filter->tag);
        self::assertSame(9, $filter->priority);
        self::assertSame(Filter::CTX_GLOBAL, $filter->context);
        self::assertSame(3, $filter->args);
        self::assertSame(Action::INV_PROXIED, $filter->invoke);
    }
}

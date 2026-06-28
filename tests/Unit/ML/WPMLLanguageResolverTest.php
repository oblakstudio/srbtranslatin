<?php
/**
 * WPMLLanguageResolverTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\ML;

use PHPUnit\Framework\TestCase;
use STL\ML\Services\WPML_Language_Resolver;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test WPML language resolution.
 */
final class WPMLLanguageResolverTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_wpml_language'] = null;
        $GLOBALS['stl_test_locale'] = 'en_US';
    }

    /**
     * @return void
     */
    public function test_resolve_language_maps_serbian_language_code_to_locale(): void {
        $resolver = new WPML_Language_Resolver(static fn (): string => 'sr');

        self::assertSame('sr_RS', $resolver->resolve_language());
    }

    /**
     * @return void
     */
    public function test_resolve_language_maps_macedonian_language_code_to_locale(): void {
        $resolver = new WPML_Language_Resolver(static fn (): string => 'mk');

        self::assertSame('mk_MK', $resolver->resolve_language());
    }

    /**
     * @return void
     */
    public function test_resolve_language_returns_null_for_unknown_wpml_language(): void {
        $resolver = new WPML_Language_Resolver(static fn (): string => 'en');

        self::assertNull($resolver->resolve_language());
    }
}

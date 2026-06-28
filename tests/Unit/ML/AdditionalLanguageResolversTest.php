<?php
/**
 * AdditionalLanguageResolversTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\ML;

use PHPUnit\Framework\TestCase;
use STL\ML\Services\Multilingual_Language_Resolver;
use STL\ML\Services\Polylang_Language_Resolver;
use STL\ML\Services\QtranslateX_Language_Resolver;
use STL\ML\Services\TranslatePress_Language_Resolver;
use STL\Translit\Contracts\Resolves_Language;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test non-WPML multilingual language resolution.
 */
final class AdditionalLanguageResolversTest extends TestCase {
    public function test_polylang_resolver_uses_current_locale(): void {
        $resolver = new Polylang_Language_Resolver(static fn(): string => 'sr_RS');

        self::assertSame('sr_RS', $resolver->resolve_language());
    }

    public function test_polylang_resolver_maps_language_code_to_locale(): void {
        $resolver = new Polylang_Language_Resolver(static fn(): string => 'bs');

        self::assertSame('bs_BA', $resolver->resolve_language());
    }

    public function test_translatepress_resolver_uses_current_language_locale(): void {
        $resolver = new TranslatePress_Language_Resolver(static fn(): string => 'mk_MK');

        self::assertSame('mk_MK', $resolver->resolve_language());
    }

    public function test_qtranslatex_resolver_uses_configured_locale_for_current_language(): void {
        $resolver = new QtranslateX_Language_Resolver(
            static fn(): string => 'sr',
            static fn(): array => ['locale' => ['sr' => 'sr_RS']]
        );

        self::assertSame('sr_RS', $resolver->resolve_language());
    }

    public function test_composite_resolver_returns_first_available_language(): void {
        $resolver = new Multilingual_Language_Resolver([
            new FixedLanguageResolver(null),
            new FixedLanguageResolver('mk_MK'),
            new FixedLanguageResolver('sr_RS'),
        ]);

        self::assertSame('mk_MK', $resolver->resolve_language());
    }
}

final class FixedLanguageResolver implements Resolves_Language {
    public function __construct(private ?string $language) {
    }

    public function resolve_language(): ?string {
        return $this->language;
    }
}

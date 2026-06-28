<?php
/**
 * ScriptManagerTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Contracts\Persists_Script_Cookie;
use STL\Translit\Contracts\Resolves_Language;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test translit script manager behavior.
 */
final class ScriptManagerTest extends TestCase {
    /**
     * @return void
     */
    protected function setUp(): void {
        $GLOBALS['stl_test_locale'] = 'en_US';
    }

    /**
     * @return void
     */
    public function test_query_param_overrides_cookie_and_default_and_persists_cookie(): void {
        $persister = new RecordingCookiePersister();
        $manager = new Script_Manager('both', 'cir', 'lat', 'cir', 'pismo', null, $persister);

        $manager->initialize();

        self::assertSame('lat', $manager->get_script());
        self::assertSame('query', $manager->get_script_source());
        self::assertSame(['lat'], $persister->values);
    }

    /**
     * @return void
     */
    public function test_cookie_is_used_when_query_param_is_missing(): void {
        $persister = new RecordingCookiePersister();
        $manager = new Script_Manager('both', 'cir', '', 'lat', 'pismo', null, $persister);

        $manager->initialize();

        self::assertSame('lat', $manager->get_script());
        self::assertSame('cookie', $manager->get_script_source());
        self::assertSame([], $persister->values);
    }

    /**
     * @return void
     */
    public function test_default_is_used_when_query_param_and_cookie_are_invalid(): void {
        $manager = new Script_Manager('both', 'cir', 'bad-value', 'wrong', 'pismo');

        $manager->initialize();

        self::assertSame('cir', $manager->get_script());
        self::assertSame('default', $manager->get_script_source());
    }

    /**
     * @return void
     */
    public function test_invalid_default_script_falls_back_to_cyrillic(): void {
        $manager = new Script_Manager('both', 'wrong', '', '', 'pismo');

        $manager->initialize();

        self::assertSame('cir', $manager->get_script());
        self::assertSame('default', $manager->get_script_source());
    }

    /**
     * @return void
     */
    public function test_uses_injected_language_resolver_when_available(): void {
        $manager = new Script_Manager(
            'both',
            'cir',
            '',
            '',
            'pismo',
            new FixedLanguageResolver('sr_RS')
        );

        $manager->initialize();

        self::assertSame('sr_RS', $manager->get_language());
    }

    /**
     * @return void
     */
    public function test_null_language_resolver_value_falls_back_to_wordpress_locale(): void {
        $GLOBALS['stl_test_locale'] = 'bs_BA';
        $manager = new Script_Manager(
            'both',
            'cir',
            '',
            '',
            'pismo',
            new FixedLanguageResolver(null),
            null,
            static fn(): string => (string) ($GLOBALS['stl_test_locale'] ?? 'en_US')
        );

        $manager->initialize();

        self::assertSame('bs_BA', $manager->get_language());
    }

    /**
     * @return void
     */
    public function test_empty_language_resolver_value_falls_back_to_wordpress_locale(): void {
        $GLOBALS['stl_test_locale'] = 'sr_RS';
        $manager = new Script_Manager(
            'both',
            'cir',
            '',
            '',
            'pismo',
            new FixedLanguageResolver(''),
            null,
            static fn(): string => (string) ($GLOBALS['stl_test_locale'] ?? 'en_US')
        );

        $manager->initialize();

        self::assertSame('sr_RS', $manager->get_language());
    }

    /**
     * @return void
     */
    public function test_falls_back_to_wordpress_locale_when_no_language_resolver_is_available(): void {
        $GLOBALS['stl_test_locale'] = 'bs_BA';
        $manager = new Script_Manager(
            'both',
            'cir',
            '',
            '',
            'pismo',
            null,
            null,
            static fn(): string => (string) ($GLOBALS['stl_test_locale'] ?? 'en_US')
        );

        $manager->initialize();

        self::assertSame('bs_BA', $manager->get_language());
    }

    /**
     * @return void
     */
    public function test_get_url_param_returns_configured_parameter(): void {
        $manager = new Script_Manager('both', 'cir', '', '', 'pismo');

        self::assertSame('pismo', $manager->get_url_param());
    }

    /**
     * @return void
     */
    public function test_latin_mode_forces_latin_script_and_skips_cookie_persistence(): void {
        $persister = new RecordingCookiePersister();
        $manager = new Script_Manager(
            'lat',
            'cir',
            'cir',
            'cir',
            'pismo',
            null,
            $persister,
            static fn(): string => 'sr_RS'
        );

        $manager->initialize();

        self::assertSame('lat', $manager->get_script());
        self::assertSame('mode', $manager->get_script_source());
        self::assertTrue($manager->is_latin());
        self::assertTrue($manager->should_transliterate());
        self::assertSame([], $persister->values);
    }

    /**
     * @return void
     */
    public function test_should_transliterate_returns_true_for_latin_serbian_locale(): void {
        $manager = $this->create_manager('lat', 'sr_RS');

        self::assertTrue($manager->should_transliterate());
    }

    /**
     * @return void
     */
    public function test_should_transliterate_returns_true_for_latin_macedonian_locale(): void {
        $manager = $this->create_manager('lat', 'mk_MK');

        self::assertTrue($manager->should_transliterate());
    }

    /**
     * @return void
     */
    public function test_should_transliterate_returns_true_for_latin_bosnian_locale(): void {
        $manager = $this->create_manager('lat', 'bs_BA');

        self::assertTrue($manager->should_transliterate());
    }

    /**
     * @return void
     */
    public function test_should_transliterate_returns_false_for_latin_unsupported_locale(): void {
        $manager = $this->create_manager('lat', 'en_US');

        self::assertFalse($manager->should_transliterate());
    }

    /**
     * @return void
     */
    public function test_should_transliterate_returns_false_for_cyrillic_supported_locale(): void {
        $manager = $this->create_manager('cir', 'sr_RS');

        self::assertFalse($manager->should_transliterate());
    }

    private function create_manager(string $script, string $locale): Script_Manager {
        $manager = new Script_Manager(
            'both',
            'cir',
            $script,
            '',
            'pismo',
            null,
            null,
            static fn(): string => $locale
        );
        $manager->initialize();

        return $manager;
    }
}

/**
 * Fixed language resolver test double.
 */
final class FixedLanguageResolver implements Resolves_Language {
    /**
     * @param string|null $language Resolved language.
     */
    public function __construct(private ?string $language) {
    }

    /**
     * @return string|null
     */
    public function resolve_language(): ?string {
        return $this->language;
    }
}

/**
 * Cookie persister test double.
 */
final class RecordingCookiePersister implements Persists_Script_Cookie {
    /** @var string[] */
    public array $values = [];

    /**
     * @param string $script Selected script.
     * @return void
     */
    public function persist(string $script): void {
        $this->values[] = $script;
    }
}

<?php
/**
 * SearchQueryServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Contracts\Resolves_Language;
use STL\Translit\Services\Search_Query_Service;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test transliterated search SQL generation.
 */
final class SearchQueryServiceTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_locale'] = 'sr_RS';
    }

    /**
     * @return void
     */
    public function test_filter_posts_search_expands_latin_terms_with_cyrillic_variants_for_serbian_main_query(): void {
        $service = $this->create_service(true, 'sr_RS');
        $query = new FakeSearchQuery('Ljubav', true);

        $result = $service->filter_posts_search('', $query);

        self::assertStringContainsString("posts.post_title LIKE '%Ljubav%'", $result);
        self::assertStringContainsString("posts.post_title LIKE '%Љубав%'", $result);
        self::assertStringContainsString("posts.post_excerpt LIKE '%Љубав%'", $result);
        self::assertStringContainsString("posts.post_password = ''", $result);
    }

    /**
     * @return void
     */
    public function test_filter_posts_search_skips_non_serbian_queries(): void {
        $service = $this->create_service(true, 'en_US');
        $query = new FakeSearchQuery('Ljubav', true);

        self::assertSame('original', $service->filter_posts_search('original', $query));
    }

    /**
     * @return void
     */
    public function test_filter_posts_search_skips_non_main_queries(): void {
        $service = $this->create_service(true, 'sr_RS');
        $query = new FakeSearchQuery('Ljubav', false);

        self::assertSame('original', $service->filter_posts_search('original', $query));
    }

    /**
     * @return void
     */
    public function test_filter_posts_search_orderby_includes_cyrillic_relevance_for_multi_term_latin_search(): void {
        $service = $this->create_service(true, 'sr_RS');
        $query = new FakeSearchQuery('Ljubav Nada', true);

        $service->filter_posts_search('', $query);
        $orderby = $service->filter_posts_search_orderby('original', $query);

        self::assertStringContainsString("posts.post_title LIKE '%Ljubav Nada%'", $orderby);
        self::assertStringContainsString("posts.post_title LIKE '%Љубав Нада%'", $orderby);
        self::assertStringContainsString('ELSE 6 END', $orderby);
    }

    private function create_service(bool $fix_search, string $language): Search_Query_Service {
        $manager = new Script_Manager(
            'both',
            'cir',
            '',
            '',
            'pismo',
            new FixedSearchLanguageResolver($language)
        );
        $manager->initialize();

        return new Search_Query_Service(
            $manager,
            new Array_Settings(
                array(
                    'fix_search' => $fix_search,
                )
            ),
            null,
            new FakeWpdb()
        );
    }
}

/**
 * Fixed language resolver test double.
 */
final class FixedSearchLanguageResolver implements Resolves_Language {
    public function __construct(private ?string $language) {
    }

    public function resolve_language(): ?string {
        return $this->language;
    }
}

/**
 * Fake WP_Query replacement for unit tests.
 */
final class FakeSearchQuery {
    /** @var array<string,mixed> */
    public array $query_vars;

    public function __construct(string $search, private bool $main_query) {
        $this->query_vars = array(
            's' => $search,
            'sentence' => false,
            'exact' => false,
        );
    }

    public function is_main_query(): bool {
        return $this->main_query;
    }

    public function get(string $key): mixed {
        return $this->query_vars[$key] ?? null;
    }
}

/**
 * Tiny wpdb test double for SQL generation.
 */
final class FakeWpdb {
    public string $posts = 'posts';

    public function esc_like(string $value): string {
        return addcslashes($value, '%_');
    }

    public function prepare(string $query, string ...$args): string {
        foreach ($args as $arg) {
            $query = preg_replace("/%s/", "'" . str_replace("'", "\\'", $arg) . "'", $query, 1) ?? $query;
        }

        return preg_replace('/\s+/', ' ', trim($query)) ?? trim($query);
    }
}

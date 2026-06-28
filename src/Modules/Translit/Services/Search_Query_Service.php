<?php
/**
 * Search_Query_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use Oblak\Transliterator;
use STL\Common\Settings\Plugin_Settings;

/**
 * Generate transliterated search SQL for Serbian content.
 */
final class Search_Query_Service {
    /**
     * Constructor.
     *
     * @param Script_Manager       $script_manager Runtime script/language manager.
     * @param Plugin_Settings      $settings Plugin settings storage.
     * @param Transliterator|null  $transliterator Transliterator instance.
     * @param mixed                $wpdb Optional database adapter for tests.
     */
    public function __construct(
        private Script_Manager $script_manager,
        private Plugin_Settings $settings,
        private ?Transliterator $transliterator = null,
        private mixed $wpdb = null,
    ) {
        $this->transliterator ??= Transliterator::instance();
    }

    /**
     * Expand Latin search terms to also match Cyrillic content.
     *
     * @param string $search Existing SQL fragment.
     * @param object $wp_query Query object.
     * @return string
     */
    public function filter_posts_search( string $search, object $wp_query ): string {
        if ( ! $this->should_expand_search( $wp_query ) ) {
            return $search;
        }

        $query_vars = (array) ( $wp_query->query_vars ?? array() );

        return $this->parse_search( $query_vars, $wp_query );
    }

    /**
     * Adjust title/content relevance ordering for transliterated terms.
     *
     * @param string $orderby Existing SQL fragment.
     * @param object $wp_query Query object.
     * @return string
     */
    public function filter_posts_search_orderby( string $orderby, object $wp_query ): string {
        if ( ! $this->should_expand_search( $wp_query ) ) {
            return $orderby;
        }

        $query_vars = (array) ( $wp_query->query_vars ?? array() );

        return $this->parse_search_order( $query_vars );
    }

    /**
     * Determine whether search expansion should run.
     *
     * @param object $wp_query Query object.
     * @return bool
     */
    private function should_expand_search( object $wp_query ): bool {
        $search = '';

        if ( \is_callable( array( $wp_query, 'get' ) ) ) {
            $search = (string) $wp_query->get( 's' );
        } elseif ( isset( $wp_query->query_vars['s'] ) ) {
            $search = (string) $wp_query->query_vars['s'];
        }

        return (bool) $this->settings->get( 'advanced', 'fix_search', false )
            && $this->is_serbian_language()
            && \is_callable( array( $wp_query, 'is_main_query' ) )
            && $wp_query->is_main_query()
            && '' !== $search
            && $this->is_latin_string( $search );
    }

    /**
     * Generate a transliterated WHERE clause.
     *
     * @param array<string,mixed> $q Query variables.
     * @param object              $wp_query Query object for in-place state updates.
     * @return string
     */
    private function parse_search( array $q, object $wp_query ): string {
        $wpdb = $this->get_wpdb();
        $search = '';

        $q['s'] = \stripslashes( (string) ( $q['s'] ?? '' ) );
        $q['s'] = \str_replace( array( "\r", "\n" ), '', $q['s'] );
        $q['search_terms_count'] = 1;

        if ( ! empty( $q['sentence'] ) ) {
            $q['search_terms'] = array( $q['s'] );
        } elseif ( \preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
            $q['search_terms_count'] = \count( $matches[0] );
            $q['search_terms'] = $this->parse_search_terms( $matches[0] );

            if ( empty( $q['search_terms'] ) || \count( $q['search_terms'] ) > 9 ) {
                $q['search_terms'] = array( $q['s'] );
            }
        } else {
            $q['search_terms'] = array( $q['s'] );
        }

        $n = ! empty( $q['exact'] ) ? '' : '%';
        $searchand = '';
        $q['search_orderby_title'] = array();
        $exclusion_prefix = (string) \apply_filters( 'wp_query_search_exclusion_prefix', '-' );

        foreach ( $q['search_terms'] as $term ) {
            $exclude = $exclusion_prefix && \substr( $term, 0, 1 ) === $exclusion_prefix;

            if ( $exclude ) {
                $like_op = 'NOT LIKE';
                $andor_op = 'AND';
                $term = \substr( $term, 1 );
            } else {
                $like_op = 'LIKE';
                $andor_op = 'OR';
            }

            if ( $n && ! $exclude ) {
                $like = '%' . $wpdb->esc_like( $term ) . '%';
                $q['search_orderby_title'][] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );

                if ( $this->is_latin_string( $term ) ) {
                    $q['search_orderby_title'][] = $wpdb->prepare(
                        "{$wpdb->posts}.post_title LIKE %s",
                        '%' . $wpdb->esc_like( $this->transliterate_to_cyrillic( $term ) ) . '%'
                    );
                }
            }

            $like = $n . $wpdb->esc_like( $term ) . $n;
            $search .= $wpdb->prepare(
                "{$searchand}(({$wpdb->posts}.post_title {$like_op} %s) {$andor_op} ({$wpdb->posts}.post_excerpt {$like_op} %s) {$andor_op} ({$wpdb->posts}.post_content {$like_op} %s)",
                $like,
                $like,
                $like
            );

            if ( $this->is_latin_string( $term ) ) {
                $cyrillic_like = $this->transliterate_to_cyrillic( $like );
                $search .= $wpdb->prepare(
                    " {$andor_op} ({$wpdb->posts}.post_title {$like_op} %s) {$andor_op} ({$wpdb->posts}.post_excerpt {$like_op} %s) {$andor_op} ({$wpdb->posts}.post_content {$like_op} %s)",
                    $cyrillic_like,
                    $cyrillic_like,
                    $cyrillic_like
                );
            }

            $search .= ')';
            $searchand = ' AND ';
        }

        $wp_query->query_vars = $q;

        if ( '' !== $search ) {
            $search = " AND ({$search}) ";

            if ( ! \is_user_logged_in() ) {
                $search .= " AND ({$wpdb->posts}.post_password = '') ";
            }
        }

        return $search;
    }

    /**
     * Generate a transliterated ORDER BY clause.
     *
     * @param array<string,mixed> $q Query variables.
     * @return string
     */
    private function parse_search_order( array $q ): string {
        $wpdb = $this->get_wpdb();
        $search_orderby = '';

        if ( (int) ( $q['search_terms_count'] ?? 0 ) > 1 ) {
            $num_terms = \count( (array) ( $q['search_orderby_title'] ?? array() ) );
            $like = '';
            $cyrillic_like = '';

            if ( ! \preg_match( '/(?:\s|^)\-/', (string) ( $q['s'] ?? '' ) ) ) {
                $like = '%' . $wpdb->esc_like( (string) $q['s'] ) . '%';
                $cyrillic_like = '%' . $wpdb->esc_like( $this->transliterate_to_cyrillic( (string) $q['s'] ) ) . '%';
            }

            if ( '' !== $like ) {
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s THEN 1 ", $like );

                if ( $this->is_latin_string( (string) $q['s'] ) ) {
                    $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s THEN 1 ", $cyrillic_like );
                }
            }

            if ( $num_terms < 7 ) {
                $search_orderby .= 'WHEN ' . \implode( ' AND ', (array) $q['search_orderby_title'] ) . ' THEN 2 ';

                if ( $num_terms > 1 ) {
                    $search_orderby .= 'WHEN ' . \implode( ' OR ', (array) $q['search_orderby_title'] ) . ' THEN 3 ';
                }
            }

            if ( '' !== $like ) {
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_excerpt LIKE %s THEN 4 ", $like );
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_excerpt LIKE %s THEN 4 ", $cyrillic_like );
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_content LIKE %s THEN 5 ", $like );
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_content LIKE %s THEN 5 ", $cyrillic_like );
            }

            if ( '' !== $search_orderby ) {
                return '(CASE ' . $search_orderby . 'ELSE 6 END)';
            }

            return '';
        }

        $title_orderby = (array) ( $q['search_orderby_title'] ?? array() );
        $first = \reset( $title_orderby );

        return false !== $first ? $first . ' DESC' : '';
    }

    /**
     * Remove stopwords and short terms from the search term list.
     *
     * @param string[] $terms Raw search terms.
     * @return string[]
     */
    private function parse_search_terms( array $terms ): array {
        $strtolower = \function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
        $checked = array();
        $stopwords = $this->get_search_stopwords();

        foreach ( $terms as $term ) {
            if ( \preg_match( '/^".+"$/', $term ) ) {
                $term = \trim( $term, "\"'" );
            } else {
                $term = \trim( $term, "\"' " );
            }

            if ( ! $term || ( 1 === \strlen( $term ) && \preg_match( '/^[a-z\-]$/i', $term ) ) ) {
                continue;
            }

            if ( \in_array( \call_user_func( $strtolower, $term ), $stopwords, true ) ) {
                continue;
            }

            $checked[] = $term;
        }

        return $checked;
    }

    /**
     * Retrieve search stopwords.
     *
     * @return string[]
     */
    private function get_search_stopwords(): array {
        $words = \explode(
            ',',
            \_x(
                'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
                'Comma-separated list of search stopwords in your language',
                'default'
            )
        );

        $stopwords = array();

        foreach ( $words as $word ) {
            $word = \trim( $word, "\r\n\t " );

            if ( '' !== $word ) {
                $stopwords[] = $word;
            }
        }

        return (array) \apply_filters( 'wp_search_stopwords', $stopwords );
    }

    /**
     * Check whether a string includes Latin search characters.
     *
     * @param string $content Search text.
     * @return bool
     */
    private function is_latin_string( string $content ): bool {
        return 1 === \preg_match( '/[a-zčćđšž]+/iu', $content ) || 1 === \preg_match( '/[a-z]+/i', $content );
    }

    /**
     * Check whether the current language is Serbian.
     *
     * @return bool
     */
    private function is_serbian_language(): bool {
        return 1 === \preg_match( '/^sr(?:[_-]|$)/i', $this->script_manager->get_language() );
    }

    /**
     * Transliterate Latin text to Cyrillic.
     *
     * @param string $value Input text.
     * @return string
     */
    private function transliterate_to_cyrillic( string $value ): string {
        return $this->transliterator->latToCir( $value );
    }

    /**
     * Resolve the wpdb adapter.
     *
     * @return object
     */
    private function get_wpdb(): object {
        if ( null !== $this->wpdb ) {
            return $this->wpdb;
        }

        global $wpdb;

        return $wpdb;
    }
}

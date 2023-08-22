<?php
/**
 * Search_Query_Transliterator class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Frontend;

use WP_Query;

/**
 * Transliterates search query
 */
class Search_Query_Transliterator {
    /**
     * Class constructor
     */
    public function __construct() {
        add_filter( 'posts_search', array( $this, 'convert_terms_to_cyrillic' ), 100, 2 );
    }

    /**
     * Converts latin search terms to cyrillic
     *
     * This will only work if:
     * 1) Search fix is enabled
     * 2) We're in the main query
     * 3) We're viewing a page in Serbian
     * 4) The search is not empty
     * 5) Search contains latin characters
     *
     * @param  string   $search   Search SQL clause.
     * @param  WP_Query $wp_query WP_Query object.
     * @return string             Modified search SQL clause.
     */
    public function convert_terms_to_cyrillic( $search, $wp_query ) {
        if (
        ! STL()->get_settings( 'advanced', 'fix_search' ) ||
        ! STL()->manager->is_serbian() ||
        ! $wp_query->is_main_query() ||
        empty( $wp_query->get( 's' ) ) ||
        ! $this->is_latin_string( $wp_query->get( 's' ) )
        ) {
            return $search;
        }

        // Modify the order by clause to match the search terms.
        add_filter( 'posts_search_orderby', array( $this, 'modify_search_orderby' ), 100, 2 );

        return $this->parse_search( $wp_query->query_vars );
    }

    /**
     * Modifes the search orderby clause to match the search terms.
     *
     * @param  string   $orderby  Orderby SQL clause.
     * @param  WP_Query $wp_query WP_Query object.
     * @return string             Modified orderby SQL clause.
     */
    public function modify_search_orderby( $orderby, $wp_query ) {
        $orderby = $this->parse_search_order( $wp_query->query_vars );
        return $orderby;
    }

    /**
     * Generates SQL for the WHERE clause based on passed search terms.
     * Copied from `wp-includes/class-wp-query.php`
     *
     * Modified to transliterate latin search terms to cyrillic.
     *
     * @since 3.7.0
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @param array $q Query variables.
     * @return string WHERE clause.
     */
    private function parse_search( &$q ) {
        global $wpdb;

        $search = '';

        // Added slashes screw with quote grouping when done early, so done later.
        $q['s'] = stripslashes( $q['s'] );

        // There are no line breaks in <input /> fields.
        $q['s']                  = str_replace( array( "\r", "\n" ), '', $q['s'] );
        $q['search_terms_count'] = 1;
        if ( ! empty( $q['sentence'] ) ) {
            $q['search_terms'] = array( $q['s'] );
        } elseif ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
                $q['search_terms_count'] = count( $matches[0] );
                $q['search_terms']       = $this->parse_search_terms( $matches[0] );
                // If the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
            if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 ) {
                $q['search_terms'] = array( $q['s'] );
            }
        } else {
            $q['search_terms'] = array( $q['s'] );
        }

        $n                         = ! empty( $q['exact'] ) ? '' : '%';
        $searchand                 = '';
        $q['search_orderby_title'] = array();

        /**
         * Filters the prefix that indicates that a search term should be excluded from results.
         *
         * @since 4.7.0
         *
         * @param string $exclusion_prefix The prefix. Default '-'. Returning
         *                                 an empty value disables exclusions.
         */
        $exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );

        foreach ( $q['search_terms'] as $term ) {
            // If there is an $exclusion_prefix, terms prefixed with it should be excluded.
            $exclude = $exclusion_prefix && ( substr( $term, 0, 1 ) === $exclusion_prefix );
            if ( $exclude ) {
                $like_op  = 'NOT LIKE';
                $andor_op = 'AND';
                $term     = substr( $term, 1 );
            } else {
                $like_op  = 'LIKE';
                $andor_op = 'OR';
            }

            if ( $n && ! $exclude ) {
                $like                        = '%' . $wpdb->esc_like( $term ) . '%';
                $q['search_orderby_title'][] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );
                if ( $this->is_latin_string( $term ) ) {
                    $q['search_orderby_title'][] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like( STL()->engine->convert_to_cyrillic( $term ) ) . '%' );
                }
            }

            $like = $n . $wpdb->esc_like( $term ) . $n;
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $search .= $wpdb->prepare(
                <<<SQL
                {$searchand}(
                    (
                        {$wpdb->posts}.post_title $like_op %s
                    )
                    $andor_op (
                        {$wpdb->posts}.post_excerpt $like_op %s
                    )
                    $andor_op (
                        {$wpdb->posts}.post_content $like_op %s
                    )

                SQL,
                $like,
                $like,
                $like
            );
            // If the term is in latin - transliterate and append.
            if ( $this->is_latin_string( $term ) ) {
                $search .= $wpdb->prepare(
                    <<<SQL
                        $andor_op (
                            {$wpdb->posts}.post_title $like_op %s
                        )
                        $andor_op (
                            {$wpdb->posts}.post_excerpt $like_op %s
                        )
                        $andor_op (
                            {$wpdb->posts}.post_content $like_op %s
                        )
                    SQL,
                    STL()->engine->convert_to_cyrillic( $like ),
                    STL()->engine->convert_to_cyrillic( $like ),
                    STL()->engine->convert_to_cyrillic( $like )
                );
            }
            //phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $search   .= ')';
            $searchand = ' AND ';
        }

        if ( ! empty( $search ) ) {
            $search = " AND ({$search}) ";
            if ( ! is_user_logged_in() ) {
                $search .= " AND ({$wpdb->posts}.post_password = '') ";
            }
        }

        return $search;
    }

    /**
     * Generates SQL for the ORDER BY condition based on passed search terms.
     *
     * @since 3.7.0
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @param array $q Query variables.
     * @return string ORDER BY clause.
     */
    protected function parse_search_order( &$q ) {
        global $wpdb;

        if ( $q['search_terms_count'] > 1 ) {
            $num_terms = count( $q['search_orderby_title'] );

            // If the search terms contain negative queries, don't bother ordering by sentence matches.
            $like = '';
            if ( ! preg_match( '/(?:\s|^)\-/', $q['s'] ) ) {
                $like          = '%' . $wpdb->esc_like( $q['s'] ) . '%';
                $cyrillic_like = '%' . $wpdb->esc_like( STL()->engine->convert_to_cyrillic( $q['s'] ) ) . '%';
            }

            $search_orderby = '';

            // Sentence match in 'post_title'.
            if ( $like ) {
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s THEN 1 ", $like );
                if ( $this->is_latin_string( $q['s'] ) ) {
                    $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s THEN 1 ", $cyrillic_like );
                }
            }

            // Sanity limit, sort as sentence when more than 6 terms
            // (few searches are longer than 6 terms and most titles are not).
            if ( $num_terms < 7 ) {
                // All words in title.
                $search_orderby .= 'WHEN ' . implode( ' AND ', $q['search_orderby_title'] ) . ' THEN 2 ';
                // Any word in title, not needed when $num_terms == 1.
                if ( $num_terms > 1 ) {
                    $search_orderby .= 'WHEN ' . implode( ' OR ', $q['search_orderby_title'] ) . ' THEN 3 ';
                }
            }

            // Sentence match in 'post_content' and 'post_excerpt'.
            if ( $like ) {
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_excerpt LIKE %s THEN 4 ", $like );
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_excerpt LIKE %s THEN 4 ", $cyrillic_like );
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_content LIKE %s THEN 5 ", $like );
                $search_orderby .= $wpdb->prepare( "WHEN {$wpdb->posts}.post_content LIKE %s THEN 5 ", $cyrillic_like );
            }

            if ( $search_orderby ) {
                $search_orderby = '(CASE ' . $search_orderby . 'ELSE 6 END)';
            }
        } else {
            // Single word or sentence search.
            $search_orderby = reset( $q['search_orderby_title'] ) . ' DESC';
        }

        return $search_orderby;
    }

    /**
     * Check if the terms are suitable for searching.
     *
     * Uses an array of stopwords (terms) that are excluded from the separate
     * term matching when searching for posts. The list of English stopwords is
     * the approximate search engines list, and is translatable.
     *
     * Copied from `wp-includes/class-wp-query.php`
     *
     * @since 3.7.0
     *
     * @param  string[] $terms Array of terms to check.
     * @return string[]        Terms that are not stopwords.
     */
    private function parse_search_terms( $terms ) {
        $strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
        $checked    = array();

        $stopwords = $this->get_search_stopwords();

        foreach ( $terms as $term ) {
            // Keep before/after spaces when term is for exact match.
            if ( preg_match( '/^".+"$/', $term ) ) {
                $term = trim( $term, "\"'" );
            } else {
                $term = trim( $term, "\"' " );
            }

            // Avoid single A-Z and single dashes.
            if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) ) {
                continue;
            }

            if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) ) {
                continue;
            }

            $checked[] = $term;
        }

        return $checked;
    }

    /**
     * Retrieve stopwords used when parsing search terms.
     *
     * Copied from `wp-includes/class-wp-query.php`
     *
     * @since 3.7.0
     *
     * @return string[] Stopwords.
     */
    private function get_search_stopwords() {
        /*
         * translators: This is a comma-separated list of very common words that should be excluded from a search,
         * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
         * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
         */
        $words = explode(
            ',',
            _x(
                'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
                'Comma-separated list of search stopwords in your language',
                'default'
            )
        );

        $stopwords = array();
        foreach ( $words as $word ) {
            $word = trim( $word, "\r\n\t " );
            if ( $word ) {
                $stopwords[] = $word;
            }
        }

        /**
         * Filters stopwords used when parsing search terms.
         *
         * @since 3.7.0
         *
         * @param string[] $stopwords Array of stopwords.
         */
        return apply_filters( 'wp_search_stopwords', $stopwords );
    }

    /**
     * Checks if the string contains latin characters
     *
     * @param  string $content String to check.
     * @return bool            True if the string contains latin characters, false otherwise.
     */
    private function is_latin_string( $content ) {
        return preg_match( '/[a-zčćđšž]+/iu', $content ) || preg_match( '/[a-z]+/i', $content );
    }
}

<?php
/**
 * Search_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Translit\Services\Search_Query_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register transliterated search hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Search_Handler {
    /**
     * Constructor.
     *
     * @param Search_Query_Service $service Search SQL service.
     */
    public function __construct( private Search_Query_Service $service ) {
    }

    /**
     * Expand Latin search terms to include Cyrillic matches.
     *
     * @param string $search Existing SQL.
     * @param object $wp_query Query object.
     * @return string
     */
    #[Filter(
        tag: 'posts_search',
        priority: 100,
        context: Filter::CTX_FRONTEND,
        args: 2,
        invoke: Action::INV_PROXIED,
    )]
    public function filter_posts_search( string $search, object $wp_query ): string {
        return $this->service->filter_posts_search( $search, $wp_query );
    }

    /**
     * Improve ordering for transliterated Serbian searches.
     *
     * @param string $orderby Existing ORDER BY clause.
     * @param object $wp_query Query object.
     * @return string
     */
    #[Filter(
        tag: 'posts_search_orderby',
        priority: 100,
        context: Filter::CTX_FRONTEND,
        args: 2,
        invoke: Action::INV_PROXIED,
    )]
    public function filter_posts_search_orderby( string $orderby, object $wp_query ): string {
        return $this->service->filter_posts_search_orderby( $orderby, $wp_query );
    }
}

<?php

namespace SGI\STL\Utils;

use SGI\STL\Core\LanguageManager   as LM,
    SGI\Transliterator              as Transliterator;

/**
 * Emulates wp_parse_args for multidimensional arrays
 * 
 * @param  array $a Options array
 * @param  array $b Default options array
 * @return array    Merged options array
 * 
 * @since 2.4
 */
function extendedParseArgs(array &$a, array $b) : array
{
    $result = $b;

    foreach ($a as $k => &$v) :
        
        if ( is_array($v) && isset($result[$k]) ) :
            $result[$k] = extendedParseArgs($v, $result[$k]);
            continue;
        endif;
            
        $result[$k] = $v;

    endforeach;

    return $result;
}

/**
 * Returns default plugin options
 * 
 * @return array Default plugin options
 * 
 * @since 2.0
 */
function getDefaultOptions() : array
{

    return [
        'migrated'       => false,
        'core'  => [
            'script'     => 'cir',
            'cookie'     => true,
            'param'      => 'pismo',
        ],
        'file'  => [
            'names'      => true,
            'translit'   => true,
            'content'    => true,
            'delim'      => '-',
        ],
        'fixes' =>[
            'permalinks' => (get_option('WPLANG') == 'sr_RS') ? false : true, 
            'search'     => true,
            'ajax'       => false,
        ],
        'menu'  => [
            'extend'     => true,
            'selector'   => '',
            'type'       => 'dropdown',
            'label'      => 'Писмо'
        ],
        'ml'    => [
            'wpml'       => true,
            'pll'        => true,
            'qtx'        => true
        ]
    ];

}

/**
 * Returns plugin options saved in the database. Fallbacks to sane defaults
 * 
 * @return array Plugin options
 * 
 * @since 2.0
 */
function getOptions() : array
{

    $opts     = get_option('sgi/stl/opt',[]);
    $defaults = getDefaultOptions();


    return extendedParseArgs($opts, $defaults);

}

function get_script()
{

    return LM::get_instance()->get_script();

}

function get_script_param()
{
    return LM::get_instance()->get_script_param();
}

function is_serbian()
{
    return LM::get_instance()->is_serbian();
}

function is_wpml_active()
{

    return LM::is_wpml_active();

}

function is_cyrillic()
{
    return (get_script() == 'cir') ? true : false;
}

function is_latin()
{

    return (get_script() == 'lat') ? true : false;

}

function get_query_param()
{
    return getOptions()['core']['param'];
}

function transliterate($content, $cut = false)
{

    return (!$cut) ? 
            Transliterator::cir_to_lat($content) :
            Transliterator::cir_to_cut_lat($content);

    return $content;

}

function reverse_transliterate($content)
{

     return Transliterator::lat_to_cir($content);
    
}

/**
 * Enables searching for cyrilic post title/content using latin script
 *
 * @param  string $search Search string to modify
 * @return string         SQL used in the WHERE clause of \WP_Query
 * 
 * @since 2.0
 */
function modifySearchQuery(string $search) : string
{

    global $wpdb;

    $search_term = $_GET['s'];

    $search_term = rtrim(ltrim($search));

    $search_like_orig = '%' . $wpdb->esc_like($search_term) . '%';
    $search_like_tran = '%' . $wpdb->esc_like( transliterate($search_term) ) . '%';

    $query = $wpdb->prepare(
        " AND ({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR {$wpdb->posts}.post_content LIKE %s)",
        $search_like_orig,
        $search_like_tran,
        $search_like_orig,
        $search_like_tran
    );

    return $query;

}
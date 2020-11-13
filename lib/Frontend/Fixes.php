<?php

namespace SGI\STL\Frontend;

use function SGI\STL\Utils\getOptions,
             SGI\STL\Utils\get_script,
             SGI\STL\Utils\transliterate,
             SGI\STL\Utils\modifySearchQuery;

class Fixes
{

    private $opts;

    public function __construct()
    {

        $this->opts = getOptions();

        // Extend WPML Language selector
        add_filter('icl_ls_languages', [&$this, 'extend_wpml_ls'], 100, 1);

        // Fix search in latin script
        add_filter('posts_search', [&$this,'fix_search'], 100, 2);

        // Change Page title on latin
        add_filter('wp_title', [&$this,'convert_title'], 100, 3);
        add_filter('pre_get_document_title', [&$this,'convert_title'], 100, 3);
        add_filter('document_title_parts', [&$this,'convert_title_parts'], 100, 3);

    }

    public function extend_wpml_ls($languages)
    {

        if (!$this->opts['ml']['wpml']) :
            return $languages;
        endif;

        $active = get_script();

        $cir = $lat = $languages['sr'];

        $primary = 'sr@cir';
        $secondary = 'sr';

        if ($active == 'cir') :

            $primary = 'sr';
            $secondary = 'sr@lat';

        endif;

        $serbian = [
            $primary   => array_replace($cir, [
                'native_name'     => do_shortcode('[stl_cyr]српски (ћир)[/stl_cyr]'),
                'translated_name' => "{$languages['sr']['translated_name']} (cyr)",
                'url'             => add_query_arg($this->opts['core']['param'], 'cir', $languages['sr']['url']),
                'active'          => ($active == 'cir') ? 1 : 0
            ]),
            $secondary => array_replace($lat, [
                'native_name'     => 'srpski (lat)',
                'translated_name' => "{$languages['sr']['translated_name']} (lat)",
                'url'             => add_query_arg($this->opts['core']['param'], 'lat', $languages['sr']['url']),
                'active'          => ($active == 'lat') ? 1 : 0
            ])
        ];

        unset($languages['sr']);

        return $serbian + $languages;

    }

    public function fix_search(string $search, \WP_Query $query)
    {

        if ( !$this->opts['fixes']['search'] || empty($search) ) :
            return $search;
        endif;

        return modifySearchQuery($search);

    }

    public function convert_title($title,$sep = '',$location = '')
    {

        if (current_theme_supports( 'title-tag' )) :
            return $title;
        endif;

        return transliterate($title);

    }

    public function convert_title_parts($title)
    {   

        $newtitle = [];

        foreach ($title as $part => $value) :

            $newtitle[$part] = transliterate($value);

        endforeach;

        return $newtitle;

    }

}
<?php

namespace SGI\STL\Frontend;

use SGI\STL\Core\Language_Manager   as LM,
    SGI\STL\Shortcode\Cyrilizer     as Cyrilizer,
    SGI\STL\Shortcode\Translator    as Translator,
    simplehtmldom\HtmlDocument      as sHTMLd;

use function SGI\STL\Utils\getOptions,
             SGI\STL\Utils\transliterate,
             SGI\STL\Utils\multiscript_sql_query;


class Core
{

    private $opts;

    /**
     * @var LM
     */
    private $lm;

    private $shortcodes;

    /**
     * @var array List of actions and hooks to apply the action on
     * 
     * @since 2.4
     */
    private static $actions = [
        'buffer_start' => ['wp_head', 'rss_head', 'atom_head', 'rdf_head', 'rss2_head'],
        'buffer_end'   => ['wp_footer', 'rss_footer', 'atom_footer', 'rdf_footer', 'rss2_footer']
    ];

    /**
     * @var array List of filters and hooks to apply the filter on
     * 
     * @since 2.4
     */
    private static $filters = [
        'convert_script' => ['gettext', 'ngettext', 'gettext_with_context', 'ngettext_with_context']
    ];

    public function __construct()
    {

        if (is_admin())
            return;

        $this->lm = LM::get_instance();

        $this->opts = getOptions();

        $this->shortcodes = [];

        $filter_priority = 9999;

        /**
         * Filters the priorty for transliteration engine
         *
         * @since 2.0.0
         *
         * @param filter_priority Integer defining transliterator priority
         */
        $filter_priority = apply_filters('sgi/stl/filter_priority', $filter_priority);

        add_filter('posts_search', [&$this,'fix_search'], $filter_priority, 2);

        if ($this->lm->is_wpml_active() && $this->opts['ml']['wpml']) :
            add_filter('icl_ls_languages', [&$this, 'extend_wpml_ls'], $filter_priority, 1);
        endif;
       

        if ($this->lm->get_script() == 'lat' && $this->lm->in_serbian) :

            foreach (self::$actions as $function => $hooks) :
                foreach ($hooks as $hook) :
                    add_action($hook, [&$this, $function], $filter_priority);
                endforeach;
            endforeach;

            foreach (self::$filters as $function => $hooks) :
                foreach ($hooks as $hook) :
                    add_filter($hook, [&$this, $function], $filter_priority);
                endforeach;
            endforeach;

            // Change Page title on latin
            if (!current_theme_supports( 'title-tag' )) :
                add_filter('wp_title', [&$this,'convert_title'], $filter_priority, 3);
            else :
                add_filter('pre_get_document_title', [&$this,'convert_title'], $filter_priority, 3);
                add_filter('document_title_parts', [&$this,'convert_title_parts'], $filter_priority, 3);
            endif;

            // Change Script Specific images in content
            if ($this->opts['file']['translit'] && $this->opts['file']['content']) :
                add_filter('the_content', [&$this, 'change_image_urls'], $filter_priority, 1);
            endif;

        endif;

        

    }

    public function buffer_start()
    {

        ob_start();

    }

    public function buffer_end()
    {

        //ob_end_flush();
        $output = ob_get_clean();

        if ($this->opts['file']['translit'] && !$this->opts['file']['content']) :
            $output = $this->change_image_urls($output);
        endif;

        $output = $this->convert_script($output);

        $this->shortcodes += Cyrilizer::get_shortcodes();
        $this->shortcodes += Translator::get_shortcodes();

        echo strtr($output, $this->shortcodes);

    }

    public function convert_title($title,$sep = '',$location = '')
    {

        return $this->convert_script($title);

    }

    public function convert_title_parts($title)
    {   

        $newtitle = [];

        foreach ($title as $part => $value) :

            $newtitle[$part] = $this->convert_script($value);

        endforeach;

        return $newtitle;

    }

    public function change_image_urls($output)
    {

        $shtmld = new sHTMLd($output);
        $delim  = $this->opts['file']['delim'];

        foreach ($shtmld->find('img') as $img) :

            $img->src    = str_replace("{$delim}cir", "{$delim}lat", $img->src);
            $img->srcset = str_replace("{$delim}cir", "{$delim}lat", $img->srcset);

        endforeach;

        return $shtmld->root->innertext;

    }

    public function fix_search(string $search, \WP_Query $query)
    {

        if (is_admin())
            return $search;

        if (!$this->opts['fixes']['search'])
            return $search;

        if ( !$query->is_main_query())
            return $search;

        $g = $_GET['s'] ?? '';

        if ($g == '')
            return $search;

        if (!is_search())
            return $search;

        return multiscript_sql_query($search);

    }

    public function convert_script($content)
    {

        return transliterate($content);

    }

    public function extend_wpml_ls($languages)
    {


        $active = $this->lm->get_script();

        $cir = $lat = $languages['sr'];

        if ($active == 'cir') :

            $primary = 'sr';
            $secondary = 'sr@lat';

        else :

            $primary = 'sr@cir';
            $secondary = 'sr';

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

}
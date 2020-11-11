<?php

namespace SGI\STL\Frontend;

use SGI\STL\Core\LanguageManager as LM,
    SGI\STL\Shortcode\Cyrilizer  as Cyrilizer,
    SGI\STL\Shortcode\Translator as Translator,
    simplehtmldom\HtmlDocument   as sHTMLd;

use function SGI\STL\Utils\getOptions,
             SGI\STL\Utils\transliterate;

class Core
{

    private $opts;

    /**
     * @var LM Language Manager class
     * 
     * @since 2.0
     */
    private $lm;

    /**
     * @var array List of shortcode uuid-content pairs
     */
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


        if ( $this->lm->get_script() != 'lat' && !$this->lm->in_serbian) :
            return;
        endif;

        add_action('wp_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('wp_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('rss_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('rss_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('atom_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('atom_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('rdf_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('rdf_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('rss2_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('rss2_footer', [&$this, 'buffer_end'], $filter_priority);

        add_filter('gettext', [&$this, 'convert_script'], $filter_priority);
        add_filter('ngettext', [&$this, 'convert_script'], $filter_priority);
        add_filter('gettext_with_context', [&$this, 'convert_script'], $filter_priority);
        add_filter('ngettext_with_context', [&$this, 'convert_script'], $filter_priority);

        // Change Script Specific images in content
        if ($this->opts['file']['translit'] && $this->opts['file']['content']) :
            add_filter('the_content', [&$this, 'change_image_urls'], $filter_priority, 1);
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

    public function convert_script($content)
    {

        return transliterate($content);

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

}
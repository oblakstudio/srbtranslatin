<?php

namespace SGI\STL\Frontend;

use SGI\STL\Core\LanguageManager as LM,
    SGI\STL\Shortcode\Cyrilizer  as Cyrilizer,
    SGI\STL\Shortcode\Translator as Translator,
    simplehtmldom\HtmlDocument   as sHTMLd;

use simplehtmldom\HtmlNode;
use function SGI\STL\Utils\getOptions,
             SGI\STL\Utils\transliterate;
use function SGI\STL\Utils\reverse_transliterate;

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

    public function __construct()
    {

        if (is_admin())
            return;

        $this->lm         = LM::get_instance();
        $this->opts       = getOptions();
        $this->shortcodes = [];

        if ( ! $this->lm->in_serbian ) :
            return;
        endif;

        /**
         * Filters the priority for transliteration engine
         *
         * @since 2.0.0
         *
         * @param $filter_priority Integer defining transliterator priority
         */
        $filter_priority = apply_filters('sgi/stl/filter_priority', 9999);

        if ( $this->lm->get_script() !== $this->opts['core']['origin_script'] ) :
            $this->loadTransliterator($filter_priority);
        endif;
    }

    /**
     * Start the transliteration system.
     * Function adds all the necessary filters and actions to predefined hooks
     * 
     * @param int $filter_priority Priority on which to load actions and filters
     * 
     * @since 2.4
     */
    public function loadTransliterator(int $filter_priority)
    {

        add_action('init', [&$this, 'buffer_start'], $filter_priority);
        add_action('wp_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('rss_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('rss_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('atom_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('atom_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('rdf_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('rdf_footer', [&$this, 'buffer_end'], $filter_priority);

        add_action('rss2_head', [&$this, 'buffer_start'], $filter_priority);
        add_action('rss2_footer', [&$this, 'buffer_end'], $filter_priority);

        // Change Script Specific images in content
        if ($this->opts['file']['translit'] && $this->opts['file']['content']) :
            add_filter('the_content', [&$this, 'change_image_urls'], $filter_priority, 1);
        endif;

    }

    /**
     * Starts output buffering so we have one large string to transliterate
     * 
     * @since 2.0
     */
    public function buffer_start()
    {
        ob_start();
    }

    /**
     * Ends output buffering and performs transliteration
     * 
     * @since 2.0
     */
    public function buffer_end()
    {
        $output = ob_get_clean();

        if ($this->opts['file']['translit'] && !$this->opts['file']['content']) :
            $output = $this->change_image_urls($output);
        endif;

        $output = $this->convert_script($output);

        $this->shortcodes += Cyrilizer::get_shortcodes();
        $this->shortcodes += Translator::get_shortcodes();

        $shortcodes = $this->shortcodes;

        // Shortcode keys will be transliterated as well, we need to adjust them before replacement.
        if ($this->lm->get_script() == 'cir') :
            foreach ($this->shortcodes as $key => $value) :

                $shortcodes[reverse_transliterate($key)] = $value;

            endforeach;
        endif;

        echo strtr($output, $shortcodes);

    }

    public function convert_script($content)
    {
        if ($this->lm->get_script() === 'lat')
            return transliterate($content);

        $shtmld = new sHTMLd($content);
        $node = $this->convert_html_to_cyrillic($shtmld->root);

        return (string) $node;
    }

    /**
     * Recursively iterates through the HTML nodes and transliterates the text.
     *
     * @param \simplehtmldom\HtmlNode $node
     */
    private function convert_html_to_cyrillic($node)
    {
        $skip_elements = [
            'unknown',
            'script',
            'style',
            'comment',
            'cdata',
            'svg',
        ];

        if (in_array($node->nodeName(), $skip_elements)) :
            return $node;
        endif;

        $text_attributes = [
            'alt',
            'title',
            'aria-label',
            'placeholder',
        ];

        foreach ($text_attributes as $attribute) :
            if ($node->hasAttribute($attribute))
                $node->setAttribute($attribute, reverse_transliterate($node->getAttribute($attribute)));
        endforeach;


        if (isset($node->_[HtmlNode::HDOM_INFO_INNER]))
            $node->_[HtmlNode::HDOM_INFO_INNER] = reverse_transliterate($node->_[HtmlNode::HDOM_INFO_INNER]);

        if (isset($node->_[HtmlNode::HDOM_INFO_TEXT]))
            $node->_[HtmlNode::HDOM_INFO_TEXT] = reverse_transliterate($node->_[HtmlNode::HDOM_INFO_TEXT]);

        /** @var \simplehtmldom\HtmlNode $child_node */
        foreach ($node->nodes as $child_node) :
            $this->convert_html_to_cyrillic($child_node);
        endforeach;

        return $node;
    }

    public function change_image_urls($output)
    {

        $shtmld = new sHTMLd($output);
        $delim  = $this->opts['file']['delim'];

        foreach ($shtmld->find('img') as $img) :
            $origin_suffix = $this->opts['core']['origin_script'];
            $replacement_suffix = $origin_suffix == 'cir' ? 'lat' : 'cir';

            $img->src    = str_replace("{$delim}{$origin_suffix}", "{$delim}{$replacement_suffix}", $img->src);
            $img->srcset = str_replace("{$delim}{$origin_suffix}", "{$delim}{$replacement_suffix}", $img->srcset);

        endforeach;

        return $shtmld->root->innertext;

    }    

}
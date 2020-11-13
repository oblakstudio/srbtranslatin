<?php

namespace SGI\STL\Ajax;

use SGI\STL\Core\LanguageManager as LM;

use function SGI\STL\Utils\getOptions,
             SGI\STL\Utils\transliterate;


/**
 * Ajax Transliteration engine.
 * 
 * @since 2.4
 * @uses  Voodoo Programming
 * @link  http://www.catb.org/jargon/html/V/voodoo-programming.html
 */
class Engine
{

    /**
     * @var array Plugin options
     */
    private $opts;

    /**
     * @var LM Language Manager class
     * 
     * @since 2.4
     */
    private $lm;

    public function __construct()
    {

        if (!wp_doing_ajax()) :
            return;
        endif;

        $this->lm    = LM::get_instance();
        $this->opts  = getOptions();

        /**
         * Filters the priorty for transliteration engine
         *
         * @since 2.4
         *
         * @param filter_priority Integer defining transliterator priority
         */
        $filter_priority = apply_filters('sgi/stl/filter_priority', 9999);

        if ( $this->lm->get_script() == 'lat' && $this->lm->in_serbian && $this->opts['fixes']['ajax']) :
            $this->loadTransliterator($filter_priority);
        endif;

    }

    /**
     * 
     * @param int $filter_priority Negative priority to run the filter by
     * 
     * @since 2.4
     */
    public function loadTransliterator(int $filter_priority)
    {
        add_action('admin_init', [&$this, 'bufferStart'], -$filter_priority);
    }

    /**
     * Starts output buffering for the transliteration
     *
     * Basically, this function will hook onto the admin_init action before Ajax actions are peformed.
     * We start the output buffer here, and define a callback function.
     * Theoretically - it should get the contents and transliterate them if needed.
     * 
     * @since 2.4
     * @uses  Deep Magic
     * @link  http://www.catb.org/jargon/html/D/deep-magic.html
     */
    public function bufferStart()
    {
        ob_start([&$this, 'bufferEnd']);
    }

    /**
     * Transliterates ajax response
     *
     * @param string $contents Contents of the Deep Magic Output Buffer
     * @return string          Transliterated string
     * 
     * @since 2.4
     * @uses  Heavy Wizardry
     * @link  http://www.catb.org/jargon/html/H/heavy-wizardry.html
     */
    public function bufferEnd($contents)
    {
        return transliterate($contents);
    }

}
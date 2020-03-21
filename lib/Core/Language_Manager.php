<?php

namespace SGI\STL\Core;

use function SGI\STL\Core\Utils\get_stl_config;

class Language_Manager
{

    private static $instance;

    private $opts;

    private $script;

    public $in_serbian;

    private function __construct()
    {

        $this->opts = get_stl_config();

        $this->in_serbian = $this->multilanguage_check();
        $this->script     = $this->determine_script();

    }

    public static function get_instance()
    {

        return (self::$instance === null) ?
                self::$instance = new self() :
                self::$instance;

    }

    private function get_cookie()
    {

        if (!$this->opts['core']['cookie'])
            return false;

        return $_COOKIE['stl_script'] ?? false;
        
    }

    private function set_cookie($script)
    {

        setcookie("stl_script", $script, strtotime("+3 months"), "/");

        return $script;

    }

    public function get_script()
    {

        /**
         * Enables forcing of specific script sitewide, disregarding all other options
         *
         * @since 2.0.0
         *
         * @param script - Script we're using on the website
         */
        $script = apply_filters("sgi/stl/script", $this->script);

        return $script;

    }

    private function multilanguage_check()
    {

        $in_serbian = true;

        if (function_exists('\pll_the_languages')) :

            $cur_lang = \pll_current_language('locale');

            if ($cur_lang == 'sr_RS') :

                $in_serbian = true;

            else : 

                $in_serbian = false;

            endif;


        elseif (function_exists('\qtranxf_getLanguage')) :

            $cur_lang = \qtranxf_getLanguage();

            if ($cur_lang == 'sr') :

                $in_serbian = true;

            else : 

                $in_serbian = false;

            endif; 

        elseif (self::is_wpml_active()) :

            $in_serbian = (\ICL_LANGUAGE_CODE == 'sr') ? true : false;

        endif;

        return $in_serbian;

    }

    public function is_serbian()
    {
        return $this->in_serbian;
    }

    public static function is_wpml_active()
    {

        return (defined('\ICL_LANGUAGE_CODE') && class_exists('\SitePress')) ? true : false;

    }

    private function determine_script()
    {

        $req_script = $_REQUEST[$this->opts['core']['param']] ?? false;


        if ($req_script) :

            if ($this->opts['core']['cookie']) :

                return $this->set_cookie($req_script);

            else :

                return $req_script;

            endif;

        else :

            if ($this->opts['core']['cookie']) :

                return ($lang = $this->get_cookie()) ?
                        $lang :
                        $this->set_cookie($this->opts['core']['script']);

            else :

                return $this->opts['core']['script'];

            endif;

        endif;


    }



}
<?php

use function SGI\STL\Core\Utils\{
    get_stl_config,
    get_script
};

use function SGI\STL\Frontend\Utils\script_selector;

use const SGI\STL\DOMAIN;

/**
 * @deprecated 
 */
function stl_transliterate($content, $script)
{
    
}

/**
 * @deprecated 
 */
function stl_get_current_script()
{

    trigger_error(
        __(
            sprintf('Function %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',__FUNCTION__),
            DOMAIN
        ),
        E_USER_DEPRECATED
    );

    return get_script();

}

/**
 * @deprecated 
 */
function stl_is_current_cyrillic()
{

   trigger_error(
        __(
            sprintf('Function %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',__FUNCTION__),
            DOMAIN
        ),
        E_USER_DEPRECATED
    );

    return is_cyrillic();

}

/**
 * @deprecated 
 */
function stl_is_current_latin()
{

    trigger_error(
        __(
            sprintf('Function %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',__FUNCTION__),
            DOMAIN
        ),
        E_USER_DEPRECATED
    );

    return is_latin();

}
/**
 * @deprecated 
 */
function stl_get_cyrillic_id()
{

    trigger_error(
        __(
            sprintf('Function %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',__FUNCTION__),
            DOMAIN
        ),
        E_USER_DEPRECATED
    );

    return 'cir';

}

/**
 * @deprecated 
 */
function stl_get_latin_id()
{

    trigger_error(
        __(
            sprintf('Function %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',__FUNCTION__),
            DOMAIN
        ),
        E_USER_DEPRECATED
    );

    return 'lat';

}

/**
 * @deprecated 
 */
function stl_get_script_identifier()
{

    trigger_error(
        __(
            sprintf('Function %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',__FUNCTION__),
            DOMAIN
        ),
        E_USER_DEPRECATED
    );

    return get_stl_config()['core']['param'];

}

function stl_show_selector($p_selection_type = 'oneline', $p_oneline_separator = ' | ', $p_cirilica_title = 'ћирилица', $p_latinica_title = 'латиница', $p_inactive_script_only = false, $p_show_only_on_wpml_languages = '')
{

    script_selector($p_selection_type, $p_oneline_separator, $p_cirilica_title, $p_latinica_title, $p_inactive_script_only, $p_show_only_on_wpml_languages);

}
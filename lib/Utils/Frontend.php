<?php

namespace SGI\STL\Utils;

use const SGI\STL\PATH;

function get_selector_template (string $template, array $args)
{

    $template_file = PATH."templates/selector/{$template}.php";

    /**
     * Filters the Template file for the selector, if you want to roll your own
     *
     * @since 2.0.0
     *
     * @param template_file - Template file for the oneline selector
     */
    $template_file = apply_filters("sgi/stl/selector/template/{$template}", $template_file);

    ob_start();

    include $template_file;

    return ob_get_clean();

}

/**
 * Returns current url on the website
 * 
 * @return string Current URL
 */
function get_current_url() : string
{

    global $wp;

    return home_url(
        add_query_arg(
            [],
            $wp->request
        )
    );

}

/**
 * Function which displays the script selector.
 * Used by the STL Widget, but can be used anywhere on the website
 * 
 * @param array      $args              Options for the script selector
 * @param mixed|null $base_url          Deprecated - Base URL for the links
 * @param mixed|null $depr_cyr_caption  Deprecated - Text to use for cyrillic link
 * @param mixed|null $depr_lat_caption  Deprecated - Text to use for latin link
 * @param mixed|null $depr_inactive     Deprecated - Show inactive link?
 * @param mixed|null $depr_wpml         Deprecated - Unknown WPML flag
 * 
 * @since 2.0
 * @todo  Remove deprecated arguments in 2.6
 */
function script_selector(
    array $args = [],
          $base_url = null,
          $depr_cyr_caption = null,
          $depr_lat_caption = null,
          $depr_inactive = null,
          $depr_wpml = null
) {

    if (!is_array($args)) :

        $f_args = [
            'selector_type' => $args,
            'separator'     => $base_url,
            'cir_caption'   => $depr_cyr_caption,
            'lat_caption'   => $depr_lat_caption,
            'inactive_only' => $depr_inactive
        ];

        $base_url = get_current_url();
        $args     = $f_args;

        unset($f_args);

        trigger_error(
            __(
                sprintf('You are using an old way calling the function %s, please read the documentation to see new function parameters', __FUNCTION__),
                'SrbTransLatin'
            ),
            E_USER_DEPRECATED
        );

    endif;

    $base_url = (empty($base_url)) ? get_current_url() : $base_url;
    $config   = getOptions();

    $args = wp_parse_args($args, [
        'selector_type' => 'oneline',
        'separator'     => '&nbsp;|&nbsp;',
        'cir_caption'   => 'Ћирилица',
        'lat_caption'   => 'Latinica',
        'inactive_only' => false,
        'active_script' => get_script(),
        'cir_link'      => add_query_arg($config['core']['param'], 'cir', $base_url),
        'lat_link'      => add_query_arg($config['core']['param'], 'lat', $base_url)
    ]);

    $html = '<div class="stl-selector">';

    switch ($args['selector_type']) :

        case 'links' :
            $html .= get_selector_template('list', $args);
            break;

        case 'list' :
        case 'dropdown' :
            $html .= get_selector_template('select', $args);
            break;

        case 'oneline' :
        default :
            $html .= get_selector_template('oneline', $args);
            break;

    endswitch;

    $html .= '</div>';

    echo $html;

}
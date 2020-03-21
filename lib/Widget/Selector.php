<?php

namespace SGI\STL\Widget;

use const SGI\STL\DOMAIN;

use function SGI\STL\Frontend\Utils\script_selector;

class Selector extends \WP_Widget
{

    public function __construct()
    {

        $widget_id   = 'sgi_stl_widget';
        $widget_opts = [
            'id_base'     => $widget_id,
            'description' => __('Serbian Script selection widget', DOMAIN)
        ];

        $this->defaults = [
            'title'         => __('Script Selection', DOMAIN),
            'selector_type' => 'oneline',
            'separator'     => '&nbsp;|&nbsp;',
            'cir_caption'   => __('Ћирилица', DOMAIN),
            'lat_caption'   => __('Latinica', DOMAIN),
            'inactive_only' => false,
        ];

        parent::__construct(
            $widget_id,
            __('Serbian Script selector', DOMAIN),
            $widget_opts
        );

    }

    public function form($instance)
    {

        $instance = wp_parse_args( (array) $instance, $this->defaults );

        printf(
            '<p>
                <label for="%s">%s</label>
                <input id="%s" name="%s" value="%s" type="text" class="widefat">
            </p>',
            $this->get_field_id( 'title' ),
            __('Title', DOMAIN),
            $this->get_field_id( 'title' ),
            $this->get_field_name( 'title' ),
            esc_attr( $instance['title'] )
        );

        printf(
            '<h4>%s</h4>',
            __('Link Options', DOMAIN)
        );

        printf(
            '<p>
                <label for="%s">%s</label>
                <input id="%s" name="%s" value="%s" type="text" class="widefat">
            </p>',
            $this->get_field_id( 'cir_caption' ),
            __('Link text - Cyrillic', DOMAIN),
            $this->get_field_id( 'cir_caption' ),
            $this->get_field_name( 'cir_caption' ),
            esc_attr( $instance['cir_caption'] )
        );

        printf(
            '<p>
                <label for="%s">%s</label>
                <input id="%s" name="%s" value="%s" type="text" class="widefat">
            </p>',
            $this->get_field_id( 'lat_caption' ),
            __('Link text - Latin', DOMAIN),
            $this->get_field_id( 'lat_caption' ),
            $this->get_field_name( 'lat_caption' ),
            esc_attr( $instance['lat_caption'] )
        );

        printf(
            '<h4>%s</h4>',
            __('Display Options', DOMAIN)
        );

        printf(
            '<p>
                <label for="%s">%s</label>
                <select id="%s" name="%s" class="widefat">
                    %s
                </select>
            </p>',
            $this->get_field_id( 'selector_type' ),
            __('Selector style', DOMAIN),
            $this->get_field_id( 'selector_type' ),
            $this->get_field_name( 'selector_type' ),
            $this->selector_select($instance)
        );

        printf(
            '<p>
                <label for="%s">%s</label>
                <input id="%s" name="%s" value="%s" type="text" class="widefat">
            </p>',
            $this->get_field_id( 'separator' ),
            __('Separator (oneline)', DOMAIN),
            $this->get_field_id( 'separator' ),
            $this->get_field_name( 'separator' ),
            esc_attr( $instance['separator'] )
        );

    }

    private function selector_select($instance)
    {

        $selectors = [
            'oneline' => __('One Line', DOMAIN),
            'list'    => __('Dropdown', DOMAIN),
            'links'   => __('List', DOMAIN) 
        ];

        $html = '';

        foreach ($selectors as $value => $title) :

            $html .= sprintf(
                '<option %s value="%s">%s</option>',
                selected($instance['selector_type'], $value, false),
                $value,
                $title
            );

        endforeach;

        return $html;

    }

    public function update($new_instance,$old_instance)
    {

        $instance = $old_instance;
        $instance['title']         = strip_tags( $new_instance['title'] );
        $instance['cir_caption']   = strip_tags( $new_instance['cir_caption'] );
        $instance['lat_caption']   = strip_tags( $new_instance['lat_caption'] );
        $instance['separator'] = strip_tags( $new_instance['separator'] );
        $instance['selector_type'] = $new_instance['selector_type'];
        

        return $instance;

    }

    public function widget($args, $instance)
    {

        extract( $args );
        $instance = wp_parse_args( (array) $instance, $this->defaults );

        echo $before_widget;

        $title = apply_filters( 'widget_title', $instance['title'] );

        if ( !empty( $title ) ) :
            echo $before_title . $title . $after_title;
        endif;

        script_selector($instance);

        echo $after_widget;

    }

}
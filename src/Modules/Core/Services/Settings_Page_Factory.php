<?php

namespace STL\Core\Services;

use WPTechnix\WP_Settings_Builder\Settings_Builder;
use WPTechnix\WP_Settings_Builder\Settings_Page;

class Settings_Page_Factory {
    /**
     * Makes a Settings_Page instance from the given configuration array.
     *
     * @param array<string,mixed> $config Settings page configuration array.
     * @return Settings_Page
     */
    public static function make( array $config ): Settings_Page {
        $page = ( new Settings_Builder() )
            ->create( $config['page']['option_name'], $config['page']['slug'] )
            ->set_page_title( $config['page']['page_title'] )
            ->set_menu_title( $config['page']['menu_title'] )
            ->set_parent_slug( $config['page']['parent_slug'] )
            ->set_capability( $config['page']['capability'] );

        foreach ( $config['tabs'] as $tab ) {
            $page->add_tab( $tab['id'], $tab['title'] ?? '', $tab['icon'] ?? null );
        }

        foreach ( $config['sections'] as $section ) {
            $page->add_section(
                $section['id'],
                $section['title'] ?? '',
                $section['description'] ?? null,
                $section['tab'] ?? null,
            );
        }

        foreach ( $config['fields'] as $field ) {
            $page->add_field(
                $field['id'],
                $field['section'],
                $field['type'],
                $field['title'],
                $field['extras'] ?? array(),
            );
        }

        // $page->init();

        return $page;
    }
}

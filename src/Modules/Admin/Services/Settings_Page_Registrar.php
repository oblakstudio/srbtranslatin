<?php
/**
 * Admin settings page registrar.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Services;

use STL\Admin\Settings\Settings_Page_Definition;
use STL\Common\Settings\Settings_Schema;
use WPTechnix\WP_Settings_Builder\Settings_Builder;
use WPTechnix\WP_Settings_Builder\Settings_Page;

final class Settings_Page_Registrar {
    public function __construct(
        private Settings_Builder $builder,
        private Settings_Page_Definition $definition,
    ) {
    }

    public function register(): Settings_Page {
        $page = Settings_Builder::get_instance( Settings_Schema::PAGE_SLUG );

        if ( $page instanceof Settings_Page ) {
            return $page;
        }

        $page = $this->builder->create( Settings_Schema::OPTION_NAME, Settings_Schema::PAGE_SLUG );

        $page
            ->set_page_title( \__( 'Latinisation', 'srbtranslatin' ) )
            ->set_menu_title( \__( 'Settings', 'default' ) )
            ->set_parent_slug( 'options-general.php' )
            ->set_capability( 'manage_options' );

        $schema = $this->definition->build();

        foreach ( $schema['tabs'] as $tab ) {
            $page->add_tab( $tab['id'], $tab['title'] ?? '', $tab['icon'] );
        }

        foreach ( $schema['sections'] as $section ) {
            $page->add_section(
                $section['id'],
                $section['title'] ?? '',
                $section['description'],
                $section['tab'],
            );
        }

        foreach ( $schema['fields'] as $field ) {
            $page->add_field(
                $field['id'],
                $field['section'],
                $field['type'],
                $field['title'],
                $field['extras'] ?? array(),
            );
        }

        $page->init();

        return $page;
    }
}

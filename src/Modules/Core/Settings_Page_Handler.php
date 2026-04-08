<?php
/**
 * Settings page boot handler.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Core;

use STL\Common\Settings\Settings_Schema;
use WPTechnix\WP_Settings_Builder\Settings_Builder;
use WPTechnix\WP_Settings_Builder\Settings_Page;

final class Settings_Page_Handler {
    public function __construct(
        private Settings_Builder $builder,
        private Settings_Page_Config $config,
    ) {
    }

    public function boot(): ?Settings_Page {
        $this->load_translations();

        if ( ! \is_admin() ) {
            return null;
        }

        $schema = $this->config->get();
        $page = Settings_Builder::get_instance( $schema['page']['slug'] ?? Settings_Schema::PAGE_SLUG );

        if ( $page instanceof Settings_Page ) {
            return $page;
        }

        $page = $this->builder->create(
            $schema['page']['option_name'],
            $schema['page']['slug'],
        );

        $page
            ->set_page_title( $schema['page']['page_title'] )
            ->set_menu_title( $schema['page']['menu_title'] )
            ->set_parent_slug( $schema['page']['parent_slug'] )
            ->set_capability( $schema['page']['capability'] );

        foreach ( $schema['tabs'] as $tab ) {
            $page->add_tab( $tab['id'], $tab['title'] ?? '', $tab['icon'] ?? null );
        }

        foreach ( $schema['sections'] as $section ) {
            $page->add_section(
                $section['id'],
                $section['title'] ?? '',
                $section['description'] ?? null,
                $section['tab'] ?? null,
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

    private function load_translations(): void {
        \load_plugin_textdomain(
            'srbtranslatin',
            false,
            \dirname( STL_BASE ) . '/languages',
        );
    }
}

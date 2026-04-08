<?php
/**
 * Settings page definition.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings;

use STL\Admin\Settings\Definitions\Settings_Tab_Definition;

final class Settings_Page_Definition {
    /**
     * @param array<int,Settings_Tab_Definition> $tabs
     */
    public function __construct(
        private array $tabs,
    ) {
    }

    /**
     * Build a normalized schema for the admin page.
     *
     * @return array{tabs: array<int,array<string,string|null>>, sections: array<int,array<string,string|null>>, fields: array<int,array<string,mixed>>}
     */
    public function build(): array {
        $schema = array(
            'tabs'     => array(),
            'sections' => array(),
            'fields'   => array(),
        );

        foreach ( $this->tabs as $tab ) {
            $tab_schema            = $tab->build();
            $schema['tabs'][]      = $tab_schema['tab'];
            $schema['sections'][]  = $tab_schema['section'];
            $schema['fields']      = array_merge( $schema['fields'], $tab_schema['fields'] );
        }

        return $schema;
    }
}

<?php
/**
 * Advanced tab definition.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings\Definitions;

use STL\Admin\Settings\Admin_Context;

final class Advanced_Tab_Definition implements Settings_Tab_Definition {
    public function __construct(
        private Admin_Context $context,
    ) {
    }

    public function build(): array {
        $locale              = $this->context->get_locale();
        $disable_permalinks  = 'sr_RS' === $locale || 'bs_BA' === $locale;
        $permalink_help_text = \__( 'Fixes permalinks for cyrillic scripts', 'srbtranslatin' );

        if ( $disable_permalinks ) {
            $permalink_help_text .= ' ' . \sprintf(
                \__( 'This option is currently disabled because your current locale is set to %s which will automatically change permalinks.', 'srbtranslatin' ),
                $locale,
            );
        }

        return array(
            'tab'     => array(
                'id'    => 'advanced',
                'title' => \__( 'Advanced', 'default' ),
                'icon'  => 'dashicons-admin-tools',
            ),
            'section' => array(
                'id'          => 'advanced',
                'title'       => \_x( 'Advanced settings', 'section name', 'srbtranslatin' ),
                'description' => \__( 'Advanced settings control permalink and search settings', 'srbtranslatin' ),
                'tab'         => 'advanced',
            ),
            'fields'  => array(
                array(
                    'id'      => 'fix_permalinks',
                    'type'    => 'checkbox',
                    'title'   => \__( 'Fix Permalinks', 'srbtranslatin' ),
                    'section' => 'advanced',
                    'extras'  => array(
                        'default'         => false,
                        'description'     => $permalink_help_text,
                        'html_attributes' => array(
                            'disabled' => $disable_permalinks,
                        ),
                    ),
                ),
                array(
                    'id'      => 'fix_search',
                    'type'    => 'checkbox',
                    'title'   => \__( 'Fix Search', 'srbtranslatin' ),
                    'section' => 'advanced',
                    'extras'  => array(
                        'default'     => true,
                        'description' => \__( 'Enables searching cyrillic content via latin script', 'srbtranslatin' ),
                    ),
                ),
                array(
                    'id'      => 'fix_ajax',
                    'type'    => 'checkbox',
                    'title'   => \__( 'Fix Ajax', 'srbtranslatin' ),
                    'section' => 'advanced',
                    'extras'  => array(
                        'default'     => false,
                        'description' => \__( 'Transliterates ajax calls', 'srbtranslatin' ),
                    ),
                ),
                array(
                    'id'      => 'fix_titles',
                    'type'    => 'checkbox',
                    'title'   => \__( 'Fix Titles', 'srbtranslatin' ),
                    'section' => 'advanced',
                    'extras'  => array(
                        'default'     => false,
                        'description' => \__( 'Fixes titles for cyrillic scripts', 'srbtranslatin' ),
                    ),
                ),
            ),
        );
    }
}

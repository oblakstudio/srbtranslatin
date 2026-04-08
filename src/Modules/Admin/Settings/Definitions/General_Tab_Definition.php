<?php
/**
 * General tab definition.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings\Definitions;

final class General_Tab_Definition implements Settings_Tab_Definition {
    public function build(): array {
        return array(
            'tab'     => array(
                'id'    => 'general',
                'title' => \__( 'General', 'srbtranslatin' ),
                'icon'  => 'dashicons-admin-generic',
            ),
            'section' => array(
                'id'          => 'general',
                'title'       => \_x( 'General settings', 'section name', 'srbtranslatin' ),
                'description' => \__( 'General settings control main functionality of the plugin', 'srbtranslatin' ),
                'tab'         => 'general',
            ),
            'fields'  => array(
                array(
                    'id'      => 'enabled_scripts',
                    'type'    => 'buttons_group',
                    'title'   => \__( 'Enabled scripts', 'srbtranslatin' ),
                    'section' => 'general',
                    'extras'  => array(
                        'default'     => 'both',
                        'description' => \__( 'Cyrillic and Latin', 'srbtranslatin' ),
                        'options'     => array(
                            'cir'  => 'Ћ ' . \__( 'Cyrillic', 'srbtranslatin' ),
                            'lat'  => 'Ć ' . \__( 'Latin', 'srbtranslatin' ),
                            'both' => 'Ć Ћ ' . \__( 'Both', 'srbtranslatin' ),
                        ),
                    ),
                ),
                array(
                    'id'      => 'default_script',
                    'type'    => 'select',
                    'title'   => \__( 'Default script', 'srbtranslatin' ),
                    'section' => 'general',
                    'extras'  => array(
                        'default'     => 'cir',
                        'description' => \__( 'Default script used for the website if user did not select a script', 'srbtranslatin' ),
                        'options'     => array(
                            'cir' => \__( 'Cyrillic', 'srbtranslatin' ),
                            'lat' => \__( 'Latin', 'srbtranslatin' ),
                        ),
                        'conditions'  => array(
                            'rules' => array(
                                array(
                                    'field'    => 'enabled_scripts',
                                    'operator' => '=',
                                    'value'    => 'both',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'id'      => 'url_param',
                    'type'    => 'text',
                    'title'   => \__( 'URL Parameter', 'srbtranslatin' ),
                    'section' => 'general',
                    'extras'  => array(
                        'default'     => 'pismo',
                        'description' => \__( 'URL parameter used for script selector', 'srbtranslatin' ),
                    ),
                ),
            ),
        );
    }
}

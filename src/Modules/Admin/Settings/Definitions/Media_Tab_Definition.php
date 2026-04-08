<?php
/**
 * Media tab definition.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings\Definitions;

final class Media_Tab_Definition implements Settings_Tab_Definition {
    public function build(): array {
        return array(
            'tab'     => array(
                'id'    => 'media',
                'title' => \__( 'Media', 'srbtranslatin' ),
                'icon'  => 'dashicons-format-image',
            ),
            'section' => array(
                'id'          => 'media',
                'title'       => \_x( 'File and Media settings', 'section name', 'srbtranslatin' ),
                'description' => \__( 'File and media settings control filename transliteration and media saving', 'srbtranslatin' ),
                'tab'         => 'media',
            ),
            'fields'  => array(
                array(
                    'id'      => 'transliterate_uploads',
                    'type'    => 'checkbox',
                    'title'   => \__( 'Transliterate uploads', 'srbtranslatin' ),
                    'section' => 'media',
                    'extras'  => array(
                        'default'     => true,
                        'description' => \__( 'Transliterate filenames on upload', 'srbtranslatin' ),
                    ),
                ),
                array(
                    'id'      => 'separate_uploads',
                    'type'    => 'checkbox',
                    'title'   => \__( 'Script specific filenames', 'srbtranslatin' ),
                    'section' => 'media',
                    'extras'  => array(
                        'default'     => true,
                        'description' => \__( 'Check this box if you want to have separate filenames for each script', 'srbtranslatin' ),
                    ),
                ),
                array(
                    'id'      => 'filename_separator',
                    'type'    => 'text',
                    'title'   => \__( 'Filename separator', 'srbtranslatin' ),
                    'section' => 'media',
                    'extras'  => array(
                        'default'         => '-',
                        'description'     => \__( 'Separator used for script specific filenames', 'srbtranslatin' ),
                        'html_attributes' => array(
                            'class' => 'small-text',
                        ),
                    ),
                ),
                array(
                    'id'      => 'transliteration_method',
                    'type'    => 'select',
                    'title'   => \__( 'Transliteration method', 'srbtranslatin' ),
                    'section' => 'media',
                    'extras'  => array(
                        'default'     => 'website',
                        'description' => \__( 'Choose if you want to limit the script specific filenames on the entire website, or in content only', 'srbtranslatin' ),
                        'options'     => array(
                            'website' => \__( 'Entire website', 'srbtranslatin' ),
                            'content' => \__( 'Content only', 'srbtranslatin' ),
                        ),
                    ),
                ),
            ),
        );
    }
}

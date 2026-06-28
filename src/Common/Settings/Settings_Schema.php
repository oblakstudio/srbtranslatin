<?php
/**
 * Settings schema definitions.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Common\Settings;

final class Settings_Schema {
    public const OPTION_NAME = 'srbtranslatin_settings';
    public const PAGE_SLUG   = 'srbtranslatin-settings';

    /**
     * Get grouped settings defaults.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function defaults(): array {
        return array(
            'general'  => array(
                'enabled_scripts' => 'both',
                'default_script'  => 'cir',
                'url_param'       => 'pismo',
            ),
            'menu'     => array(
                'extend'        => true,
                'extend_menu'   => '',
                'selector_type' => 'submenu',
                'menu_title'    => 'Script',
            ),
            'media'    => array(
                'transliterate_uploads' => true,
                'separate_uploads'      => true,
                'filename_separator'    => '-',
                'transliteration_method'=> 'website',
            ),
            'wpml'     => array(
                'extend_ls' => false,
            ),
            'advanced' => array(
                'fix_permalinks' => false,
                'fix_search'     => true,
                'fix_ajax'       => false,
                'fix_titles'     => false,
            ),
        );
    }

    /**
     * Return a grouped map of option keys.
     *
     * @return array<string,array<string,string>>
     */
    public static function keys(): array {
        $keys = array();

        foreach ( self::defaults() as $group => $settings ) {
            $keys[ $group ] = array_combine( array_keys( $settings ), array_keys( $settings ) );
        }

        return $keys;
    }
}

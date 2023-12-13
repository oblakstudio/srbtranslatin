<?php
/**
 * WPML class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Language;

/**
 * WPML Plugin compatibility
 */
class WPML {
    /**
     * Primary language
     *
     * @var string
     */
    protected $primary_lang = null;

    /**
     * Class constructor
     */
    public function __construct() {
        add_filter( 'icl_ls_languages', array( $this, 'extend_wpml_language_selector' ), 99, 1 );
    }

    /**
     * Extends WPML language selector
     *
     * @param  array[] $languages Languages for the language selector.
     * @return array[]            Extended languages.
     */
    public function extend_wpml_language_selector( $languages ) {
        if ( ! STL()->get_settings( 'wpml', 'extend_ls' ) ) {
            return $languages;
        }

        $this->primary_lang = $this->detect_primary_lang( $languages );

        if ( 'sr' !== $this->primary_lang ) {
            return $languages;
        }

        $serbian_ls = $languages['sr'] ?? $languages['mk'];
        $script     = STL()->manager->get_script();

        $languages['sr'] = array_merge(
            $serbian_ls,
            array(
                'native_name'     => do_shortcode( '[stl_cyr]српски (ћир)[/stl_cyr]' ),
                'translated_name' => "{$serbian_ls['translated_name']} (cyr)",
                'url'             => add_query_arg( STL()->manager->get_url_param(), 'cir', $serbian_ls['url'] ),
                'active'          => 'cir' === $script,
            )
        );

        $languages['sr@lat'] = array_merge(
            $serbian_ls,
            array(
                'native_name'     => 'srpski (lat)',
                'translated_name' => "{$serbian_ls['translated_name']} (lat)",
                'url'             => add_query_arg( STL()->manager->get_url_param(), 'lat', $serbian_ls['url'] ),
                'active'          => 'lat' === $script,
            )
        );

        return $languages;
    }

    /**
     * Detects primary language from WPML languages
     *
     * @param  array<string, array> $languages WPML languages.
     * @return string                          Primary language
     */
    protected function detect_primary_lang( $languages ) {
        $languages = array_keys( $languages );

        if ( in_array( 'sr', $languages, true ) ) {
            return 'sr';
        }

        if ( in_array( 'mk', $languages, true ) ) {
            return 'mk';
        }

        return 'en';
    }
}

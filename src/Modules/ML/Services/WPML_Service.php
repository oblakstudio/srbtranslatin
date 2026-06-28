<?php
/**
 * WPML_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

use STL\Common\Settings\Plugin_Settings;
use STL\Translit\Services\Script_Manager;

/**
 * Extend WPML language switcher output for Serbian script variants.
 */
final class WPML_Service {
    /**
     * Constructor.
     *
     * @param Script_Manager  $script_manager Runtime script manager.
     * @param Plugin_Settings $settings Plugin settings.
     * @param mixed           $url_builder Optional URL builder callback for tests.
     * @param mixed           $shortcode_renderer Optional shortcode renderer callback for tests.
     */
    public function __construct(
        private Script_Manager $script_manager,
        private Plugin_Settings $settings,
        private mixed $url_builder = null,
        private mixed $shortcode_renderer = null,
    ) {
    }

    /**
     * Extend the WPML language selector with Cyrillic and Latin Serbian entries.
     *
     * @param array<string,array<string,mixed>> $languages WPML language data.
     * @return array<string,array<string,mixed>>
     */
    public function extend_language_selector( array $languages ): array {
        if ( ! (bool) $this->settings->get( 'wpml', 'extend_ls', false ) ) {
            return $languages;
        }

        $primary_language = $this->detect_primary_language( $languages );

        if ( null === $primary_language ) {
            return $languages;
        }

        $serbian = $languages[ $primary_language ];
        $script = $this->script_manager->get_script();
        $url_param = $this->script_manager->get_url_param();

        $languages['sr'] = \array_merge(
            $serbian,
            array(
                'native_name' => $this->render_shortcode( '[stl_cyr]српски (ћир)[/stl_cyr]' ),
                'translated_name' => (string) ( $serbian['translated_name'] ?? 'Serbian' ) . ' (cyr)',
                'url' => $this->build_url( $url_param, 'cir', (string) $serbian['url'] ),
                'active' => 'cir' === $script,
            )
        );

        $languages['sr@lat'] = \array_merge(
            $serbian,
            array(
                'native_name' => 'srpski (lat)',
                'translated_name' => (string) ( $serbian['translated_name'] ?? 'Serbian' ) . ' (lat)',
                'url' => $this->build_url( $url_param, 'lat', (string) $serbian['url'] ),
                'active' => 'lat' === $script,
            )
        );

        return $languages;
    }

    /**
     * Detect the primary Serbian-family language key from WPML data.
     *
     * @param array<string,array<string,mixed>> $languages Language list.
     * @return string|null
     */
    private function detect_primary_language( array $languages ): ?string {
        if ( isset( $languages['sr'] ) ) {
            return 'sr';
        }

        if ( isset( $languages['mk'] ) ) {
            return 'mk';
        }

        return null;
    }

    /**
     * Build a script-switching URL.
     *
     * @param string $key Query parameter name.
     * @param string $value Script identifier.
     * @param string $url Base URL.
     * @return string
     */
    private function build_url( string $key, string $value, string $url ): string {
        if ( \is_callable( $this->url_builder ) ) {
            return (string) \call_user_func( $this->url_builder, $key, $value, $url );
        }

        return (string) \add_query_arg( $key, $value, $url );
    }

    /**
     * Render shortcode content when a renderer is available.
     *
     * @param string $content Shortcode content.
     * @return string
     */
    private function render_shortcode( string $content ): string {
        if ( \is_callable( $this->shortcode_renderer ) ) {
            return (string) \call_user_func( $this->shortcode_renderer, $content );
        }

        return \function_exists( 'do_shortcode' ) ? (string) \do_shortcode( $content ) : $content;
    }
}

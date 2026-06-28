<?php
/**
 * Media_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use Oblak\Transliterator;
use STL\Common\Settings\Plugin_Settings;

/**
 * Media filename and image URL transliteration service.
 */
final class Media_Service {
    /**
     * Constructor.
     *
     * @param Script_Manager      $script_manager Runtime script manager.
     * @param Plugin_Settings     $settings Plugin settings.
     * @param Transliterator|null $transliterator Transliterator instance.
     */
    public function __construct(
        private Script_Manager $script_manager,
        private Plugin_Settings $settings,
        private ?Transliterator $transliterator = null,
    ) {
        $this->transliterator ??= Transliterator::instance();
    }

    /**
     * Transliterate an upload filename to cut Latin when enabled.
     *
     * @param string $filename Upload filename.
     * @return string
     */
    public function transliterate_filename( string $filename ): string {
        return (bool) $this->settings->get( 'media', 'transliterate_uploads', true )
            ? $this->transliterator->cirToCutLat( $filename )
            : $filename;
    }

    /**
     * Rewrite image src/srcset URLs from script-specific Cyrillic to Latin markers.
     *
     * @param string $html HTML fragment or document.
     * @return string
     */
    public function rewrite_image_urls( string $html ): string {
        if ( ! $this->script_manager->should_transliterate() || ! $this->separate_uploads_enabled() ) {
            return $html;
        }

        $separator = $this->filename_separator();
        $from      = $separator . 'cir';

        if ( ! \str_contains( $html, $from ) ) {
            return $html;
        }

        $rewritten = \preg_replace_callback(
            '/<img\b[^>]*>/i',
            fn( array $matches ): string => $this->rewrite_image_tag( $matches[0], $from, $separator . 'lat' ),
            $html,
        );

        return \is_string( $rewritten ) ? $rewritten : $html;
    }

    /**
     * Rewrite src and srcset values in a single image tag.
     *
     * @param string $tag Image tag.
     * @param string $from Source marker.
     * @param string $to Target marker.
     * @return string
     */
    private function rewrite_image_tag( string $tag, string $from, string $to ): string {
        $rewritten = \preg_replace_callback(
            '/\s(src|srcset)\s*=\s*(["\'])(.*?)\2/si',
            static fn( array $matches ): string => \sprintf(
                ' %s=%s%s%s',
                $matches[1],
                $matches[2],
                \str_replace( $from, $to, $matches[3] ),
                $matches[2],
            ),
            $tag,
        );

        return \is_string( $rewritten ) ? $rewritten : $tag;
    }

    /**
     * Get the configured script filename separator.
     *
     * @return string
     */
    private function filename_separator(): string {
        $separator = (string) $this->settings->get( 'media', 'filename_separator', '-' );

        return '' !== $separator ? $separator : '-';
    }

    /**
     * Check whether script-specific filenames are enabled.
     *
     * @return bool
     */
    private function separate_uploads_enabled(): bool {
        return (bool) $this->settings->get( 'media', 'separate_uploads', true );
    }
}

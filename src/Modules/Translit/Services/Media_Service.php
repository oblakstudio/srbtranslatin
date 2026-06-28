<?php
/**
 * Media_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use Oblak\Transliterator;

/**
 * Handle upload filename and script-specific media URL parity.
 */
final class Media_Service {
    /**
     * Constructor.
     *
     * @param Script_Manager $script_manager Runtime script manager.
     * @param bool $transliterate_uploads Whether upload filenames should be transliterated.
     * @param bool $separate_uploads Whether script-specific filenames should be rewritten.
     * @param string $filename_separator Configured script marker separator.
     * @param string $transliteration_method Media rewrite scope.
     * @param Transliterator|null $transliterator Transliterator instance.
     */
    public function __construct(
        private Script_Manager $script_manager,
        private bool $transliterate_uploads,
        private bool $separate_uploads,
        private string $filename_separator,
        private string $transliteration_method,
        private ?Transliterator $transliterator = null,
    ) {
        $this->transliterator ??= Transliterator::instance();
    }

    /**
     * Transliterate upload filenames to ASCII-safe cut Latin.
     *
     * @param string $filename Sanitized filename.
     * @return string
     */
    public function sanitize_file_name( string $filename ): string {
        return $this->transliterate_uploads ? $this->transliterator->cirToCutLat( $filename ) : $filename;
    }

    /**
     * Rewrite media URLs inside content.
     *
     * @param string $content HTML or text content.
     * @return string
     */
    public function rewrite_content( string $content ): string {
        return $this->rewrite_media_urls( $content );
    }

    /**
     * Rewrite media URLs inside whole-page output when configured.
     *
     * @param string $output Buffered output.
     * @return string
     */
    public function rewrite_page_output( string $output ): string {
        return 'website' === $this->normalize_method() ? $this->rewrite_media_urls( $output ) : $output;
    }

    /**
     * Rewrite configured and legacy script filename markers.
     *
     * @param string $contents HTML, URL, or text that may contain media URLs.
     * @return string
     */
    public function rewrite_media_urls( string $contents ): string {
        if ( ! $this->separate_uploads ) {
            return $contents;
        }

        $target = $this->script_manager->is_latin() ? 'lat' : 'cir';
        $source = 'lat' === $target ? 'cir' : 'lat';

        return \str_replace(
            $this->markers_for_script( $source ),
            $this->markers_for_script( $target ),
            $contents
        );
    }

    /**
     * Check if media URLs should be rewritten through the content filter.
     *
     * @return bool
     */
    public function uses_content_method(): bool {
        return 'content' === $this->normalize_method();
    }

    /**
     * Build marker variants for the given script.
     *
     * @param string $script Script marker.
     * @return array<int,string>
     */
    private function markers_for_script( string $script ): array {
        return \array_values(
            \array_unique(
                array(
                    $this->normalize_separator() . $script,
                    '__' . $script,
                )
            )
        );
    }

    /**
     * Normalize the media transliteration method.
     *
     * @return string
     */
    private function normalize_method(): string {
        return 'content' === \sanitize_key( $this->transliteration_method ) ? 'content' : 'website';
    }

    /**
     * Normalize the configured filename separator.
     *
     * @return string
     */
    private function normalize_separator(): string {
        return '' !== $this->filename_separator ? $this->filename_separator : '-';
    }
}

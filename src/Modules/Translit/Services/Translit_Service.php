<?php
/**
 * Translit_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use Oblak\Transliterator;

/**
 * Barebones transliteration runtime service.
 */
final class Translit_Service {
    /**
     * Constructor.
     *
     * @param Script_Manager $script_manager Script manager instance.
     * @param Transliterator|null $transliterator Transliterator instance.
     */
    public function __construct(
        private Script_Manager $script_manager,
        private ?Transliterator $transliterator = null,
        private ?Shortcode_Service $shortcodes = null,
    ) {
        $this->transliterator ??= Transliterator::instance();
    }

    /**
     * Start output buffering for page/RSS rendering.
     *
     * @return void
     */
    public function buffer_start(): void {
        \ob_start( array( $this, 'buffer_end' ) );
    }

    /**
     * Transliterate buffered output.
     *
     * @param string $contents Buffered contents.
     * @return string
     */
    public function buffer_end( string $contents ): string {
        $transliterated = $this->transliterate( $contents );

        return $this->shortcodes?->restore_placeholders( $transliterated ) ?? $transliterated;
    }

    /**
     * Transliterate buffered AJAX output.
     *
     * Plain text gets transliterated directly. JSON payloads get the Black Art
     * treatment, where only string leaves are touched before the response is
     * encoded again.
     *
     * @param string $contents Buffered contents.
     * @return string
     */
    public function ajax_buffer_end( string $contents ): string {
        $decoded = \json_decode( $contents, true );

        if ( null !== $decoded && \is_array( $decoded ) ) {
            return (string) \wp_json_encode( $this->transliterate_ajax_payload( $decoded ), JSON_UNESCAPED_UNICODE );
        }

        return $this->transliterate( $contents );
    }

    /**
     * Transliterate gettext strings.
     *
     * @param string $translation Translated text.
     * @return string
     */
    public function translate_gettext( string $translation ): string {
        return $this->transliterate( $translation );
    }

    /**
     * Transliterate arbitrary text using the active runtime state.
     *
     * @param string $contents Input text.
     * @return string
     */
    public function transliterate_text( string $contents ): string {
        return $this->transliterate( $contents );
    }

    /**
     * Transliterate ngettext strings.
     *
     * @param string $translation Translated text.
     * @return string
     */
    public function translate_ngettext( string $translation ): string {
        return $this->transliterate( $translation );
    }

    /**
     * Transliterate gettext with context strings.
     *
     * @param string $translation Translated text.
     * @return string
     */
    public function translate_gettext_with_context( string $translation ): string {
        return $this->transliterate( $translation );
    }

    /**
     * Transliterate ngettext with context strings.
     *
     * @param string $translation Translated text.
     * @return string
     */
    public function translate_ngettext_with_context( string $translation ): string {
        return $this->transliterate( $translation );
    }

    /**
     * Recursively transliterate string leaves within an AJAX payload.
     *
     * @param array<array-key,mixed> $payload Decoded AJAX payload.
     * @return array<array-key,mixed>
     */
    private function transliterate_ajax_payload( array $payload ): array {
        foreach ( $payload as $key => $value ) {
            if ( \is_array( $value ) ) {
                $payload[ $key ] = $this->transliterate_ajax_payload( $value );
                continue;
            }

            $payload[ $key ] = $this->maybe_transliterate( $value );
        }

        return $payload;
    }

    /**
     * Transliterate the given value if it is a string.
     *
     * @param mixed $value AJAX payload value.
     * @return mixed
     */
    private function maybe_transliterate( mixed $value ): mixed {
        if ( ! \is_string( $value ) ) {
            return $value;
        }

        return $this->transliterate( \html_entity_decode( $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' ) );
    }

    /**
     * Transliterate the given string if runtime state requires it.
     *
     * @param string $contents Input text.
     * @return string
     */
    private function transliterate( string $contents ): string {
        return $this->script_manager->should_transliterate()
            ? $this->transliterator->cirToLat( $contents )
            : $contents;
    }
}

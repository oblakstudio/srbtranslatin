<?php
/**
 * Shortcode_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

/**
 * Register and process transliteration shortcodes.
 */
final class Shortcode_Service {
    /** @var array<string,string> */
    private array $placeholders = array();

    public function __construct(
        private Script_Manager $script_manager,
        private mixed $register_shortcode_callback = null,
        private mixed $uuid_generator = null,
    ) {
    }

    /**
     * Register legacy shortcode tags.
     *
     * @return void
     */
    public function register_shortcodes(): void {
        $this->register_shortcode( 'stl_cyr', array( $this, 'render_cyrillic_shortcode' ) );
        $this->register_shortcode( 'stl_cyrillic', array( $this, 'render_cyrillic_shortcode' ) );
        $this->register_shortcode( 'stl_translit', array( $this, 'render_translit_shortcode' ) );
        $this->register_shortcode( 'stl_selective_output', array( $this, 'render_selective_output_shortcode' ) );
        $this->register_shortcode( 'stl_show', array( $this, 'render_selective_output_shortcode' ) );
    }

    /**
     * Render the Cyrillic-protection shortcode.
     *
     * @param array<string,mixed> $atts Shortcode attributes.
     * @param string|null         $content Inner shortcode content.
     * @return string
     */
    public function render_cyrillic_shortcode( array $atts = array(), ?string $content = null ): string {
        $content ??= '';

        if ( ! $this->is_serbian_language() || ! $this->script_manager->is_latin() ) {
            return $content;
        }

        return $this->protect( $content );
    }

    /**
     * Render the explicit transliteration shortcode.
     *
     * @param array<string,mixed> $atts Shortcode attributes.
     * @param string|null         $content Inner shortcode content.
     * @param string              $tag Shortcode tag.
     * @return string
     */
    public function render_translit_shortcode( array $atts = array(), ?string $content = null, string $tag = '' ): string {
        $content ??= '';
        $atts = \shortcode_atts( array( 'latin' => '' ), $atts, $tag );

        if ( ! $this->is_serbian_language() || ! $this->script_manager->is_latin() ) {
            return $content;
        }

        return $this->protect( (string) $atts['latin'] );
    }

    /**
     * Render the selective-output shortcode.
     *
     * @param array<string,mixed> $atts Shortcode attributes.
     * @param string|null         $content Inner shortcode content.
     * @param string              $tag Shortcode tag.
     * @return string
     */
    public function render_selective_output_shortcode( array $atts = array(), ?string $content = null, string $tag = '' ): string {
        $content ??= '';
        $atts = \shortcode_atts( array( 'script' => '' ), $atts, $tag );
        $target_script = (string) $atts['script'];

        if ( ! $this->is_serbian_language() || '' === $target_script ) {
            return '';
        }

        return \str_starts_with( $target_script, $this->script_manager->get_script() ) ? $content : '';
    }

    /**
     * Restore protected placeholders after transliteration.
     *
     * @param string $contents Buffered output.
     * @return string
     */
    public function restore_placeholders( string $contents ): string {
        if ( array() === $this->placeholders ) {
            return $contents;
        }

        return \strtr( $contents, $this->placeholders );
    }

    /**
     * Store protected output and return a placeholder token.
     *
     * @param string $contents Protected output.
     * @return string
     */
    private function protect( string $contents ): string {
        $uuid = $this->generate_uuid();
        $this->placeholders[ $uuid ] = $contents;

        return $uuid;
    }

    /**
     * Register a shortcode callback.
     *
     * @param string   $tag Shortcode tag.
     * @param callable $callback Callback function.
     * @return void
     */
    private function register_shortcode( string $tag, callable $callback ): void {
        if ( \is_callable( $this->register_shortcode_callback ) ) {
            \call_user_func( $this->register_shortcode_callback, $tag, $callback );
            return;
        }

        \add_shortcode( $tag, $callback );
    }

    /**
     * Generate a placeholder UUID.
     *
     * @return string
     */
    private function generate_uuid(): string {
        if ( \is_callable( $this->uuid_generator ) ) {
            return (string) \call_user_func( $this->uuid_generator );
        }

        return \wp_generate_uuid4();
    }

    /**
     * Check whether the current runtime language is Serbian.
     *
     * @return bool
     */
    private function is_serbian_language(): bool {
        return 1 === \preg_match( '/^sr(?:[_-]|$)/i', $this->script_manager->get_language() );
    }
}

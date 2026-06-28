<?php
/**
 * Title_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Common\Settings\Plugin_Settings;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Translit_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register title transliteration hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Title_Handler {
    /**
     * Constructor.
     *
     * @param Translit_Service $service Transliteration runtime service.
     * @param Script_Manager   $script_manager Script state manager.
     * @param Plugin_Settings  $settings Plugin settings.
     * @param mixed            $theme_supports_callback Optional current_theme_supports callback override.
     */
    public function __construct(
        private Translit_Service $service,
        private Script_Manager $script_manager,
        private Plugin_Settings $settings,
        private mixed $theme_supports_callback = null,
    ) {
    }

    /**
     * Transliterate classic title strings.
     *
     * @param string $title Title text.
     * @return string
     */
    #[Filter(
        tag: 'wp_title',
        priority: 100,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    #[Filter(
        tag: 'pre_get_document_title',
        priority: 100,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function transliterate_title( string $title ): string {
        if ( $this->supports_title_tag() || ! $this->should_fix_titles() ) {
            return $title;
        }

        return $this->service->transliterate_text( $title );
    }

    /**
     * Transliterate structured title parts for modern title generation.
     *
     * @param array<string,mixed> $parts Title parts.
     * @return array<string,mixed>
     */
    #[Filter(
        tag: 'document_title_parts',
        priority: 100,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function transliterate_title_parts( array $parts ): array {
        if ( ! $this->should_fix_titles() ) {
            return $parts;
        }

        foreach ( $parts as $key => $value ) {
            if ( \is_string( $value ) ) {
                $parts[ $key ] = $this->service->transliterate_text( $value );
            }
        }

        return $parts;
    }

    /**
     * Check whether title transliteration is enabled and relevant.
     *
     * @return bool
     */
    private function should_fix_titles(): bool {
        return $this->script_manager->should_transliterate()
            && (bool) $this->settings->get( 'advanced', 'fix_titles', false );
    }

    /**
     * Check if the current theme provides title-tag support.
     *
     * @return bool
     */
    private function supports_title_tag(): bool {
        if ( \is_callable( $this->theme_supports_callback ) ) {
            return (bool) \call_user_func( $this->theme_supports_callback, 'title-tag' );
        }

        return \current_theme_supports( 'title-tag' );
    }
}

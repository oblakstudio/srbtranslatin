<?php
/**
 * Translit_Handler class file.
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
 * Register transliteration runtime hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Translit_Handler {
    /**
     * Check whether the handler should initialize for the current script state.
     *
     * @param Script_Manager $script_manager Script manager state.
     * @return bool
     */
    public static function can_initialize( Script_Manager $script_manager ): bool {
        return $script_manager->should_transliterate();
    }

    /**
     * Check whether the current request is a WooCommerce AJAX endpoint.
     *
     * @return bool
     */
    public static function is_wc_ajax_request(): bool {
        return '' !== \xwp_fetch_get_var( 'wc-ajax', '' );
    }

    /**
     * Check whether standard AJAX buffering is enabled.
     *
     * @param Plugin_Settings $settings Plugin settings.
     * @return bool
     */
    public static function should_buffer_ajax( Plugin_Settings $settings ): bool {
        return (bool) $settings->get( 'advanced', 'fix_ajax', false );
    }

    /**
     * Constructor.
     *
     * @param Translit_Service $service Transliteration service.
     */
    public function __construct( private Translit_Service $service ) {
    }

    /**
     * Start output buffering for frontend and feed rendering.
     *
     * @return void
     */
    #[Action(
        tag: 'wp_head',
        priority: 9999,
        context: Action::CTX_FRONTEND,
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    #[Action(
        tag: 'rss_head',
        priority: 9999,
        context: Action::CTX_FRONTEND,
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    #[Action(
        tag: 'atom_head',
        priority: 9999,
        context: Action::CTX_FRONTEND,
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    #[Action(
        tag: 'rdf_head',
        priority: 9999,
        context: Action::CTX_FRONTEND,
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    #[Action(
        tag: 'rss2_head',
        priority: 9999,
        context: Action::CTX_FRONTEND,
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    public function buffer_start(): void {
        \ob_start( array( $this->service, 'buffer_end' ) );
    }

    /**
     * Start output buffering for standard AJAX and WooCommerce AJAX responses.
     *
     * This is the Deep Magic entrypoint. We hook before the response is emitted
     * so the buffer can perform its Heavy Wizardry on the way out.
     *
     * @return void
     */
    #[Action(
        tag: 'admin_init',
        priority: -9999,
        context: Action::CTX_AJAX,
        conditional: array( self::class, 'should_buffer_ajax' ),
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    #[Action(
        tag: 'template_redirect',
        priority: -1,
        context: Action::CTX_FRONTEND,
        conditional: array( self::class, 'is_wc_ajax_request' ),
        args: 0,
        invoke: Action::INV_PROXIED,
    )]
    public function ajax_buffer_start(): void {
        \ob_start( array( $this->service, 'ajax_buffer_end' ) );
    }

    /**
     * Transliterate gettext-family strings.
     *
     * @param string $translation Translated text.
     * @return string
     */
    #[Filter(
        tag: 'gettext',
        priority: 9999,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    #[Filter(
        tag: 'ngettext',
        priority: 9999,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    #[Filter(
        tag: 'gettext_with_context',
        priority: 9999,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    #[Filter(
        tag: 'ngettext_with_context',
        priority: 9999,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function translate( string $translation ): string {
        return $this->service->translate_gettext( $translation );
    }
}

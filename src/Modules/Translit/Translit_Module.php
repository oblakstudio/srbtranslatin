<?php
/**
 * Translit_Module class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit;

use Oblak\Transliterator;
use STL\Common\Settings\Plugin_Settings;
use STL\Translit\Contracts\Persists_Script_Cookie;
use STL\Translit\Contracts\Resolves_Language;
use STL\Translit\Services\Cookie_Persister;
use STL\Translit\Services\Media_Service;
use STL\Translit\Services\Menu_Integration_Service;
use STL\Translit\Services\Permalink_Service;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Translit_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

/**
 * Transliterator module definition.
 */
#[Module(
    container: 'stl',
    hook: 'srbtranslatin_loaded',
    priority: 30,
    handlers: array(
        Handlers\Media_Handler::class,
        Handlers\Menu_Integration_Handler::class,
        Handlers\Permalink_Handler::class,
        Handlers\Search_Handler::class,
        Handlers\Selector_Widget_Handler::class,
        Handlers\Shortcode_Handler::class,
        Handlers\Translit_Handler::class,
        Handlers\Title_Handler::class,
    ),
)]
class Translit_Module {
    /**
     * Get the module configuration.
     *
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            'stl.enabled_scripts'         => \DI\factory(
                static fn( Plugin_Settings $settings ): string => (string) $settings->get( 'general', 'enabled_scripts', 'both' )
            ),
            'stl.script.default'          => \DI\factory(
                static fn( Plugin_Settings $settings ): string => (string) $settings->get( 'general', 'default_script', 'cir' )
            ),
            'stl.script.param'            => \DI\factory(
                static fn( Plugin_Settings $settings ): string => (string) $settings->get( 'general', 'url_param', 'pismo' )
            ),
            'stl.script.req'              => \DI\factory(
                static fn( string $key, string $def = '' ): string => (string) \xwp_fetch_req_var( $key, $def )
            )
                ->parameter( 'key', \DI\get( 'stl.script.param' ) )
                ->parameter( 'def', '' ),
            'stl.script.cookie'           => \DI\factory(
                static fn( string $key, string $def = '' ): string => \xwp_fetch_cookie_var( $key, $def )
            )
                ->parameter( 'key', 'stl_script' )
                ->parameter( 'def', '' ),
            'stl.language.resolver'       => \DI\value( null ),
            Transliterator::class         => \DI\factory( static fn() => Transliterator::instance() ),
            Persists_Script_Cookie::class => \DI\autowire( Cookie_Persister::class ),
            Media_Service::class          => \DI\factory(
                static function ( Script_Manager $script_manager, Plugin_Settings $settings ): Media_Service {
                    return new Media_Service(
                        $script_manager,
                        (bool) $settings->get( 'media', 'transliterate_uploads', true ),
                        (bool) $settings->get( 'media', 'separate_uploads', true ),
                        (string) $settings->get( 'media', 'filename_separator', '-' ),
                        (string) $settings->get( 'media', 'transliteration_method', 'website' ),
                    );
                }
            ),
            Permalink_Service::class      => \DI\factory(
                static fn( Plugin_Settings $settings ): Permalink_Service => new Permalink_Service(
                    (bool) $settings->get( 'advanced', 'fix_permalinks', false )
                )
            ),
            Script_Manager::class         => \DI\factory(
                static function (
                    string $enabled_scripts,
                    string $default_script,
                    string $request_script,
                    string $cookie_script,
                    string $url_param,
                    ?Resolves_Language $language_resolver,
                    Persists_Script_Cookie $cookie_persister,
                ): Script_Manager {
                    return new Script_Manager(
                        $enabled_scripts,
                        $default_script,
                        $request_script,
                        $cookie_script,
                        $url_param,
                        $language_resolver,
                        $cookie_persister,
                    );
                }
            )
                ->parameter( 'enabled_scripts', \DI\get( 'stl.enabled_scripts' ) )
                ->parameter( 'default_script', \DI\get( 'stl.script.default' ) )
                ->parameter( 'request_script', \DI\get( 'stl.script.req' ) )
                ->parameter( 'cookie_script', \DI\get( 'stl.script.cookie' ) )
                ->parameter( 'url_param', \DI\get( 'stl.script.param' ) )
                ->parameter( 'language_resolver', \DI\get( 'stl.language.resolver' ) )
                ->parameter( 'cookie_persister', \DI\get( Persists_Script_Cookie::class ) ),
            Translit_Service::class       => \DI\autowire(),
            Menu_Integration_Service::class => \DI\factory(
                static function ( Script_Manager $script_manager, Plugin_Settings $settings ): Menu_Integration_Service {
                    return new Menu_Integration_Service(
                        $script_manager,
                        (bool) $settings->get( 'menu', 'extend', true ),
                        (string) $settings->get( 'menu', 'extend_menu', '' ),
                        (string) $settings->get( 'menu', 'selector_type', 'submenu' ),
                        (string) $settings->get( 'menu', 'menu_title', 'Script' ),
                    );
                }
            ),
        );
    }

    /**
     * Initialize the translit script manager.
     *
     * @param Script_Manager $mngr Script manager instance.
     * @return void
     */
    #[Action( tag: 'srbtranslatin_init', priority: 0, invoke: Action::INV_PROXIED, args: 0 )]
    public function initialize_script_manager( Script_Manager $mngr ): void {
        $mngr->initialize();
    }
}

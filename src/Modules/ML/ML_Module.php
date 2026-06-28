<?php
/**
 * ML_Module class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML;

use STL\ML\Services\Multilingual_Language_Resolver;
use STL\ML\Services\Polylang_Language_Resolver;
use STL\ML\Services\QtranslateX_Language_Resolver;
use STL\ML\Services\TranslatePress_Language_Resolver;
use STL\ML\Services\WPML_Language_Resolver;
use XWP\DI\Decorators\Module;

/**
 * Multilingual support module.
 */
#[Module(
    container: 'stl',
    hook: 'srbtranslatin_loaded',
    priority: 20,
    handlers: array(
        Handlers\WPML_Handler::class,
    ),
)]
final class ML_Module {
    /**
     * Get the module configuration.
     *
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            'stl.language.resolver' => \DI\factory(
                static function (): ?Multilingual_Language_Resolver {
                    $resolvers = array();

                    if ( \class_exists( 'SitePress' ) ) {
                        $resolvers[] = new WPML_Language_Resolver();
                    }

                    if ( \function_exists( 'pll_current_language' ) ) {
                        $resolvers[] = new Polylang_Language_Resolver();
                    }

                    if ( \defined( 'TRP_PLUGIN_VERSION' ) || isset( $GLOBALS['TRP_LANGUAGE'] ) ) {
                        $resolvers[] = new TranslatePress_Language_Resolver();
                    }

                    if ( \function_exists( 'qtranxf_getLanguage' ) || isset( $GLOBALS['q_config'] ) ) {
                        $resolvers[] = new QtranslateX_Language_Resolver();
                    }

                    return array() !== $resolvers ? new Multilingual_Language_Resolver( $resolvers ) : null;
                }
            ),
        );
    }
}

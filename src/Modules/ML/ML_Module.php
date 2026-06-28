<?php
/**
 * ML_Module class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML;

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
                static fn() => \class_exists( 'SitePress' ) ? new WPML_Language_Resolver() : null
            ),
        );
    }
}

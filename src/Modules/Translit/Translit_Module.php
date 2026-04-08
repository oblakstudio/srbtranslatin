<?php

namespace STL\Translit;

use STL\Translit\Services\Script_Manager;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

/**
 * Transliterator module definition
 */
#[Module(
    container: 'stl',
    hook: 'srbtranslatin_loaded',
    priority: 30,
    handlers: array(
        Handlers\Title_Handler::class,
    ),
)]
class Translit_Module {
    #[Action( tag: 'wp_loaded', priority: 20, invoke: Action::INV_PROXIED, args: 0 )]
    public function determine_script( Script_Manager $mngr ): void {
    }
}

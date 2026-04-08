<?php

namespace STL\Admin;

use XWP\DI\Decorators\Module;

/**
 * Admin module definition
 */
#[Module( container: 'stl', hook: 'srbtranslatin_loaded', priority: 20, handlers: array() )]
class Admin_Module {
    public static function can_initialize(): bool {
        return \is_admin();
    }
}

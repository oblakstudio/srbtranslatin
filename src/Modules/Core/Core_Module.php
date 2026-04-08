<?php

namespace STL\Core;

use XWP\DI\Decorators\Module;

#[Module( container: 'stl', hook: 'srbtranslatin_loaded', priority: 0, imports: array(), handlers: array() )]
class Core_Module {
}

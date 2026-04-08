<?php

namespace STL\Translit\Handlers;

use XWP\DI\Decorators\Handler;

#[Handler( tag: 'init', priority: 10, context: Handler::CTX_FRONTEND, container: 'stl' )]
class Title_Handler {
}

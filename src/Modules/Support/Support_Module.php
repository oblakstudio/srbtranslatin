<?php
/**
 * Support_Module class file.
 *
 * @package SrbTransLatin
 * @subpackage Support
 */

namespace STL\Support;

use XWP\DI\Decorators\Module;

/**
 * Builder and compatibility support module.
 */
#[Module( container: 'stl', hook: 'srbtranslatin_loaded', priority: 40 )]
final class Support_Module {
}

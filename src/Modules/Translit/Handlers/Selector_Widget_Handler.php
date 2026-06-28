<?php
/**
 * Selector_Widget_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use Oblak\STL\Widget\Selector_Widget;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Handler;

/**
 * Register the legacy selector widget from the active src boot path.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Selector_Widget_Handler {
    /**
     * Constructor.
     *
     * @param callable|null $register_widget Widget registration callback.
     */
    public function __construct( private mixed $register_widget = null ) {
        $this->register_widget ??= static function ( string $widget_class ): void {
            \register_widget( $widget_class );
        };
    }

    /**
     * Register the legacy selector widget.
     *
     * @return void
     */
    #[Action( tag: 'widgets_init', args: 0, invoke: Action::INV_PROXIED )]
    public function register_widget(): void {
        \call_user_func( $this->register_widget, Selector_Widget::class );
    }
}

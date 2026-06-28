<?php
/**
 * WordPress_Script_Cookie_Persister class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use STL\Translit\Contracts\Persists_Script_Cookie;

/**
 * Persist script selection using WordPress cookie semantics.
 */
final class Cookie_Persister implements Persists_Script_Cookie {
    /**
     * Persist the selected script in a cookie.
     *
     * @param string $script Selected script.
     * @return void
     */
    public function persist( string $script ): void {
        if ( \headers_sent() ) {
            return;
        }

        \setcookie( 'stl_script', $script, 0, '/', '', \is_ssl() );
    }
}

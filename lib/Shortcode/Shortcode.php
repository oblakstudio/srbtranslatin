<?php

namespace SGI\STL\Shortcode;

interface Shortcode
{

    public function shortcode_callback($atts, ?string $content);
}
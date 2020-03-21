<?php

namespace SGI\STL\Shortcode;

interface Multicode
{

    public function multicode_callback($atts, ?string $content, ?string $shortcode);
}
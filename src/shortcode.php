<?php

function storeFinder()
{
    ob_start();
    include dirname(__FILE__) . '/../view/map.php';
    return ob_get_clean();
}

add_shortcode('wp-store-finder', 'storeFinder');

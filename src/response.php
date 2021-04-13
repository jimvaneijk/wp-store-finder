<?php
function store_finder_response()
{
    $posts = get_posts([
        'post_type' => 'store',
        'post_status' => 'publish',
        'order' => 'ASC',
        'numberposts' => -1,
    ]);

    $stores = array();
    foreach ($posts as $store) {
        $stores[] = array(
            'name' => $store->post_title,
            'address' => esc_attr(get_post_meta($store->ID, 'sf_address', true)),
            'zipcode' => esc_attr(get_post_meta($store->ID, 'sf_postal_code', true)),
            'city' => esc_attr(get_post_meta($store->ID, 'sf_city', true)),
            'position' => array(
                'lat' => esc_attr(get_post_meta($store->ID, 'latitude', true)),
                'lng' => esc_attr(get_post_meta($store->ID, 'longitude', true)),
            ),
        );
    }

    return $stores;
}

function import_stores()
{
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $json = "{$actual_link}/json/stores.json";
    $response = file_get_contents($json, false, stream_context_create($arrContextOptions));
    $stores = json_decode($response, true);

    $addedStrore = 0;
    foreach ($stores as $store) {
        if(get_page_by_title($store['name'].' ('. $store['city'] . ')', OBJECT, 'store') === NULL) {
            $new_post = array(
                'post_title' => $store['name'].' ('. $store['city'] . ')',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'store'
            );
            $post_id = wp_insert_post($new_post);
            add_post_meta($post_id, 'sf_address', "{$store['street']} {$store['number']}");
            add_post_meta($post_id, 'sf_postal_code', $store['zipcode']);
            add_post_meta($post_id, 'sf_city', $store['city']);
            add_post_meta($post_id, 'sf_country', 'Nederland');
            add_post_meta($post_id, 'latitude', $store['position']['lat']);
            add_post_meta($post_id, 'longitude', $store['position']['lng']);

            $addedStrore++;
        } else {
            continue;
        }
    }

    echo 'Done added '. $addedStrore . ' stores van de '. count($stores);
}
add_action('rest_api_init', function () {
    register_rest_route('wp-store-finder/v1', 'get', array(
        'methods'  => 'GET',
        'callback' => 'store_finder_response'
    ));
});

if (env('ACCEPT_IMPORT', false)) {
    add_action('rest_api_init', function () {
        register_rest_route('wp-store-finder/v1', 'import', array(
            'methods'  => 'GET',
            'callback' => 'import_stores'
        ));
    });
}



<?php

/**
 * Create Store post type
 *
 */
function create_stores()
{
    $labels = array(
        'name'                => _x('Verkooppunten', 'Post Type General Name', 'wp-store-finder'),
        'singular_name'       => _x('Verkooppunt', 'Post Type Singular Name', 'wp-store-finder'),
        'menu_name'           => __('Verkooppunten', 'wp-store-finder'),
        'parent_item_colon'   => __('Parent Store', 'wp-store-finder'),
        'all_items'           => __('Alle verkooppunten', 'wp-store-finder'),
        'view_item'           => __('Bekijk verkooppunt', 'wp-store-finder'),
        'add_new_item'        => __('Voeg nieuw verkooppunt toe', 'wp-store-finder'),
        'add_new'             => __('Nieuw verkooppunt', 'wp-store-finder'),
        'edit_item'           => __('Bewerk verkooppunt', 'wp-store-finder'),
        'update_item'         => __('Update verkooppunt', 'wp-store-finder'),
        'search_items'        => __('Zoek verkooppunt', 'wp-store-finder'),
        'not_found'           => __('Verkooppunt niet gevonden', 'wp-store-finder'),
        'not_found_in_trash'  => __('Verkooppunt niet gevonden in verwijderde items', 'wp-store-finder'),
    );

    $args = array(
        'label'               => __('verkooppunten', 'wp-store-finder'),
        'description'         => __('Verkooppunten voor de StoreFinder', 'wp-store-finder'),
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'capability_type'     => 'post',
        'menu_icon'           => 'dashicons-store',
    );
    register_post_type(
        'store',
        $args
    );
}
add_action('init', 'create_stores');


/**
 * Add meta box to post typ Store.
 */
function sf_register_meta_boxes()
{
    $screen = get_current_screen();
    add_meta_box('sf-1', __('Address', 'sf'), 'sf_display_callback', 'store');
    if ($screen->action !== 'add') {
        add_meta_box('sf-2', __('Coördinaten voor de google map', 'sf'), 'sf_display_coordinates_callback', 'store');
    }
}
add_action('add_meta_boxes', 'sf_register_meta_boxes');



/**
 * Load meta box form.
 *
 * @param int $store Store ID
 */
function sf_display_callback($store)
{
    include dirname(__FILE__). '/../view/form.php';
}

/**
 * Load meta box form.
 *
 * @param int $store Store ID
 */
function sf_display_coordinates_callback($store)
{
    include dirname(__FILE__). '/../view/coordinates.php';
}

/**
 * Save meta box content.
 *
 * @param int $store_id Store ID
 */
function sf_save_meta_box($store_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if ($parent_id = wp_is_post_revision($store_id)) {
        $store_id = $parent_id;
    }
    $fields = [
        'sf_address',
        'sf_postal_code',
        'sf_city',
        'sf_country',
    ];
    foreach ($fields as $field) {
        if (array_key_exists($field, $_POST)) {
            update_post_meta($store_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    $latlng = '';

    if (isset($_POST['sf_address'])) {
        $latlng = geocode($_POST['sf_address'] . ', '. $_POST['sf_postal_code']);
    }

    // Add LatLong to Store
    if ($latlng) {
        foreach ($latlng as $name => $value) {
            update_post_meta($store_id, $name, sanitize_text_field($value));
        }
    }

}

add_action('save_post', 'sf_save_meta_box');

function geocode($address)
{
    $address = urlencode($address);
    $apiKey = env('WP_STORE_FINDER_GOOGLE_API');
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";
    $json = file_get_contents($url);
    $response = json_decode($json, true);

    if ($response['status'] !== 'OK') {
        add_action( 'admin_notices', 'sf_error_message');
    }

    $lat = $response['results'][0]['geometry']['location']['lat'] ?? '';
    $long = $response['results'][0]['geometry']['location']['lng'] ?? '';

    if (!$lat || !$long) {
        return new WP_Error( 'broke', __('No Long or Latitude found', 'wp-store-finder'));
    }

    return [
        'latitude' => $lat,
        'longitude' => $long,
    ];
}

function sf_error_message($message) {
    ?>
    <div class="notice error my-acf-notice" >
        <p><?php _e('Google API went wrong', 'my-text-domain' ); ?></p>
    </div>
    <?php
}

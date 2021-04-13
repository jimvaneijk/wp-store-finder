<div id="map" class="stores-map"></div>
<?php $stores = get_posts([
    'post_type' => 'store',
    'post_status' => 'publish',
    'order' => 'ASC',
    'numberposts' => -1,
]); ?>
<div class="stores">
    <?php foreach ($stores as $store) : ?>
        <div class="store-column">
            <div class="store">
                <h2 class="store-title"><?php echo $store->post_title ?></h2>
                <address class="store-address">
                    <?php echo esc_attr(get_post_meta($store->ID, 'sf_address', true)); ?><br>
                    <?php echo esc_attr(get_post_meta($store->ID, 'sf_postal_code', true)); ?>,
                    <?php echo esc_attr(get_post_meta($store->ID, 'sf_city', true)); ?><br>
                    <?php echo esc_attr(get_post_meta($store->ID, 'sf_country', true)); ?>
                </address>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    let map;
    let markers = [];
    let bounds;
    let stores = [
        <?php foreach ($stores as $store) : ?>
            <?php if (get_post_meta($store->ID, 'latitude', true) && get_post_meta($store->ID, 'longitude', true)) : ?>
                <?php $address = esc_attr(get_post_meta($store->ID, 'sf_address', true)). ', '. esc_attr(get_post_meta($store->ID, 'sf_postal_code', true)). ', '. esc_attr(get_post_meta($store->ID, 'sf_city', true));?>
                {
                    title: "<?php echo $store->post_title ?>",
                    latLng: {
                        lat: <?php echo esc_attr(get_post_meta($store->ID, 'latitude', true));?>,
                        lng: <?php echo esc_attr(get_post_meta($store->ID, 'longitude', true));?>,
                    },
                    address: "<?php echo $address; ?>",
                },
            <?php endif;?>
        <?php endforeach; ?>
    ];
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: new google.maps.LatLng(51.9178558,4.4734676),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        bounds = new google.maps.LatLngBounds();
        stores.forEach((store) => {
            addMarker(
                store,
                map,
            );
        });
        map.fitBounds(bounds);

    }

    function addMarker(store, map) {
        const infowindow = new google.maps.InfoWindow({
            content: `<h2 class="store-title-map">${store.title}</h2><address class="store-address">${store.address}</address>`,
        });
        const marker = new google.maps.Marker({
            map: map,
            icon: '<?php echo WP_CONTENT_URL . '/plugins/wp-store-finder/images/marker.png'?>',
            title: store.title,
            position: store.latLng
        });
        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
        bounds.extend(marker.getPosition());
    }
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo getenv('WP_STORE_FINDER_GOOGLE_API'); ?>&callback=initMap"></script>

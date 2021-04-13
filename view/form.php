<div class="sf_box">
    <style>
        .sf_field label{
            display: block;
        }
        .sf_field input{
            display: block;
            width: 100%;
        }
    </style>
    <p class="meta-options sf_field">
        <label for="sf_address"><?php echo __('Adres', 'wordpress');?></label>
        <input
            id="sf_address"
            type="text"
            name="sf_address"
            value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'sf_address', true));?>"
        >
    </p>
    <p class="meta-options sf_field">
        <label for="sf_postal_code"><?php echo __('Postcode', 'wordpress');?></label>
        <input
            id="sf_postal_code"
            type="text"
            name="sf_postal_code"
            value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'sf_postal_code', true));?>"
        >
    </p>
    <p class="meta-options sf_field">
        <label for="sf_city"><?php echo __('Stad', 'wordpress');?></label>
        <input
            id="sf_city"
            type="text"
            name="sf_city"
            value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'sf_city', true));?>"
        >
    </p>
    <p class="meta-options sf_field">
        <label for="sf_city"><?php echo __('Land', 'wordpress');?></label>
        <input
            id="sf_country"
            type="text"
            name="sf_country"
            value="<?php echo get_post_meta(get_the_ID(), 'sf_country', true) ? esc_attr(get_post_meta(get_the_ID(), 'sf_country', true)) : 'Nederland';?>"
        >
    </p>
</div>

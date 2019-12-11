<?php

defined( 'ABSPATH' ) || exit;

/**
*
* WooCommerce Product Add-Ons compatibilty
* Adds an option to multiply addon cost by booking duration
*
**/
add_action( 'woocommerce_product_addons_panel_before_options', 'wceb_product_addons_options', 10, 3 );

function wceb_product_addons_options( $post, $addon, $loop ) {

    $multiply_addon = isset( $addon['multiply_by_booking_duration'] ) ? $addon['multiply_by_booking_duration'] : 0;

    ?>

	<div class="wc-pao-addons-secondary-settings">
        <div class="wc-pao-row wc-pao-addon-multiply-setting">
            <label for="wc-pao-addon-multiply-<?php echo esc_attr( $loop ); ?>">
                <input type="checkbox" id="wc-pao-addon-multiply-<?php echo esc_attr( $loop ); ?>" name="product_addon_multiply[<?php echo esc_attr( $loop ); ?>]" <?php checked( $multiply_addon, 1 ); ?> />
                    <?php esc_html_e( 'Multiply addon cost by booking duration (bookable products only)?', 'woocommerce-easy-booking-system' ); ?>
            </label>
        </div>
    </div>

    <?php

}

/**
*
* WooCommerce Product Add-Ons compatibilty
* Saves option to multiply addon cost by booking duration
*
**/
add_filter( 'woocommerce_product_addons_save_data', 'wceb_product_addons_save_data', 10, 2 );

function wceb_product_addons_save_data( $data, $i ) {
    $multiply_addon = isset( $_POST['product_addon_multiply'] ) ? $_POST['product_addon_multiply'] : array();

    $data['multiply_by_booking_duration'] = isset( $multiply_addon[$i] ) ? 1 : 0;

    // Also have multiply option in each addon option to display "/ day" price.
    foreach ( $data['options'] as $i => $option ) {
        $data['options'][$i]['multiply'] = $data['multiply_by_booking_duration'];
    }

    return $data;
}

/**
*
* WooCommerce Product Add-Ons compatibilty
* Displays a custom price if the addon cost is multiplied by booking duration
*
* @param str $price - Product price
* @param array - $addon
* @param int - $key
* @param str - $type
* @return str $price - Custom or base price
*
**/
add_filter( 'woocommerce_product_addons_price', 'wceb_product_addons_price', 10, 4 );

function wceb_product_addons_price( $price, $addon, $key, $type ) {
    global $product;

    // Small verification because WC Product Addons is very well coded (...) and the same filter is used to display price in input label and in html data attribute.
    if ( is_float( $price ) ) {
        return $price;
    }

    if ( wceb_is_bookable( $product ) ) {

        $adjust_price = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';

        if ( $adjust_price != '1' ) {
            return $price;
        }

        $maybe_multiply = isset( $addon['multiply_by_booking_duration'] ) ? $addon['multiply_by_booking_duration'] : 0;

        if ( $maybe_multiply ) {
            
            $addon_price  = ! empty( $addon['price'] ) ? $addon['price'] : '';
            $price_prefix = 0 < $addon_price ? '+' : '';
            $price_raw    = apply_filters( 'woocommerce_product_addons_price_raw', $addon_price, $addon );

            if ( ! $price_raw ) {
                return $price;
            }

            $price_type = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';

            if ( 'percentage_based' === $price_type ) {
                $content = $price_prefix . $price_raw . '%';
            } else {
                $content = $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) );
            }

            $price_text = wceb_get_price_html( $product );

            $wceb_addon_price = apply_filters(
                'easy_booking_display_price',
                $content . '<span class="wceb-price-format">' . $price_text . '</span>',
                $product,
                $content
            );

            $price = '(' . $wceb_addon_price . ')';

        }

    }

    return $price;

}

/**
*
* WooCommerce Product Add-Ons compatibilty
* Displays a custom price if the addon option cost is multiplied by booking duration
* This is for addons with options (multiple choice or checkbox)
*
* @param str $price - Product price
* @param array - $option
* @param int - $key
* @param str - $type
* @return str $price - Custom or base price
*
**/
add_filter( 'woocommerce_product_addons_option_price', 'wceb_product_addons_option_price', 10, 4 );

function wceb_product_addons_option_price( $price, $option, $key, $type ) {
    global $product;

    // Small verification because WC Product Addons is very well coded (...) and the same filter is used to display price in input label and in html data attribute.
    if ( is_float( $price ) ) {
        return $price;
    }

    if ( wceb_is_bookable( $product ) ) {

        $maybe_multiply = isset( $option['multiply'] ) ? $option['multiply'] : 0;

        if ( $maybe_multiply ) {
            
            $option_price = ! empty( $option['price'] ) ? $option['price'] : '';
            $price_prefix = 0 < $option_price ? '+' : '';
            $price_raw    = apply_filters( 'woocommerce_product_addons_option_price_raw', $option_price, $option );

            if ( ! $price_raw ) {
                return $price;
            }

            $price_type = ! empty( $option['price_type'] ) ? $option['price_type'] : '';

            if ( 'percentage_based' === $price_type ) {
                $content = $price_prefix . $price_raw . '%';
            } else {
                $content = $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) );
            }

            $price_text = wceb_get_price_html( $product );

            $wceb_addon_price = apply_filters(
                'easy_booking_display_price',
                $content . '<span class="wceb-price-format">' . $price_text . '</span>',
                $product,
                $content
            );

            $price = '(' . $wceb_addon_price . ')';

        }

    }

    return $price;

}
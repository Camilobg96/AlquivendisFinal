<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Cart' ) ) :

class WCEB_Cart {

    public function __construct() {
        
        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'wceb_check_dates_before_add_to_cart' ), 20, 5 );
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'wceb_add_cart_item_data' ), 12, 3 );
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'wceb_get_cart_item_from_session' ), 98, 2 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'wceb_get_item_data' ), 10, 2 );
        add_filter( 'woocommerce_add_cart_item', array( $this, 'wceb_add_cart_item_booking_price' ), 10, 1 );

        add_filter( 'woocommerce_product_addon_cart_item_data', array( $this, 'wceb_product_addon_cart_item_data' ), 10, 4 );

        // WooCommerce Product Add-Ons 3.0.0
        if ( true === wceb_pao_version_3() ) {
            add_filter( 'woocommerce_product_addons_add_cart_item', array( $this, 'wceb_adjust_addon_price' ), 10, 2 );
        }
        
        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            add_filter( 'woocommerce_get_price', array( $this, 'wceb_set_cart_item_price' ), 20, 2 );
        } else {
            add_filter( 'woocommerce_product_get_price', array( $this, 'wceb_set_cart_item_price' ), 20, 2 );
            add_filter( 'woocommerce_product_variation_get_price', array( $this, 'wceb_set_cart_item_price' ), 20, 2 );
        }
        
    }

    /**
    *
    * Checks if two dates are set before adding to cart
    *
    * @param bool $passed
    * @param int $product_id
    * @param int $quantity
    * @param int | optional $variation_id
    * @return bool $passed
    *
    **/
    public function wceb_check_dates_before_add_to_cart( $passed = true, $product_id, $quantity, $variation_id = '', $cart_item_data = array() ) {
        
        $id = empty( $variation_id ) ? $product_id : $variation_id;

        if ( ! $passed ) {
            return $passed;
        }

        $product = wc_get_product( $id );

        if ( ! $product ) {
            return false;
        }
        
        // If product is bookable
        if ( wceb_is_bookable( $product ) ) {

            $booking_session = WC()->session->get( 'booking' );
            $dates_format = wceb_get_product_booking_dates( $product );

            if ( isset( $booking_session[$id] ) && ! empty( $booking_session[$id] ) ) {

                // If product is grouped or bundled, get the "parent" product data
                if ( isset( $booking_session[$id]['grouped_by'] ) ) {
                    $_product = $booking_session[$id]['grouped_by'];
                    $dates_format = wceb_get_product_booking_dates( $_product );
                }

                if ( $dates_format === 'one' ) {

                    if ( ! isset( $booking_session[$id]['start'] ) ) {
                        wc_add_notice( esc_html__( 'Please choose a date', 'woocommerce-easy-booking-system' ), 'error' );
                        $passed = false;
                    }

                    if ( isset( $booking_session[$id]['end'] ) ) {
                        wc_add_notice( esc_html__( 'You can only select one date', 'woocommerce-easy-booking-system' ), 'error' );
                        $passed = false;
                    }

                } else if ( $dates_format === 'two' ) {
            
                    if ( ! isset( $booking_session[$id]['start'] ) || ! isset( $booking_session[$id]['end'] ) ) {
                        wc_add_notice( esc_html__( 'Please choose two dates', 'woocommerce-easy-booking-system' ), 'error' );
                        $passed = false;
                    }

                }

            } else {

                if ( $dates_format === 'one' ) {
                    wc_add_notice( esc_html__( 'Please choose a date', 'woocommerce-easy-booking-system' ), 'error' );
                } else if ( $dates_format === 'two' ) {
                    wc_add_notice( esc_html__( 'Please choose two dates', 'woocommerce-easy-booking-system' ), 'error' );
                }
                
                $passed = false;

            }

        }

        return $passed;
    }

    /**
    *
    * Adds session data to cart item
    *
    * @param array $cart_item_meta
    * @param int $product_id
    * @return array $cart_item_meta
    *
    **/
    function wceb_add_cart_item_data( $cart_item_meta, $product_id, $variation_id ) {
        // Get session
        $booking_session = WC()->session->get( 'booking' );
        $id              = empty( $variation_id ) ? $product_id : $variation_id;

        if ( isset( $booking_session[$id] ) && ! empty( $booking_session[$id] ) ) {

            if ( isset( $booking_session[$id]['new_price'] ) ) {
                $cart_item_meta['_booking_price'] = wc_format_decimal( $booking_session[$id]['new_price'] );
            }

            if ( isset( $booking_session[$id]['duration'] ) ) {
                $cart_item_meta['_booking_duration'] = absint( $booking_session[$id]['duration'] );
            }

            // Start date yyyy-mm-dd
            if ( isset( $booking_session[$id]['start'] ) ) {
                $cart_item_meta['_ebs_start'] = sanitize_text_field( $booking_session[$id]['start'] );
            }

            // End date yyyy-mm-dd
            if ( isset( $booking_session[$id]['end'] ) ) {
                $cart_item_meta['_ebs_end'] = sanitize_text_field( $booking_session[$id]['end'] );
            }

            // Reset session for this product ID
            unset( $booking_session[$id] );
            WC()->session->set( 'booking', $booking_session );

            // Maybe multiply addon costs by booking duration (PAO < 3.0.0)
            if ( ! wceb_pao_version_3() ) {

                if ( isset( $cart_item_meta['addons'] ) && ! empty( $cart_item_meta['addons'] ) ) {

                    foreach ( $cart_item_meta['addons'] as $i => $addon ) {

                        if ( $addon['multiply'] === 1 ) {
                            $addon_price = $addon['price'] * $cart_item_meta['_booking_duration'];
                            $cart_item_meta['addons'][$i]['price'] = strval( $addon_price );
                        }

                    }

                }

            }

        }

        return $cart_item_meta;
    }

    /**
    *
    * Adds data to cart item
    *
    * @param array $cart_item
    * @param array $values - cart_item_meta
    * @return array $cart_item
    *
    **/
    function wceb_get_cart_item_from_session( $cart_item, $values ) {

        if ( isset( $values['_booking_price'] ) ) {
            $cart_item['_booking_price'] = $values['_booking_price'];
        }

        if ( isset( $values['_booking_duration'] ) ) {
            $cart_item['_booking_duration'] = $values['_booking_duration'];
        }

        // Start date yyyy-mm-dd
        if ( isset( $values['_ebs_start'] ) ) {
            $cart_item['_ebs_start'] = $values['_ebs_start'];
        }

        // End date yyyy-mm-dd
        if ( isset( $values['_ebs_end'] ) ) {
            $cart_item['_ebs_end'] = $values['_ebs_end'];
        }

        $this->wceb_add_cart_item_booking_price( $cart_item );
        
        return $cart_item;
    }

    /**
    *
    * Override any filters on the price with the booking price once the item is in the cart
    *
    * @param str $price
    * @param WC_Product $_product
    * @return str $price
    *
    **/
    function wceb_set_cart_item_price( $price, $_product ) {
        
        if ( isset( $_product->new_booking_price ) && ! empty( $_product->new_booking_price ) ) {
            $price = $_product->new_booking_price;
        }

        return $price;

    }

    /**
    *
    * Sets custom price to the cart item
    *
    * @param array $cart_item
    * @return array $cart_item
    *
    **/
    function wceb_add_cart_item_booking_price( $cart_item ) {

        if ( isset( $cart_item['_booking_price'] ) && $cart_item['_booking_price'] >= 0 ) {

            $booking_price = apply_filters(
                'easy_booking_set_booking_price',
                $cart_item['_booking_price'],
                $cart_item
            );

            // If bundled
            if ( isset( $cart_item['bundled_by'] ) ) {
                
                // Get parent bundle product
                $bundle = WC()->cart->get_cart_item( $cart_item['bundled_by'] );

                if ( method_exists( $bundle['data'], 'get_bundled_item' ) ) {

                    // Get bundle item
                    $bundle_item = $bundle['data']->get_bundled_item( $cart_item['bundled_item_id'] );

                    // If is not priced individually, remove booking price
                    if ( method_exists( $bundle_item, 'is_priced_individually' ) ) {

                        if ( ! $bundle_item->is_priced_individually() ) {
                            $cart_item['data']->new_booking_price = '';
                            return $cart_item;
                        }

                    }

                }

            }

            $cart_item['data']->set_price( (float) $booking_price );
            $cart_item['data']->new_booking_price = (float) $booking_price;

        }
    
        return $cart_item;
    }
 
    /**
    *
    * Adds formatted dates to the cart item
    *
    * @param array $other_data
    * @param array $cart_item
    * @return array $other_data
    *
    **/
    function wceb_get_item_data( $other_data, $cart_item ) {

        // If is bundled, return
        if ( isset( $cart_item['bundled_by'] ) ) {
            return $other_data;
        }

        $product_id   = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];

        $id = empty( $variation_id ) ? $product_id : $variation_id;

        $product = wc_get_product( $id );

        $start_text = esc_html( apply_filters( 'easy_booking_start_text', __( 'Start', 'woocommerce-easy-booking-system' ), $product ) );
        $end_text   = esc_html( apply_filters( 'easy_booking_end_text', __( 'End', 'woocommerce-easy-booking-system' ), $product ) );

        if ( isset( $cart_item['_ebs_start'] ) && ! empty ( $cart_item['_ebs_start'] ) ) {

            $other_data[] = array(
                'name'  => $start_text,
                'value' => date_i18n( get_option( 'date_format' ), strtotime( $cart_item['_ebs_start'] ) )
            );

        }

        if ( isset( $cart_item['_ebs_end'] ) && ! empty ( $cart_item['_ebs_end'] ) ) {

            $other_data[] = array(
                'name'  => $end_text,
                'value' => date_i18n( get_option( 'date_format' ), strtotime( $cart_item['_ebs_end'] ) )
            );

        }

        return $other_data;
    }

    /**
    *
    * WooCommerce Product Add-Ons compatibilty
    * Store multiply by booking duration in each addon when adding a product to cart
    *
    * @param array - $data
    * @param array - $addon
    * @param int - $product_id
    * @param array - $post_data
    * @return array - $data
    *
    **/
    public function wceb_product_addon_cart_item_data( $data, $addon, $product_id, $post_data ) {

        $maybe_multiply = isset( $addon['multiply_by_booking_duration'] ) ? $addon['multiply_by_booking_duration'] : 0;

        foreach ( $data as $i => $addon_data ) {
            $data[$i]['multiply'] = intval( $maybe_multiply );
        }

        return $data;
    }

    /**
    *
    * WooCommerce Product Add-Ons 3.0.0 compatibilty
    * Adjust product add-on price in cart
    *
    * @param array - $data
    * @param str - $addon_cart_item_key
    * @return array - $data
    *
    **/
    function wceb_adjust_addon_price( $data, $addon_cart_item_key ) {

        $product_price = $data['_booking_price'];
        $duration      = $data['_booking_duration'];

        $addon_name = $data['addon_label'];
        $addons     = $data['addons'];

        foreach ( $addons as $index => $addon ) {

            if ( $addon['value'] === $addon_name ) {

                $addon_booking_price = $addon['price'];

                // Multiply addon cost by booking duration?
                $maybe_multiply = isset( $addon['multiply'] ) ? absint( $addon['multiply'] ) : 0;

                // Backward compatibility - Pass true to filter to multiply additional costs by booking duration (default: false)
                if ( ! $maybe_multiply && true === apply_filters( 'easy_booking_multiply_additional_costs', false ) ) {
                    $maybe_multiply = 1;
                }

                if ( $addon['price_type'] === 'percentage_based' ) {
                    $addon_booking_price = ( ( ( $product_price / $duration ) * $addon_booking_price ) / 100 );
                }

                if ( $maybe_multiply ) {
                    $addon_booking_price *= $duration;
                }

                $data['price'] = floatval( $addon_booking_price );

                break;
            }

        }

        // Remove unnecessary data
        unset( $data['_booking_price'] );
        unset( $data['_booking_duration'] );
        unset( $data['_ebs_start'] );
        unset( $data['_ebs_end'] );

        return $data;

    }

}

new WCEB_Cart();

endif;
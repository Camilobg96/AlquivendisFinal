<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Order' ) ) :

class Order {

    public function __construct() {

        add_action( 'woocommerce_before_order_itemmeta', array( $this, 'bookable_products_order_itemmeta' ), 10, 3 );

    }
    /**
    *
    * Displays booked dates and a picker form on the order page
    *
    * @param int - $item_id
    * @param WC_Order_Item - $item
    * @param WC_Product || WC_Product_Variation - $product
    *
    **/
    public function bookable_products_order_itemmeta( $item_id, $item, $product ) {
        global $wpdb;

        if ( ! $product || is_null( $product ) ) {
            return;
        }

        $start_date = wc_get_order_item_meta( $item_id, '_ebs_start_format' );
        $end_date   = wc_get_order_item_meta( $item_id, '_ebs_end_format' );
        $booking_status = wc_get_order_item_meta( $item_id, '_booking_status' );

        $start_date_text = apply_filters( 'easy_booking_start_text', __( 'Start', 'woocommerce-easy-booking-system' ), $product );
        $end_date_text   = apply_filters( 'easy_booking_end_text', __( 'End', 'woocommerce-easy-booking-system' ), $product );

        $item_order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        
        if ( ! empty( $start_date ) ) {

            // Get meta ids from the database
            $start_date_meta_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT `meta_id` FROM $item_order_meta_table WHERE `order_item_id` = %d AND `meta_key` LIKE %s",
                $item_id, '_ebs_start_format'
            ));

            if ( ! empty( $end_date ) ) {

                $end_date_meta_id = $wpdb->get_var( $wpdb->prepare(
                    "SELECT `meta_id` FROM $item_order_meta_table WHERE `order_item_id` = %d AND `meta_key` LIKE %s",
                    $item_id, '_ebs_end_format'
                ));

            }

            if ( ! empty( $booking_status ) ) {
                
                $booking_status_meta_id = $wpdb->get_var( $wpdb->prepare(
                    "SELECT `meta_id` FROM $item_order_meta_table WHERE `order_item_id` = %d AND `meta_key` LIKE %s",
                    $item_id, '_booking_status'
                ));
                
            }

            // Formatted start date
            $start_date_i18n = date_i18n( get_option( 'date_format' ), strtotime( $start_date ) );

            // Formatted end date
            if ( ! empty( $end_date ) ) {
                $end_date_i18n = date_i18n( get_option( 'date_format' ), strtotime( $end_date ) );
            }

            include( 'views/order-items/html-wceb-order-item-meta.php' );

            include( 'views/order-items/html-wceb-edit-order-item-meta.php' );

        } else if ( wceb_is_bookable( $product ) ) {

            $meta_array = array(
                'start_date_meta_id'     => '_ebs_start_format',
                'end_date_meta_id'       => '_ebs_end_format',
                'booking_status_meta_id' => '_booking_status'
            );

            // If meta key is not already in database, create it
            foreach ( $meta_array as $var => $meta_name ) {

                // Check if there's already an entry in the database.
                ${$var} = $wpdb->get_var( $wpdb->prepare(
                    "SELECT `meta_id` FROM $item_order_meta_table WHERE `order_item_id` = %d AND `meta_key` LIKE %s",
                    $item_id, $meta_name
                ));

                // Otherwise create order item meta.
                if ( is_null( ${$var} ) ) {
                    ${$var} = wc_add_order_item_meta( $item_id, $meta_name, '' );
                }

            }

            include( 'views/order-items/html-wceb-add-order-item-meta.php' );

        }

    }

}

return new Order();

endif;
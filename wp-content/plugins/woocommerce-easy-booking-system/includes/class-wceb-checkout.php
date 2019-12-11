<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Checkout' ) ) :

class WCEB_Checkout {

    public function __construct() {

        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            add_action( 'woocommerce_add_order_item_meta', array( $this, 'wceb_add_order_meta' ), 10, 2 );
            add_filter( 'woocommerce_order_items_meta_display', array( $this, 'wceb_display_booking_item_meta' ), 10, 2 );
        } else {
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'wceb_order_line_item' ), 10, 3 );
            add_filter( 'woocommerce_display_item_meta', array( $this, 'wceb_display_item_meta' ), 10, 3 );
        }
        
        add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'wceb_hide_formatted_date' ), 10, 1 );
        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'wceb_set_order_booking_status' ), 10, 2 );

    }

    /**
    *
    * Deprecated in WooCommerce 3.0
    * Adds booked dates to the order item
    *
    * @param int $item_id
    * @param array $values - 
    *
    **/
    public function wceb_add_order_meta( $item_id, $values ) {

        if ( ! empty( $values['_ebs_start'] ) ) {
            // Start date yyyy-mm-dd
            wc_add_order_item_meta( $item_id, '_ebs_start_format', sanitize_text_field( $values['_ebs_start'] ) );
            wc_add_order_item_meta( $item_id, '_booking_status', 'wceb-pending' );
        }

        if ( ! empty( $values['_ebs_end'] ) ) {
            // Start end yyyy-mm-dd
            wc_add_order_item_meta( $item_id, '_ebs_end_format', sanitize_text_field( $values['_ebs_end'] ) );
        }

    }

    /**
    *
    * Since WooCommerce 3.0
    * Adds booked dates to the order item
    *
    * @param int $item_id
    * @param array $values - 
    *
    **/
    public function wceb_order_line_item( $item, $cart_item_key, $values ) {

        if ( ! empty( $values['_ebs_start'] ) ) {

            // Start date yyyy-mm-dd
            $start = sanitize_text_field( $values['_ebs_start'] );

            // Store start date
            $item->add_meta_data( '_ebs_start_format', $start );

            // End date yyyy-mm-dd
            $end = ! empty( $values['_ebs_end'] ) ? sanitize_text_field( $values['_ebs_end'] ) : false;

            // Maybe store end date
            if ( $end ) {
                $item->add_meta_data( '_ebs_end_format', $end );
            }

            // Store booking status
            $this->wceb_set_item_booking_status( $item, $start, $end );
            
            do_action( 'easy_booking_add_booked_item', $item, $start, $end );

        }

    }

    /**
    *
    * Set item booking status when ordering a product
    *
    * @param WC_Order_Item $item
    * @param str - $start
    * @param str - optional - $end
    *
    **/
    private function wceb_set_item_booking_status( $item, $start, $end = false ) {
        
        $change_start     = get_option( 'wceb_set_start_booking_status' );
        $change_start_day = get_option( 'wceb_keep_start_status_for' );

        $current_date = strtotime( date( 'Y-m-d' ) );
        $start_time   = strtotime( $start );

        $item_status = '';

        if ( $end ) {

            $end_time = strtotime( $end );

            // Start date in the future = Pending status
            if ( $current_date < $start_time ) {
                $item_status = 'wceb-pending';
            }
            
            // Set Start status x days before start date (defined in the plugin settings)
            $change_start_status = strtotime( $start . ' -' . $change_start_day . ' days' );
            if ( $current_date === $change_start_status ) {
                $item_status = 'wceb-start';
            }

            // Set Processing status between start and end dates
            if ( $current_date > $start_time && $current_date < $end_time ) {
                $item_status = 'wceb-processing';
            }

        } else {

            // Start date in the future = Pending status
            if ( $current_date < $start_time ) {
                $item_status = 'wceb-pending';
            }

            // Set Start status x days before start date (defined in the plugin settings)
            $change_start_status = strtotime( $start . ' -' . $change_start_day . ' days' );
            if ( $current_date === $change_start_status ) {
                $item_status = 'wceb-start';
            }

            if ( $current_date === $start_time ) {
                $item_status = 'wceb-processing';
            }

        }

        if ( isset( $item_status ) && ! empty( $item_status ) ) {
            $item->add_meta_data( '_booking_status', sanitize_text_field( $item_status ) );
        }

    }

    /**
    *
    * Deprecated in WooCommerce 2.7
    * Display order item booking meta (start and (maybe) end date(s)), even if hidden
    *
    * @param str $output
    * @param WC_Order_Item $order_item
    * @param str $output
    *
    **/
    public function wceb_display_booking_item_meta( $output, $order_item ) {

        $order_item_meta = $order_item->meta;
        $product = $order_item->product;

        $start_text = esc_html( apply_filters( 'easy_booking_start_text', __( 'Start', 'woocommerce-easy-booking-system' ), $product ) );
        $end_text   = esc_html( apply_filters( 'easy_booking_end_text', __( 'End', 'woocommerce-easy-booking-system' ), $product ) );

        if ( isset( $order_item_meta['_ebs_start_format'] ) ) {

            foreach ( $order_item_meta['_ebs_start_format'] as $index => $meta ) {
                $formatted_meta = date_i18n( get_option( 'date_format' ), strtotime( $meta ) );
                $output .= '<dl class="variation">' . wp_kses_post( $start_text . ': ' . $formatted_meta ) . '</dl>';
            }

        }

        if ( isset( $order_item_meta['_ebs_end_format'] ) ) {

            foreach ( $order_item_meta['_ebs_end_format'] as $index => $meta ) {
                $formatted_meta = date_i18n( get_option( 'date_format' ), strtotime( $meta ) );
                $output .= '<dl class="variation">' . wp_kses_post( $end_text . ': ' . $formatted_meta ) . '</dl>';
            }

        }
        
        return $output;

    }

    /**
    *
    * Since WooCommerce 3.0
    * Display order item booking meta (start and (maybe) end date(s)), even if hidden
    *
    * @param str $output
    * @param WC_Order_Item $order_item
    * @param str $output
    *
    **/
    function wceb_display_item_meta( $html, $item, $args ) {

        $product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : $item->product;

        $start_text = esc_html( apply_filters( 'easy_booking_start_text', __( 'Start', 'woocommerce-easy-booking-system' ), $product ) );
        $end_text   = esc_html( apply_filters( 'easy_booking_end_text', __( 'End', 'woocommerce-easy-booking-system' ), $product ) );

        $start = $item->get_meta( '_ebs_start_format' );
        if ( isset( $start ) && ! empty( $start ) ) {
            $formatted_start = date_i18n( get_option( 'date_format' ), strtotime( $start ) );
            $html .= '<dl class="variation">' . wp_kses_post( $start_text . ': ' . $formatted_start ) . '</dl>';
        }

        $end = $item->get_meta( '_ebs_end_format' );
        if ( isset( $end ) && ! empty( $end ) ) {
            $formatted_end = date_i18n( get_option( 'date_format' ), strtotime( $end ) );
            $html .= '<dl class="variation">' . wp_kses_post( $end_text . ': ' . $formatted_end ) . '</dl>';
        }

        return $html;
    }
    
    /**
    *
    * Hides dates on the order page (to display a custom form instead)
    *
    * @param array $item_meta - Hidden values
    * @return array $item_meta
    *
    **/
    public function wceb_hide_formatted_date( $item_meta ) {

        $item_meta[] = '_ebs_start_format';
        $item_meta[] = '_ebs_end_format';
        $item_meta[] = '_booking_status';

        return $item_meta;

    }

    /**
    *
    * Set order booking status to "Processing" if there is a bookable item in it.
    *
    * @param int $order_id - Order ID
    * @param array $data
    *
    **/
    public function wceb_set_order_booking_status( $order_id, $data ) {

        $order = wc_get_order( $order_id );
        $items = $order->get_items();

        if ( $items ) foreach ( $items as $item_id => $item ) {

            $start = wc_get_order_item_meta( $item_id, '_ebs_start_format' );

            if ( ! empty( $start ) ) {
                update_post_meta( $order_id, 'order_booking_status', 'processing' );
                return false;
            }

        }
    }

}

return new WCEB_Checkout();

endif;
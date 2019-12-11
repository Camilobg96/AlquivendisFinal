<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Emails' ) ) :

class WCEB_Emails {

    public function __construct() {

        if ( ! wp_next_scheduled( 'wceb_check_orders' ) ) {

            $ve = get_option( 'gmt_offset' ) > 0 ? '-' : '+';

            wp_schedule_event(  strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ), 'daily', 'wceb_check_orders' );

        }

        // Update statuses when saving settings
        add_action( 'easy_booking_save_settings', array( $this, 'wceb_update_orders' ) );
        add_action( 'wceb_check_orders', array( $this, 'wceb_update_orders' ) );

        // Update when modifying order items
        add_action( 'woocommerce_saved_order_items', array( $this, 'wceb_update_order_item_booking_status' ), 10, 1 );
    }

    public function wceb_update_orders() {

        // Query orders
        $args = array(
            'post_type'      => 'shop_order',
            'post_status'    => apply_filters( 
                                'easy_booking_get_order_statuses',
                                array(
                                    'wc-pending',
                                    'wc-processing',
                                    'wc-on-hold',
                                    'wc-completed',
                                    'wc-refunded'
                                ) ),
            'posts_per_page' => -1,
            'meta_key'       => 'order_booking_status',
            'meta_value'     => 'processing'
        );

        $query_orders = new WP_Query( $args );

        if ( $query_orders ) foreach ( $query_orders->posts as $post ) :

            $order_id = $post->ID;
            $this->wceb_update_order_item_booking_status( $order_id, true );

        endforeach;

        do_action( 'wceb_update_order_items_booking_statuses' );

    }

    public function wceb_update_order_item_booking_status( $order_id, $auto = false ) {
        $order    = wc_get_order( $order_id );
        $items    = $order->get_items();

        // Get current date
        $current_date = strtotime( date( 'Y-m-d' ) );
        
        $change_start     = get_option( 'wceb_set_start_booking_status' );
        $change_start_day = get_option( 'wceb_keep_start_status_for' );

        $change_processing = get_option( 'wceb_set_processing_booking_status' );

        $change_completed     = get_option( 'wceb_set_completed_booking_status' );
        $change_completed_day = get_option( 'wceb_keep_end_status_for' );

        $item_statuses = array();
        if ( $items ) foreach ( $items as $item_id => $item ) {

            $start = wc_get_order_item_meta( $item_id, '_ebs_start_format' );
            $end = wc_get_order_item_meta( $item_id, '_ebs_end_format' );

            if ( ! empty( $start ) ) {

                $start_time = strtotime( $start );
                $booking_status = wc_get_order_item_meta( $item_id, '_booking_status' );

                if ( ! empty( $end ) ) {

                    $end_time = strtotime( $end );

                    // No status and start date in the future = Pending status
                    if ( $current_date < $start_time ) {
                        $item_status = 'wceb-pending';
                    }
                    
                    // Automatically change to Start status x days before start date (defined in the plugin settings)
                    $change_start_status = strtotime( $start . ' -' . $change_start_day . ' days' );
                    if ( $change_start === 'automatic' && $current_date >= $change_start_status ) {
                        $item_status = 'wceb-start';
                    }

                    // Automatically change to Processing status between start and end dates
                    if ( $change_processing === 'automatic' && $current_date > $start_time && $current_date < $end_time ) {
                        $item_status = 'wceb-processing';
                    }

                    // Automatically change to End status on end date
                    if ( $current_date >= $end_time ) {
                        $item_status = 'wceb-end';
                    }

                    // If start and end dates are the same day, set Processing instead of Start/End
                    if ( $change_processing === 'automatic' && $start_time === $end_time && $current_date === $start_time && $current_date === $end_time ) {
                        $item_status = 'wceb-processing';
                    }

                    // Automatically change to Completed status after end date
                    $change_completed_status = strtotime( $end . ' +' . $change_completed_day . ' days' );
                    if ( $change_completed === 'automatic' && $current_date > $change_completed_status ) {
                        $item_status = 'wceb-completed';
                    }

                    if ( isset( $item_status ) ) {

                        // Update order item status once it is defined (only when using the CRON function)
                        if ( true === $auto && $booking_status !== $item_status ) {
                            wc_update_order_item_meta( $item_id, '_booking_status', sanitize_text_field( $item_status ) );

                            $old_status = str_replace( 'wceb-', '', $booking_status );
                            $new_status = str_replace( 'wceb-', '', $item_status );
                            
                            do_action( 'wceb_order_item_status_' . $new_status, $item_id );
                            do_action( 'wceb_order_item_status_changed_from_' . $old_status . '_to_' . $new_status, $item_id );
                        }

                        // Store item status in an array in order to get all order item statuses later
                        $item_statuses[] = $item_status;
                    }

                } else {

                    // No status and start date in the future = Pending status
                    if ( $current_date < $start_time ) {
                        $item_status = 'wceb-pending';
                    }

                    // Automatically change to Start status x days before start date (defined in the plugin settings)
                    $change_start_status = strtotime( $start . ' -' . $change_start_day . ' days' );
                    if ( $change_start === 'automatic' && $current_date >= $change_start_status ) {
                        $item_status = 'wceb-start';
                    }

                    if ( $change_processing === 'automatic' && $current_date >= $start_time ) {
                        $item_status = 'wceb-processing';
                    }

                    // Automatically change to End status after date
                    if ( $current_date > $start_time ) {
                        $item_status = 'wceb-end';
                    }

                    $change_completed_status = strtotime( $start . ' +' . $change_completed_day . ' days' );
                    if ( $change_completed === 'automatic' && $current_date > $change_completed_status ) {
                        $item_status = 'wceb-completed';
                    }

                    if ( isset( $item_status ) ) {

                        // Update order item status once it is defined (only when using the CRON function)
                        if ( true === $auto && $booking_status !== $item_status ) {
                            wc_update_order_item_meta( $item_id, '_booking_status', sanitize_text_field( $item_status ) );

                            $old_status = str_replace( 'wceb-', '', $booking_status );
                            $new_status = str_replace( 'wceb-', '', $item_status );

                            do_action( 'wceb_order_item_status_' . $new_status, $item_id );
                            do_action( 'wceb_order_item_status_changed_from_' . $old_status . '_to_' . $new_status, $item_id );
                        }

                        // Store item status in an array in order to get all order item statuses later
                        $item_statuses[] = $item_status;
                    }

                }

            }

        }

        // Add order booking status to prevent getting too many orders when updating availabilities
        if ( ! empty( $item_statuses ) ) {

            $item_statuses = array_flip( $item_statuses );

            // If all items in the order have the "wceb-completed" status, mark order booking status as complete
            if ( count( $item_statuses ) === 1 && array_key_exists( 'wceb-completed', $item_statuses ) ) {
                update_post_meta( $order_id, 'order_booking_status', 'completed' );
            } else {
                update_post_meta( $order_id, 'order_booking_status', 'processing' );
            }
            
            do_action( 'wceb_order_booking_status_changed', $order_id, $item_statuses );
        } else {
            update_post_meta( $order_id, 'order_booking_status', 'completed' );
        }
    }

}

return new WCEB_Emails();

endif;
<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Admin_Product' ) ) :

class Admin_Product {

    private $allowed_types;

	public function __construct() {

        // Get allowed product types
        $this->allowed_types = wceb_get_allowed_product_types();

        add_action( 'product_type_options', array( $this, 'add_bookable_option' ), 10, 1 );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_easy_booking_tab' ), 10, 1 );
        add_action( 'woocommerce_product_data_panels', array( $this, 'easy_booking_data_panel' ) );

        if ( $this->allowed_types ) foreach ( $this->allowed_types as $type ) {
            add_action( 'woocommerce_process_product_meta_' . $type, array( $this, 'save_product_booking_options' ) );
        }

	}

    /**
    *
    * Adds a checkbox to the product admin page to set the product as bookable.
    *
    * @param array $product_type_options
    * @return array $product_type_options
    *
    **/
    public function add_bookable_option( $product_type_options ) {
        global $product_object;

        $is_bookable = is_a( $product_object, 'WC_Product' ) ? $product_object->get_meta( '_bookable', true ) : '';
        
        // Backward compatibility < 2.2.4
        if ( is_a( $product_object, 'WC_Product' ) && empty( $is_bookable ) ) {
            $is_bookable = $product_object->get_meta( '_booking_option', true );
        }

        $show = array();
        if ( $this->allowed_types ) foreach ( $this->allowed_types as $type ) {
            $show[] = 'show_if_' . $type;
        }

        $product_type_options['wceb_bookable'] = array(
            'id'            => '_bookable',
            'wrapper_class' => implode( ' ', $show ),
            'label'         => __( 'Bookable', 'woocommerce-easy-booking-system' ),
            'description'   => __( 'Bookable products can be rent or booked on a daily/weekly/custom schedule.', 'woocommerce-easy-booking-system' ),
            'default'       => $is_bookable === 'yes' ? 'yes' : 'no'
        );

        return $product_type_options;
    }

    /**
    *
    * Adds a booking tab to the product admin page for booking options.
    *
    * @param array $product_data_tabs
    * @return array $product_data_tabs
    *
    **/
    public function add_easy_booking_tab( $product_data_tabs ) {

        $product_data_tabs['bookings'] = array(
                'label'    => __( 'Bookings', 'woocommerce-easy-booking-system' ),
                'target'   => 'booking_product_data',
                'class'    => array( 'show_if_bookable' ),
                'priority' => 15
        );

        return $product_data_tabs;

    }

    /**
    *
    * Adds booking options in the booking tab.
    *
    **/
    public function easy_booking_data_panel() {
        global $post;

        $product = wc_get_product( $post->ID );
        $product_type = $product->get_type();

        include( 'views/products/html-wceb-product-options.php' );
    }

    /**
    *
    * Saves checkbox value and booking options for the product
    *
    * @param int $post_id
    *
    **/
    public function save_product_booking_options( $post_id ) {

        $booking_data = array(
            'bookable'                => isset( $_POST['_bookable'] ) ? 'yes' : 'no',
            'dates'                   => isset( $_POST['_number_of_dates'] ) ? $_POST['_number_of_dates'] : '',
            'booking_min'             => isset( $_POST['_booking_min'] ) ? $_POST['_booking_min'] : '',
            'booking_max'             => isset( $_POST['_booking_max'] ) ? $_POST['_booking_max'] : '',
            'first_available_date'    => isset( $_POST['_first_available_date'] ) ? $_POST['_first_available_date'] : '',
            'booking_duration'        => isset( $_POST['_booking_duration'] ) ? $_POST['_booking_duration'] : '',
            'custom_booking_duration' => isset( $_POST['_custom_booking_duration'] ) ? $_POST['_custom_booking_duration'] : ''
        );

        wceb_save_product_booking_options( $post_id, $booking_data );

    }

}

return new Admin_Product();

endif;
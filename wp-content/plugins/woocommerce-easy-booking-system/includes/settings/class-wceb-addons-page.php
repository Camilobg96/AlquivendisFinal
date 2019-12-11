<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

class Addons_Page {
	
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_addons_page' ), 10 );

		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'add_addons_page' ), 10 );
		}
		
	}

	/**
	 *
	 * Add add-ons page into "Easy Booking" menu.
	 *
	 */
	public function add_addons_page() {

		// Create a "Add-ons" page inside "Easy Booking" menu
		$addons_page = add_submenu_page(
			'easy-booking',
			__( 'Add-ons', 'woocommerce-easy-booking-system' ),
			__( 'Add-ons', 'woocommerce-easy-booking-system' ),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking-addons',
			array( $this, 'display_addons_page' )
		);

	}

	/**
	 *
	 * Load HTML for add-ons page.
	 *
	 */
	public function display_addons_page() {
		include_once( 'views/html-wceb-addons-page.php' );
	}

}

return new Addons_Page();
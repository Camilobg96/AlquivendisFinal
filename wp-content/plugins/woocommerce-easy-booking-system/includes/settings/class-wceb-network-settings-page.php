<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

class Network_Settings_Page {
	
	public function __construct() {

		add_action( 'network_admin_menu', array( $this,'add_network_settings_page' ) );
		
	}

	/**
	 *
	 * Add network settings page into "Easy Booking" menu for multisites.
	 *
	 */
	public function add_network_settings_page() {

		// Create a "Network" page inside "Easy Booking" menu for multisites
		$option_page = add_submenu_page(
			'easy-booking',
			__( 'Network Settings', 'woocommerce-easy-booking-system' ),
			__( 'Network Settings', 'woocommerce-easy-booking-system' ),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking',
			array( $this, 'display_network_settings_page' )
		);

	}

	/**
	 *
	 * Load HTML for network settings page.
	 *
	 */
	public function display_network_settings_page() {
		include_once( 'views/html-wceb-network-settings-page.php' );
	}

}

return new Network_Settings_Page();
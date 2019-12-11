<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

class Settings_Page {
	
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_settings_page' ), 10 );

	}

	/**
	 *
	 * Add settings page into "Easy Booking" menu.
	 *
	 */
	public function add_settings_page() {

		// Create a "Settings" page inside "Easy Booking" menu
		$settings_page = add_submenu_page(
			'easy-booking',
			__( 'Settings', 'woocommerce-easy-booking-system' ),
			__( 'Settings', 'woocommerce-easy-booking-system' ),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking',
			array( $this, 'display_settings_page' )
		);

		// Maybe load scripts on the "Settings" page.
		add_action( 'admin_print_scripts-'. $settings_page, array( $this, 'load_settings_scripts' ) );

	}

	/**
	 *
	 * Load HTML for settings page.
	 *
	 */
	public function display_settings_page() {
		include_once( 'views/html-wceb-settings-page.php' );
	}

	/**
	 *
	 * Load CSS and JS for settings page.
	 *
	 */
	public function load_settings_scripts() {

		// WP colorpicker CSS.
		wp_enqueue_style( 'wp-color-picker' );

		// WP colorpicker JS.
	  	wp_enqueue_script(
	  		'color-picker',
	  		plugins_url( 'assets/js/admin/colorpicker.min.js', WCEB_PLUGIN_FILE ),
	  		array( 'wp-color-picker' ),
	  		false,
	  		true
	  	);

	}

}

return new Settings_Page();
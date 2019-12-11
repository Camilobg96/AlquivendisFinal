<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Reports_Page' ) ) :

class Reports_Page {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_reports_page' ), 10 );
	}

	/**
	 *
	 * Add reports page into "Easy Booking" menu
	 *
	 */
	public function add_reports_page() {

		// Create a "Reports" page inside "Easy Booking" menu
		$reports_page = add_submenu_page(
			'easy-booking',
			__( 'Reports', 'woocommerce-easy-booking-system' ),
			__( 'Reports', 'woocommerce-easy-booking-system' ),
			apply_filters( 'easy_booking_settings_capability', 'manage_options' ),
			'easy-booking-reports',
			array( $this, 'display_reports_page' )
		);

		// Load scripts on this page only
		add_action( 'admin_print_scripts-'. $reports_page, array( $this, 'load_reports_scripts' ) );

	}

	/**
	 *
	 * Load HTML for reports page.
	 *
	 */
	public function display_reports_page() {
		include_once( 'views/html-wceb-reports-page.php' );
	}

	/**
	 *
	 * Load CSS and JS for reports page.
	 *
	 */
	public function load_reports_scripts() {
		
		// WooCommerce scripts
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'wc-enhanced-select' );
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script( 'wc-admin-meta-boxes' );

		// WooCommerce styles
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

		// Easy Booking scripts
		wp_enqueue_script( 'pickadate' );

		wp_enqueue_script(
			'easy_booking_reports',
			wceb_get_file_path( 'admin', 'wceb-reports', 'js' ),
			array( 'jquery' ),
			'1.0',
			true
		);

		wp_enqueue_script( 'datepicker.language' );

		// Easy Booking styles
		wp_enqueue_style(
			'easy_booking_reports_styles',
			wceb_get_file_path( 'admin', 'wceb-reports', 'css' ),
			array(),
			1.0
		);

		wp_enqueue_style( 'picker' );

		// Action hook to load extra scripts on the reports page
		do_action( 'easy_booking_load_report_scripts' );

	}

}

return new Reports_Page();

endif;
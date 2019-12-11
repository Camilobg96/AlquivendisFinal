<?php

namespace EasyBooking;
use EasyBooking\Settings;

defined( 'ABSPATH' ) || exit;

class Settings_Network {

	private $settings;

	public function __construct() {

		$this->settings = $this->get_settings();

		add_action( 'admin_init', array( $this, 'settings' ) );

	}

	/**
	 *
	 * Get array of network settings (refer to each add-on).
	 * @return array | $settings
	 *
	 */
	private function get_settings() {

		$settings = apply_filters( 'easy_booking_network_settings', array() );
		return $settings;

	}

	/**
	 *
	 * Init network settings.
	 *
	 */
	public function settings() {

		$this->register_settings();

		// Init network settings the first time
		$this->init_settings();

	}

	/**
	 *
	 * Register general settings.
	 *
	 */
	private function register_settings() {

		register_setting(
			'easy_booking_global_settings',
			'easy_booking_global_settings', 
			array( $this, 'sanitize_network_settings' )
		);

	}

	/**
	 *
	 * Maybe init network settings.
	 *
	 */
	private function init_settings() {

        if ( false === get_option( 'easy_booking_global_settings' ) ) {
            update_option( 'easy_booking_global_settings', array() );
        }

	}

	/**
	 *
	 * Sanitize "Global settings" option.
	 *
	 */
	public function sanitize_network_settings( $value ) {
		return array_map( 'sanitize_text_field', $value );
	}
	
}

return new Settings_Network();
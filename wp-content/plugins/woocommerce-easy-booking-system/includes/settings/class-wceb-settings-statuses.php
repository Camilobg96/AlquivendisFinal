<?php

namespace EasyBooking;
use EasyBooking\Settings;

defined( 'ABSPATH' ) || exit;

class Settings_Statuses {

	private $settings;

	public function __construct() {

		$this->settings = $this->get_settings();

		add_action( 'admin_init', array( $this, 'settings' ) );
		add_action( 'easy_booking_settings_statuses_tab', array( $this, 'booking_statuses_settings_tab' ), 10 );

	}

	/**
	 *
	 * Get array of booking statuses settings.
	 * @return array | $settings
	 *
	 */
	private function get_settings() {

		// Backward compatibility
		$wceb_settings = get_option( 'easy_booking_settings' );

		$settings = array(
			'set_start_booking_status'      => isset( $wceb_settings['easy_booking_start_status'] ) ? $wceb_settings['easy_booking_start_status'] : 'automatic',
			'keep_start_status_for'         => isset( $wceb_settings['easy_booking_start_status_change'] ) ? $wceb_settings['easy_booking_start_status_change'] : '0',
			'set_processing_booking_status' => isset( $wceb_settings['easy_booking_processing_status'] ) ? $wceb_settings['easy_booking_processing_status'] : 'automatic',
			'set_end_booking_status'        => 'automatic',
			'keep_end_status_for'           => isset( $wceb_settings['easy_booking_completed_status_change'] ) ? $wceb_settings['easy_booking_completed_status_change'] : '0',
			'set_completed_booking_status'  => isset( $wceb_settings['easy_booking_completed_status'] ) ? $wceb_settings['easy_booking_completed_status'] : 'automatic'
		);

		return $settings;

	}

	/**
	 *
	 * Init booking statuses settings.
	 *
	 */
	public function settings() {

		$this->add_settings_sections();
		$this->register_settings();
		$this->add_settings_fields();

		// Init booking_statuses settings the first time
		$this->init_settings();

	}
	
	/**
	 *
	 * Add booking statuses settings section.
	 *
	 */
	private function add_settings_sections() {

		add_settings_section(
			'easy_booking_main_settings',
			__( 'Booking statuses settings', 'woocommerce-easy-booking-system' ),
			array( $this, 'booking_statuses_settings_section' ),
			'easy_booking_statuses_settings'
		);

	}

	/**
	 *
	 * Register booking statuses settings.
	 *
	 */
	private function register_settings() {

		foreach ( $this->settings as $setting => $value ) {

			$function_name = 'sanitize_' . $setting;
			$args = array(
				'type'              => 'string',
				'description'       => '',
				'sanitize_callback' => method_exists( $this, $function_name ) ? array( $this, 'sanitize_' . $setting ) : 'sanitize_text_field',
				'show_in_rest'      => false
			);

			register_setting(
				'easy_booking_statuses_settings',
				'wceb_' . $setting,
				$args
			);

		}

	}

	/**
	 *
	 * Add booking statuses settings fields.
	 *
	 */
	private function add_settings_fields() {

		foreach ( $this->settings as $setting => $value ) {

			$field_name = str_replace( '_', ' ', $setting );

			 add_settings_field(
				'wceb_' . $setting,
				__( ucfirst( $field_name ), 'woocommerce-easy-booking-system' ),
				array( $this, $setting ),
				'easy_booking_statuses_settings',
				'easy_booking_main_settings'
			);

		}

	}

	/**
	 *
	 * Maybe init boking statuses settings.
	 *
	 */
	private function init_settings() {

		foreach ( $this->settings as $setting => $value ) {

			if ( false === get_option( 'wceb_' . $setting ) ) {
				update_option( 'wceb_' . $setting, $value );
			}

		}

	}

	/**
	 *
	 * Display booking statuses settings fields in "Booking statuses" tab.
	 *
	 */
	public function booking_statuses_settings_tab() {

		do_settings_sections( 'easy_booking_statuses_settings' );
		settings_fields( 'easy_booking_statuses_settings' );

	}

	/**
	 *
	 * Booking statuses settings section description.
	 *
	 */
	public function booking_statuses_settings_section() {

		?>

		<p><b><?php _e( 'These booking statuses are for information and organization purposes only, and are not related to WooCommerce order statuses.', 'woocommerce-easy-booking-system' ) ?></b></p>

		<p><?php _e( 'They follow this pattern:', 'woocommerce-easy-booking-system' ) ?></p>
		<ul>
			<li><b><?php _e( 'Pending:&nbsp;', 'woocommerce-easy-booking-system' ) ?></b><?php _e( 'Booking hasn\'t started yet.', 'woocommerce-easy-booking-system' ) ?></li>
			<li><b><?php _e( 'Start:&nbsp;', 'woocommerce-easy-booking-system' ) ?></b><?php _e( 'Booking starts. If you need time to prepare or deliver your items, keep this status a few days before the actual booking start date.', 'woocommerce-easy-booking-system' ) ?></li>
			<li><b><?php _e( 'Processing&hellip;', 'woocommerce-easy-booking-system' ) ?></b></li>
			<li><b><?php _e( 'End:&nbsp;', 'woocommerce-easy-booking-system' ) ?></b><?php _e( 'Booking ends. If you need time to retrieve or prepare your items for another booking, keep this status a few days after the actual booking end date.', 'woocommerce-easy-booking-system' ) ?></li>
			<li><b><?php _e( 'Completed:&nbsp;', 'woocommerce-easy-booking-system' ) ?></b><?php _e( 'Booking is over.', 'woocommerce-easy-booking-system' ) ?></li>
		</ul>

		<p><?php _e( 'You can choose to change them manually or automatically and then easily track your bookings in the "Reports" page. To manually change an item booking status, go to the corresponding order page and click on "Edit item".', 'woocommerce-easy-booking-system' ) ?></p>
		
		<?php

	}

	/**
	 *
	 * "Set start booking status" option.
	 *
	 */
	public function set_start_booking_status() {

		Settings::select( array(
			'id'          => 'set_start_booking_status',
			'name'        => 'wceb_set_start_booking_status',
			'value'       => get_option( 'wceb_set_start_booking_status' ) ? get_option( 'wceb_set_start_booking_status' ) : 'automatic',
			'description' => __( 'If set to "Automatically", "Start" status will be set on booking start date.', 'woocommerce-easy-booking-system' ),
			'options'     => array(
				'automatic' => __( 'Automatically', 'woocommerce-easy-booking-system' ),
				'manual'    => __( 'Manually', 'woocommerce-easy-booking-system' )
			)
		));

	}

	/**
	 *
	 * "Keep start booking status for" option.
	 *
	 */
	public function keep_start_status_for() {

		Settings::input( array(
			'type'        => 'number',
			'id'          => 'keep_start_status_for',
			'name'        => 'wceb_keep_start_status_for',
			'description' => __( 'Day(s) before start date. Only if automatically is set.', 'woocommerce-easy-booking-system' ),
			'value'       =>  get_option( 'wceb_keep_start_status_for' ) ? get_option( 'wceb_keep_start_status_for' ) : '0',
			'custom_attributes' => array(
				'min' => 0,
				'max' => 30
			)
		));

	}

	/**
	 *
	 * "Set processing booking status" option.
	 *
	 */
	public function set_processing_booking_status() {

		Settings::select( array(
			'id'          => 'set_processing_booking_status',
			'name'        => 'wceb_set_processing_booking_status',
			'value'       => get_option( 'wceb_set_processing_booking_status' ) ? get_option( 'wceb_set_processing_booking_status' ) : 'automatic',
			'description' => __( 'If set to "Automatically", "Processing" status will start the day after booking start date.', 'woocommerce-easy-booking-system' ),
			'options'     => array(
				'automatic' => __( 'Automatically', 'woocommerce-easy-booking-system' ),
				'manual'    => __( 'Manually', 'woocommerce-easy-booking-system' )
			)
		));

	}

	/**
	 *
	 * "Set end booking status" option.
	 *
	 */
	public function set_end_booking_status() {

		Settings::select( array(
			'id'          => 'set_end_booking_status',
			'name'        => 'wceb_set_end_booking_status',
			'value'       => get_option( 'wceb_set_end_booking_status' ) ? get_option( 'wceb_set_end_booking_status' ) : 'automatic',
			'description' => __( 'If set to "Automatically", "End" status will be set on booking end date.', 'woocommerce-easy-booking-system' ),
			'options'     => array(
				'automatic' => __( 'Automatically', 'woocommerce-easy-booking-system' ),
				'manual'    => __( 'Manually', 'woocommerce-easy-booking-system' )
			)
		));

	}

	/**
	 *
	 * "Keep end booking status for" option.
	 *
	 */
	public function keep_end_status_for() {

		Settings::input( array(
			'type'        => 'number',
			'id'          => 'keep_end_status_for',
			'name'        => 'wceb_keep_end_status_for',
			'description' => __( 'Day(s) after end date. Only if automatically is set.', 'woocommerce-easy-booking-system' ),
			'value'       =>  get_option( 'wceb_keep_end_status_for' ) ? get_option( 'wceb_keep_end_status_for' ) : '0',
			'custom_attributes' => array(
				'min' => 0,
				'max' => 30
			)
		));

	}

	/**
	 *
	 * "Set completed booking status" option.
	 *
	 */
	public function set_completed_booking_status() {

		Settings::select( array(
			'id'          => 'set_completed_booking_status',
			'name'        => 'wceb_set_completed_booking_status',
			'value'       => get_option( 'wceb_set_completed_booking_status' ) ? get_option( 'wceb_set_completed_booking_status' ) : 'automatic',
			'options'     => array(
				'automatic' => __( 'Automatically', 'woocommerce-easy-booking-system' ),
				'manual'    => __( 'Manually', 'woocommerce-easy-booking-system' )
			)
		));

	}

	/**
	 *
	 * Sanitize "Keep start booking status for" option.
	 *
	 */
	public function sanitize_keep_start_status_for( $value ) {
		return Settings::sanitize_duration_field( $value, 0, 30 );
	}

	/**
	 *
	 * Sanitize "Keep end booking status for" option.
	 *
	 */
	public function sanitize_keep_end_status_for( $value ) {
		return Settings::sanitize_duration_field( $value, 0, 30 );
	}
	
}

return new Settings_Statuses();
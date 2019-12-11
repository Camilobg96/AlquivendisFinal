<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Admin_Assets' ) ) :

class Admin_Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 20 );
	}

	public function enqueue_admin_scripts() {

        // Register scripts and styles
        $this->register_pickadate_scripts();
        $this->register_pickadate_styles();
        $this->register_admin_scripts();
        $this->register_admin_styles();

        // Get current screen ID
        $screen    = get_current_screen();
        $screen_id = $screen->id;

        // Enqueue common admin scripts and styles
        wp_enqueue_script( 'wceb-admin-js' );
        wp_enqueue_style( 'wceb-admin-css' );

        // Admin product pages
        if ( in_array( $screen_id, array( 'product' ) ) ) {

            wp_enqueue_script( 'wceb-admin-product' );
            wp_enqueue_style( 'picker' );

        }

        // Admin order pages
        if ( in_array( $screen_id, array( 'shop_order' ) ) ) {

            wp_enqueue_script( 'wceb-admin-order' );
            wp_enqueue_style( 'picker' );

        }
        
        // Admin product and order pages
        if ( in_array( $screen_id, array( 'product' ) ) || in_array( $screen_id, array( 'shop_order' ) ) ) {

            wp_enqueue_script( 'pickadate' );

            if ( is_rtl() ) {
                wp_enqueue_style( 'rtl-style' );  
            }
            
            wp_enqueue_script( 'pickadate.language' );

        }

    }

    /**
    *
    * Register pickadate.js script and its translation.
    *
    **/
    private function register_pickadate_scripts() {

        // Debugging mode
        if ( true === wceb_script_debug() ) {

            wp_register_script(
                'picker',
                plugins_url( 'assets/js/dev/picker.js', WCEB_PLUGIN_FILE  ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_register_script(
                'legacy',
                plugins_url( 'assets/js/dev/legacy.js', WCEB_PLUGIN_FILE  ),
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_register_script(
                'pickadate',
                plugins_url( 'assets/js/dev/picker.date.js', WCEB_PLUGIN_FILE  ),
                array( 'jquery', 'picker', 'legacy' ),
                '1.0',
                true
            );

        } else {

            // Concatenated and minified script including picker.js, picker.date.js and legacy.js
            wp_register_script(
                'pickadate',
                plugins_url( 'assets/js/pickadate.min.js', WCEB_PLUGIN_FILE ),
                array( 'jquery' ),
                '1.0',
                true
            );

        }

        // Pickadate.js translation
        wp_register_script(
            'pickadate.language',
            plugins_url( 'assets/js/translations/' . WCEB_LANG . '.js', WCEB_PLUGIN_FILE ),
            array( 'jquery', 'pickadate' ),
            '1.0',
            true
        );

        wp_localize_script(
            'pickadate.language',
            'params',
            array(
                'first_day' => absint( get_option( 'start_of_week' ) )
            )
        );

    }

    /**
    *
    * Register pickadate.js CSS.
    *
    **/
    private function register_pickadate_styles() {

        // If multisite, register the CSS file corresponding to the blog ID
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            
            $blog_id = get_current_blog_id();

            wp_register_style(
                'picker',
                plugins_url( 'assets/css/default.' . $blog_id . '.min.css', WCEB_PLUGIN_FILE ),
                true
            );

        } else {

            wp_register_style(
                'picker',
                plugins_url( 'assets/css/default.min.css', WCEB_PLUGIN_FILE ),
                true
            );

        }

        // Pickadate right-to-left CSS
        wp_register_style(
            'rtl-style',
            wceb_get_file_path( '', 'rtl', 'css' ),
            true
        );

    }

    /**
    *
    * Register admin scripts.
    *
    **/
    private function register_admin_scripts() {

        // JS for pickadate.js in the admin panel
        wp_register_script(
            'wceb-admin-order',
            wceb_get_file_path( 'admin', 'wceb-admin-order', 'js' ),
            '1.0',
            true
        );

        $booking_mode = get_option( 'wceb_booking_mode' ); // Calculation mode (Days or Nights)

        wp_localize_script(
            'wceb-admin-order',
            'wceb_admin_order',
            array( 
                'booking_mode' => esc_html( $booking_mode )
            )
        );

        // JS for admin product settings
        wp_register_script(
            'wceb-admin-product',
            wceb_get_file_path( 'admin', 'wceb-admin-product', 'js' ),
            array( 'jquery' ),
            '1.0',
            true
        );

        $global_booking_duration = get_option( 'wceb_booking_duration' );
        $booking_duration_text   = __( 'days', 'woocommerce-easy-booking-system' );

        switch ( $global_booking_duration ) {
            case 'weeks' :
                $booking_duration_text = __( 'weeks', 'woocommerce-easy-booking-system' );
            break;
            case 'custom':
                $booking_duration_text = __( 'custom period', 'woocommerce-easy-booking-system' );
            break;
            default:
                $booking_duration_text = __( 'days', 'woocommerce-easy-booking-system' );
            break;
        }

        wp_localize_script(
            'wceb-admin-product',
            'wceb_admin_product',
            array(
                'number_of_dates'       => esc_html( get_option( 'wceb_number_of_dates' ) ),
                'booking_duration_text' => esc_html( $booking_duration_text ),
                'daily_duration_text'   => __( 'days', 'woocommerce-easy-booking-system' ),
                'weekly_duration_text'  => __( 'weeks', 'woocommerce-easy-booking-system' ),
                'custom_duration_text'  => __( 'custom period', 'woocommerce-easy-booking-system' )
            )
        );

        // JS for global admin functions
        wp_register_script(
            'wceb-admin-js',
            wceb_get_file_path( 'admin', 'wceb-admin', 'js' ),
            array( 'jquery' ),
            '1.0',
            true
        );

        wp_localize_script(
            'wceb-admin-js',
            'wceb_admin',
            array(
                'ajax_url'          => esc_url( admin_url( 'admin-ajax.php' ) ),
                'hide_notice_nonce' => wp_create_nonce( 'wceb-hide-notice' )
            )
        );
    }

    /**
    *
    * Register admin styles.
    *
    **/
    private function register_admin_styles() {

        // Global CSS for admin
        wp_register_style(
            'wceb-admin-css',
            wceb_get_file_path( 'admin', 'wceb-admin', 'css' ),
            WCEB_PLUGIN_FILE
        );

    }

}

return new Admin_Assets();

endif;
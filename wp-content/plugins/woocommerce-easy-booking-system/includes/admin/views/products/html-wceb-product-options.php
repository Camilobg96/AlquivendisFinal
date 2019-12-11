<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div id="booking_product_data" class="panel woocommerce_options_panel">

    <div class="options_group show_if_bookable">

        <?php woocommerce_wp_select( array(
            'id'          => 'booking_dates',
            'class'       => 'select short booking_dates',
            'name'        => '_number_of_dates',
            'label'       => __( 'Number of dates to select', 'woocommerce-easy-booking-system' ),
            'desc_tip'    => true,
            'description' => __( 'Choose whether to have one or two date(s) to select for this product.', 'woocommerce-easy-booking-system' ),
            'value'       => ! empty( $product->get_meta( '_number_of_dates' ) ) ? $product->get_meta( '_number_of_dates' ) : 'global',
            'options'     => array(
                'global' => __( 'Same as global settings', 'woocommerce-easy-booking-system' ),
                'one'    => __( 'One', 'woocommerce-easy-booking-system' ),
                'two'    => __( 'Two', 'woocommerce-easy-booking-system' )
            )
        ) ); ?>

        <div class="show_if_two_dates">

            <?php woocommerce_wp_select( array(
                'id'          => 'booking_duration',
                'class'       => 'select short booking_duration',
                'name'        => '_booking_duration',
                'label'       => __( 'Booking duration', 'woocommerce-easy-booking-system' ),
                'desc_tip'    => true,
                'description' => __( 'The booking duration of your products. Daily, weekly or a custom period. The price will be applied to the whole period.', 'woocommerce-easy-booking-system' ),
                'value'       => ! empty( $product->get_meta( '_booking_duration' ) ) ? $product->get_meta( '_booking_duration' ) : 'global',
                'options'     => array(
                    'global' => __( 'Same as global settings', 'woocommerce-easy-booking-system' ),
                    'days'   => __( 'Daily', 'woocommerce-easy-booking-system' ),
                    'weeks'  => __( 'Weekly', 'woocommerce-easy-booking-system' ),
                    'custom' => __( 'Custom', 'woocommerce-easy-booking-system' )
                )
            ) );

            woocommerce_wp_text_input( array(
                'id'                => 'custom_booking_duration',
                'class'             => 'custom_booking_duration',
                'name'              => '_custom_booking_duration',
                'label'             => __( 'Custom booking duration (days)', 'woocommerce-easy-booking-system' ),
                'value'             => ! empty( $product->get_meta( '_custom_booking_duration' ) ) ? $product->get_meta( '_custom_booking_duration' ) : '',
                'placeholder'       =>  __( 'Same as global settings', 'woocommerce-easy-booking-system' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '1',
                    'max'  => '366'
                ) 
            ) );

            woocommerce_wp_text_input( array(
                'id'                => 'booking_min',
                'class'             => 'booking_min',
                'name'              => '_booking_min',
                'label'             => __( 'Minimum booking duration', 'woocommerce-easy-booking-system' ) . ' (<span class="wceb_unit">' . __('days', 'woocommerce-easy-booking-system') . '</span>)',
                'desc_tip'          => 'true',
                'description'       => __( 'The minimum number of days / weeks / custom period to book. Leave zero to set no duration limit. Leave empty to use the global settings.', 'woocommerce-easy-booking-system' ),
                'value'             => ! empty( $product->get_meta( '_booking_min' ) ) || $product->get_meta( '_booking_min' ) === '0' ? $product->get_meta( '_booking_min' ) : '',
                'placeholder'       => __( 'Same as global settings', 'woocommerce-easy-booking-system' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '0',
                    'max'  => '3650'
                ) 
            ) );

            woocommerce_wp_text_input( array(
                'id'                => 'booking_max',
                'class'             => 'booking_max',
                'name'              => '_booking_max',
                'label'             => __( 'Maximum booking duration', 'woocommerce-easy-booking-system' ) . ' (<span class="wceb_unit">' . __('days', 'woocommerce-easy-booking-system') . '</span>)',
                'desc_tip'          => 'true',
                'description'       => __( 'The maximum number of days / weeks / custom period to book. Leave zero to set no duration limit. Leave empty to use the global settings.', 'woocommerce-easy-booking-system' ),
                'value'             => ! empty( $product->get_meta( '_booking_max' ) ) || $product->get_meta( '_booking_max' ) === '0' ? $product->get_meta( '_booking_max' ) : '',
                'placeholder'       => __( 'Same as global settings', 'woocommerce-easy-booking-system' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => '1',
                    'min'  => '0',
                    'max'  => '3650'
                )
            ) ); ?>

        </div>

        <?php woocommerce_wp_text_input( array(
            'id'                => 'first_available_date',
            'class'             => 'first_available_date',
            'name'              => '_first_available_date',
            'label'             => __( 'First available date (day)', 'woocommerce-easy-booking-system' ),
            'desc_tip'          => 'true',
            'description'       => __( 'First available date, relative to the current day. I.e. : today + 5 days. Leave zero for the current day. Leave empty to use the global settings.', 'woocommerce-easy-booking-system' ),
            'value'             => ! empty( $product->get_meta( '_first_available_date' ) ) || $product->get_meta( '_first_available_date' ) === '0' ? $product->get_meta( '_first_available_date' ) : '',
            'placeholder'       => __( 'Same as global settings', 'woocommerce-easy-booking-system' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min'  => '0',
                'max'  => '3650'
            )
        ) ); ?>

    </div>

    <?php do_action( 'easy_booking_after_booking_options', $product ); ?>
    <?php do_action( 'easy_booking_after_' . $product_type . '_booking_options', $product ); ?>

</div>
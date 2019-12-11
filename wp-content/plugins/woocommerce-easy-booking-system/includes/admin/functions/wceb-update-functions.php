<?php

defined( 'ABSPATH' ) || exit;

/**
*
* Function to update database if necessary.
* Checks if current db version is inferior to plugin db version, and runs update functions if necessary.
*
* @param bool - $full_update - Set true to run all updates.
*
*/
function wceb_db_update( $full_update = false ) {

    $current_db_version = get_option( 'easy_booking_db_version' );

    // First time init or full update (db updates started in version 2.2.4)
    if ( ! $current_db_version || empty( $current_db_version ) || true === $full_update ) {
        $current_db_version = '2.2.3';
    }

	if ( version_compare( $current_db_version, wceb_get_db_version(), '<' ) ) {

        foreach ( wceb_get_db_updates() as $index => $update_version ) {

            if ( version_compare( $current_db_version, $update_version, '<' ) ) {

                $update = str_replace( '.', '', $update_version );

                if ( function_exists( 'wceb_update_db_version_' . $update ) ) {
                    call_user_func( 'wceb_update_db_version_' . $update );
                }

            }

        }
         
    }

}

function wceb_get_db_updates() {
    $updates = array( '2.2.4', '2.2.5' );
    return $updates;
}

/**
*
* In version 2.2.4 "_booking_option" post meta becomes "_bookable".
*
*/
function wceb_update_db_version_224() {

    // Query Products
    $args = array(
        'post_type'      => array( 'product', 'product_variation' ),
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'meta_key'       => '_booking_option',
        'fields'         => 'ids'
    );

    $query_products = new WP_Query( $args );

    if ( $query_products->posts ) foreach ( $query_products->posts as $product_id ) :

        $is_bookable = get_post_meta( $product_id, '_booking_option', true );

        if ( $is_bookable === 'yes' ) {
            update_post_meta( $product_id, '_bookable', 'yes' );
        }

        delete_post_meta( $product_id, '_booking_option' );

    endforeach;

    update_option( 'easy_booking_db_version', '2.2.4' );

}

/**
*
* In version 2.2.5 "_booking_dates" post meta becomes "_number_of_dates".
*
*/
function wceb_update_db_version_225() {

    // Query Products
    $args = array(
        'post_type'      => array( 'product', 'product_variation' ),
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'meta_key'       => '_booking_dates',
        'fields'         => 'ids'
    );

    $query_products = new WP_Query( $args );

    if ( $query_products->posts ) foreach ( $query_products->posts as $product_id ) :

        $number_of_dates = get_post_meta( $product_id, '_booking_dates', true );

        if ( ! is_null( $number_of_dates ) || ! is_empty( $number_of_dates ) ) {
            update_post_meta( $product_id, '_number_of_dates', sanitize_text_field( $number_of_dates ) );
            delete_post_meta( $product_id, '_booking_dates' );
        }

    endforeach;

    update_option( 'easy_booking_db_version', '2.2.5' );

}
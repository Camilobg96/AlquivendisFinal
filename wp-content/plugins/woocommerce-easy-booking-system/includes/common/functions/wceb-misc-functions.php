<?php

defined( 'ABSPATH' ) || exit;

/**
*
* Returns true if script debug is enabled.
*
**/
function wceb_script_debug() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
}

/**
*
* Returns an array of product types compatible with Easy Booking.
*
* @return array
*
**/
function wceb_get_allowed_product_types() {

	// Filter to extend allowed product types.
	$allowed_types = apply_filters(
        'easy_booking_allowed_product_types',
        array(
            'simple',
            'variable',
            'grouped',
            'bundle'
        )
    );

    return $allowed_types;
    
}

function wceb_get_db_version() {
    return '2.2.5';
}
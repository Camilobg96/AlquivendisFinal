<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
*
* Returns whether the product is bookable or not
* @param mixed $product - Product object or product ID
* @return bool
*
**/
function wceb_is_bookable( $product ) {

	// If product ID was passed, get the product
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}

	if ( ! $product ) {
		return false;
	}

	$product_type = $product->get_type();

	// Check the product type
	$allowed_product_types   = wceb_get_allowed_product_types();
	$allowed_product_types[] = 'variation';

    $is_bookable = $product->get_meta( '_bookable', true );

    // Backward compatibility < 2.2.4
    if ( empty( $is_bookable ) ) {
        $is_bookable = $product->get_meta( '_booking_option', true );
    }

    // Check that variable products have at least one bookable variation.
    if ( $product_type === 'variable' ) {

        $has_bookable_variation = false;
        foreach ( $product->get_children() as $variation_id ) {

            $variation = wc_get_product( $variation_id );

            if ( wceb_is_bookable( $variation ) ) {
                $has_bookable_variation = true;
                break;
            }

        }

        if ( ! $has_bookable_variation ) {
            return false;
        }

    }

	return ( $is_bookable === 'yes' && in_array( $product_type, $allowed_product_types ) ) ? true : false;

}

/**
*
* Returns the product booking data
* If the data is empty, it gets the global booking setting
*
* @param WC_Product $product - Product object
* @return array - $booking_data
*
**/
function wceb_get_product_booking_data( $product ) {

    $calc_mode = get_option( 'wceb_booking_mode' );

    $product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;

	// If a setting is empty, get the global setting instead
	$booking_meta = array(
    	'booking_min',
    	'booking_max',
    	'first_available_date'
    );

	foreach ( $booking_meta as $meta ) {

		$global_meta = get_option( 'wceb_' . $meta );

        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            $product_{$meta} = $product->$meta;
        } else {
            $product_{$meta} = $product->get_meta( '_' . $meta, true );
        }

		if ( ! isset( $product_{$meta} ) || ( empty( $product_{$meta} ) && $product_{$meta} != '0' ) ) {
			${$meta} = $global_meta;
		} else {
			${$meta} = $product_{$meta};
		}

	}

	if ( $booking_min <= 0 ) {
		$booking_min = 1;
	}

	// Get custom booking duration
	$custom_booking_duration = wceb_get_product_custom_booking_duration( $product );

	if ( ! $product->is_type( 'variable' ) ) {

		// Multiply by custom booking duration
		$booking_min = $calc_mode === 'days' ? $booking_min * $custom_booking_duration - 1 : $booking_min * $custom_booking_duration;
		$booking_max = $calc_mode === 'days' ? $booking_max * $custom_booking_duration - 1 : $booking_max * $custom_booking_duration;

		if ( $booking_min <= 0 ) {
			$booking_min = 0;
		}

		if ( ( $calc_mode === 'days' && $booking_max < 0 ) || ( $calc_mode === 'nights' && $booking_max <= 0 ) ) {
			$booking_max = false;
		}

	}

	$booking_data = array(
		'booking_dates'           => wceb_get_product_booking_dates( $product ),
		'booking_duration'        => wceb_get_product_booking_duration( $product ),
		'custom_booking_duration' => $custom_booking_duration,
		'booking_min'             => apply_filters( 'easy_booking_product_booking_min', $booking_min, $product ),
		'booking_max'             => apply_filters( 'easy_booking_product_booking_max', $booking_max, $product ),
		'first_available_date'    => apply_filters( 'easy_booking_product_first_available_date', $first_available_date, $product )
	);

	return $booking_data;

}

/**
*
* Returns the variation booking data
* If the data is empty, it gets the parent product booking setting (or the global settings)
*
* @param WC_Product_Variation - $variation - Variation object
* @param array - $parent_booking_data - The parent product booking data
* @return array - $booking_data
*
**/
function wceb_get_variation_booking_data( $variation, array $parent_booking_data ) {

	if ( ! $variation ) {
		return;
	}

    $calc_mode = get_option( 'wceb_booking_mode' );

	$variation_id = is_callable( array( $variation, 'get_id' ) ) ? $variation->get_id() : $variation->variation_id;

	// If a setting is empty, get the parent setting instead
	$booking_meta = array(
    	'booking_min',
    	'booking_max',
    	'first_available_date'
    );

	foreach ( $booking_meta as $meta ) {

        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            $variation_{$meta} = $variation->$meta;
        } else {
            $variation_{$meta} = $variation->get_meta( '_' . $meta, true );
        }

		if ( ! isset( $variation_{$meta} ) || ( empty( $variation_{$meta} ) && $variation_{$meta} != '0' ) ) {
			${$meta} = $parent_booking_data[$meta];
		} else {
			${$meta} = $variation_{$meta};
		}
	}

	if ( $booking_min <= 0 ) {
		$booking_min = 1;
	}

	// Get custom booking duration
	$custom_booking_duration = wceb_get_product_custom_booking_duration( $variation );

	// Multiply by custom booking duration
	$booking_min = $calc_mode === 'days' ? $booking_min * $custom_booking_duration - 1 : $booking_min * $custom_booking_duration;
	$booking_max = $calc_mode === 'days' ? $booking_max * $custom_booking_duration - 1 : $booking_max * $custom_booking_duration;

	if ( $booking_min <= 0 ) {
		$booking_min = 0;
	}

	if ( ( $calc_mode === 'days' && $booking_max < 0 ) || ( $calc_mode === 'nights' && $booking_max <= 0 ) ) {
		$booking_max = false;
	}

	$booking_data = array(
		'booking_dates'           => wceb_get_product_booking_dates( $variation ),
		'booking_duration'        => wceb_get_product_booking_duration( $variation ),
		'custom_booking_duration' => $custom_booking_duration,
		'booking_min'             => apply_filters( 'easy_booking_product_booking_min', $booking_min, $variation ),
		'booking_max'             => apply_filters( 'easy_booking_product_booking_max', $booking_max, $variation ),
		'first_available_date'    => apply_filters( 'easy_booking_product_first_available_date', $first_available_date, $variation )
	);

	return $booking_data;

}

/**
*
* Gets the html to display the booking price on the product page
*
* @param WC_Product or WC_Product_Variation $product - The product or variation object
* @return str - $price_html
*
**/
function wceb_get_price_html( $product ) {

    $calc_mode = get_option( 'wceb_booking_mode' );

	// Get product booking duration
    $booking_duration = wceb_get_product_booking_duration( $product );

	// Get product custom booking duration
	$custom_duration = wceb_get_product_custom_booking_duration( $product );

	// Get number of dates to set
	$dates_format = wceb_get_product_booking_dates( $product );

    // If it is a variable product, price format will be displayed in Javascript for each variation
    if ( $product->is_type( 'variable' ) || ! wceb_is_bookable( $product ) || $dates_format === 'one' ) {

        $price_html = '';

    } else {

        if ( $booking_duration === 'weeks' ) {

            $price_html = __(' / week', 'woocommerce-easy-booking-system');

        } else if ( $booking_duration === 'custom' && $custom_duration != '1' ) {

        	if ( $calc_mode === 'nights' ) {
        		$price_html = sprintf( _n( ' / %s night', ' / %s nights', $custom_duration, 'woocommerce-easy-booking-system' ), $custom_duration );
        	} else {
        		$price_html = sprintf( _n( ' / %s day', ' / %s days', $custom_duration, 'woocommerce-easy-booking-system' ), $custom_duration );
        	}

        } else if ( $calc_mode === 'nights' ) {

            $price_html = __(' / night', 'woocommerce-easy-booking-system');

        } else {

            $price_html = __(' / day', 'woocommerce-easy-booking-system');

        }

    }

    return apply_filters( 'easy_booking_get_price_html', $price_html, $product, $booking_duration, $custom_duration );
}

/**
*
* Gets product number of dates to select
*
* @param WC_Product or WC_Product_Variation $product - The product or variation object
* @return str - $booking_dates
*
**/
function wceb_get_product_booking_dates( $product ) {
    
    $product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;

    $booking_dates = $product->get_meta( '_number_of_dates', true );

    // Backward compatibility 2.2.5
    if ( empty( $booking_dates ) ) {
        $booking_dates = $product->get_meta( '_booking_dates', true );
    }
 
    // If it is a children grouped product or bundled product on the parent product page
    if ( is_product() ) {

    	$current_id = get_queried_object_id();
    	$parent_product = wc_get_product( $current_id );
    	
    	// If it is a children grouped product or bundled product on the parent product page
	    if ( ( $parent_product->is_type( 'grouped' ) || $parent_product->is_type( 'bundle' ) ) && $current_id !== $product_id ) {

            $_product = wc_get_product( $current_id );
            $booking_dates = $_product->get_meta( '_number_of_dates', true );

            // Backward compatibility 2.2.5
            if ( empty( $booking_dates ) ) {
                $booking_dates = $_product->get_meta( '_booking_dates', true );
            }

	    }
	    
	}

    // If it is a variation with the parent product setting
    if ( $product->is_type( 'variation' ) && ( $booking_dates === 'parent' || empty( $booking_dates ) ) ) {

        // Get parent product
        $parent_product_id = is_callable( array( $product, 'get_parent_id' ) ) ? $product->get_parent_id() : $product->parent->id;
        $parent_product = wc_get_product( $parent_product_id );

        if ( $parent_product ) {

            $booking_dates = $parent_product->get_meta( '_number_of_dates', true );

            // Backward compatibility 2.2.5
            if ( empty( $booking_dates ) ) {
                $booking_dates = $parent_product->get_meta( '_booking_dates', true );
            }

        }

    }

    if ( empty( $booking_dates ) || $booking_dates === 'global' ) {
    	$booking_dates = get_option( 'wceb_number_of_dates' );
    }

    return $booking_dates;

}

/**
*
* Gets product booking duration
*
* @param WC_Product or WC_Product_Variation $product - The product or variation object
* @return str - $booking_duration
*
**/
function wceb_get_product_booking_duration( $product ) {

    $product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;
    
    if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
        $booking_duration = get_post_meta( $product_id, '_booking_duration', true );
    } else {
        $booking_duration = $product->get_meta( '_booking_duration', true );
    }

    if ( is_product() ) {

    	$current_id = get_queried_object_id();
    	$parent_product = wc_get_product( $current_id );
    	
    	// If it is a children grouped product or bundled product on the parent product page
	    if ( ( $parent_product->is_type( 'grouped' ) || $parent_product->is_type( 'bundle' ) ) && $current_id !== $product_id ) {

            $_product = wc_get_product( $current_id );

            if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
                $booking_duration = get_post_meta( $current_id, '_booking_duration', true );
            } else {
                $booking_duration = $_product->get_meta( '_booking_duration', true );
            }

	    }

	}

    // If it is a variation with the parent product setting
    if ( $product->is_type( 'variation' ) && ( $booking_duration === 'parent' || empty( $booking_duration ) ) ) {

        // Get parent product
        $parent_product_id = is_callable( array( $product, 'get_parent_id' ) ) ? $product->get_parent_id() : $product->parent->id;
        $parent_product = wc_get_product( $parent_product_id );

        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            $booking_duration = get_post_meta( $parent_product_id, '_booking_duration', true );
        } else {
            $booking_duration = $parent_product->get_meta( '_booking_duration', true );
        }

    }

    if ( empty( $booking_duration ) || $booking_duration === 'global' ) {
    	$booking_duration = get_option( 'wceb_booking_duration' );
    }

    return $booking_duration;

}

/**
*
* Gets product custom booking duration
*
* @param WC_Product or WC_Product_Variation $product - The product or variation object
* @return int - $custom_booking_duration
*
**/
function wceb_get_product_custom_booking_duration( $product ) {

    $product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;

    $calc_mode = get_option( 'wceb_booking_mode' );

    if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
        $custom_booking_duration = get_post_meta( $product_id, '_custom_booking_duration', true );
    } else {
        $custom_booking_duration = $product->get_meta( '_custom_booking_duration', true );
    }

    // If it is a children grouped product or bundled product on the parent product page
    if ( is_product() ) {

    	$current_id = get_queried_object_id();
    	$parent_product = wc_get_product( $current_id );

    	// If it is a children grouped product or bundled product on the parent product page
	    if ( ( $parent_product->is_type( 'grouped' ) || $parent_product->is_type( 'bundle' ) ) && $current_id !== $product_id ) {
	    	
            $_product = wc_get_product( $current_id );

            if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
                $custom_booking_duration = get_post_meta( $current_id, '_custom_booking_duration', true );
            } else {
                $custom_booking_duration = $_product->get_meta( '_custom_booking_duration', true );
            }

	    }

	}

    // If it is a variation with the parent product setting
    if ( $product->is_type( 'variation' ) && ( empty( $custom_booking_duration ) ) ) {
        
    	// Get parent product
        $parent_product_id = is_callable( array( $product, 'get_parent_id' ) ) ? $product->get_parent_id() : $product->parent->id;
        $parent_product = wc_get_product( $parent_product_id );

        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
            $custom_booking_duration = get_post_meta( $parent_product_id, '_custom_booking_duration', true );
        } else {
            $custom_booking_duration = $parent_product->get_meta( '_custom_booking_duration', true );
        }

    }

    if ( empty( $custom_booking_duration ) || $custom_booking_duration === 'global' ) {
    	$custom_booking_duration = get_option( 'wceb_custom_booking_duration' );
    }

    $booking_duration = wceb_get_product_booking_duration( $product );
    
    // If booking duration is "Weeks" and the plugin in "Days" mode, a week is 6 days
    if ( $booking_duration === 'weeks' && $calc_mode === 'days' ) {
        $custom_booking_duration = 6;
    } else if ( $booking_duration === 'weeks' && $calc_mode === 'nights' ) {
        $custom_booking_duration = 7;
    }

    if ( $custom_booking_duration <= 0 ) {
    	$custom_booking_duration = 1;
    }

    return $custom_booking_duration;

}

/**
*
* Gets product price
*
* @param WC_Product or WC_Product_Variation $product - The product or variation object
* @param WC_Product - $child - The product child (for grouped or bundled products)
* @param bool - $display - True if displaying the price on the front end
* @param str - $type - 'single' or 'array', 'single' will return the price, 'array' an array of regular and sale price if on sale
* @return str or array - $prices
*
**/
function wceb_get_product_price( $product, $child = false, $display = false, $type = 'single' ) {

	$prices_include_tax = get_option( 'woocommerce_prices_include_tax' );
	$tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );

	if ( $child ) {
    	$_product = $child;
    } else {
    	$_product = $product;
    }

    if ( $product->is_type( 'bundle' ) && $child ) {

        if ( is_callable( array( $child, 'get_id' ) ) ) {
            $id = $child->get_id();
        } else {
            $id = isset( $child->variation_id ) ? $child->variation_id : $child->id;
        }

    	$bundled_items = $product->get_bundled_items();

        $ids = array();
        foreach ( $bundled_items as $bundled_item ) {

            if ( $bundled_item->get_product_variations() ) {

                foreach ( $bundled_item->get_product_variations() as $variation ) {
                    
                    if ( $variation['variation_id'] == $id ) {

                        if ( $bundled_item->is_priced_individually() ) {
                            $discount = $bundled_item->get_discount();
                        } else {
                            return false;
                        }
                        
                    }

                }

            } else {

                if ( $bundled_item->product_id == $id ) {

                    if ( $bundled_item->is_priced_individually() ) {
                        $discount = $bundled_item->get_discount();
                    } else {
                        return false;
                    }
                    
                }

            }

        }

        $regular_price = $child->get_regular_price();
		$price = $child->get_price();

        if ( isset( $discount ) && ! empty( $discount ) ) {
            $price -= ( $price * $discount / 100 );
        }

    } else {

		$regular_price = $_product->get_regular_price();
		$price = $_product->get_price();

	}

	if ( $type === 'array' ) {

		$prices = array( 'price' => '', 'regular_price' => '' );

		if ( true === $display ) {

            $args = array( 'price' => $price );

            if ( $tax_display_mode === 'incl' ) {
                $prices['price'] = function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, $args ) : $_product->get_price_including_tax( 1, $price );
            } else {
                $prices['price'] = function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, $args ) : $_product->get_price_excluding_tax( 1, $price );
            }

	    } else {

            $args = array( 'price' => $price );

            if ( $prices_include_tax === 'yes' ) {
                $prices['price'] = function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, $args ) : $_product->get_price_including_tax( 1, $price );
            } else {
                $prices['price'] = function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, $args ) : $_product->get_price_excluding_tax( 1, $price );
            }

	    }

		if ( true === $display ) {

            $args = array( 'price' => $regular_price );

            if ( $tax_display_mode === 'incl' ) {
                $prices['regular_price'] = function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, $args ) : $_product->get_price_including_tax( 1, $regular_price );
            } else {
                $prices['regular_price'] = function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, $args ) : $_product->get_price_excluding_tax( 1, $regular_price );
            }

	    } else {

            $args = array( 'price' => $regular_price );

            if ( $prices_include_tax === 'yes' ) {
                $prices['regular_price'] = function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, $args ) : $_product->get_price_including_tax( 1, $regular_price );
            } else {
                $prices['regular_price'] = function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, $args ) : $_product->get_price_excluding_tax( 1, $regular_price );
            }

	    }

		return $prices;

	} else {

		if ( true === $display ) {

            $args = array( 'price' => $price );

            if ( $tax_display_mode === 'incl' ) {
                return function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, $args ) : $_product->get_price_including_tax( 1, $price );
            } else {
                return function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, $args ) : $_product->get_price_excluding_tax( 1, $price );
            }

	    }

        $args = array( 'price' => $price );

        if ( $prices_include_tax === 'yes' ) {
            return function_exists( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, $args ) : $_product->get_price_including_tax( 1, $price );
        } else {
            return function_exists( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, $args ) : $_product->get_price_excluding_tax( 1, $price );
        }

	}

}

function wceb_get_product_children_ids( $product ) {

	$product_children = array();

    if ( $product->is_type('grouped') ) {
        $product_children = $product->get_children();  
    } else if ( $product->is_type('bundle') ) {

        $bundled = $product->get_bundled_item_ids();
        $product_children = array();

        if ( $bundled ) foreach ( $bundled as $bundled_item_id ) {

            $bundled_item = $product->get_bundled_item( $bundled_item_id );
            $_product = $bundled_item->product;
            
            if ( $_product->is_type( 'variable' ) ) {
                $variations = $_product->get_children();

                foreach ( $variations as $variation_id ) {
                    $product_children[] = $variation_id;
                }
            }

            $product_children[] = is_callable( array( $_product, 'get_id' ) ) ? $_product->get_id() : $_product->id;

        }

        // Add main bundle product
        $product_children[] = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;

    }

    return $product_children;

}
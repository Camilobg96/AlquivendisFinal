<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Api' ) ) :

class WCEB_Api {

	public function __construct() {

        add_filter( 'woocommerce_rest_prepare_shop_order', array( $this, 'wceb_api_add_item_meta' ), 10, 3 );

	}

    /**
    *
    * Returns start and (maybe) end date(s) when fetching orders with WooCommerce Rest API.
    *
    **/
    function wceb_api_add_item_meta( $response, $post, $request ) {

        $items = $response->data['line_items'];
        if ( $items ) foreach ( $items as $index => $item ) {

            $start = wc_get_order_item_meta( $item['id'], '_ebs_start_format', true );

            if ( ! empty( $start ) ) {
                $response->data['line_items'][$index]['meta']['start_date'] = esc_html( $start );
            }

            $end = wc_get_order_item_meta( $item['id'], '_ebs_end_format', true );

            if ( ! empty( $end ) ) {
                $response->data['line_items'][$index]['meta']['end_date'] = esc_html( $end );
            }
           
        }

        return $response;
    }
}

return new WCEB_Api();

endif;
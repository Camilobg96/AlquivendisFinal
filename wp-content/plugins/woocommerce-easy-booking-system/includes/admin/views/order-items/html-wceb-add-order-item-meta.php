<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="edit" style="display: none;">

    <table class="meta" cellspacing="0">

        <tbody class="meta_items">

            <tr data-meta_id="<?php echo esc_attr( $start_date_meta_id ); ?>">

                <td>

                    <label for="start_date" style="font-weight: bold;"><?php echo esc_html( $start_date_text ); ?>: </label>
                    <input type="hidden" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $start_date_meta_id ); ?>]" value="_ebs_start_format">
                    <input type="text"  name="meta_value[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $start_date_meta_id ); ?>]" id="start_date" class="datepicker datepicker_start--<?php echo absint( $item_id ); ?>" value="" data-value="">

                </td>

            </tr>

            <?php if ( wceb_get_product_booking_dates( $product ) === 'two' ) : ?>

                <tr data-meta_id="<?php echo esc_attr( $end_date_meta_id ); ?>">

                    <td>

                        <label for="end_date" style="font-weight: bold;"><?php echo esc_html( $end_date_text ); ?>: </label>
                        <input type="hidden" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $end_date_meta_id ); ?>]" value="_ebs_end_format">
                        <input type="text" name="meta_value[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $end_date_meta_id ); ?>]" id="end_date" class="datepicker datepicker_end--<?php echo absint( $item_id ); ?>" value="" data-value="">
                        
                    </td>

                </tr>

            <?php endif; ?>

            <tr data-meta_id="<?php echo esc_attr( $booking_status_meta_id ); ?>">

                <td>

                    <label for="booking_status" style="font-weight: bold;"><?php _e( 'Booking status', 'woocommerce-easy-booking-system' ); ?>: </label>
                    <input type="hidden" id="booking_status" name="meta_key[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $booking_status_meta_id ); ?>]" value="_booking_status">
                    <?php wceb_settings_select( array(
                        'id'          => 'booking_status',
                        'name'        => 'meta_value[' . esc_attr( $item_id ) . '][' . esc_attr( $booking_status_meta_id ) . ']',
                        'value'       => isset( $booking_status ) ? $booking_status : 'wceb-pending',
                        'options'     => array(
                            'wceb-pending'    => apply_filters( 'easy_booking_display_status_pending', __( 'Pending', 'woocommerce-easy-booking-system' ) ),
                            'wceb-start'      => apply_filters( 'easy_booking_display_status_start', __( 'Start', 'woocommerce-easy-booking-system' ) ),
                            'wceb-processing' => apply_filters( 'easy_booking_display_status_processing', __( 'Processing', 'woocommerce-easy-booking-system' ) ),
                            'wceb-end'        => apply_filters( 'easy_booking_display_status_end', __( 'End', 'woocommerce-easy-booking-system' ) ),
                            'wceb-completed'  => apply_filters( 'easy_booking_display_status_completed', __( 'Completed', 'woocommerce-easy-booking-system' ) )
                        )
                    )); ?>

                </td>

            </tr>

        </tbody>

    </table>

</div>
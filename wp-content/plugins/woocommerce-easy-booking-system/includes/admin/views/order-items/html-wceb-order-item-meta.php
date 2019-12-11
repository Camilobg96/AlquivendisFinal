<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="view">

	<table cellspacing="0" class="display_meta">
		
		<tbody>

			<tr>
			    <th><?php echo esc_html( $start_date_text ); ?>:</th>
			    <td><p><?php echo esc_html( $start_date_i18n ); ?></p></td>
			</tr>
			
			<?php if ( ! empty( $end_date ) ) : ?>

				<tr>
				    <th><?php echo esc_html( $end_date_text ); ?>: </th>
				    <td><p><?php echo esc_html( $end_date_i18n ); ?></p></td>
				</tr>

			<?php endif; ?>

			<?php if ( ! empty( $booking_status ) ) : ?>
				
				<tr>
				    <th><?php _e( 'Booking status', 'woocommerce-easy-booking-system' ); ?>: </th>
				    <?php $status = str_replace('wceb-', '', $booking_status ); ?>
				    <td><p><?php echo apply_filters( 'easy_booking_display_status_' . $status, esc_html( ucfirst( $status ) ) ); ?></p></td>
				</tr>

        	<?php endif; ?>

		</tbody>

	</table>

</div>
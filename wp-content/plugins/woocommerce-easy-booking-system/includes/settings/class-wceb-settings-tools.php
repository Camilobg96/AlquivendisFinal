<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

class Settings_Tools {

	public function __construct() {

		add_action( 'easy_booking_settings_tools_tab', array( $this, 'tools_settings_tab' ), 10 );

	}

	/**
	 *
	 * Get array of appearance settings.
	 * @return array | $settings
	 *
	 */
	public function tools_settings_tab() {

		?>

		<table class="wceb-tools-table widefat" cellspacing="0">

			<tbody class="tools">

				<tr>

					<th>
						
						<strong><?php esc_html_e( 'Update database', 'woocommerce-easy-booking-system' ); ?></strong>
						<p class="description"><?php esc_html_e( 'This tool will update your Easy Booking database to the latest version. Please ensure you make sufficient backups before proceeding.', 'woocommerce-easy-booking-system' ); ?></p>
					</th>

					<td class="run-tool">

						<button type="button" class="button easy-booking-button wceb-db-update">
							<?php esc_html_e( 'Update database', 'woocommerce-easy-booking-system' ); ?>
							<span class="wceb-response"></span>
						</button>
						<input type="hidden" name="wceb-full-db-update" value="1">

					</td>

				</tr>

			</tbody>

		</table>

		<?php

	}
	
}

return new Settings_Tools();
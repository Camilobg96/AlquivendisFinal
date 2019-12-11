<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="wrap">

	<h2><?php esc_html_e( 'Network settings for WooCommerce Easy Booking', 'woocommerce-easy-booking-system' ); ?></h2>
	<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">

		<?php settings_fields('easy_booking_global_settings'); ?>
		<?php do_settings_sections('easy_booking_global_settings'); ?>
		 
		<?php submit_button(); ?>

	</form>

</div>
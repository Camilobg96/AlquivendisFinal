<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="wrap">

	<h2><?php esc_html_e( 'WooCommerce Easy Booking Add-ons', 'woocommerce-easy-booking-system' ); ?></h2>

	<div class="addons-container row">
		<?php $addons = array(
			array(
				'name' => 'Availability Check',
				'slug' => 'availability-check',
				'desc' => __( 'Manage availabilities of your bookable products.', 'woocommerce-easy-booking-system' )
			),
			array(
				'name' => 'Duration Discounts',
				'slug' => 'duration-discounts',
				'desc' =>  __( 'Set discounts or surcharges depending on booking duration.', 'woocommerce-easy-booking-system' )
			),
			array(
				'name' => 'Disable Dates',
				'slug' => 'disable-dates',
				'desc' =>  __( 'Disable days or dates on your products\' booking schedules.', 'woocommerce-easy-booking-system' )
			),
			array(
				'name' => 'Pricing',
				'slug' => 'pricing',
				'desc' =>  __( 'Set different prices depending on a day, date or daterange.', 'woocommerce-easy-booking-system' )
			),
			array(
				'name' => 'Google Calendar',
				'slug' => 'google-calendar',
				'desc' =>  __( 'Syncronize your bookings with Google Calendar.', 'woocommerce-easy-booking-system' )
			)
		);

		$plugin_dir = plugins_url( '/', WCEB_PLUGIN_FILE );

		$active_plugins = (array) get_option( 'active_plugins', array() );

        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

		foreach ( $addons as $addon ) : ?>

			<div class="addon-single">

				<div class="addon-single__img">
					<img src="<?php echo esc_url( $plugin_dir . 'assets/img/addons/' . $addon['slug'] . '.png' ); ?>" alt="<?php esc_attr_e( $addon['slug'] ); ?>">
				</div>

				<div class="addon-single__desc">
					<h2><?php esc_html_e( $addon['name'] ); ?></h2>
					<p><?php esc_html_e( $addon['desc'] ); ?></p>
					<p>
						<?php if ( ! ( array_key_exists( 'easy-booking-' . $addon['slug'] .'/' . 'easy-booking-' . $addon['slug'] . '.php', $active_plugins ) || in_array( 'easy-booking-' . $addon['slug'] .'/' . 'easy-booking-' . $addon['slug'] . '.php', $active_plugins ) ) ) { ?>
							<a href="<?php echo esc_url( 'https://www.easy-booking.me/addon/' . $addon["slug"] ); ?>" target="_blank" class="button">
								<?php esc_html_e( 'Learn more', 'woocommerce-easy-booking-system' ); ?>
							</a>
						<?php } else { ?>
							<a href="#" class="button easy-booking-button easy-booking-button--installed">
								<?php esc_html_e( 'Installed', 'woocommerce-easy-booking-system' ); ?>
							</a>
							<a href="<?php echo esc_url( 'https://www.easy-booking.me/documentation/' . $addon["slug"] ); ?>" target="_blank" class="button">
								<?php esc_html_e( 'Documentation', 'woocommerce-easy-booking-system' ); ?>
							</a>
							<a href="<?php echo esc_url( 'https://www.easy-booking.me/support/' . $addon["slug"] ); ?>" target="_blank" class="button">
								<?php esc_html_e( 'Support', 'woocommerce-easy-booking-system' ); ?>
							</a>
						<?php } ?>
					</p>
				</div>

			</div>

		<?php endforeach; ?>

	</div>

</div>
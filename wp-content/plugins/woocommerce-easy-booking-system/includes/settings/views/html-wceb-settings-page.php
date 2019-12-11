<?php

defined( 'ABSPATH' ) || exit;

?>

<div class="wrap">

	<?php $settings_tabs = apply_filters( 'easy_booking_settings_tabs', array(
		'general'    => __( 'General ', 'woocommerce-easy-booking-system' ),
		'appearance' => __( 'Appearance', 'woocommerce-easy-booking-system' ),
		'statuses'   => __( 'Booking statuses', 'woocommerce-easy-booking-system' ),
		'tools'      => __( 'Tools', 'woocommerce-easy-booking-system' )
	));

	$current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] ); ?>

	<form method="post" action="options.php">

		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php foreach ( $settings_tabs as $tab => $label ) { ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=easy-booking&tab=' . esc_attr( $tab ) ) ); ?>" class="nav-tab <?php echo ( $current_tab === $tab ? 'nav-tab-active' : '' ) ?>"><?php echo esc_html( $label ); ?></a>
			<?php } ?>
		</h2>
			 
		<?php do_action( 'easy_booking_settings_' . $current_tab . '_tab' ); ?>
		
		<?php

		if ( $current_tab !== 'tools' ) {
			submit_button(); 
		}

		?>

	</form>

</div>
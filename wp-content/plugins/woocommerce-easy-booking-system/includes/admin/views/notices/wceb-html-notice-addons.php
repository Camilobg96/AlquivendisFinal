<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="updated easy-booking-notice">
	<p>
		<?php printf( __( 'Thanks for installing Easy Booking! If you want more features, %scheck the add-ons%s!', 'woocommerce-easy-booking-system' ), '<a href="admin.php?page=easy-booking-addons">', '</a>' ); ?>
	</p>
</div>
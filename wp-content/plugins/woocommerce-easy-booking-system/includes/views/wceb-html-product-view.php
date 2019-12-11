<!-- Display info text -->
<?php

$info_text = apply_filters( 'easy_booking_information_text', '', $product );

$product_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id;

if ( isset( $info_text ) && ! empty ( $info_text ) ) { ?>
    <p class="woocommerce-info wceb_picker_wrap wceb_info_text"><?php esc_html_e( $info_text ); ?></p>
<?php } ?>

<!-- Do not remove existing inputs' attributes (classes, ids, etc.) -->
<div class="wceb_picker_wrap">

    <p class="form-row form-row-wide">
        <label for="start_date"><?php esc_html_e( $start_date_text ); ?></label>
        <input type="text" name="start_date" id="start_date" class="datepicker datepicker_start" data-value="" placeholder="<?php esc_attr_e( $start_date_text ); ?>">
    </p>

    <p class="form-row form-row-wide show_if_two_dates" style="display:<?php echo ( $dates === 'one' ) ? 'none' : 'block'; ?>">
        <label for="end_date"><?php esc_html_e( $end_date_text ); ?></label>
        <input type="text" name="end_date" id="end_date" class="datepicker datepicker_end" data-value="" placeholder="<?php esc_attr_e( $end_date_text ); ?>">
    </p>

    <input type="hidden" name="_wceb_nonce" class="wceb_nonce" value="<?php echo wp_create_nonce( 'set-dates' ); ?>">

    <a href="#" class="reset_dates" data-ids=""><?php esc_html_e( 'Clear dates', 'woocommerce-easy-booking-system' ); ?></a>
</div>

<p class="booking_details"></p>
<p class="booking_price" data-booking_price="<?php echo floatval( $product->get_price() ); ?>" data-booking_regular_price="<?php echo floatval( $product->get_regular_price() ); ?>">

    <!-- If the product is variable, the price will be loaded with Javascript for each variation -->
    <?php if ( ! $product->is_type( 'variable' ) ) : ?>

        <span class="price">
        </span>

    <?php endif; ?>

</p>
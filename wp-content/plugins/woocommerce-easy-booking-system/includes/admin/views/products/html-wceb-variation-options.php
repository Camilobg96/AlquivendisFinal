<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="booking_variation_data show_if_variation_bookable">

    <?php $number_of_dates = get_post_meta( $variation_id, '_number_of_dates', true ); ?>
    
    <p class="form-row form-row-first">
        <label for="_var_number_of_dates[<?php echo $loop; ?>]">
            <?php _e( 'Number of dates to select', 'woocommerce-easy-booking-system' ); ?>
            <span class="tips" data-tip="<?php _e( 'Choose whether to have one or two date(s) to select for this product.', 'woocommerce-easy-booking-system' ); ?>">[?]</span>
        </label>
        <select name="_var_number_of_dates[<?php echo $loop; ?>]" id="_var_number_of_dates[<?php echo $loop; ?>]" class="select short booking_dates">
            <option value="parent" <?php selected( $number_of_dates, 'parent', true ); ?>>
                <?php _e( 'Same as parent', 'woocommerce' ); ?>
            </option>
            <option value="one" <?php selected( $number_of_dates, 'one', true ); ?>>
                <?php _e( 'One', 'woocommerce-easy-booking-system' ); ?>
            </option>
            <option value="two" <?php selected( $number_of_dates, 'two', true ); ?>>
                <?php _e( 'Two', 'woocommerce-easy-booking-system' ); ?>
            </option>
        </select>
    </p>

    <div class="show_if_two_dates">

        <?php $selected_duration = get_post_meta( $variation_id, '_booking_duration', true ); ?>

        <p class="form-row form-row-first">
            <label for="var_booking_duration[<?php echo $loop; ?>]">
                <?php _e( 'Booking duration', 'woocommerce-easy-booking-system' ); ?>
                <span class="tips" data-tip="<?php _e( 'The booking duration of your products. Daily, weekly or a custom period (e.g. 28 days for a monthly booking). The price will be applied to the whole period.', 'woocommerce-easy-booking-system' ); ?>">[?]</span>
            </label>
            <select name="_var_booking_duration[<?php echo $loop; ?>]" id="var_booking_duration[<?php echo $loop; ?>]" class="select short booking_duration">
                <option value="parent" <?php selected( $selected_duration, 'parent', true ); ?>>
                    <?php _e( 'Same as parent', 'woocommerce' ); ?>
                </option>
                <option value="days" <?php selected( $selected_duration, 'days', true ); ?>>
                    <?php _e( 'Daily', 'woocommerce-easy-booking-system' ); ?>
                </option>
                <option value="weeks" <?php selected( $selected_duration, 'weeks', true ); ?>>
                    <?php _e( 'Weekly', 'woocommerce-easy-booking-system' ); ?>
                </option>
                <option value="custom" <?php selected( $selected_duration, 'custom', true ); ?>>
                    <?php _e( 'Custom', 'woocommerce-easy-booking-system' ); ?>
                </option>
            </select>
        </p>

        <p class="form-row form-row-last custom_booking_duration_field">

            <label for="_var_custom_booking_duration[<?php echo $loop; ?>]">
                <?php echo __( 'Custom booking duration (days)', 'woocommerce-easy-booking-system' ); ?>
            </label>

            <?php $custom_booking_duration = get_post_meta( $variation_id, '_custom_booking_duration', true ); ?>

            <input type="number" class="input_text custom_booking_duration" min="1" max="366" name="_var_custom_booking_duration[<?php echo $loop; ?>]" id="_var_custom_booking_duration[<?php echo $loop; ?>]" placeholder="<?php _e( 'Same as parent', 'woocommerce' ) ?>" value="<?php if ( isset( $custom_booking_duration ) ) echo esc_attr( $custom_booking_duration ); ?>" />

        </p>

        <p class="form-row form-row-first">

            <label for="_var_booking_min[<?php echo $loop; ?>]">
                <?php echo __( 'Minimum booking duration', 'woocommerce-easy-booking-system' ) . ' (<span class="wceb_unit">' . __('days', 'woocommerce-easy-booking-system') . '</span>)'; ?>
                <span class="tips" data-tip="<?php _e( 'The minimum number of days / weeks / custom period to book. Enter zero to set no duration limit or leave blank to use the parent product\'s booking options or the global settings.', 'woocommerce-easy-booking-system' ); ?>">[?]</span>
            </label>

            <?php $booking_min = get_post_meta( $variation_id, '_booking_min', true ); ?>

            <input type="number" class="input_text booking_min" min="0" name="_var_booking_min[<?php echo $loop; ?>]" id="_var_booking_min[<?php echo $loop; ?>]" placeholder="<?php _e( 'Same as parent', 'woocommerce' ) ?>" value="<?php if ( isset( $booking_min ) ) echo esc_attr( $booking_min ); ?>" />

        </p>

        <p class="form-row form-row-last">

            <label for="_var_booking_max[<?php echo $loop; ?>]">
                <?php echo __( 'Maximum booking duration', 'woocommerce-easy-booking-system' ) . ' (<span class="wceb_unit">' . __('days', 'woocommerce-easy-booking-system') . '</span>)'; ?>
                <span class="tips" data-tip="<?php _e( 'The maximum number of days / weeks / custom period to book. Enter zero to set no duration limit or leave blank to use the parent product\'s booking options or the global settings.', 'woocommerce-easy-booking-system' ); ?>">[?]</span>
            </label>

            <?php $booking_max = get_post_meta( $variation_id, '_booking_max', true ); ?>

            <input type="number" class="input_text booking_max" min="0" name="_var_booking_max[<?php echo $loop; ?>]" id="_var_booking_max[<?php echo $loop; ?>]" placeholder="<?php _e( 'Same as parent', 'woocommerce' ) ?>" value="<?php if ( isset( $booking_max ) ) echo esc_attr( $booking_max ); ?>" />

        </p>

    </div>

    <p class="form-row form-row-first">

        <label for="_var_first_available_date[<?php echo $loop; ?>]">
            <?php _e( 'First available date', 'woocommerce-easy-booking-system' ); ?>
            <span class="tips" data-tip="<?php _e( 'First available date, relative to today. I.e. : today + 5 days. Enter 0 for today or leave blank to use the parent product\'s booking options or the global settings.', 'woocommerce-easy-booking-system' ); ?>">[?]</span>
        </label>

        <?php $first_available_date = get_post_meta( $variation_id, '_first_available_date', true ); ?>

        <input type="number" class="input_text" min="0" name="_var_first_available_date[<?php echo $loop; ?>]" id="_var_first_available_date[<?php echo $loop; ?>]" placeholder="<?php _e( 'Same as parent', 'woocommerce' ) ?>" value="<?php if ( isset( $first_available_date ) ) echo esc_attr( $first_available_date ); ?>" />
        
    </p>
    
    <div class="clear"></div>

    <?php do_action('easy_booking_after_variation_booking_options', $loop, $variation ); ?>

</div>
(function($) {

	$(document).ready(function() {

		wceb.dateFormat            = wceb_object.booking_dates,
		wceb.firstDate             = parseInt( wceb_object.first_date ),
		wceb.bookingMin            = parseInt( wceb_object.min ),
		wceb.bookingMax            = ( wceb_object.max === '' ) ? '' : parseInt( wceb_object.max ),
		wceb.bookingDuration       = wceb_object.booking_duration,
		wceb.bookingCustomDuration = parseInt( wceb_object.booking_custom_duration ),
		wceb.priceHtml             = wceb_object.prices_html;

		$pickerWrap   = $('.wceb_picker_wrap');
		$reset_dates  = $pickerWrap.find('a.reset_dates');
		$bookingPrice = $('.booking_price');

		$pickerWrap.hide();

		wceb.pickers.init();
		
		$('.cart').on( 'change', '.quantity input.qty, .wc-grouped-product-add-to-cart-checkbox', function() {

			// Clear session
			wceb.clearBookingSession();

			var $this      = $(this),
				ids        = {},
				quantities = [];

			totalGroupedPrice = 0;
			totalGroupedRegularPrice = 0;

			var children = wceb.get.childrenIds();

			$.each( children, function( id, qty ) {

				var price = wceb_object.product_price[id];
				var regular_price = wceb_object.product_regular_price[id];

				if ( qty > 0 ) {
					totalGroupedPrice += parseFloat( price * qty );

					if ( regular_price !== '' ) {
						totalGroupedRegularPrice += parseFloat( regular_price * qty );
					} else {
						totalGroupedRegularPrice += parseFloat( price * qty );
					}

					ids[id] = qty;
				}

				quantities.push( qty );

			});

			// Get highest quantity selected
			max_qty = Math.max.apply( Math, quantities );

			// Hide date inputs if no quantity is selected
			( max_qty > 0 ) ? $pickerWrap.slideDown( 200 ) : $pickerWrap.hide();
			
			// Update data-booking_price attribute
			$bookingPrice.attr('data-booking_price', totalGroupedPrice );

			// Update total price, maybe including addons
			var formatted_total = wceb.formatPrice( wceb.get.basePrice() );
			var formatted_price = '<span class="woocommerce-Price-amount amount">' + formatted_total + '</span>';

			// If product is on sale
			if ( totalGroupedPrice !== totalGroupedRegularPrice ) {

				// Update data-booking_regular_price attribute
				$bookingPrice.attr('data-booking_regular_price', totalGroupedRegularPrice );

				// Update total regular price, maybe including addons
				var formatted_regular_price = wceb.formatPrice( wceb.get.regularPrice() );
				var formatted_price = '<del><span class="woocommerce-Price-amount amount">' + formatted_regular_price + '</span></del> <ins><span class="woocommerce-Price-amount amount">' + formatted_total + '</span></ins>';
				
			}

			// Update price
			$bookingPrice.html( '<span class="price">' + formatted_price + '</span>' );

			// Store selected ids and quantity
			$reset_dates.attr( 'data-ids', JSON.stringify( ids ) );
			
			wceb.pickers.init();
			wceb.pickers.render( ids );

			// Hide reset dates button if dates are not set
			if ( wceb.dateFormat === 'two' && ! wceb.checkIf.datesAreSet() ) {
				$reset_dates.hide();
			} else if ( wceb.dateFormat === 'one' && ! wceb.checkIf.dateIsSet( 'start' ) ) {
				$reset_dates.hide();
			}

		});

	});

})(jQuery);
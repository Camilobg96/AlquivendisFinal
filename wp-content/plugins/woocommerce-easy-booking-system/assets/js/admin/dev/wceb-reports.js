(function($) {
	$(document).ready(function() {

		var $input = $('.datepicker').pickadate({
			formatSubmit: 'yyyy-mm-dd',
			hiddenName: true
		});

		var picker = $input.pickadate('picker');
		
	});
})(jQuery);
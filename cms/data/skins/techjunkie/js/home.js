(function($) {
	$(function() {
		$('#tab-box').tabs(/*{ fx: { opacity: 'toggle' } }*/);
		$("#slider").easySlider({
			auto: false,
			continuous: true 
		});
	});
})(jQuery);
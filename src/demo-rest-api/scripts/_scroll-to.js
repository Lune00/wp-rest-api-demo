jQuery(document).ready(function($) {

	'use strict';

	$(document).on('click', '[data-type="scroll-to"]', function(e){
		e.preventDefault();
		var target = $(this).attr('href');
		$('html, body').stop().animate({
			scrollTop: $(target).offset().top
		}, 1000);
	});
});

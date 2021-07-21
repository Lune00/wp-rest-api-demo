jQuery(document).ready(function($) {

	'use strict';

	//// HEADER
	$(window).scroll(function () {
		var position = $(window).scrollTop();
		if ( position === 0 ){
			$('#masthead').removeClass('fixed');
		} else {
			$('#masthead').addClass('fixed');
		}
	});

	//// MENU MOBILE
	$(document).on('click', '[data-type="open-menu"]', function(e){
		e.preventDefault();
		$('#mobile-menu').addClass('visible');
	});
	$(document).on('click', '[data-type="close-menu"]', function(e){
		e.preventDefault();
		$('#mobile-menu').removeClass('visible');
	});
	$('.menu-item-has-children > a').click(function(e){
		e.preventDefault();
		if (!$(this).siblings('.sub-menu').hasClass('visible')) {
			$('.sub-menu').removeClass('visible');
			$(this).siblings('.sub-menu').addClass('visible');
		} else {
			$('.sub-menu').removeClass('visible');
		}
	});

});

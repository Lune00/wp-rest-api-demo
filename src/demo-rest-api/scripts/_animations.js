jQuery(document).ready(function($) {

	'use strict';

	var defaultAnimation = {
		opacity: 0,
		duration: 800,
		delay: 500,
		interval: 200,
		easing: 'ease-in-out'
	}
	var fadeIn = {
	    distance: '0',
	    origin: 'bottom'
	};
	fadeIn = groupObjects(fadeIn, defaultAnimation);
	var slideUp = {
	    distance: '60px',
	    origin: 'bottom'
	};
	slideUp = groupObjects(slideUp, defaultAnimation);
	var slideDown = {
	    distance: '60px',
	    origin: 'top'
	};
	slideDown = groupObjects(slideDown, defaultAnimation);
	var slideLeft = {
	    distance: '60px',
	    origin: 'right'
	};
	slideLeft = groupObjects(slideLeft, defaultAnimation);
	var slideRight = {
	    distance: '60px',
	    origin: 'left'
	};
	slideRight = groupObjects(slideRight, defaultAnimation);
	var bounceIn = {
	    distance: '0',
	    origin: 'bottom',
		scale: 0,
		rotate: {
	        x: 0,
	        y: 0,
	        z: -10
	    }
	};
	bounceIn = groupObjects(bounceIn, defaultAnimation);
	bounceIn.duration 	= 500;
	bounceIn.easing 	= 'cubic-bezier(0.480, 0.460, 0.000, 1.650)';
	bounceIn.opacity 	= 1;

	ScrollReveal().reveal('.fade-in', fadeIn);
	ScrollReveal().reveal('.slide-left', slideLeft);
	ScrollReveal().reveal('.slide-right', slideRight);
	ScrollReveal().reveal('.slide-up', slideUp);
	ScrollReveal().reveal('.slide-down', slideDown);
	ScrollReveal().reveal('.bounce-in', bounceIn);

});

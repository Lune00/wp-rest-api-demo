jQuery(document).ready(function($) {

	'use strict';

	//// SVG SPRITES
	$.get(VARS.svg_sprite_url, function(data) {
        var div = document.createElement('div');
        div.classList.add('screen-reader-text');
        div.innerHTML = new XMLSerializer().
        serializeToString(data.documentElement);
        document.body.insertBefore(div, document.body.childNodes[0]);
    });
});

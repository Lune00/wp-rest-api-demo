<?php
function theme_enqueue_styles() {
	$css_version = '1.0';
	wp_enqueue_style('style', get_stylesheet_directory_uri().'/style.css');
	wp_enqueue_style('screen', get_stylesheet_directory_uri().'/styles/screen.min.css', array(), $css_version, 'screen, projection');
    wp_enqueue_style('print', get_stylesheet_directory_uri().'/styles/print.min.css', array(), $css_version, 'print');
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function theme_enqueue_scripts(){
	$js_version = '1.0';
	wp_enqueue_script('lazyload', get_stylesheet_directory_uri() . '/js/lazyload.min.js', array('jquery') );
	wp_enqueue_script('scripts', get_stylesheet_directory_uri().'/js/scripts.min.js', array('jquery'));
	$js_var = array(
		'svg_sprite_url'    => get_template_directory_uri() . '/styles/svg/icons.svg?ver=' . $js_version,
		'site_url' 			=> get_option('siteurl'),
		'theme_url' 		=> get_stylesheet_directory_uri(),
		'ajax_url' 			=> admin_url('admin-ajax.php'),
        'nonce'             => wp_create_nonce('ajax-nonce')
	);
	wp_localize_script('scripts', 'VARS', $js_var);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');

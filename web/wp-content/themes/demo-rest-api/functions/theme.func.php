<?php
function theme_setup() {

	// MENUS
	register_nav_menus(array(
		'header' => 'Menu principal',
        'footer' => 'Menu du footer',
		'mobile' => 'Menu mobile'
	));

	// SUPPORTS
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	));
	add_theme_support('post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	));

	// EDITOR STYLE
	add_theme_support('editor-styles');
	add_editor_style('styles/screen.min.css');
	add_editor_style('style-editor.css');

	// OPTIONS
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page();
		$acf_options_pages = array(
			array('page_title' => 'Général', 'menu_slug' => 'general-settings'),
	        array('page_title' => 'Réseaux sociaux', 'menu_slug' => 'network-settings')
	    );
	    foreach ($acf_options_pages as $option_page) {
	        $menu_title = (!empty($option_page['menu_title'])) ? $option_page['menu_title'] : $option_page['page_title'];
	        acf_add_options_sub_page(array(
	            'page_title' 	=> $option_page['page_title'],
	            'menu_title'	=> $menu_title,
	            'menu_slug' 	=> $option_page['menu_slug'],
	            'parent_slug'   => 'acf-options',
	            'capability'	=> 'manage_options',
	            'redirect'		=> false
	        ));
	    }
	}
}
add_action('after_setup_theme', 'theme_setup');

function wpc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'wpc_mime_types');

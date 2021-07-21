<?php
add_action('init', 'cpt_init');

function cpt_init() {

    register_post_type('Foobar', array(
        'labels' => array(
            'name'                => __('Foobars', 'theme'),
            'singular_name'       => __('Foobar', 'theme'),
            'all_items'           => __('All foobars', 'theme'),
            'new_item'            => __('New foobar', 'theme'),
            'add_new'             => __('Add new foobar', 'theme'),
            'add_new_item'        => __('Add new foobar', 'theme'),
            'edit_item'           => __('Edit foobar', 'theme'),
            'view_item'           => __('View foobar', 'theme'),
            'search_items'        => __('Search', 'theme'),
            'not_found'           => __('No foobar', 'theme'),
            'not_found_in_trash'  => __('No foobar found in trash', 'theme'),
            'menu_name'           => __('foobars', 'theme'),
        ),
        'public'                => true,
        'publicly_queryable'    => true,
        'query_var'             => false,
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_nav_menus'     => true,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'author'),
        'has_archive'           => true,
        'rewrite'               => array('slug' => 'foobar'),
        'menu_icon'             => 'dashicons-list-view',
        'menu_position'         => 20,
        'show_in_rest'          => true,
    ));
}

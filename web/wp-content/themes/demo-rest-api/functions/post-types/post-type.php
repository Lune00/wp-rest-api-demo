<?php
add_action('init', 'cpt_init');

function cpt_init()
{

    /**
     * Register a foobar post type, with REST API support
     *
     * Based on example at: https://codex.wordpress.org/Function_Reference/register_post_type
     */
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
        //Spécifique REST
        //Rendre dispo dans l'api
        'show_in_rest'          => true,
        //url de la ressource dans l'api, par défaut c'est le nom du post-type
        'rest_base' => 'foobar',
        //Controlleur par défaut de WP, ici rendu explicite
        'rest_controller_class' => 'WP_REST_Posts_Controller'
    ));
}

/**
 * Register a genre post type, with REST API support
 *
 * Based on example at: https://codex.wordpress.org/Function_Reference/register_taxonomy
 */
add_action('init', 'my_book_taxonomy', 30);
function my_book_taxonomy()
{

    $labels = array(
        'name'              => _x('Genres', 'taxonomy general name'),
        'singular_name'     => _x('Genre', 'taxonomy singular name'),
        'search_items'      => __('Search Genres'),
        'all_items'         => __('All Genres'),
        'parent_item'       => __('Parent Genre'),
        'parent_item_colon' => __('Parent Genre:'),
        'edit_item'         => __('Edit Genre'),
        'update_item'       => __('Update Genre'),
        'add_new_item'      => __('Add New Genre'),
        'new_item_name'     => __('New Genre Name'),
        'menu_name'         => __('Genre'),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'genre'),
        'show_in_rest'          => true,
        'rest_base'             => 'genre',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
    );

    register_taxonomy('genre', array('foobar'), $args);
}



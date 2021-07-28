<?php


/**
 * Custom Post Type d'une déclaration : contient toutes les données saisies par un user,
 * sera associée à un logement.
 */
add_action('init', 'cpt_declaration_init');

function cpt_declaration_init()
{

    //Quand on recupere les logements on recupere les declarations associées :
    //
    // register_post_type('Declaration', array(
    //     'labels' => array(
    //         'name'                => __('Declarations', 'theme'),
    //         'singular_name'       => __('Declaration', 'theme'),
    //         'all_items'           => __('All declarations', 'theme'),
    //         'new_item'            => __('New Declaration', 'theme'),
    //         'add_new'             => __('Add new Declaration', 'theme'),
    //         'add_new_item'        => __('Add new Declaration', 'theme'),
    //         'edit_item'           => __('Edit Declaration', 'theme'),
    //         'view_item'           => __('View Declaration', 'theme'),
    //         'search_items'        => __('Search', 'theme'),
    //         'not_found'           => __('No Declaration', 'theme'),
    //         'not_found_in_trash'  => __('No Declaration found in trash', 'theme'),
    //         'menu_name'           => __('declarations', 'theme'),
    //     ),
    //     'public'                => true,
    //     'publicly_queryable'    => true,
    //     'query_var'             => false,
    //     'hierarchical'          => false,
    //     'show_ui'               => true,
    //     'show_in_nav_menus'     => true,
    //     'supports'              => array('title', 'author'),
    //     'has_archive'           => true,
    //     'rewrite'               => array('slug' => 'declaration'),
    //     'menu_icon'             => 'dashicons-list-view',
    //     'menu_position'         => 20,
    //     //Spécifique REST
    //     //Rendre dispo dans l'api
    //     'show_in_rest'          => true,
    //     //url de la ressource dans l'api, par défaut c'est le nom du post-type
    //     'rest_base' => 'declaration',
    //     //Controlleur par défaut de WP, ici rendu explicite
    //     'rest_controller_class' => 'WP_REST_Posts_Controller'
    // ));

    register_post_type('Logement', array(
        'labels' => array(
            'name'                => __('Logement', 'theme'),
            'singular_name'       => __('Logement', 'theme'),
            'all_items'           => __('All Logement', 'theme'),
            'new_item'            => __('New Logement', 'theme'),
            'add_new'             => __('Add new Logement', 'theme'),
            'add_new_item'        => __('Add new Logement', 'theme'),
            'edit_item'           => __('Edit Logement', 'theme'),
            'view_item'           => __('View Logement', 'theme'),
            'search_items'        => __('Search', 'theme'),
            'not_found'           => __('No Logement', 'theme'),
            'not_found_in_trash'  => __('No Logement found in trash', 'theme'),
            'menu_name'           => __('logements', 'theme'),
        ),
        'public'                => true,
        'publicly_queryable'    => true,
        'query_var'             => false,
        'hierarchical'          => false,
        'show_ui'               => true,
        'show_in_nav_menus'     => true,
        'supports'              => array('title', 'thumbnail', 'author'),
        'has_archive'           => true,
        'rewrite'               => array('slug' => 'logement'),
        'menu_icon'             => 'dashicons-list-view',
        'menu_position'         => 20,
        //Spécifique REST
        //Rendre dispo dans l'api
        'show_in_rest'          => true,
        //url de la ressource dans l'api, par défaut c'est le nom du post-type
        'rest_base' => 'logement',
        //Controlleur par défaut de WP, ici rendu explicite
        'rest_controller_class' => 'WP_REST_Posts_Controller'
    ));
}

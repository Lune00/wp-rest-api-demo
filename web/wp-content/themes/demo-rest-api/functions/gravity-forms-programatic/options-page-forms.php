<?php

//Ici on va faire une page d'admin qui permet de définir l'id d'un formulaire à utiliser. Ces ids seront servis par une route custom ensuite pour le client

//Le client a besoin de l'id du formulaire vers lequel il post

add_action('acf/init', 'my_acf_op_init');
function my_acf_op_init() {

    // Check function exists.
    if( function_exists('acf_add_options_page') ) {
        // Register options page.
        $option_page = acf_add_options_page(array(
            'page_title'    => __('Formulaires'),
            'menu_title'    => __('Formulaires'),
            'menu_slug'     => 'theme-forms',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));
    }
}
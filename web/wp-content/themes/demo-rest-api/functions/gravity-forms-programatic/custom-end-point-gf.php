<?php

/**
 * On manipule les forms de GF derrière des endpoints custom, et GFAPI. Cela nous permet d'ajouter de la logique custom et des nonces
 */

/**
 * On laisse le soin a GF de valider les inputs
 */

/**
 * Pour le POST on peut imposer un schéma d'entrée : on veut un champ input qui contient tous les inputs et un champ nonce qui contient le nonce. On va pas mettre de schéma de retour pour le moment
 */

add_action('rest_api_init', function () {

    register_rest_route('myplugin/v1', 'form/(?P<form_id>\d+)', array(

        /**
         * Post un form
         */
        array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'post_my_gf_form',
            'permission_callback' => function () {
                return user_can(wp_get_current_user(), 'edit_posts');
            },
            'args' => array(
                'nonce' => array(
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Nonce anti-CSRF',
                    'validate_callback' => function ($param, $request, $key) {
                    }
                ),
            )
        ),
        /**
         * Envoyer le formulaire au client
         */
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'get_my_gf_form',
            'permission_callback' => function () {
                return user_can(wp_get_current_user(), 'edit_posts');
            }
        ),

    ));
});



function get_my_gf_form(WP_REST_Request $request)
{

    //Si on va chercher directement dans la requete parmi les paramètres
    //mergés, on ne sait pas quelle valeur on prend pour form_id
    //Si c'est dans une path variable il vaut mieux aller la chercher
    //explicitement ici
    $path_parameter = $request->get_url_params();
    $id_form = $path_parameter['form_id'];

    //On récupere le form
    $target_form = GFAPI::get_form($id_form);

    if (empty($target_form))
        return new WP_Error(
            'invalid_data',
            __('Ressource introuvable'),
            array('status' => 400)
        );

    //On créé un nonce
    $nonce = wp_create_nonce('get_my_gf_form_' . $id_form);

    $response = array(
        'form' => $target_form,
        'nonce' => $nonce
    );

    return rest_ensure_response($response);
}



function post_my_gf_form(WP_REST_Request $request)
{
    $user = wp_get_current_user();
    $path_parameter = $request->get_url_params();
    $form = $path_parameter['form_id'];

    //On vérifie le nonce
    $nonce = $request['nonce'];
    if (false === wp_verify_nonce($nonce, 'get_my_gf_form_' . $form)) {
        return new WP_Error(
            'invalid_data',
            __('I do not trust you !'),
            array('status' => 400)
        );
    }

    //Check que le form existe (On peut laisser ça à GF je pense)
    $target_form = GFAPI::get_form($form);

    if (empty($target_form))
        return new WP_Error(
            'invalid_data',
            __('Ressource introuvable'),
            array('status' => 400)
        );

    //Retrouve les paramètres du form dans le body
    //On récupères les inputs passés dans le corps de la méthode sous format JSON
    $form_inputs = $request->get_json_params();

    //On sanitize à la main car Gravity Forms a l'air de plus le faire
    //dans sa derniere version
    $clean = array_map(function ($data) {
        return sanitize_text_field($data);
    }, $form_inputs);

    //On soumet le form
    //WARNING : Gravity Forms ne sanitize pas les tags !!! BUG??
    $submission = GFAPI::submit_form($form, $clean);

    return rest_ensure_response($submission);
}

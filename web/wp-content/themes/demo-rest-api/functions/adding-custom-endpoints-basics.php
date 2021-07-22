<?php

//Source : https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/


/**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest, * or message if none.
 */
function my_awesome_func(WP_REST_Request $request): string
{

    var_dump($request);

    // $posts = get_posts(array(
    //     'author' => $request['id'],
    // ));

    // if (empty($posts)) {
    //     return 'No posts for this author';
    // }

    // return $posts[0]->post_title;
}


/**
 * Callback pour montrer les différents moyens d'acceder aux paramètres
 * envoyés avec la requete, et voir comment ils sont classifiés
 */
function test_params( WP_REST_Request $request ) {

    $params = array(
        //On peut acceder directement au paramètre dans l'objet WP_REST_REQUEST
        //S'il n'existe pas renvoie null
        'direct_access' => $request['id'],
        //Tous les paramètres sont mergés par défaut
        'merged_params' => $request->get_params(),
        //Paramètres passé dans l'url et matchés par l'expression réguliere
        'get_url_params' => $request->get_url_params(),
        //Paramètres passés en argument dans la requete (derriere le ?)
        'get_query_params' => $request->get_query_params(),
        'get_body_params' => $request->get_body_params(),
        'json_params' => $request->get_json_params(),
        'default_params' => $request->get_default_params(),
        'file_params' => $request->get_file_params()
    );
    return $params;
  }

/**
 * Register custom routes
 */
add_action('rest_api_init', function () {

    register_rest_route('myplugin/v1', '/author/(?P<id>\d+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'my_awesome_func',
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key){
                    return is_numeric($param) && intval($param) !== 0;
                }
            )
        )
    ));

    register_rest_route('myplugin/v1', '/testparams/(?P<id>\d+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'test_params'
    ));
});

<?php

//Source : https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/


/**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest, * or message if none.
 */
function my_awesome_func(WP_REST_Request $request)
{

    // var_dump($request);

    $posts = get_posts(array(
        'author' => $request['id'],
    ));

    if (empty($posts))
        return new WP_Error('no_author', "Cet auteur n'a pas écrit d'article",  array('status' => 404));


    return $posts[0]->post_title . '. Want to buy that article for ' . $request['price'] . ' dollars ?';
}


/**
 * Callback pour montrer les différents moyens d'acceder aux paramètres
 * envoyés avec la requete, et voir comment ils sont classifiés
 */
function test_params(WP_REST_Request $request)
{

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
        'file_params' => $request->get_file_params(),
        'current_user' => wp_get_current_user()
    );
    return rest_ensure_response($params);
}



function say_hello(WP_REST_Request $request)
{
    $data = 'Hi ' . $request['name'];
    return rest_ensure_response($data);
}


/**
 * Register custom routes
 */
add_action('rest_api_init', function () {

    register_rest_route('myplugin/v1', '/author/(?P<id>\d+)', array(

        'methods' => WP_REST_Server::READABLE,

        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'my_awesome_func',

        //Dire explicitement qu'une ressource est publique (specs WP, indiqué sinon dans le header de la réponse x-wp-doingitwrong)
        'permission_callback' => '__return_true',

        'args' => array(
            'id' => array(
                'required' => true,
                'type' => 'integer',
                'description' => 'Id of an author',
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param) && intval($param) !== 0;
                }
            ),
            'price' => array(
                'required' => true,
                'type' => 'integer',
                'description' => 'Price of something',
                'minimum' => 10,
                'maximum' => 100
            )
        )
    ));

    register_rest_route('myplugin/v1', '/testparams/(?P<id>\d+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'test_params',
        //Quand on demande la permission, on demande l'authentification
        //implicitement. Une façon minimale de demander l'authentification
        //seulement.
        'permission_callback' => function () {
            return user_can(get_current_user(), 'edit_posts');
        }
    ));

    register_rest_route('myplugin/v1', '/sayhello/(?P<name>\S+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'say_hello',
        'permission_callback' => function () {
            //Seul l'utilisateur avec id 1 peut consommer ce endpoint
            return 1 === get_current_user_id();
        },
        'args' => array(
            'name' => array(
                'sanitize_callback' => function ($value, $request, $param) {
                    // return sanitize_text_field($value);
                    return str_replace('a', '', $value);
                },
                'validate_callback' => function ($param, $request, $key) {
                    return strlen($param) <= 5;
                },
                'description' => 'Le nom. Doit faire moins de 5 caractères'
            )
        )
    ));

    register_rest_route('jwt-auth/v1', '/test-auth', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => function (WP_REST_Request $request) {
            // $user_name = _(wp_get_current_user())->user_nicename;
            // rest_ensure_response('Welcome ' . $user_name . ', I know you !');
            rest_ensure_response('hello : )');
        }
        //Ici on n'a pas mis de permission_callback, donc on ne demande pas explicitement d'authentification. Mais comme on est sur le namespace du plugin JWT tous les endpoints demandent à voir le Token dans le header. S'il n'est pas présent, ou invalide, la requete est rejetée
    ));
});


function test_auth(WP_REST_Request $request)
{
    $user_name = _(wp_get_current_user())->user_nicename;
    rest_ensure_response('Welcome ' . $user_name . ', I know you !');
}

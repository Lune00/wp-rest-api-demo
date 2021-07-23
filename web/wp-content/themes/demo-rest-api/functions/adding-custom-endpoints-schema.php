<?php

/**
 * On crée des nouveaux endpoints mais avec des schémas de données cette fois
 * Source : https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
 */

// Register our routes.
function register_my_other_custom_route()
{
    register_rest_route(
        'myplugin/v1',
        '/am-i-old/',
        //On enregistre plusieurs routes pour mettre le schema,
        //le schema equivaut (implicite) à une route pour la méthode OPTIONS
        array(
            array(
                'methods'  =>  WP_REST_Server::READABLE,
                'callback' => 'answer_you',
                // Register our schema callback.
            ),
            'schema' => 'am_i_old_schema',
        )
    );
}

add_action('rest_api_init', 'register_my_other_custom_route');

function answer_you(WP_REST_Request $request)
{
    return rest_ensure_response('Hi !');
}


function am_i_old_schema()
{
    $schema = array(
        // This tells the spec of JSON Schema we are using which is draft 4.
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        // The title property marks the identity of the resource.
        'title'                => 'comment',
        'type'                 => 'object',
        // In JSON Schema you can specify object properties in the properties attribute.
        'properties'           => array(
            'name' => array(
                'description'  => esc_html__('Votre nom', 'my-textdomain'),
                'type' => 'string'
            ),
            'age' => array(
                'description'  => esc_html__('Votre âge', 'my-textdomain'),
                'type' => 'integer',
                'minimum' => 1,
            )
        ),
    );

    return $schema;
}

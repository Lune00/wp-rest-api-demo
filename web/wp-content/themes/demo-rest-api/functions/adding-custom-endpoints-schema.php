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
                //Schema pour les arguments (body POST ou url GET)
                //Si on requete la route avec la méthode OPTIONS
                //on voit les arguements listés sous 'args'
                //Dit ce que le endpoint accepte en entrée (entrée)
                'args' => array(
                    'name' => array(
                        'sanitize_callback' => function ($value, WP_REST_Request $request, $param) {
                            return $value;
                        },
                        'validate_callback' => function ($value, $request, $param) {
                            //Si on veut valider le corps d'un body auprès du schéma
                            //$params = $request->get_json_params();
                            // return rest_validate_value_from_schema($params, am_i_old_schema(),'name');;
                            return strlen($value) < 5;
                        },
                        'description' => 'Argument Schema',
                        'required' => true
                    ),
                    'age' => array(
                        'type' => 'integer',
                        'required' => true
                    )
                )
            ),
            //Schema pour Discovery (méthode OPTIONS)
            //Dit ce que le endpoint Retourne (sortie)
            'schema' => 'am_i_old_schema',
        )
    );
}

add_action('rest_api_init', 'register_my_other_custom_route');

function answer_you(WP_REST_Request $request)
{
    $name = $request['name'];
    $age = $request['age'];

    //On map la réponse au format attendu
    $response = array(
        'answer' => "Hi {$name} ! You are {$age} years old !",
        'age' => $age
    );

    //On valide la réponse dans la callback avec le schéma JSON
    $schema = am_i_old_schema();
    //Je valide ma réponse avec le schéma
    $valid_response = rest_validate_value_from_schema($response, $schema, '');
    write_log($valid_response);

    //Si une erreur je renvoie l'erreur
    if (is_wp_error($valid_response))
        return $valid_response;

    //Je renvoie la réponse
    return rest_ensure_response($response);
}


function am_i_old_schema()
{
    $schema = array(
        // This tells the spec of JSON Schema we are using which is draft 4.
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        // The title property marks the identity of the resource.
        'title'                => 'question',
        'type'                 => 'object',
        // In JSON Schema you can specify object properties in the properties attribute.
        'properties'           => array(
            'answer' => array(
                'description'  => esc_html__('La réponse à votre question', 'my-textdomain'),
                'type' => 'string',
                'required' => true,
                'maxLength' => 274
            ),
            'age' => array(
                'description'  => esc_html__('L\'âge que vous avez donné', 'my-textdomain'),
                'type' => 'integer',
                'minimum' => 18,
                'maximum' => 2000,
                'required' => true
            )
        ),
    );

    return $schema;
}

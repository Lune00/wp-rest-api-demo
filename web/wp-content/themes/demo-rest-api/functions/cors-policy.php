<?php

/**
 * Customisation de la CORS Policy
 */


/**
 * Sources :
 * https://developer.wordpress.org/rest-api/frequently-asked-questions/#require-authentication-for-all-requests
 * https://linguinecode.com/post/enable-wordpress-rest-api-cors
 */


/**
 * La définition de notre CORS Policy
 */
function cors_policy($value)
{
    //On récupere l'origin de la requete
    $origin = get_http_origin();

    error_log('requête depuis l\'origin : ' . $origin);

    //Les origines acceptées
    $white_list = array(
        'http://localhost:8000',
        'http://localhost:8002'
    );

    if ($origin && in_array($origin, $white_list)) {
        error_log('origin ' . $origin . ' acceptée');
        header('Access-Control-Allow-Origin: ' . esc_url_raw($origin));
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Credentials: true');
    }

    return $value;
}


/**
 * On retire la cors policy par défaut de WP sur son API REST et on hook la notre, si on est en env de prod
 */
add_action('rest_api_init', function () {

    //On check si on est dans un env de production.
    //Si on est pas sur un env de production on ne fait rien. On laisse la CORS policy par défaut qui autorise toutes les origines

    //L'environnement est défini dans le wp-config.php
    if (ENVIRONMENT === 'production') {
        error_log('apply restictrive and custom CORS policy for the rest api');
        remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
        add_filter('rest_pre_serve_request', 'cors_policy');
    }
}, 15);

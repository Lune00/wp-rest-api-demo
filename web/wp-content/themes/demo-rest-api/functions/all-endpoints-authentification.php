<?php

/**
 * Ici on met en place une authentification sur tous les endpoints (natifs ou custom)
 * En gros, s'il n'y a pas d'erreur on envoie une erreur custom où on test si y'a un user logged in
 */

function authentificate_all_endpoints($result)
{
    // If a previous authentication check was applied,
    // pass that result along without modification. (Déjà une erreur)
    if (true === $result || is_wp_error($result)) {
        return $result;
    }

    // Preflight requests (OPTIONS) should not require autentication:
    // https://stackoverflow.com/questions/40722700/add-authentication-to-options-request
    // https://fetch.spec.whatwg.org/#http-cors-protocol
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        return $result;
    }

    //Si on est pas en prod on lève l'authentification (juste pour des tests)
    if(ENVIRONMENT !== 'production'){
        return $result;
    }

    //Check si le endpoint demandé est sur la whitelist. La whitelist est une liste
    //de endpoint ne nécessitant pas l'authentification
    if (is_unauthentificated_endpoint()) {
        error_log('this endpoint is public (in a whitelist)');
        //Si le endpoint est publique, on ne test pas si l'authentification a été faite (token ou non)
        return $result;
    }

    // No authentication has been performed yet (par le plugin JWT Token du coup)
    // Return an error if user is not logged in.
    if (!is_user_logged_in()) {
        error_log('unauthentificated request, rejected.');
        return new WP_Error(
            'rest_not_logged_in',
            __('You are not currently logged in. No way'),
            array('status' => 401)
        );
    }

    // Our custom authentication check should have no effect
    // on logged-in requests
    return $result;
}
add_filter('rest_authentication_errors', 'authentificate_all_endpoints');


/**
 * Utilise une whitelist définissant des enpoints ne nécessitant pas l'authentification.
 * Renvoie vrai si le endpoint(URI+method) est dans la whitelist, faux sinon
 * Inspiré du plugin api-bearer https://wordpress.org/plugins/api-bearer-auth/#whitelist%20unauthenticated%20urls
 */
function is_unauthentificated_endpoint()
{
    //Notre whitelist par défaut
    define('UNAUTHENTIFICATED_ENDPOINTS_DEFAULT', array(
        //Le endpoint pour créer un token
        WP_REST_Server::CREATABLE => array(
            '/wp-json/jwt-auth/v1/token'
        ),
        WP_REST_Server::READABLE => array(
            '/wp-json',
            '/wp-json/wp/v2',
            '/wp-json/wp/v2/posts',
            '/wp-json/wp/v2/posts/[0-9]+',
            '/wp-json/myplugin/v1/author/[0-9]+',
            //Si on veut gerer des urls dynamiques ajouter une regex sur la route concernée
            //Ici seulement des caracteres alphanumériques
            '/wp-json/myplugin/v1/sayhello/[a-zA-Z0-9]+'
        )
    ));

    //On récupere l'url demandée (sans les paramètres de l'url)
    write_log($_SERVER['REQUEST_URI']);

    $current_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');

    error_log('endpoint demandé: ' . 'uri: ' . $current_url . ' method: ' .  $_SERVER['REQUEST_METHOD']);

    $site_url = get_site_url();

    error_log('site_url (nom domaine) : ' . $site_url);

    /**
     * Hook pour ajouter des endpoints a la liste par défaut si besoin
     * Add URLs that should be avialble to unauthenticated users.
     * Specify only the part after the site url, e.g. /wp-json/wp/v2/users
     * Each URL will be prepended by the value of get_site_url()
     * And each resulting URL will be put in between ^ and $ regular expression signs.
     */
    $custom_urls_whitelist = array();

    /**
     * On filtre les endpoints pour récupérer les urls associées à la méthode de la requete en cours
     */
    $custom_urls_whitelist = apply_filters('jwt_auth_unauthenticated_endpoints_filter', $custom_urls_whitelist, $_SERVER['REQUEST_METHOD']);

    $default_urls_whitelist = array();

    if (!empty(UNAUTHENTIFICATED_ENDPOINTS_DEFAULT[$_SERVER['REQUEST_METHOD']])) {
        $default_urls_whitelist = UNAUTHENTIFICATED_ENDPOINTS_DEFAULT[$_SERVER['REQUEST_METHOD']];
    }

    //On merge la whitelist custom avec la whitelist par defaut
    $urls_whitelist = array_merge($custom_urls_whitelist, $default_urls_whitelist);

    write_log('La whitelist des urls sur la méthode ' . $_SERVER['REQUEST_METHOD']);
    write_log($urls_whitelist);

    //Si l'url demandée match une url dans la whitelist, on a une url (et un endpoint car on a filtré en amont sur la méthode) non authentifié (publique)
    foreach ($urls_whitelist as $url) {

        //Pour le moment c'est strict (exact match), pas de gestion des urls dynamiques
        if (preg_match("[^" . $site_url . $url . "$]", $current_url)) {
            return true;
        }
    }

    write_log('url ' . $current_url . ' demande authentification');

    //Cette url n'est pas dans la whitelist, on doit demander l'authentification
    return false;
}

/**
 * Utiliser le hook pour ajouter des endpoints custom a la whitelist
 */
add_filter('jwt_auth_unauthenticated_endpoints_filter', 'add_unauthentificated_endpoints_to_whitelist', 10, 2);
function add_unauthentificated_endpoints_to_whitelist(array $custom_urls, string $request_method)
{
    switch ($request_method) {
        case WP_REST_Server::CREATABLE:
            $custom_urls[] = '/wp-json/myplugin/v1/something/?';
            break;
        case WP_REST_Server::READABLE:
            $custom_urls[] = '/wp-json/myplugin/v1/something/other/?';
            break;
    }
    return $custom_urls;
}

/**
 * Masquer l'information exposée sur {domain}/wp-json 
 * (schémas, endpoints, authentification)
 */
function my_site_rest_index($response)
{
    if (ENVIRONMENT === 'production')
        return array();

    return $response;
}
add_filter('rest_index', 'my_site_rest_index');

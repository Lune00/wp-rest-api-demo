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

    // No authentication has been performed yet (par le plugin JWT Token du coup)
    // Return an error if user is not logged in.
    if (!is_user_logged_in()) {
        error_log('unauthentificated request');
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
// add_filter('rest_authentication_errors', 'authentificate_all_endpoints');

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

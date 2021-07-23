<?php

/**
 * Ici on met en place une authentification sur tous les endpoints (natifs ou custom)
 */

// add_filter( 'rest_authentication_errors', function( $result ) {
//     // If a previous authentication check was applied,
//     // pass that result along without modification.
//     if ( true === $result || is_wp_error( $result ) ) {
//         return $result;
//     }

//     // No authentication has been performed yet.
//     // Return an error if user is not logged in.
//     if ( ! is_user_logged_in() ) {
//         error_log('unauthentificated request');
//         return new WP_Error(
//             'rest_not_logged_in',
//             __( 'You are not currently logged in. No way' ),
//             array( 'status' => 401 )
//         );
//     }

//     // Our custom authentication check should have no effect
//     // on logged-in requests
//     return $result;
// });


function chuck_disable_rest_endpoints($access)
{
    if (!is_user_logged_in()) {
        return new WP_Error('rest_cannot_access', __('Only logged users are able to call REST API.', 'disable-json-api'), array('status' => rest_authorization_required_code()));
    }
    return $access;
}
add_filter('rest_authentication_errors', 'chuck_disable_rest_endpoints');


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

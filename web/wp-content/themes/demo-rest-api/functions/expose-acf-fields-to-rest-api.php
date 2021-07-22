<?php

/**
 * Filters the post data (custom post type here 'foobar') for a REST API response to
 * add ACF field values. 
 * Remonte toutes les métas. S'applique à tous les endpoints (global)
 */
function add_acf_all(WP_REST_Response $response, WP_Post $post, WP_REST_Request $request)
{
    if (!function_exists('get_fields'))
        return $response;

    if (!isset($post))
        return $response;

    /**
     * D'apres le code source acf get_fields est tres gourmande
     * en ressources (PHP memory et requetes SQL). Peut être manuellement
     * aller chercher chaque field plutot
     */
    $fields = get_fields($post->ID);
    $response->data['acf_all'] = $fields;
    return $response;
}
add_filter('rest_prepare_foobar', 'add_acf_all', 3, 10);



/**
 * Filters the post data (custom post type here 'foobar') for a REST API response to
 * add ACF field values. 
 * Expose une liste définie par l'utilisateur des métas ACF.
 * Si un champ ACF n'existe pas sur le POST aucun champ n'est renvoyé
 */
function add_acf_restricted(WP_REST_Response $response, WP_Post $post, WP_REST_Request $request)
{
    if (!isset($post))
        return $response;

    try {
        $response->data['acf'] = acf_values_of_post(array(
            'champ_custom_vraifaux'
        ), $post->ID);

    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    return $response;
}
// add_filter('rest_prepare_foobar', 'add_acf_restricted', 3, 10);

/**
 * Retourne un tableau contenant les valeurs de champs ACF
 * demandées d'un post.
 * @throws Error : Si le nom d'un champ n'existe pas comme méta du post, SI la fonction get_field d'ACF n'est pas trouvée
 */
function acf_values_of_post(array $fields_name, int $post_id)
{
    $values = array();

    if (!function_exists('get_field'))
        throw new Exception('La fonction get_field n\'existe pas. Installer ACF');

    foreach ($fields_name as $field_name) {

        $value = get_field($field_name, $post_id);

        if (!$value)
            throw new Exception('WP REST API : Le champ ' . $field_name . ' n\'existe pas sur le post ' . $post_id . ' et ne peux être exposé par l\'API');

        $values[$field_name] = $value;
    }
    return $values;
}


// TODO : Faire la même chose sur les Taxonomies, avec une Custom Taxo
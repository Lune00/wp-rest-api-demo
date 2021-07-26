<?php

/**
 * Ici on veut profiter du Controller par défaut de Wordpress sur le CRUD de notre custom post type foobar. Mais on voudrait ajouter des champs au schéma défini par défaut par WP. Par exemple, on voudrait exposer dans le schéma des champs ACF. 
 * 
 * On va utiliser la fonction register_rest_field. A voir comment ça se lie avec les champs ACF que jajoute en plus à la réponse avec rest_prepare_foobar. Si ca se trouve je n'en aurai plus besoin ? La valeur sera auto exportée dans le schéma. Ou alors il faudra le faire quand même manuellement. 
 * 
 * Source : https://stackoverflow.com/questions/52709167/wordpress-rest-api-how-to-get-post-schema
 * https://developer.wordpress.org/reference/functions/register_rest_field/
 */


add_action('rest_api_init', 'foobar_extend_schema');




// 1) Déclarer les champs ACF que l'on veut ajouter au schéma
define('METAS', array(
    'custom_field_text',
    'champ_custom_vraifaux'
));

// 2) Ecrire une fonction qui leve une Exception si un champ de METAS n'existe pas ds ACF

// 3) Si ok, register_rest_field sur tous les noms.

//Ou sinon définir un schéma, et valider le schéma (les noms des champs)

function foobar_extend_schema()
{
    register_rest_field('foobar', 'my_custom_metas', array(
        'get_callback' => function ($post_arr) {
            return array(
                'custom_field_text' => get_field('custom_field_text', $post_arr['id']),
                'champ_custom_vraifaux' => get_field('champ_custom_vraifaux', $post_arr['id']),
            );
        },
        'update_callback' => function ($values, $post, $fieldname) {
            /*
             * function(values, post, fieldname)
             * values : tableau cle/valeur des champs
             * post : l'objet post sur lequel on update les metas
             * fieldname :  le nom du champ enregistré avec register_rest_field
             */
            $successfull_updates = array();

            //Select only unchanged values
            $values_to_update = array_filter($values, function($val, $field) use($post){
                $current_value = get_field($field, $post->ID, false);
                write_log('field: ' . $field . ' value(post): ' . $val . ' current : ' . $current_value);
                return $current_value !== $val;
            },ARRAY_FILTER_USE_BOTH);

            foreach ($values_to_update as $field_name => $value) {
                //Définir une action custom sur chaque nom de champ
                //do_action('update_field{field_name}) si on veut faire
                //une action custom pour un champ

                //Simule une erreur
                // if($field_name === 'custom_field_text'){
                //     $updates[$field_name]=false;
                //     continue;
                // }
                $successfull_updates[$field_name] = update_field($field_name, $value, $post->ID);
            }

            $errors = array_filter($successfull_updates, fn ($update) => false === $update);

            if (empty($errors))
                return true;

            $fields_not_updated = implode(', ', array_keys($errors));

            return new WP_Error(
                'rest_foobar_incomplete_update',
                __("Failed to update the following fields: {$fields_not_updated}"),
                array('status' => 500)
            );
        },
        //J'enregistre tous mes champs ACFS ici (sous un objet)

        'schema' => array(
            'description' => __('Champs customs', 'demo'),
            'type' => 'object',
            'properties' => array(
                'custom_field_text' => array(
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Qui êtes vous ?'
                ),
                'champ_custom_vraifaux' => array(
                    'type' => 'string',
                    //Valider parmi un enum de valeurs
                    'enum' => array(
                        'red',
                        'blue'
                    ),
                    'required' => true
                )
            )
        )
    ));
}

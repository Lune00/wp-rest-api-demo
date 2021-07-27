<?php

require 'tunnel-models.php';

/**
 * Helpers
 */
function help_has_unique_field_with_input_name(string $input_name, $form): bool
{
    if (!isset($form['fields']))
        return false;

    $match = array_filter($form['fields'], function ($field) use ($input_name) {
        return $field->inputName === $input_name;
    });

    return  is_array($match) && 1 === count($match);
}

function help_get_field(string $input_name, array $form): ?GF_Field
{
    if (!help_has_unique_field_with_input_name($input_name, $form))
        return null;

    $matching_fields = array_filter($form['fields'], function ($field) use ($input_name) {
        return $field->inputName === $input_name;
    });

    if (empty($matching_fields))
        return null;

    return array_shift($matching_fields);
}


function value_by_field_input_name(array $entry, array $form, $input_name, string $suffix = '')
{
    $field = help_get_field($input_name, $form);

    if (!isset($field))
        return '';

    $field_id = empty($suffix) && $suffix !== '0' ? $field['id'] : $field['id'] . '.' . $suffix;

    return  $entry[$field_id] ?? '';
}


add_action('gform_after_submission', 'set_post_content', 10, 2);
function set_post_content($entry, $form)
{

    //On retrouve bien notre valeur par nom dans l'entrée
    $field_value = value_by_field_input_name($entry, $form, 'foobar');

    //Recupere la liste des entrées du form et les ranger dans une liste avec la clé (input_name) valeur(input user)
    $values = array();

    foreach ($form['fields'] as $field) {
        if (isset($field['inputName']) && $field['inputName'] !== '')
            $values[$field['inputName']] = value_by_field_input_name($entry, $form, $field['inputName']);
    }

    //On filtre toutes les clefs liées au tunnel

    //On appelera la liste de nos modeles
    //La liste sera chargée à partir d'un fichier JSON à la volée
    $equipments = array(
        new ElectricalEquipment('enceinte_connectee')
    );

    $tunnel_keys = array_map(function ($eq) {
        return $eq->id;
    }, $equipments);

    $tunnel_data = array_filter($values, function ($key) use ($tunnel_keys) {
        return in_array($key, $tunnel_keys);
    }, ARRAY_FILTER_USE_KEY);


    //On récupere l'id du logement dans le formulaire
    $id_logement = 36;

    //Si :
    // - un id de déclaration : si la declaration est perimée on refuse de mettre à jour la déclaration, il faut en créer une nouvelle
    //                          si elle n'est pas périmée on met a jour directement la declaration
    // - pas d'id de déclaration : premiere soumission, on cree une déclaration et on l'attache au logement 

    $id_declaration = null;

    if (!isset($id_declaration)) {

        //On cree une nouvelle déclaration, on attache les métas
        $id_created_declaration = wp_insert_post(array(
            'post_title' => 'Declaration ' . date('Y-m-d'),
            'post_type' => 'declaration',
            'meta_input' => array(
                'tunnel_data' => $tunnel_data
            )
        ));

        //Les metas sont sauvées
        // dump(get_post_meta($id, 'tunnel_data', true));

        //On attache le post a l'hebergement
        add_post_meta($id_logement, 'declaration', $id_created_declaration);
    } else {

        //Todo
        dump('Mettre à jour la déclaration liée au logement');
    }



    dump($values);
}


/**
 * Pour afficher les metas d'une déclaration, proposer aussi un lien vers le logement associé
 */

add_action('add_meta_boxes', 'initialisation_metaboxes');
function initialisation_metaboxes()
{
    add_meta_box('id_ma_meta', 'Ma metabox', 'display_metas_on_logement', 'declaration', 'normal', 'high');
}

function display_metas_on_logement($post)
{

    $id_declaration = get_post_meta($post->ID, 'declaration');
    dump($id_declaration);
    $tunnel_data = get_post_meta($id_declaration, 'tunnel_data', true);
    dump($tunnel_data);
    echo '<p>Voici les métas du tunnel</p>';
    foreach ($tunnel_data as $name => $value) {
        echo "<div>{$name} : {$value}</div>";
    }
}

<?php

require 'tunnel-models.php';


/**
 * Des tests avec soumission du formulaire directement sur le site
 */

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

    //On récupere l'id du logement dans le formulaire
    $id_logement = 36;

    //Premiere création
    $id_declaration = null;

    if (!isset($id_declaration)) {

        //On attache l'entry a l'hebergement
        update_post_meta($id_logement, 'entry', $entry['id']);
        //On attache la ref du logement à l'entrée
        gform_add_meta($entry['id'], 'logement', $id_logement, $form['id']);
    } else {

        //Todo
        dump('Mettre à jour la déclaration liée au logement');
    }

    dump($entry);
}


/**
 * Pour afficher les metas d'une déclaration, proposer aussi un lien vers le logement associé
 */

add_action('add_meta_boxes', 'initialisation_metaboxes');
function initialisation_metaboxes()
{
    add_meta_box('id_ma_meta', 'Ma metabox', 'display_metas_on_logement', 'logement', 'normal', 'low');
}


function extract_tunnel_meta_from_entry($entry)
{

    $form = GFAPI::get_form($entry['form_id']);

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

    return $tunnel_data;
}

/**
 * Affiche métas sur un post (meta du tunnel)
 */
function display_metas_on_logement($post)
{
    $entry_id = get_post_meta($post->ID, 'entry', true);
    $entry = GFAPI::get_entry($entry_id);
    $tunnel_data = extract_tunnel_meta_from_entry($entry);
    echo '<p>Voici les métas du tunnel stockés dans l entry</p>';
    foreach ($tunnel_data as $name => $value) {
        echo "<div>{$name} : {$value}</div>";
    }
}

/**
 * Hook pour ajouter une metabox sur une entrée
 */
add_filter( 'gform_entry_detail_meta_boxes', 'register_ur_meta_box', 10, 3 );
function register_ur_meta_box($metabox, $entry, $form){
    if ( ! isset( $meta_boxes['logement'] ) ) {
        $meta_boxes['logement'] = array(
            'title'         => esc_html__( 'Logement Details', 'gravityforms' ),
            'callback'      => 'display_link_to_logement',
            'context'       => 'side',
            'callback_args' => array( $entry, $form ),
        );
    }
 
    return $meta_boxes;
}

/**
 * Afficher les métas sur une entrée (lien vers logement etc...)
 */
function display_link_to_logement($args){
    $form  = $args['form'];
    $entry = $args['entry'];
    $logement_id = gform_get_meta($entry['id'],'logement');
    $logement = get_post($logement_id);
    $permalink = get_edit_post_link($logement_id);
    $html   = '<p>Logement : ' . $logement->post_title .' <a href="' . $permalink .'">Voir le logement</a> <p>';
    echo $html;
}
<?php

/**
 * Création d'un formulaire de manière programmatique (sans l'éditeur)
 * en important un json qui définira soit les fieds soit tout le form
 */

/**
 * Build a GF Form Meta with a title from a json template
 */
function build_gf_form_meta(string $title, string $ressource)
{

    //Gravity Forms doit être installé
    if (!class_exists('GF_Fields'))
        return array();

    $form = array();

    //Servira d'identifiant
    $form['title'] = $title;

    //Load the fields from a json file
    $json_fields = file_get_contents(get_template_directory() . '/functions/gravity-forms-programatic/' . $ressource);

    //On map le JSON a un array
    $entities = json_decode($json_fields, true);

    if (!isset($entities))
        throw new Exception('Impossible de décoder le JSON ' . $ressource);

    //On récupere les champs
    $fields = array_map(fn ($entity) => $entity['field'], $entities);

    //Valide les champs fields
    if (!valid_fields($fields))
        throw new Exception('Les champs ne sont pas tous valides');


    //On fabrique des champs GF à partir des champs renseignés dans le JSON
    foreach ($entities as $entity) {

        $GF_field = array_merge(array(
            'inputName' => $entity['id'],
            'label' => $entity['label'],
        ), $entity['field']);

        $form['fields'][] = GF_Fields::create($GF_field);
    }

    return $form;
}


/**
 * Valide les champs
 */
function valid_fields(array $fields)
{
    return true;
}

/**
 * Retourne un formulaire par son nom
 */
function get_form_by_name(string $title)
{
    $forms = GFAPI::get_forms();
    foreach ($forms as $form) {
        if ($title === $form['title'])
            return $form;
    }
    return null;
}


function create_form($form)
{
    if (!class_exists('GFAPI'))
        return false;

    $existing_form = get_form_by_name($form['title']);

    if (!isset($existing_form))
        return GFAPI::add_form($form);

    return false;
}

$form = build_gf_form_meta('Équipements Électriques', 'electrical-equipments.json');
$result = create_form($form);

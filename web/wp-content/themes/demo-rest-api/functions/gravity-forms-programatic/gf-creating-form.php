<?php

/**
 * Création d'un formulaire de manière programmatique (sans l'éditeur)
 * en important un json qui définira soit les fieds soit tout le form
 */


function box_get_google_api_client_credentials()
{
    return ABSPATH . 'wp-content/themes/box-v2/inc/library/google-api-client/client-google-api-credentials.json';
}

define('THE_BIG_FORM', 'THE_BIG_FORM');


function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}


/**
 * Build a GF Form Meta with a title from a json template
 */
function build_gf_form_meta(string $title, string $ressource)
{
    $form = array();
    //Servira d'identifiant
    $form['title'] = $title;

    //Load the fields from a json file
    $json_fields = file_get_contents(get_template_directory() . '/functions/gravity-forms-programatic/' . $ressource);

    //On map le JSON a un array
    $fields = json_decode($json_fields, true);

    foreach ($fields as $field) {
        $form['fields'][] = GF_Fields::create($field);
    }


    return $form;
}

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
    $existing_form = get_form_by_name($form['title']);

    if (!isset($existing_form))
        return GFAPI::add_form($form);

    return false; 
}


//1) Si le form existe on recupere le form, sinon on le cree
// $form = get_form_by_name(THE_BIG_FORM);

// if (!isset($form)) {
//     $form = build_form(THE_BIG_FORM);
//     $result = GFAPI::add_form($form);
//     write_log('Form created');
//     write_log($result);
// }


//ca ca marche
// $form = [];
// $form['title'] = 'Test';
// $form['fields'] = array(
//     GF_Fields::create(array(
//         'type' => 'text',
//         'id' => 100,
//         'label' => 'Foobar'
//     ))

// ) ;
// $result = GFAPI::add_form($form);
// write_log('Form created');
// write_log($result);


$form = build_gf_form_meta('Test 2', 'myform.json');
$result = create_form($form);
// dump($result);

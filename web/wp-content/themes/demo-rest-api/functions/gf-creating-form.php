<?php

/**
 * Création d'un formulaire de manière programmatique (sans l'éditeur)
 */


define('THE_BIG_FORM', 'THE_BIG_FORM');

function build_form(string $title)
{
    $form = array();
    //Servira d'identifiant
    $form['title'] = $title;

    //Load the fields from a json file
    $json_fields = file_get_contents('myform.json');
    $form['fields'] = json_decode($json_fields);
    return $form;
}

function get_form_by_name(string $title)
{
    $forms = GFAPI::get_forms($active = true, $trash = false);
    foreach ($forms as $form) {
        if ($title === $form['title'])
            return $form;
    }
    return null;
}


//1) Si le form existe on recupere le form, sinon on le cree
$form = get_form_by_name(THE_BIG_FORM);

if (!isset($form)) {
    $form = build_form(THE_BIG_FORM);
    $result = GFAPI::add_form($form);
    write_log('Form created');
    write_log($result);
}

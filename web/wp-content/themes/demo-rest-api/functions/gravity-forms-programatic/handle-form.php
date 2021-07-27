<?php
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


add_action( 'gform_after_submission', 'set_post_content', 10, 2 );
function set_post_content( $entry, $form ) {
 
    $field_value = value_by_field_input_name($entry, $form, 'foobar');
    dump($field_value);
}
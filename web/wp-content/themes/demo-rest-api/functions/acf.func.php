<?php
add_filter('acf/load_field/name=icon', 'acf_load_icon_field_choices');
function acf_load_icon_field_choices($field) {
    $field['choices'] = array();
    $icons = array();
    $files = scandir(dirname(__FILE__).'/../icons');

    foreach ($files as $file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($ext == 'svg') {
            $icons[] = str_replace('.svg', '', $file);
        }
    }

    foreach ($icons as $icon) {
        $field['choices'][$icon] = '<svg class="icon" width="32" height="32"><use xlink:href="#icon-'.$icon.'"></use></svg>';
    }

    return $field;
}

add_action('admin_print_scripts', 'add_sprite_svg_in_js', 50);
function add_sprite_svg_in_js() {
    $svg_sprite_url = get_template_directory_uri().'/styles/svg/icons.svg';
    ?>
    <script>
        jQuery.get('<?php echo $svg_sprite_url; ?>', function(data) {
            var div = document.createElement('div');
            div.classList.add('screen-reader-text');
            div.innerHTML = new XMLSerializer().serializeToString(data.documentElement);
            document.body.insertBefore(div, document.body.childNodes[0]);
        });
    </script>
    <?php
}

add_filter('acf/load_field/name=image_position_x', 'acf_load_image_position_x');
function acf_load_image_position_x($field) {
    $field['choices'] = array(
        'center'    => __('Centre'),
        'left'      => __('Gauche'),
        'right'     => __('Droite')
    );
    return $field;
}

add_filter('acf/load_field/name=image_position_y', 'acf_load_image_position_y');
function acf_load_image_position_y($field) {
    $field['choices'] = array(
        'center'    => __('Centre'),
        'top'       => __('Haut'),
        'bottom'    => __('Bas')
    );
    return $field;
}

add_filter('acf/load_field/name=image_repeat', 'acf_load_image_repeat');
function acf_load_image_repeat($field) {
    $field['choices'] = array(
        'no-repeat' => __('Non'),
        'repeat'    => __('Oui')
    );
    return $field;
}

add_filter('acf/load_field/name=background_color', 'acf_load_background_color');
function acf_load_background_color($field) {
    $field['choices'] = array(
        'white'     => __('Blanc'),
        'dark'      => __('Sombre'),
        'primary'   => __('Primaire'),
        'secondary' => __('Secondaire'),
        'custom'    => __('Personnalisée'),
        //'custom'    => __('Image')
    );
    return $field;
}

add_filter('acf/load_field/name=text_color', 'acf_load_text_color');
function acf_load_text_color($field) {
    $field['choices'] = array(
        'dark'  => __('Sombre'),
        'white' => __('Clair')
    );
    return $field;
}

add_filter('acf/load_field/name=text_align', 'acf_load_text_align');
function acf_load_text_align($field) {
    $field['choices'] = array(
        'left'      => __('Gauche'),
        'center'    => __('Centre'),
        'right'     => __('Droite')
    );
    return $field;
}

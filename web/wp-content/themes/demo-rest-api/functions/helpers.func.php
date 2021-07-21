<?php
function load_template_part( $template_name, $part_name = null ) {
    ob_start();
    get_template_part($template_name, $part_name);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}

function goliath_get_page_by_template($template_name) {
    $posts_args = array(
        'numberposts'     => 1,
        'meta_key'        => '_wp_page_template',
        'meta_value'      => 'templates/'.$template_name.'.php',
        'post_type'       => 'page',
    );
    $posts = get_posts( $posts_args );
    if (is_array($posts) && isset ($posts[0])) {
        return $posts[0];
    }
}

function goliath_get_page_url_by_template($template_name) {
    $page       = goliath_get_page_by_template($template_name);
    $page_ID    = $page->ID;
    return get_the_permalink($page_ID);
}

function goliath_gf_spinner_replace( $image_src, $form ) {
    return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
}
add_filter( 'gform_ajax_spinner_url', 'goliath_gf_spinner_replace', 10, 2 );

function prettify($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function get_lazy_image($id, $img_size = 'full', $fallback_size = '300x300x1', $return_markup = true) {
    $img       = wp_get_attachment_image_src($id, $img_size);
	$fallback  = wp_get_attachment_image_src($id, $fallback_size);
    if ($return_markup) :
        return '<img src="'.$fallback[0].'" data-src="'.$img[0].'" class="lazyload" />';
    else:
        return array(
            'img'       => array(
                'url'       => $img[0],
                'width'     => $img[1],
                'height'    => $img[2]
            ),
            'fallback'  => array(
                'url'       => $fallback[0],
                'width'     => $fallback[1],
                'height'    => $fallback[2]
            )
        );
    endif;
}

<?php
function goliath_page_blocks()
{
    // Check if function exists.
    if (function_exists('acf_register_block_type')) {

        $blocks = array(
            array(
                'name'          => 'section',
                'js'            => false,
                'innerblocks'   => true,
                'args'          => array(
                    'title'         => __('Section', 'goliath'),
                    'description'   => __(''),
                    'category'      => 'common',
                    'icon'          => 'block-default',
                    'mode'          => 'auto',
                    'align'         => 'full',
                    'keywords'      => array('section'),
                    'supports'      => array('align' => false),
                )
            ),
            array(
                'name'          => 'icon',
                'js'            => false,
                'innerblocks'   => false,
                'args'          => array(
                    'title'         => __('Icône', 'goliath'),
                    'description'   => __(''),
                    'category'      => 'common',
                    'icon'          => 'block-default',
                    'mode'          => 'preview',
                    'align'         => 'full',
                    'keywords'      => array('icon'),
                    'supports'      => array('align' => false),
                )
            ),
            array(
                'name'          => 'caption-image',
                'js'            => false,
                'innerblocks'   => false,
                'args'          => array(
                    'title'         => __('Image avec légende', 'goliath'),
                    'description'   => __('Image mise en forme avec une légende'),
                    'category'      => 'common',
                    'icon'          => 'format-gallery',
                    'mode'          => 'auto',
                    'align'         => 'full',
                    'keywords'      => array('image', 'quote'),
                    'supports'      => array('align' => false),
                )
            ),
            array(
                'name'          => 'accordion',
                'js'            => false,
                'innerblocks'   => false,
                'args'          => array(
                    'title'         => __('Accordéon', 'goliath'),
                    'description'   => __('Bloc de contenu en accordeon'),
                    'category'      => 'common',
                    'icon'          => 'image-flip-vertical',
                    'mode'          => 'auto',
                    'align'         => 'full',
                    'keywords'      => array('accordeon'),
                    'supports'      => array('align' => false),
                )
            ),
            array(
                'name'          => 'button',
                'js'            => false,
                'innerblocks'   => false,
                'args'          => array(
                    'title'         => __('Bouton', 'goliath'),
                    'description'   => '',
                    'category'      => 'common',
                    'icon'          => 'block-default',
                    'mode'          => 'auto',
                    'align'         => 'full',
                    'keywords'      => array('bouton'),
                    'supports'      => array('align' => false),
                )
            ),
        );

        foreach ($blocks as $block) {
            goliath_register_block_type($block['name'], $block['args'], $block['js'], $block['innerblocks']);
        }

    }
}
add_action('acf/init', 'goliath_page_blocks');

function goliath_register_block_type($name, $args, $js = false, $innerblocks = false)
{
    $base_args = array(
        'name' => $name,
        'render_template' => 'blocks/'.$name.'.php',
    );

    if ($js) {
        $base_args['enqueue_script'] = get_stylesheet_directory_uri().'/blocks/js/'.$name.'.min.js';
    }

    if ($innerblocks) {
        $base_args['mode']      = 'preview';
        $base_args['supports']  = array(
            'align'     => true,
            'mode'      => false,
            'jsx'       => true
        );
    }

    $merged_args = array_merge($args, $base_args);

    acf_register_block_type($merged_args);
}

function goliath_allowed_block_types($allowed_blocks, $post)
{
    $allowed_blocks = array(
        'core/block',
        'core/image',
        'core/paragraph',
        'core/list',
        'core/video',
        'core/heading',
        'core/columns',
        'core/more',

        'acf/section',

        'acf/icon',
        'acf/button',
        'acf/caption-image',
    );

    if ('page' == get_post_type()) {
        $allowed_blocks[] = 'acf/accordion';
    }

    if ('project' != get_post_type()) {
        $allowed_blocks[] = 'acf/accordion';
    }

    return $allowed_blocks;
}
add_filter( 'allowed_block_types', 'goliath_allowed_block_types', 10, 2 );


/**
 * Disable Gutenberg by template
 *
 */
function goliath_disable_gutenberg($can_edit, $post)
{

    if ($post->ID === (int)get_option('page_on_front')) {
        $can_edit = false;
    }

    return $can_edit;

}
add_filter('gutenberg_can_edit_post', 'goliath_disable_gutenberg', 10, 2);
add_filter('use_block_editor_for_post', 'goliath_disable_gutenberg', 10, 2);

function goliath_remove_patterns(){
    // No block patterns
    remove_theme_support( 'core-block-patterns' );
}
add_action('after_setup_theme', 'goliath_remove_patterns');

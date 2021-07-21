<?php
// Ajout de la metabox
add_action( 'add_meta_boxes_page', 'goliath_meta_boxes_archive_page' );
function goliath_meta_boxes_archive_page(){
    add_meta_box( 'archive_page_desc', 'Archive à présenter', 'goliath_archive_page_content', 'page', 'side' );
}

// Fonction de la metabox
function goliath_archive_page_content( $post ) {
    $archive_page = get_post_meta( $post->ID, '_archive_page', true );
    ?>
    <select name="archive_page">
        <option value="">Aucune</option>
        <?php
        $post_types = get_post_types(  array(
            'show_ui'       => true,
            'has_archive'   => true,
            '_builtin'      => false
        ), 'objects' );
        foreach( $post_types as $post_type )
            echo '<option value="' . esc_attr( $post_type->name ) . '" ' . selected( $post_type->name, $archive_page, false ) . '>' . esc_html( $post_type->labels->name ) . '</option>';
        ?>
    </select>
    <p>Choisissez la cible de cette page</p>
    <?php
    wp_nonce_field( 'archive_page-save_' . $post->ID, 'archive_page-nonce') ;
}

// Sauvegarde de la metabox
add_action( 'save_post_page', 'goliath_save_post', 10, 2 );
function goliath_save_post( $post_ID, $post ){
    // on retourne rien du tout s'il s'agit d'une sauvegarde automatique
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_ID;

    if ( isset( $_POST[ 'archive_page' ] ) ) {
        check_admin_referer( 'archive_page-save_' . $post_ID, 'archive_page-nonce' );
        if ( isset( $_POST[ 'archive_page' ] ) ) {
            $target = $_POST[ 'archive_page' ];
            if ( $target ){
                global $wpdb;
                $suppr = $wpdb->get_results( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_archive_page' AND meta_value = '%s'", $target) );
                foreach( $suppr as $s ){
                    delete_post_meta( $s->post_id, '_archive_page' );
                }
                update_post_meta( $post_ID, '_archive_page', $_POST[ 'archive_page' ] );
            } else {
                delete_post_meta( $post_ID, '_archive_page' );
            }
        }
    }
}

// Présentation de l'archive
function goliath_presentation_archive() {
    $page = false;
    if( is_post_type_archive() ){
        $target = false;
        $post_type_obj = get_queried_object();
        if( is_a( $post_type_obj, 'WP_Post_Type') ){
            $target = $post_type_obj->name;
        } else if ( is_a( $post_type_obj, 'WP_Term') ){
            $taxo = get_taxonomy( $post_type_obj->taxonomy );
            if( count( $taxo->object_type === 1 ) ){
                $target = $taxo->object_type[0];
            }
        }
        if( $target ){
            $presentation = new WP_Query( array(
                'post_type' => 'page',
                'meta_query' => array(
                    array(
                        'key' => '_archive_page',
                        'value' => $target,
                        'compare' => '='
                    )
                )
            ) );
            if( $presentation->have_posts() ){
                $page = $presentation->posts[0];
            }
        }
    } elseif ( is_home() ){
        $page_for_post_id = get_option( 'page_for_posts' );
        $page = get_post( $page_for_post_id );
    }
    return $page;
}

// Filtre permalien
add_filter( 'page_link', 'goliath_archive_permalink', 10, 2 );
function goliath_archive_permalink( $lien, $id ) {
    if( '' != ( $archive = get_post_meta( $id, '_archive_page', true ) ) && ! is_admin() )
        return get_post_type_archive_link( $archive );
    else
        return $lien;
}

// Redirect
add_action( 'template_redirect', 'goliath_redirect_to_archive' );
function goliath_redirect_to_archive() {
    if( is_page() && ! is_admin() ){
        global $post;
        if( '' != ( $archive = get_post_meta( $post->ID, '_archive_page', true ) ) ) {
            wp_redirect( get_post_type_archive_link( $archive ), 301 );
            exit();
        }
    }
}

// Filtre classes nav menu
add_filter( 'nav_menu_css_class', 'goliath_add_my_archive_menu_classes', 10 , 3 );
function goliath_add_my_archive_menu_classes( $classes , $item, $args ) {
    if( '' != ( $archive = get_post_meta( $item->object_id, '_archive_page', true ) ) ) {
        if( is_post_type_archive( $archive ) )
            $classes[] = 'current-menu-item';
        if( is_singular( $archive ) )
            $classes[] = 'current-menu-ancestor';
    }
    return $classes;
}

add_filter( 'wpseo_title', 'goliath_page_archive_meta_title' );
function goliath_page_archive_meta_title( $title ){
    if( is_post_type_archive() ) {
        $page_presentation = goliath_presentation_archive();
        if( $page_presentation ){
            $meta_title = get_post_meta( $page_presentation->ID, '_yoast_wpseo_title', true );
            if( $meta_title ){
                $title = $meta_title;
            }
        }
    }
    return $title;
}

add_filter( 'wpseo_metadesc', 'goliath_page_archive_meta_desc' );
function goliath_page_archive_meta_desc( $title ){
    if (is_post_type_archive() ) {
        $page_presentation = goliath_presentation_archive();
        if( $page_presentation ){
            $meta_title = get_post_meta( $page_presentation->ID, '_yoast_wpseo_metadesc', true );
            if( $meta_title ){
                $title = $meta_title;
            }
        }
    }
    return $title;
}

<?php
add_action('widgets_init', 'footer_widgets_init');
function footer_widgets_init(){
    register_sidebars(3, array(
        'id'    => 'footer',
        'name'  => 'Footer %d',
        'class' => 'footer-widget',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => "</div>\n",
        'before_title' => '<div class="widget-title">',
        'after_title' => "</div>\n",
    ));
}

<?php
function goliath_register_required_plugins() {

    $tgmpa_plugins = array(
		array(
            'name'              => 'Advanced Custom Fields PRO',
            'slug'              => 'advanced-custom-fields-pro',
            'source'            => get_template_directory_uri().'/plugins/advanced-custom-fields-pro.zip',
            'required'          => true,
        ),
		array(
            'name'              => 'Gravity Forms',
            'slug'              => 'gravityforms',
            'source'            => get_template_directory_uri().'/plugins/gravityforms.zip',
            'required'          => true,
        ),
        array(
            'name'              => 'Restricted Site Access',
            'slug'              => 'restricted-site-access',
			'source'            => 'https://downloads.wordpress.org/plugin/restricted-site-access.7.2.0.zip',
            'required'          => false,
            'force_activation'	=> false,
        )
    );

    $tgmpa_config = array(
        'id'           => 'goliath',               // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'themes.php',            // Parent menu slug.
        'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    tgmpa($tgmpa_plugins, $tgmpa_config);
}

add_action('tgmpa_register', 'goliath_register_required_plugins');

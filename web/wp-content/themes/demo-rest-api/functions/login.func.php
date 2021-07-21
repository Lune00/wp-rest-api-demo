<?php
function my_login_logo() { ?>
    <style type="text/css">
		.login {
			background: #222;
		}
        .login h1 a {
			background: url(<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg) no-repeat center !important;
			height: 100px !important;
			width: 100% !important;
        }
        .login #backtoblog a,
        .login #nav a,
        .privacy-policy-link {
            color: #fff !important;
        }
        .login.wp-core-ui .button-primary,
        .login.wp-core-ui .button-primary:hover {
            background: #222 !important;
            border-color: #222 !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }
        .login #login_error,
        .login .message,
        .login .success {
            border-color: #ffc000 !important;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

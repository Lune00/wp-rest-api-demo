<div id="mobile-bar" class="text-center">
	<div class="container-fluid">
		<div class="row">
			<div class="col-2">
				<button class="menu-trigger" data-type="open-menu"><span class="sr-only"><?php _e('Menu', 'theme_name'); ?></span></button>
			</div>
		</div><!-- .row -->
	</div><!-- .container-fluid -->
</div><!-- #mobile-bar -->

<div id="mobile-menu">
	<nav>
		<a href="#" class="back" data-type="close-menu"><?php _e('Retour', 'theme_name'); ?></a>
		<?php
		$args = array(
			'container' 		=> '',
			'theme_location' 	=> 'mobile'
		);
		wp_nav_menu($args);
		?>
	</nav>
	<div class="mask" data-type="close-menu"></div>
</div><!-- #mobile-menu -->

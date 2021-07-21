<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

	<header id="masthead" class="site-header bg-primary text-white" role="banner">
		<div class="main-menu">
			<div class="container">
				<div class="row justify-content-between">
					<div class="col-lg-3 col-6 d-flex align-items-center">
						<div class="site-branding">
				            <?php if (is_front_page()) : ?>
							<h1 class="site-title">
								<?php get_template_part('template-parts/logo'); ?>
								<span class="sr-only"><?php bloginfo( 'name' ); ?></span>
							</h1>
							<?php else : ?>
							<p class="site-title">
								<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
									<?php get_template_part('template-parts/logo'); ?>
									<span class="sr-only"><?php bloginfo( 'name' ); ?></span>
								</a>
							</p>
							<?php endif; ?>
				        </div><!-- .site-branding -->
					</div>
					<?php if (has_nav_menu('header')) : ?>
					<div class="menu-container col-lg-9 col-3 d-flex align-items-center justify-content-end">
						<div id="menu">
							<nav>
								<?php
								$args = array(
									'container' 		=> '',
									'theme_location' 	=> 'header'
								);
								wp_nav_menu($args);
								?>
							</nav><!-- .main-menu -->
						</div><!-- #menu -->
						<button class="menu-trigger d-none d-sm-block d-lg-none" data-type="open-menu"><span class="sr-only"><?php _e('Menu', 'theme_name'); ?></span></button>
					</div>
					<?php endif; ?>
				</div><!-- .row -->
			</div><!-- .container -->
		</div><!-- .main-menu -->
	</header><!-- #masthead -->

	<div class="site-content-contain">
		<div id="content" class="site-content">

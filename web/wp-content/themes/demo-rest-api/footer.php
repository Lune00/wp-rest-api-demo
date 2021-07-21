		</div><!-- #content -->

	</div><!-- .site-content-contain -->
	<footer id="colophon" class="site-footer bg-primary text-white" role="contentinfo">
		<div class="footer py-md-5 py-4">
			<div class="container">
				<div class="row">
					<div class="edito col-lg-3 col-md-6 mb-3 mb-md-0">
						<h3 class="site-title mb-4"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg" alt="<?php bloginfo( 'name' ); ?>" /></h3>
					</div>
					<?php
			        for ($i = 1; $i<=3; $i++) {
					$sidebar_id = ($i==1) ? 'footer' : 'footer-'.$i;
					if (is_active_sidebar($sidebar_id)) {
					?>
					<div class="footer-widget-area col-lg-3 col-md-6 mb-3 mb-lg-0">
						<?php dynamic_sidebar('footer-'.$i); ?>
			        </div>
					<?php } ?>
			        <?php } ?>
				</div><!-- .row -->
			</div><!-- .container -->
		</div><!-- .footer -->
		<div class="site-info">
			<div class="container">
				<div class="row">
					<p class="col-md-6 text-center text-md-left mb-3 mb-md-0">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?> - <?php _e('Tous droits réservés', 'theme_name'); ?></p>
					<nav class="col-md-6 d-flex justify-content-center justify-content-md-end">
						<?php
						$args = array(
							'container' 		=> '',
							'theme_location' 	=> 'footer'
						);
						wp_nav_menu($args);
						?>
					</nav>
				</div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php get_template_part('template-parts/mobile-menu'); ?>

<?php wp_footer(); ?>

</body>
</html>

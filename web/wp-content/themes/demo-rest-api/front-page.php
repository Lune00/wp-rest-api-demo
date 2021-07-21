<?php get_header(); ?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<div style="padding-top: 120px">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</main><!-- #main -->
</div><!-- #primary -->
<?php get_footer();

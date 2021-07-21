<?php get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<header class="entry-header">
				<div class="container">
					<h1 class="entry-title"><?php _e('Le blog', 'theme_name'); ?></h1>
				</div><!-- .container -->
			</header><!-- .entry-header -->
			<?php if ( have_posts() ) : ?>
				<div class="container">
					<?php while ( have_posts() ) : the_post(); ?>
					<h2><?php the_title(); ?></h2>
					<?php the_excerpt(); ?>
					<?php endwhile; ?>
					<div id="pagination">
						<?php
						the_posts_pagination(array(
							'prev_text' => __('Précédent', 'theme_name'),
							'next_text' => __('Suivant', 'theme_name'),
							'before_page_number' => '',
							'screen_reader_text' => ''
						));
						?>
					</div><!-- #pagination -->
				</div><!-- .container -->
			<?php endif; ?>
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();

<?php get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<div class="container">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</div><!-- .container -->
				</header><!-- .entry-header -->
				<?php the_content(); ?>
			</article><!-- #post-## -->
			<?php endwhile; ?>
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
<?php get_footer();

<?php
/*
Template Name: Fullwidth
*/
?>

<?php get_header(); ?>

<?php $is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>



<?php if ( ! $is_page_builder_used ) : ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column et_pb_extra_column_main">

<?php endif; ?>

				<?php if ( have_posts() ) : ?>

					<?php while ( have_posts() ) : the_post(); ?>

						<?php if ( ! $is_page_builder_used ) : ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<div class="post-wrap">
									<h1 class="entry-title"><?php the_title(); ?></h1>
									<div class="post-content entry-content">
						<?php endif; ?>
										<?php the_content(); ?>
						<?php if ( ! $is_page_builder_used ) : ?>
									</div><!-- /.post-content -->
								</div><!-- /.post-wrap -->
							</article>
						<?php endif; ?>

					<?php endwhile; ?>

				<?php else : ?>
					<h2><?php esc_html_e( 'Page not found', 'extra' ); ?></h2>
				<?php endif; ?>

				<?php
				if ( ! $is_page_builder_used && ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'extra_show_pagecomments', 'on' ) ) {
					comments_template( '', true );
				}
				?>
<?php if ( ! $is_page_builder_used ) : ?>
			</div><!-- /.et_extra_column.et_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->
<?php endif; ?>



<?php get_footer();

<?php get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="post-wrap">
						<?php if ( is_post_extra_title_meta_enabled() ) { ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<?php } ?>
						<div class="post-content entry-content">
							<?php the_content(); ?>
							<?php
							if ( ! extra_is_builder_built() ) {
								wp_link_pages( array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
									'after'  => '</div>',
								) );
							}
							?>
						</div>
					</div><!-- /.post-wrap -->
				</article>
				<?php
					endwhile;
				else :
					?>
					<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
					<?php
				endif;
				wp_reset_query();
				?>
				<?php
				if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'extra_show_pagescomments', 'on' ) ) {
					comments_template( '', true );
				}
				?>
			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();

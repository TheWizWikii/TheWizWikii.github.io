<?php get_header(); ?>
<div id="main-content">
	<?php
	if ( et_builder_is_product_tour_enabled() ):

			while ( have_posts() ): the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-content">
					<?php
						the_content();
					?>
					</div> <!-- .entry-content -->

				</article> <!-- .et_pb_post -->

		<?php endwhile;
		else:
	?>
	<div class="container">
		<div id="content-area" class="<?php extra_project_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'module single-project-module' ); ?>>
							<?php if ( is_post_extra_title_meta_enabled() ) { ?>
							<div class="post-header">
								<h1 class="entry-title"><?php the_title(); ?></h1>
								<div class="post-meta">
									<p>
										<?php
											echo et_extra_display_post_meta( array('rating_stars' => false) );
										?>
									</p>
								</div>
							</div>
							<?php } ?>

							<?php
							$attachments  = extra_get_the_project_gallery_images();
							$thumbnail_id = get_post_thumbnail_id();

							if ( $attachments ) {
							?>
							<?php $gallery_autoplay = get_post_meta( get_the_ID(), '_gallery_autoplay', true ); ?>
							<div class="post-thumbnail post-gallery">
								<div class="gallery et-slider" <?php if ( $gallery_autoplay ) { echo ' data-autoplay="' . esc_attr( $gallery_autoplay ) . '"'; } ?>>
									<div class="carousel-items">
										<div class="carousel-item-size"></div>
									<?php foreach ( $attachments as $attachment_id => $attachment_src ) { ?>
										<div class="gallery_image carousel-item">
											<img src="<?php echo esc_attr( $attachment_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
										</div>
									<?php } ?>
									</div>
								</div>
							</div><!-- .post-thumbnail.post-gallery -->
							<?php } else if ( $thumbnail_id && is_post_extra_featured_image_enabled() ) { ?>
							<div class="post-thumbnail">
								<?php list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $thumbnail_id, 'extra-image-huge' ); ?>

								<img src="<?php echo esc_attr( $thumb_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
							</div><!-- .post-thumbnail -->
							<?php } ?>

							<div class="post-wrap">
								<div class="post-content entry-content">
									<?php the_content(); ?>
									<?php
										wp_link_pages( array(
											'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
											'after'  => '</div>',
										) );
									?>
								</div>
							</div>
						</article>
				<?php endwhile; ?>
				<?php else : ?>
					<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
				<?php endif; ?>

				<?php extra_project_get_below_content(); ?>

				<nav class="post-nav">
					<div class="nav-links clearfix">
						<div class="nav-link nav-link-prev">
							<?php previous_post_link( '%link', et_get_safe_localization( __( '<span class="button" title="%title"></span>', 'extra' ) ) ); ?>
						</div>
						<div class="nav-link nav-link-next">
							<?php next_post_link( '%link', et_get_safe_localization( __( '<span class="button" title="%title"></span>', 'extra' ) ) ); ?>
						</div>
					</div>
				</nav>

				<?php
				if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'extra_show_postcomments', 'on' ) ) {
					comments_template( '', true );
				}
				?>

			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php extra_project_get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
	<?php endif; ?>
</div> <!-- #main-content -->

<?php get_footer();

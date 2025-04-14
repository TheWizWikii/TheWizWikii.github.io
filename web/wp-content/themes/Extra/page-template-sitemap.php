<?php
/*
Template Name: Sitemap
*/

get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) : the_post(); ?>

						<div class="et_extra_other_module sitemap-box">
							<div class="sitemap-content">
								<h2><?php the_title(); ?></h2>
								<?php the_content(); ?>
								<?php
								if ( ! extra_is_builder_built() ) {
									wp_link_pages( array(
										'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
										'after'  => '</div>',
									) );
								}
								?>

								<?php $sitemap_page_options = extra_get_sitemap_page_options(); ?>

								<div class="sections">
									<?php
									foreach ( $sitemap_page_options['sections'] as $section_slug => $section_name ) {
										echo '<ul class="section-' . esc_attr( $section_slug ) . '">';
										echo '<li>' . esc_html( $section_name ) . '</li>';
										echo extra_get_sitemap_page_section_items( $section_slug, $sitemap_page_options['page_section_options'] );
										echo '</ul>';
									}
									?>
								</div>
							</div>
						</div>

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
				if ( comments_open() || get_comments_number() ) {
					comments_template( '', true );
				}
				?>
			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();

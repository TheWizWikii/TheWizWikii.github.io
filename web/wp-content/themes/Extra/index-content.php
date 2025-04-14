<?php
/**
 * Template partial of index content.
 *
 * @package Extra
 */

$type = strtolower( strval( et_get_option( 'archive_list_style', 'standard' ) ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- This is not WP $type global variable.

?>
<div class="posts-blog-feed-module <?php echo esc_attr( $type ); ?> post-module et_pb_extra_module module">
	<div class="paginated_content">
		<div class="paginated_page" <?php echo 'masonry' == $type ? 'data-columns' : ''; ?>>
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				$post_format = et_get_post_format();
				$post_format_class = !empty( $post_format ) ? 'et-format-' . $post_format : '';
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'hentry ' . $post_format_class ); ?>>
						<div class="header">
							<?php
							$thumb_args = array(
								'size'      => 'extra-image-medium',
								'img_after' => '<span class="et_pb_extra_overlay"></span>',
							);
							require locate_template( 'post-top-content.php' );
							?>
						</div>
						<?php
						if ( !in_array( $post_format, array( 'quote', 'link' ) ) ) {
						?>
						<div class="post-content">
							<?php $color = extra_get_post_category_color(); ?>
							<h2 class="post-title entry-title"><a class="et-accent-color" style="color:<?php echo esc_attr( $color ); ?>;" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="post-meta vcard">
								<p><?php echo extra_display_archive_post_meta(); ?></p>
							</div>
							<div class="excerpt entry-summary">
								<p><?php
								if ( has_excerpt() ) {
									the_excerpt();
								} else {
									$excerpt_length = get_post_thumbnail_id() ? '100' : '230';
									et_truncate_post( $excerpt_length );
								}
								?></p>
								<a class="read-more-button" href="<?php the_permalink(); ?>"><?php echo esc_html__( 'Read More', 'extra' ); ?></a>
							</div>
						</div>
						<?php } ?>
					</article>
				<?php
			endwhile;
		else :
			?>
			<article class='nopost'>
				<h5><?php esc_html_e( 'Sorry, No Posts Found', 'extra' ); ?></h5>
			</article>
			<?php
		endif;
		?>
		</div><!-- .paginated_page -->
	</div><!-- .paginated_content -->

	<?php global $wp_query; ?>
	<?php if ( $wp_query->max_num_pages > 1 ) { ?>
	<div class="archive-pagination">
		<?php echo extra_archive_pagination(); ?>
	</div>
	<?php } ?>
</div><!-- /.posts-blog-feed-module -->

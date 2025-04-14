<?php $page = !empty( $page ) ? $page : 1; ?>
<div class="paginated_page paginated_page_<?php esc_attr_e( $page ); ?> active" <?php echo 'masonry' == $blog_feed_module_type ? ' data-columns' : ''; ?>  data-columns>
<?php
while ( $module_posts->have_posts() ) : $module_posts->the_post();

	$post_format = et_get_post_format();
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'post et-format-'.$post_format ); ?>>
		<div class="header">
			<?php
			if ( $show_featured_image || et_has_post_format( 'quote' ) || et_has_post_format( 'link' ) ) {
				$overlay = '' !== $hover_overlay_icon ? '<span class="et_pb_extra_overlay et_pb_inline_icon" data-icon="'. esc_attr( et_pb_process_font_icon( $hover_overlay_icon ) ) .'"></span>' : '<span class="et_pb_extra_overlay"></span>';
				$thumb_args = array(
					'size'      => 'extra-image-medium',
					'img_after' => $overlay,
				);
				$score_bar = extra_get_the_post_score_bar();
				require locate_template( 'post-top-content.php' );
			}
			?>
		</div>
		<?php
		if ( !in_array( $post_format, array( 'quote', 'link' ) ) ) {
		?>
		<div class="post-content">
			<?php
				$color = extra_get_post_category_color();

				$et_permalink = get_the_permalink();
			?>
			<h2 class="post-title entry-title"><a class="et-accent-color" style="color:<?php echo esc_attr( $color ); ?>;" href="<?php echo esc_url( $et_permalink ); ?>"><?php the_title(); ?></a></h2>
			<div class="post-meta vcard">
				<?php
				$meta_args = array(
					'author_link'   => $show_author,
					'post_date'     => $show_date,
					'date_format'   => $date_format,
					'categories'    => $show_categories,
					'comment_count' => $show_comments,
					'rating_stars'  => $show_rating,
				);
				?>
				<p><?php echo et_extra_display_post_meta( $meta_args ); ?></p>
			</div>
			<div class="excerpt entry-summary">
				<?php
				if ( 'excerpt' == $content_length ) {
					if ( has_excerpt() ) {
						the_excerpt();
					} else {
						if ( in_array( $post_format, array( 'audio', 'map' ) ) ) {
							$excerpt_length = '100';
						} else {
							$excerpt_length = get_post_thumbnail_id() ? '100' : '230';
						}

						echo wpautop( et_truncate_post( $excerpt_length, false ) );
					}
					if ( $show_more ) {

						$read_more_class = 'read-more-button';
						$data_icon = '';

						if ( isset( $custom_read_more ) && 'on' === $custom_read_more && isset( $read_more_icon ) && '' !== $read_more_icon ) {
							$read_more_class .= ' et_pb_inline_icon';
							$data_icon = et_pb_process_font_icon( $read_more_icon );
						}
						?>

						<a class="<?php echo esc_attr( $read_more_class ); ?>" data-icon="<?php echo esc_attr( $data_icon ); ?>" href="<?php echo esc_url( $et_permalink ); ?>"><?php esc_html_e( 'Read More', 'extra' ); ?></a>
					<?php }
				} else {
					echo extra_get_de_buildered_content();
				}
				?>
			</div>
		</div>
		<?php } ?>
	</article>
<?php
endwhile;
wp_reset_postdata();
?>
</div><!-- /.paginated_page.paginated_page_<?php echo $page; ?> -->

<?php
	if ( $module_posts->have_posts() ) :
	$max_title_characters = isset( $max_title_characters ) && '' !== $max_title_characters ? intval( $max_title_characters ) : 50;
?>
<?php $id_attr = '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : ''; ?>
<div <?php echo $id_attr ?> class="module featured-posts-slider-module et_pb_extra_module <?php echo esc_attr( $module_class ); ?>" data-breadcrumbs="enabled"<?php if ( $enable_autoplay ) { echo ' data-autoplay="' . esc_attr( $autoplay_speed ) . '"'; } ?>>
	<div class="posts-slider-module-items carousel-items et_pb_slides">
	<?php while ( $module_posts->have_posts() ) : $module_posts->the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'post carousel-item et_pb_slide' ); ?> <?php et_thumb_as_style_background(); ?>>
			<div class="post-content-box">
				<div class="post-content">
					<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php truncate_title( $max_title_characters ); ?></a></h3>
					<div class="post-meta vcard">
						<?php
						$meta_args = array(
							'author_link'    => $show_author,
							'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
							'post_date'      => $show_date,
							'date_format'    => $date_format,
							'categories'     => $show_categories,
							'comment_count'  => $show_comments,
							'rating_stars'   => $show_rating,
						);
						?>
						<p><?php echo et_extra_display_post_meta( $meta_args ); ?>
					</div>
				</div>
			</div>
		</article>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
	</div>
</div>
<?php endif;

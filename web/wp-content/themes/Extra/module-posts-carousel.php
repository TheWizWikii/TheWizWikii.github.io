<?php
if ( $module_posts->have_posts() ) :
	$carousel_post_index = 0;
	$max_title_characters = isset( $max_title_characters ) && '' !== $max_title_characters ? intval( $max_title_characters ) : 40;
?>
<?php $id_attr = '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : ''; ?>
<div <?php echo $id_attr ?> class="module posts-carousel-module et_pb_extra_module loading <?php echo esc_attr( $module_class ); ?>" style="border-top-color:<?php echo esc_attr( $border_top_color ); ?>" <?php if ( $enable_autoplay ) { echo ' data-autoplay="' . esc_attr( $autoplay_speed ) . '"'; } ?>>
	<div class="module-head">
		<h1 style="color:<?php echo esc_attr( $module_title_color ); ?>"><?php echo esc_html( $title ); ?></h1>
		<div class="module-filter"><?php echo esc_html( $sub_title ); ?></div>
	</div>
	<div class="posts-slider-module-items carousel-items">
		<?php
		while ( $module_posts->have_posts() ) : $module_posts->the_post();
			$carousel_post_index++;
			$carousel_post_class = 'carousel-item';

			if ( 4 < $carousel_post_index ) {
				$carousel_post_class .= ' carousel-item-hide-on-load';
			} elseif ( 2 < $carousel_post_index && 4 >= $carousel_post_index ) {
				$carousel_post_class .= ' carousel-item-hide-on-load-medium';
			} elseif ( 2 === $carousel_post_index) {
				$carousel_post_class .= ' carousel-item-hide-on-load-small';
			}
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( $carousel_post_class ); ?>>
			<?php
			if ( ! empty( $hover_overlay_icon ) ) {
				$overlay = '<span class="et_pb_extra_overlay et_pb_inline_icon" data-icon="' . esc_attr( et_pb_process_font_icon( $hover_overlay_icon ) ) . '"></span>';
			} else {
				$overlay = '<span class="et_pb_extra_overlay"></span>';
			}
			echo et_extra_get_post_thumb(array(
				'size'      => 'extra-image-small',
				'a_class'   => array( 'post-thumbnail' ),
				'img_after' => $overlay,
			));
			?>
			<div class='post-content-box'>
				<div class="post-content">
					<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php truncate_title( $max_title_characters ); ?></a></h3>
					<?php if ( $show_date ) { ?>
					<div class="post-meta vcard">
						<?php echo extra_get_the_post_date( get_the_ID(), $date_format ); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</article>

	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
	</div>
</div>
<?php else: ?>
<div class="module post-carousel-module et_pb_extra_module">
	<article class="post carousel-item nopost">
		<h5><?php esc_html_e( 'Sorry, no posts found.', 'extra' ); ?></h5>
	</article>
</div>
<?php endif;

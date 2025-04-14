<?php if ( $module_posts->have_posts() ) : ?>
<?php $module_posts->the_post(); ?>
	<div class="main-post">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="header">
			<?php
				$thumbnail_size = extra_sidebar() ? 'extra-image-medium' : 'extra-image-small';
				$overlay = ! empty( $hover_overlay_icon ) ? '<span class="et_pb_extra_overlay et_pb_inline_icon" data-icon="' . esc_attr( et_pb_process_font_icon( $hover_overlay_icon ) ) . '"></span>' : '<span class="et_pb_extra_overlay"></span>';
				$thumb_args = array(
					'size'      => $thumbnail_size,
					'img_after' => $overlay,
				);
				$score_bar = extra_get_the_post_score_bar();
				require locate_template( 'post-top-content.php' );
			?>
			</div>
			<div class="post-content">
				<?php $color = !empty( $term_color ) ? $term_color : extra_get_post_category_color(); ?>
				<h2 class="entry-title"><a class="et-accent-color" style="color:<?php echo esc_attr( $color ); ?>;" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="post-meta vcard">
					<p><?php
					echo et_extra_display_post_meta(array(
						'author_link'   => $show_author,
						'post_date'     => $show_date,
						'date_format'   => $date_format,
						'categories'    => $show_categories,
						'comment_count' => $show_comments,
						'rating_stars'  => $show_rating,
					));
					?></p>
				</div>
				<?php
				if ( has_excerpt() ) {
					$excerpt = get_the_excerpt();
				} else {
					$excerpt_length = get_post_thumbnail_id() ? '100' : '230';
					$excerpt = et_truncate_post( $excerpt_length, false );
				}
				?>
				<div class="excerpt entry-summary">
				<?php if ( !empty( $excerpt ) ) { ?>
					<p><?php echo $excerpt; ?></p>
				<?php } ?>
				</div>
			</div>
		</article>
	</div>
	<ul class="posts-list">
	<?php
	if ( $module_posts->have_posts() ) :
		while ( $module_posts->have_posts() ) : $module_posts->the_post();
	?>
		<li>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'title-thumb-hover' ); ?>>
				<?php

				$post_format = et_get_post_format();

				if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
					$thumb_src = et_get_post_format_thumb( $post_format );
					$img_style = sprintf( 'background-color:%s', $color );
				} else if ( 'gallery' == $post_format ) {
					$thumb_src = et_get_gallery_post_format_thumb();
				} else if ( !get_post_thumbnail_id() ) {
					$thumb_src = et_get_post_format_thumb( 'text', 'icon' );
					$img_style = sprintf( 'background-color:%s', $color );
				} else {
					$img_style = sprintf( 'background-color:%s', $color );
				}

				if ( $show_thumbnails ) {
					echo et_extra_get_post_thumb( array(
						'size'      => 'extra-image-square-small',
						'a_class'   => array('post-thumbnail'),
						'thumb_src' => !empty( $thumb_src ) ? $thumb_src : '',
						'img_style' => !empty( $img_style ) ? $img_style : '',
					));
				}
				?>
				<div class="post-content">
					<h3 class="entry-title"><a href="<?php the_permalink(); ?>" data-hover-color="<?php echo esc_attr( $color ); ?>"><?php the_title(); ?></a></h3>
					<div class="post-meta vcard">
						<p><?php
						echo et_extra_display_post_meta(array(
							'author_link'   => $show_author,
							'post_date'     => $show_date,
							'date_format'   => $date_format,
							'categories'    => $show_categories,
							'comment_count' => $show_comments,
							'rating_stars'  => $show_rating,
						));
						?></p>
					</div>
				</div>
			</article>
		</li>
	<?php
		endwhile;
	endif;
	?>
	</ul>

	<?php wp_reset_postdata(); ?>
<?php else: ?>
	<article class='nopost'>
		<h5><?php esc_html_e( 'Sorry, No Posts Found', 'extra' ); ?></h5>
	</article>
<?php endif;

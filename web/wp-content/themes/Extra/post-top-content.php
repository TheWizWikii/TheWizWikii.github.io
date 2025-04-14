<?php

$score_bar = !empty( $score_bar ) ? $score_bar : '';

$post_category_color = extra_get_post_category_color();

if ( et_has_post_format( 'quote' ) ) {
	$quote = get_post_meta( get_the_ID(), '_quote_format_quote', true );

	if ( ! empty( $quote ) ) {
	?>
	<div class="quote-format" style="background-color:<?php echo esc_attr( $post_category_color ); ?>">
		<?php
		if ( !empty( $quote ) ) {
			$quote = esc_html( $quote );

			if ( ! is_single() ) {
				$quote = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_permalink() ), $quote );
			}
		?>
		<h2 class="title"><?php echo $quote; ?></h2>
		<?php } ?>
		<?php $quote_attribution = get_post_meta( get_the_ID(), '_quote_format_quote_attribution', true ); ?>
		<?php if ( !empty( $quote_attribution ) ) { ?>
		<span class="attribution"><?php echo esc_html( $quote_attribution ); ?></span>
		<?php } ?>

		<?php echo $score_bar; ?>
	</div>
	<?php

	}
} else if ( et_has_post_format( 'link' ) ) {
	$title = get_post_meta( get_the_ID(), '_link_format_link_title', true );
	$link  = get_post_meta( get_the_ID(), '_link_format_link_url', true );

	if ( !empty( $link ) ) {
	?>
	<div class="link-format" style="background-color:<?php echo esc_attr( $post_category_color ); ?>">
		<?php if ( !empty( $title ) ) { ?>
			<a href="<?php the_permalink(); ?>">
				<h2 class="title"><?php echo $title; ?></h2>
			</a>
		<?php } ?>
		<?php if ( !empty( $link ) ) { ?>
			<a href="<?php echo esc_url( $link ); ?>" target="_blank">
				<span class="attribution" ><?php echo esc_attr( $link ); ?></span>
			</a>
		<?php } ?>

		<?php echo $score_bar; ?>
	</div>
	<?php

	}

} else if ( et_has_post_format( 'map' ) ) {
	$lat = get_post_meta( get_the_ID(), '_map_format_lat', true );
	$lng = get_post_meta( get_the_ID(), '_map_format_lng', true );
	$zoom = get_post_meta( get_the_ID(), '_map_format_zoom', true );

	if ( $lat && $lng && $zoom ) {
		et_extra_enqueue_google_maps_api();
	?>
	<div class="map-format">
		<div class="post-format-map" data-lat="<?php echo esc_attr( $lat ); ?>" data-lng="<?php echo esc_attr( $lng ); ?>" data-zoom="<?php echo esc_attr( $zoom ); ?>"></div>
		<?php echo $score_bar; ?>
	</div>
	<?php

	}
} else if ( et_has_post_format( 'gallery' ) ) {
	$attachment_ids   = get_post_meta( get_the_ID(), '_gallery_format_attachment_ids', true );
	$gallery_autoplay = get_post_meta( get_the_ID(), '_gallery_format_autoplay', true );
	$attachment_ids   = trim( $attachment_ids );

	if ( !empty( $attachment_ids ) ) {

	?>
	<div class="gallery-format et-slider" <?php if ( $gallery_autoplay ) { echo ' data-autoplay="' . esc_attr( $gallery_autoplay ) . '"'; } ?>>
	<?php
	$attachment_ids = explode( ',', $attachment_ids );
		?>
		<div class="carousel-items">
			<div class="carousel-item-size"></div>
		<?php
		$thumbnail_size = !empty( $thumb_args ) ? $thumb_args['size'] : extra_get_column_thumbnail_size();
		foreach ( $attachment_ids as $attachment_id ) {
			list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );

			printf(
				'<div class="gallery_image carousel-item %s">
					<img src="%s" alt="%s" />
				</div>',
				esc_attr( $thumbnail_size ),
				esc_attr( $thumb_src ),
				esc_attr( get_the_title() )
			);
		}
		?>
		</div><!-- .carousel-items -->
	</div><!-- .gallery-format.et-slider -->
	<?php echo $score_bar; ?>
	<?php

	}
} else if ( et_has_post_format( 'video' ) ) {
	$video_urls = get_post_meta( get_the_ID(), '_video_format_urls', true );

	if ( !empty( $video_urls ) ) {
	?>
	<div class="video-format">
		<?php
		$video_embed = extra_get_video_embed( $video_urls );

		if ( $video_embed ) {
			echo $video_embed;

			if ( has_post_thumbnail() ) {
				$thumbnail_id = get_post_thumbnail_id();
				$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, extra_get_column_thumbnail_size() );
				if ( isset( $thumbnail_src[0] ) ) {
				?>
				<div class="video-overlay" style="background-image: url(<?php echo esc_attr( esc_url( $thumbnail_src[0] ) ); ?>);">
					<div class="video-overlay-hover">
						<a href="#" class="video-play-button"></a>
					</div>
				</div>
				<?php
				}
			}
		}

		?>
		<?php echo $score_bar; ?>
	</div>
	<?php
	}
} else if ( et_has_post_format( 'audio' ) ) {
	$thumbnail_id     = get_post_thumbnail_id();
	$background_color = get_post_meta( get_the_ID(), '_audio_format_background_color', true );
	$title            = get_post_meta( get_the_ID(), '_audio_format_title', true );
	$sub_title        = get_post_meta( get_the_ID(), '_audio_format_sub_title', true );
	$audio_src        = get_post_meta( get_the_ID(), '_audio_format_file_url', true );

	if ( $audio_src ) {

	?>
	<div class="audio-format">
		<?php
		$style = '';

		if ( !empty( $thumbnail_id ) ) {
			list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $thumbnail_id, extra_get_column_thumbnail_size() );
			$style = 'background-image:url(\'' . $thumb_src . '\');';
		} else if ( !empty( $background_color ) ) {
			$style = 'background-color:' . $background_color . ';';
		}

		$style_attr = ' style="' . esc_attr( $style ) .'"';
		?>
		<div class="audio-wrapper" <?php echo $style_attr; ?>>

			<div class="audio-titles">
				<?php

				if ( !empty( $title ) ) {
					echo '<h2 class="title">' . esc_html( $title ) . '</h2>';
				}

				if ( !empty( $sub_title ) ) {
					echo '<h3 class="sub_title">' . esc_html( $sub_title ) . '</h3>';
				}

				?>
			</div>
		<?php
			$args = array(
				'src' => $audio_src,
			);
			echo wp_audio_shortcode( $args );
		?>
		</div>
	</div>
	<?php

	}
} else {
	if ( get_post_thumbnail_id() ) {
		$thumb_args = !empty( $thumb_args ) ? $thumb_args : array();
		extra_the_post_featured_image( $thumb_args );
		echo $score_bar;
	}
}

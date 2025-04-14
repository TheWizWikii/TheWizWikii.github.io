<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function extra_the_post_categories( $post_id = 0, $before = null, $sep = ', ', $after = '' ) {
	echo extra_get_the_post_categories( $post_id, $before, $sep, $after );
}

function extra_get_the_post_categories( $post_id = 0, $before = null, $sep = ', ', $after = '' ) {
	return get_the_term_list( $post_id, 'category', $before, $sep, $after );
}

function extra_get_the_post_date( $post = null, $date_format = '' ) {
	$date_format = !empty( $date_format ) ? $date_format : get_option( 'date_format' );
	return '<span class="updated">' . get_the_time( $date_format, $post ) . '</span>';
}

function extra_the_post_date( $post = null ) {
	echo extra_get_the_post_date( $post );
}

function extra_get_the_post_score_bar( $args = array() ) {
	$default_args = array(
		'post_id' => 0,
	);

	$args = wp_parse_args( $args, $default_args );

	$post_id = $args['post_id'] ? $args['post_id'] : get_the_ID();

	$color = extra_get_post_category_color( $post_id );
	$breakdown_score = get_post_meta( $post_id, '_post_review_box_breakdowns_score', true );

	if ( false === extra_post_review() ) {
		return;
	}

	$bar = sprintf('<span class="score-bar" style="width:%1$d%%;background-color:%2$s;"><span class="score-text">%3$s</span></span>',
		esc_attr( max( 9, intval( $breakdown_score ) ) ),
		esc_attr( $color ),
		sprintf( esc_html__( 'Score %1$d%%', 'extra' ), intval( $breakdown_score ) )
	);
	return $bar;
}

function extra_post_review( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	$review = array();

	$review_breakdowns = (array) get_post_meta( $post_id, '_post_review_box_breakdowns', true );
	if ( 1 === count( $review_breakdowns ) && empty( $review_breakdowns[0]['title'] ) && empty( $review_breakdowns[0]['rating'] ) ) {
		return false;
	}

	$review['title'] = get_post_meta( $post_id, '_post_review_box_title', true );
	$review['summary'] = get_post_meta( $post_id, '_post_review_box_summary', true );
	$review['summary_title'] = get_post_meta( $post_id, '_post_review_box_summary_title', true );
	$review['breakdowns'] = $review_breakdowns;
	$review['score'] = get_post_meta( $post_id, '_post_review_box_breakdowns_score', true );
	$review['score_title'] = get_post_meta( $post_id, '_post_review_box_score_title', true );
	return $review;
}

function extra_the_post_comments_link( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	echo extra_get_the_post_comments_link( $post_id );
}

function extra_get_the_post_comments_link( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return sprintf(
		'<a class="comments-link" href="%s">%d <span title="%s" class="comment-bubble post-meta-icon"></span></a>',
		esc_attr( get_the_permalink( $post_id ) . '#comments' ),
		esc_html( get_comments_number( $post_id ) ),
		esc_attr( __( 'comment count', 'extra' ) )
	);
}

function extra_the_post_featured_image( $args = array() ) {
	echo extra_get_the_post_featured_image( $args );
}

function extra_get_the_post_featured_image( $args = array() ) {
	$default_args = array(
		'size'      => 'extra-image-huge',
		'a_class'   => array('featured-image'),
		'img_after' => extra_get_the_post_score_bar(),
	);

	$args = wp_parse_args( $args, $default_args );

	return et_extra_get_post_thumb( $args );
}

function et_extra_get_post_thumb( $args = array() ) {
	$default_args = array(
		'post_id'                    => 0,
		'size'                       => '',
		'height'                     => 50,
		'width'                      => 50,
		'title'                      => '',
		'link_wrapped'               => true,
		'permalink'                  => '',
		'a_class'                    => array(),
		'img_class'                  => array(),
		'img_style'                  => '',
		'img_after'                  => '', // Note: this value is not escaped/sanitized, and should be used for internal purposes only, not any user input
		'post_format_thumb_fallback' => false,
		'fallback'                   => '',
		'thumb_src'                  => '',
		'return'                     => 'img',
	);

	$args = wp_parse_args( $args, $default_args );

	$post_id = $args['post_id'] ? $args['post_id'] : get_the_ID();
	$permalink = !empty( $args['permalink'] ) ? $args['permalink'] : get_the_permalink( $post_id );
	$title = !empty( $args['title'] ) ? $args['title'] : get_the_title( $post_id );

	$width = (int) apply_filters( 'et_extra_post_thumbnail_width', $args['width'] );
	$height = (int) apply_filters( 'et_extra_post_thumbnail_height', $args['height'] );
	$size = !empty( $args['size'] ) ? $args['size'] : array( $width, $height );
	$thumb_src = $args['thumb_src'];
	$img_style = $args['img_style'];

	$thumbnail_id = get_post_thumbnail_id( $post_id );

	if ( !$thumbnail_id && !$args['thumb_src'] ) {
		if ( $args['post_format_thumb_fallback'] ) {
			$post_format = et_get_post_format();
			if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
				$thumb_src = et_get_post_format_thumb( $post_format, 'thumb' );
			} else {
				$thumb_src = et_get_post_format_thumb( 'text', 'thumb' );
			}
		} else if ( !empty( $args['fallback'] ) ) {
			return $args['fallback'];
		} else {
			$thumb_src = et_get_post_format_thumb( 'text', 'icon' );
		}
	}

	if ( $thumbnail_id ) {
		list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $thumbnail_id, $size );
	}

	if ( 'thumb_src' === $args['return'] ) {
		return $thumb_src;
	}

	$image_output = sprintf(
		'<img src="%1$s" alt="%2$s"%3$s %4$s/>%5$s',
		esc_attr( $thumb_src ),
		esc_attr( $title ),
		( !empty( $args['img_class'] ) ? sprintf( ' class="%s"', esc_attr( implode( ' ', $args['img_class'] ) ) ) : '' ),
		( !empty( $img_style ) ? sprintf( ' style="%s"', esc_attr( $img_style ) ) : '' ),
		$args['img_after']
	);

	if ( $args['link_wrapped'] ) {
		$image_output = sprintf(
			'<a href="%1$s" title="%2$s"%3$s%5$s>
				%4$s
			</a>',
			esc_attr( $permalink ),
			esc_attr( $title ),
			( !empty( $args['a_class'] ) ? sprintf( ' class="%s"', esc_attr( implode( ' ', $args['a_class'] ) ) ) : '' ),
			$image_output,
			( !empty( $img_style ) ? sprintf( ' style="%s"', esc_attr( $img_style ) ) : '' )
		);
	}

	return $image_output;
}

function et_thumb_as_style_background() {
	$thumb_src = et_extra_get_post_thumb( array(
		'size'   => extra_get_column_thumbnail_size(),
		'return' => 'thumb_src',
	) );

	if ( ! empty( $thumb_src ) ) {
		echo 'style="background-image: url(' . esc_attr( $thumb_src ) . ');"';
	}

	return;
}

function et_get_post_format_thumb( $post_format, $size =  'icon' ) {
	$template_dir = get_template_directory_uri();

	$size = 'icon' == $size ? 'icon' : 'thumb';

	if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
		$img = 'post-format-' . $size . '-' . $post_format . '.svg';
	} else {
		$img = 'post-format-' . $size . '-text.svg';
	}

	return $template_dir . '/images/' . $img;
}

function et_get_gallery_post_format_thumb() {
	$attachment_ids = get_post_meta( get_the_ID(), '_gallery_format_attachment_ids', true );
	$attachment_ids = explode( ',', $attachment_ids );
	if ( count( $attachment_ids ) ) {
		foreach ( $attachment_ids as $attachment_id ) {
			list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $attachment_id, 'full' );
			return $thumb_src;
		}
	} else {
		return et_get_post_format_thumb( 'gallery', 'thumb' );
	}
}

function extra_get_post_rating_stars( $post_id = 0 ) {
	$rating = extra_get_post_rating( $post_id );
	$output = '<span class="rating-stars" title="' . esc_attr( sprintf( __( 'Rating: %0.2f', 'extra' ), $rating ) ) .'">' . extra_make_mini_stars( $rating ) . '</span>';
	return $output;
}

function extra_the_post_rating_stars( $post_id = 0 ) {
	echo extra_get_post_rating_stars( $post_id );
}

function extra_the_post_rating_stars_with_rating_count( $post_id = 0 ) {
	$rating = extra_get_user_post_rating( $post_id );
	$rating_count = extra_get_post_ratings_count();
	printf(
		'<span class="post-rating-stars">%s<span class="rating">%s</span></span>',
		extra_make_mini_stars( $rating ),
		esc_html( sprintf( _n( '%d rating', '%d ratings', $rating_count ), $rating_count ) )
	);
}

function extra_is_post_rating_enabled( $post_id = 0 ) {
	if ( false === et_get_post_meta_setting( 'all', 'rating_stars' ) ) {
		return false;
	}

	if ( is_single() && false === et_get_post_meta_setting( 'post', 'rating_stars' ) ) {
		return false;
	}

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$hide_rating = get_post_meta( $post_id, '_post_extra_rating_hide', true );

	$has_post_rating = $hide_rating ? false : true;

	return apply_filters( 'extra_is_post_rating_enabled', $has_post_rating, $post_id );
}

function is_post_extra_element_enabled( $element = false, $post_id = 0 ) {
	$allowed_elements = array(
		'title_meta',
		'featured_image',
	);

	if ( ! $element || ! in_array( $element, $allowed_elements ) ) {
		return false;
	}

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	// by default, don't hide element
	$hide_element_setting = false;

	if ( is_singular() ) {
		$hide_element_setting = get_post_meta( $post_id, "_post_extra_{$element}_hide_single", true );
	}

	$has_element = $hide_element_setting ? false : true;

	return apply_filters( "is_post_extra_{$element}_enabled", $has_element, $post_id );
}

function is_post_extra_title_meta_enabled( $post_id = 0 ) {
	return is_post_extra_element_enabled( 'title_meta' );
}

function is_post_extra_featured_image_enabled( $post_id = 0 ) {
	return is_post_extra_element_enabled( 'featured_image' );
}

function post_extra_class( $classes ) {
	$flexible_elements = apply_filters( 'post_extra_class_flexible_elements', array(
		'title_meta',
		'featured_image',
	) );

	if ( ! empty( $flexible_elements ) ) {
		foreach ( $flexible_elements as $element ) {
			if ( ! is_post_extra_element_enabled( $element ) ) {
				$classes[] = "et-doesnt-have-{$element}";
			}
		}
	}

	return $classes;
}

add_filter( 'post_class', 'post_extra_class' );

function extra_rating_stars_display( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	if ( $rating = extra_get_user_post_rating( $post_id ) ) {
		$output = '<p id="rate-title" class="rate-title">' . esc_html__( 'Your Rating:', 'extra' ) . '</p>';
		$output .= '<div id="rated-stars">' . extra_make_fixed_stars( $rating ) . '</div>';

	} else {
		$title = esc_html__( 'Rate:', 'extra' );

		$output = '<p id="rate-title" class="rate-title">' . esc_html__( 'Rate:', 'extra' ) . '</p>';
		$output .= '<div id="rating-stars"></div>';
		$output .= '<input type="hidden" id="post_id" value="' . $post_id . '" />';
	}

	echo $output;
}

function extra_make_fixed_stars( $rating ) {
	$images_base = get_template_directory_uri() . '/images/';

	$output = '';
	for ( $x = 1; $x <= 5; $x++ ) {

		if ( $x <= $rating ) {
			$class = 'star-on';
			$icon = 'full';
		} elseif ( ( $x - 0.5 ) <= $rating ) {
			$class = 'star-on star-half';
			$icon = 'half-full';
		} else {
			$class = 'star-off';
			$icon = 'full';
		}

		$src = $images_base . 'star-' . $icon . '.svg';
		$output .= '<img src="' . $src . '" class="rating-star '. $class . ' rating-star-' . $x . '" alt="' . esc_attr__( "Star", "extra" ) . '" />' . "\n";
	}

	return $output;
}

function extra_make_mini_stars( $rating ) {
	$output = '';
	for ( $x = 1; $x <= 5; $x++ ) {

		if ( $x <= $rating ) {
			$class = 'rating-star-on';
		} elseif ( ( $x - 0.5 ) <= $rating ) {
			$class = 'rating-star-half';
		} else {
			$class = 'rating-star-empty';
		}
		$output .= '<span class="post-meta-icon rating-star '. $class . ' rating-star-' . $x . '"></span>'."\n";
	}

	return $output;
}

function extra_sidebar_class() {
	echo extra_get_sidebar_class();
}

function extra_sidebar_body_class( $classes ){
	$classes = array_merge( $classes, explode( ' ', extra_get_sidebar_class() ) );
	return $classes;
}

add_filter( 'body_class', 'extra_sidebar_body_class', 10, 2 );

function extra_get_sidebar_class() {
	if ( 'product' == get_query_var( 'post_type' ) ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_singular() ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_archive() ) {
		if ( $layout_id = extra_get_tax_layout_id() ) {
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}

		// Override the above value (if any) set based on $layout_id.
		if ( is_tax( 'product_cat' ) ) {
			$sidebar_location = et_get_option( 'woocommerce_sidebar_location', extra_global_sidebar_location() );
		}
	} else if ( is_home() && et_extra_show_home_layout() ) {
		if ( $layout_id = extra_get_home_layout_id() ) {
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	}

	if ( empty( $sidebar_location ) ) {
		if ( 'product' == get_query_var( 'post_type' ) ) {
			$sidebar_location = et_get_option( 'woocommerce_sidebar_location', extra_global_sidebar_location() );
		} else {
			$sidebar_location = extra_global_sidebar_location();
		}
	}

	// Project's sidebar location overrides
	if ( is_singular( EXTRA_PROJECT_POST_TYPE ) ) {
		$project_details = extra_get_project_details();

		if ( isset( $project_details['location'] ) && 'single_col' === $project_details['location'] ) {
			$sidebar_location = 'none';
		}
	}

	$class = '';
	if ( 'none' != $sidebar_location ) {
		$class .= 'with_sidebar';

		$class .= 'right' == $sidebar_location ? ' with_sidebar_right' : ' with_sidebar_left';
	}

	return $class;
}

function extra_sidebar() {
	$is_woocommerce_sidebar = 'product' === get_query_var( 'post_type' ) || is_tax( 'product_cat' ) || is_tax( 'product_tag' );

	if ( $is_woocommerce_sidebar ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar = get_post_meta( $post_id, '_extra_sidebar', true );
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_singular() ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar = get_post_meta( $post_id, '_extra_sidebar', true );
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_archive() ) {
		if ( $layout_id = extra_get_tax_layout_id() ) {
			$sidebar = get_post_meta( $layout_id, '_extra_sidebar', true );
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	} else if ( is_home() && et_extra_show_home_layout() ) {
		if ( $layout_id = extra_get_home_layout_id() ) {
			$sidebar = get_post_meta( $layout_id, '_extra_sidebar', true );
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	}

	if ( empty( $sidebar_location ) ) {
		if ( $is_woocommerce_sidebar ) {
			$sidebar_location = et_get_option( 'woocommerce_sidebar_location', extra_global_sidebar_location() );
		} else {
			$sidebar_location = extra_global_sidebar_location();
		}
	}

	if ( 'none' === $sidebar_location ) {
		return;
	}

	if ( empty( $sidebar ) ) {
		if ( $is_woocommerce_sidebar ) {
			$sidebar = et_get_option( 'woocommerce_sidebar', extra_global_sidebar() );
		} else {
			$sidebar = extra_global_sidebar();
		}
	}

	return $sidebar;
}

function extra_get_header_vars() {
	$items = array();

	$header_items = array(
		'header_social_icons',
		'header_search_field',
		'header_cart_total',
	);

	foreach ( $header_items as $header_item ) {
		$items['show_' . $header_item ] = extra_customizer_el_visible( extra_get_dynamic_selector( $header_item ) );
		$items['output_' . $header_item] = $items['show_' . $header_item ] || is_customize_preview();
	}

	$items['show_header_trending_bar'] = et_get_option( 'show_header_trending', 'on' );
	$items['output_header_trending_bar'] = $items['show_header_trending_bar'] || is_customize_preview();

	$items['header_search_field_alone'] = false;

	$items['header_cart_total_alone'] = false;

	$items['secondary_nav'] = wp_nav_menu( array(
		'theme_location' => 'secondary-menu',
		'container'      => '',
		'fallback_cb'    => '',
		'menu_class'     => 'nav',
		'menu_id'        => 'et-secondary-menu',
		'echo'           => false,
	) );

	$trending_posts = new WP_Query( apply_filters( 'extra_trending_posts_query', array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => '3',
		'orderby'        => 'comment_count',
		'order'          => 'DESC',
	) ) );
	$items['trending_posts'] = isset( $trending_posts->posts ) ? $trending_posts : false;

	$items['top_info_defined'] = false;

	$top_info_items = array(
		'show_header_social_icons',
		'secondary_nav',
		'show_header_trending_bar',
		'show_header_search_field',
		'show_header_cart_total',
	);

	$top_info_items_count = 0;
	foreach ( $top_info_items as $top_info_item ) {
		if ( !empty( $items[ $top_info_item ] ) ) {
			$top_info_items_count++;
			$items['top_info_defined'] = true;
		}
	}

	if ( 1 == $top_info_items_count ) {
		if ( !empty( $items['show_header_search_field'] ) ) {
			$items['header_search_field_alone'] = true;
			$items['show_header_search_field'] = false;
		}

		if ( !empty( $items['show_header_cart_total'] ) ) {
			$items['header_cart_total_alone'] = true;
			$items['show_header_cart_total'] = false;
		}

		if ( $items['header_search_field_alone'] || $items['header_cart_total_alone'] ) {
			$items['top_info_defined'] = false;
			add_filter( 'wp_nav_menu_items', 'extra_primary_nav_extended_items', 10, 2 );
		}
	} elseif ( is_customize_preview() ) {
		add_filter( 'wp_nav_menu_items', 'extra_primary_nav_extended_items', 10, 2 );
	}

	$items['header_style'] = et_get_option( 'header_style', 'left-right' );

	$items['header_ad'] = extra_display_ad( 'header', false );

	$header_classes = array();

	if ( !empty( $items['header_style'] ) && 'centered' == $items['header_style'] ) {
		$header_classes[] = 'centered';
	} else {
		$header_classes[] = 'left-right';
	}

	if ( !empty( $header_ad ) ) {
		$header_classes[] = 'has_headerad';
	}

	$items['header_classes'] = extra_classes( $header_classes, 'header', false );

	return $items;
}

function extra_primary_nav_extended_items( $items, $args ) {
	if ( 'primary-menu' === $args->theme_location ) {

		if ( is_customize_preview() || !empty( $args->header_search_field_alone ) ) {
			$show_search_on_primary_nav = !empty( $args->header_search_field_alone ) ? true : false;
			$items .= sprintf(
				'<li class="menu-item et-top-search-primary-menu-item" style="%s"><span id="et-search-icon" class="search-icon"></span><div class="et-top-search">%s</div></li>',
				extra_visible_display_css( $show_search_on_primary_nav, false ),
				extra_header_search_field( false )
			);
		}

		if ( is_customize_preview() || !empty( $args->header_cart_total_alone ) ) {
			$show_cart_on_primary_nav = !empty( $args->header_cart_total_alone ) ? true : false;
			$items .= sprintf(
				'<li class="menu-item et-cart-info-primary-menu-item" style="%s">%s</li>',
				extra_visible_display_css( $show_cart_on_primary_nav, false ),
				extra_header_cart_total( true, false )
			);
		}
	}

	return $items;
}

function extra_header_search_field( $echo = true ) {
	$output = sprintf(
		'<form role="search" class="et-search-form" method="get" action="%1$s">
			<input type="search" class="et-search-field" placeholder="%2$s" value="%3$s" name="s" title="%4$s" />
			<button class="et-search-submit"></button>
		</form>',
		esc_url( home_url( '/' ) ),
		esc_attr_x( 'Search', 'placeholder', 'extra' ),
		get_search_query(),
		esc_attr_x( 'Search for:', 'label', 'extra' )
	);

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function extra_header_cart_total( $no_text = false, $echo = true ) {
	ob_start();
	et_show_cart_total( array(
		'no_text' => $no_text,
	) );
	$output = ob_get_clean();

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function extra_display_stars( $score ) {
	$output = '';
	for ( $x = 0; $x < floor( $score ); $x++ ) {
		$output .= '<span class="rating-star"></span>';
	}

	if ( $score != floor( $score ) ) {
		$output .= '<span class="rating-star rating-star-half"></span>';
	}

	$leftover = 5 - floor( $score );
	if ( $leftover > 1 ) {
		for ( $x = 1; $x < $leftover; $x++ ) {
			$output .= '<span class="rating-star rating-star-empty"></span>';
		}
	}

	return $output;
}

function extra_get_post_author_link( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$post_author_id = get_post( $post_id )->post_author;
	$author = get_user_by( 'id', $post_author_id );
	$link = sprintf(
		'<a href="%1$s" class="url fn" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $author->ID, $author->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s' ), $author->display_name ) ),
		esc_html( $author->display_name )
	);
	return $link;
}

function extra_get_author_contact_methods( $user_id = 0 ) {
	$user_id = !empty( $user_id ) ? $user_id : get_the_author_meta( 'ID' );
	$author = get_userdata( $user_id );

	$methods = array();
	foreach ( wp_get_user_contact_methods( $author ) as $name => $desc ) {
		if ( !empty( $author->$name ) ) {
			$methods[$name] = array(
				'slug' => $name,
				'name' => $desc,
				'url'  => $author->$name,
			);
		}
	}

	return $methods;
}

function extra_post_share_links( $echo = true ) {
	$output = '';
	$networks = array();

	// this is backwards due to how epanel saves checkboxes values
	$excluded_networks = et_get_option( "extra_post_share_icons" );
	$excluded_networks = !empty( $excluded_networks ) ? $excluded_networks : array();
	foreach ( ET_Social_Share::get_networks() as $network ) {
		if ( !in_array( $network->slug, $excluded_networks ) ) {
			$networks[$network->slug] = $network;
		}
	}

	$permalink = get_the_permalink();
	$title = get_the_title();
	foreach ( $networks as $network ) {
		$share_url = $network->create_share_url( $permalink, $title );
		$share_title = sprintf( __( 'Share "%s" via %s', 'extra' ), $title, $network->name );

		$output .= sprintf(
			'<a href="%1$s" class="social-share-link" title="%2$s" data-network-name="%3$s" data-share-title="%4$s" data-share-url="%5$s">
				<span class="et-extra-icon et-extra-icon-%3$s et-extra-icon-background-hover" ></span>
			</a>',
			$share_url,
			esc_attr( $share_title ),
			esc_attr( $network->slug ),
			esc_attr( $title ),
			esc_attr( $permalink )
		);
		?>

		<?php
	}

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function extra_is_builder_built( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	return (bool) 'on' == get_post_meta( $post_id, '_et_builder_use_builder', true );
}

function extra_get_timeline_menu_month_groups() {
	global $wpdb;

	$month_groups = $wpdb->get_col( "SELECT DISTINCT DATE_FORMAT( {$wpdb->posts}.post_date, '%M-%Y' ) as date_slug FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = 'post' AND {$wpdb->posts}.post_status = 'publish'  ORDER BY {$wpdb->posts}.post_date desc" );
	return $month_groups;
}

function extra_get_timeline_posts( $args = array() ) {
	$default_args = array(
		'date_query'          => array(
			array(
				'after'     => array(
					'month' => date( 'm', strtotime( '12 months ago' ) ),
					'year'  => date( 'Y', strtotime( '12 months ago' ) ),
					'day'   => 1,
				),
				'inclusive' => true,
			),
		),
		'nopaging'            => true,
		'posts_per_page'      => -1,
		'post_type'           => 'post',
		'orderby'             => 'date',
		'order'               => 'desc',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	);

	$args = wp_parse_args( $args, $default_args );

	$posts = new WP_Query( $args );

	return $posts;
}

function extra_get_timeline_posts_onload() {
	// Get all posts published in the last year
	$timeline_posts = extra_get_timeline_posts();

	// Some sites don't publish posts in the last year
	if ( ! $timeline_posts->have_posts() ) {
		// Get last published post
		$last_post = get_posts( array( 'posts_per_page' => 1, 'post_status' => 'publish' ) );

		// If there's any post ever, override WP_Query object made earlier
		if ( isset( $last_post[0] ) ) {
			$post_date           = $last_post[0]->post_date;
			$post_date_timestamp = strtotime( $post_date );
			$args                = array(
				'date_query' => array(
					array(
						'after'     => array(
							'month' => '1',
							'year'  => date( 'Y', $post_date_timestamp ),
						),
						'inclusive' => true,
					),
				),
			);

			// Get all posts published in a year where last published post found
			$timeline_posts = extra_get_timeline_posts( $args );
		}
	}

	return $timeline_posts;
}

function extra_timeline_get_content() {
	if ( !isset( $_POST['timeline_nonce'] ) || !wp_verify_nonce( $_POST['timeline_nonce'], 'timeline_nonce' ) ) {
		die( -1 );
	}

	$last_month = sanitize_text_field( $_POST['last_month'] );
	$last_year = sanitize_text_field( $_POST['last_year'] );

	$before_date = strtotime( $last_month . ' '. $last_year );

	if ( isset( $_POST['through_month'] ) && isset( $_POST['through_year'] ) ) {
		$through_month = sanitize_text_field( $_POST['through_month'] );
		$through_year = sanitize_text_field( $_POST['through_year'] );
		$after_date = strtotime( $through_month . ' 1 ' . $through_year );
	} else if ( isset( $_POST['through_year'] ) ) {
		$through_year = sanitize_text_field( $_POST['through_year'] );
		$after_date = strtotime( 'January 1 ' . $through_year );
	} else {
		$after_date = strtotime( date( 'M d Y', $before_date ) . ' - 6 months' );
	}

	$args = array(
		'date_query' => array(
			array(
				'before'    => array(
					'month' => date( 'm', $before_date ),
					'year'  => date( 'Y', $before_date ),
					'day'   => 1,
				),
				'after'     => array(
					'month' => date( 'm', $after_date ),
					'year'  => date( 'Y', $after_date ),
					'day'   => 1,
				),
				'inclusive' => true,
			),
		),
	);

	$timeline_posts = extra_get_timeline_posts( $args );

	if ( $timeline_posts->have_posts() ) {
		require locate_template( 'timeline-posts-content.php' );
	}

	die();
}

add_action( 'wp_ajax_extra_timeline_get_content', 'extra_timeline_get_content' );
add_action( 'wp_ajax_nopriv_extra_timeline_get_content', 'extra_timeline_get_content' );

function extra_blog_feed_get_content() {
	if ( !isset( $_POST['blog_feed_nonce'] ) || !wp_verify_nonce( $_POST['blog_feed_nonce'], 'blog_feed_nonce' ) ) {
		die( -1 );
	}

	$to_page = absint( $_POST['to_page'] );
	$order = sanitize_text_field( $_POST['order'] );
	$orderby = sanitize_text_field( $_POST['orderby'] );
	$posts_per_page = absint( $_POST['posts_per_page'] );

	$show_featured_image = sanitize_text_field( $_POST['show_featured_image'] );
	$blog_feed_module_type = sanitize_text_field( $_POST['blog_feed_module_type'] );

	$show_author = sanitize_text_field( $_POST['show_author'] );
	$show_date = sanitize_text_field( $_POST['show_date'] );
	$date_format = sanitize_text_field( $_POST['date_format'] );
	$show_categories = sanitize_text_field( $_POST['show_categories'] );
	$categories = !empty( $_POST['categories'] ) ? array_map( 'absint', explode( ',', $_POST['categories'] ) ) : '';
	$order = sanitize_text_field( $_POST['order'] );
	$content_length = sanitize_text_field( $_POST['content_length'] );
	$hover_overlay_icon = et_sanitize_font_icon( $_POST['hover_overlay_icon'] );
	$show_more = sanitize_text_field( $_POST['show_more'] );
	$show_comments = sanitize_text_field( $_POST['show_comments'] );
	$show_rating = sanitize_text_field( $_POST['show_rating'] );
	$use_tax_query = sanitize_text_field( $_POST['use_tax_query'] );
	$tax_query = isset( $_POST['tax_query'] ) ? $_POST['tax_query'] : array();

	// This is normally set in includes/builder/shortcodes.php in et_pb_column(),
	// but since this is an ajax request, we need to pass this data along
	global $et_column_type;
	$et_column_type = sanitize_text_field( $_POST['et_column_type'] );

	$offset = ( $to_page * $posts_per_page ) - $posts_per_page;
	$post_status = array( 'publish' );

	if ( is_user_logged_in() && current_user_can( 'read_private_posts' ) ) {
		$post_status[] = 'private';
	}

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => $posts_per_page,
		'offset'         => $offset,
		'order'          => $order,
		'orderby'        => $orderby,
		'post_status'    => $post_status,
	);

	if ( !empty( $categories ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $categories,
				'operator' => 'IN',
			),
		);
	}

	if ( $use_tax_query === '1' && ! empty( $tax_query ) ) {
		$valid_taxonomies = get_taxonomies();
		$valid_fields     = array( 'term_id', 'name', 'slug', 'term_taxonomy_id' );
		$valid_operators  = array( 'IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS' );
		$sanitized_terms  = array();

		foreach ( $tax_query as $taxonomy ) {
			if ( isset( $taxonomy['taxonomy'] ) && 'category' === $taxonomy['taxonomy'] ) {
				continue;
			}

			if ( ! isset( $taxonomy['taxonomy'] ) || ! in_array( $taxonomy['taxonomy'], $valid_taxonomies ) ) {
				continue;
			}

			if ( ! isset( $taxonomy['field'] ) || ! in_array( $taxonomy['field'], $valid_fields ) ) {
				continue;
			}

			if ( ! isset( $taxonomy['operator'] ) || ! in_array( $taxonomy['operator'], $valid_operators ) ) {
				continue;
			}

			if ( ! isset( $taxonomy['terms'] ) || ! is_array( $taxonomy['terms']) || empty( $taxonomy['terms'] ) ) {
				continue;
			}

			foreach ( $taxonomy['terms'] as $taxonomy_term ) {
				$sanitized_terms[] = sanitize_text_field( $taxonomy_term );
			}

			$args['tax_query'][] = array(
				'taxonomy' => sanitize_text_field( $taxonomy['taxonomy'] ),
				'field'    => sanitize_text_field( $taxonomy['field'] ),
				'terms'    => $sanitized_terms,
				'operator' => sanitize_text_field( $taxonomy['operator'] ),
			);
		}

		if ( 1 < count( $args['tax_query'] ) ) {
			$args['tax_query']['relation'] = 'AND';
		}
	}

	$module_posts = new WP_Query( $args );

	$page = $to_page;
	if ( $module_posts->have_posts() ) {
		require locate_template( 'module-posts-blog-feed-loop.php' );
	}

	die();
}

add_action( 'wp_ajax_extra_blog_feed_get_content', 'extra_blog_feed_get_content' );
add_action( 'wp_ajax_nopriv_extra_blog_feed_get_content', 'extra_blog_feed_get_content' );

function extra_get_portfolio_projects( $args = array() ) {
	$default_args = array(
		'post_type'      => EXTRA_PROJECT_POST_TYPE,
		'nopaging'       => true,
		'posts_per_page' => -1,
	);

	$args = wp_parse_args( $args, $default_args );

	$portfolio_options = extra_get_portfolio_options();
	if ( !empty( $portfolio_options['project_categories'] ) ) {
		$term_ids = array();
		foreach ( $portfolio_options['project_categories'] as $category ) {
			$term_ids[] = $category->term_id;
		}
	}

	if ( !empty( $term_ids ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => EXTRA_PROJECT_CATEGORY_TAX,
				'field'    => 'id',
				'terms'    => $term_ids,
				'operator' => 'IN',
			),
		);
	}

	$projects = new WP_Query( $args );

	return $projects;
}

function extra_get_portfolio_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$project_categories = get_post_meta( $post_id, '_portfolio_project_categories', true );

	$args = array(
		'include' => $project_categories,
	);

	$options['project_categories'] = get_terms( EXTRA_PROJECT_CATEGORY_TAX, $args );

	$options['hide_title'] = get_post_meta( $post_id, '_portfolio_hide_title', true );
	$options['hide_categories'] = get_post_meta( $post_id, '_portfolio_hide_categories', true );

	return $options;
}

function extra_get_portfolio_project_category_classes() {
	$categories = extra_get_the_project_categories();

	$classes = "";

	if ( !empty( $categories ) ) {
		$classes_array = array();

		foreach ( $categories as $category ) {
			$classes_array[] = 'project_category_' . $category->slug;
		}

		$classes = implode( ' ', $classes_array );

	}

	return $classes;
}

function extra_get_authors_page_options( $post_id = 0 ) {
	global $wp_version;

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$authors = get_post_meta( $post_id, '_authors_page_authors', true );

	$authors_all = get_post_meta( $post_id, '_authors_page_authors_all', true );

	$query_args = array(
		'order'   => 'ASC',
		'orderby' => 'display_name',
		'include' => !empty( $authors_all ) ? array() : $authors,
	);

	// Alternative for deprecated `who` property and backward compatibility.
	if ( version_compare( $wp_version, '5.9-beta', '>=' ) ) {
		$query_args['capability'] = array( 'edit_posts' );
	} else {
		$query_args['who'] = 'authors';
	}

	$options['authors'] = get_users( $query_args );

	return $options;
}

function extra_get_blog_feed_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$options_fields = array(
		'display_style',
		'categories',
		'posts_per_page',
		'order',
		'orderby',
		'show_author',
		'show_categories',
		'show_ratings',
		'show_featured_image',
		'content_length',
		'show_date',
		'date_format',
		'show_comment_count',
	);

	foreach ( $options_fields as $options_field ) {
		$options[$options_field] = get_post_meta( $post_id, '_blog_feed_page_' . $options_field, true );
	}

	$options['border_color_style'] = '';

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => isset( $options['posts_per_page'] ) && is_numeric( $options['posts_per_page'] ) ? $options['posts_per_page'] : 5,
		'order'          => $options['order'],
		'orderby'        => $options['orderby'],
	);

	$paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
	$args['paged'] = $paged;

	if ( 'rating' == $options['orderby'] ) {
		$args['orderby'] = 'meta_value_num';
		$args['meta_key'] = '_extra_rating_average';
	}

	if ( !empty( $options['categories'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => array_map( 'absint', explode( ',', $options['categories'] ) ),
				'operator' => 'IN',
			),
		);

		if ( 'standard' == $options['display_style'] && false === strpos( $options['categories'], ',' ) ) {
			$color = extra_get_category_color( $options['categories'] );
			$options['border_color_style'] = esc_attr( sprintf( 'border-color:%s;', $color ) );
			$options['category_color'] = $color;
		}
	}

	// Automatically add sgiw comment count's default value to empty variable for backward compatibility
	if ( '' === $options['show_comment_count'] ) {
		$options['show_comment_count'] = '1';
	}

	wp_reset_postdata();

	$options['posts_query'] = new WP_Query( $args );

	return $options;
}

function extra_get_sitemap_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$checked_sections = get_post_meta( $post_id, '_sitemap_page_sections', true );

	if ( !empty( $checked_sections ) ) {
		$checked_sections = explode( ',', $checked_sections );
	} else {
		$checked_sections = array();
	}

	$sections = array(
		'pages'        => esc_html__( 'Pages', 'extra' ),
		'categories'   => esc_html__( 'Categories', 'extra' ),
		'tags'         => esc_html__( 'Tags', 'extra' ),
		'recent_posts' => esc_html__( 'Recent Posts', 'extra' ),
		'archives'     => esc_html__( 'Archives', 'extra' ),
		'authors'      => esc_html__( 'Authors', 'extra' ),
	);

	$options['sections'] = array();
	foreach ( $checked_sections as $checked_section ) {
		$options['sections'][$checked_section] = $sections[$checked_section];
	}

	$page_section_option_keys = array(
		'pages_exclude',
		'categories_count',
		'authors_include',
		'archives_limit',
		'archives_count',
		'recent_posts_limit',
	);

	$options['page_section_options'] = array();
	foreach ( $page_section_option_keys as $page_section_option_key ) {
		$options['page_section_options'][ $page_section_option_key ] = get_post_meta( $post_id, '_sitemap_page_' . $page_section_option_key, true );
	}

	return $options;
}

function extra_get_sitemap_page_section_items( $section, $options ) {
	$items = '';
	switch( $section ) {
		case 'pages':
			$sortby = 'menu_order, post_title';
			$exclude = !empty( $options['pages_exclude'] ) ? $options['pages_exclude'] : '';
			$items = wp_list_pages( array(
				'title_li'    => '',
				'echo'        => 0,
				'sort_column' => $sortby,
				'exclude'     => $exclude,
			));
			break;
		case 'categories':
			$count = !empty( $options['categories_count'] ) ? $options['categories_count'] : '0';
			$items = wp_list_categories(array(
				'show_count'   => $count,
				'hierarchical' => '1',
				'echo'         => '0',
				'title_li'     => false,
			));
			break;
		case 'tags':
			$tags = get_terms( 'post_tag', array(
				'orderby' => 'count',
				'order'   => 'DESC',
			));
			foreach ( $tags as $tag ) {

				$link = get_term_link( intval( $tag->term_id ), $tag->taxonomy );
				$name = $tag->name;

				$items .= '<li><a href="' . esc_url( $link ) .'">' . esc_html( $name ) . '</a></li>';
			}
			break;
		case 'authors':
			global $wp_version;

			$authors = !empty( $options['authors_include'] ) ? $options['authors_include'] : '';

			$query_args = array(
				'order'   => 'ASC',
				'orderby' => 'display_name',
				'include' => $authors,
			);

			// Alternative for deprecated `who` property and backward compatibility.
			if ( version_compare( $wp_version, '5.9-beta', '>=' ) ) {
				$query_args['capability'] = array( 'edit_posts' );
			} else {
				$query_args['who'] = 'authors';
			}

			$authors = get_users( $query_args );

			foreach ( $authors as $author ) {
				$items .= sprintf(
					'<li><a href="%s" title="%s" rel="author">%s</a></li>',
					esc_url( get_author_posts_url( $author->ID ) ),
					esc_attr( sprintf( __( 'Posts By: %s', 'extra' ), $author->display_name ) ),
					esc_html( $author->display_name )
				);
			}
			break;
		case 'archives':
			$count = !empty( $options['archives_count'] ) ? $options['archives_count'] : '0';
			$limit = !empty( $options['archives_limit'] ) ? $options['archives_limit'] : '12';
			$items = wp_get_archives( array(
				'type'            => 'monthly',
				'show_post_count' => $count,
				'limit'           => $limit,
				'echo'            => '0',
			) );
			break;
		case 'recent_posts':
			$posts_per_page = !empty( $options['recent_posts_limit'] ) ? $options['recent_posts_limit'] : 10;
			$args = array(
				'post_type'      => 'post',
				'status'         => 'publish',
				'posts_per_page' => $posts_per_page,
				'order'          => 'date',
				'orderby'        => 'DESC',
			);

			$posts = new WP_Query( $args );

			foreach ( $posts->posts as $post ) {
				$items .= sprintf(
					'<li><a href="%s" title="%s">%s</a></li>',
					esc_url( get_the_permalink( $post->ID ) ),
					esc_attr( get_the_title( $post->ID ) ),
					esc_html( get_the_title( $post->ID ) )
				);
			}

			wp_reset_postdata();

			break;
	}

	return $items;
}

function extra_accent_color() {
	echo esc_attr( extra_global_accent_color() );
}

function extra_contact_form_submit() {
	$error = false;

	if ( !isset( $_POST['action'] ) || 'extra_contact_form_submit' != $_POST['action'] ) {
		return array();
	}

	if ( !isset( $_POST['nonce_extra_contact_form'] ) || !wp_verify_nonce( $_POST['nonce_extra_contact_form'], 'extra-contact-form' ) ) {
		$message = array(
			'message' => esc_html__( 'Form submission error, please refresh and try again.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( empty( $_POST['contact_name'] ) ) {
		$message = array(
			'message' => esc_html__( 'Name field cannot be empty.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( empty( $_POST['contact_email'] ) ) {
		$message = array(
			'message' => esc_html__( 'Email field cannot be empty.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( ! is_email( $_POST['contact_email'] ) ) {
		$message = array(
			'message' => esc_html__( 'Please enter a valid email address.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( !$error ) {
		$contact_page_options = extra_get_contact_page_options();

		$name = stripslashes( sanitize_text_field( $_POST['contact_name'] ) );
		$email = sanitize_email( $_POST['contact_email'] );

		$email_to = !empty( $contact_page_options['email'] ) ? $contact_page_options['email'] : get_site_option( 'admin_email' );

		$email_to = apply_filters( 'extra_contact_page_email_to', $email_to );

		$email_to = sanitize_email( $email_to );

		$subject = sprintf(
			__( 'New Message From %1$s%2$s: %3$s', 'extra' ),
			sanitize_text_field( get_option( 'blogname' ) ),
			( '' !== $contact_page_options['title'] ? sprintf( esc_html_x( ' - %s', 'contact form title separator', 'extra' ), sanitize_text_field( $contact_page_options['title'] ) ) : '' ),
			sanitize_text_field( $_POST['contact_subject'] )
		);

		$message = stripslashes( wp_strip_all_tags( $_POST['contact_message'] ) );

		$headers  = 'From: ' . $name . ' <' . $email . '>' . "\r\n";
		$headers .= 'Reply-To: ' . $name . ' <' . $email . '>';
		apply_filters( 'et_contact_page_headers', $headers, $name, $email );

		wp_mail( $email_to, $subject, $message, $headers );

		$message = array(
			'message' => esc_html__( 'Thanks for contacting us.', 'extra' ),
			'type'    => 'success',
		);
	}

	if ( empty( $message ) ) {
		$message = array(
			'message' => esc_html__( 'There was a problem, please try again.', 'extra' ),
			'type'    => 'error',
		);
	}

	return $message;
}

function extra_contact_form_submit_ajax(){
	$message = extra_contact_form_submit();
	exit( json_encode( $message ) );
}

add_action( 'wp_ajax_extra_contact_form_submit', 'extra_contact_form_submit_ajax' );
add_action( 'wp_ajax_nopriv_extra_contact_form_submit', 'extra_contact_form_submit_ajax' );

function extra_get_contact_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$options['title'] = get_post_meta( $post_id, '_contact_form_title', true );
	$options['email'] = get_post_meta( $post_id, '_contact_form_email', true );
	$options['map_zoom'] = get_post_meta( $post_id, '_contact_form_map_zoom', true );
	$options['map_lat'] = get_post_meta( $post_id, '_contact_form_map_address_lat', true );
	$options['map_lng'] = get_post_meta( $post_id, '_contact_form_map_address_lng', true );

	return $options;
}

function extra_ajax_loader_img( $echo = true ) {
	$img = '<img src="' . esc_url( get_template_directory_uri() ) .'/images/pagination-loading.gif" alt="' . esc_attr__( "Loading", "extra" ) . '" />';
	if ( $echo ) {
		echo $img;
	} else {
		return $img;
	}
}

function extra_get_video_embed( $video_urls ) {
	if ( version_compare( $GLOBALS['wp_version'], '5.3', '<' ) ) {
		require_once ABSPATH . WPINC . '/class-oembed.php';
	} else {
		require_once ABSPATH . WPINC . '/class-wp-oembed.php';
	}

	$video_sources = '';
	$video_urls    = explode( ',', $video_urls );
	$local_video   = array();
	$video_index   = 0;

	if ( ! empty( $video_urls ) ) {
		foreach ( $video_urls as $video_url ) {
			$video_index++;

			$video_url = esc_url( $video_url );

			$oembed_args = array(
				'discover' => false,
			);

			$oembed = _wp_oembed_get_object();

			$provider = $oembed->get_provider( $video_url, $oembed_args );

			if ( ! empty( $provider ) ) {
				if ( ! is_singular() && $video_index > 1 ) {
					continue;
				}

				$video_sources .= $oembed->get_html( $video_url, $oembed_args );
			} else {
				$type = wp_check_filetype( $video_url, wp_get_mime_types() );

				if ( !empty( $type['type'] ) ) {
					$local_video[] = sprintf( '<source type="%s" src="%s" />',
						esc_attr( $type['type'] ),
						esc_attr( $video_url )
					);
				}
			}
		}

		if ( ! empty( $local_video ) ) {
			$video_sources = sprintf( '<video controls>%s</video>',
				implode( '', $local_video )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}

	return $video_sources;
}

function et_get_post_meta_settings( $type = 'all' ) {
	$default_options = array(
		'author',
		'date',
		'categories',
		'comments',
		'rating_stars',
	);

	switch ( $type ) {
		case 'all':
			$post_meta_options = (array) et_get_option( 'extra_postinfo1', $default_options );
			break;

		case 'post':
			$post_meta_options = (array) et_get_option( 'extra_postinfo2', $default_options );
			break;

		default:
			$post_meta_options = $default_options;
			break;
	}

	$meta_args = array(
		'author_link'    => in_array( 'author', $post_meta_options ),
		'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
		'post_date'      => in_array( 'date', $post_meta_options ),
		'categories'     => in_array( 'categories', $post_meta_options ),
		'comment_count'  => in_array( 'comments', $post_meta_options ),
		'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
	);

	return apply_filters( 'et_get_post_meta_settings', $meta_args );
}

function et_get_post_meta_setting( $type = 'all', $option = 'author_link' ) {
	$settings = et_get_post_meta_settings( $type );

	$setting = isset( $settings[ $option ] ) ? $settings[ $option ] : true;

	return apply_filters( 'et_get_post_meta_setting_' . $option, $setting );
}

function extra_display_archive_post_meta() {
	$post_meta_options = (array) et_get_option(
		'extra_postinfo1',
		array(
			'author',
			'date',
			'categories',
			'comments',
			'rating_stars',
		)
	);

	$meta_args = array(
		'author_link'    => in_array( 'author', $post_meta_options ),
		'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
		'post_date'      => in_array( 'date', $post_meta_options ),
		'categories'     => in_array( 'categories', $post_meta_options ),
		'comment_count'  => in_array( 'comments', $post_meta_options ),
		'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
	);

	return et_extra_display_post_meta( $meta_args );
}

function extra_display_single_post_meta() {
	$post_meta_options = et_get_option( 'extra_postinfo2', array(
		'author',
		'date',
		'categories',
		'comments',
		'rating_stars',
	) );

	$meta_args = array(
		'author_link'    => in_array( 'author', $post_meta_options ),
		'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
		'post_date'      => in_array( 'date', $post_meta_options ),
		'categories'     => in_array( 'categories', $post_meta_options ),
		'comment_count'  => in_array( 'comments', $post_meta_options ),
		'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
	);

	return et_extra_display_post_meta( $meta_args );
}

function et_extra_display_post_meta( $args = array() ) {
	$default_args = array(
		'post_id'        => get_the_ID(),
		'author_link'    => true,
		'author_link_by' => et_get_safe_localization( __( 'by %s', 'extra' ) ),
		'post_date'      => true,
		'date_format'    => et_get_option( 'extra_date_format', '' ),
		'categories'     => true,
		'comment_count'  => true,
		'rating_stars'   => true,
	);

	$args = wp_parse_args( $args, $default_args );

	$meta_pieces = array();

	if ( $args['author_link'] ) {
		$meta_pieces[] = sprintf( $args['author_link_by'], extra_get_post_author_link( $args['post_id'] ) );
	}

	if ( $args['post_date'] ) {
		$meta_pieces[] = extra_get_the_post_date( $args['post_id'], $args['date_format'] );
	}

	if ( $args['categories'] ) {
		$meta_piece_categories = extra_get_the_post_categories( $args['post_id'] );
		if ( !empty( $meta_piece_categories ) ) {
			$meta_pieces[] = $meta_piece_categories;
		}
	}

	if ( $args['comment_count'] ) {
		$meta_piece_comments = extra_get_the_post_comments_link( $args['post_id'] );
		if ( !empty( $meta_piece_comments ) ) {
			$meta_pieces[] = $meta_piece_comments;
		}
	}

	if ( $args['rating_stars'] && extra_is_post_rating_enabled( $args['post_id'] ) ) {
		$meta_piece_rating_stars = extra_get_post_rating_stars( $args['post_id'] );
		if ( !empty( $meta_piece_rating_stars ) ) {
			$meta_pieces[] = $meta_piece_rating_stars;
		}
	}

	$output = implode( ' | ', $meta_pieces );

	return $output;
}

function extra_get_de_buildered_content() {
	$content = get_the_content( '' );
	$content = apply_filters( 'the_content', $content );
	$content = wp_strip_all_tags( $content, false );
	$content = wpautop( $content );

	return $content;
}

function extra_get_category_color( $term_id ) {
	return et_get_childmost_taxonomy_meta( $term_id, 'color', true, extra_global_accent_color() );
}

function extra_get_post_category_color( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$categories = wp_get_post_categories( $post_id );

	$color = '';
	if ( !empty( $categories ) ) {
		$first_category_id = $categories[0];
		if ( function_exists( 'et_get_childmost_taxonomy_meta' ) ) {
			$color = et_get_childmost_taxonomy_meta( $first_category_id, 'color', true, extra_global_accent_color() );
		} else {
			$color = extra_global_accent_color();
		}

	}
	return $color;
}

function extra_get_post_related_posts() {
	$post_id = get_the_ID();
	$terms = get_the_terms( $post_id, 'category' );

	$term_ids = array();
	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;
		}
	}

	$related_posts = new WP_Query( array(
		'tax_query'      => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $term_ids,
				'operator' => 'IN',
			),
		),
		'post_type'      => 'post',
		'posts_per_page' => '4',
		'orderby'        => 'rand',
		'post__not_in'   => array( $post_id ),
	) );

	if ( $related_posts->have_posts() ) {
		return $related_posts;
	} else {
		return false;
	}
}

function extra_get_column_thumbnail_size() {
	global $et_column_type;

	if ( is_singular( 'post' ) || is_singular( EXTRA_PROJECT_POST_TYPE ) || is_page() ) {
		$size = 'extra-image-huge';
	} else {
		switch ( $et_column_type ) {
			case '4_4':
				$size = 'extra-image-huge';
				break;
			case '1_2':
				$size = 'extra-image-medium';
				break;
			case '1_3':
				$size = 'extra-image-small';
				break;
			default:
				$size = 'extra-image-huge';
				break;
		}
	}

	return $size;
}

function et_extra_show_home_layout() {
	return (bool) 'layout' == get_option( 'show_on_front' ) && extra_get_home_layout_id();
}

function extra_archive_pagination( $query = '' ) {
	global $wp_query;

	if ( empty( $query ) || !is_a( $query, 'WP_Query' ) ) {
		$query = $wp_query;
	}

	$big = 999999999; // need an unlikely integer
	$base = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
	$total = $query->max_num_pages;
	$current = max( 1, absint( $query->get( 'paged' ) ) );

	if ( $total - $current < 4 ) {
		$end_size = 3;
	} else {
		$end_size = 1;
	}

	if ( $current < 4 ) {
		$beg_size = 3;
	} else {
		$beg_size = 1;
	}

	$args = array(
		'base'      => $base,
		'format'    => '?paged=%#%',
		'total'     => $total,
		'current'   => $current,
		'beg_size'  => $beg_size,
		'end_size'  => $end_size,
		'mid_size'  => 1,
		'prev_text' => '',
		'next_text' => '',
		'type'      => 'list',
	);

	return et_paginate_links( $args );
}

function extra_display_ad( $location, $echo = true ) {
	if ( 'on' == et_get_option( $location . '_ad_enable' ) ) {
		$output = '';

		$adsense = et_get_option( $location . '_ad_adsense' );
		$enable_responsive_ad = et_get_option( $location . '_responsive_adsense_ad_enable' );
		$image = et_get_option( $location . '_ad_image' );
		$url = et_get_option( $location . '_ad_url' );

		if ( !empty( $adsense ) ) {
			$output = $adsense;

			if ( 'on' === $enable_responsive_ad ) {
				$output = '<div class="adsense-responsive-ad">'. $output .'</div>';
			}
		} elseif ( !empty( $image ) && !empty( $url ) ) {
			$output = '<a href="' . esc_url( $url ) . '"><img src="' . esc_url( $image ) . '" alt="' . esc_attr__( "Advertisement", 'extra' ) . '" /></a>';
		}

		if ( $echo ) {
			echo et_core_fix_unclosed_html_tags( $output );
		} else {
			return et_core_fix_unclosed_html_tags( $output );
		}
	}
}

function extra_footer_classes() {
	echo extra_customizer_selector_classes( '#footer', false );
}

/**
 * Add filterable element attribute. Like body_class or post_class for any element
 * @return string|void
 */
function extra_element_attribute( $filter, $attribute, $value = array(), $echo = true ) {
	$value = implode( " ", apply_filters( $filter, $value ) );

	if ( "" === $value ) {
		return;
	}

	$output = $attribute . '="' . esc_attr( $value ) .'"';

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function extra_check_feature_availability_in_post_type( $post_type, $feature_name ) {
	switch ( $feature_name ) {
		case 'hide_featured_image_in_single':
			$availability = array( 'post' );
			break;

		case 'hide_title_meta_in_single':
			$availability = array(
				'post',
				'page',
				'project',
			);
			break;

		default:
			$availability = array();
			break;
	}

	return in_array( $post_type, apply_filters( "extra_feature_availability_{$feature_name}", $availability, $feature_name ) );
}

/**
 * Check if the post authox box is enabled.
 *
 * This function uses get_the_id() and must be called in the post loop.
 *
 * @since To define
 *
 * @return bool Return true if enabled, false otherwise.
 */
function extra_is_post_author_box() {
	$epanel_show_author = 'on' == et_get_option( 'extra_show_author_box', 'on' );

	/* Return true if the ePanel author box option is enable and not disabled in the
	 * post meta, and vise versa.
	 */
	if ( $epanel_show_author ) {
		if ( true != get_post_meta( get_the_id(), '_extra_hide_author_box', true ) ) {
			return true;
		}
	} else if ( true == get_post_meta( get_the_id(), '_extra_show_author_box', true ) ) {
		return true;
	}

	return false;
}

/**
 * Check if the related posts box is enabled.
 *
 * This does not check if related posts exists, see @see extra_get_post_related_posts()
 * to get related posts. This function uses get_the_id() and must be called in the post loop.
 *
 * @since To define
 *
 * @return bool Return true if enabled, false otherwise.
 */
function extra_is_post_related_posts() {
	$epanel_show_related_posts = 'on' == et_get_option( 'extra_show_related_posts', 'on' );

	/* Return true if the ePanel related posts option is enable and not disabled in the
	 * post meta, and vise versa.
	 */
	if ( $epanel_show_related_posts ) {
		if ( true != get_post_meta( get_the_id(), '_extra_hide_related_posts', true ) ) {
			return true;
		}
	} else if ( true == get_post_meta( get_the_id(), '_extra_show_related_posts', true ) ) {
		return true;
	}

	return false;
}

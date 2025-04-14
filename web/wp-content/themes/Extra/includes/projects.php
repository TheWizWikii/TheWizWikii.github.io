<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'EXTRA_PROJECT_DETAILS_SHORT_DESC_META_KEY', '_extra_project_details_short_desc' );
define( 'EXTRA_PROJECT_DETAILS_LOCATION_META_KEY', '_extra_project_details_location' );
define( 'EXTRA_PROJECT_DETAILS_TITLE_META_KEY', '_extra_project_details_title' );
define( 'EXTRA_PROJECT_DETAILS_PROJECT_URL_META_KEY', '_extra_project_details_project_url' );
define( 'EXTRA_PROJECT_DETAILS_PROJECT_URL_TEXT_META_KEY', '_extra_project_details_project_url_text' );
define( 'EXTRA_PROJECT_DETAILS_PROJECT_URL_NEW_WINDOW_META_KEY', '_extra_project_details_project_url_new_window' );

function extra_get_project_details( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$project_details_meta_keys = array(
		'title'                  => EXTRA_PROJECT_DETAILS_TITLE_META_KEY,
		'location'               => EXTRA_PROJECT_DETAILS_LOCATION_META_KEY,
		'description'            => EXTRA_PROJECT_DETAILS_SHORT_DESC_META_KEY,
		'project_url'            => EXTRA_PROJECT_DETAILS_PROJECT_URL_META_KEY,
		'project_url_text'       => EXTRA_PROJECT_DETAILS_PROJECT_URL_TEXT_META_KEY,
		'project_url_new_window' => EXTRA_PROJECT_DETAILS_PROJECT_URL_NEW_WINDOW_META_KEY,
	);

	$project_details = array();
	foreach ( $project_details_meta_keys as $key => $meta_key ) {
		$project_details[ $key ] = get_post_meta( $post_id, $meta_key, true );
	}

	return $project_details;
}

function extra_get_the_project_categories( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	return get_the_terms( $post_id, EXTRA_PROJECT_CATEGORY_TAX );
}

function extra_the_project_categories( $post_id = 0, $before = null, $sep = ', ', $after = '' ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$categories = array();

	$project_categories = extra_get_the_project_categories( $post_id );

	if ( $project_categories ) {
		foreach ( $project_categories as $category ) {
			$categories[] = $category->name;
		}
	}

	echo $before . implode( $sep, $categories ) . $after;
}

function extra_get_the_project_gallery_images() {
	$attachment_ids = get_post_meta( get_the_ID(), '_gallery_attachment_ids', true );

	$attachments = array();
	$attachment_ids = trim( $attachment_ids );
	if ( !empty( $attachment_ids ) ) {
		$attachment_ids = explode( ',', $attachment_ids );
		foreach ( $attachment_ids as $attachment_id ) {
			list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $attachment_id, 'extra-image-huge' );
			$attachments[$attachment_id] = $thumb_src;
		}
	}

	return $attachments;
}

function extra_project_get_sidebar() {
	$details = extra_get_project_details();
	if ( 'split' == $details['location'] ) {
		extra_the_project_details_box( get_the_ID(), true );
	} else if ( 'sidebar' == $details['location'] ) {
		get_sidebar();
	}
}

function extra_project_sidebar_class() {
	$details = extra_get_project_details();

	if ( in_array( $details['location'], array('split', 'sidebar') ) ) {
		extra_sidebar_class();
	}
}

function extra_project_get_below_content() {
	$details = extra_get_project_details();
	if ( in_array( $details['location'], array( 'single_col', 'sidebar' ) ) ) {
		extra_the_project_details_box();
	}
}

function extra_the_project_details_box( $post_id = 0, $is_the_sidebar = false ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$project_details = extra_get_project_details( $post_id );

	$defaults = array(
		'title'            => esc_html__( 'Project Details:', 'extra' ),
		'project_url_text' => esc_html__( 'View The Project', 'extra' ),
	);

	$project_details = et_parse_args( $project_details, $defaults );

	$categories_list = get_the_term_list( $post_id, EXTRA_PROJECT_CATEGORY_TAX, '<li>', '</li><li>', '</li>' );

	$skills_list = get_the_term_list( $post_id, EXTRA_PROJECT_TAG_TAX, '<li>', '</li><li>', '</li>' );

	$description = !empty( $project_details['description'] ) ? sprintf( '<p>%s</p>', wp_kses_post( $project_details['description'] ) ) : '';

	$project_details['title'] = !empty( $project_details['title'] ) ? $project_details['title'] : $defaults['title'];

	$project_url = sprintf(
		'<div class="project-url">
			<ul>
				<li><a href="%1$s"%2$s>%3$s</a></li>
			</ul>
		</div>',
		esc_url( $project_details['project_url'] ),
		( $project_details['project_url_new_window'] ? ' target="_blank"' : '' ),
		esc_html( $project_details['project_url_text'] )
	);

	$output = sprintf(
		'<div class="module project-details">
			<div class="project-description">
				<h5 class="project-title">%2$s</h5>
				%4$s
			</div>
			<div class="project-categories">
				<h5 class="project-title">%5$s</h5>
				<ul>
				%6$s
				</ul>
			</div>
			<div class="project-tags">
				<h5 class="project-title">%7$s</h5>
				<ul>
				%8$s
				</ul>
			</div>
			%9$s
		</div>',
		esc_attr( et_get_option( 'accent_color', '#00A8FF' ) ),
		esc_html( $project_details['title'] ),
		! empty( $description ) ? sprintf( '<h5 class="project-description-title">%s</h5>', esc_html__( 'Project Description:', 'extra' ) ) : '',
		$description,
		esc_html__( 'Categories: ', 'extra' ),
		$categories_list,
		esc_html__( 'Skills: ', 'extra' ),
		$skills_list,
		$project_url
	);

	if ( $is_the_sidebar ) {
		$output = sprintf(
			'<div class="et_pb_extra_column_sidebar">%s</div>',
			$output
		);
	}

	echo $output;
}

function extra_has_project_gallery_post_class( $classes, $class, $post_id ) {
	if ( 'project' === get_post_type( $post_id ) && is_single() ) {
		$attachments  = extra_get_the_project_gallery_images();

		if ( empty( $attachments ) ) {
			$classes[] = 'et-doesnt-have-project-gallery';
		} else {
			$classes[] = 'et-has-project-gallery';
		}
	}

	return $classes;
}

add_filter( 'post_class', 'extra_has_project_gallery_post_class', 10, 3 );

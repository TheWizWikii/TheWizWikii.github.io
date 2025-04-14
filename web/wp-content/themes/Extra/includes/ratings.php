<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function extra_new_rating() {
	if ( ! wp_verify_nonce( $_POST['extra_rating_nonce'], 'extra_rating_nonce' ) ) {
		die( -1 );
	}

	$post_id = absint( sanitize_text_field( $_POST['extra_post_id'] ) );
	$rating = floatval( sanitize_text_field( $_POST['extra_rating'] ) );

	$result = extra_add_post_rating( $post_id, $rating );
	echo json_encode( $result );

	die();
}

add_action( 'wp_ajax_extra_new_rating', 'extra_new_rating' );
add_action( 'wp_ajax_nopriv_extra_new_rating', 'extra_new_rating' );

function extra_add_post_rating( $post_id, $rating ) {
	if ( extra_get_user_post_rating( $post_id ) ) {
		return array();
	}

	$commentdata = array(
		'comment_type'         => EXTRA_RATING_COMMENT_TYPE,
		'comment_author'       => '',
		'comment_author_url'   => '',
		'comment_author_email' => '',
		'comment_post_ID'      => absint( $post_id ),
		'comment_content'      => abs( floatval( $rating ) ),
	);

	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$commentdata['comment_author'] = wp_slash( $user->display_name );
		$commentdata['user_ID'] = $user->ID;
	}

	// prevent notifications
	add_filter( 'extra_rating_notify_intercept', '__return_zero' );

	wp_new_comment( $commentdata );

	return array(
		'rating'  => $rating,
		'average' => extra_set_post_rating_average( $post_id ),
	);
}

function extra_rating_notify_intercept( $option ) {
	$intercepted = apply_filters( 'extra_rating_notify_intercept', false );

	return false !== $intercepted ? $intercepted : false;
}

add_filter( 'pre_option_comments_notify', 'extra_rating_notify_intercept' );

function extra_rating_pre_comment_approved( $approved, $commentdata ) {
	if ( !empty( $commentdata['comment_type'] ) && EXTRA_RATING_COMMENT_TYPE == $commentdata['comment_type'] ) {
		$approved = 1;
	}
	return $approved;
}

add_filter( 'pre_comment_approved', 'extra_rating_pre_comment_approved', 10, 2 );

function extra_rating_pre_comment_user_ip() {
	return extra_get_user_ip();
}

add_filter( 'pre_comment_user_ip', 'extra_rating_pre_comment_user_ip' );

function extra_set_post_rating_average( $post_id ) {
	$ratings = get_comments( array(
		'type'    => EXTRA_RATING_COMMENT_TYPE,
		'post_id' => $post_id,
		'status'  => 'approve',
		'parent'  => 0,
	) );

	if ( empty( $ratings ) ) {
		$rating_avg = 0;
		update_post_meta( $post_id, '_extra_rating_average', 0 );
		update_post_meta( $post_id, '_extra_rating_count', 0 );
		return;
	}

	$rating_values = array();

	foreach ( $ratings as $rating ) {
		$rating_values[] = floatval( trim( $rating->comment_content ) );
	}

	$num = array_sum( $rating_values ) / count( $rating_values );

	$ceil = ceil( $num );

	$half = $ceil - 0.5;

	if ( $num >= $half + 0.25 ) {
		$rating_average = $ceil;
	} else if ( $num < $half - 0.25 ) {
		$rating_average = floor( $num );
	} else {
		$rating_average = $half;
	}

	$rating_count = count( $rating_values );
	update_post_meta( $post_id, '_extra_rating_average', $rating_average );
	update_post_meta( $post_id, '_extra_rating_count',  $rating_count );

	return compact( 'rating_average', 'rating_count' );
}

function extra_get_post_ratings_count( $post_id = '' ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	return get_comments( array(
		'type'    => EXTRA_RATING_COMMENT_TYPE,
		'post_id' => $post_id,
		'status'  => 'approve',
		'parent'  => 0,
		'count'   => true,
	) );
}

function extra_get_post_rating( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$rating = get_post_meta( $post_id, '_extra_rating_average', true );
	return $rating ? $rating : 0;
}

function extra_get_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	// disallow blocklisted characters
	preg_replace( '/[^0-9a-fA-F:., ]/', '', $ip );

	if ( '::1' == $ip ) {
		$ip = '127.0.0.1';
	}

	return $ip;
}

function extra_get_user_post_rating( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$args = array(
		'type'    => EXTRA_RATING_COMMENT_TYPE,
		'post_id' => $post_id,
		'status'  => 'approve',
		'parent'  => 0,
		'number'  => 1,
	);

	// If the user is logged in
	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$args['user_id'] = $user->ID;
	} else {
		$args['comment_author_IP'] = extra_get_user_ip();
	}

	$rating = get_comments( $args );
	return !empty( $rating ) ? $rating[0]->comment_content : false;
}

function extra_rating_get_comment_author_ip( $clauses, $wp_comment_query ) {
	global $wpdb;

	if ( $wp_comment_query->query_vars['type'] == EXTRA_RATING_COMMENT_TYPE && !empty( $wp_comment_query->query_vars['comment_author_IP'] ) ) {
		$clauses['where'] .= $wpdb->prepare( ' AND comment_author_IP = "%s"', $wp_comment_query->query_vars['comment_author_IP'] );
	}

	return $clauses;
}

add_filter( 'comments_clauses', 'extra_rating_get_comment_author_ip', 10, 2 );


/**
 * Prevent ratings comments from being shown as normal comments anywhere in the UI.
 */
function et_pre_get_comments_filter( $wp_comment_query ) {
	$type_is_set = isset( $wp_comment_query->query_vars['type'] );

	if ( false === $type_is_set || EXTRA_RATING_COMMENT_TYPE !== $wp_comment_query->query_vars['type'] ) {
		$wp_comment_query->query_vars['type__not_in'] = EXTRA_RATING_COMMENT_TYPE;
	}

	return $wp_comment_query;

}
add_filter( 'pre_get_comments', 'et_pre_get_comments_filter' );


/**
 * Post types which use rating
 * @return array
 */
function extra_get_rating_post_types() {
	return apply_filters( 'extra_rating_post_types', array(
		'post',
	) );
}

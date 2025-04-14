<?php
/**
 * Ajax service which searches through posts.
 *
 * @package Divi
 * @subpackage Builder
 */

/**
 * Handle ajax requests to search for posts.
 *
 * @since 3.26.7
 *
 * @return void
 */
function et_builder_ajax_search_posts() {
	et_core_security_check( 'edit_posts', 'et_builder_search_posts', 'nonce', '_GET' );

	$current_page         = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
	$current_page         = max( $current_page, 1 );
	$post_type            = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
	$value                = isset( $_GET['value'] ) ? sanitize_text_field( $_GET['value'] ) : '';
	$search               = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
	$prepend_value        = (int) $value > 0;
	$results_per_page     = 20;
	$results              = array(
		'results' => array(),
		'meta'    => array(),
	);
	$include_current_post = '1' === (string) et_()->array_get( $_GET, 'include_current_post', '0' );
	$include_latest_post  = '1' === (string) et_()->array_get( $_GET, 'include_latest_post', '0' );

	$public_post_types = et_builder_get_public_post_types();

	if ( ! isset( $public_post_types[ $post_type ] ) ) {
		$post_type = 'post';
	}

	$post_type_object = get_post_type_object( $post_type );
	$post_type_label  = $post_type_object ? $post_type_object->labels->singular_name : '';

	$query = array(
		'post_type'      => $post_type,
		'posts_per_page' => $results_per_page,
		'post_status'    => 'attachment' === $post_type ? 'inherit' : 'publish',
		's'              => $search,
		'orderby'        => 'date',
		'order'          => 'desc',
		'paged'          => $current_page,
	);

	if ( $prepend_value ) {
		$value_post = get_post( $value );

		if ( $value_post && 'publish' === $value_post->post_status && $value_post->post_type === $post_type ) {
			$results['results'][] = array(
				'value' => $value,
				'label' => et_core_intentionally_unescaped( wp_strip_all_tags( $value_post->post_title ), 'react_jsx' ),
				'meta'  => array(
					'post_type' => et_core_intentionally_unescaped( $post_type_label, 'react_jsx' ),
				),
			);

			// We will manually prepend the current value so we need to reduce the number of results.
			$query['posts_per_page'] -= 1;
			$query['post__not_in']    = array( $value );
		}
	}

	if ( 'attachment' === $post_type ) {
		add_filter( 'posts_join', 'et_builder_ajax_search_posts_query_join' );
		add_filter( 'posts_where', 'et_builder_ajax_search_posts_query_where' );
	}

	$posts = new WP_Query( $query );

	if ( 'attachment' === $post_type ) {
		remove_filter( 'posts_join', 'et_builder_ajax_search_posts_query_join' );
		remove_filter( 'posts_where', 'et_builder_ajax_search_posts_query_where' );
	}

	if ( $include_current_post && ! empty( $posts->posts ) ) {
		$current_post_type        = sanitize_text_field( et_()->array_get( $_GET, 'current_post_type', 'post' ) );
		$current_post_type        = isset( $public_post_types[ $current_post_type ] ) ? $current_post_type : 'post';
		$current_post_type_object = get_post_type_object( $current_post_type );
		$current_post_type_label  = $current_post_type_object ? $current_post_type_object->labels->singular_name : '';

		$results['results'][] = array(
			'value' => 'current',
			// Translators: %1$s: Post type singular name.
			'label' => et_core_intentionally_unescaped( sprintf( __( 'This %1$s', 'et_builder' ), $current_post_type_label ), 'react_jsx' ),
			'meta'  => array(
				'post_type' => et_core_intentionally_unescaped( $current_post_type_label, 'react_jsx' ),
			),
		);

		$query['posts_per_page'] -= 1;
	}

	if ( $include_latest_post && ! empty( $posts->posts ) ) {
		$results['results'][] = array(
			'value' => 'latest',
			// Translators: %1$s: Post type singular name.
			'label' => et_core_intentionally_unescaped(
				sprintf(
					__( 'Latest %1$s', 'et_builder' ),
					$post_type_label
				),
				'react_jsx'
			),
			'meta'  => array(
				'post_type' => et_core_intentionally_unescaped( $post_type_label, 'react_jsx' ),
			),
		);

		$query['posts_per_page'] -= 1;
	}

	foreach ( $posts->posts as $post ) {
		$results['results'][] = array(
			'value' => (int) $post->ID,
			'label' => et_core_intentionally_unescaped( wp_strip_all_tags( $post->post_title ), 'react_jsx' ),
			'meta'  => array(
				'post_type' => et_core_intentionally_unescaped( $post_type_label, 'react_jsx' ),
			),
		);
	}

	$results['meta']['pagination'] = array(
		'results' => array(
			'per_page' => (int) $results_per_page,
			'total'    => (int) $posts->found_posts,
		),
		'pages'   => array(
			'current' => (int) $current_page,
			'total'   => (int) $posts->max_num_pages,
		),
	);

	wp_send_json_success( $results );
}
add_action( 'wp_ajax_et_builder_search_posts', 'et_builder_ajax_search_posts' );

/**
 * Join the parent post for attachments queries.
 *
 * @since 3.27.3
 *
 * @param string $join  The JOIN clause of the query.
 *
 * @return string
 */
function et_builder_ajax_search_posts_query_join( $join ) {
	global $wpdb;

	$join .= " LEFT JOIN `$wpdb->posts` AS `parent` ON `parent`.`ID` = `$wpdb->posts`.`post_parent` ";

	return $join;
}

/**
 * Filter attachments based on the parent post status, if any.
 *
 * @since 3.27.3
 *
 * @param string $where The WHERE clause of the query.
 *
 * @return string
 */
function et_builder_ajax_search_posts_query_where( $where ) {
	global $wpdb;

	$public_post_types = array_keys( et_builder_get_public_post_types() );

	// Add an empty value to:
	// - Avoid syntax error for `IN ()` when there are no public post types.
	// - Cause the query to only return posts with no parent when there are no public post types.
	$public_post_types[] = '';

	$where .= $wpdb->prepare(
		' AND (
		`parent`.`ID` IS NULL OR (
			`parent`.`post_status` = %s
			AND
			`parent`.`post_type` IN (' . implode( ',', array_fill( 0, count( $public_post_types ), '%s' ) ) . ')
		)
	)',
		array_merge( array( 'publish' ), $public_post_types )
	);

	return $where;
}

<?php
/**
 * Local library functions.
 *
 * @package \ET\Common
 */

/**
 * Ajax :: Save item to the local library.
 */
function et_library_save_item() {
	et_core_security_check( 'publish_posts', 'et_library_save_item' );

	$post_id = et_save_item_to_local_library( $_POST );

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		wp_send_json_error();
	}

	$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';

	$data = array();

	switch ( $post_type ) {
		case 'et_theme_options':
			$item_library_local = et_pb_theme_options_library_local();
			$data               = $item_library_local->get_library_items( 'theme-options' );
			break;
	}

	wp_send_json_success( $data );
}

add_action( 'wp_ajax_et_library_save_item', 'et_library_save_item' );

/**
 * Save item to local library.
 *
 * @param array $item Item data.
 */
function et_save_item_to_local_library( $item ) {
	$item_name = $item['item_name'];
	$content   = $item['content'];
	$post_type = $item['post_type'];
	$built_for = isset( $item['builtFor'] ) ? $item['builtFor'] : '';

	$library_post_types = array( ET_THEME_OPTIONS_POST_TYPE, ET_CODE_SNIPPET_POST_TYPE );

	// Only allow to save library post types.
	if ( ! in_array( $post_type, $library_post_types, true ) ) {
		return false;
	}

	$new_post_data = array(
		'post_type'    => $post_type,
		'post_title'   => $item_name,
		'post_content' => $content,
		'post_status'  => 'publish',
	);

	if ( '' !== $built_for ) {
		$new_post_data['meta_input'] = array(
			'_built_for' => $built_for,
		);
	}

	$post_id = wp_insert_post( $new_post_data );

	et_local_library_set_item_taxonomy( $post_id, $item );

	return $post_id;
}

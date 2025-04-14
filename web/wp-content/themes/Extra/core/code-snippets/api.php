<?php
/**
 * Code Snippets Library API.
 *
 * @package Divi
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieves Code Snippets Library items.
 *
 * @return void
 */
function et_code_snippets_library_get_items() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_get_items', 'nonce' );

	$code_snippet_type = '';
	if ( isset( $_POST['et_code_snippet_type'] ) && is_string( $_POST['et_code_snippet_type'] ) ) {
		$code_snippet_type = sanitize_text_field( $_POST['et_code_snippet_type'] );
	}

	$is_code_snippet_type_valid = in_array(
		$code_snippet_type,
		[
			'et_code_snippet_css',
			'et_code_snippet_css_no_selector',
			'et_code_snippet_html_js',
		],
		true
	);

	if ( ! $code_snippet_type || ! $is_code_snippet_type_valid ) {
		wp_send_json_error( 'Error: Wrong item type provided.' );
	}

	$item_library_local = et_pb_code_snippets_library_local();
	$data               = $item_library_local->get_library_items( $code_snippet_type );

	wp_send_json_success( $data );
}

add_action( 'wp_ajax_et_code_snippets_library_get_items', 'et_code_snippets_library_get_items' );

/**
 * Retrieves Code Snippets Library item content.
 *
 * @return void
 */
function et_code_snippets_library_get_item_content() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_get_item_content', 'nonce' );

	$post_id       = isset( $_POST['et_code_snippet_id'] ) ? (int) sanitize_text_field( $_POST['et_code_snippet_id'] ) : 0;
	$return_format = isset( $_POST['et_code_snippet_format'] ) ? (string) sanitize_text_field( $_POST['et_code_snippet_format'] ) : 'raw';
	$snippet_type  = isset( $_POST['et_code_snippet_type'] ) ? (string) sanitize_text_field( $_POST['et_code_snippet_type'] ) : 'et_code_snippet_html_js';
	$post_type     = get_post_type( $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) || ET_CODE_SNIPPET_POST_TYPE !== $post_type ) {
		wp_send_json_error( 'You do not have permission.' );
	}

	$post = get_post( $post_id );

	$post_content = $post->post_content;
	$exported     = array();

	if ( 'exported' === $return_format ) {
		$exported = array(
			'context'      => 'et_code_snippet',
			'data'         => $post_content,
			'snippet_type' => $snippet_type,
		);
	}

	wp_send_json_success(
		array(
			'snippet'  => $post_content,
			'exported' => $exported,
		)
	);
}

add_action( 'wp_ajax_et_code_snippets_library_get_item_content', 'et_code_snippets_library_get_item_content' );



/**
 * Save Code Snippets Library item content.
 *
 * @return void
 */
function et_code_snippets_library_save_item_content() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_save_item_content', 'nonce' );

	$post_id   = isset( $_POST['et_code_snippet_id'] ) ? absint( $_POST['et_code_snippet_id'] ) : 0;
	$post_type = get_post_type( $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) || ET_CODE_SNIPPET_POST_TYPE !== $post_type ) {
		wp_send_json_error( 'You do not have permission.' );
	}

	// phpcs:disable ET.Sniffs.ValidatedSanitizedInput -- $_POST is an array, it's value sanitization is done at the time of saving by wp_update_post.
	$result = wp_update_post(
		[
			'ID'           => $post_id,
			'post_content' => $_POST['et_code_snippet_content'],
		]
	);
	// phpcs:enable

	wp_send_json_success(
		array(
			'result' => $result,
		)
	);
}

add_action( 'wp_ajax_et_code_snippets_library_save_item_content', 'et_code_snippets_library_save_item_content' );

/**
 * Update Code Snippets Library item.
 *
 * @return void
 */
function et_code_snippets_library_update_item() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_update_item', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

	$item_library_local = et_pb_code_snippets_library_local();
	$response           = $item_library_local->perform_item_update( $payload );

	if ( ! $response ) {
		wp_send_json_error( 'Error: Wrong data provided.' );
	}

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_code_snippets_library_update_item', 'et_code_snippets_library_update_item' );

/**
 * Prepare Library Categories or Tags List.
 *
 * @param string $taxonomy Name of the taxonomy.
 *
 * @return array Clean Categories/Tags array.
 **/
function et_get_clean_library_terms( $taxonomy = 'layout_category' ) {
	$raw_terms_array   = apply_filters( 'et_pb_new_layout_cats_array', get_terms( $taxonomy, array( 'hide_empty' => false ) ) );
	$clean_terms_array = array();

	if ( is_array( $raw_terms_array ) && ! empty( $raw_terms_array ) ) {
		foreach ( $raw_terms_array as $term ) {
			$clean_terms_array[] = array(
				'name'  => et_core_intentionally_unescaped( html_entity_decode( $term->name ), 'react_jsx' ),
				'id'    => $term->term_id,
				'slug'  => $term->slug,
				'count' => $term->count,
			);
		}
	}

	return $clean_terms_array;
}

/**
 * AJAX Callback: Remove the Library layout after it was moved to the Cloud.
 *
 * @since 4.17.0
 *
 * @global $_POST['payload'] Array with the layout data to remove.
 *
 * @return void|string JSON encoded in case of empty payload
 */
function et_code_snippets_toggle_cloud_status() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_toggle_item_location', 'nonce' );

	$post_id = isset( $_POST['et_code_snippet_id'] ) ? (int) sanitize_text_field( $_POST['et_code_snippet_id'] ) : 0;

	if ( empty( $post_id ) ) {
		wp_send_json_error( 'No post ID' );
	}

	$post_type = get_post_type( $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) || ET_CODE_SNIPPET_POST_TYPE !== $post_type ) {
		wp_send_json_error( 'You do not have permission.' );
	}

	wp_delete_post( $post_id, true );

	wp_send_json_success(
		array(
			'localLibraryTerms' => [
				'layout_category' => et_get_clean_library_terms(),
				'layout_tag'      => et_get_clean_library_terms( 'layout_tag' ),
			],
		)
	);
}

add_action( 'wp_ajax_et_code_snippets_toggle_cloud_status', 'et_code_snippets_toggle_cloud_status' );

/**
 * Export Code Snippets Library item.
 *
 * @return void
 */
function et_code_snippets_library_export_item() {
	et_core_security_check( et_core_portability_cap( 'et_code_snippets' ), 'et_code_snippets_library_export_item', 'nonce' );

	$post_id       = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	$cloud_content = isset( $_POST['cloudContent'] ) ? $_POST['cloudContent'] : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['cloudContent'] is an array, it's value sanitization is done at the time of accessing value.
	$direct_export = isset( $_POST['directExport'] ) ? $_POST['directExport'] : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['directExport'] is an array, it's value sanitization is done at the time of accessing value.

	$response = et_code_snippets_library_export_item_data( $post_id, $cloud_content, $direct_export );

	if ( ! $response ) {
		wp_send_json_error( 'Error: Wrong data provided.' );
	}

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_code_snippets_library_export_item', 'et_code_snippets_library_export_item' );

/**
 * Download exported Code Snippets Library item.
 *
 * @return void
 */
function et_code_snippets_library_export_item_download() {
	et_core_security_check( et_core_portability_cap( 'et_code_snippets' ), 'et_code_snippets_library_export_item', 'nonce', '_GET' );

	$id        = ! empty( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	$file_name = empty( $_GET['fileName'] ) ? 'code-snippet' : sanitize_file_name( $_GET['fileName'] );

	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: attachment; filename="' . $file_name . '.json"' );
	header( 'Content-Type: application/json' );
	header( 'Pragma: no-cache' );

	$transient      = 'et_code_snippet_export_' . get_current_user_id() . '_' . $id;
	$export_content = get_transient( $transient );

	delete_transient( $transient );

	echo wp_json_encode( $export_content );

	wp_die();
}

add_action( 'wp_ajax_et_code_snippets_library_export_item_download', 'et_code_snippets_library_export_item_download' );

/**
 * Import Code Snippets Library item.
 *
 * @return void
 */
function et_code_snippets_library_import_item() {
	et_core_security_check( et_core_portability_cap( 'et_code_snippets' ), 'et_code_snippets_library_import_item', 'nonce' );

	$response = et_code_snippets_library_import_item_data();

	if ( ! $response ) {
		wp_send_json_error( 'Not a valid file.' );
	}

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_code_snippets_library_import_item', 'et_code_snippets_library_import_item' );


/**
 * Update Local Library Tags and Categories.
 *
 * @return void
 */
function et_code_snippets_library_update_terms() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_update_terms', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

	if ( empty( $payload ) ) {
		wp_send_json_error( 'Payload is empty.' );
	}

	$item_library_local = et_pb_code_snippets_library_local();
	$response           = $item_library_local->perform_terms_update( $payload );

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_code_snippets_library_update_terms', 'et_code_snippets_library_update_terms' );

/**
 * Ajax :: Save code snippets to the local library.
 */
function et_code_snippets_library_save() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_save_to_local_library' );

	$post_id = et_save_item_to_local_library( $_POST );

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error();
	}

	wp_send_json_success();
}

add_action( 'wp_ajax_et_code_snippets_library_save', 'et_code_snippets_library_save' );

/**
 * Ajax :: Get Cloud token.
 */
function et_code_snippets_library_get_token() {
	et_core_security_check( 'edit_posts', 'et_code_snippets_library_get_token', 'nonce' );

	$access_token = get_transient( 'et_cloud_access_token' );

	wp_send_json_success(
		array(
			'accessToken' => $access_token,
		)
	);
}

add_action( 'wp_ajax_et_code_snippets_library_get_token', 'et_code_snippets_library_get_token' );

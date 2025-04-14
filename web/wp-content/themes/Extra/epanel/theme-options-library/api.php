<?php
/**
 * Theme Options Library API.
 *
 * @package Divi
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieves Theme Options Library items.
 *
 * @return void
 */
function et_theme_options_library_get_items() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_get_items', 'nonce' );

	$context = isset( $_POST['context'] )
		? sanitize_text_field( $_POST['context'] )
		: '';

	if ( '' === $context ) {
		wp_send_json_error( 'missing_context' );
	}

	$item_library_local = et_pb_theme_options_library_local();
	$data               = $item_library_local->get_library_items( $context );

	wp_send_json_success( $data );
}

add_action( 'wp_ajax_et_theme_options_library_get_items', 'et_theme_options_library_get_items' );

/**
 * Update Terms.
 *
 * @return void
 */
function et_theme_options_library_update_terms() {
	et_core_security_check( 'manage_categories', 'et_theme_options_library_update_terms', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

	if ( empty( $payload ) ) {
		wp_send_json_error( 'Payload is empty.' );
	}

	$item_library_local = et_pb_theme_options_library_local();
	$response           = $item_library_local->perform_terms_update( $payload );

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_theme_options_library_update_terms', 'et_theme_options_library_update_terms' );

/**
 * Export the Theme Options library item.
 * This function only retrieves the data.
 * All the permissions checks should be performed at the top level function which calls this one.
 *
 * @since 4.19.0
 *
 * @param int   $id            Item ID.
 * @param array $cloud_content Optional cloud content.
 *
 * @return array
 */
function et_theme_options_library_export_item_data( $id, $cloud_content ) {
	if ( empty( $cloud_content ) ) {
		if ( empty( $id ) ) {
			return false;
		}

		$id             = absint( $id );
		$post           = get_post( $id );
		$export_content = $post->post_content;
		$export_content = json_decode( $export_content );
	} else {
		$export_content = $cloud_content;
	}

	if ( empty( $export_content ) ) {
		return;
	}

	$transient = 'et_theme_options_export_' . get_current_user_id() . '_' . $id;
	set_transient( $transient, $export_content, 60 * 60 * 24 );

	return $export_content;
}

/**
 * Export Theme options Library item.
 *
 * @return void
 */
function et_theme_options_library_export_item() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_export_item', 'nonce' );

	$post_id       = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	$cloud_content = isset( $_POST['cloudContent'] ) ? $_POST['cloudContent'] : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['cloudContent'] is an array, it's value sanitization is done at the time of accessing value.

	$post_type = get_post_type( $post_id );

	// When exporting cloud content this check doesn't make sense as we already have data.
	if ( empty( $cloud_content ) && ( ! current_user_can( 'edit_post', $post_id ) || ET_THEME_OPTIONS_POST_TYPE !== $post_type ) ) {
		wp_send_json_error( 'You do not have permission.' );
	}

	$response = et_theme_options_library_export_item_data( $post_id, $cloud_content );

	if ( ! $response ) {
		wp_send_json_error( 'Error: Wrong data provided.' );
	}

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_theme_options_library_export_item', 'et_theme_options_library_export_item' );

/**
 * Download exported Theme options Library item.
 *
 * @return void
 */
function et_theme_options_library_export_item_download() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_export_item', 'nonce', '_GET' );

	$id        = ! empty( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	$file_name = empty( $_GET['fileName'] ) ? 'Theme Options' : sanitize_file_name( $_GET['fileName'] );

	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: attachment; filename="' . $file_name . '.json"' );
	header( 'Content-Type: application/json' );
	header( 'Pragma: no-cache' );

	$transient      = 'et_theme_options_export_' . get_current_user_id() . '_' . $id;
	$export_content = get_transient( $transient );

	delete_transient( $transient );

	echo wp_json_encode( $export_content );

	wp_die();
}

add_action( 'wp_ajax_et_theme_options_library_export_item_download', 'et_theme_options_library_export_item_download' );

/**
 * Update theme options Library item.
 *
 * @return void
 */
function et_theme_options_library_update_item() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_update_item', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

	if ( empty( $payload ) ) {
		wp_send_json_error( 'Payload is empty.' );
	}

	$item_library_local = et_pb_theme_options_library_local();
	$response           = $item_library_local->perform_item_update( $payload );

	if ( ! $response ) {
		wp_send_json_error( 'Error: Wrong data provided.' );
	}

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_theme_options_library_update_item', 'et_theme_options_library_update_item' );

/**
 * Get cloud access token.
 *
 * @return void
 */
function et_theme_options_library_get_token() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_get_token', 'nonce' );

	wp_send_json_success(
		array( 'accessToken' => get_transient( 'et_cloud_access_token' ) )
	);
}

add_action( 'wp_ajax_et_theme_options_library_get_token', 'et_theme_options_library_get_token' );

/**
 * Get Theme options Library item.
 *
 * @since ??
 * Retrieves theme options library item content.
 *
 * @return void
 */
function et_theme_options_library_get_item_content() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_get_item_content', 'nonce' );

	$id = isset( $_POST['et_theme_option_id'] ) ? (int) sanitize_text_field( $_POST['et_theme_option_id'] ) : 0;

	if ( empty( $id ) ) {
		wp_send_json_error();
	}

	$result = array();
	$post   = get_post( $id );

	$post_type = ET_THEME_OPTIONS_POST_TYPE;

	if ( $post_type !== $post->post_type ) {
		wp_die();
	}

	$result = [];

	$result['exported'] = json_decode( $post->post_content );

	$response = wp_json_encode(
		array(
			'success' => true,
			'data'    => $result,
		)
	);

	// Charset has to be explicitly mentioned when it is other than UTF-8.
	header( 'Content-Type: application/json; charset=' . esc_attr( get_option( 'blog_charset' ) ) );

	die( et_core_intentionally_unescaped( $response, 'html' ) );
}

add_action( 'wp_ajax_et_theme_options_library_get_item_content', 'et_theme_options_library_get_item_content' );

/**
 * AJAX Callback: Remove the Library layout after it was moved to the Cloud.
 *
 * @since ??
 *
 * @global $_POST['payload'] Array with the layout data to remove.
 *
 * @return void|string JSON encoded in case of empty payload
 */
function et_theme_options_toggle_cloud_status() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_library_toggle_item_location', 'nonce' );

	$post_id   = isset( $_POST['et_theme_option_id'] ) ? (int) sanitize_text_field( $_POST['et_theme_option_id'] ) : 0;
	$post_type = get_post_type( $post_id );

	if ( empty( $post_id ) ) {
		wp_send_json_error( 'No post ID' );
	}

	$post_type = get_post_type( $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) || ET_THEME_OPTIONS_POST_TYPE !== $post_type ) {
		wp_send_json_error( 'You do not have permission.' );
	}

	wp_delete_post( $post_id, true );

	$item_library_local = et_pb_theme_options_library_local();

	wp_send_json_success(
		array(
			'localLibraryTerms' => [
				'layout_category' => $item_library_local->get_formatted_library_terms(),
				'layout_tag'      => $item_library_local->get_formatted_library_terms( 'layout_tag' ),
			],
		)
	);
}

add_action( 'wp_ajax_et_theme_options_toggle_cloud_status', 'et_theme_options_toggle_cloud_status' );

/**
 * Delete temporary options library
 */
function et_theme_options_delete_temp_options() {
	et_core_security_check( et_core_portability_cap( 'epanel' ), 'et_theme_options_delete_temp_options' );

	$deleted = delete_option( 'et_divi_' . get_current_user_id() );

	if ( $deleted ) {
		return wp_send_json_success();
	}

	return wp_send_json_error();
}

add_action( 'wp_ajax_et_theme_options_delete_temp_options', 'et_theme_options_delete_temp_options' );

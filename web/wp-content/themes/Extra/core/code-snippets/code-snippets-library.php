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
 * Gets the terms list and processes it into desired format.
 *
 * @since 4.19.0
 *
 * @param string $tax_name Term Name.
 *
 * @return array $terms_by_id
 */
function et_code_snippets_library_get_processed_terms( $tax_name ) {
	$terms       = get_terms( $tax_name, [ 'hide_empty' => false ] );
	$terms_by_id = [];

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return [];
	}

	foreach ( $terms as $term ) {
		$term_id = $term->term_id;

		$terms_by_id[ $term_id ]['id']    = $term_id;
		$terms_by_id[ $term_id ]['name']  = $term->name;
		$terms_by_id[ $term_id ]['slug']  = $term->slug;
		$terms_by_id[ $term_id ]['count'] = $term->count;
	}

	return $terms_by_id;
}

/**
 * Processes item taxonomies for inclusion in the theme builder library UI items data.
 *
 * @since 4.19.0
 *
 * @param WP_POST $post          Unprocessed item.
 * @param object  $item          Currently processing item.
 * @param int     $index         The item's index position.
 * @param array[] $item_terms    Processed items.
 * @param string  $taxonomy_name Item name.
 * @param string  $type          Item type.
 *
 * @return void
 */
function et_code_snippets_library_process_item_taxonomy( $post, $item, $index, &$item_terms, $taxonomy_name, $type ) {
	$terms = wp_get_post_terms( $post->ID, $taxonomy_name );

	if ( ! $terms ) {
		if ( 'category' === $type ) {
			$item->category_slug = 'uncategorized';
		}

		return;
	}

	foreach ( $terms as $term ) {
		$term_name = et_core_intentionally_unescaped( $term->name, 'react_jsx' );

		if ( ! isset( $item_terms[ $term->term_id ] ) ) {
			$item_terms[ $term->term_id ] = array(
				'id'    => $term->term_id,
				'name'  => $term_name,
				'slug'  => $term->slug,
				'items' => array(),
			);
		}

		$item_terms[ $term->term_id ]['items'][] = $index;

		if ( 'category' === $type ) {
			$item->categories[] = $term_name;
		} else {
			$item->tags[] = $term_name;
		}

		$item->{$type . '_ids'}[] = $term->term_id;

		if ( ! isset( $item->{$type . '_slug'} ) ) {
			$item->{$type . '_slug'} = $term->slug;
		}

		$id = get_post_meta( $post->ID, "_primary_{$taxonomy_name}", true );

		if ( $id ) {
			// $id is a string, $term->term_id is an int.
			if ( $id === $term->term_id ) {
				// This is the primary term (used in the item URL).
				$item->{$type . '_slug'} = $term->slug;
			}
		}
	}
}

/**
 * Sanitize txonomies.
 *
 * @since 4.19.0
 *
 * @param array $taxonomies Array of id for categories and tags.
 *
 * @return array Sanitized value.
 */
function et_code_snippets_library_sanitize_taxonomies( $taxonomies ) {
	if ( empty( $taxonomies ) ) {
		return array();
	}

	return array_unique(
		array_map( 'intval', $taxonomies )
	);
}

/**
 * Get all terms of an item and merge any newly passed IDs with the list.
 *
 * @since 4.19.0
 *
 * @param string $new_terms_list List of new terms.
 * @param array  $taxonomies Taxonomies.
 * @param string $taxonomy_name Taxonomy name.
 *
 * @return array
 */
function et_code_snippets_library_create_and_get_all_item_terms( $new_terms_list, $taxonomies, $taxonomy_name ) {
	$new_names_array = explode( ',', $new_terms_list );

	foreach ( $new_names_array as $new_name ) {
		if ( '' !== $new_name ) {
			$new_term = wp_insert_term( $new_name, $taxonomy_name );

			if ( ! is_wp_error( $new_term ) ) {
				$taxonomies[] = $new_term['term_id'];
			} elseif (
					! empty( $new_term->error_data ) &&
					! empty( $new_term->error_data['term_exists'] )
				) {
				$taxonomies[] = $new_term->error_data['term_exists'];
			}
		}
	}

	return $taxonomies;
}

/**
 * Export the Code Snippets library item from cloud.
 *
 * @since 4.19.0
 *
 * @param array $cloud_content Optional cloud content.
 *
 * @return array
 */
function et_code_snippets_library_export_cloud_item( $cloud_content ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die();
	}

	$content                 = array( 'context' => 'et_code_snippet' );
	$content['snippet_type'] = $cloud_content['snippet_type'];
	$content['data']         = $cloud_content['data'];

	return $content;
}

/**
 * Export the Code Snippets library item local item.
 *
 * @since 4.19.0
 *
 * @param int $post_id Item ID.
 *
 * @return array
 */
function et_code_snippets_library_export_local_item( $post_id ) {
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die();
	}

	$snippet      = get_post( $post_id );
	$snippet_type = current( wp_get_post_terms( $post_id, 'et_code_snippet_type' ) );

	$content                 = array( 'context' => 'et_code_snippet' );
	$content['snippet_type'] = $snippet_type->slug;
	$content['data']         = $snippet->post_content;

	return $content;
}

/**
 * Export the Code Snippets library item directly.
 *
 * @since 4.19.0
 * @param array $direct_export Contain snippet-type and content.
 *
 * @return array
 */
function et_code_snippets_library_export_directly( $direct_export ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die();
	}

	if ( ! trim( $direct_export['content'] ) ) {
		return false;
	}

	$content                 = array( 'context' => 'et_code_snippet' );
	$content['snippet_type'] = $direct_export['snippet_type'];
	$content['data']         = stripslashes_deep( $direct_export['content'] );

	return $content;
}

/**
 * Export the Code Snippets library item.
 *
 * @since 4.19.0
 *
 * @param int   $id            Item ID.
 * @param array $cloud_content Optional cloud content.
 * @param array $direct_export Contain snippet-type and content.
 *
 * @return array
 */
function et_code_snippets_library_export_item_data( $id, $cloud_content, $direct_export ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die();
	}

	if ( empty( $id ) ) {
		return false;
	}

	$id = absint( $id );

	if ( empty( $direct_export ) ) {
		$export_content = empty( $cloud_content ) ?
			et_code_snippets_library_export_local_item( $id )
			:
			et_code_snippets_library_export_cloud_item( $cloud_content );
	} else {
		$export_content = et_code_snippets_library_export_directly( $direct_export );
	}

	if ( empty( $export_content ) ) {
		return;
	}

	$transient = 'et_code_snippet_export_' . get_current_user_id() . '_' . $id;
	set_transient( $transient, $export_content, 60 * 60 * 24 );

	return $export_content;
}

/**
 * Import the Code Snippets library item.
 *
 * @since 4.19.0
 *
 * @return array
 */
function et_code_snippets_library_import_item_data() {
	if ( ! current_user_can( 'edit_posts' ) || ! isset( $_POST['fileData'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check` before calling this function.
		return false;
	}

	// phpcs:disable ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- wp_insert_post function does sanitization.
	$file_data    = $_POST['fileData']; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check` before calling this function.
	$file_name    = sanitize_text_field( $file_data['title'] );
	$file_content = isset( $_POST['fileContent'] ) ? json_decode( stripslashes( $_POST['fileContent'] ), true ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check` before calling this function.
	// phpcs:enable

	if ( ! isset( $file_data['type'] ) ) {
		return false;
	}

	$snippet = array(
		'item_name' => $file_name,
		'item_type' => $file_data['type'],
		'content'   => $file_content,
	);

	return et_code_snippets_save_to_local_library( $snippet );
}

/**
 * Save a code snippet to local library.
 *
 * @param array $item Item data.
 */
function et_code_snippets_save_to_local_library( $item ) {
	$_         = et_();
	$item_name = sanitize_text_field( $_->array_get( $item, 'item_name', '' ) );
	$content   = $_->array_get( $item, 'content', '' );

	$post_id = wp_insert_post(
		array(
			'post_type'    => ET_CODE_SNIPPET_POST_TYPE,
			'post_status'  => 'publish',
			'post_title'   => $item_name,
			'post_content' => $content,
		)
	);

	et_local_library_set_item_taxonomy( $post_id, $item );

	return $post_id;
}

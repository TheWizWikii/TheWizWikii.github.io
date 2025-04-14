<?php
/**
 * Local library functions.
 *
 * @package Divi
 * @subpackage Builder
 */

/**
 * Gets the selected taxonomies from Preset/Template modals.
 *
 * @param array $preferences Preferences set in the Save Builder Preset/Template modals.
 *
 * @return array
 */
function et_local_library_get_selected_taxonomy( $preferences ) {
	$selected_cats = isset( $preferences['selected_cats'] ) ? $preferences['selected_cats'] : [];
	$selected_tags = isset( $preferences['selected_tags'] ) ? $preferences['selected_tags'] : [];

	return [
		'layout_category' => array_map( 'intval', $selected_cats ),
		'layout_tag'      => array_map( 'intval', $selected_tags ),
	];
}

/**
 * Gets the newly added taxonomies set in the Preset/Template modals.
 *
 * @param array $preferences Preferences set in the Save Builder Preset/Template modals.
 *
 * @return array
 */
function et_local_library_get_new_taxonomy( $preferences ) {
	return [
		'layout_category' => isset( $preferences['new_category_name'] ) ? $preferences['new_category_name'] : '',
		'layout_tag'      => isset( $preferences['new_tag_name'] ) ? $preferences['new_tag_name'] : '',
	];
}

/**
 * Insert terms from comma seperated string.
 *
 * @since 4.19.0
 * @param string $terms_str Comma seperated list of new terms.
 * @param string $tax Taxonomy name.
 *
 * @return (void|array)
 */
function et_local_library_insert_terms_from_str( $terms_str, $tax ) {
	// Insert categories.
	if ( '' === $terms_str || ! in_array( $tax, [ 'layout_category', 'layout_tag' ], true ) ) {
		return;
	}

	// Multiple terms could be provided.
	$term_names   = explode( ',', $terms_str );
	$new_term_ids = array();

	foreach ( $term_names as $term_name ) {
		$new_term = wp_insert_term( $term_name, $tax );

		if ( ! is_wp_error( $new_term ) && isset( $new_term['term_id'] ) ) {
			$new_term_ids[] = (int) $new_term['term_id'];
		}
	}

	return $new_term_ids;
}

/**
 * Sets the taxomomy for Template & Preset.
 *
 * @param int   $post_id Post ID.
 * @param array $preferences Preferences set in the Save Builder Preset/Template modals.
 */
function et_local_library_set_item_taxonomy( $post_id, $preferences ) {
	$_         = et_();
	$tax_input = et_local_library_get_selected_taxonomy( $preferences );

	$item_type = $_->array_get( $preferences, 'item_type' );

	// Taxonomy: TB item type and selected category and tags.
	if ( ! empty( $item_type ) ) {
		if ( ET_CODE_SNIPPET_POST_TYPE === get_post_type( $post_id ) ) {
			$item_type_taxonomy = ET_CODE_SNIPPET_TAXONOMY_TYPE;
		} else {
			$item_type_taxonomy = ET_THEME_BUILDER_TAXONOMY_TYPE;
		}
		$tax_input = array_merge( $tax_input, [ $item_type_taxonomy => $item_type ] );
	}

	// Insert new category and tags.
	$new_taxs = et_local_library_get_new_taxonomy( $preferences );

	foreach ( $new_taxs as $tax => $new_terms ) {
		if ( '' !== $new_terms ) {
			$inserted_terms_ids = et_local_library_insert_terms_from_str( $new_terms, $tax );
			if ( ! empty( $inserted_terms_ids ) ) {
				$tax_input[ $tax ] = array_merge( $tax_input[ $tax ], $inserted_terms_ids );
			}
		}
	}

	// Set category and tags for the template saved into local library.
	if ( ! empty( $tax_input ) ) {
		foreach ( $tax_input as $taxonomy => $terms ) {
			wp_set_post_terms( $post_id, $terms, $taxonomy );
		}
	}
}

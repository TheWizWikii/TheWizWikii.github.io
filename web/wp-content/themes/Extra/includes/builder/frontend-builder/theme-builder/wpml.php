<?php
/**
 * Disable language filtering of terms in TB.
 *
 * @since 4.2
 *
 * @param string $parent_id
 * @param string $child_type
 * @param string $child_value
 */
function et_theme_builder_wpml_disable_term_filters( $parent_id, $child_type, $child_value ) {
	global $sitepress;

	if ( ! $sitepress || 'taxonomy' !== $child_type ) {
		return;
	}

	remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10 );
	remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10 );
	remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
}
add_action( 'et_theme_builder_before_get_template_setting_child_options', 'et_theme_builder_wpml_disable_term_filters', 10, 3 );

/**
 * Enable language filtering of terms in TB.
 *
 * @since 4.2
 *
 * @param string $parent_id
 * @param string $child_type
 * @param string $child_value
 */
function et_theme_builder_wpml_enable_term_filters( $parent_id, $child_type, $child_value ) {
	global $sitepress;

	if ( ! $sitepress || 'taxonomy' !== $child_type ) {
		return;
	}

	add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 3 );
	add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10, 2 );
	add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );
}
add_action( 'et_theme_builder_after_get_template_setting_child_options', 'et_theme_builder_wpml_enable_term_filters', 10, 3 );

/**
 * Normalize an object ID to it's base language ID if it is a translation.
 *
 * @since 4.2
 *
 * @param integer $id      WPML object ID.
 * @param string  $type    Type.
 * @param string  $subtype Subtype.
 *
 * @return integer
 */
function et_theme_builder_wpml_normalize_object_id( $id, $type, $subtype ) {
	return apply_filters( 'wpml_object_id', $id, $subtype, true );
}
add_filter( 'et_theme_builder_template_setting_filter_validation_id', 'et_theme_builder_wpml_normalize_object_id', 10, 3 );

/**
 * Prioritize IDs for the current active language over translated IDs
 * when comparing template settings priority.
 *
 * @since 4.2
 *
 * @param string                   $prioritized_setting Prioritized setting.
 * @param string                   $a                   First translated id.
 * @param string                   $b                   Second translated id.
 * @param ET_Theme_Builder_Request $request
 *
 * @return string
 */
function et_theme_builder_wpml_prioritize_translated_id( $prioritized_setting, $a, $b, $request ) {
	$a_id            = '';
	$a_id_translated = '';
	$b_id            = '';
	$b_id_translated = '';
	$a_matches       = array();
	$b_matches       = array();

	// Match singular:post_type:<post_type>:id:<id>
	$singular = '/^singular:post_type:([^:]+):id:(\d+)$/i';
	// Match singular:post_type:<post_type>:children:id:<id>
	$singular_children = '/^singular:post_type:([^:]+):children:id:(\d+)$/i';
	// Match singular:taxonomy:<taxonomy>:term:id:<id>
	$singular_term = '/^singular:taxonomy:([^:]+):term:id:(\d+)$/i';
	// Match archive:taxonomy:<taxonomy>:term:id:<id>
	$archive_term = '/^archive:taxonomy:([^:]+):term:id:(\d+)$/i';

	if ( preg_match( $singular, $a, $a_matches ) && preg_match( $singular, $b, $b_matches ) ) {
		$a_id            = (int) $a_matches[2];
		$a_id_translated = et_theme_builder_wpml_normalize_object_id( $a_id, 'post', $a_matches[1] );
		$b_id            = (int) $b_matches[2];
		$b_id_translated = et_theme_builder_wpml_normalize_object_id( $b_id, 'post', $b_matches[1] );
	} elseif ( preg_match( $singular_children, $a, $a_matches ) && preg_match( $singular_children, $b, $b_matches ) ) {
		$a_id            = (int) $a_matches[2];
		$a_id_translated = et_theme_builder_wpml_normalize_object_id( $a_id, 'post', $a_matches[1] );
		$b_id            = (int) $b_matches[2];
		$b_id_translated = et_theme_builder_wpml_normalize_object_id( $b_id, 'post', $b_matches[1] );
	} elseif ( preg_match( $singular_term, $a, $a_matches ) && preg_match( $singular_term, $b, $b_matches ) ) {
		$a_id            = (int) $a_matches[2];
		$a_id_translated = et_theme_builder_wpml_normalize_object_id( $a_id, 'taxonomy', $a_matches[1] );
		$b_id            = (int) $b_matches[2];
		$b_id_translated = et_theme_builder_wpml_normalize_object_id( $b_id, 'taxonomy', $b_matches[1] );
	} elseif ( preg_match( $archive_term, $a, $a_matches ) && preg_match( $archive_term, $b, $b_matches ) ) {
		$a_id            = (int) $a_matches[2];
		$a_id_translated = et_theme_builder_wpml_normalize_object_id( $a_id, 'taxonomy', $a_matches[1] );
		$b_id            = (int) $b_matches[2];
		$b_id_translated = et_theme_builder_wpml_normalize_object_id( $b_id, 'taxonomy', $b_matches[1] );
	}

	if ( $a_id && $a_id_translated && $a_id_translated === $a_id ) {
		// $a is an exact match for the current request and not a translated match so we prioritize it.
		return $a;
	}

	if ( $b_id && $b_id_translated && $b_id_translated === $b_id ) {
		// $b is an exact match for the current request and not a translated match so we prioritize it.
		return $b;
	}

	// Neither $a nor $b are exact matches so don't prioritize either.
	return $prioritized_setting;
}
add_filter( 'et_theme_builder_prioritized_template_setting', 'et_theme_builder_wpml_prioritize_translated_id', 10, 6 );

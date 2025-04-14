<?php
/**
 * Filters an object id for use in template settings validation functions.
 *
 * @since 4.2
 *
 * @param integer $id      Object ID.
 * @param string  $type    Type.
 * @param string  $subtype Subtype.
 *
 * @return integer
 */
function et_theme_builder_template_setting_filter_validation_object_id( $id, $type, $subtype ) {
	/**
	 * Filters template settings object id for validation use.
	 *
	 * @since 4.2
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $subtype
	 */
	return apply_filters( 'et_theme_builder_template_setting_filter_validation_id', $id, $type, $subtype );
}

/**
 * Validate homepage.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_homepage( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_FRONT_PAGE === $type;
}

/**
 * Validate singular:post_type:<post_type>:all.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_singular_post_type_all( $type, $subtype, $id, $setting ) {
	if ( ET_Theme_Builder_Request::TYPE_FRONT_PAGE === $type && 'page' === $setting[2] && $id === (int) get_option( 'page_on_front' ) ) {
		// Cover the homepage as well.
		return true;
	}

	return ET_Theme_Builder_Request::TYPE_SINGULAR === $type && $subtype === $setting[2];
}

/**
 * Validate archive:post_type:<post_type>.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_post_type( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_POST_TYPE_ARCHIVE === $type && $subtype === $setting[2];
}

/**
 * Validate singular:post_type:<post_type>:id:<id>.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_singular_post_type_id( $type, $subtype, $id, $setting ) {
	$object_id = et_theme_builder_template_setting_filter_validation_object_id( (int) $setting[4], 'post', $setting[2] );

	return (
		// Cover the special case where the post selected is assigned as the website homepage.
		( ET_Theme_Builder_Request::TYPE_FRONT_PAGE === $type && $id === $object_id )
		||
		( ET_Theme_Builder_Request::TYPE_SINGULAR === $type && $id === $object_id )
	);
}

/**
 * Validate singular:post_type:<post_type>:children:id:<id>.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_singular_post_type_children_id( $type, $subtype, $id, $setting ) {
	if ( ET_Theme_Builder_Request::TYPE_SINGULAR !== $type ) {
		return false;
	}

	$object_id = et_theme_builder_template_setting_filter_validation_object_id( (int) $setting[5], 'post', $setting[2] );

	return in_array( $object_id, get_post_ancestors( $id ), true );
}

/**
 * Validate singular:taxonomy:<taxonomy>:term:id:<id>.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_singular_taxonomy_term_id( $type, $subtype, $id, $setting ) {
	if ( ET_Theme_Builder_Request::TYPE_SINGULAR !== $type ) {
		return false;
	}

	$taxonomy  = $setting[2];
	$object_id = et_theme_builder_template_setting_filter_validation_object_id( (int) $setting[5], 'taxonomy', $taxonomy );

	return has_term( $object_id, $taxonomy, $id );
}

/**
 * Validate archive:all.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_all( $type, $subtype, $id, $setting ) {
	$archives = array(
		ET_Theme_Builder_Request::TYPE_POST_TYPE_ARCHIVE,
		ET_Theme_Builder_Request::TYPE_TERM,
		ET_Theme_Builder_Request::TYPE_AUTHOR,
		ET_Theme_Builder_Request::TYPE_DATE,
	);

	return in_array( $type, $archives, true );
}

/**
 * Validate archive:taxonomy:<taxonomy>:all.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_taxonomy_all( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_TERM === $type && $subtype === $setting[2];
}

/**
 * Validate archive:taxonomy:<taxonomy>:term:id:<id>.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_taxonomy_term_id( $type, $subtype, $id, $setting ) {
	$taxonomy  = $setting[2];
	$object_id = et_theme_builder_template_setting_filter_validation_object_id( (int) $setting[5], 'post', $taxonomy );

	if ( ET_Theme_Builder_Request::TYPE_TERM === $type && $subtype === $taxonomy ) {
		// Exact match.
		if ( $id === $object_id ) {
			return true;
		}

		// Specified setting term id is an ancestor of the request term id ($id).
		if ( term_is_ancestor_of( $object_id, $id, $taxonomy ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Validate archive:user:all.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_user_all( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_AUTHOR === $type;
}

/**
 * Validate archive:user:id:<id>.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_user_id( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_AUTHOR === $type && $id === (int) $setting[3];
}

/**
 * Validate archive:user:role:<role>.
 *
 * @since 4.0.10
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_user_role( $type, $subtype, $id, $setting ) {
	$user = get_userdata( $id );

	if ( ! $user ) {
		return false;
	}

	if ( 'administrator' === $setting[3] && is_super_admin( $user->ID ) ) {
		// Superadmins may:
		// - have a low-level role assigned in the current site
		// - not be added to the site at all
		// in either case they are treated as administrators so we have to handle this edge case.
		return true;
	}

	return ET_Theme_Builder_Request::TYPE_AUTHOR === $type && in_array( $setting[3], $user->roles, true );
}

/**
 * Validate archive:date:all.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_archive_date_all( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_DATE === $type;
}

/**
 * Validate search.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_search( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_SEARCH === $type;
}

/**
 * Validate 404.
 *
 * @since 4.0
 *
 * @param string   $type    Type.
 * @param string   $subtype Subtype.
 * @param integer  $id      ID.
 * @param string[] $setting Setting.
 *
 * @return bool
 */
function et_theme_builder_template_setting_validate_404( $type, $subtype, $id, $setting ) {
	return ET_Theme_Builder_Request::TYPE_404 === $type;
}

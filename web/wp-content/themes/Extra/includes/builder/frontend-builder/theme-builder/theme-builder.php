<?php
if ( ! defined( 'ET_THEME_BUILDER_DIR' ) ) {
	define( 'ET_THEME_BUILDER_DIR', ET_BUILDER_DIR . 'frontend-builder/theme-builder/' );
}

if ( ! defined( 'ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE' ) ) {
	define( 'ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE', 'et_theme_builder' );
}

if ( ! defined( 'ET_THEME_BUILDER_TEMPLATE_POST_TYPE' ) ) {
	define( 'ET_THEME_BUILDER_TEMPLATE_POST_TYPE', 'et_template' );
}

if ( ! defined( 'ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE' ) ) {
	define( 'ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE', 'et_header_layout' );
}

if ( ! defined( 'ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE' ) ) {
	define( 'ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE', 'et_body_layout' );
}

if ( ! defined( 'ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE' ) ) {
	define( 'ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE', 'et_footer_layout' );
}

if ( ! defined( 'ET_THEME_BUILDER_SETTING_SEPARATOR' ) ) {
	// Must be a single character.
	define( 'ET_THEME_BUILDER_SETTING_SEPARATOR', ':' );
}

if ( ! defined( 'ET_THEME_BUILDER_DYNAMIC_CONTENT_REGEX' ) ) {
	define( 'ET_THEME_BUILDER_DYNAMIC_CONTENT_REGEX', '/@ET-DC@(.*?)@/' );
}

require_once ET_THEME_BUILDER_DIR . 'ThemeBuilderApiErrors.php';
require_once ET_THEME_BUILDER_DIR . 'ThemeBuilderRequest.php';
require_once ET_THEME_BUILDER_DIR . 'template-setting-validations.php';
require_once ET_THEME_BUILDER_DIR . 'api.php';
require_once ET_THEME_BUILDER_DIR . 'admin.php';
require_once ET_THEME_BUILDER_DIR . 'frontend.php';
require_once ET_THEME_BUILDER_DIR . 'dynamic-content.php';
require_once ET_THEME_BUILDER_DIR . 'TBItemLibrary.php';
require_once ET_THEME_BUILDER_DIR . 'constants.php';
require_once ET_THEME_BUILDER_DIR . 'LocalLibraryItemEditor.php';
require_once ET_THEME_BUILDER_DIR . 'local-library.php';
require_once ET_THEME_BUILDER_DIR . 'theme-builder-library.php';
require_once ET_THEME_BUILDER_DIR . 'LocalLibraryItem.php';

// Conditional Includes.
if ( et_is_woocommerce_plugin_active() ) {
	require_once ET_THEME_BUILDER_DIR . 'woocommerce.php';
	require_once ET_THEME_BUILDER_DIR . 'WoocommerceProductVariationPlaceholder.php';
	require_once ET_THEME_BUILDER_DIR . 'WoocommerceProductVariablePlaceholder.php';
	require_once ET_THEME_BUILDER_DIR . 'WoocommerceProductVariablePlaceholderDataStoreCPT.php';
}

if ( et_core_is_wpml_plugin_active() ) {
	require_once ET_THEME_BUILDER_DIR . 'wpml.php';
}

/**
 * Register all relevant Theme Builder entities such as post types.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_register_entities() {
	$publicly_queryable = isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] && et_pb_is_allowed( 'use_visual_builder' );

	register_post_type(
		ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE,
		array(
			'labels'             => array(
				'name'               => esc_html__( 'Theme Builders', 'et_builder' ),
				'singular_name'      => esc_html__( 'Theme Builder', 'et_builder' ),
				'add_new'            => esc_html__( 'Add New', 'et_builder' ),
				'add_new_item'       => esc_html__( 'Add New Theme Builder', 'et_builder' ),
				'edit_item'          => esc_html__( 'Edit Theme Builder', 'et_builder' ),
				'new_item'           => esc_html__( 'New Theme Builder', 'et_builder' ),
				'all_items'          => esc_html__( 'All Theme Builders', 'et_builder' ),
				'view_item'          => esc_html__( 'View Theme Builder', 'et_builder' ),
				'search_items'       => esc_html__( 'Search Theme Builders', 'et_builder' ),
				'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
				'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
				'parent_item_colon'  => '',
			),
			'can_export'         => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'public'             => false,
			'publicly_queryable' => false,
			'query_var'          => false,
			'show_ui'            => false,
			'show_in_rest'       => false,
			'rewrite'            => true,
			'supports'           => array( 'title', 'author' ),
		)
	);

	register_post_type(
		ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
		array(
			'labels'             => array(
				'name'               => esc_html__( 'Templates', 'et_builder' ),
				'singular_name'      => esc_html__( 'Template', 'et_builder' ),
				'add_new'            => esc_html__( 'Add New', 'et_builder' ),
				'add_new_item'       => esc_html__( 'Add New Template', 'et_builder' ),
				'edit_item'          => esc_html__( 'Edit Template', 'et_builder' ),
				'new_item'           => esc_html__( 'New Template', 'et_builder' ),
				'all_items'          => esc_html__( 'All Templates', 'et_builder' ),
				'view_item'          => esc_html__( 'View Template', 'et_builder' ),
				'search_items'       => esc_html__( 'Search Templates', 'et_builder' ),
				'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
				'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
				'parent_item_colon'  => '',
			),
			'can_export'         => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'public'             => false,
			'publicly_queryable' => $publicly_queryable,
			'query_var'          => false,
			'show_ui'            => false,
			'show_in_rest'       => false,
			'rewrite'            => true,
			'supports'           => array( 'title', 'author' ),
		)
	);

	register_post_type(
		ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
		array(
			'labels'             => array(
				'name'               => esc_html__( 'Header Templates', 'et_builder' ),
				'singular_name'      => esc_html__( 'Header Template', 'et_builder' ),
				'add_new'            => esc_html__( 'Add New', 'et_builder' ),
				'add_new_item'       => esc_html__( 'Add New Header Template', 'et_builder' ),
				'edit_item'          => esc_html__( 'Edit Header Template', 'et_builder' ),
				'new_item'           => esc_html__( 'New Header Template', 'et_builder' ),
				'all_items'          => esc_html__( 'All Header Templates', 'et_builder' ),
				'view_item'          => esc_html__( 'View Header Template', 'et_builder' ),
				'search_items'       => esc_html__( 'Search Header Templates', 'et_builder' ),
				'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
				'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
				'parent_item_colon'  => '',
			),
			'can_export'         => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'public'             => false,
			'publicly_queryable' => $publicly_queryable,
			'query_var'          => false,
			'show_ui'            => false,
			'show_in_rest'       => false,
			'rewrite'            => true,
			'supports'           => array( 'title', 'editor', 'author', 'revisions', 'comments' ),
		)
	);

	register_post_type(
		ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
		array(
			'labels'             => array(
				'name'               => esc_html__( 'Body Templates', 'et_builder' ),
				'singular_name'      => esc_html__( 'Body Template', 'et_builder' ),
				'add_new'            => esc_html__( 'Add New', 'et_builder' ),
				'add_new_item'       => esc_html__( 'Add New Body Template', 'et_builder' ),
				'edit_item'          => esc_html__( 'Edit Body Template', 'et_builder' ),
				'new_item'           => esc_html__( 'New Body Template', 'et_builder' ),
				'all_items'          => esc_html__( 'All Body Templates', 'et_builder' ),
				'view_item'          => esc_html__( 'View Body Template', 'et_builder' ),
				'search_items'       => esc_html__( 'Search Body Templates', 'et_builder' ),
				'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
				'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
				'parent_item_colon'  => '',
			),
			'can_export'         => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'public'             => false,
			'publicly_queryable' => $publicly_queryable,
			'query_var'          => false,
			'show_ui'            => false,
			'show_in_rest'       => false,
			'rewrite'            => true,
			'supports'           => array( 'title', 'editor', 'author', 'revisions', 'comments' ),
		)
	);

	register_post_type(
		ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
		array(
			'labels'             => array(
				'name'               => esc_html__( 'Footer Templates', 'et_builder' ),
				'singular_name'      => esc_html__( 'Footer Template', 'et_builder' ),
				'add_new'            => esc_html__( 'Add New', 'et_builder' ),
				'add_new_item'       => esc_html__( 'Add New Footer Template', 'et_builder' ),
				'edit_item'          => esc_html__( 'Edit Footer Template', 'et_builder' ),
				'new_item'           => esc_html__( 'New Footer Template', 'et_builder' ),
				'all_items'          => esc_html__( 'All Footer Templates', 'et_builder' ),
				'view_item'          => esc_html__( 'View Footer Template', 'et_builder' ),
				'search_items'       => esc_html__( 'Search Footer Templates', 'et_builder' ),
				'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
				'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
				'parent_item_colon'  => '',
			),
			'can_export'         => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'public'             => false,
			'publicly_queryable' => $publicly_queryable,
			'query_var'          => false,
			'show_ui'            => false,
			'show_in_rest'       => false,
			'rewrite'            => true,
			'supports'           => array( 'title', 'editor', 'author', 'revisions', 'comments' ),
		)
	);
}
add_action( 'init', 'et_theme_builder_register_entities', 11 );

/**
 * Get array of post types that can be layouts within templates.
 *
 * @since 4.0
 *
 * @return string[]
 */
function et_theme_builder_get_layout_post_types() {
	return array(
		ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
		ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
		ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
	);
}

/**
 * Convert 'header', 'body', 'footer' to the appropriate layout post type name.
 *
 * @since 4.0
 *
 * @param string $layout_type
 *
 * @return string
 */
function et_theme_builder_get_valid_layout_post_type( $layout_type ) {
	$map = array(
		'header' => ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
		'body'   => ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
		'footer' => ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
	);

	if ( ! isset( $map[ $layout_type ] ) ) {
		return '';
	}

	return $map[ $layout_type ];
}

/**
 * Get whether post type is a Theme Builder layout type.
 *
 * @since 4.0
 *
 * @param string $post_type
 *
 * @return boolean
 */
function et_theme_builder_is_layout_post_type( $post_type ) {
	return in_array( $post_type, et_theme_builder_get_layout_post_types(), true );
}

/**
 * Get list of post content module slugs.
 *
 * @since 4.0
 *
 * @return string[]
 */
function et_theme_builder_get_post_content_modules() {
	return array( 'et_pb_post_content', 'et_pb_fullwidth_post_content' );
}

/**
 * Filter post types with builder support by default.
 *
 * @since 4.0
 *
 * @param $post_types
 *
 * @return array
 */
function et_theme_builder_filter_builder_default_post_types( $post_types ) {
	return array_merge( $post_types, et_theme_builder_get_layout_post_types() );
}
add_filter( 'et_builder_default_post_types', 'et_theme_builder_filter_builder_default_post_types' );
add_filter( 'et_library_builder_post_types', 'et_theme_builder_filter_builder_default_post_types' );

/**
 * Filter post types which should be blocklisted from appearing as options when enabling/disabling the builder.
 *
 * @param $post_types
 *
 * @return array
 */
function et_theme_builder_filter_builder_post_type_options_blocklist( $post_types ) {
	return array_merge(
		$post_types,
		et_theme_builder_get_layout_post_types(),
		array( ET_THEME_BUILDER_TEMPLATE_POST_TYPE )
	);
}
add_filter( 'et_builder_post_type_options_blocklist', 'et_theme_builder_filter_builder_default_post_types' );

/**
 * Filter builder status for template area posts.
 *
 * @since 4.0
 *
 * @param $enabled
 * @param $post_id
 *
 * @return bool
 */
function et_theme_builder_filter_enable_builder_for_post_types( $enabled, $post_id ) {
	$post_type = get_post_type( $post_id );

	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		$enabled = true;
	}

	return $enabled;
}
add_filter( 'et_builder_fb_enabled_for_post', 'et_theme_builder_filter_enable_builder_for_post_types', 10, 2 );

/**
 * Get the theme builder post.
 *
 * @since 4.0
 *
 * @param boolean $live Get the live version or the draft one.
 * @param boolean $create Create the post if it does not exist.
 *
 * @return integer
 */
function et_theme_builder_get_theme_builder_post_id( $live, $create = true ) {
	$status = $live ? 'publish' : 'auto-draft';
	$query  = new WP_Query(
		array(
			'post_type'              => ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE,
			'post_status'            => $status,
			'posts_per_page'         => 1,
			'orderby'                => 'date',
			'order'                  => 'desc',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_et_library_theme_builder',
			'meta_compare'           => 'NOT EXISTS',
		)
	);

	if ( ! empty( $query->posts ) ) {
		return $query->posts[0];
	}

	if ( ! $create ) {
		return 0;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE,
			'post_status' => $status,
			'post_title'  => 'Theme Builder',
		)
	);

	return $post_id;
}

/**
 * Get the theme builder post's template IDs.
 *
 * @since 4.0
 *
 * @param boolean $live Get the live version or the draft one.
 *
 * @return integer[]
 */
function et_theme_builder_get_theme_builder_template_ids( $live, $theme_builder_id = 0 ) {
	$theme_builder_id = $theme_builder_id ? $theme_builder_id : et_theme_builder_get_theme_builder_post_id( $live, false );

	// Try to get the template Ids from the backup.
	// that maybe stored during saving templates process.
	// @see et_theme_builder_api_save function.
	$template_ids = get_option( 'et_tb_templates_backup_' . $theme_builder_id, false );

	// If there is no backup available, then query the post meta.
	if ( false === $template_ids ) {
		$template_ids = get_post_meta( $theme_builder_id, '_et_template', false );
	}

	$template_ids = is_array( $template_ids ) ? $template_ids : array();
	$template_ids = array_map( 'intval', $template_ids );

	return $template_ids;
}

/**
 * Get the theme builder post's templates.
 *
 * @since 4.0
 *
 * @param boolean $live Get the live version or the draft one.
 *
 * @return array
 */
function et_theme_builder_get_theme_builder_templates( $live, $theme_builder_id = 0 ) {
	return array_filter(
		array_map(
			'et_theme_builder_get_template',
			et_theme_builder_get_theme_builder_template_ids( $live, $theme_builder_id )
		)
	);
}

/**
 * Get a template.
 * Returns an empty array if the template is not found.
 *
 * @since 4.0
 *
 * @param integer $template_id
 *
 * @return array
 */
function et_theme_builder_get_template( $template_id ) {
	$post = get_post( $template_id );

	if ( null === $post || ET_THEME_BUILDER_TEMPLATE_POST_TYPE !== $post->post_type ) {
		return array();
	}

	$autogenerated_title = '1' === get_post_meta( $template_id, '_et_autogenerated_title', true );
	$header_id           = (int) get_post_meta( $post->ID, '_et_header_layout_id', true );
	$header_enabled      = get_post_meta( $post->ID, '_et_header_layout_enabled', true ) === '1';
	$body_id             = (int) get_post_meta( $post->ID, '_et_body_layout_id', true );
	$body_enabled        = get_post_meta( $post->ID, '_et_body_layout_enabled', true ) === '1';
	$footer_id           = (int) get_post_meta( $post->ID, '_et_footer_layout_id', true );
	$footer_enabled      = get_post_meta( $post->ID, '_et_footer_layout_enabled', true ) === '1';
	$use_on              = get_post_meta( $post->ID, '_et_use_on', false );
	$exclude_from        = get_post_meta( $post->ID, '_et_exclude_from', false );
	$item_id             = (int) get_post_meta( $template_id, '_et_library_item_id', true );
	$header_global       = '1' === get_post_meta( $template_id, '_et_header_layout_global', true );
	$body_global         = '1' === get_post_meta( $template_id, '_et_body_layout_global', true );
	$footer_global       = '1' === get_post_meta( $template_id, '_et_footer_layout_global', true );

	return array(
		'id'           => $post->ID,
		'item_id'      => $item_id,
		'default'      => get_post_meta( $post->ID, '_et_default', true ) === '1',
		'enabled'      => get_post_meta( $post->ID, '_et_enabled', true ) === '1',
		'title'        => $autogenerated_title ? '' : $post->post_title,
		'layouts'      => array(
			'header' => array(
				'id'       => $header_id,
				'enabled'  => $header_enabled,
				'override' => 0 !== $header_id || false === $header_enabled,
				'global'   => $header_global,
			),
			'body'   => array(
				'id'       => $body_id,
				'enabled'  => $body_enabled,
				'override' => 0 !== $body_id || false === $body_enabled,
				'global'   => $body_global,
			),
			'footer' => array(
				'id'       => $footer_id,
				'enabled'  => $footer_enabled,
				'override' => 0 !== $footer_id || false === $footer_enabled,
				'global'   => $footer_global,
			),
		),
		'use_on'       => is_array( $use_on ) ? $use_on : array(),
		'exclude_from' => is_array( $exclude_from ) ? $exclude_from : array(),
	);
}

/**
 * Trash the theme builder draft and any unused theme builder templates and layouts.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_trash_draft_and_unused_posts() {
	$mark_meta_key  = '_et_theme_builder_marked_as_unused';
	$live_id        = et_theme_builder_get_theme_builder_post_id( true, false );
	$draft_id       = et_theme_builder_get_theme_builder_post_id( false, false );
	$post_types     = array(
		ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
		ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
		ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
		ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
	);
	$has_permission = current_user_can( 'delete_post', $draft_id );

	if ( $draft_id > 0 && $has_permission ) {
		wp_trash_post( $draft_id );
	}

	$used_templates = get_post_meta( $live_id, '_et_template', false );
	$used_templates = is_array( $used_templates ) ? array_map( 'intval', $used_templates ) : array();
	$used_posts     = array();

	foreach ( $used_templates as $template_id ) {
		$used_posts[] = $template_id;
		$used_posts[] = (int) get_post_meta( $template_id, '_et_header_layout_id', true );
		$used_posts[] = (int) get_post_meta( $template_id, '_et_body_layout_id', true );
		$used_posts[] = (int) get_post_meta( $template_id, '_et_footer_layout_id', true );
	}

	$used_posts = array_filter( $used_posts );

	// Unmark all used posts.
	foreach ( $used_posts as $post_id ) {
		delete_post_meta( $post_id, $mark_meta_key );
	}

	// Mark unreferenced layouts for trashing.
	$posts_to_mark = new WP_Query(
		array(
			'post_type'              => $post_types,
			'post__not_in'           => $used_posts,
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'meta_query'             => array(
				array(
					'key'     => $mark_meta_key,
					'compare' => 'NOT EXISTS',
					'value'   => 'https://core.trac.wordpress.org/ticket/23268',
				),
			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $posts_to_mark->posts as $post_id ) {
		update_post_meta( $post_id, $mark_meta_key, date( 'Y-m-d H:i:s' ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date -- No need to use gmdate.
	}

	// Trash any posts marked more than 7 days ago.
	// We only trash up to 50 posts at a time in order to avoid performance issues.
	// Any leftover posts will be cleaned up eventually whenever this is called again.
	$posts_to_trash = new WP_Query(
		array(
			'post_type'              => $post_types,
			'posts_per_page'         => 50,
			'fields'                 => 'ids',
			'meta_query'             => array(
				array(
					'key'     => $mark_meta_key,
					'compare' => '<',
					'value'   => date( 'Y-m-d H:i:s', time() - 60 * 60 * 24 * 7 ), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date -- No need to use gmdate.
					'type'    => 'DATE',
				),
			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $posts_to_trash->posts as $post_id ) {
		$has_permission = current_user_can( 'delete_post', $post_id ) && in_array( get_post_type( $post_id ), $post_types, true );

		if ( $has_permission ) {
			wp_trash_post( $post_id );
		}
	}
}

/**
 * Get the template settings options for a given post type.
 *
 * @since 4.0
 *
 * @param string $post_type_name
 *
 * @return array
 */
function et_theme_builder_get_template_settings_options_for_post_type( $post_type_name ) {
	$post_type = get_post_type_object( $post_type_name );

	if ( null === $post_type ) {
		return array();
	}

	$post_type_plural = ucwords( $post_type->labels->name );
	$taxonomies       = get_object_taxonomies( $post_type_name, 'objects' );

	$group = array(
		'label'    => et_core_intentionally_unescaped( $post_type_plural, 'react_jsx' ),
		'settings' => array(
			array(
				'id'       => implode(
					ET_THEME_BUILDER_SETTING_SEPARATOR,
					array( 'singular', 'post_type', $post_type_name, 'all' )
				),
				// Translators: %1$s: Post type plural name.
				'label'    => et_core_intentionally_unescaped( sprintf( __( 'All %1$s', 'et_builder' ), $post_type_plural ), 'react_jsx' ),
				'priority' => 70,
				'validate' => 'et_theme_builder_template_setting_validate_singular_post_type_all',
			),
		),
	);

	if ( 'page' === $post_type_name ) {
		$group['settings'][] = array(
			'id'       => 'homepage',
			'label'    => et_core_intentionally_unescaped( __( 'Homepage', 'et_builder' ), 'react_jsx' ),
			'priority' => 110,
			'validate' => 'et_theme_builder_template_setting_validate_homepage',
		);
	} elseif ( 'post' === $post_type_name || $post_type->has_archive ) {
		$group['settings'][] = array(
			'id'       => implode(
				ET_THEME_BUILDER_SETTING_SEPARATOR,
				array( 'archive', 'post_type', $post_type_name )
			),
			'label'    => 'post' === $post_type_name
				? et_core_intentionally_unescaped( __( 'Blog', 'et_builder' ), 'react_jsx' )
				// Translators: %1$s: Post type plural name.
				: et_core_intentionally_unescaped( sprintf( __( '%1$s Archive Page', 'et_builder' ), $post_type_plural ), 'react_jsx' ),
			'title'    => trim( str_replace( home_url(), '', get_post_type_archive_link( $post_type_name ) ), '/' ),
			'priority' => 60,
			'validate' => 'et_theme_builder_template_setting_validate_archive_post_type',
		);
	}

	foreach ( $taxonomies as $taxonomy ) {
		/**
		 * Filters whether the given taxonomy should be used to generate the following template settings:
		 * - Posts with Specific %
		 *
		 * @since 4.3.3
		 *
		 * @param boolean $show
		 */
		$show = apply_filters( 'et_theme_builder_template_settings_options_posts_with_specific_term', $taxonomy->show_ui );

		if ( ! $show ) {
			continue;
		}

		$taxonomy_plural  = ucwords( $taxonomy->labels->name );
		$use_short_plural = in_array(
			$taxonomy->name,
			array(
				'project_category',
				'project_tag',
				'product_cat',
				'product_tag',
			),
			true
		);

		// Translators: %1$s: Post type plural name; %2$s: Taxonomy plural name.
		$label = et_core_intentionally_unescaped(
			sprintf(
				__( '%1$s with Specific %2$s', 'et_builder' ),
				$post_type_plural,
				$use_short_plural ? esc_html__( 'Tags', 'et_builder' ) : $taxonomy_plural
			),
			'react_jsx'
		);

		if ( in_array( $taxonomy->name, array( 'category', 'project_category', 'product_cat' ), true ) ) {
			// Translators: %1$s: Post type plural name; %2$s: Taxonomy plural name.
			$label = et_core_intentionally_unescaped(
				sprintf(
					__( '%1$s in Specific %2$s', 'et_builder' ),
					$post_type_plural,
					$use_short_plural ? esc_html__( 'Categories', 'et_builder' ) : $taxonomy_plural
				),
				'react_jsx'
			);
		}

		$group['settings'][] = array(
			'id'       => implode(
				ET_THEME_BUILDER_SETTING_SEPARATOR,
				array( 'singular', 'taxonomy', $taxonomy->name, 'term', 'id', '' )
			),
			// Translators: %1$s: Post type plural name; %2$s: Taxonomy plural name.
			'label'    => $label,
			'priority' => 80,
			'validate' => 'et_theme_builder_template_setting_validate_singular_taxonomy_term_id',
			'options'  => array(
				'label' => $taxonomy_plural,
				'type'  => 'taxonomy',
				'value' => $taxonomy->name,
			),
		);
	}

	$group['settings'][] = array(
		'id'       => implode(
			ET_THEME_BUILDER_SETTING_SEPARATOR,
			array( 'singular', 'post_type', $post_type_name, 'id', '' )
		),
		// Translators: %1$s: Post type plural name.
		'label'    => et_core_intentionally_unescaped( sprintf( __( 'Specific %1$s', 'et_builder' ), $post_type_plural ), 'react_jsx' ),
		'priority' => 100,
		'validate' => 'et_theme_builder_template_setting_validate_singular_post_type_id',
		'options'  => array(
			'label' => $post_type_plural,
			'type'  => 'post_type',
			'value' => $post_type_name,
		),
	);

	if ( is_post_type_hierarchical( $post_type_name ) ) {
		$group['settings'][] = array(
			'id'       => implode(
				ET_THEME_BUILDER_SETTING_SEPARATOR,
				array( 'singular', 'post_type', $post_type_name, 'children', 'id', '' )
			),
			// Translators: %1$s: Post type plural name.
			'label'    => et_core_intentionally_unescaped( sprintf( __( 'Children of Specific %1$s', 'et_builder' ), $post_type_plural ), 'react_jsx' ),
			'priority' => 90,
			'validate' => 'et_theme_builder_template_setting_validate_singular_post_type_children_id',
			'options'  => array(
				'label' => $post_type_plural,
				'type'  => 'post_type',
				'value' => $post_type_name,
			),
		);
	}

	return $group;
}


/**
 * Get the template settings options for all archive pages.
 *
 * @since 4.0
 *
 * @return array
 */
function et_theme_builder_get_template_settings_options_for_archive_pages() {
	$taxonomies = get_taxonomies(
		array(
			'public'   => true,
			'show_ui'  => true,
			'_builtin' => false,
		),
		'objects'
	);

	ksort( $taxonomies );

	$taxonomies = array_merge(
		array(
			'category' => get_taxonomy( 'category' ),
			'post_tag' => get_taxonomy( 'post_tag' ),
		),
		$taxonomies
	);

	$group = array(
		'label'    => et_core_intentionally_unescaped( __( 'Archive Pages', 'et_builder' ), 'react_jsx' ),
		'settings' => array(
			array(
				'id'       => implode(
					ET_THEME_BUILDER_SETTING_SEPARATOR,
					array( 'archive', 'all' )
				),
				'label'    => et_core_intentionally_unescaped( __( 'All Archive Pages', 'et_builder' ), 'react_jsx' ),
				'priority' => 30,
				'validate' => 'et_theme_builder_template_setting_validate_archive_all',
			),
		),
	);

	foreach ( $taxonomies as $taxonomy ) {
		/**
		 * Filters whether the given taxonomy should be used to generate the following template settings:
		 * - All % Pages
		 * - Specific % Pages
		 *
		 * @since 4.3.3
		 *
		 * @param boolean $show
		 * @param object $taxonomy
		 */
		$show = apply_filters( 'et_theme_builder_template_settings_options_term_pages', $taxonomy->public && $taxonomy->show_ui, $taxonomy );

		if ( ! $show ) {
			continue;
		}

		$taxonomy_plural = ucwords( $taxonomy->labels->name );
		$taxonomy_name   = $taxonomy_plural;

		if ( 'product_cat' === $taxonomy->name ) {
			// WooCommerce registers Product Categories with a singular name of Category instead of Product Category...
			$taxonomy_name = __( 'Product Category', 'et_builder' );
		} elseif ( false !== strpos( $taxonomy->name, 'cat' ) ) {
			// Use singular for Category.
			$taxonomy_name = ucwords( $taxonomy->labels->singular_name );
		}

		$group['settings'][] = array(
			'id'       => implode(
				ET_THEME_BUILDER_SETTING_SEPARATOR,
				array( 'archive', 'taxonomy', $taxonomy->name, 'all' )
			),
			// Translators: %1$s: Taxonomy name.
			'label'    => et_core_intentionally_unescaped( sprintf( __( 'All %1$s Pages', 'et_builder' ), $taxonomy_name ), 'react_jsx' ),
			'priority' => 70,
			'validate' => 'et_theme_builder_template_setting_validate_archive_taxonomy_all',
		);

		$group['settings'][] = array(
			'id'       => implode(
				ET_THEME_BUILDER_SETTING_SEPARATOR,
				array( 'archive', 'taxonomy', $taxonomy->name, 'term', 'id', '' )
			),
			// Translators: %1$s: Taxonomy name.
			'label'    => et_core_intentionally_unescaped( sprintf( __( 'Specific %1$s Pages', 'et_builder' ), $taxonomy_name ), 'react_jsx' ),
			'priority' => 75,
			'validate' => 'et_theme_builder_template_setting_validate_archive_taxonomy_term_id',
			'options'  => array(
				'label' => $taxonomy_plural,
				'type'  => 'taxonomy',
				'value' => $taxonomy->name,
			),
		);
	}

	$group['settings'][] = array(
		'id'       => implode(
			ET_THEME_BUILDER_SETTING_SEPARATOR,
			array( 'archive', 'user', 'all' )
		),
		'label'    => et_core_intentionally_unescaped( __( 'All Author Pages', 'et_builder' ), 'react_jsx' ),
		'priority' => 50,
		'validate' => 'et_theme_builder_template_setting_validate_archive_user_all',
	);

	$group['settings'][] = array(
		'id'       => implode(
			ET_THEME_BUILDER_SETTING_SEPARATOR,
			array( 'archive', 'user', 'id', '' )
		),
		'label'    => et_core_intentionally_unescaped( __( 'Specific Author Page', 'et_builder' ), 'react_jsx' ),
		'priority' => 55,
		'validate' => 'et_theme_builder_template_setting_validate_archive_user_id',
		'options'  => array(
			'label' => et_core_intentionally_unescaped( __( 'Users', 'et_builder' ), 'react_jsx' ),
			'type'  => 'user',
			'value' => '',
		),
	);

	$group['settings'][] = array(
		'id'       => implode(
			ET_THEME_BUILDER_SETTING_SEPARATOR,
			array( 'archive', 'user', 'role', '' )
		),
		'label'    => et_core_intentionally_unescaped( __( 'Specific Author Page By Role', 'et_builder' ), 'react_jsx' ),
		'priority' => 53,
		'validate' => 'et_theme_builder_template_setting_validate_archive_user_role',
		'options'  => array(
			'label' => et_core_intentionally_unescaped( __( 'Roles', 'et_builder' ), 'react_jsx' ),
			'type'  => 'user_role',
			'value' => '',
		),
	);

	$group['settings'][] = array(
		'id'       => implode(
			ET_THEME_BUILDER_SETTING_SEPARATOR,
			array( 'archive', 'date', 'all' )
		),
		'label'    => et_core_intentionally_unescaped( __( 'All Date Pages', 'et_builder' ), 'react_jsx' ),
		'priority' => 40,
		'validate' => 'et_theme_builder_template_setting_validate_archive_date_all',
	);

	$_utils = ET_Core_Data_Utils::instance();

	// Order settings alphabetically by label.
	$group['settings'] = $_utils->array_sort_by( $group['settings'], 'label' );

	return $group;
}

/**
 * Get array of template setting options.
 * Settings that have children should have a trailing ET_THEME_BUILDER_SETTING_SEPARATOR in their id.
 * Settings that have children should have their id be unique even without the trailing ET_THEME_BUILDER_SETTING_SEPARATOR.
 *
 * @since 4.0
 *
 * @return array
 */
function et_theme_builder_get_template_settings_options() {
	$post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		)
	);
	sort( $post_types );

	$options = array(
		'page'    => et_theme_builder_get_template_settings_options_for_post_type( 'page' ),
		'post'    => et_theme_builder_get_template_settings_options_for_post_type( 'post' ),
		'archive' => et_theme_builder_get_template_settings_options_for_archive_pages(),
	);

	foreach ( $post_types as $post_type_name ) {
		$options[ $post_type_name ] = et_theme_builder_get_template_settings_options_for_post_type( $post_type_name );
	}

	$options['other'] = array(
		'label'    => et_core_intentionally_unescaped( __( 'Other', 'et_builder' ), 'react_jsx' ),
		'settings' => array(
			array(
				'id'       => 'search',
				'label'    => et_core_intentionally_unescaped( __( 'Search Results', 'et_builder' ), 'react_jsx' ),
				'priority' => 1,
				'validate' => 'et_theme_builder_template_setting_validate_search',
			),
			array(
				'id'       => '404',
				'label'    => et_core_intentionally_unescaped( __( '404 Page', 'et_builder' ), 'react_jsx' ),
				'priority' => 1,
				'validate' => 'et_theme_builder_template_setting_validate_404',
			),
		),
	);

	/**
	 * Filters available template settings options.
	 *
	 * @since 4.0
	 *
	 * @param array
	 */
	$options = apply_filters( 'et_theme_builder_template_settings_options', $options );

	return $options;
}

/**
 * Get flat array of template setting options from the current live and draft theme builder posts.
 *
 * @since 4.0
 *
 * @return array[]
 */
function et_theme_builder_get_template_settings_options_for_preloading( $tb_id ) {
	$templates   = array_unique(
		array_merge(
			et_theme_builder_get_theme_builder_template_ids( true, $tb_id ),
			et_theme_builder_get_theme_builder_template_ids( false, $tb_id )
		)
	);
	$setting_ids = array();

	foreach ( $templates as $template_id ) {
		$use_on       = get_post_meta( $template_id, '_et_use_on', false );
		$exclude_from = get_post_meta( $template_id, '_et_exclude_from', false );

		if ( ! is_array( $use_on ) ) {
			$use_on = array();
		}

		if ( ! is_array( $exclude_from ) ) {
			$exclude_from = array();
		}

		$setting_ids = array_merge( $setting_ids, $use_on, $exclude_from );
	}

	return et_theme_builder_load_template_setting_options( array_unique( $setting_ids ) );
}

/**
 * Sanitize an array of use_on/exclude_from conditions stripping out invalid ones.
 *
 * @since 4.0
 *
 * @param string[] $setting_ids
 *
 * @return string[]
 */
function et_theme_builder_load_template_setting_options( $setting_ids ) {
	$flat_parent_settings = et_theme_builder_get_flat_template_settings_options();
	$groups               = array();

	foreach ( $setting_ids as $setting_id ) {
		$parent_id = explode( ET_THEME_BUILDER_SETTING_SEPARATOR, $setting_id );
		$entity_id = implode( '', array_slice( $parent_id, -1 ) );
		$parent_id = array_slice( $parent_id, 0, -1 );
		$parent_id = implode( ET_THEME_BUILDER_SETTING_SEPARATOR, $parent_id ) . ET_THEME_BUILDER_SETTING_SEPARATOR;

		if ( ! isset( $flat_parent_settings[ $parent_id ] ) ) {
			// Top-level, invalid or unknown setting.
			continue;
		}

		if ( ! isset( $groups[ $parent_id ] ) ) {
			$groups[ $parent_id ] = array(
				'parent'   => $flat_parent_settings[ $parent_id ],
				'settings' => array(),
			);
		}

		$groups[ $parent_id ]['settings'][ $setting_id ] = $entity_id;
	}

	$settings = array();

	foreach ( $groups as $parent_id => $group ) {
		$settings = array_merge(
			$settings,
			et_theme_builder_get_template_setting_child_options( $group['parent'], $group['settings'] )
		);
	}

	return $settings;
}

/**
 * Get a flat array of template setting options.
 *
 * @since 4.0
 *
 * @return array
 */
function et_theme_builder_get_flat_template_settings_options() {
	$settings = et_theme_builder_get_template_settings_options();
	$flat     = array();

	foreach ( $settings as $group ) {
		foreach ( $group['settings'] as $setting ) {
			$flat[ $setting['id'] ] = $setting;
		}
	}

	return $flat;
}

function et_theme_builder_get_template_setting_child_options( $parent, $include = array(), $search = '', $page = 1, $per_page = 30 ) {
	$include = array_map( 'intval', $include );

	if ( ! empty( $include ) ) {
		$search   = '';
		$page     = 1;
		$per_page = -1;
	}

	$page   = $page >= 1 ? $page : 1;
	$values = array();

	/**
	 * Fires before loading child options from the database.
	 *
	 * @since 4.2
	 *
	 * @param string $parent_id
	 * @param string $child_type
	 * @param string $child_value
	 */
	do_action( 'et_theme_builder_before_get_template_setting_child_options', $parent['id'], $parent['options']['type'], $parent['options']['value'] );

	switch ( $parent['options']['type'] ) {
		case 'post_type':
			$posts = get_posts(
				array(
					'post_type'      => $parent['options']['value'],
					'post_status'    => 'any',
					'post__in'       => $include,
					's'              => $search,
					'posts_per_page' => $per_page,
					'paged'          => $page,
				)
			);

			foreach ( $posts as $post ) {
				$id            = $parent['id'] . $post->ID;
				$values[ $id ] = array(
					'id'       => $id,
					'parent'   => $parent['id'],
					'label'    => et_core_intentionally_unescaped( $post->post_title, 'react_jsx' ),
					'title'    => et_core_intentionally_unescaped( $post->post_name, 'react_jsx' ),
					'priority' => $parent['priority'],
					'validate' => $parent['validate'],
				);
			}
			break;

		case 'taxonomy':
			$terms = get_terms(
				array(
					'taxonomy'   => $parent['options']['value'],
					'hide_empty' => false,
					'include'    => $include,
					'search'     => $search,
					'number'     => -1 === $per_page ? false : $per_page,
					'offset'     => -1 !== $per_page ? ( $page - 1 ) * $per_page : 0,
				)
			);

			foreach ( $terms as $term ) {
				$id            = $parent['id'] . $term->term_id;
				$values[ $id ] = array(
					'id'       => $id,
					'parent'   => $parent['id'],
					'label'    => et_core_intentionally_unescaped( $term->name, 'react_jsx' ),
					'title'    => et_core_intentionally_unescaped( $term->slug, 'react_jsx' ),
					'priority' => $parent['priority'],
					'validate' => $parent['validate'],
				);
			}
			break;

		case 'user':
			$users = get_users(
				array(
					'include' => $include,
					'search'  => $search,
					'number'  => $per_page,
					'paged'   => $page,
				)
			);

			foreach ( $users as $user ) {
				$id            = $parent['id'] . $user->ID;
				$values[ $id ] = array(
					'id'       => $id,
					'parent'   => $parent['id'],
					'label'    => et_core_intentionally_unescaped( $user->display_name, 'react_jsx' ),
					'title'    => et_core_intentionally_unescaped( $user->user_login, 'react_jsx' ),
					'priority' => $parent['priority'],
					'validate' => $parent['validate'],
				);
			}
			break;

		case 'user_role':
			$roles = wp_roles()->get_names();

			foreach ( $roles as $role => $label ) {
				$id            = $parent['id'] . $role;
				$values[ $id ] = array(
					'id'       => $id,
					'parent'   => $parent['id'],
					'label'    => et_core_intentionally_unescaped( $label, 'react_jsx' ),
					'title'    => et_core_intentionally_unescaped( $role, 'react_jsx' ),
					'priority' => $parent['priority'],
					'validate' => $parent['validate'],
				);
			}
			break;
	}

	/**
	 * Fires after loading child options from the database.
	 *
	 * @since 4.2
	 *
	 * @param string $parent_id
	 * @param string $child_type
	 * @param string $child_value
	 */
	do_action( 'et_theme_builder_after_get_template_setting_child_options', $parent['id'], $parent['options']['type'], $parent['options']['value'] );

	return $values;
}

/**
 * Get the template and its layouts, if any, for the given request.
 *
 * @since 4.0
 *
 * @param ET_Theme_Builder_Request $request Request to check against. Defaults to the current one.
 * @param bool                     $cache Cache the result or not, regardless of whether any layouts should be loaded.
 * @param bool                     $load_from_cache Load the cached result for the given post ID, if available.
 *
 * @return array Array of layouts or an empty array if no layouts should be loaded.
 */
function et_theme_builder_get_template_layouts( $request = null, $cache = true, $load_from_cache = true ) {
	static $store = array();

	// Ignore TB templates when editing cloud items.
	if ( isset( $_GET['cloudItem'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		return array();
	}

	if ( null === $request ) {
		if ( is_embed() ) {
			// Ignore TB templates when displaying posts intended for embedding.
			return array();
		}

		if ( is_et_pb_preview() ) {
			// Ignore TB templates when previewing.
			return array();
		}

		$request = ET_Theme_Builder_Request::from_current();
	}

	if ( null === $request || ET_GB_Block_Layout::is_layout_block_preview() ) {
		return array();
	}

	$cache_key = "{$request->get_type()}:{$request->get_subtype()}:{$request->get_id()}";

	if ( $load_from_cache && isset( $store[ $cache_key ] ) ) {
		return $store[ $cache_key ];
	}

	$post_type = ET_Theme_Builder_Request::TYPE_SINGULAR === $request->get_type() ? $request->get_subtype() : '';
	$layouts   = array();

	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		// We are currently editing a layout in the VB.
		$layouts = array_replace(
			array(
				ET_THEME_BUILDER_TEMPLATE_POST_TYPE      => 0,
				ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE => array(
					'id'       => 0,
					'enabled'  => false,
					'override' => true,
				),
				ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE   => array(
					'id'       => 0,
					'enabled'  => false,
					'override' => true,
				),
				ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE => array(
					'id'       => 0,
					'enabled'  => false,
					'override' => true,
				),
			),
			array(
				$post_type => array(
					'id'       => $request->get_id(),
					'enabled'  => true,
					'override' => true,
				),
			)
		);
	} else {
		// We are currently displaying a template in the FE.
		$templates   = et_theme_builder_get_theme_builder_templates( true, false );
		$settings    = et_theme_builder_get_flat_template_settings_options();
		$template    = $request->get_template( $templates, $settings );
		$is_singular = is_singular();

		if ( ! empty( $template ) ) {
			$is_default      = $template['default'];
			$override_header = $template['layouts']['header']['override'];
			$override_body   = $template['layouts']['body']['override'];
			$override_footer = $template['layouts']['footer']['override'];

			// The Default Website Template has a special case - it should not take over if
			// it does not override any areas otherwise it will take over ALL site pages.
			if ( ! $is_default || $override_header || $override_body || $override_footer ) {
				$layouts = array(
					ET_THEME_BUILDER_TEMPLATE_POST_TYPE    => $is_singular ? $template['id'] : false,
					ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE => $template['layouts']['header'],
					ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE => $template['layouts']['body'],
					ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE => $template['layouts']['footer'],
				);
			}
		}
	}

	/**
	 * Filter template layouts.
	 *
	 * @since 4.0
	 *
	 * @param array $layouts
	 */
	$layouts = apply_filters( 'et_theme_builder_template_layouts', $layouts );

	// Add AB Subjects array.
	foreach ( $layouts as $key => $layout ) {
		if ( is_array( $layout ) && $layout['override'] ) {
			$layouts[ $key ]['et_pb_ab_subjects'] = et_pb_ab_get_subjects( $layout['id'] );
		}
	}

	if ( $cache ) {
		$store[ $cache_key ] = $layouts;
	}

	return $layouts;
}

/**
 * Get whether TB overrides the specified layout for the current request.
 *
 * @since 4.0.6
 *
 * @param string $layout Layout post type.
 *
 * @return boolean
 */
function et_theme_builder_overrides_layout( $layout ) {
	$layouts = et_theme_builder_get_template_layouts();

	return ! empty( $layouts ) && $layouts[ $layout ]['override'];
}

/**
 * Get whether the specified layout will properly render the real post content.
 *
 * @since 4.0
 *
 * @param array $layout
 *
 * @return boolean
 */
function et_theme_builder_layout_has_post_content( $layout ) {
	if ( ! $layout['override'] ) {
		// The layout does not override the content so post content will render.
		return true;
	}

	if ( $layout['enabled'] ) {
		$content = get_post_field( 'post_content', $layout['id'] );
		$modules = et_theme_builder_get_post_content_modules();

		foreach ( $modules as $module ) {
			if ( has_shortcode( $content, $module ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Create or update a Theme Builder template.
 *
 * @since 4.0
 *
 * @param integer $theme_builder_id Theme builder ID.
 * @param array   $template         Template.
 * @param boolean $allow_default    Allow default.
 *
 * @return (integer|false) Return false on failure.
 */
function et_theme_builder_store_template( $theme_builder_id, $template, $allow_default ) {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die();
	}

	$_                   = et_();
	$raw_post_id         = $_->array_get( $template, 'id', 0 );
	$post_id             = is_numeric( $raw_post_id ) ? (int) $raw_post_id : 0;
	$title               = sanitize_text_field( $_->array_get( $template, 'title', '' ) );
	$default             = $allow_default && '1' === $_->array_get( $template, 'default', '1' );
	$enabled             = '1' === $_->array_get( $template, 'enabled', '1' );
	$header_id           = (int) $_->array_get( $template, 'layouts.header.id', 0 );
	$header_enabled      = (bool) $_->array_get( $template, 'layouts.header.enabled', true );
	$body_id             = (int) $_->array_get( $template, 'layouts.body.id', 0 );
	$body_enabled        = (bool) $_->array_get( $template, 'layouts.body.enabled', true );
	$footer_id           = (int) $_->array_get( $template, 'layouts.footer.id', 0 );
	$footer_enabled      = (bool) $_->array_get( $template, 'layouts.footer.enabled', true );
	$use_on              = array_map( 'sanitize_text_field', $_->array_get( $template, 'use_on', array() ) );
	$exclude_from        = array_map( 'sanitize_text_field', $_->array_get( $template, 'exclude_from', array() ) );
	$exists              = $post_id > 0 && ET_THEME_BUILDER_TEMPLATE_POST_TYPE === get_post_type( $post_id ) && 'publish' === get_post_status( $post_id );
	$autogenerated_title = '1' === $_->array_get( $template, 'autogenerated_title', '1' );
	$onboarding          = (bool) $_->array_get( $template, 'onboarding', '0' );

	if ( ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE !== get_post_type( $header_id ) || 'publish' !== get_post_status( $header_id ) ) {
		$header_id = 0;
	}

	if ( ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE !== get_post_type( $body_id ) || 'publish' !== get_post_status( $body_id ) ) {
		$body_id = 0;
	}

	if ( ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE !== get_post_type( $footer_id ) || 'publish' !== get_post_status( $footer_id ) ) {
		$footer_id = 0;
	}

	if ( $exists ) {
		// Preform update only when needed.
		if ( get_post_field( 'post_title', $post_id, 'raw' ) !== $title ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => $title,
				)
			);

			// Update layout title for each template.
			et_theme_builder_update_layout_title( $template );
		}

		update_post_meta( $post_id, '_et_onboarding_created', '0' );

	} else {
		$post_id = wp_insert_post(
			array(
				'post_type'   => ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => $title,
			)
		);

		if ( $onboarding ) {
			update_post_meta( $post_id, '_et_onboarding_created', '1' );
		}
	}

	if ( 0 === $post_id || is_wp_error( $post_id ) ) {
		return false;
	}

	$metas = array(
		'_et_autogenerated_title'   => $autogenerated_title ? '1' : '0',
		'_et_default'               => $default ? '1' : '0',
		'_et_enabled'               => $enabled ? '1' : '0',
		'_et_header_layout_id'      => $header_id,
		'_et_header_layout_enabled' => $header_enabled ? '1' : '0',
		'_et_body_layout_id'        => $body_id,
		'_et_body_layout_enabled'   => $body_enabled ? '1' : '0',
		'_et_footer_layout_id'      => $footer_id,
		'_et_footer_layout_enabled' => $footer_enabled ? '1' : '0',
	);

	foreach ( $metas as $key => $value ) {
		if ( strval( $value ) === strval( get_post_meta( $post_id, $key, true ) ) ) {
			continue;
		}

		update_post_meta( $post_id, $key, $value );
	}

	if ( $onboarding ) {
		$onboarding_use_on       = get_post_meta( $post_id, '_et_use_on' );
		$onboarding_exclude_from = get_post_meta( $post_id, '_et_exclude_from' );

		foreach ( $onboarding_use_on as $condition ) {
			add_post_meta( $post_id, '_et_old_use_on', $condition );
		}

		foreach ( $onboarding_exclude_from as $condition ) {
			add_post_meta( $post_id, '_et_old_exclude_from', $condition );
		}
	}

	// Handle _et_use_on meta.
	delete_post_meta( $post_id, '_et_use_on' );
	if ( $use_on ) {
		$use_on_unique = array_unique( $use_on );

		foreach ( $use_on_unique as $condition ) {
			add_post_meta( $post_id, '_et_use_on', $condition );
		}
	}

	// Handle _et_exclude_from meta.
	delete_post_meta( $post_id, '_et_exclude_from' );
	if ( $exclude_from ) {
		$exclude_from_unique = array_unique( $exclude_from );

		foreach ( $exclude_from_unique as $condition ) {
			add_post_meta( $post_id, '_et_exclude_from', $condition );
		}
	}

	return $post_id;
}

/**
 * Sanitize a Theme Builder template.
 *
 * @since 4.0
 *
 * @param array $template
 *
 * @return array
 */
function et_theme_builder_sanitize_template( $template ) {
	$_                   = et_();
	$autogenerated_title = $_->array_get( $template, 'autogenerated_title', '0' );
	$default             = $_->array_get( $template, 'default', '0' );
	$enabled             = $_->array_get( $template, 'enabled', '0' );
	$use_on              = $_->array_get( $template, 'use_on', array() );
	$exclude_from        = $_->array_get( $template, 'exclude_from', array() );
	$header_enabled      = $_->array_get( $template, 'layouts.header.enabled', '1' );
	$body_enabled        = $_->array_get( $template, 'layouts.body.enabled', '1' );
	$footer_enabled      = $_->array_get( $template, 'layouts.footer.enabled', '1' );

	$sanitized = array(
		'title'               => sanitize_text_field( $_->array_get( $template, 'title', '' ) ),
		'autogenerated_title' => true === $autogenerated_title || '1' === $autogenerated_title,
		'default'             => true === $default || '1' === $default,
		'enabled'             => true === $enabled || '1' === $enabled,
		'use_on'              => array_map( 'sanitize_text_field', $use_on ),
		'exclude_from'        => array_map( 'sanitize_text_field', $exclude_from ),
		'layouts'             => array(
			'header' => array(
				'id'      => (int) $_->array_get( $template, 'layouts.header.id', '0' ),
				'enabled' => true === $header_enabled || '1' === $header_enabled,
			),
			'body'   => array(
				'id'      => (int) $_->array_get( $template, 'layouts.body.id', '0' ),
				'enabled' => true === $body_enabled || '1' === $body_enabled,
			),
			'footer' => array(
				'id'      => (int) $_->array_get( $template, 'layouts.footer.id', '0' ),
				'enabled' => true === $footer_enabled || '1' === $footer_enabled,
			),
		),
	);

	return $sanitized;
}

/**
 * Insert a Theme Builder layout post.
 *
 * @since 4.0
 *
 * @param array $options
 *
 * @return integer|WP_Error
 */
function et_theme_builder_insert_layout( $options ) {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die();
	}

	$post_id = wp_insert_post(
		array_merge(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Theme Builder Layout',
			),
			$options
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	wp_set_object_terms( $post_id, 'layout', 'layout_type', true );
	et_builder_enable_for_post( $post_id );

	return $post_id;
}

/**
 * Overrides cache post_type so that TB custom post types and 'page' share the same files.
 *
 * @since 4.0
 *
 * @param string $post_type
 *
 * @return string.
 */
function et_theme_builder_cache_post_type( $post_type ) {
	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		// Use a generic name for all Theme Builder post type modules
		// as they are identical for most practical reasons.
		$post_type = 'page';
	}

	return $post_type;
}
add_filter( 'et_builder_cache_post_type', 'et_theme_builder_cache_post_type' );

/**
 * Decorate a page resource slug based on the current request and TB.
 *
 * @since 4.0.7
 *
 * @param integer|string $post_id
 * @param string         $resource_slug Resource slug.
 *
 * @return string
 */
function et_theme_builder_decorate_page_resource_slug( $post_id, $resource_slug ) {
	if ( ! is_numeric( $post_id ) || ! is_singular() ) {
		return $resource_slug;
	}

	$post_type = get_post_type( (int) $post_id );

	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		$resource_slug .= '-tb-for-' . ET_Post_Stack::get_main_post_id();
	} else {
		$layout_types = et_theme_builder_get_layout_post_types();
		$layouts      = et_theme_builder_get_template_layouts();

		foreach ( $layout_types as $type ) {
			if ( ! isset( $layouts[ $type ] ) || ! $layouts[ $type ]['override'] ) {
				continue;
			}

			$resource_slug .= '-tb-' . $layouts[ $type ]['id'];
		}
	}

	return $resource_slug;
}
add_filter( 'et_builder_cache_post_type', 'et_theme_builder_cache_post_type' );

/**
 * Clear cache of 3P caching plugins partially on the posts or all of them.
 *
 * @since 4.5.0
 *
 * @param string|array $post_ids 'all' or array of post IDs.
 *
 * @return void
 */
function et_theme_builder_clear_wp_cache( $post_ids = 'all' ) {
	if ( ! et_pb_detect_cache_plugins() ) {
		return;
	}

	if ( empty( $post_ids ) ) {
		return;
	}

	if ( 'all' === $post_ids ) {
		et_core_clear_wp_cache();
	} elseif ( is_array( $post_ids ) ) {
		foreach ( $post_ids as $post_id ) {
			et_core_clear_wp_cache( $post_id );
		}
	}
}

/**
 * Clear cache of 3P caching plugins fully or partially after TB layouts saved.
 *
 * Clear all the cache when the template updated is:
 * - Default template
 * - Used on archive, 404, or all posts
 * - Non static homepage
 *
 * @since 4.5.0
 *
 * @param int $layout_id
 *
 * @return void
 */
function et_theme_builder_clear_wp_post_cache( $layout_id = '' ) {
	$layout_type = get_post_type( $layout_id );

	if ( ! et_theme_builder_is_layout_post_type( $layout_type ) ) {
		return;
	}

	if ( ! et_pb_detect_cache_plugins() ) {
		return;
	}

	// Get template of current TB layout.
	$template = new WP_Query(
		array(
			'post_type'              => ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				'relation' => 'AND',
				array(
					'key'     => '_et_enabled',
					'value'   => '1',
					'compare' => '=',
				),
				array(
					'key'     => "_{$layout_type}_id",
					'value'   => $layout_id,
					'compare' => '=',
				),
				array(
					'key'     => "_{$layout_type}_enabled",
					'value'   => '1',
					'compare' => '=',
				),
				array(
					'key'     => '_et_theme_builder_marked_as_unused',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	if ( ! $template->have_posts() ) {
		return;
	}

	$_                   = et_();
	$template_id         = $_->array_get( $template->posts, '0' );
	$template_use_on     = get_post_meta( $template_id, '_et_use_on', false );
	$is_template_default = '1' === get_post_meta( $template_id, '_et_default', true );

	// Unassigned Template - False or empty _et_use_on means it's unassigned.
	if ( empty( $template_use_on ) ) {
		// Clear All - If the template is 'default' because it's enabled globally.
		if ( $is_template_default ) {
			et_theme_builder_clear_wp_cache( 'all' );
		}
		return;
	}

	$target_post_ids = array();

	foreach ( $template_use_on as $location ) {
		$location_pieces = explode( ':', $location );
		$location_first  = $_->array_get( $location_pieces, '0' );
		$location_last   = end( $location_pieces );

		if ( in_array( $location_first, array( 'archive', '404' ) ) || 'all' === $location_last ) {
			// Path: archive:user:id:{user_id}, singular:post_type:{post_type_slug}:all,
			// archive:taxonomy:{taxonomy_name}:all, etc.
			// Clear All - If the template is being used on 'archive:' or ':all' posts.
			$target_post_ids = 'all';
			break;
		} elseif ( 'homepage' === $location_first ) {
			// Path: homepage
			$homepage_id       = (int) get_option( 'page_on_front' );
			$target_post_ids[] = $homepage_id;
			if ( ! $homepage_id ) {
				// Clear All - If the homepage is non static page.
				$target_post_ids = 'all';
				break;
			}
		} elseif ( 'singular' === $location_first ) {
			$singular_type = $_->array_get( $location_pieces, '3' );

			if ( 'id' === $singular_type ) {
				// Path: singular:post_type:{post_type_slug}:id:{post_id}
				$target_post_ids[] = (int) $_->array_get( $location_pieces, '4' );
			} elseif ( 'children' === $singular_type ) {
				// Path: singular:post_type:{post_type_slug}:children:id:{post_id}
				$parent_id       = (int) $_->array_get( $location_pieces, '5' );
				$children_ids    = get_children(
					array(
						'posts_per_page' => -1,
						'post_parent'    => $parent_id,
						'fields'         => 'ids',
					)
				);
				$target_post_ids = array_merge( $target_post_ids, $children_ids );
			} elseif ( 'term' === $singular_type ) {
				// Path: singular:taxonomy:{taxonomy_name}:term:id:{term_id}
				$taxonomy        = $_->array_get( $location_pieces, '2' );
				$taxonomy_object = get_taxonomy( $taxonomy );
				$taxonomy_type   = ! empty( $taxonomy_object->object_type ) ? $_->array_get( $taxonomy_object->object_type, '0' ) : 'post';
				$term_id         = (int) $_->array_get( $location_pieces, '5' );
				$posts_ids       = get_posts(
					array(
						'posts_per_page' => -1,
						'fields'         => 'ids',
						'post_type'      => $taxonomy_type,
						'tax_query'      => array(
							array(
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $term_id,
							),
						),
					)
				);
				$target_post_ids = array_merge( $target_post_ids, $posts_ids );
			}
		} elseif ( 'woocommerce' === $location_first && et_is_woocommerce_plugin_active() && function_exists( 'wc_get_page_id' ) ) {
			// Path: woocommerce:my_account, woocommerce:cart, etc.
			$woocommerce_page    = str_replace( '_', '', $_->array_get( $location_pieces, '1' ) );
			$woocommerce_page_id = wc_get_page_id( $woocommerce_page );
			if ( $woocommerce_page_id ) {
				$target_post_ids[] = $woocommerce_page_id;
			}
		}
	}

	// Remove duplicate posts.
	if ( is_array( $target_post_ids ) ) {
		$target_post_ids = array_unique( $target_post_ids );
	}

	et_theme_builder_clear_wp_cache( $target_post_ids );
}

add_action( 'et_save_post', 'et_theme_builder_clear_wp_post_cache' );

/**
 *
 * Update layout title for each template
 *
 * @param array $template Theme Builder Template.
 */
function et_theme_builder_update_layout_title( $template ) {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die();
	}

	if ( empty( $template['layouts'] ) ) {
		return;
	}

	foreach ( $template['layouts'] as $layout ) {
		$layout_id = (int) $layout['id'];

		$new_layout_title  = sanitize_text_field( $template['title'] );
		$curr_layout_title = get_the_title( $layout_id );

		if ( ! $layout_id || $new_layout_title === $curr_layout_title ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'         => $layout_id,
				'post_title' => sanitize_text_field( $template['title'] ),
			)
		);
	}
}

/**
 * Get post content module placeholder html.
 *
 * @since 4.18.0
 *
 * @return string Placeholder html.
 */
function et_theme_builder_get_post_content_placeholder() {
	return '<div class="et_pb_section"><div class="et_pb_row"><div class="et_pb_column et_pb_column_4_4">
	<h1>Post Content Heading 1</h1>
	<p>Post Content Paragraph Text. Lorem ipsum dolor sit amet, <a href="#">consectetur adipiscing elit</a>. Ut vitae congue libero, nec finibus purus. Vestibulum egestas orci vel ornare venenatis. Sed et ultricies turpis. Donec sit amet rhoncus erat. Phasellus volutpat vitae mi eu aliquam.</p>
	<h2>Post Content Heading 2</h2>
	<p>Curabitur a commodo sapien, at pellentesque velit. Vestibulum ornare vulputate. Mauris tempus massa orci, vitae lacinia tortor maximus sit amet. In hac habitasse platea dictumst. Praesent id tincidunt dolor. Morbi gravida sapien convallis sapien tempus consequat. </p>
	<h3>Post Content Heading 3</h3>
	<blockquote>
	<p>Post Content Block Quote. Vehicula velit ut felis semper, non convallis dolor fermentum. Sed sapien nisl, tempus ut semper sed, congue quis leo. Integer nec suscipit lacus. Duis luctus eros dui, nec finibus lectus tempor nec. Pellentesque at tincidunt turpis.</p>
	</blockquote>
	<img src="' . ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA . '" alt="" />
	<h4>Post Content Heading 4</h4>
	<ul>
	<li>Vestibulum posuere</li>
	<li>Mi interdum nunc dignissim auctor</li>
	<li>Cras non dignissim quam, at volutpat massa</li>
	</ul>
	<h5>Post Content Heading 5</h5>
	<ol>
	<li>Ut mattis orci in scelerisque tempus</li>
	<li>Velit urna sagittis arcu</li>
	<li>Mon ultrices risus lectus non nisl</li>
	</ol>
	<h6>Post Content Heading 6</h6>
	<p>posuere nec lectus sit amet, pulvinar dapibus sapien. Donec placerat erat ac fermentum accumsan. Nunc in scelerisque dui. Etiam vitae purus velit. Proin dictum auctor mi, eu congue odio tempus et. Curabitur ac semper ligula. Praesent purus ligula, ultricies vel porta ac, elementum et lacus. Nullam vitae augue aliquet, condimentum est ut, vehicula sapien. Donec euismod, sem et elementum finibus, lacus mauris pulvinar enim, nec faucibus sapien neque quis sem. Vivamus suscipit tortor eget felis porttitor volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
	</div></div></div>';
}

/**
 * Adds Library editor admin body classes.
 *
 * @param string $classes Comma separated Classes string.
 * @return string
 */
function et_theme_builder_add_library_editor_body_class( $classes ) {
	if ( isset( $_GET['tb_template'] ) || isset( $_GET['tb_set'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		$classes = $classes . ' et-library-item-editor';
	}

	return $classes;
}
add_action( 'admin_body_class', 'et_theme_builder_add_library_editor_body_class' );

<?php
/**
 * Helpers needed for the WP Editor compatibility.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.14.8
 */

if ( ! defined( 'ET_WP_EDITOR_TEMPLATE_POST_TYPE' ) ) {
	define( 'ET_WP_EDITOR_TEMPLATE_POST_TYPE', 'wp_template' );
}

if ( ! defined( 'ET_WP_EDITOR_TEMPLATE_PART_POST_TYPE' ) ) {
	define( 'ET_WP_EDITOR_TEMPLATE_PART_POST_TYPE', 'wp_template_part' );
}

if ( ! function_exists( 'et_builder_get_wp_editor_template_post_types' ) ) {
	/**
	 * Get supported WP Editor template post types.
	 *
	 * At this moment, the list is:
	 * - wp_template
	 * - wp_template_part
	 *
	 * @since 4.14.8
	 *
	 * @return array List of supported WP Editor template post types.
	 */
	function et_builder_get_wp_editor_template_post_types() {
		// Supported WP Editor template post types.
		$post_types = array(
			ET_WP_EDITOR_TEMPLATE_POST_TYPE,
			ET_WP_EDITOR_TEMPLATE_PART_POST_TYPE,
		);

		return $post_types;
	}
}

if ( ! function_exists( 'et_builder_is_wp_editor_template_post_type' ) ) {
	/**
	 * Whether current post type is supported WP Editor template post type or not.
	 *
	 * @since 4.14.8
	 *
	 * @param string $type Template post type.
	 *
	 * @return boolean Post type check status.
	 */
	function et_builder_is_wp_editor_template_post_type( $type ) {
		return in_array( $type, et_builder_get_wp_editor_template_post_types(), true );
	}
}

if ( ! function_exists( 'et_builder_wp_editor_decorate_page_resource_slug' ) ) {
	/**
	 * Decorate a page resource slug based on the current request and WP Editor.
	 *
	 * @since 4.14.8
	 *
	 * @param integer|string $post_id       Post ID.
	 * @param string         $resource_slug Resource slug.
	 *
	 * @return string
	 */
	function et_builder_wp_editor_decorate_page_resource_slug( $post_id, $resource_slug ) {
		// Bail early if current page is not singular.
		if ( ! is_numeric( $post_id ) || ! is_singular() ) {
			return $resource_slug;
		}

		$templates = et_builder_get_wp_editor_templates();

		// Bail early if current page doesn't have templates.
		if ( empty( $templates ) ) {
			return $resource_slug;
		}

		foreach ( $templates as $template ) {
			// The `wpe` is stand for WP Editor.
			$template_id    = isset( $template->wp_id ) ? (int) $template->wp_id : 0;
			$resource_slug .= $template_id ? '-wpe-' . $template_id : '';
		}

		return $resource_slug;
	}
}

if ( ! function_exists( 'et_builder_get_wp_editor_templates' ) ) {
	/**
	 * Get WP Editor templates on current post.
	 *
	 * @since 4.14.8
	 *
	 * @return array List of templates and template parts.
	 */
	function et_builder_get_wp_editor_templates() {
		static $templates = null;

		// Bail early if the list is already processed.
		if ( null !== $templates ) {
			return $templates;
		}

		$templates = array();

		// Bail early if `get_block_template` function doesn't exist because we need it to
		// get template data. This function is introduced on WP 5.8 along with Template and
		// Template Parts editors.
		if ( ! function_exists( 'get_block_template' ) ) {
			return $templates;
		}

		// Bail early if current page is not singular.
		if ( ! is_singular() ) {
			return $templates;
		}

		global $post;

		// Bail early if current post doesn't have page template.
		if ( empty( $post->page_template ) ) {
			return $templates;
		}

		// A. Template.
		// Get block template data based on post slug and post type.
		$template = get_block_template( get_stylesheet() . '//' . $post->page_template, ET_WP_EDITOR_TEMPLATE_POST_TYPE );

		// Bail early if the template is empty.
		if ( empty( $template ) ) {
			return $templates;
		}

		$template_id               = isset( $template->wp_id ) ? (int) $template->wp_id : 0;
		$templates[ $template_id ] = $template;

		// Parse and fetch blocks list in the template to find the template parts.
		$blocks = parse_blocks( $template->content );

		// Bail early if the blocks is empty.
		if ( empty( $blocks ) ) {
			return $templates;
		}

		foreach ( $blocks as $block ) {
			$name = et_()->array_get( $block, 'blockName' );
			$slug = et_()->array_get( $block, array( 'attrs', 'slug' ) );

			// Skip if current block is not template part.
			if ( 'core/template-part' !== $name || empty( $slug ) ) {
				continue;
			}

			// B. Template Parts.
			// Get block template part data based on post slug and post type.
			$template_part = get_block_template( get_stylesheet() . '//' . $slug, ET_WP_EDITOR_TEMPLATE_PART_POST_TYPE );

			// Skip if the template part is empty.
			if ( empty( $template_part ) ) {
				continue;
			}

			$template_part_id               = isset( $template_part->wp_id ) ? (int) $template_part->wp_id : 0;
			$templates[ $template_part_id ] = $template_part;
		}

		return $templates;
	}
}

if ( ! function_exists( 'et_builder_is_block_theme' ) ) {
	/**
	 * Whether current theme is block theme or not.
	 *
	 * @since 4.17.4
	 *
	 * @return boolean Block theme status.
	 */
	function et_builder_is_block_theme() {
		// Use `wp_is_block_theme` on WP 5.9.
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return (bool) wp_is_block_theme();
		}

		// Use `gutenberg_is_fse_theme` on GB plugin.
		if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
			return (bool) gutenberg_is_fse_theme();
		}

		// Use manual check on WP 5.8 below.
		$block_templates_index_html_file = get_stylesheet_directory() . '/block-templates/index.html';
		$templates_index_html_file       = get_stylesheet_directory() . '/templates/index.html';

		return is_readable( $block_templates_index_html_file ) || is_readable( $templates_index_html_file );
	}
}

<?php
/**
 * Utility functions for checking conditions.
 *
 * To be included in this file a function must:
 *
 *   * Return a bool value
 *   * Have a name that asks a yes or no question (where the first word after
 *     the et_ prefix is a word like: is, can, has, should, was, had, must, or will)
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.0.7
 */

// phpcs:disable Squiz.PHP.CommentedOutCode -- We may add `et_builder_()` in future.

/*
Function Template

if ( ! function_exists( '' ) ):
function et_builder_() {

}
endif;

*/
// phpcs:enable

// Note: Functions in this file are sorted alphabetically.

if ( ! function_exists( 'et_builder_is_frontend' ) ) :
	/**
	 * Determine whether current request is frontend.
	 * This excludes the visual builder.
	 *
	 * @return bool
	 */
	function et_builder_is_frontend() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Only used to disable some FE optmizations.
		$is_builder              = isset( $_GET['et_fb'] ) || isset( $_GET['et_bfb'] );
		$is_block_layout_preview = isset( $_GET['et_block_layout_preview'] );
		// phpcs:enable

		return $is_builder || is_admin() || wp_doing_ajax() || wp_doing_cron() || $is_block_layout_preview ? false : true;
	}
endif;

if ( ! function_exists( 'et_builder_is_frontend_or_builder' ) ) :
	/**
	 * Determine whether current request is frontend.
	 * This includes the visual builder.
	 *
	 * @since 4.10.0
	 *
	 * @return bool
	 */
	function et_builder_is_frontend_or_builder() {
		static $et_builder_is_frontend_or_builder = null;

		if ( null === $et_builder_is_frontend_or_builder ) {
			if (
				! is_admin()
				&& ! wp_doing_ajax()
				&& ! wp_doing_cron()
			) {
				$et_builder_is_frontend_or_builder = true;
			}
		}

		return $et_builder_is_frontend_or_builder;
	}
endif;

if ( ! function_exists( 'et_builder_is_loading_data' ) ) :
	/**
	 * Determine whether builder is loading full data or not.
	 *
	 * @param string $type Is it a bb or vb.
	 *
	 * @return bool
	 */
	function et_builder_is_loading_data( $type = 'vb' ) {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any stats, hence CSRF ok.
		if ( 'bb' === $type ) {
			return 'et_pb_get_backbone_templates' === et_()->array_get( $_POST, 'action' );
		}

		$data_actions = array(
			'et_fb_retrieve_builder_data',
			'et_fb_update_builder_assets',
			'et_pb_process_computed_property',
		);

		return isset( $_POST['action'] ) && in_array( $_POST['action'], $data_actions, true );
		// phpcs:enable
	}
endif;

if ( ! function_exists( 'et_builder_should_load_all_data' ) ) :
	/**
	 * Determine whether to load full builder data.
	 *
	 * @return bool
	 */
	function et_builder_should_load_all_data() {
		$needs_cached_definitions = et_core_is_fb_enabled() && ! et_fb_dynamic_asset_exists( 'definitions' );

		return $needs_cached_definitions || ( ET_Builder_Element::is_saving_cache() || et_builder_is_loading_data() );
	}
endif;

if ( ! function_exists( 'et_builder_should_load_all_module_data' ) ) :
	/**
	 * Determine whether to load all module data.
	 *
	 * @return bool
	 */
	function et_builder_should_load_all_module_data() {

		if ( ! et_builder_is_frontend() ) {
			// Always load everything when not a frontend request.
			return true;
		}

		$needs_cached_definitions = et_core_is_fb_enabled();

		$et_dynamic_module_framework = et_builder_dynamic_module_framework();

		$result = $needs_cached_definitions || ( ET_Builder_Element::is_saving_cache() || et_builder_is_loading_data() ) || 'on' !== $et_dynamic_module_framework;

		/**
		 * Whether to load all module data,
		 * including all module classes, on a given page load.
		 *
		 * @since 4.10.0
		 *
		 * @param bool $result Whether to load all module data.
		 */
		return apply_filters( 'et_builder_should_load_all_module_data', $result );
	}
endif;


if ( ! function_exists( 'et_builder_dynamic_module_framework' ) ) :
	/**
	 * Determine whether module framework is on.
	 *
	 * @return string
	 */
	function et_builder_dynamic_module_framework() {
		global $shortname;

		if ( et_is_builder_plugin_active() ) {
			$options                     = get_option( 'et_pb_builder_options', array() );
			$et_dynamic_module_framework = isset( $options['performance_main_dynamic_module_framework'] ) ? $options['performance_main_dynamic_module_framework'] : 'on';
		} else {
			$et_dynamic_module_framework = et_get_option( $shortname . '_dynamic_module_framework', 'on' );
		}
		return $et_dynamic_module_framework;
	}
endif;

if ( ! function_exists( 'et_builder_is_mod_pagespeed_enabled' ) ) :
	/**
	 * Determine whether Mod PageSpeed is enabled.
	 *
	 * @return bool
	 */
	function et_builder_is_mod_pagespeed_enabled() {
		static $enabled;

		if ( isset( $enabled ) ) {
			// Use the cached value.
			return $enabled;
		}

		$key     = 'et_check_mod_pagespeed';
		$version = get_transient( $key );

		if ( false === $version ) {
			// Mod PageSpeed is an output filter, hence it can't be detected from within the request.
			// To figure out whether it is active or not:
			// 1. Use `wp_remote_get` to make another request.
			// 2. Retrieve PageSpeed version from response headers (if set).
			// 3. Save the value in a transient for 24h.
			// The `et_check_mod_pagespeed` url parameter is also added to the request so
			// we can exit early (content is irrelevant, only headers matter).
			$args = [
				$key => 'on',
			];

			// phpcs:disable WordPress.Security.NonceVerification -- Only checking arg is set.
			if ( isset( $_REQUEST['PageSpeed'] ) ) {
				// This isn't really needed but it's harmless and makes testing a lot easier.
				$args['PageSpeed'] = sanitize_text_field( $_REQUEST['PageSpeed'] );
			}
			// phpcs:enable

			$request = wp_remote_get( add_query_arg( $args, get_home_url() ) );
			// Apache header.
			$version = wp_remote_retrieve_header( $request, 'x-mod-pagespeed' );
			if ( empty( $version ) ) {
				// Nginx header.
				$version = wp_remote_retrieve_header( $request, 'x-page-speed' );
			}

			set_transient( $key, $version, DAY_IN_SECONDS );
		}

		// Cache the value.
		$enabled = ! empty( $version );
		return $enabled;
	}
endif;

<?php
/**
 * Classic Editor Enabler.
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ET_Builder_Classic_Editor' ) ) :
	/**
	 * Load classic editor and disable Gutenberg/Block Editor
	 *
	 * Adapted from Classic Editor plugin by WordPress Contributors.
	 *
	 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
	 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
	 * that you can use any other version of the GPL.
	 *
	 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
	 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	 *
	 * Classic Editor
	 *
	 * Copyright 2018 by WordPress Contributors
	 *
	 * Classic Editor  is released under the GPL-2.0+
	 */
	class ET_Builder_Classic_Editor {
		/**
		 * Instance of `ET_Builder_Classic_Editor`.
		 *
		 * @var ET_Builder_Classic_Editor
		 */
		private static $_instance;

		/**
		 * ET_Builder_Classic_Editor constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_actions' ), 20 );
		}

		/**
		 * Get the class instance.
		 *
		 * @since 3.18
		 *
		 * @return ET_Builder_Classic_Editor
		 */
		public static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Add & remove necessary actions and filters needed to load Classic Editor back
		 * These filters are based on Classic Editor plugin to ensure required filters & actions needed
		 * to load Classic Editor on Gutenberg / Block Editor (WordPress 5.0). All conditiononal Block Editor
		 * loader based on query string has been removed.
		 *
		 * @since 3.18
		 */
		public function register_actions() {
			$gutenberg    = has_filter( 'replace_editor', 'gutenberg_init' );
			$block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

			if ( ! $gutenberg && ! $block_editor ) {
				return;
			}

			// Load classic editor.
			// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
			$enable_classic_editor = apply_filters( 'et_builder_enable_classic_editor', isset( $_GET['et_classic_editor'] ) );

			if ( $block_editor && $enable_classic_editor ) {
				add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
			}

			if ( $gutenberg && $enable_classic_editor ) {
				// gutenberg.php.
				remove_action( 'admin_menu', 'gutenberg_menu' );
				remove_action( 'admin_notices', 'gutenberg_build_files_notice' );
				remove_action( 'admin_notices', 'gutenberg_wordpress_version_notice' );
				remove_action( 'admin_init', 'gutenberg_redirect_demo' );

				remove_filter( 'replace_editor', 'gutenberg_init' );

				// lib/client-assets.php.
				remove_action( 'wp_enqueue_scripts', 'gutenberg_register_scripts_and_styles', 5 );
				remove_action( 'admin_enqueue_scripts', 'gutenberg_register_scripts_and_styles', 5 );
				remove_action( 'wp_enqueue_scripts', 'gutenberg_common_scripts_and_styles' );
				remove_action( 'admin_enqueue_scripts', 'gutenberg_common_scripts_and_styles' );

				// lib/compat.php.
				remove_filter( 'wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers' );

				// lib/rest-api.php.
				remove_action( 'rest_api_init', 'gutenberg_register_rest_routes' );
				remove_action( 'rest_api_init', 'gutenberg_add_taxonomy_visibility_field' );

				remove_filter( 'rest_request_after_callbacks', 'gutenberg_filter_oembed_result' );
				remove_filter( 'registered_post_type', 'gutenberg_register_post_prepare_functions' );
				remove_filter( 'register_post_type_args', 'gutenberg_filter_post_type_labels' );

				// lib/meta-box-partial-page.php.
				remove_action( 'do_meta_boxes', 'gutenberg_meta_box_save', 1000 );
				remove_action( 'submitpost_box', 'gutenberg_intercept_meta_box_render' );
				remove_action( 'submitpage_box', 'gutenberg_intercept_meta_box_render' );
				remove_action( 'edit_page_form', 'gutenberg_intercept_meta_box_render' );
				remove_action( 'edit_form_advanced', 'gutenberg_intercept_meta_box_render' );

				remove_filter( 'redirect_post_location', 'gutenberg_meta_box_save_redirect' );
				remove_filter( 'filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes' );
			}

			if ( $gutenberg && $enable_classic_editor ) {
				// gutenberg.php.
				remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );
				remove_action( 'admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button' );

				remove_filter( 'body_class', 'gutenberg_add_responsive_body_class' );
				remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );

				// lib/compat.php.
				remove_action( 'admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks' );

				// lib/register.php.
				remove_action( 'edit_form_top', 'gutenberg_remember_classic_editor_when_saving_posts' );

				remove_filter( 'redirect_post_location', 'gutenberg_redirect_to_classic_editor_when_saving_posts' );
				remove_filter( 'get_edit_post_link', 'gutenberg_revisions_link_to_editor' );
				remove_filter( 'wp_prepare_revision_for_js', 'gutenberg_revisions_restore' );
				remove_filter( 'display_post_states', 'gutenberg_add_gutenberg_post_state' );

				// lib/plugin-compat.php.
				remove_filter( 'rest_pre_insert_post', 'gutenberg_remove_wpcom_markdown_support' );
			}
		}
	}

endif;

ET_Builder_Classic_Editor::instance();

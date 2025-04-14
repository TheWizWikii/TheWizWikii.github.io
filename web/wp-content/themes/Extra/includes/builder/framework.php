<?php

if ( ! defined( 'ET_BUILDER_CSS_WRAPPER_PREFIX' ) ) {
	define( 'ET_BUILDER_CSS_WRAPPER_PREFIX', '.et-db' );
}

if ( ! defined( 'ET_BUILDER_CSS_CONTAINER_PREFIX' ) ) {
	define( 'ET_BUILDER_CSS_CONTAINER_PREFIX', '#et-boc' );
}

if ( ! defined( 'ET_BUILDER_CSS_LAYOUT_PREFIX' ) ) {
	define( 'ET_BUILDER_CSS_LAYOUT_PREFIX', ET_BUILDER_CSS_CONTAINER_PREFIX . ' .et-l' );
}

if ( ! defined( 'ET_BUILDER_CSS_PREFIX' ) ) {
	define( 'ET_BUILDER_CSS_PREFIX', ET_BUILDER_CSS_WRAPPER_PREFIX . ' ' . ET_BUILDER_CSS_LAYOUT_PREFIX );
}

if ( ! defined( 'ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA' ) ) {
	define( 'ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==' );
}

if ( ! defined( 'ET_BUILDER_PLACEHOLDER_PORTRAIT_IMAGE_DATA' ) ) {
	define( 'ET_BUILDER_PLACEHOLDER_PORTRAIT_IMAGE_DATA', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDUwMCA1MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxwYXRoIGZpbGw9IiNFQkVCRUIiIGQ9Ik0wIDBoNTAwdjUwMEgweiIvPgogICAgICAgIDxyZWN0IGZpbGwtb3BhY2l0eT0iLjEiIGZpbGw9IiMwMDAiIHg9IjY4IiB5PSIzMDUiIHdpZHRoPSIzNjQiIGhlaWdodD0iNTY4IiByeD0iMTgyIi8+CiAgICAgICAgPGNpcmNsZSBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBjeD0iMjQ5IiBjeT0iMTcyIiByPSIxMDAiLz4KICAgIDwvZz4KPC9zdmc+Cg==' );
}

if ( ! defined( 'ET_BUILDER_PLACEHOLDER_PORTRAIT_VARIATION_IMAGE_DATA' ) ) {
	define( 'ET_BUILDER_PLACEHOLDER_PORTRAIT_VARIATION_IMAGE_DATA', 'data:image/svg+xml;base64,PHN2ZyBpZD0ibXlTdmdFbGVtZW50IiB3aWR0aD0iNTQwIiBoZWlnaHQ9IjU0MCIgdmlld0JveD0iMCAwIDU0MCA1NDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+Cgk8Zz4KCQk8cGF0aCBkPSJNMCwwaDU0MHY1NDBIMFYweiIgZmlsbD0iI0VCRUJFQiIvPgoJCTxwYXRoIGQ9Ik00NDUuNiw1NDBoLTk5bC0yMDItMjAyTDAsNDgyLjZ2LTk5bDExNi40LTExNi40YzE1LjYtMTUuNiw0MC45LTE1LjYsNTYuNiwwTDQ0NS42LDU0MEw0NDUuNiw1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KCQk8Y2lyY2xlIGN4PSIzMzEiIGN5PSIxNDgiIHI9IjcwIiBmaWxsLW9wYWNpdHk9Ii4wNSIgZmlsbD0iIzAwMCIvPgoJCTxwb2x5Z29uIHBvaW50cz0iNTQwLDIxNS4yIDIxNS4yLDU0MCAzMjguMyw1NDAgNTQwLDMyOC4zIiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KCTwvZz4KPC9zdmc+' );
}

// phpcs:ignore WordPress.Security.NonceVerification -- Only checking arg is set.
if ( isset( $_REQUEST['et_check_mod_pagespeed'] ) ) {
	// This is an internal request used to check response headers, hence we exit early.
	// Must still output some html or else Mod Pagepeed won't add any header.
	echo '<html><head/></html>';
	die();
}

// Detect Codeception and load additional code required by tests.
if ( class_exists( 'Codeception\TestCase\WPTestCase' ) ) {
	foreach ( glob( ET_BUILDER_DIR . 'tests/codeception/wpunit/*.php' ) as $test_file ) {
		require_once $test_file;
	}
}

require_once ET_BUILDER_DIR . 'autoload.php';

/**
 * Retrieve a commonly used translation.
 *
 * @since 4.4.9
 *
 * @param string  $key Translation key.
 * @param boolean $reset Reset cache.
 *
 * @return string
 */
function et_builder_i18n( $key, $reset = false ) {
	static $cache;

	if ( true === $reset ) {
		$cache = array();
	}

	if ( ! isset( $cache[ $key ] ) ) {
		$cache[ $key ] = ET_Builder_I18n::get( $key );
	}

	return $cache[ $key ];

}

/**
 * Resets commonly used translations cache.
 *
 * @since 4.4.9
 *
 * @return void
 */
function et_builder_i18n_reset_cache() {
	et_builder_i18n( '', true );
}

/**
 * Escape translation with optional value and caches the result.
 *
 * @since 4.4.9
 *
 * @param string $text
 * @param string $value
 *
 * @return string.
 */
function et_esc_html_once( $text, $value = '' ) {
	static $cache = array();

	if ( isset( $cache[ $text ][ $value ] ) ) {
		return $cache[ $text ][ $value ];
	}

	$escaped = esc_html( '' === $value ? $text : sprintf( $text, $value ) );

	$cache[ $text ][ $value ] = $escaped;
	return $escaped;
}

/**
 * Checks to see if Critical CSS is enabled.
 *
 * @since 4.10.0
 *
 * @return string.
 */
function et_builder_is_critical_enabled() {
	global $shortname;

	$value = false;

	if ( et_is_builder_plugin_active() ) {
		$options = get_option( 'et_pb_builder_options', array() );
		$value   = isset( $options['performance_main_critical_css'] ) ? $options['performance_main_critical_css'] : 'on';
	} else {
		$value = et_get_option( $shortname . '_critical_css', 'on' );
	}

	return apply_filters( 'et_pb_critical_css_enabled', 'on' === $value );
}

require_once ET_BUILDER_DIR . 'compat/early.php';
require_once ET_BUILDER_DIR . 'compat/scripts.php';
require_once ET_BUILDER_DIR . 'feature/gutenberg/blocks/Layout.php';
require_once ET_BUILDER_DIR . 'feature/gutenberg/blocks/PostExcerpt.php';
require_once ET_BUILDER_DIR . 'feature/gutenberg/utils/Conversion.php';
require_once ET_BUILDER_DIR . 'feature/gutenberg/utils/Editor.php';
require_once ET_BUILDER_DIR . 'feature/gutenberg/EditorTypography.php';
require_once ET_BUILDER_DIR . 'core.php';
require_once ET_BUILDER_DIR . 'conditions.php';
require_once ET_BUILDER_DIR . 'post/PostStack.php';

if ( ! (
	is_admin() ||
	wp_doing_ajax() ||
	is_customize_preview() ||
	is_et_pb_preview()
) ) {
	require_once ET_BUILDER_DIR . 'feature/DoNotCachePage.php';
}

require_once ET_BUILDER_DIR . 'feature/ClassicEditor.php';
require_once ET_BUILDER_DIR . 'feature/AjaxCache.php';
require_once ET_BUILDER_DIR . 'feature/post-content.php';
require_once ET_BUILDER_DIR . 'feature/content-retriever/ContentRetriever.php';

if ( et_builder_is_critical_enabled() ) {
	require_once ET_BUILDER_DIR . 'feature/CriticalCSS.php';
}

require_once ET_BUILDER_DIR . 'feature/dynamic-assets/dynamic-assets.php';
require_once ET_BUILDER_DIR . 'feature/dynamic-assets/class-dynamic-assets.php';
require_once ET_BUILDER_DIR . 'feature/dynamic-content.php';
require_once ET_BUILDER_DIR . 'feature/search-posts.php';
require_once ET_BUILDER_DIR . 'feature/ErrorReport.php';
require_once ET_BUILDER_DIR . 'api/DiviExtensions.php';
require_once ET_BUILDER_DIR . 'api/rest/BlockLayout.php';
require_once ET_BUILDER_DIR . 'frontend-builder/theme-builder/theme-builder.php';
require_once ET_BUILDER_DIR . 'feature/global-presets/Settings.php';
require_once ET_BUILDER_DIR . 'feature/global-presets/History.php';
require_once ET_BUILDER_DIR . 'feature/window.php';
require_once ET_BUILDER_DIR . 'feature/et-server-frame.php';
require_once ET_BUILDER_DIR . 'feature/ajax-data/AjaxData.php';
require_once ET_BUILDER_DIR . 'feature/display-conditions/DisplayConditions.php';
require_once ET_BUILDER_DIR . 'feature/icon-manager/ExtendedFontIcons.php';
require_once ET_BUILDER_DIR . 'feature/background-masks/Functions.php';
require_once ET_BUILDER_DIR . 'feature/background-masks/PatternFields.php';
require_once ET_BUILDER_DIR . 'feature/background-masks/MaskFields.php';
require_once ET_BUILDER_DIR . 'feature/gutenberg/BlockTemplates.php';
require_once ET_BUILDER_DIR . 'feature/local-library.php';
require_once ET_BUILDER_DIR . 'feature/ai-button.php';

// Conditional Includes.
if ( et_is_woocommerce_plugin_active() ) {
	require_once ET_BUILDER_DIR . 'feature/woocommerce-modules.php';
}

global $shortname;

if (
	apply_filters( 'et_builder_enable_jquery_body', true ) &&
	! (
		is_admin() ||
		wp_doing_ajax() ||
		et_is_builder_plugin_active() ||
		is_customize_preview() ||
		is_et_pb_preview()
	) &&
	'on' === et_get_option( $shortname . '_enable_jquery_body', 'on' )
) {
	require_once ET_BUILDER_DIR . 'feature/JQueryBody.php';
}

if ( wp_doing_ajax() && ! is_customize_preview() ) {
	define( 'WPE_HEARTBEAT_INTERVAL', et_builder_heartbeat_interval() );

	// Default ajax request exceptions
	$builder_load_requests = array(
		'action' => array(
			'et_pb_get_backbone_template',
			'et_pb_get_backbone_templates',
			'et_pb_process_computed_property',
			'et_fb_ajax_render_shortcode',
			'et_fb_ajax_save',
			'et_fb_get_shortcode_from_fb_object',
			'et_fb_get_html_from_shortcode',
			'et_fb_ajax_drop_autosave',
			'et_fb_get_saved_layouts',
			'et_fb_save_layout',
			'et_fb_get_cloud_item_content',
			'et_fb_update_layout',
			'et_pb_execute_content_shortcodes',
			'et_pb_ab_builder_data',
			'et_pb_create_ab_tables',
			'et_pb_update_stats_table',
			'et_pb_ab_clear_cache',
			'et_pb_ab_clear_stats',
			'et_fb_prepare_shortcode',
			'et_fb_process_imported_content',
			'et_fb_get_saved_templates',
			'et_fb_retrieve_builder_data',
			'et_fb_update_builder_assets',
			'et_pb_process_custom_font',
			'et_builder_email_add_account',     // email opt-in module.
			'et_builder_email_remove_account',  // email opt-in module.
			'et_builder_email_get_lists',       // email opt-in module.
			'et_builder_save_settings',         // builder plugin dashboard (global builder settings).
			'save_epanel',                      // ePanel (global builder settings).
			'save_epanel_temp',                 // ePanel (temp global builder settings).
			'et_builder_library_get_layout',
			'et_builder_library_update_terms',
			'et_builder_toggle_cloud_status',
			'et_builder_library_save_temp_layout',
			'et_builder_library_remove_temp_layout',
			'et_builder_library_clear_temp_presets',
			'et_builder_library_update_item',
			'et_builder_library_convert_item',
			'et_theme_builder_library_update_item',
			'et_theme_builder_library_save_temp_layout',
			'et_theme_builder_library_remove_temp_layout',
			'et_theme_builder_library_get_preset_items',
			'et_theme_builder_library_get_set_items',
			'et_builder_library_upload_thumbnail',
			'et_builder_library_get_cloud_token',
			'et_builder_library_get_layouts_data',
			'et_theme_builder_library_get_items_data',
			'et_theme_builder_library_update_terms',
			'et_theme_builder_library_get_item',
			'et_fb_fetch_attachments',
			'et_pb_get_saved_templates',
			'et_builder_resolve_post_content',
			'et_builder_activate_bfb_auto_draft',
			'et_builder_toggle_bfb',
			'et_fb_error_report',
			'et_core_portability_import',
			'et_core_version_rollback',
			'update-theme',
			'et_safe_mode_update',
			'et_core_portability_export',
			'et_core_portability_import_default_presets',
			'et_builder_migrate_module_customizer_phase_two',
			'et_builder_save_global_presets_history',
			'et_builder_retrieve_global_presets_history',
			'et_theme_builder_api_export_theme_builder_step',
			'et_theme_builder_api_import_theme_builder_step',
			'et_pb_submit_subscribe_form',
			'et_builder_get_woocommerce_tabs',
			'et_builder_global_colors_save',
			'et_builder_default_colors_update',
			'et_builder_ajax_save_domain_token',
			'et_fb_fetch_before_after_components',
			'et_code_snippets_library_get_items',
			'et_builder_global_colors_get',
			'et_update_customizer_fonts',
		),
	);

	// AJAX requests that use PHP modules cache for performance reasons.
	$builder_use_cache_actions = array(
		'heartbeat',
		'et_builder_retrieve_global_presets_history',
		'et_fb_get_saved_templates',
		'et_fb_ajax_save',
		'et_fb_ajax_drop_autosave',
	);

	// Added built-in third party plugins support
	// Easy Digital Downloads
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		$builder_load_requests['action'][] = 'edd_load_gateway';
	}

	// WooCommerce - it uses its own ajax endpoint instead of admin-ajax.php
	if ( class_exists( 'WooCommerce' ) ) {
		$builder_load_requests['wc-ajax'] = array(
			'update_order_review',
		);
	}

	// WPML.
	if ( class_exists( 'SitePress' ) ) {
		$builder_load_requests['action'][] = 'et_builder_wpml_translate_layout';
	}

	// Merging third party exceptions; built-in exceptions should not be removable
	$builder_custom_load_requests = apply_filters( 'et_builder_load_requests', array() );

	if ( ! empty( $builder_custom_load_requests ) ) {
		foreach ( $builder_custom_load_requests as $builder_custom_query_string => $builder_custom_possible_values ) {
			if ( ! isset( $builder_load_requests[ $builder_custom_query_string ] ) ) {
				$builder_load_requests[ $builder_custom_query_string ] = $builder_custom_possible_values;
			} else {
				$builder_load_requests[ $builder_custom_query_string ] = array_merge( $builder_custom_possible_values, $builder_load_requests[ $builder_custom_query_string ] );
			}
		}
	}

	// Legacy compatibility for action only request exception filter
	$builder_load_actions = apply_filters( 'et_builder_load_actions', array() );

	if ( ! empty( $builder_load_actions ) ) {
		$builder_load_requests['action'] = array_merge( $builder_load_actions, $builder_load_requests['action'] );
	}

	// Determine whether current AJAX request should load builder or not
	$load_builder_on_ajax = false;

	// If current request's query string exists on list of possible values, load builder
	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	foreach ( $builder_load_requests as $query_string => $possible_values ) {
		if ( ! is_array( $possible_values ) ) {
			continue;
		}

		if ( isset( $_REQUEST[ $query_string ] ) && in_array( $_REQUEST[ $query_string ], $possible_values ) ) {
			$load_builder_on_ajax = true;

			break;
		}
	}

	define( 'ET_BUILDER_LOAD_ON_AJAX', $load_builder_on_ajax );

	$action             = et_()->array_get( $_POST, 'action', false );
	$force_builder_load = isset( $_POST['et_load_builder_modules'] ) && '1' === $_POST['et_load_builder_modules'];
	$force_memory_limit = 'et_fb_retrieve_builder_data' === $action;

	if ( 'heartbeat' === $action ) {
		// if this is the heartbeat, and if its not packing our heartbeat data, then return
		if ( ! isset( $_REQUEST['data'] ) || ! isset( $_REQUEST['data']['et'] ) ) {
			return;
		}
	} elseif ( ! $force_builder_load && ! $load_builder_on_ajax ) {
		return;
	}

	if ( $force_memory_limit || et_should_memory_limit_increase() ) {
		et_increase_memory_limit();
	}

	if ( $action && in_array( $action, $builder_use_cache_actions ) ) {
		add_filter( 'et_builder_ajax_use_cache', '__return_true' );
	}
	// phpcs:enable
}

/**
 * Get product script handle for the main script bundle.
 *
 * @since 4.10.0
 */
function et_get_combined_script_handle() {
	global $shortname;

	if ( 'divi' === $shortname ) {
		$combined_script_handle = 'divi-custom-script';
	} elseif ( 'extra' === $shortname ) {
		$combined_script_handle = 'extra-scripts';
	} else {
		$combined_script_handle = 'divi-builder-custom-script';
	}

	return $combined_script_handle;
}
/**
 * Localizes the main front end bundle and adds variables
 * for et-builder-modules-global-functions-script.
 *
 * @since 4.10.0
 */
function et_builder_load_global_functions_script() {
	wp_localize_script(
		et_get_combined_script_handle(),
		'et_builder_utils_params',
		array(
			'condition'              => array(
				'diviTheme'  => function_exists( 'et_divi_fonts_url' ),
				'extraTheme' => function_exists( 'et_extra_fonts_url' ),
			),
			'scrollLocations'        => et_builder_get_window_scroll_locations(),
			'builderScrollLocations' => et_builder_get_onload_scroll_locations(),
			'onloadScrollLocation'   => et_builder_get_onload_scroll_location(),
			'builderType'            => et_builder_get_current_builder_type(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'et_builder_load_global_functions_script', 11 );

function et_builder_load_modules_styles() {
	$current_page_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );
	$is_fb_enabled   = et_core_is_fb_enabled();
	$ab_tests        = function_exists( 'et_builder_ab_get_current_tests' ) ? et_builder_ab_get_current_tests() : array();
	$is_ab_testing   = ! empty( $ab_tests );

	$google_maps_api_url_args = array(
		'v'   => 3,
		'key' => et_pb_get_google_api_key(),
	);

	if ( $is_fb_enabled && ! et_builder_tb_enabled() && ! et_builder_bfb_enabled() ) {
		$google_maps_api_url_args['callback'] = 'ETBuilderInitGoogleMaps';
	}

	$google_maps_api_url = add_query_arg( $google_maps_api_url_args, is_ssl() ? 'https://maps.googleapis.com/maps/api/js' : 'http://maps.googleapis.com/maps/api/js' );

	wp_register_script( 'salvattore', ET_BUILDER_URI . '/feature/dynamic-assets/assets/js/salvattore.js', array(), ET_BUILDER_VERSION, true );
	wp_register_script( 'google-maps-api', esc_url_raw( $google_maps_api_url ), array(), ET_BUILDER_VERSION, true );

	$frontend_scripts_data = array(
		'builderCssContainerPrefix' => ET_BUILDER_CSS_CONTAINER_PREFIX,
		'builderCssLayoutPrefix'    => ET_BUILDER_CSS_LAYOUT_PREFIX,
	);

	wp_localize_script( et_get_combined_script_handle(), 'et_frontend_scripts', $frontend_scripts_data );

	// Load modules wrapper on CPT.
	// Use get_the_ID() explicitly so we decide based on the first post of an archive page.
	if ( et_builder_post_is_of_custom_post_type( get_the_ID() ) ) {
		wp_enqueue_script( 'et-builder-cpt-modules-wrapper', ET_BUILDER_URI . '/scripts/cpt-modules-wrapper.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

		$modules_wrapper_data = array(
			'builderCssContainerPrefix' => ET_BUILDER_CSS_CONTAINER_PREFIX,
			'builderCssLayoutPrefix'    => ET_BUILDER_CSS_LAYOUT_PREFIX,
		);
		wp_localize_script( 'et-builder-cpt-modules-wrapper', 'et_modules_wrapper', $modules_wrapper_data );
	}

	if ( et_builder_has_limitation( 'register_fittext_script' ) ) {
		wp_register_script( 'fittext', ET_BUILDER_URI . '/scripts/ext/jquery.fittext.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	}

	// Load visible.min.js only if AB testing active on current page OR VB (because post settings is synced between VB and BB)
	if ( $is_ab_testing || $is_fb_enabled ) {
		wp_enqueue_script( 'et-jquery-visible-viewport', ET_BUILDER_URI . '/scripts/ext/jquery.visible.min.js', array( 'jquery', et_get_combined_script_handle() ), ET_BUILDER_VERSION, true );
	}

	$pb_custom_data = array(
		'ajaxurl'                => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
		'images_uri'             => get_template_directory_uri() . '/images',
		'builder_images_uri'     => ET_BUILDER_URI . '/images',
		'et_frontend_nonce'      => wp_create_nonce( 'et_frontend_nonce' ),
		'subscription_failed'    => esc_html__( 'Please, check the fields below to make sure you entered the correct information.', 'et_builder' ),
		'et_ab_log_nonce'        => wp_create_nonce( 'et_ab_testing_log_nonce' ),
		'fill_message'           => esc_html__( 'Please, fill in the following fields:', 'et_builder' ),
		'contact_error_message'  => esc_html__( 'Please, fix the following errors:', 'et_builder' ),
		'invalid'                => esc_html__( 'Invalid email', 'et_builder' ),
		'captcha'                => esc_html__( 'Captcha', 'et_builder' ),
		'prev'                   => esc_html__( 'Prev', 'et_builder' ),
		'previous'               => esc_html__( 'Previous', 'et_builder' ),
		'next'                   => esc_html__( 'Next', 'et_builder' ),
		'wrong_captcha'          => esc_html__( 'You entered the wrong number in captcha.', 'et_builder' ),
		'wrong_checkbox'         => esc_html__( 'Checkbox', 'et_builder' ),
		'ignore_waypoints'       => et_is_ignore_waypoints() ? 'yes' : 'no',
		'is_divi_theme_used'     => function_exists( 'et_divi_fonts_url' ),
		'widget_search_selector' => apply_filters( 'et_pb_widget_search_selector', '.widget_search' ),
		'ab_tests'               => $ab_tests,
		'is_ab_testing_active'   => $is_ab_testing,
		'page_id'                => $current_page_id,
		'unique_test_id'         => get_post_meta( $current_page_id, '_et_pb_ab_testing_id', true ),
		'ab_bounce_rate'         => '' !== get_post_meta( $current_page_id, '_et_pb_ab_bounce_rate_limit', true ) ? get_post_meta( $current_page_id, '_et_pb_ab_bounce_rate_limit', true ) : 5,
		'is_cache_plugin_active' => false === et_pb_detect_cache_plugins() ? 'no' : 'yes',
		'is_shortcode_tracking'  => get_post_meta( $current_page_id, '_et_pb_enable_shortcode_tracking', true ),
		'tinymce_uri'            => defined( 'ET_FB_ASSETS_URI' ) ? ET_FB_ASSETS_URI . '/vendors' : '',
		'accent_color'           => et_builder_accent_color(),
		/**
		 * Filters Waypoints options for client side rendering.
		 *
		 * @since 4.15.0
		 *
		 * @param array $options {
		 *     Filtered Waypoints options. Only support `context` at this moment because
		 *     there is no test case for other properties.
		 *
		 *     @type string[] $context List of container selectors for the Waypoint. The
		 *                             element will iterate and looking for the closest
		 *                             parent element matches the given selectors.
		 */
		'waypoints_options'      => apply_filters( 'et_builder_waypoints_options', array() ),
	);

	wp_localize_script( et_get_combined_script_handle(), 'et_pb_custom', $pb_custom_data );

	/**
	 * Only load this during builder preview screen session
	 */
	if ( is_et_pb_preview() ) {
		// Set fixed protocol for preview URL to prevent cross origin issue
		$preview_scheme = is_ssl() ? 'https' : 'http';

		// Get home url, then parse it
		$preview_origin_component = parse_url( home_url( '', $preview_scheme ) );

		// Rebuild origin URL, strip sub-directory address if there's any (postMessage e.origin doesn't pass sub-directory address)
		$preview_origin = '';

		// Perform check, prevent unnecessary error
		if ( isset( $preview_origin_component['scheme'] ) && isset( $preview_origin_component['host'] ) ) {
			$preview_origin = "{$preview_origin_component['scheme']}://{$preview_origin_component['host']}";

			// Append port number if different port number is being used
			if ( isset( $preview_origin_component['port'] ) ) {
				$preview_origin = "{$preview_origin}:{$preview_origin_component['port']}";
			}
		}

		// Enqueue theme's style.css if it hasn't been enqueued (possibly being hardcoded by theme)
		if ( ! et_builder_has_theme_style_enqueued() && et_builder_has_limitation( 'force_enqueue_theme_style' ) ) {
			wp_enqueue_style( 'et-builder-theme-style-css', get_stylesheet_uri(), array() );
		}

		wp_enqueue_style( 'et-builder-preview-style', ET_BUILDER_URI . '/styles/preview.css', array(), ET_BUILDER_VERSION );

		wp_enqueue_script( 'et-builder-preview-script', ET_BUILDER_URI . '/frontend-builder/build/frontend-builder-preview.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

		$preview_params_data = array(
			'preview_origin'           => esc_url( $preview_origin ),
			'alert_origin_not_matched' => sprintf(
				esc_html__( 'Unauthorized access. Preview cannot be accessed outside %1$s.', 'et_builder' ),
				esc_url( home_url( '', $preview_scheme ) )
			),
		);

		wp_localize_script( 'et-builder-preview-script', 'et_preview_params', $preview_params_data );
	}
}
add_action( 'wp_enqueue_scripts', 'et_builder_load_modules_styles', 11 );

function et_builder_get_modules_js_data() {
	// Data shouldn't be loaded in Builder, so always pass an empty array there.
	$animation_data      = et_core_is_fb_enabled() ? array() : et_builder_handle_animation_data();
	$animation_data_json = json_encode( $animation_data );

	$link_options_data      = et_core_is_fb_enabled() ? array() : et_builder_handle_link_options_data();
	$link_options_data_json = json_encode( $link_options_data );

	if ( empty( $animation_data ) && empty( $link_options_data ) ) {
		return;
	}

	?>
	<script type="text/javascript">
		<?php if ( $animation_data ) : ?>
		var et_animation_data = <?php echo et_core_esc_previously( $animation_data_json ); ?>;
			<?php
		endif;

		if ( $link_options_data ) :
			?>
		var et_link_options_data = <?php echo et_core_esc_previously( $link_options_data_json ); ?>;
		<?php endif; ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'et_builder_get_modules_js_data' );

// Force Backbone templates cache to be cleared on language change to make sure the settings modal is translated
// defaults for arguments are provided because their number is different for both the actions
function et_pb_force_clear_template_cache( $meta_id = false, $object_id = false, $meta_key = false, $_meta_value = false ) {
	$current_action = current_action();

	if ( ( 'updated_user_meta' === $current_action && 'locale' === $meta_key ) || 'update_option_WPLANG' === $current_action ) {
		et_update_option( 'et_pb_clear_templates_cache', true );
	}
}
add_action( 'update_option_WPLANG', 'et_pb_force_clear_template_cache' );
add_action( 'updated_user_meta', 'et_pb_force_clear_template_cache', 10, 4 );

function et_builder_handle_animation_data( $element_data = false ) {
	static $data         = array();
	static $data_classes = array();

	if ( ! $element_data ) {
		return $data;
	}

	// This should not be possible but let's be safe
	if ( empty( $element_data['class'] ) ) {
		return;
	}

	// Prevent duplication animation data entries created by global modules
	if ( in_array( $element_data['class'], $data_classes ) ) {
		return;
	}

	$data[]         = et_core_esc_previously( $element_data );
	$data_classes[] = et_core_esc_previously( $element_data['class'] );
}

function et_builder_handle_link_options_data( $element_data = false ) {
	static $data         = array();
	static $data_classes = array();

	if ( ! $element_data ) {
		return $data;
	}

	// Safe checks bellow
	if ( empty( $element_data['class'] ) ) {
		return;
	}

	// Prevent duplication link options data entries created by global modules
	if ( in_array( $element_data['class'], $data_classes ) ) {
		return;
	}

	$data[]         = $element_data;
	$data_classes[] = $element_data['class'];
}

/**
 * Get list of combined scripts and their possible alternative names.
 *
 * @return array
 */
function et_builder_get_minified_scripts() {
	$static_scripts = array(
		'waypoints',
		'jquery-waypoints',
	);

	return apply_filters( 'et_builder_get_minified_scripts', $static_scripts );
}

/**
 * Get list of concatenated & minified styles (sans style.css)
 *
 * @return array
 */
function et_builder_get_minified_styles() {
	$minified_styles = array(
		'et-animations',
	);

	return apply_filters( 'et_builder_get_minified_styles', $minified_styles );
}

/**
 * Re-enqueue listed concatenated & minified scripts (and their possible alternative name) used empty string
 * to keep its dependency in order but avoiding WordPress to print the script to avoid the same file printed twice
 * Case in point: salvattore that is being called via builder module's render() method
 *
 * @return void
 */
function et_builder_dequeue_minified_scripts() {
	if ( ! is_admin() ) {
		/**
		 * Builder script handle name
		 *
		 * @since 3.??
		 *
		 * @param string
		 */
		$builder_script_handle = et_get_combined_script_handle();

		foreach ( et_builder_get_minified_scripts() as $script ) {
			// Get script's localized data before the script is dequeued.
			$script_data = wp_scripts()->get_data( $script, 'data' );

			// If to-be dequeued script has localized data, get builder script's data and concatenated both to ensure compatibility.
			// Concatenating is needed because script's localize data is saved as string (encoded array concatenated into variable name).
			if ( $script_data && '' !== trim( $script_data ) ) {

				// If builder script handle localized data returns false/empty, $script_data still need to be added.
				$concatenated_scripts_data = implode(
					' ',
					array_filter(
						array(
							wp_scripts()->get_data( $builder_script_handle, 'data' ),
							$script_data,
						)
					)
				);

				// Add concatenated localized data to builder script handle.
				wp_scripts()->add_data( $builder_script_handle, 'data', $concatenated_scripts_data );
			}

			// If dequeued script has inline script, get it then re-add it to builder script handle using appropriate position.
			$inline_script_positions = array( 'before', 'after' );
			foreach ( $inline_script_positions as $inline_script_position ) {
				$inline_script = wp_scripts()->get_data( $script, $inline_script_position );

				// Inline script is saved as array. add_inline_script() method will handle it appending process.
				if ( is_array( $inline_script ) && ! empty( $inline_script ) ) {
					wp_scripts()->add_inline_script( $builder_script_handle, implode( ' ', $inline_script ), $inline_script_position );
				}
			}

			wp_dequeue_script( $script );
			wp_deregister_script( $script );
			wp_register_script( $script, '', array(), ET_BUILDER_VERSION, true );
		}
	}
}
add_action( 'wp_print_scripts', 'et_builder_dequeue_minified_scripts', 99999999 ); // <head>
add_action( 'wp_print_footer_scripts', 'et_builder_dequeue_minified_scripts', 9 ); // <footer>

function et_builder_dequeue_minifieds_styles() {
	if ( ! is_admin() ) {
		// Get builder minified + combined style handle.
		$builder_optimized_style_name = apply_filters( 'et_builder_optimized_style_handle', '' );

		foreach ( et_builder_get_minified_styles() as $style ) {
			// If dequeued style has inline style, get it then re-add it to minified + combiled style handle.
			// Inline style only has 'after' position.
			$inline_style = wp_styles()->get_data( $style, 'after' );

			// Inline style is saved as array. add_inline_style() method will handle it appending process.
			if ( is_array( $inline_style ) && ! empty( $inline_style ) ) {
				wp_styles()->add_inline_style( $builder_optimized_style_name, implode( ' ', $inline_style ), 'after' );
			}

			wp_dequeue_style( $style );
			wp_deregister_style( $style );
			wp_register_style( $style, '', array(), ET_BUILDER_VERSION );
		}
	}
}
add_action( 'wp_print_styles', 'et_builder_dequeue_minifieds_styles', 99999999 ); // <head>

/**
 * Determine whether current theme supports Waypoints or not
 *
 * @return bool
 */
function et_is_ignore_waypoints() {
	// WPBakery Visual Composer plugin conflicts with waypoints
	if ( class_exists( 'Vc_Manager' ) ) {
		return true;
	}

	// always return false if not in divi plugin
	if ( ! et_is_builder_plugin_active() ) {
		return false;
	}

	$theme_data = wp_get_theme();

	if ( empty( $theme_data ) ) {
		return false;
	}

	// list of themes without Waypoints support
	$no_waypoints_themes = array(
		'Avada',
	);
	$no_waypoints_themes = apply_filters( 'et_pb_no_waypoints_themes', $no_waypoints_themes );

	// return true if current theme doesn't support Waypoints
	if ( in_array( $theme_data->Name, $no_waypoints_themes, true ) ) {
		return true;
	}

	return false;
}

/**
 * Determine whether current page has enqueued theme's style.css or not
 * This is mainly used on preview screen to decide to enqueue theme's style nor not
 *
 * @return bool
 */
function et_builder_has_theme_style_enqueued() {
	global $wp_styles;

	if ( ! empty( $wp_styles->queue ) ) {
		$theme_style_uri = get_stylesheet_uri();

		foreach ( $wp_styles->queue as $handle ) {
			if ( isset( $wp_styles->registered[ $handle ]->src ) && $theme_style_uri === $wp_styles->registered[ $handle ]->src ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Added specific body classes for builder related situation
 * This enables theme to adjust its case independently
 *
 * @return array
 */
function et_builder_body_classes( $classes ) {
	if ( is_et_pb_preview() ) {
		$classes[] = 'et-pb-preview';
	}

	$post_id   = et_core_page_resource_get_the_ID();
	$post_type = get_post_type( $post_id );

	// Add layout classes when on library page
	if ( et_core_is_fb_enabled() && 'et_pb_layout' === $post_type ) {
		$layout_type  = et_fb_get_layout_type( $post_id );
		$layout_scope = et_fb_get_layout_term_slug( $post_id, 'scope' );

		$classes[] = "et_pb_library_page-{$layout_type}";
		$classes[] = "et_pb_library_page-{$layout_scope}";
	}

	return $classes;
}
add_filter( 'body_class', 'et_builder_body_classes' );

if ( ! function_exists( 'et_builder_add_main_elements' ) ) :
	/**
	 * Add Main Elements.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	function et_builder_add_main_elements() {
		if ( ET_BUILDER_CACHE_MODULES ) {
			ET_Builder_Element::init_cache();
		}

		// init this AFTER def cache has been init above.
		et_builder_init_shortcode_manager();

		require_once ET_BUILDER_DIR . 'main-structure-elements.php';

		require_once ET_BUILDER_DIR . 'class-et-builder-module-use-detection.php';

		/**
		 * Fires after the builder's structural element classes are loaded.
		 *
		 * @since 4.10.0
		 */
		do_action( 'et_builder_ready' );
	}
endif;

if ( ! function_exists( 'et_builder_init_shortcode_manager' ) ) :
	/**
	 * Initialize shortcode manager.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	function et_builder_init_shortcode_manager() {
		$manager = new ET_Builder_Module_Shortcode_Manager();
		$manager->init();
		do_action( 'et_builder_shortcode_manager_init' );
	}
endif;

if ( ! function_exists( 'et_builder_load_framework' ) ) :
	function et_builder_load_framework() {

		require_once ET_BUILDER_DIR . 'functions.php';
		require_once ET_BUILDER_DIR . 'compat/woocommerce.php';
		require_once ET_BUILDER_DIR . 'class-et-global-settings.php';
		require_once ET_BUILDER_DIR . 'feature/BlockEditorIntegration.php';

		if ( is_admin() ) {
			global $pagenow, $et_current_memory_limit;

			if ( ! empty( $pagenow ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
				$et_current_memory_limit = et_core_get_memory_limit();
			}
		}

		/**
		 * Filters builder modules loading hook. Load builder files on front-end and on specific admin pages only by default.
		 *
		 * @since 3.1
		 *
		 * @param string Hook name.
		 */
		$action_hook = apply_filters( 'et_builder_modules_load_hook', is_admin() ? 'wp_loaded' : 'wp' );

		if ( et_builder_should_load_framework() ) {
			require_once ET_BUILDER_DIR . 'class-et-builder-value.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-element.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-plugin-compat-base.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-plugin-compat-loader.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-theme-compat-base.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-theme-compat-handler.php';
			require_once ET_BUILDER_DIR . 'ab-testing.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-settings.php';

			require_once ET_BUILDER_DIR . 'class-et-builder-post-feature-base.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-global-feature-base.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-module-features.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-google-fonts-feature.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-dynamic-assets-feature.php';

			require_once ET_BUILDER_DIR . 'class-et-builder-module-shortcode-manager.php';

			$builder_settings_loaded = true;

			do_action( 'et_builder_framework_loaded' );

			add_action( $action_hook, 'et_builder_init_global_settings', apply_filters( 'et_pb_load_global_settings_priority', 9 ) );
			add_action( $action_hook, 'et_builder_add_main_elements', apply_filters( 'et_pb_load_main_elements_priority', 10 ) );
		} elseif ( is_admin() ) {
			require_once ET_BUILDER_DIR . 'class-et-builder-plugin-compat-base.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-plugin-compat-loader.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-settings.php';
			$builder_settings_loaded = true;
		}

		if ( isset( $builder_settings_loaded ) ) {
			add_action( 'init', 'et_builder_settings_init', 100 );
		}

		add_action( $action_hook, 'et_builder_load_frontend_builder' );

		if ( isset( $_GET['et_bfb'] ) && is_user_logged_in() ) {
			add_filter( 'wpe_heartbeat_allowed_pages', 'et_bfb_wpe_heartbeat_allowed_pages' );
		}
	}
endif;

/**
 * Checking whether current page is BFB page based on its query string only; Suitable for basic
 * early check BEFORE $wp_query global is generated in case builder need to alter query
 * configuration. This is needed because BFB layout is basically loaded in front-end
 *
 * @since 3.19.9
 *
 * @return bool
 */
function et_bfb_maybe_bfb_url() {
	$has_bfb_query_string = ! empty( $_GET['et_fb'] ) && ! empty( $_GET['et_bfb'] );
	$has_vb_permission    = et_pb_is_allowed( 'use_visual_builder' );

	// This check assumes that $wp_query isn't ready (to be used before query is parsed) so any
	// query based check such as is_single(), etc don't exist yet. Thus BFB URL might valid if:
	// 1. not admin page
	// 2. user has logged in
	// 3. has `et_fb` & `et_bfb` query string
	// 4. has visual builder permission
	return ! is_admin() && is_user_logged_in() && $has_bfb_query_string && $has_vb_permission;
}

/**
 * Get verified query string value for et_bfb_make_post_type_queryable()
 *
 * @since 3.19.9
 *
 * @param string $param_name
 *
 * @return string|number|bool
 */
function et_bfb_get_make_queryable_param( $param_name ) {
	$param          = isset( $_GET[ "et_{$param_name}" ] ) ? $_GET[ "et_{$param_name}" ] : false;
	$param_nonce    = isset( $_GET[ "et_{$param_name}_nonce" ] ) ? $_GET[ "et_{$param_name}_nonce" ] : false;
	$verified_param = $param && $param_nonce && wp_verify_nonce(
		$param_nonce,
		"et_{$param_name}_{$param}"
	);

	return $verified_param ? $param : false;
}

/**
 * Set builder's registered post type's publicly_queryable property to true (if needed) so publicly
 * hidden post type can render BFB page on backend edit screen
 *
 * @see WP->parse_request() on how request is parsed
 *
 * @since 3.19.9
 *
 * @return void
 */
function et_bfb_make_post_type_queryable() {
	// Valid query isn't available at this point so builder will guess whether current request is
	// BFB based on available value; Stop if this might not be BFB url
	if ( ! et_bfb_maybe_bfb_url() ) {
		return;
	}

	$get_post_id   = absint( et_bfb_get_make_queryable_param( 'post_id' ) );
	$get_post_type = sanitize_text_field( et_bfb_get_make_queryable_param( 'post_type' ) );

	// Stop if no valid post id / post type for make queryable found on query string
	if ( ! $get_post_id || ! $get_post_type ) {
		return;
	}

	$post_type_object = get_post_type_object( $get_post_type );

	// Stop if requested post type doesn't exist
	if ( is_null( $post_type_object ) ) {
		return;
	}

	$unqueryable_post_type    = et_builder_get_third_party_unqueryable_post_types();
	$is_post_type_unqueryable = in_array( $get_post_type, $unqueryable_post_type );

	// CPT's version of edit_post is always available on cap->edit_post regardless CPT's meta_map_cap
	// or capability_type setting are set or not. If meta_map_cap is set to true, WordPress
	// automatically translates it into edit_post. Otherwise, CPT version of edit_post is sent as
	// it is and it is plugin / post type registrant's responsibility to add the capability to role
	// and map it into primitive capabilities on map_meta_cap()
	$capability         = isset( $post_type_object->cap->edit_post ) ? $post_type_object->cap->edit_post : 'edit_post';
	$can_edit_this_post = current_user_can( $capability, $get_post_id );

	// Flip publicly_queryable of current request so BFB layout page can be rendered.
	// Note: post_type existence have been verified on is_null( $post_type_object ) check above
	if ( $is_post_type_unqueryable && $can_edit_this_post ) {
		global $wp_post_types;

		$wp_post_types[ $get_post_type ]->publicly_queryable = true;
	}
}
add_action( 'init', 'et_bfb_make_post_type_queryable' );

/**
 * Modify rewrite rule's redirect of current BFB request if its post type's `publicly_queryable`
 * is set to false and its `query_var` is NOT set to `false`. When this situation happens, current
 * BFB page cannot be rendered because rewrite rule's redirect value doesn't have `post_type`
 * param which makes page query gets incorrect page value
 *
 * @since 3.19.9
 *
 * @return void
 */
function et_bfb_make_cpt_rewrite_rule_queryable( $value ) {
	// Get verified make queryable post_type param from query string
	$unqueryable_post_type = et_bfb_get_make_queryable_param( 'post_type' );

	// Make sure that value is array, current request might be BFB, and verified post_type from
	// query string exist. Note: need to use early return otherwise the rest need multiple stack
	// if/else condition
	if ( ! is_array( $value ) || ! et_bfb_maybe_bfb_url() || ! $unqueryable_post_type ) {
		return $value;
	}

	$rewrite_regex        = $unqueryable_post_type . '/([^/]+)(?:/([0-9]+))?/?$';
	$rewrite_redirect     = isset( $value[ $rewrite_regex ] ) ? $value[ $rewrite_regex ] : false;
	$has_post_type_substr = $rewrite_redirect && strpos( $rewrite_redirect, '?post_type=' ) !== false;
	$post_type_object     = get_post_type_object( $unqueryable_post_type );

	// If current page's post type object `query_var` isn't falsey and no `post_type=` substring is
	// found on current page's post type rewrite rule redirect value, modify the rewrite rule
	// redirect value so it can picks up current post type when query is parsed
	if ( $post_type_object->query_var && ! $has_post_type_substr ) {
		$value[ $rewrite_regex ] = 'index.php?post_type=' . $unqueryable_post_type . '&name=$matches[1]&page=$matches[2]';
	}

	return $value;
}
add_filter( 'option_rewrite_rules', 'et_bfb_make_cpt_rewrite_rule_queryable' );

if ( ! function_exists( 'et_bfb_wpe_heartbeat_allowed_pages' ) ) :
	function et_bfb_wpe_heartbeat_allowed_pages( $pages ) {
		global $pagenow;

		$pages[] = $pagenow;

		return $pages;
	}
endif;

function et_builder_load_frontend_builder() {
	global $et_current_memory_limit;

	$et_current_memory_limit = et_core_get_memory_limit();

	// Set memory limit when there is a limit set, and that is less than 256M.
	if ( $et_current_memory_limit && $et_current_memory_limit < 256 ) {
		@ini_set( 'memory_limit', '256M' );
	}

	require_once ET_BUILDER_DIR . 'frontend-builder/init.php';
}

if ( ! function_exists( 'et_pb_get_google_api_key' ) ) :
	function et_pb_get_google_api_key() {
		$google_api_option = get_option( 'et_google_api_settings' );
		$google_api_key    = isset( $google_api_option['api_key'] ) ? $google_api_option['api_key'] : '';

		return $google_api_key;
	}
endif;

if ( ! function_exists( 'et_pb_enqueue_google_maps_script' ) ) :
	function et_pb_enqueue_google_maps_script() {
		$google_api_option          = get_option( 'et_google_api_settings' );
		$google_maps_script_enqueue = ! $google_api_option || ! isset( $google_api_option['enqueue_google_maps_script'] ) || ( isset( $google_api_option['enqueue_google_maps_script'] ) && 'on' === $google_api_option['enqueue_google_maps_script'] ) ? true : false;

		return apply_filters(
			'et_pb_enqueue_google_maps_script',
			$google_maps_script_enqueue
		);
	}
endif;

/**
 * Add pseudo-action via the_content to hook filter/action at the end of main content
 *
 * @param string  content string
 * @return string content string
 */
function et_pb_content_main_query( $content ) {
	global $post, $et_pb_comments_print;

	// Perform filter on main query + if builder is used only
	if ( is_main_query() && et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		add_filter( 'comment_class', 'et_pb_add_non_builder_comment_class', 10, 5 );

		// Actual front-end only adjustment. has_shortcode() can't use passed $content since
		// Its shortcode has been parsed
		if ( false === $et_pb_comments_print && ! et_fb_is_enabled() && has_shortcode( $post->post_content, 'et_pb_comments' ) ) {
			add_filter( 'get_comments_number', '__return_zero' );
			add_filter( 'comments_open', '__return_false' );
			add_filter( 'comments_array', '__return_empty_array' );
		}
	}

	return $content;
}
add_filter( 'the_content', 'et_pb_content_main_query', 1500 );
add_filter( 'et_builder_render_layout', 'et_pb_content_main_query', 1500 );

/**
 * Added special class name for comment items that are placed outside builder
 *
 * See {@see 'comment_class'}.
 *
 * @param  array       $classes    classname
 * @param  string      $comment    comma separated list of additional classes
 * @param  int         $comment_ID comment ID
 * @param  WP_Comment  $comment    comment object
 * @param  int|WP_Post $post_id    post ID or WP_Post object
 *
 * @return array modified classname
 */
function et_pb_add_non_builder_comment_class( $classes, $class, $comment_ID, $comment, $post_id ) {

	$classes[] = 'et-pb-non-builder-comment';

	return $classes;
}

/**
 * Enqueue Open Sans for the builder UI.
 *
 * @since 4.0
 *
 * @return void
 */
function et_builder_enqueue_open_sans() {
	if ( wp_style_is( 'et-core-main-fonts', 'enqueued' ) ) {
		return;
	}

	$protocol   = is_ssl() ? 'https' : 'http';
	$query_args = array(
		'family' => 'Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
		'subset' => 'latin,latin-ext',
	);

	wp_enqueue_style( 'et-fb-fonts', esc_url_raw( add_query_arg( $query_args, "{$protocol}://fonts.googleapis.com/css" ) ), array(), null );
}

et_builder_load_framework();

// Register assets that need to be fired at head
function et_builder_enqueue_assets_head() {
	// Setup WP media.
	// Around 5.2-alpha, `wp_enqueue_media` started using a function defined in a file
	// which is only included in admin. Unfortunately there's no safe/reliable way to conditionally
	// load this other than checking the WP version.
	if ( version_compare( $GLOBALS['wp_version'], '5.2-alpha-44947', '>=' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}

	wp_enqueue_media();

	// Setup Builder Media Library
	wp_enqueue_script( 'et_pb_media_library', ET_BUILDER_URI . '/scripts/ext/media-library.js', array( 'media-editor' ), ET_BUILDER_PRODUCT_VERSION, true );
}

// TODO, make this fire late enough, so that the_content has fired and ET_Builder_Element::get_computed_vars() is ready
// currently its being called in temporary_app_boot() in view.php
function et_builder_enqueue_assets_main() {
	$ver    = ET_BUILDER_VERSION;
	$root   = ET_BUILDER_URI;
	$assets = ET_BUILDER_URI . '/frontend-builder/assets';

	wp_register_script( 'wp-color-picker-alpha', ET_BUILDER_URI . '/scripts/ext/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ) );
	wp_localize_script(
		'wp-color-picker-alpha',
		'et_pb_color_picker_strings',
		apply_filters(
			'et_pb_color_picker_strings_builder',
			array(
				'legacy_pick'    => esc_html__( 'Select', 'et_builder' ),
				'legacy_current' => esc_html__( 'Current Color', 'et_builder' ),
			)
		)
	);

	wp_enqueue_script( 'wp-color-picker-alpha' );
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_style( 'et-core-admin', ET_CORE_URL . 'admin/css/core.css', array(), ET_CORE_VERSION );
	wp_enqueue_style( 'et-core-portability', ET_CORE_URL . 'admin/css/portability.css', array(), ET_CORE_VERSION );

	wp_register_style( 'et_pb_admin_date_css', "{$root}/styles/jquery-ui-1.12.1.custom.css", array(), $ver );
	wp_register_style( 'et-fb-top-window', "{$assets}/css/fb-top-window.css", array(), $ver );

	$conditional_deps = array();

	if ( ! et_builder_bfb_enabled() && ! et_builder_tb_enabled() ) {
		$conditional_deps[] = 'et-fb-top-window';
	}

	// Enqueue the appropriate bundle CSS (hot/start/build)
	$deps = array_merge(
		array(
			'et_pb_admin_date_css',
			'wp-mediaelement',
			'wp-color-picker',
			'et-core-admin',
		),
		$conditional_deps
	);

	et_fb_enqueue_bundle( 'et-frontend-builder', 'bundle.css', $deps );

	// Load Divi Builder style.css file with hardcore CSS resets and Full Open Sans font if the Divi Builder plugin is active
	if ( et_is_builder_plugin_active() ) {
		// `bundle.css` was removed from `divi-builder-style.css` and is now enqueued separately for the DBP as well.
		wp_enqueue_style(
			'et-builder-divi-builder-styles',
			"{$assets}/css/divi-builder-style.css",
			array_merge( array( 'et-core-admin', 'wp-color-picker' ), $conditional_deps ),
			$ver
		);
	}

	wp_enqueue_script( 'mce-view' );

	if ( ! et_core_use_google_fonts() || et_is_builder_plugin_active() ) {
		et_builder_enqueue_open_sans();
	}

	wp_enqueue_style( 'et-frontend-builder-failure-modal', "{$assets}/css/failure_modal.css", array(), $ver );
	wp_enqueue_style( 'et-frontend-builder-notification-modal', "{$root}/styles/notification_popup_styles.css", array(), $ver );
}

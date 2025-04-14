<?php
/**
 * Theme Options Library App helpers.
 *
 * @since ??
 *
 * @package Divi
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ET_Theme_Options_Library_App class.
 *
 * Nonces and i18n strings needed for Theme Options Library Cloud app.
 *
 * @package Divi
 */
class ET_Theme_Options_Library_App {
	/**
	 * Class instance.
	 *
	 * @var ET_Theme_Options_Library_App
	 */
	private static $_instance;

	/**
	 * Get the class instance.
	 *
	 * @return ET_Theme_Options_Library_App
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get Cloud Helpers.
	 *
	 * @return array Helpers.
	 */
	public static function get_cloud_helpers() {
		$role_capabilities = et_pb_get_role_settings();
		$user_role         = et_pb_get_current_user_role();
		$args              = array(
			'et_core_portability' => true,
			'context'             => 'epanel',
			'name'                => 'save',
			'nonce'               => wp_create_nonce( 'et_core_portability_export' ),
		);

		$epanel_save_url = add_query_arg( $args, admin_url() );

		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow -- Aligned manually.
		return [
			'i18n'         => [
				'library' => require ET_CORE_PATH . '/i18n/library.php',
				'epanel' => require ET_EPANEL_DIR . '/i18n/epanel.php',
			],
			'api'          => admin_url( 'admin-ajax.php' ),
			'capabilities' => isset( $role_capabilities[ $user_role ] ) ? $role_capabilities[ $user_role ] : array(),
			'epanel_save_url' => $epanel_save_url,
			'post_types' => [
				'et_theme_options' => ET_THEME_OPTIONS_POST_TYPE,
			],
			'nonces'       => [
				'et_theme_options_library_get_items'            => wp_create_nonce( 'et_theme_options_library_get_items' ),
				'et_theme_options_library_update_terms'         => wp_create_nonce( 'et_theme_options_library_update_terms' ),
				'et_theme_options_library_get_item_content'     => wp_create_nonce( 'et_theme_options_library_get_item_content' ),
				'et_theme_options_library_import_item_content'  => wp_create_nonce( 'et_theme_options_library_import_item_content' ),
				'et_core_portability_import'                    => wp_create_nonce( 'et_core_portability_import' ),
				'et_theme_options_library_update_item'          => wp_create_nonce( 'et_theme_options_library_update_item' ),
				'et_theme_options_library_export_item'          => wp_create_nonce( 'et_theme_options_library_export_item' ),
				'et_theme_options_library_get_token'            => wp_create_nonce( 'et_theme_options_library_get_token' ),
				'et_core_save_theme_options'                    => wp_create_nonce( 'et_core_save_theme_options' ),
				'et_core_portability_export'                    => wp_create_nonce( 'et_core_portability_export' ),
				'et_library_save_item'                          => wp_create_nonce( 'et_library_save_item' ),
				'et_theme_options_delete_temp_options'          => wp_create_nonce( 'et_theme_options_delete_temp_options' ),
				'et_theme_options_library_toggle_item_location' => wp_create_nonce( 'et_theme_options_library_toggle_item_location' ),
			],
		];
		// phpcs:enable
	}

	/**
	 * Load the Cloud App scripts.
	 *
	 * @param string $enqueue_prod_scripts Flag to force Production scripts.
	 * @param bool   $skip_react_loading   Flag to skip react loading.
	 *
	 * @return void
	 */
	public static function load_js( $enqueue_prod_scripts = true, $skip_react_loading = false ) {
		// phpcs:disable ET.Sniffs.ValidVariableName.VariableNotSnakeCase -- Following the pattern found in /cloud.
		$EPANEL_VERSION = et_get_theme_version();
		$ET_DEBUG       = defined( 'ET_DEBUG' ) && ET_DEBUG;
		$DEBUG          = $ET_DEBUG;

		$home_url      = wp_parse_url( get_site_url() );
		$build_dir_uri = ET_EPANEL_URI . '/build';
		$cache_buster  = $DEBUG ? mt_rand() / mt_getrandmax() : $EPANEL_VERSION; // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand -- mt_rand() should do for cache busting.
		$asset_path    = ET_EPANEL_DIR . '/build/et-theme-options-library-app.bundle.js';

		if ( file_exists( $asset_path ) ) {
			wp_enqueue_style( 'et-theme-options-library-styles', "{$build_dir_uri}/et-theme-options-library-app.bundle.css", [], (string) $cache_buster );
		}

		$BUNDLE_DEPS = [
			'jquery',
			'react',
			'react-dom',
			'es6-promise',
		];

		if ( $DEBUG || $enqueue_prod_scripts || file_exists( $asset_path ) ) {
			$BUNDLE_URI = ! file_exists( $asset_path ) ? "{$home_url['scheme']}://{$home_url['host']}:31599/et-theme-options-library-app.bundle.js" : "{$build_dir_uri}/et-theme-options-library-app.bundle.js";

			// Skip the React loading if we already have React ( Gutenberg editor for example ) to avoid conflicts.
			if ( ! $skip_react_loading ) {
				if ( function_exists( 'et_fb_enqueue_react' ) ) {
					et_fb_enqueue_react();
				}
			}

			wp_enqueue_script(
				'et-theme-options-library-app',
				$BUNDLE_URI,
				$BUNDLE_DEPS,
				(string) $cache_buster,
				true
			);

			wp_localize_script(
				'et-theme-options-library-app',
				'et_theme_options_data',
				self::get_cloud_helpers()
			);
		}
		// phpcs:enable
	}
}

ET_Theme_Options_Library_App::instance();

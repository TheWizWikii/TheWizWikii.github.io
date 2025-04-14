<?php
/**
 * Code Snippets App helpers.
 *
 * @since 4.19.0
 *
 * @package Divi
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ET_Code_Snippets_App class.
 *
 * Nonces and i18n strings needed for Code Snippets Library Cloud app.
 *
 * @package Divi
 */
class ET_Code_Snippets_App {
	/**
	 * Class instance.
	 *
	 * @var ET_Code_Snippets_App
	 */
	private static $_instance;

	/**
	 * Get the class instance.
	 *
	 * @since 4.19.0
	 *
	 * @return ET_Code_Snippets_App
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

		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow -- Aligned manually.
		return [
			'i18n'         => [ 'library' => require ET_CORE_PATH . '/i18n/library.php' ],
			'api'          => admin_url( 'admin-ajax.php' ),
			'capabilities' => isset( $role_capabilities[ $user_role ] ) ? $role_capabilities[ $user_role ] : array(),
			'nonces'       => [
				'et_code_snippets_library_get_items'            => wp_create_nonce( 'et_code_snippets_library_get_items' ),
				'et_code_snippets_library_get_item_content'     => wp_create_nonce( 'et_code_snippets_library_get_item_content' ),
				'et_code_snippets_library_update_item'          => wp_create_nonce( 'et_code_snippets_library_update_item' ),
				'et_code_snippets_library_toggle_item_location' => wp_create_nonce( 'et_code_snippets_library_toggle_item_location' ),
				'et_code_snippets_library_export_item'          => wp_create_nonce( 'et_code_snippets_library_export_item' ),
				'et_code_snippets_library_import_item'          => wp_create_nonce( 'et_code_snippets_library_import_item' ),
				'et_code_snippets_library_save_item_content'    => wp_create_nonce( 'et_code_snippets_library_save_item_content' ),
				'et_code_snippets_library_update_terms'         => wp_create_nonce( 'et_code_snippets_library_update_terms' ),
				'et_code_snippets_library_get_token'            => wp_create_nonce( 'et_code_snippets_library_get_token' ),
				'et_code_snippets_save_to_local_library'        => wp_create_nonce( 'et_code_snippets_save_to_local_library' ),
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
	 * @since 4.19.0
	 *
	 * @return void
	 */
	public static function load_js( $enqueue_prod_scripts = true, $skip_react_loading = false ) {
		// phpcs:disable ET.Sniffs.ValidVariableName.VariableNotSnakeCase -- Following the pattern found in /cloud.
		$CORE_VERSION = defined( 'ET_CORE_VERSION' ) ? ET_CORE_VERSION : '';
		$ET_DEBUG     = defined( 'ET_DEBUG' ) && ET_DEBUG;
		$DEBUG        = $ET_DEBUG;

		$home_url       = wp_parse_url( get_site_url() );
		$build_dir_uri  = ET_CORE_URL . 'build';
		$common_scripts = ET_COMMON_URL . '/scripts';
		$cache_buster   = $DEBUG ? mt_rand() / mt_getrandmax() : $CORE_VERSION; // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand -- mt_rand() should do for cache busting.
		$asset_path     = ET_CORE_PATH . 'build/et-core-app.bundle.js';

		if ( file_exists( $asset_path ) ) {
			wp_enqueue_style( 'et-code-snippets-styles', "{$build_dir_uri}/et-core-app.bundle.css", [], (string) $cache_buster );
		}

		wp_enqueue_script( 'es6-promise', "{$common_scripts}/es6-promise.auto.min.js", [], '4.2.2', true );

		$BUNDLE_DEPS = [
			'jquery',
			'react',
			'react-dom',
			'es6-promise',
		];

		if ( $DEBUG || $enqueue_prod_scripts || file_exists( $asset_path ) ) {
			$BUNDLE_URI = ! file_exists( $asset_path ) ? "{$home_url['scheme']}://{$home_url['host']}:31499/et-core-app.bundle.js" : "{$build_dir_uri}/et-core-app.bundle.js";

			// Skip the React loading if we already have React ( Gutenberg editor for example ) to avoid conflicts.
			if ( ! $skip_react_loading ) {
				if ( function_exists( 'et_fb_enqueue_react' ) ) {
					et_fb_enqueue_react();
				}
			}

			wp_enqueue_script(
				'et-code-snippets-app',
				$BUNDLE_URI,
				$BUNDLE_DEPS,
				(string) $cache_buster,
				true
			);

			wp_localize_script(
				'et-code-snippets-app',
				'et_code_snippets_data',
				self::get_cloud_helpers()
			);
		}
	}
}

ET_Code_Snippets_App::instance();
// phpcs:enable

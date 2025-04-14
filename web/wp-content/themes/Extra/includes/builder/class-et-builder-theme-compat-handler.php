<?php
/**
 * Load theme compatibility file for current theme.
 *
 * @since 4.10.0
 * @package Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class ET_Builder_Plugin_Compat_Loader.
 */
class ET_Builder_Theme_Compat_Handler {
	/**
	 * Unique instance of class.
	 *
	 * @var ET_Builder_Theme_Compat_Handler
	 */
	public static $instance;

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->_init_hooks();
	}

	/**
	 * Gets the instance of the class.
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook methods to WordPress action and filter.
	 *
	 * @return void
	 */
	private function _init_hooks() {
		// Load plugin.php for frontend usage.
		if ( ! function_exists( 'wp_get_theme' ) ) {
			include_once ABSPATH . 'wp-admin/includes/theme.php';
		}

		$current_theme      = wp_get_theme();
		$current_theme_name = $current_theme->get( 'TextDomain' );
		$theme_compat_url   = apply_filters(
			"et_builder_theme_compat_path_{$current_theme_name}",
			ET_BUILDER_DIR . "theme-compat/{$current_theme_name}.php",
			$current_theme_name
		);

		if ( file_exists( $theme_compat_url ) ) {
			require_once $theme_compat_url;
		}

	}
}

ET_Builder_Theme_Compat_Handler::init();

<?php
/**
 * Plugin compat divi space.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin compatibility for CoursePress Pro
 *
 * @since 3.21.3
 */
class ET_Builder_Plugin_Compat_DiviRocket extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'ds-divi-rocket/ds-divi-rocket.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @return void
	 */
	public function init_hooks() {

		if ( ! class_exists( 'DiviRocket' ) ) {
			return;
		}

		add_action( 'wp_loaded', array( $this, 'remove_shortcode_manager' ) );

	}

	/**
	 * Remove the extra shortcode manager.
	 *
	 * @since 4.10.0
	 * @return void
	 */
	public function remove_shortcode_manager() {
		// Set empty array to short-circuit the plugin's shortcode manager.
		DiviRocket::$shortcodeFiles = []; //phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- third party.
	}

}

new ET_Builder_Plugin_Compat_DiviRocket();

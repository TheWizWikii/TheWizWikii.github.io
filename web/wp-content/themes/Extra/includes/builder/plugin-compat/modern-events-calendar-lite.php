<?php
/**
 * Compatibility for Modern Events Calendar Lite.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for Modern Events Calendar Lite.
 *
 * @since 4.10.6
 *
 * @link https://wordpress.org/plugins/modern-events-calendar-lite/
 */
class ET_Builder_Plugin_Compat_Modern_Events_Calendar_Lite extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.10.6
	 */
	public function __construct() {
		$this->plugin_id = 'modern-events-calendar-lite/modern-events-calendar-lite.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.10.6
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Modern Events Calendar Lite JS code is:
		// 1. Added everywhere.
		// 2. Not compatible with JQuery Body feature.
		// Only way to solve is to always disable the latter when the plugin is active.
		add_filter( 'et_builder_enable_jquery_body', '__return_false' );
	}
}

new ET_Builder_Plugin_Compat_Modern_Events_Calendar_Lite();

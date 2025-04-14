<?php
/**
 * Plugin Compatibility for Divi Filterable Blog Module plugin.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.11.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin Compatibility for Divi Filterable Blog Module.
 *
 * @since 4.11.4
 */
class ET_Builder_Plugin_Compat_Divi_Filterable_Blog_Module extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor.
	 *
	 * @since 4.11.4
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 4.11.4
	 *
	 * @return void
	 */
	public function init_hooks() {
		if ( ! is_plugin_active( 'divi-filterable-blog-module/divi-filterable-blog-module.php' ) ) {
			return;
		}

		$dfbm_controller = new dfbmControllerInitialize();
		$dfbm_module     = new dfbmControllerModules();

		// Add new hooks.
		add_action( 'et_builder_ready', array( $dfbm_controller, 'setFrontend' ) );
		add_action( 'et_builder_ready', array( $dfbm_module, 'modules' ) );
	}
}

new ET_Builder_Plugin_Compat_Divi_Filterable_Blog_Module();

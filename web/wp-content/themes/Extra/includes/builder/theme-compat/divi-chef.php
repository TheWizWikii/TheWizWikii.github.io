<?php
/**
 * Theme compat divi chef theme.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Theme compatibility for Divi Chef.
 *
 * @since 4.10.0
 */
class ET_Builder_Theme_Compat_DiviChef extends ET_Builder_Theme_Compat_Base {

	/**
	 * Constructor.
	 *
	 * @since 4.10.0
	 */
	public function __construct() {
		$this->theme_id = $this->get_theme_dir_path();
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.10.0
	 *
	 * @return void
	 */
	public function init_hooks() {
		$current_theme = $this->get_theme_data();
		if ( 'divi-chef' !== $current_theme->get( 'TextDomain' ) ) {
			return;
		}

		// Remove old hook. Add new one.
		remove_action( 'et_builder_modules_loaded', 'el_dc_custom_modules' );
		add_action( 'et_builder_ready', 'el_dc_custom_modules' );
	}


}

new ET_Builder_Theme_Compat_DiviChef();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Gravityforms
 *
 * @since 3.19
 *
 * @link https://www.gravityforms.com/
 */
class ET_Builder_Plugin_Compat_Gravityforms extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'gravityforms/gravityforms.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		$is_bfb = et_()->array_get( $_GET, 'et_bfb' );

		// Load Gravity Form button in BFB
		if ( $is_bfb ) {
			add_filter( 'gform_display_add_form_button', '__return_true' );
		}
	}
}

new ET_Builder_Plugin_Compat_Gravityforms();

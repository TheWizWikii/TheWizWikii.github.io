<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for CoursePress Pro
 *
 * @since 3.21.3
 */
class ET_Builder_Plugin_Compat_CoursePress extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'coursepress/coursepress.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {

		if ( ! method_exists( 'CoursePress_Admin_Edit', 'enable_tinymce' ) ) {
			return;
		}

		// Remove the filter so it doesn't execute too early....
		remove_filter( 'user_can_richedit', array( 'CoursePress_Admin_Edit', 'enable_tinymce' ) );
		// ... and add it again later, when it won't cause errors.
		add_action( 'current_screen', array( $this, 'current_screen' ) );

	}

	/**
	 * Add the filter again.
	 *
	 * @access public.
	 * @return void
	 */
	public function current_screen() {
		add_filter( 'user_can_richedit', array( 'CoursePress_Admin_Edit', 'enable_tinymce' ) );
	}
}

new ET_Builder_Plugin_Compat_CoursePress();

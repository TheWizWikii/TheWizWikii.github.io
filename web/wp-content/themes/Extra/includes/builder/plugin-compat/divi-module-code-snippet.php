<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Divi Code Snippet Module
 *
 * @since 3.17.3
 */
class ET_Builder_Plugin_Compat_Divi_Code_Snippet_Module extends ET_Builder_Plugin_Compat_Base {

	protected $posts = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'divi-module-code-snippet/divi-module-code-snippet.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		if ( function_exists( 'load_DMB_Module_Code_Snippet' ) && function_exists( 'load_db_cs_ET_Builder_Module' ) ) {
			remove_action( 'wp_loaded', 'load_db_cs_ET_Builder_Module' );
			remove_action( 'wp_loaded', 'load_DMB_Module_Code_Snippet' );

			add_action( 'et_builder_ready', array( $this, 'et_builder_ready' ) );
			add_filter( 'the_posts', array( $this, 'the_posts' ) );
		}
	}

	/**
	 * Saves filter value for later use.
	 *
	 * @return array
	 */
	public function the_posts( $posts ) {
		$this->posts = $posts;
		return $posts;
	}

	/**
	 * Runs when the builder is ready.
	 *
	 * @return void
	 */
	public function et_builder_ready() {
		load_db_cs_ET_Builder_Module();
		load_DMB_Module_Code_Snippet();

		$instance = et_()->array_get( ET_Builder_Module::get_modules(), 'et_pb_dmb_code_snippet', false );

		if ( $instance ) {
			$instance->module_used_in_post( $this->posts );
		}
	}
}

new ET_Builder_Plugin_Compat_Divi_Code_Snippet_Module();

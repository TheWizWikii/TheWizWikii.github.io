<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WordPress Landing Pages
 */
class ET_Builder_Plugin_Compat_Landing_Pages extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'landing-pages/landing-pages.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'et_fb_framework_loaded', array( $this, 'fix_the_content_hooks' ) );
	}

	function fix_the_content_hooks() {
		$post_id   = et_core_page_resource_get_the_ID();
		$post_type = get_post_type( $post_id );

		if ( 'landing-page' === $post_type ) {
			// Landing Page plugin adds `the_content` filter with 20 priority, so we have to fire our actions after that.
			add_filter( 'the_content', 'et_fb_app_boot', 30 );
			add_filter( 'the_content', 'et_builder_add_builder_content_wrapper', 31 );
		}
	}
}

new ET_Builder_Plugin_Compat_Landing_Pages();

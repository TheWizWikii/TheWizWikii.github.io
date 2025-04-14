<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Divi Woo Layout Injector.
 *
 * @since 4.0.5
 *
 * @link https://elegantmarketplace.com/downloads/woo-layout-injector-subscription/
 */
class ET_Builder_Plugin_Compat_Divi_Woo_Layout_Injector extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 */
	function __construct() {
		$this->plugin_id = 'divi_woo_layout_injector/divi_woo_layout_injector.php';

		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'the_content', array( $this, 'maybe_disable_in_the_content' ), 998 );
	}

	/**
	 * Disable layout injection when editing TB layouts.
	 *
	 * @since 4.0.5
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	function maybe_disable_in_the_content( $content ) {
		$is_tb = et_theme_builder_is_layout_post_type( get_post_type( get_the_ID() ) );

		if ( $is_tb ) {
			remove_filter( 'the_content', 'sb_et_woo_li_content_filter', 999 );
		} elseif ( did_action( 'plugins_loaded' ) && ! has_action( 'the_content', 'sb_et_woo_li_content_filter' ) ) {
			add_filter( 'the_content', 'sb_et_woo_li_content_filter', 999 );
		}

		return $content;
	}
}

new ET_Builder_Plugin_Compat_Divi_Woo_Layout_Injector();

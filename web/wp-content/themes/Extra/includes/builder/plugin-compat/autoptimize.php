<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Autoptimize
 *
 * @since 3.17.1
 *
 * @link https://wordpress.org/plugins/autoptimize/
 */
class ET_Builder_Plugin_Compat_Autoptimize extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'autoptimize/autoptimize.php';
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

		$enabled = array(
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			'vb'  => et_()->array_get( $_GET, 'et_fb' ),
			'bfb' => et_()->array_get( $_GET, 'et_bfb' ),
			// phpcs:enable
		);

		if ( $enabled['vb'] || $enabled['bfb'] ) {
			// JS optimization breaks the builder so we need to disable it
			add_filter( 'autoptimize_filter_js_noptimize', '__return_true' );
			add_filter( 'autoptimize_filter_css_noptimize', '__return_true' );
		}
	}
}

new ET_Builder_Plugin_Compat_Autoptimize();

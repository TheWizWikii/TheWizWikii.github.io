<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Caldera Forms
 *
 * @since 3.17.3
 *
 * @link https://wordpress.org/plugins/caldera-forms/
 */
class ET_Builder_Plugin_Compat_Caldera_Forms extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'caldera-forms/caldera-core.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1.7.6
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		if ( ! class_exists( 'Caldera_Forms_Admin' ) ) {
			return;
		}

		$enabled = array(
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			'vb'  => et_()->array_get( $_GET, 'et_fb' ),
			'bfb' => et_()->array_get( $_GET, 'et_bfb' ),
			// phpcs:enable
		);

		if ( $enabled['vb'] && ! $enabled['bfb'] ) {
			// Caldera Form custom tinyMCE's button doesn't work in VB, let's remove it.
			$instance = Caldera_Forms_Admin::get_instance();
			remove_action( 'media_buttons', array( $instance, 'shortcode_insert_button' ), 11 );
		}
	}
}

new ET_Builder_Plugin_Compat_Caldera_Forms();

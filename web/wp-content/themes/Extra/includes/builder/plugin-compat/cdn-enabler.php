<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for CDN Enabler
 *
 * @since 3.19.10
 *
 * @link https://wordpress.org/plugins/cdn-enabler/
 */
class ET_Builder_Plugin_Compat_CDN_Enabler extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'cdn-enabler/cdn-enabler.php';
		$this->init_hooks();
	}

	/**
	 * Replace CDN Enabler option with custom values.
	 *
	 * @param string $value Option value.
	 *
	 * @access public.
	 * @return void
	 */
	public function option_cdn_enabler( $value ) {
		$fix      = ',(,]';
		$excludes = et_()->array_get( $value, 'excludes', '' );

		// If the fix isn't included
		if ( substr( $excludes, -strlen( $fix ) ) !== $fix ) {
			$value['excludes'] = "$excludes$fix";
		}
		return $value;
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1.0.8
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
			add_action( 'option_cdn_enabler', array( $this, 'option_cdn_enabler' ) );
		}

	}
}

new ET_Builder_Plugin_Compat_CDN_Enabler();

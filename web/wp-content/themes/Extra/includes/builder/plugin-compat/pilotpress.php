<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for PilotPress Views
 *
 * @since 3.21.1
 *
 * @link https://wordpress.org/plugins/pilotpress/
 */
class ET_Builder_Plugin_Compat_PilotPress extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'pilotpress/pilotpress.php';
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

		// I know what you're thinking .... can't check for 'edit_post' because too early
		// and don't have a post ID yet but will do inside the filter.
		if ( ( $enabled['vb'] || $enabled['bfb'] ) && current_user_can( 'edit_posts' ) ) {
			// Plugin's content filter breaks VB / BFB, disable it
			add_action( 'pilotpress_content_hiding', array( $this, 'pilotpress_content_hiding' ) );
		}
	}

	/**
	 * Remove plugin's custom content filter.
	 *
	 * @return void
	 */
	public function pilotpress_content_hiding() {
		global $pilotpress, $post;

		if ( $pilotpress && $post && current_user_can( 'edit_post', $post->ID ) ) {
			remove_filter( 'the_content', array( $pilotpress, 'content_process' ) );
		}

	}
}

new ET_Builder_Plugin_Compat_PilotPress();

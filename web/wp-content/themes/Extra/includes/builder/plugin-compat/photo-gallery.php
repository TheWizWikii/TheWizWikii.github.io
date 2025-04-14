<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Photo Gallery
 *
 * @since 3.21.3
 */
class ET_Builder_Plugin_Compat_PhotoGallery extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'photo-gallery/photo-gallery.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		if ( ! method_exists( 'BWG', 'instance' ) ) {
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
		if ( $enabled['vb'] && ! $enabled['bfb'] && current_user_can( 'edit_posts' ) ) {
			add_action( 'wp', array( $this, 'wp' ) );
		}
	}

	/**
	 * Disable plugin's TinyMCE custom button because it doesn't work in VB
	 *
	 * @access public.
	 * @return void
	 */
	public function wp() {
		global $post;
		if ( $post && current_user_can( 'edit_post', $post->ID ) ) {
			remove_filter( 'media_buttons_context', array( BWG::instance(), 'media_button' ) );
		}
	}
}

new ET_Builder_Plugin_Compat_PhotoGallery();

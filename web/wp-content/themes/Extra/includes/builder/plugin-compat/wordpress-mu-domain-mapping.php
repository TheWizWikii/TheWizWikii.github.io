<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WordPress MU Domain Mapping
 */
class ET_Builder_Plugin_Compat_Mu_Domain_Mapping extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'wordpress-mu-domain-mapping/domain_mapping.php';

		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	function init_hooks() {
		if ( ! function_exists( 'redirect_to_mapped_domain' ) ) {
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
			// Override plugin's redirection code.
			remove_action( 'template_redirect', 'redirect_to_mapped_domain' );
			add_action( 'template_redirect', array( $this, 'redirect' ) );
		}
	}

	/**
	 * Disable plugin redirections when VB/BFB page and current user can edit.
	 *
	 * @return void
	 */
	public function redirect() {
		global $post;
		if ( ! ( $post && current_user_can( 'edit_post', $post->ID ) ) ) {
			// Perform redirections anyway when current user cannot edit the page.
			redirect_to_mapped_domain();
		}
	}
}

new ET_Builder_Plugin_Compat_Mu_Domain_Mapping();

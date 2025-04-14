<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Paid Membership Pro
 *
 * @since 3.20.2
 *
 * @link https://wordpress.org/plugins/paid-memberships-pro/
 */
class ET_Builder_Plugin_Compat_PaidMembershipProp extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'paid-memberships-pro/paid-memberships-pro.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		if ( ! function_exists( 'pmpro_wp' ) ) {
			return;
		}

		$enabled = array(
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			'vb'  => et_()->array_get( $_GET, 'et_fb' ),
			'bfb' => et_()->array_get( $_GET, 'et_bfb' ),
			// phpcs:enable
		);

		if ( $enabled['vb'] || $enabled['bfb'] ) {
			// Override plugin's redirection code.
			remove_action( 'wp', 'pmpro_wp', 1 );
			add_action( 'wp', array( $this, 'wp' ), 1 );
		}

	}

	/**
	 * Disable plugin redirections when VB/BFB page and current user can edit.
	 *
	 * @return void
	 */
	public function wp() {
		global $post;

		if ( ! ( $post && current_user_can( 'edit_post', $post->ID ) ) ) {
			// Perform redirections anyway when current user cannot edit the page.
			pmpro_wp();
		}
	}
}

new ET_Builder_Plugin_Compat_PaidMembershipProp();

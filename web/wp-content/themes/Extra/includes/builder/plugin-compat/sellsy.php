<?php
/**
 * ET_Builder_Plugin_Compat_Sellsy class file.
 *
 * @class   ET_Builder_Plugin_Compat_Sellsy
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin compatibility for Sellsy
 *
 * @since 4.17.5
 * @link https://wordpress.org/plugins/sellsy/
 */
class ET_Builder_Plugin_Compat_Sellsy extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'sellsy/index.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'wp', array( $this, 'sellsy_restore_post_global' ), 1 );
	}

	/**
	 * Method to return unseted variable
	 *
	 * @return void
	 */
	public function sellsy_restore_post_global() {
		// phpcs:disable WordPress.Security.NonceVerification -- since the variable is already empty, there is no need to verify.
		if ( empty( $_POST ) ) {
			$_POST = array();
		}
	}
}
new ET_Builder_Plugin_Compat_Sellsy();

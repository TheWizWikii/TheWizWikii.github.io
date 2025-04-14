<?php
/**
 * Compatibility for PayPal Plus for WooCommerce.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for PayPal Plus for WooCommerce.
 *
 * @since 4.10.8
 *
 * @link https://wordpress.org/plugins/woo-paypalplus/
 */
class ET_Builder_Plugin_Compat_Woo_PayPal_Plus extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.10.8
	 */
	public function __construct() {
		$this->plugin_id = 'woo-paypalplus/paypalplus-woocommerce.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.10.8
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_enable_jquery_body', array( $this, 'maybe_disable_jquery_body' ), 10, 2 );
	}

	/**
	 * Maybe Disable JQuery Body feature.
	 *
	 * @since 4.10.8
	 *
	 * @param bool   $enabled Whether the feature should be enabled or not.
	 * @param string $content TB/Post content.
	 *
	 * @return bool
	 */
	public function maybe_disable_jquery_body( $enabled, $content ) {
		// Disable when plugin scripts are enqueued.
		return wp_script_is( 'ppplus' ) ? false : $enabled;
	}
}

new ET_Builder_Plugin_Compat_Woo_PayPal_Plus();

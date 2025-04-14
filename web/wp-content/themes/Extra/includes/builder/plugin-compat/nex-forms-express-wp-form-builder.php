<?php
/**
 * Compatibility for NEX-Forms.
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
 * Compatibility for NEX-Forms.
 *
 * @since 4.10.8
 *
 * @link https://wordpress.org/plugins/nex-forms-express-wp-form-builder/
 */
class ET_Builder_Plugin_Compat_NEX_Forms extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.10.8
	 */
	public function __construct() {
		$this->plugin_id = 'nex-forms-express-wp-form-builder/main.php';
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
		// Disable when enqueued or `NEXForms` shortcode is used.
		return false !== strpos( $content, '[NEXForms' ) ? false : $enabled;
	}
}

new ET_Builder_Plugin_Compat_NEX_Forms();

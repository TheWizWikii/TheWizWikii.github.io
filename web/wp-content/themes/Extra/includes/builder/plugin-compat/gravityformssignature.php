<?php
/**
 * Compatibility Gravity Forms Signature Add-On.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility Gravity Forms Signature Add-On.
 *
 * @since 4.10.6
 *
 * @link https://gravityforms.com
 */
class ET_Builder_Plugin_Compat_GravityForms_Signature_AddOn extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.10.6
	 */
	public function __construct() {
		$this->plugin_id = 'gravityformssignature/signature.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.10.6
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
	 * @since 4.10.6
	 *
	 * @param bool   $enabled Whether the feature should be enabled or not.
	 * @param string $content TB/Post content.
	 *
	 * @return bool
	 */
	public function maybe_disable_jquery_body( $enabled, $content ) {
		// Disable when plugin scripts are enqueued or `gravityform` shortcode is used.
		// Ideally we'd want to do the latter only when the form actually includes a signature field
		// but that would require extracting and parsing the shortcode manually in order to get the form ID
		// and we already have enough opened can of worms to deal with.
		return wp_script_is( 'super_signature_script' ) || false !== strpos( $content, '[gravityform' ) ? false : $enabled;
	}
}

new ET_Builder_Plugin_Compat_GravityForms_Signature_AddOn();

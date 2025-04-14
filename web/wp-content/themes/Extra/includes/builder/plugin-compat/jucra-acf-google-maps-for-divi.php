<?php
/**
 * Plugin compatibility for Jucra ACF Maps.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin compatibility for Jucra ACF Maps.
 *
 * @since 4.10.5
 *
 * @link https://www.jucra.com/display-acf-maps-in-a-divi-theme-builder-page/
 */
class ET_Builder_Plugin_Compat_Jucra_ACF_Maps extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'jucra-acf-google-maps-for-divi/index.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		add_filter( 'et_builder_enable_jquery_body', [ $this, 'maybe_disable_jquery_body' ], 10, 2 );
	}

	/**
	 * Disable JQuery Body feature when showing a map
	 *
	 * @since 4.10.5
	 *
	 * @param bool   $enabled Whether the feature should be enabled or not.
	 * @param string $content TB/Post Content.
	 *
	 * @return bool
	 */
	public function maybe_disable_jquery_body( $enabled, $content = '' ) {
		if ( empty( $content ) ) {
			return $enabled;
		}

		// disable when the shortcode is found.
		return false === strpos( $content, 'jucra_acf_map' ) ? $enabled : false;
	}

}

new ET_Builder_Plugin_Compat_Jucra_ACF_Maps();

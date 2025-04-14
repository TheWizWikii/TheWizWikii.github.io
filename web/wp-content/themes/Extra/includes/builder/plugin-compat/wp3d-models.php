<?php
/**
 * Compatibility for WP3D Models.
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
 * Compatibility for WP3D Models.
 *
 * @since 4.10.8
 *
 * @link https://wp3dmodels.com/
 */
class ET_Builder_Plugin_Compat_WP3D_Models extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.10.8
	 */
	public function __construct() {
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
		// No version check here is intentional: the plugin is very niche, commercial only,
		// expensive and they ignored my request to provide a copy so that I could fix the issue.
		// Due the above, we don't know the complete slug.
		// However, by checking customer's site, we know for sure that folder is name `wp3d-models`
		// and that's enough to detect the plugin and disable JQuery Body.
		add_filter( 'et_builder_enable_jquery_body', '__return_false', 10, 2 );
	}
}

new ET_Builder_Plugin_Compat_WP3D_Models();

<?php // phpcs:ignore WordPress.Files.FileName -- We don't follow WP filename format.
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * ET_Builder_Plugin_Compat_WP_Rocket class file.
 *
 * @class   ET_Builder_Plugin_Compat_WP_Rocket
 * @package Divi
 * @subpackage Builder
 * @since 4.14.5
 * @link https://wp-rocket.me/
 */
class ET_Builder_Plugin_Compat_WP_Rocket extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.14.5
	 */
	public function __construct() {
		$this->plugin_id = 'wp-rocket/wp-rocket.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.14.5
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'rocket_rucss_safelist', array( $this, 'et_rocket_css_safelist' ), 10, 1 );
	}

	/**
	 * CSS safelist to exclude from WP Rocket's 'Unused CSS' option.
	 * The array may contain CSS filenames, IDs or classes.
	 *
	 * @since 4.14.5
	 *
	 * @param array[] $css_safelist list of CSS filenames, IDs or classes.
	 *
	 * @return array[]
	 */
	public function et_rocket_css_safelist( $css_safelist ) {
		$et_css_safelist = array(
			'.et_pb_blog_grid',
		);

		return array_merge( $css_safelist, $et_css_safelist );
	}
}

new ET_Builder_Plugin_Compat_WP_Rocket();

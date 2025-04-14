<?php
/**
 * Compatibility for M Chart
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
 * Compatibility for M Chart.
 *
 * @since 4.10.6
 *
 * @link http://github.com/methnen/m-chart
 */
class ET_Builder_Plugin_Compat_M_Chart extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.10.6
	 */
	public function __construct() {
		$this->plugin_id = 'm-chart/m-chart.php';
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
		// Disable when showing plugin's post type / shortcode.
		return is_singular( 'm-chart' ) || false !== strpos( $content, '[chart' ) ? false : $enabled;
	}
}

new ET_Builder_Plugin_Compat_M_Chart();

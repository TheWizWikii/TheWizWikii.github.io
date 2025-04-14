<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WP Responsive Table.
 *
 * @since 4.4.0
 *
 * @link https://wordpress.org/plugins/wp-responsive-table/
 */
class ET_Builder_Plugin_Compat_WPResponsiveTable extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'wp-responsive-table/wp-responsive-table.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1.2.4
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_render_layout', array( $this, 'add_content_wrapper' ) );
	}

	/**
	 * Equivalent of ResponsiveTable\Frontend\Frontend::addContentWrapper().
	 * We have no sensible way to get the instance reference and run its method
	 * so we just add the container it would've added manually.
	 *
	 * @since 4.4.0
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function add_content_wrapper( $content ) {
		return '<div class="wprt-container">' . $content . '</div>';
	}
}

new ET_Builder_Plugin_Compat_WPResponsiveTable();

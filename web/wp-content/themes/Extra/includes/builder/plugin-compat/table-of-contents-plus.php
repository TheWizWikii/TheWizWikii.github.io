<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Table Of Contents Plus
 *
 * @since 3.0.89
 *
 * @link https://wordpress.org/plugins/table-of-contents-plus/
 */
class ET_Builder_Plugin_Compat_Table_Of_Contents_Plus extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'table-of-contents-plus/toc.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1601
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Do not render plugin shortcodes in admin
		add_filter( 'et_pb_admin_excluded_shortcodes', array( $this, 'et_pb_admin_excluded_shortcodes' ) );
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	function et_pb_admin_excluded_shortcodes( $shortcodes ) {
		$shortcodes[] = 'toc';
		return $shortcodes;
	}
}

new ET_Builder_Plugin_Compat_Table_Of_Contents_Plus();

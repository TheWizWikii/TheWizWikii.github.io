<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WPML Sticky Links. This plugin needs WPML Multilingual
 * CMS plugin to work.
 *
 * @since 4.4.5
 *
 * @link https://wpml.org
 */
class ET_Builder_Plugin_Compat_WPML_Sticky_Links extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'wpml-sticky-links/plugin.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1.5.1
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found or no WPML Multilingual CMS plugin active.
		if ( ! $this->get_plugin_version() || ! defined( 'ICL_PLUGIN_PATH' ) ) {
			return;
		}

		add_filter( 'et_pb_module_content', array( $this, 'maybe_show_permalinks' ), 10, 3 );
	}

	/**
	 * Convert sticky links into permalinks on Global items.
	 *
	 * @since 4.4.5
	 *
	 * @param  string $content
	 * @param  array  $props
	 * @param  array  $attrs
	 * @return string
	 */
	public function maybe_show_permalinks( $content, $props, $attrs ) {
		$global_module_id = et_()->array_get( $attrs, 'global_module' );
		if ( empty( $global_module_id ) || ! class_exists( 'WPML_Sticky_Links' ) ) {
			return $content;
		}

		$sticky_links = new WPML_Sticky_Links();
		return $sticky_links->show_permalinks( $content );
	}
}

new ET_Builder_Plugin_Compat_WPML_Sticky_Links();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Max Mega Menu
 *
 * @since 4.4.5
 *
 * @link https://wordpress.org/plugins/megamenu/
 */
class ET_Builder_Plugin_Compat_Megamenu extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'megamenu/megamenu.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_get_widget_areas_list', array( $this, 'remove_sidebar' ) );
	}

	/**
	 * Remove Mega Menu sidebar from Builders widget areas list.
	 *
	 * @since 4.4.5
	 *
	 * @return void
	 */
	public function remove_sidebar( $list ) {
		// This plugin creates a custom sidebar for convenience: widgets added
		// to menus are also listed in Widgets WP Admin page and can be edited
		// there.

		// Such area can't be used by the Sidebar Module because only added
		// when `is_admin() === true`, hence it would always show as empty when
		// rendered by the FE.

		// Additionally, it causes Builder to reload when cache is deleted
		// ( eg due to enabling/disabling a plugin ).

		// This is due to widget areas being included in static helpers and the
		// list being different when generated inline (when the page is
		// loading) or via the AJAX call: `is_admin` would be `false` in the
		// first case and `true` in the latter.
		unset( $list['mega-menu'] );
		return $list;
	}
}

new ET_Builder_Plugin_Compat_Megamenu();

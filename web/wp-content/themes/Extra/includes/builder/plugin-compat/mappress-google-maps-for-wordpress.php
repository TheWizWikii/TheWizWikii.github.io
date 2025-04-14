<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for "MapPress Easy Google Maps"
 *
 * @since 3.0.98
 * @link https://wordpress.org/plugins/insert-pages/
 */
class ET_Builder_Plugin_Compat_Mappress extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'mappress-google-maps-for-wordpress/mappress.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Once this issue is fixed in future version, do version_compare() to limit the scope of the fix
	 * Latest plugin version: 2.47.5
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest plugin version
		add_action( 'wp_footer', array( $this, 'dequeue_scripts' ) );
	}

	/**
	 * Mappress loads the exact same Google Maps library Divi is using which causes an issue
	 *
	 * @return void
	 */
	function dequeue_scripts() {
		// Check if current page is builder page and enqueueing Google Maps script
		if ( is_singular() && et_pb_is_pagebuilder_used( get_the_ID() ) && et_pb_enqueue_google_maps_script() ) {
			// Deregister MapPress' Google Maps
			wp_dequeue_script( 'mappress-gmaps' );

			// There's no cleaner way to add dependency to registered script, thus direct access to
			// $wp_scripts. Append Divi's google maps handle to make MapPress' script depends on it
			global $wp_scripts;

			if ( isset( $wp_scripts->registered['mappress'] ) && ! in_array( 'google-maps-api', $wp_scripts->registered['mappress']->deps ) ) {
				$wp_scripts->registered['mappress']->deps[] = 'google-maps-api';
			}
		}
	}
}
new ET_Builder_Plugin_Compat_Mappress();

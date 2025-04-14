<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for Events Manager plugin.
 *
 * @since 3.10
 *
 * @link https://wordpress.org/plugins/events-manager/
 */
class ET_Builder_Plugin_Compat_Events_Manager extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 3.10
	 */
	public function __construct() {
		$this->plugin_id = 'events-manager/events-manager.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'em_event_output_placeholder', array( $this, 'maybe_filter_content' ), 10, 4 );
		add_filter( 'em_location_output_placeholder', array( $this, 'maybe_filter_content' ), 10, 4 );
	}

	/**
	 * Pass the single event content through et_fb_app_boot() since Events Manager skips usual
	 * `the_content` filters.
	 *
	 * @since 3.10
	 *
	 * @param string $replace
	 * @param mixed  $event
	 * @param string $full_result
	 * @param mixed  $target
	 *
	 * @return string
	 */
	public function maybe_filter_content( $replace, $event, $full_result, $target ) {
		$content_placeholders = array(
			'#_NOTES',
			'#_EVENTNOTES',
			'#_DESCRIPTION',
			'#_LOCATIONNOTES',
		);

		if ( ! function_exists( 'et_fb_app_boot' ) || ! in_array( $full_result, $content_placeholders ) ) {
			return $replace;
		}

		return et_fb_app_boot( $replace );
	}
}

new ET_Builder_Plugin_Compat_Events_Manager();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for The Events Calendar Community Events plugin.
 *
 * @since 4.4.9
 *
 * @link https://theeventscalendar.com/
 */
class ET_Builder_Plugin_Compat_The_Events_Calendar_Community_Events extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.4.9
	 */
	public function __construct() {
		$this->plugin_id = 'the-events-calendar-community-events/tribe-community-events.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.4.9
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'the_post', array( $this, 'maybe_restore_layout_content' ), 11, 2 );
	}

	/**
	 * Maybe restore TB layout content.
	 *
	 * Restore global $pages content on TB layouts when they override the template.
	 *
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	function maybe_restore_layout_content( $post ) {
		if ( ! et_theme_builder_is_layout_post_type( $post->post_type ) ) {
			return;
		}

		if ( ! et_theme_builder_overrides_layout( $post->post_type ) ) {
			return;
		}

		global $pages;
		$pages = array( $post->post_content );
	}
}

new ET_Builder_Plugin_Compat_The_Events_Calendar_Community_Events();

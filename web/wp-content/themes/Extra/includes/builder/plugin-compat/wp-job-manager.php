<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WP Job Manager.
 *
 * @since 4.0.10
 *
 * @link https://wordpress.org/plugins/wp-job-manager/
 */
class ET_Builder_Plugin_Compat_WPJobManager extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor.
	 *
	 * @since 4.0.10
	 */
	public function __construct() {
		$this->plugin_id = 'wp-job-manager/wp-job-manager.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 * Latest plugin version: 1.33.3
	 *
	 * @since 4.0.10
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'option_job_manager_hide_expired_content', array( $this, 'never_hide_content_in_builder' ) );
		add_filter( 'wpjm_the_job_description', array( $this, 'maybe_call_the_content' ) );
	}

	/**
	 * Always show the content even for expired jobs when editing in the builder.
	 *
	 * @since 4.0.10
	 *
	 * @param mixed $hide
	 *
	 * @return boolean
	 */
	public function never_hide_content_in_builder( $hide ) {
		if ( et_core_is_fb_enabled() ) {
			return false;
		}

		return $hide;
	}

	/**
	 * Maybe trigger the_content() instead of returning the description as the plugin
	 * does not call the_content().
	 * Do this only if the builder is used for the current post.
	 *
	 * @since 4.0.10
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function maybe_call_the_content( $content ) {
		static $output = false;

		if ( ! $output && et_core_is_builder_used_on_current_request() ) {
			if ( doing_action( 'wp_footer' ) ) {
				// Do not use the full content when outputting structured data.
				return truncate_post( apply_filters( 'excerpt_length', 55 ), false );
			}

			$output = true;

			the_content();

			return '';
		}

		return $content;
	}
}

new ET_Builder_Plugin_Compat_WPJobManager();

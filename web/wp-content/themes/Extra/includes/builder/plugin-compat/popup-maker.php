<?php
/**
 * ET_Builder_Plugin_Compat_Popup_Maker class file.
 *
 * @class   ET_Builder_Plugin_Compat_Popup_Maker
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for Popup Maker plugin.
 *
 * @since 4.13.0
 *
 * @link https://wordpress.org/plugins/popup-maker/
 */
class ET_Builder_Plugin_Compat_Popup_Maker extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.13.0
	 */
	public function __construct() {
		$this->plugin_id = 'popup-maker/popup-maker.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.13.0
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'all_module_css_selector', array( $this, 'et_builder_maybe_update_module_styles_selector' ), 10, 4 );
		add_filter( 'et_pb_set_style_selector', array( $this, 'et_builder_maybe_update_selector' ), 10, 4 );
		add_filter( 'et_core_enqueued_style_handle', array( $this, 'et_builder_maybe_update_style_handle' ), 10, 4 );

		// Disable Feature: Dynamic Assets.
		add_filter( 'et_use_dynamic_css', array( $this, 'et_builder_disable_dynamic_features' ), 10, 4 );
		add_filter( 'et_should_generate_dynamic_assets', array( $this, 'et_builder_disable_dynamic_features' ), 10, 4 );

		// Disable Cache in Feature Manager.
		add_filter( 'et_builder_post_feature_cache_enabled', array( $this, 'et_builder_disable_dynamic_features' ), 10, 4 );

		// Override Waypoint context.
		add_filter( 'et_builder_waypoints_options', array( $this, 'maybe_override_waypoints_options' ) );
	}

	/**
	 * Return false if Popup Maker is active to disable dynamic assets feature.
	 *
	 * @since 4.13.0
	 *
	 * @param bool $current_state Current state of the feature.
	 *
	 * @return string
	 */
	public function et_builder_disable_dynamic_features( $current_state ) {
		// Should only be modified for Popup Maker plugin.
		if ( ! class_exists( 'PUM_Shortcode_Popup' ) ) {
			return $current_state;
		}

		return false;
	}

	/**
	 * Update Divi Builder selector for Popup Maker plugin.
	 * The purpose of this update is to make sure custom module styles applied to the content inside Popup Maker which placed outside the main page content and `#page-container` container
	 *
	 * @since 4.13.0
	 *
	 * @param string $selector Selector to modify.
	 *
	 * @return string
	 */
	public function et_builder_maybe_update_module_styles_selector( $selector ) {
		// Selector should only be modified for Popup Maker plugin.
		if ( ! class_exists( 'PUM_Shortcode_Popup' ) ) {
			return $selector;
		}

		// Add 'body .pum-container' into selector along with existing 'body #page-container' to target the content inside Popup Maker.
		if ( false !== strpos( $selector, 'body #page-container' ) ) {
			// add the prefix for all the selectors in a string.
			$pum_prefixed_selector = str_replace( 'body #page-container', 'body .pum-container', $selector );
			$selector             .= ', ' . $pum_prefixed_selector;
		}

		return $selector;
	}

	/**
	 * Update Divi Builder selector for Popup Maker plugin.
	 * The purpose of this update is to make sure custom module styles applied to the content inside Popup Maker which placed outside the main page content and `#et-boc` container
	 *
	 * @since 4.13.0
	 *
	 * @param string $selector Selector to modify.
	 *
	 * @return string
	 */
	public function et_builder_maybe_update_selector( $selector ) {
		// Selector should only be modified for Popup Maker plugin.
		if ( ! class_exists( 'PUM_Shortcode_Popup' ) ) {
			return $selector;
		}

		// Add '.et-db .pum' into selector along with existing '.et-db #et-boc' to target the content inside Popup Maker.
		if ( false !== strpos( $selector, '.et-db #et-boc' ) ) {
			// add the prefix for all the selectors in a string.
			$non_prefixed_selector = str_replace( '.et-db #et-boc', '.et-db .pum', $selector );
			$selector             .= ', ' . $non_prefixed_selector;
		}

		return $selector;
	}

	/**
	 * Update divi-style handle when replacing divi main style with the CPT style for Popup Maker plugin.
	 * The purpose of this update is to make sure Divi main style is loaded along with the CPT style
	 * Otherwise Content inside Popup Maker plugin loses styles because it's placed outside the main page content and `#et-boc` container
	 *
	 * @since 4.13.0
	 *
	 * @param string $handle Handle to modify.
	 *
	 * @return string
	 */
	public function et_builder_maybe_update_style_handle( $handle ) {
		// Handle should only be modified for Popup Maker plugin and when it's `divi-style`.
		if ( ! class_exists( 'PUM_Shortcode_Popup' ) || 'divi-style' !== $handle ) {
			return $handle;
		}

		// Add suffix to make sure not prefixed divi-style won't be dequeued.
		return $handle . '-pum';
	}

	/**
	 * Override Waypoints context for modules inside Popup Maker overlay.
	 *
	 * @since 4.15.0
	 *
	 * @param array $options Waypoints options.
	 *
	 * @return array Filtered Waypoints options.
	 */
	public function maybe_override_waypoints_options( $options ) {
		// Check whether `context` property exists or not.
		if ( ! isset( $options['context'] ) ) {
			$options['context'] = array();
		}

		// Make sure the existing `context` is already on array format. Then add Popup Maker
		// overlay selector to the list.
		$options['context']   = (array) $options['context'];
		$options['context'][] = '.pum-overlay';

		return $options;
	}
}

new ET_Builder_Plugin_Compat_Popup_Maker();

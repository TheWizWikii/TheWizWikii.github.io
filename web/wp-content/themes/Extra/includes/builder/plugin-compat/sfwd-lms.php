<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the LearnDash plugin.
 *
 * @since 4.3.4
 *
 * @link https://www.learndash.com/
 */
class ET_Builder_Plugin_Compat_LearnDash extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Original `in_the_loop` property value for the layouts.
	 *
	 * @var array
	 */
	protected $_in_the_loop = array();

	/**
	 * Constructor.
	 *
	 * @since 4.3.4
	 */
	public function __construct() {
		$this->plugin_id = 'sfwd-lms/sfwd_lms.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.3.4
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'learndash-focus-header-before', array( $this, 'fire_learndash_compatibility_action' ) );
		add_action( 'learndash-focus-template-start', array( $this, 'maybe_inject_theme_builder_header' ) );
		add_action( 'learndash-focus-template-end', array( $this, 'maybe_inject_theme_builder_footer' ) );
		add_action( 'et_theme_builder_template_before_body', array( $this, 'maybe_override_query_before_body' ), 10, 3 );
		add_action( 'et_theme_builder_template_after_body', array( $this, 'maybe_override_query_after_body' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'focus_mode_compatibility_script' ), 99 );
	}

	/**
	 * Maybe override `$wp_query` temporarily before TB layout body template.
	 *
	 * @since 4.7.4
	 *
	 * @param integer $layout_id      TB layout post ID (header, body, footer).
	 * @param boolean $layout_enabled Current layout status whether is enabled or not.
	 * @param integer $template_id    TB template post ID (parent of TB layout).
	 */
	public function maybe_override_query_before_body( $layout_id, $layout_enabled, $template_id ) {
		if ( ! $layout_enabled || ! function_exists( 'learndash_get_post_types' ) ) {
			return;
		}

		if ( ! in_array( get_post_type(), learndash_get_post_types(), true ) ) {
			return;
		}

		global $wp_query;

		// Save current value as reference for later usage.
		$this->_in_the_loop[ $layout_id ] = $wp_query->in_the_loop;

		// Force the query to be treated as a loop (fake loop).
		$wp_query->in_the_loop = true;
	}

	/**
	 * Maybe restore `$wp_query` after TB layout body template rendered.
	 *
	 * @since 4.7.4
	 *
	 * @param integer $layout_id      TB layout post ID (header, body, footer).
	 * @param boolean $layout_enabled Current layout status whether is enabled or not.
	 * @param integer $template_id    TB template post ID (parent of TB layout).
	 */
	public function maybe_override_query_after_body( $layout_id, $layout_enabled, $template_id ) {
		if ( ! $layout_enabled || ! function_exists( 'learndash_get_post_types' ) ) {
			return;
		}

		if ( ! in_array( get_post_type(), learndash_get_post_types(), true ) ) {
			return;
		}

		global $wp_query;

		// Restore `in_the_loop` property to original state.
		$wp_query->in_the_loop = et_()->array_get( $this->_in_the_loop, $layout_id, false );
	}

	/**
	 * Disable TB hooks for Divi and Extra.
	 *
	 * @since 4.3.4
	 */
	public function fire_learndash_compatibility_action() {
		/**
		 * Fires when LearnDash Focus mode is enabled for the current request.
		 *
		 * @since 4.3.4
		 */
		do_action( 'et_theme_builder_compatibility_learndash_focus_mode' );
	}

	/**
	 * Maybe inject the TB header back in.
	 *
	 * @since 4.3.4
	 */
	public function maybe_inject_theme_builder_header() {
		$layouts = et_theme_builder_get_template_layouts();

		if ( empty( $layouts ) ) {
			return;
		}

		et_theme_builder_frontend_render_header(
			$layouts[ ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ]['id'],
			$layouts[ ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ]['enabled'],
			$layouts[ ET_THEME_BUILDER_TEMPLATE_POST_TYPE ]
		);
	}

	/**
	 * Maybe inject the TB footer back in.
	 *
	 * @since 4.3.4
	 */
	public function maybe_inject_theme_builder_footer() {
		$layouts = et_theme_builder_get_template_layouts();

		if ( empty( $layouts ) ) {
			return;
		}

		et_theme_builder_frontend_render_footer(
			$layouts[ ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ]['id'],
			$layouts[ ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ]['enabled'],
			$layouts[ ET_THEME_BUILDER_TEMPLATE_POST_TYPE ]
		);
	}

	/**
	 * Focus Mode compatibility for global header and footer.
	 */
	public function focus_mode_compatibility_script() {
		wp_enqueue_script( 'et-builder-sfwd-lms-compat-scripts', ET_BUILDER_URI . '/plugin-compat/scripts/sfwd-lms.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	}
}

new ET_Builder_Plugin_Compat_LearnDash();

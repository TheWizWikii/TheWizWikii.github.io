<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Toolset Views
 *
 * @since 3.20
 *
 * @link https://toolset.com
 */
class ET_Builder_Plugin_Compat_ToolsetViews extends ET_Builder_Plugin_Compat_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'wp-views/wp-views.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 2.7.1
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_post_types', array( $this, 'add_post_type' ) );
		add_filter( 'et_builder_third_party_post_types', array( $this, 'add_post_type' ) );
		add_filter( 'et_builder_third_party_unqueriable_post_types', array( $this, 'add_post_type' ) );

		$enabled = array(
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			'vb'  => et_()->array_get( $_GET, 'et_fb' ),
			'bfb' => et_()->array_get( $_GET, 'et_bfb' ),
			// phpcs:enable
		);

		if ( $enabled['vb'] && ! $enabled['bfb'] ) {
			// Fields and Views custom TinyMCE button doesn't work in VB and also generate an error.
			add_action( 'toolset_editor_add_form_buttons', '__return_false' );
		}

		// I know what you're thinking .... can't check for 'edit_post' because too early
		// and don't have a post ID yet but will do inside the filter.
		if ( $enabled['vb'] && $enabled['bfb'] && current_user_can( 'edit_posts' ) ) {
			// Content templates not using Divi break the BFB, disable them.
			add_filter( 'get_post_metadata', array( $this, 'disable_views' ), 10, 4 );
		}

		add_filter( 'et_builder_render_layout', array( $this, 'transform_shortcodes' ), 4 );
	}

	/**
	 * Add `view-template` post type.
	 *
	 * @param array $types
	 * @return array
	 */
	public function add_post_type( $types ) {
		return array_merge( $types, array( 'view-template' ) );
	}

	/**
	 * Disable Views for a post.
	 *
	 * @param null|array|string $value     The value get_metadata() should return - a single metadata value,
	 *                                     or an array of values.
	 * @param int               $object_id Object ID.
	 * @param string            $meta_key  Meta key.
	 * @param bool              $single    Whether to return only the first value of the specified $meta_key.
	 *
	 * @access public.
	 * @return void
	 */
	public function disable_views( $value, $object_id, $meta_key, $single ) {
		if ( '_views_template' === $meta_key && current_user_can( 'edit_post', $object_id ) ) {
			return false;
		}
		return $value;
	}

	/**
	 * Transform {!{ ... }!} shortcodes to [] ones.
	 *
	 * @since 4.0.10
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function transform_shortcodes( $content ) {
		/**
		 * @see WPV_Frontend_Render_Filters::pre_process_shortcodes()
		 *
		 * @param string $content
		 */
		$content = apply_filters( 'wpv-pre-process-shortcodes', $content );

		/**
		 * @see Toolset_Shortcode_Transformer::replace_shortcode_placeholders_with_brackets()
		 *
		 * @param string $content
		 */
		$content = apply_filters( 'toolset_transform_shortcode_format', $content );

		return $content;
	}
}

new ET_Builder_Plugin_Compat_ToolsetViews();

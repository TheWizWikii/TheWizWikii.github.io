<?php
/**
 * Module Use Detection class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Handles Module Use Detection.
 *
 * @since 4.10.0
 */
class ET_Builder_Module_Use_Detection {

	/**
	 * Module Slugs Used.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_modules_used = [];

	/**
	 * Module Attrs Used.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_module_attrs_used = [];

	/**
	 * Module Attr Values Used.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_module_attr_values_used = [];

	/**
	 * Valid Shortcode Slugs.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_valid_slugs = [];

	/**
	 * `ET_Builder_Module_Use_Detection` instance.
	 *
	 * @var ET_Builder_Module_Use_Detection
	 */
	private static $_instance;

	/**
	 * Construct instance.
	 */
	public function __construct() {
		add_filter( 'pre_do_shortcode_tag', [ $this, 'log_slug_used' ], 99, 3 );
		add_action( 'wp_footer', [ $this, 'footer' ], 1000 );

		add_action( 'et_builder_ready', array( $this, '_setup_valid_slugs' ), 100 );
	}

	/**
	 * Get instance.
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new static();
		}

		return self::$_instance;
	}

	/**
	 * Get valid slugs.
	 */
	public function _setup_valid_slugs() {
		$this->_valid_slugs = ET_Builder_Element::get_all_module_slugs();
	}

	/**
	 * Log the Shortcode Tag/Slug.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param mixed  $override Whether to override do_shortcode return value or not.
	 * @param string $tag Shortcode tag.
	 * @param array  $attrs Shortcode attrs.
	 * @return mixed
	 */
	public function log_slug_used( $override, $tag, $attrs ) {
		$interested_attrs_and_values = apply_filters(
			'et_builder_module_attrs_values_used',
			[
				'gutter_width',
				'animation_style',
				'sticky_position',
				'specialty',
				'use_custom_gutter',
				'font_icon',
				'button_icon',
				'hover_icon',
				'scroll_down_icon',
				'social_network',
				'show_in_lightbox',
				'fullwidth',
				'scroll_vertical_motion_enable',
				'scroll_horizontal_motion_enable',
				'scroll_fade_enable',
				'scroll_scaling_enable',
				'scroll_rotating_enable',
				'scroll_blur_enable',
				'show_content',
			]
		);

		/**
		 * The "gallery" shortcode is not part of the Divi modules but is used for enqueuing MagnificPopup
		 * when Divi Gallery is enabled under Theme Options > Enable Divi Gallery, so we need to include
		 * it in late detection for edge cases such as shortcodes hardcoded into child themes.
		 */
		$additional_valid_slugs = apply_filters(
			'et_builder_valid_module_slugs',
			[
				'gallery',
			]
		);

		$valid_slugs = array_unique( array_merge( $this->_valid_slugs, $additional_valid_slugs ) );

		// Log the shortcode tags used.
		if ( in_array( $tag, $valid_slugs, true ) ) {
			$this->_modules_used[] = $tag;
			$this->_modules_used   = array_unique( $this->_modules_used );

			if ( ! is_null( $attrs ) && ! is_array( $attrs ) ) {
				$attrs = (array) $attrs;
			}

			$found_interested_attr_and_values = array_intersect( array_keys( $attrs ), $interested_attrs_and_values );
			foreach ( $found_interested_attr_and_values as $key => $attr_name ) {
				if ( empty( $this->_module_attr_values_used[ $attr_name ] ) ) {
					$this->_module_attr_values_used[ $attr_name ] = [];
				}

				$this->_module_attr_values_used[ $attr_name ][] = $attrs[ $attr_name ];
				$this->_module_attr_values_used[ $attr_name ]   = array_unique( $this->_module_attr_values_used[ $attr_name ] );
			}
		}

		return $override;
	}

	/**
	 * Add footer actions.
	 */
	public function footer() {
		/**
		 * Fires after wp_footer hook and contains unique array of
		 * slugs of the modules that were used on the page load.
		 *
		 * @param array  $_used_modules Module slugs used on the page load.
		 *
		 * @since 4.10.0
		 */
		do_action( 'et_builder_modules_used', $this->_modules_used );
	}

	/**
	 * Get modules used.
	 *
	 * @return array List of module slugs used.
	 * @since 4.10.0
	 * @access public
	 */
	public function get_modules_used() {
		return $this->_modules_used;
	}

	/**
	 * Get module attrs used.
	 *
	 * @return array List of interested module attrs used.
	 * @since 4.10.0
	 * @access public
	 */
	public function get_module_attrs_used() {
		return $this->_module_attrs_used;
	}

	/**
	 * Get module attr values used.
	 *
	 * @return array List of interested module attrs and values used.
	 * @since 4.10.0
	 * @access public
	 */
	public function get_module_attr_values_used() {
		return $this->_module_attr_values_used;
	}

}

ET_Builder_Module_Use_Detection::instance();

<?php
/**
 * Module Features feature class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Handles Builder Module Features.
 *
 * @since 4.10.0
 */
class ET_Builder_Module_Features extends ET_Builder_Post_Feature_Base {

	const CACHE_META_KEY = '_et_builder_module_features_cache';

	/**
	 * Cache group.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache_group = [];

	/**
	 * Construct instance.
	 */
	public function __construct() {
		parent::__construct();
		// Get shortcode tag / attributes.
		add_filter( 'pre_do_shortcode_tag', [ $this, 'set_cache_group' ], 99, 3 );
		add_filter( 'do_shortcode_tag', [ $this, 'restore_previous_cache_group' ] );
	}

	/**
	 * Creates a key from a shortcode tag and its attributes.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param string $tag Shortcode tag.
	 * @param string $attrs Shortcode attributes.
	 *
	 * @return string
	 */
	public static function get_key( $tag, $attrs ) {
		$key = $tag . '_' . md5( wp_json_encode( $attrs ) );
		return $key;
	}

	/**
	 * Set cache group.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param mixed  $override Whether to override do_shortcode return value or not.
	 * @param string $tag Shortcode tag.
	 * @param string $attrs Shortcode attributes.
	 * @return mixed
	 */
	public function set_cache_group( $override, $tag, $attrs ) {
		$this->_cache_group[] = self::get_key( $tag, $attrs );
		return $override;
	}

	/**
	 * Restore previous cache group when current shortcode execution ends.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param mixed $output Shortcode content.
	 * @return mixed
	 */
	public function restore_previous_cache_group( $output ) {
		// Get rid of current shortcode cache group.
		array_pop( $this->_cache_group );
		return $output;
	}

	/**
	 * Check for cached value.
	 *
	 * First check cache if present, if not, determine
	 * from calling the callback.
	 *
	 * @param string   $key Name of item.
	 * @param function $cb  Callback function to perform logic.
	 * @param string   $group Cache group.
	 *
	 * @return bool/mixed Result.
	 */
	public function get( $key, $cb, $group = 'default' ) {
		return parent::get( $key, $cb, end( $this->_cache_group ) );
	}

}

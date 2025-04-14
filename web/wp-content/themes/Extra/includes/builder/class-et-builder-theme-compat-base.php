<?php
/**
 * Theme compat base class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base class for plugin compatibility file.
 *
 * @since 4.10.0
 */
class ET_Builder_Theme_Compat_Base {

	/**
	 * Theme name.
	 *
	 * @access private
	 * @var array
	 */
	public $theme_id;

	/**
	 * Get theme dir path based on theme_id.
	 *
	 * @return sting
	 */
	public function get_theme_dir_path() {
		return get_stylesheet_directory();
	}

	/**
	 * Get theme data.
	 *
	 * @return object
	 */
	public function get_theme_data() {
		return wp_get_theme();
	}

}

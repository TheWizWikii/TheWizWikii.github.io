<?php
/**
 * Background Pattern Config
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Background_Pattern_Style_Factory.
 */
class ET_Builder_Background_Pattern_Style_Factory {
	/**
	 * Class instance object
	 *
	 * @var array Holds all Pattern Style instance.
	 */
	private static $_instance = array();

	/**
	 * Get instance of the Class
	 *
	 * @param string $name Pattern Style Name.
	 *
	 * @return ET_Builder_Background_Pattern_Style_Base
	 */
	public static function get( $name ) {
		$name = sanitize_file_name( $name );

		if ( ! isset( self::$_instance[ $name ] ) ) {
			// Look at feature/background-masks/pattern directory.
			$file     = ET_BUILDER_DIR . "feature/background-masks/pattern/$name.php";
			$instance = file_exists( $file ) ? require_once $file : null;

			self::$_instance[ $name ] = $instance instanceof ET_Builder_Background_Pattern_Style_Base ? $instance : null;
		}

		return self::$_instance[ $name ];
	}
}

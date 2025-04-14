<?php
/**
 * Factory Class for Mask Style Options.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Background_Mask_Style_Factory.
 *
 * @since 4.15.0
 */
class ET_Builder_Background_Mask_Style_Factory {
	/**
	 * Class instance object
	 *
	 * @var array Holds all Mask Style instance.
	 */
	private static $_instance = array();

	/**
	 * Get instance of the Class
	 *
	 * @param string $name Mask Style Name.
	 *
	 * @return ET_Builder_Background_Mask_Style_Base
	 */
	public static function get( $name ) {
		$name = sanitize_file_name( $name );

		if ( ! isset( self::$_instance[ $name ] ) ) {
			// Look at feature/background-masks/mask directory.
			$file     = ET_BUILDER_DIR . "feature/background-masks/mask/$name.php";
			$instance = file_exists( $file ) ? require_once $file : null;

			self::$_instance[ $name ] = $instance instanceof ET_Builder_Background_Mask_Style_Base ? $instance : null;
		}

		return self::$_instance[ $name ];
	}
}

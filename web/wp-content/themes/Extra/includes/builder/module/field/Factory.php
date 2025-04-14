<?php

class ET_Builder_Module_Fields_Factory {

	protected static $fields = array();

	/**
	 * @param $fields_type
	 *
	 * @return ET_Builder_Module_Field_Base
	 */
	public static function get( $fields_type ) {
		if ( ! isset( self::$fields[ $fields_type ] ) ) {
			$file = implode( DIRECTORY_SEPARATOR, array( ET_BUILDER_DIR, 'module', 'field', "$fields_type.php" ) );

			$instance = file_exists( $file ) ? require_once $file : null;

			self::$fields[ $fields_type ] = $instance instanceof ET_Builder_Module_Field_Base ? $instance : null;
		}

		return self::$fields[ $fields_type ];
	}
}

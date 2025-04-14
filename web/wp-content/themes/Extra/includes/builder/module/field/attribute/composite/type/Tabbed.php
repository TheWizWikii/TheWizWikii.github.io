<?php

/**
 * Class for parsing shortcode attributes for tabbed controls.
 * The tabbed composite structure as follows
 * 'composite_structure' => array(
 *      //tab
 *      'border_all' => array(
 *          //tab icon
 *          'icon'     => 'border-all',
 *           //list of standard controls which will be placed within the tab
 *          'controls' => array(
 *              'border_width_all' => array(
 *                  'label'          => esc_html__( 'Border Width', 'et_builder' ),
 *                  'type'           => 'range',
 *                  'default'           => '0',
 *                  'range_settings' => array(
 *                      'min'  => 0,
 *                      'max'  => 50,
 *                      'step' => 1,
 *                  ),
 *              ),
 *              'border_color_all' => array(
 *                  'label'   => esc_html__( 'Border Color', 'et_builder' ),
 *                  'type'    => $color_type,
 *                  'default' => '#333333',
 *              ),
 *          ),
 *      ),
 *  )
 */
class ET_Builder_Module_Field_Attribute_Composite_Type_Tabbed {

	/**
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	public static function parse( $structure ) {
		$result = array();

		if ( ! is_array( $structure ) ) {
			return $result;
		}

		if ( is_null( self::$_ ) ) {
			self::$_ = ET_Core_Data_Utils::instance();
		}

		foreach ( $structure as $tab ) {
			if ( ! isset( $tab['controls'] ) || ! is_array( $tab['controls'] ) ) {
				continue;
			}

			foreach ( $tab['controls'] as $control => $control_settings ) {
				// We don't want to set defaults right now as doing so makes it virtually impossible
				// to generate accurate border css styles later on.
				$result[ $control ] = '';
			}
		}

		return $result;
	}
}

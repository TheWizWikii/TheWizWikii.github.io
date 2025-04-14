<?php

class ET_Builder_Module_Settings_Migration_DropShadowToBoxShadow extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.94';

	public function get_modules() {
		return array(
			'et_pb_blog',
		);
	}

	public function get_fields() {
		return array(
			'box_shadow_horizontal' => array(
				'affected_fields' => array(
					'box_shadow_style' => $this->get_modules(),
				),
			),
			'box_shadow_vertical'   => array(
				'affected_fields' => array(
					'box_shadow_style' => $this->get_modules(),
				),
			),
			'box_shadow_blur'       => array(
				'affected_fields' => array(
					'box_shadow_style' => $this->get_modules(),
				),
			),
			'box_shadow_spread'     => array(
				'affected_fields' => array(
					'box_shadow_style' => $this->get_modules(),
				),
			),
			'box_shadow_color'      => array(
				'affected_fields' => array(
					'box_shadow_style' => $this->get_modules(),
				),
			),
			'box_shadow_style'      => array(
				'affected_fields' => array(
					'use_dropshadow' => $this->get_modules(),
				),
			),
		);
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if (
			'on' !== ET_Core_Data_Utils::instance()->array_get( $attrs, 'use_dropshadow' )
			||
			'off' !== ET_Core_Data_Utils::instance()->array_get( $attrs, 'fullwidth' )
		) {
			return $saved_value;
		}

		switch ( $field_name ) {
			case 'box_shadow_style':
				return 'none' !== $saved_value ? $saved_value : 'preset1';
			case 'box_shadow_blur':
				return 'none' === $current_value ? '5px' : $saved_value;
			case 'box_shadow_horizontal':
				return 'none' === $current_value ? '0px' : $saved_value;
			case 'box_shadow_vertical':
				return 'none' === $current_value ? '1px' : $saved_value;
			case 'box_shadow_spread':
				return 'none' === $current_value ? '0px' : $saved_value;
			case 'box_shadow_color':
				return 'none' === $current_value ? 'rgba(0,0,0,.1)' : $saved_value;
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_DropShadowToBoxShadow();

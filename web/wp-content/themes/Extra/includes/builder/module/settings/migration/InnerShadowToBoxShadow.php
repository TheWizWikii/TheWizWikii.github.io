<?php

class ET_Builder_Module_Settings_Migration_InnerShadowToBoxShadow extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.99';

	public function get_modules( $group = '' ) {
		return array(
			'et_pb_slider',
			'et_pb_fullwidth_slider',
			'et_pb_post_slider',
			'et_pb_fullwidth_post_slider',
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
			'box_shadow_position'   => array(
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
					'show_inner_shadow' => $this->get_modules(),
				),
			),
		);
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {

		switch ( $field_name ) {
			case 'box_shadow_style':
				return ( 'none' !== $saved_value || 'off' === $current_value ) ? $saved_value : 'preset6';
			case 'box_shadow_blur':
				return 'none' === $current_value ? '10px' : $saved_value;
			case 'box_shadow_position':
				return 'none' === $current_value ? 'inner' : $saved_value;
			case 'box_shadow_color':
				return 'none' === $current_value ? 'rgba(0,0,0,0.1)' : $saved_value;
			case 'box_shadow_horizontal':
			case 'box_shadow_vertical':
			case 'box_shadow_spread':
				return 'none' === $current_value ? '0px' : $saved_value;
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_InnerShadowToBoxShadow();

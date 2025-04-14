<?php

class ET_Builder_Module_Settings_Migration_HoverOptions extends ET_Builder_Module_Settings_Migration {
	public $version = '3.16';

	public function get_modules( $group = '' ) {
		$button = array(
			'et_pb_button',
			'et_pb_comments',
			'et_pb_contact_field',
			'et_pb_cta',
			'et_pb_fullwidth_post_slider',
			'et_pb_fullwidth_slider',
			'et_pb_login',
			'et_pb_post_slider',
			'et_pb_pricing_tables',
			'et_pb_pricing_table',
			'et_pb_search',
			'et_pb_signup',
			'et_pb_slider',
			'et_pb_slide',
		);

		$buttons = array( 'et_pb_fullwidth_header' );
		switch ( $group ) {
			case 'button':
				return $button;
			case $buttons:
				return $buttons;
			default:
				return array_merge( $buttons, $button );
		}
	}

	public function get_fields() {
		$fields = array(
			'text_size',
			'text_color',
			'text_color',
			'border_width',
			'border_color',
			'border_radius',
			'letter_spacing',
			'bg_color',
		);

		$return = array();
		$hover  = et_pb_hover_options();

		foreach ( $fields as $field ) {
			$return[ $hover->get_hover_enabled_field( "button_{$field}" ) ]     = array(
				'affected_fields' => array(
					"button_{$field}_hover" => $this->get_modules( 'button' ),
				),
			);
			$return[ $hover->get_hover_field( "button_{$field}" ) ]             = array(
				'affected_fields' => array(
					"button_{$field}_hover" => $this->get_modules( 'button' ),
				),
			);
			$return[ $hover->get_hover_enabled_field( "button_one_{$field}" ) ] = array(
				'affected_fields' => array(
					"button_one_{$field}_hover" => $this->get_modules( 'buttons' ),
				),
			);
			$return[ $hover->get_hover_field( "button_one_{$field}" ) ]         = array(
				'affected_fields' => array(
					"button_one_{$field}_hover" => $this->get_modules( 'buttons' ),
				),
			);
			$return[ $hover->get_hover_enabled_field( "button_two_{$field}" ) ] = array(
				'affected_fields' => array(
					"button_two_{$field}_hover" => $this->get_modules( 'buttons' ),
				),
			);
			$return[ $hover->get_hover_field( "button_two_{$field}" ) ]         = array(
				'affected_fields' => array(
					"button_two_{$field}_hover" => $this->get_modules( 'buttons' ),
				),
			);
		}

		return $return;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if ( $field_name == et_pb_hover_options()->get_hover_field( $field_name ) ) {
			return strlen( $current_value ) ? $current_value : null;
		}

		return strlen( $current_value ) ? 'on' : 'off';
	}
}

return new ET_Builder_Module_Settings_Migration_HoverOptions();

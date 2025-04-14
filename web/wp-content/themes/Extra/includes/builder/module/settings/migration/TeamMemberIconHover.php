<?php

class ET_Builder_Module_Settings_Migration_TeamMemberIconHover extends ET_Builder_Module_Settings_Migration {

	public $version = '3.12.3';

	public function get_fields() {
		return array(
			et_pb_hover_options()->get_hover_field( 'icon_color' ) => array(
				'affected_fields' => array(
					'icon_hover_color' => $this->get_modules(),
				),
			),
			et_pb_hover_options()->get_hover_enabled_field( 'icon_color' ) => array(
				'affected_fields' => array(
					'icon_hover_color' => $this->get_modules(),
				),
			),
		);
	}

	public function get_modules() {
		$modules = array(
			'et_pb_team_member',
		);

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {

		if ( empty( $current_value ) ) {
			return $saved_value;
		}

		switch ( $field_name ) {
			case et_pb_hover_options()->get_hover_field( 'icon_color' ):
				return $current_value;
			case et_pb_hover_options()->get_hover_enabled_field( 'icon_color' ):
				return 'on';
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_TeamMemberIconHover();

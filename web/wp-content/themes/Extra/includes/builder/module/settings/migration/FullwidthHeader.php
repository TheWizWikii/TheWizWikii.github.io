<?php


class ET_Builder_Module_Settings_Migration_Fullwidth_Header extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.84';

	public function get_fields() {
		return array(
			'title_text_color'   => array(
				'affected_fields' => array(
					'title_font_color' => $this->get_modules(),
				),
			),
			'content_text_color' => array(
				'affected_fields' => array(
					'content_font_color' => $this->get_modules(),
				),
			),
			'subhead_text_color' => array(
				'affected_fields' => array(
					'subhead_font_color' => $this->get_modules(),
				),
			),
		);
	}

	public function get_modules() {
		$modules = array( 'et_pb_fullwidth_header' );

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if ( '' !== $current_value ) {
			return $current_value;
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_Fullwidth_Header();

<?php
class ET_Builder_Module_Settings_Migration_Fullwidth_Header2 extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.102';

	public function get_fields() {
		return array(
			'text_orientation' => array(
				'affected_fields' => array(
					'text_orientation' => $this->get_modules(),
				),
				'map'             => array(
					'justified' => 'center',
				),
			),
		);
	}

	public function get_modules() {
		$modules = array( 'et_pb_fullwidth_header' );

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Migrate Fullwidth Header's text orientation justified to center
		if ( 'et_pb_fullwidth_header' === $module_slug && 'text_orientation' === $field_name && isset( $this->fields['text_orientation']['map'][ $current_value ] ) ) {
			return $this->fields['text_orientation']['map'][ $current_value ];
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_Fullwidth_Header2();

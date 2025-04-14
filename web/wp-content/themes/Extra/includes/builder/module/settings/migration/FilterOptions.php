<?php


class ET_Builder_Module_Settings_Migration_FilterOptions extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.91';

	public function get_fields() {
		return array(
			'child_filter_saturate' => array(
				'affected_fields' => array(
					'grayscale_filter_amount' => $this->get_modules(),
				),
			),
		);
	}

	public function get_modules() {
		$modules = array(
			'et_pb_map',
			'et_pb_fullwidth_map',
		);

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Convert saved Google Maps JS API `stylers->saturation` values to CSS `filter: saturate()` format
		if ( 0 !== intval( $current_value ) ) {
			return 100 - intval( $current_value ) . '%';
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_FilterOptions();

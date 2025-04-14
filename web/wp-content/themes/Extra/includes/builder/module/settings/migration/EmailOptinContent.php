<?php


class ET_Builder_Module_Settings_Migration_EmailOptinContent extends ET_Builder_Module_Settings_Migration {

	public $version = '3.4';

	public function get_fields() {
		return array(
			'description' => array(
				'affected_fields' => array(
					'content' => array( 'et_pb_signup' ),
				),
			),
		);
	}

	public function get_modules( $for_affected_fields = false ) {
		return array( 'et_pb_signup' );
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if ( '' === $current_value && '' === $saved_value && '' !== $content ) {
			return $content;
		}

		if ( '' === $current_value && '' !== $saved_value ) {
			return $saved_value;
		}

		return $current_value;
	}
}

return new ET_Builder_Module_Settings_Migration_EmailOptinContent();

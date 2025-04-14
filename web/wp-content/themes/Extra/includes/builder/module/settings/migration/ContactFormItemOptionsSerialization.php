<?php
/**
 * Migration for the ContactFormItem module which fixes quote serialization for checkbox, radio and select field options.
 *
 * @since 3.10
 */
class ET_Builder_Module_Settings_Migration_ContactFormItemOptionsSerialization extends ET_Builder_Module_Settings_Migration {
	public function get_modules() {
		return array( 'et_pb_contact_field' );
	}

	public function get_fields() {
		return array(
			'checkbox_options' => array(
				'affected_fields' => array(
					'checkbox_options' => $this->get_modules(),
				),
			),
			'radio_options'    => array(
				'affected_fields' => array(
					'radio_options' => $this->get_modules(),
				),
			),
			'select_options'   => array(
				'affected_fields' => array(
					'select_options' => $this->get_modules(),
				),
			),
		);
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if ( json_decode( $saved_value ) ) {
			return $saved_value;
		}

		$pattern = '/
			\{
				("value":")(.*?)(",)
				(
					"checked":.,?
					.*?
				)
			\}
		/ix';

		$saved_value = preg_replace_callback( $pattern, array( $this, 'escape_quotes' ), $saved_value );

		return $saved_value;
	}

	public function escape_quotes( $matches ) {
		return '{' . $matches[1] . preg_replace( '/(?<!\\\\)"/', '\\"', $matches[2] ) . $matches[3] . $matches[4] . '}';
	}
}

return new ET_Builder_Module_Settings_Migration_ContactFormItemOptionsSerialization();

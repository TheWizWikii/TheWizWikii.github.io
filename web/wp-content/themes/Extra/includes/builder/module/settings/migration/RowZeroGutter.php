<?php

class ET_Builder_Module_Settings_Migration_RowZeroGutter extends ET_Builder_Module_Settings_Migration {

	public $version = '3.22.3';

	public function get_modules() {
		return array( 'et_pb_row', 'et_pb_section' );
	}

	public function get_fields() {
		$fields = array();

		foreach ( $this->get_modules() as $module ) {
			foreach ( $this->get_keys() as $key ) {
				$field            = $module === 'et_pb_section' ? "inner_$key" : $key;
				$fields[ $field ] = array(
					'affected_fields' => array(
						$field => array( $module ),
					),
				);
			}
		}

		return $fields;
	}

	public function migrate(
		$field_name,
		$current_value,
		$module_slug,
		$saved_value,
		$saved_field_name,
		$attrs,
		$content,
		$module_address
	) {
		$raw_field = str_replace( 'inner_', '', $field_name );
		$gutter    = et_builder_module_prop( 'gutter_width', $attrs, '' );
		$classes   = array_map( 'trim', explode( ' ', et_builder_module_prop( 'module_class', $attrs, '' ) ) );
		$is_fw     = in_array( 'et_pb_row_fullwidth', $classes ) || in_array( 'et_pb_specialty_fullwidth', $classes );

		if ( '0' === $gutter && $is_fw ) {
			switch ( $raw_field ) {
				case 'width_tablet':
				case 'max_width_tablet':
					return '80%' === $saved_value ? '100%' : $saved_value;
				case 'width':
				case 'max_width':
					return '89%' === $saved_value ? '100%' : $saved_value;
			}
		}

		return $saved_value;
	}

	protected function get_keys() {
		return array_merge( $this->to_fields( 'width' ), $this->to_fields( 'max_width' ) );
	}

	protected function to_fields( $field ) {
		return array(
			$field,
			"{$field}_tablet",
			"{$field}_phone",
		);
	}
}

return new ET_Builder_Module_Settings_Migration_RowZeroGutter();

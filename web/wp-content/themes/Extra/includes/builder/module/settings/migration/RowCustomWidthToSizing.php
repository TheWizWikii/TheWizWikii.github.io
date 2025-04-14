<?php

class ET_Builder_Module_Settings_Migration_RowCustomWidthToSizing extends ET_Builder_Module_Settings_Migration {

	public $version = '3.22';

	public function get_modules() {
		return array( 'et_pb_row', 'et_pb_section' );
	}

	public function get_fields() {
		$fields = array(
			'module_class' => array(
				'affected_fields' => array(
					'make_fullwidth' => $this->get_modules(),
				),
			),
		);

		foreach ( $this->get_modules() as $module ) {
			foreach ( $this->get_keys() as $key ) {
				$field            = $module === 'et_pb_section' ? "inner_$key" : $key;
				$fields[ $field ] = array(
					'affected_fields' => array(
						'make_fullwidth'                 => array( $module ),
						'use_custom_width'               => array( $module ),
						'width_unit'                     => array( $module ),
						'custom_width_px'                => array( $module ),
						'custom_width_px__hover'         => array( $module ),
						'custom_width_px__hover_enabled' => array( $module ),
						'custom_width_percent'           => array( $module ),
						'custom_width_percent__hover'    => array( $module ),
						'custom_width_percent__hover_enabled' => array( $module ),
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

		if ( et_builder_module_prop( 'use_custom_width', $attrs, '' ) === 'on' ) {
			$percent = et_builder_module_prop( 'width_unit', $attrs, '' ) === 'off';
			$field   = $percent ? 'custom_width_percent' : 'custom_width_px';

			switch ( $raw_field ) {
				// If the field is set to % only the max-width is defined
				case 'width':
					return ! $percent ? $saved_value : et_builder_module_prop( $field, $attrs, '' );
				case 'width__hover':
					return ! $percent ? $saved_value : et_builder_module_prop( "{$field}__hover", $attrs, '' );
				case 'width__hover_enabled':
					return ! $percent ? $saved_value : et_builder_module_prop( "{$field}__hover_enabled", $attrs, '' );

				case 'max_width':
					return et_builder_module_prop( $field, $attrs, $percent ? '80%' : '' );
				case 'max_width__hover':
					return et_builder_module_prop( "{$field}__hover", $attrs, '' );
				case 'max_width__hover_enabled':
					return et_builder_module_prop( "{$field}__hover_enabled", $attrs, '' );
				default:
					return $saved_value;
			}
		}

		if ( et_builder_module_prop( 'make_fullwidth', $attrs, '' ) === 'on' ) {
			$gutter = (int) et_builder_module_prop( 'gutter_width', $attrs, '' );

			switch ( $raw_field ) {
				case 'module_class':
					return $saved_value . ' ' . $this->class_name( $module_slug );
				case 'width_last_edited':
				case 'max_width_last_edited':
					return 'on|desktop';
				case 'width_tablet':
				case 'max_width_tablet':
					return 1 === $gutter ? '100%' : '80%';
				case 'width':
				case 'max_width':
					return $this->get_width( $gutter );
			}

			if ( et_builder_module_prop( 'gutter_width__hover_enabled', $attrs, '' ) === 'on' ) {
				$gutter = (int) et_builder_module_prop( 'gutter_width__hover', $attrs, '' );

				switch ( $raw_field ) {
					case 'width__hover_enabled':
					case 'max_width__hover_enabled':
						return 'on';
					case 'width__hover':
					case 'max_width__hover':
						return $this->get_width( $gutter );
				}
			}
		}

		return $saved_value;
	}

	private function get_keys() {
		return array_merge( $this->to_fields( 'width' ), $this->to_fields( 'max_width' ) );
	}

	private function to_fields( $field ) {
		return array(
			$field,
			"{$field}_tablet",
			"{$field}_phone",
			"{$field}_last_edited",
			"{$field}__hover",
			"{$field}__hover_enabled",
		);
	}

	private function get_width( $gutter ) {
		switch ( $gutter ) {
			case 1:
				return '100%';
			case 2:
				return '94%';
			case 3:
				return '89%';
			case 4:
				return '86%';
			default:
				return '89%';
		}
	}

	private function class_name( $module ) {
		switch ( $module ) {
			case 'et_pb_row':
				return 'et_pb_row_fullwidth';
			case 'et_pb_section':
				return 'et_pb_specialty_fullwidth';
			default:
				return '';
		}
	}
}

return new ET_Builder_Module_Settings_Migration_RowCustomWidthToSizing();

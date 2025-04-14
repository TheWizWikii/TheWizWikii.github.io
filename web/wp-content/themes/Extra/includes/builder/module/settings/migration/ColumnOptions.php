<?php

class ET_Builder_Module_Settings_Migration_ColumnOptions extends ET_Builder_Module_Settings_Migration {

	public $version = '3.25';

	public $add_missing_fields = true;

	public $columnSettingsFromRow = array();

	public $fieldsWithSuffix = array(
		'padding'           => array( 'tablet', 'phone', 'last_edited', '_hover', '_hover_enabled' ),
		'padding_top'       => array( '_hover', '_hover_enabled' ),
		'padding_right'     => array( '_hover', '_hover_enabled' ),
		'padding_bottom'    => array( '_hover', '_hover_enabled' ),
		'padding_left'      => array( '_hover', '_hover_enabled' ),
		'background_color'  => array( '_hover', '_hover_enabled' ),
		'custom_css_before' => array( '_hover', '_hover_enabled' ),
		'custom_css_main'   => array( '_hover', '_hover_enabled' ),
		'custom_css_after'  => array( '_hover', '_hover_enabled' ),
	);

	public $fieldsWithSuffixAppended = array(
		'custom_padding'                         => '__no_suffix__',
		'custom_padding_tablet'                  => 'custom_padding',
		'custom_padding_phone'                   => 'custom_padding',
		'custom_padding_last_edited'             => 'custom_padding',
		'custom_padding__hover'                  => '__no_suffix__',
		'custom_padding__hover_enabled'          => 'custom_padding',
		'background_color__hover'                => 'background_color',
		'background_color__hover_enabled'        => 'background_color',
		'custom_css_before'                      => '__no_suffix__',
		'custom_css_main_element'                => '__no_suffix__',
		'custom_css_after'                       => '__no_suffix__',
		'custom_css_before__hover'               => 'custom_css_before',
		'custom_css_main_element__hover'         => 'custom_css_main_element',
		'custom_css_after__hover'                => 'custom_css_after',
		'custom_css_before__hover_enabled'       => 'custom_css_before',
		'custom_css_main_element__hover_enabled' => 'custom_css_main_element',
		'custom_css_after__hover_enabled'        => 'custom_css_after',
	);

	public function get_modules() {
		return array( 'et_pb_row', 'et_pb_column', 'et_pb_row_inner', 'et_pb_column_inner' );
	}

	public function get_fields() {
		$fields            = array();
		$fields_to_migrate = array(
			'module_id',
			'module_class',
			'background_color',
			'bg_img',
			'background_size',
			'background_position',
			'background_repeat',
			'background_blend',
			'padding_top',
			'padding_right',
			'padding_bottom',
			'padding_left',
			'padding',
			'parallax',
			'parallax_method',
			'custom_css_before',
			'custom_css_main',
			'custom_css_after',
			'use_background_color_gradient',
			'background_color_gradient_stops',
			'background_color_gradient_type',
			'background_color_gradient_direction',
			'background_color_gradient_direction_radial',
			'background_color_gradient_overlays_image',
			'background_color_gradient_start',
			'background_color_gradient_end',
			'background_color_gradient_start_position',
			'background_color_gradient_end_position',
			'background_video_mp4',
			'background_video_webm',
			'background_video_width',
			'background_video_height',
			'allow_player_pause',
			'background_video_pause_outside_viewport',
		);

		foreach ( $this->get_modules() as $module ) {
			foreach ( $fields_to_migrate as $field_name_raw ) {
				$field_name = $field_name_raw;

				if ( in_array( $module, array( 'et_pb_row', 'et_pb_row_inner' ) ) ) {
					$max_columns_number = 'et_pb_row_inner' === $module ? 4 : 6;
					for ( $i = 1; $i <= $max_columns_number; $i++ ) {
						if ( array_key_exists( $field_name_raw, $this->fieldsWithSuffix ) ) {
							foreach ( $this->fieldsWithSuffix[ $field_name_raw ] as $suffix ) {
								$fields[ "{$field_name}_{$i}_$suffix" ] = array(
									'affected_fields' => array(
										"{$field_name}_{$i}_$suffix" => array( 'et_pb_row', 'et_pb_row_inner' ),
									),
								);
							}
						}

						$fields[ "{$field_name}_{$i}" ] = array(
							'affected_fields' => array(
								"{$field_name}_{$i}" => array( 'et_pb_row', 'et_pb_row_inner' ),
							),
						);
					}
				}

				if ( in_array( $module, array( 'et_pb_column', 'et_pb_column_inner' ) ) ) {
					if ( in_array( $field_name, array( 'padding_top', 'padding_right', 'padding_bottom', 'padding_left' ) ) ) {
						continue;
					}

					switch ( $field_name ) {
						case 'bg_img':
							$field_name = 'background_image';
							break;
						case 'padding':
							$field_name = 'custom_padding';
							break;
						case 'custom_css_main':
							$field_name = 'custom_css_main_element';
							break;
					}

					if ( array_key_exists( $field_name_raw, $this->fieldsWithSuffix ) ) {
						foreach ( $this->fieldsWithSuffix[ $field_name_raw ] as $suffix ) {
							$fields[ "{$field_name}_{$suffix}" ] = array(
								'affected_fields' => array(
									"{$field_name}_{$suffix}" => array( 'et_pb_column', 'et_pb_column_inner' ),
								),
							);
						}
					}

					$fields[ $field_name ] = array(
						'affected_fields' => array(
							$field_name => array( 'et_pb_column', 'et_pb_column_inner' ),
						),
					);
				}
			}
		}

		return $fields;
	}

	public function migrate_padding( $row_address, $column_index, $field_name, $saved_value ) {
		$padding_sides    = array( 'padding_top', 'padding_right', 'padding_bottom', 'padding_left' );
		$padding_combined = array();
		$suffix           = str_replace( 'padding', '', $field_name );

		// If padding was migrated already, no need to process it again.
		if ( ! empty( $saved_value ) ) {
			return $saved_value;
		}

		// phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase, ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Existing codebase.
		foreach ( $padding_sides as $side ) {
			if ( isset( $this->columnSettingsFromRow[ $row_address ], $this->columnSettingsFromRow[ $row_address ][ "{$side}_{$column_index}{$suffix}" ] ) ) {
				$padding_combined[] = $this->columnSettingsFromRow[ $row_address ][ "{$side}_{$column_index}{$suffix}" ];
			} else {
				$padding_combined[] = '';
			}
		}
		// phpcs:enable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase, ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Existing codebase.

		return implode( '|', $padding_combined );
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if ( in_array( $module_slug, array( 'et_pb_row', 'et_pb_row_inner' ) ) ) {
			$this->columnSettingsFromRow[ $module_address ][ $field_name ] = $saved_value;

			return '';
		}

		if ( in_array( $module_slug, array( 'et_pb_column', 'et_pb_column_inner' ) ) ) {
			$row_level     = 'et_pb_column_inner' === $module_slug ? 3 : 2;
			$address_array = explode( '.', $module_address );
			$parent_row    = implode( '.', array_slice( $address_array, 0, $row_level ) );
			$column_index  = (int) implode( '', array_slice( $address_array, $row_level, 1 ) ) + 1;

			if ( in_array( $field_name, array_keys( $this->fieldsWithSuffixAppended ) ) ) {
				$field_name_without_suffix = $this->fieldsWithSuffixAppended[ $field_name ];
				$field_name_replacement    = $field_name_without_suffix;

				if ( in_array( $field_name, array( 'custom_padding', 'custom_padding__hover' ) ) ) {
					$field_name = str_replace( 'custom_', '', $field_name );
				}

				if ( 'custom_css_main_element' === $field_name ) {
					$field_name = 'custom_css_main';
				}

				if ( in_array( $field_name, array( 'custom_padding_phone', 'custom_padding_tablet', 'custom_padding_last_edited', 'custom_padding__hover', 'custom_padding__hover_enabled' ) ) ) {
					$field_name_replacement = 'padding';
				}

				if ( in_array( $field_name, array( 'custom_css_main_element__hover', 'custom_css_main_element__hover_enabled' ) ) ) {
					$field_name_replacement = 'custom_css_main';
				}

				// Insert the column index in the middle of field name right before suffix.
				$row_field_name = '__no_suffix__' === $field_name_without_suffix ? "{$field_name}_{$column_index}" : str_replace( $field_name_without_suffix, "{$field_name_replacement}_{$column_index}", $field_name );
			} else {
				$row_field_name = 'background_image' === $field_name ? "bg_img_{$column_index}" : "{$field_name}_{$column_index}";
			}

			if ( in_array( $field_name, array( 'padding', 'padding__hover' ) ) ) {
				return $this->migrate_padding( $parent_row, $column_index, $field_name, $saved_value );
			}

			if ( isset( $this->columnSettingsFromRow[ $parent_row ], $this->columnSettingsFromRow[ $parent_row ][ $row_field_name ] ) && ! empty( $this->columnSettingsFromRow[ $parent_row ][ $row_field_name ] ) ) {
				return $this->columnSettingsFromRow[ $parent_row ][ $row_field_name ];
			}
		}
		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_ColumnOptions();

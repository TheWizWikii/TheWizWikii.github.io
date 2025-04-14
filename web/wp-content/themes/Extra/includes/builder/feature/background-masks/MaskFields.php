<?php
/**
 * Functions needed for the Background Pattern Fields.
 *
 * @since 4.15.0
 *
 * @package Divi
 * @subpackage Builder
 */

/**
 * Mask Fields Language Strings.
 *
 * @since 4.15.0
 *
 * @return array
 */
function et_pb_mask_i18n() {
	static $_i18n = null;

	if ( is_null( $_i18n ) ) {
		$_i18n = array(
			'mask_style'             => array(
				'label'        => esc_html__( 'Mask Style', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Style', 'et_builder' ),
			),
			'mask_color'             => array(
				'label'        => esc_html__( 'Mask Color', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Color', 'et_builder' ),
			),
			'mask_transform'         => array(
				'label'        => esc_html__( 'Mask Transform', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Transform', 'et_builder' ),
				'options'      => array(
					'flip_horizontal'  => esc_html__( 'Flip Horizontal', 'et_builder' ),
					'flip_vertical'    => esc_html__( 'Flip Vertical', 'et_builder' ),
					'rotate_90_degree' => esc_html__( 'Rotate 90 Degree', 'et_builder' ),
					'invert'           => esc_html__( 'Invert', 'et_builder' ),
				),
			),
			'mask_aspect_ratio'      => array(
				'label'        => esc_html__( 'Mask Aspect Ratio', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Aspect Ratio', 'et_builder' ),
				'options'      => array(
					'landscape' => esc_html__( 'Landscape', 'et_builder' ),
					'square'    => esc_html__( 'Square', 'et_builder' ),
					'portrait'  => esc_html__( 'Portrait', 'et_builder' ),
				),
			),
			'mask_size'              => array(
				'label'        => esc_html__( 'Mask Size', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Size', 'et_builder' ),
				'options'      => array(
					'stretch' => et_builder_i18n( 'Stretch to Fill' ),
					'cover'   => et_builder_i18n( 'Cover' ),
					'contain' => et_builder_i18n( 'Fit' ),
					'custom'  => et_builder_i18n( 'Custom Size' ),
				),
			),
			'mask_width'             => array(
				'label'        => esc_html__( 'Mask Width', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Width', 'et_builder' ),
			),
			'mask_height'            => array(
				'label'        => esc_html__( 'Mask Height', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Height', 'et_builder' ),
			),
			'mask_position'          => array(
				'label'        => esc_html__( 'Mask Position', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Position', 'et_builder' ),
			),
			'mask_horizontal_offset' => array(
				'label'        => esc_html__( 'Mask Horizontal Offset', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Horizontal Offset', 'et_builder' ),
			),
			'mask_vertical_offset'   => array(
				'label'        => esc_html__( 'Mask Vertical Offset', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Vertical Offset', 'et_builder' ),
			),
			'mask_blend_mode'        => array(
				'label'        => esc_html__( 'Mask Blend Mode', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Mask Blend Mode', 'et_builder' ),
			),
		);
	}

	return $_i18n;
}

/**
 * Mask Field Templates.
 *
 * @since 4.15.0
 *
 * @return array[]
 */
function et_ph_mask_field_templates() {
	return array(
		'mask_style'             => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'tab_filler'       => true,
			'type'             => 'select-mask',
		),
		'mask_color'             => array(
			'custom_color'     => true,
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'color-alpha',
		),
		'mask_transform'         => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'multi_selection'  => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'toggleable'       => true,
			'type'             => 'multiple_buttons',
		),
		'mask_aspect_ratio'      => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'multi_selection'  => false,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'toggleable'       => true,
			'type'             => 'multiple_buttons',
		),
		'mask_size'              => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'mask_width'             => array(
			'allow_empty'      => true,
			'default_on_child' => true,
			'default_unit'     => '%',
			'fixed_range'      => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'range_settings'   => array(
				'min'       => 0,
				'min_limit' => 0,
				'max'       => 100,
				'step'      => 1,
			),
			'type'             => 'range',
			'validate_unit'    => true,
		),
		'mask_height'            => array(
			'allow_empty'      => true,
			'default_on_child' => true,
			'default_unit'     => '%',
			'fixed_range'      => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'range_settings'   => array(
				'min'       => 0,
				'min_limit' => 0,
				'max'       => 100,
				'step'      => 1,
			),
			'type'             => 'range',
			'validate_unit'    => true,
		),
		'mask_position'          => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'mask_horizontal_offset' => array(
			'default_on_child' => true,
			'default_unit'     => '%',
			'fixed_range'      => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'range_settings'   => array(
				'min'  => - 100,
				'max'  => 100,
				'step' => 1,
			),
			'type'             => 'range',
			'validate_unit'    => true,
		),
		'mask_vertical_offset'   => array(
			'default_on_child' => true,
			'default_unit'     => '%',
			'fixed_range'      => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'range_settings'   => array(
				'min'  => - 100,
				'max'  => 100,
				'step' => 1,
			),
			'type'             => 'range',
			'validate_unit'    => true,
		),
		'mask_blend_mode'        => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'enable_mask_style'      => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'skip',
		),
	);
}

/**
 * Generates Background Mask fields.
 *
 * @since 4.15.0
 *
 * @param string $base_name background base name.
 * @param bool   $specialty whether return field for specialty section column.
 *
 * @return array
 */
function et_pb_get_mask_fields( $base_name, $specialty = false ) {
	static $_cache = null;

	$suffix     = $specialty ? '_%s' : '';
	$field_type = "{$base_name}{$suffix}";

	if ( ! isset( $_cache[ $field_type ] ) ) {
		$i18n    = et_pb_mask_i18n();
		$label   = $specialty ? 'column_label' : 'label';
		$options = array();

		$options[ "{$base_name}_enable_mask_style{$suffix}" ] = ET_Builder_Element::background_field_template(
			'enable_mask_style',
			array(
				'default' => 'off',
			)
		);

		$options[ "{$base_name}_mask_style{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_style',
			array(
				'default'   => et_pb_background_mask_options()->get_default_style_name(),
				'label'     => $i18n['mask_style'][ $label ],
				'options'   => et_pb_get_mask_style_options(),
				'copy_with' => array(
					"{$base_name}_enable_mask_style{$suffix}",
				),
			)
		);

		$options[ "{$base_name}_mask_color{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_color',
			array(
				'default'     => '#ffffff',
				'label'       => $i18n['mask_color'][ $label ],
				'show_if_not' => array(
					"{$base_name}_enable_mask_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_mask_transform{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_transform',
			array(
				'default'     => '',
				'label'       => $i18n['mask_transform'][ $label ],
				'options'     => array(
					'flip_horizontal'  => array(
						'title' => $i18n['mask_transform']['options']['flip_horizontal'],
						'icon'  => 'flip-horizontally',
					),
					'flip_vertical'    => array(
						'title' => $i18n['mask_transform']['options']['flip_vertical'],
						'icon'  => 'flip-vertically',
					),
					'rotate_90_degree' => array(
						'title' => $i18n['mask_transform']['options']['rotate_90_degree'],
						'icon'  => 'rotate-90-degree',
					),
					'invert'           => array(
						'title' => $i18n['mask_transform']['options']['invert'],
						'icon'  => 'invert',
					),
				),
				'show_if_not' => array(
					"{$base_name}_enable_mask_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_mask_aspect_ratio{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_aspect_ratio',
			array(
				'default'     => 'landscape',
				'label'       => $i18n['mask_aspect_ratio'][ $label ],
				'options'     => array(
					'landscape' => array(
						'title' => $i18n['mask_aspect_ratio']['options']['landscape'],
						'icon'  => 'aspect-ratio-landscape',
					),
					'square'    => array(
						'title' => $i18n['mask_aspect_ratio']['options']['square'],
						'icon'  => 'aspect-ratio-square',
					),
					'portrait'  => array(
						'title' => $i18n['mask_aspect_ratio']['options']['portrait'],
						'icon'  => 'aspect-ratio-portrait',
					),
				),
				'show_if_not' => array(
					"{$base_name}_enable_mask_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_mask_size{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_size',
			array(
				'default'     => 'stretch',
				'label'       => $i18n['mask_size'][ $label ],
				'options'     => array(
					'stretch' => $i18n['mask_size']['options']['stretch'],
					'cover'   => $i18n['mask_size']['options']['cover'],
					'contain' => $i18n['mask_size']['options']['contain'],
					'custom'  => $i18n['mask_size']['options']['custom'],
				),
				'show_if_not' => array(
					"{$base_name}_enable_mask_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_mask_width{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_width',
			array(
				'allowed_units'  => et_pb_get_background_field_allowed_units(),
				'allowed_values' => et_builder_get_acceptable_css_string_values( 'background-size' ),
				'default'        => 'auto',
				'label'          => $i18n['mask_width'][ $label ],
				'show_if'        => array(
					"{$base_name}_mask_size{$suffix}" => 'custom',
				),
			)
		);

		$options[ "{$base_name}_mask_height{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_height',
			array(
				'allowed_units'  => et_pb_get_background_field_allowed_units(),
				'allowed_values' => et_builder_get_acceptable_css_string_values( 'background-size' ),
				'default'        => 'auto',
				'label'          => $i18n['mask_height'][ $label ],
				'show_if'        => array(
					"{$base_name}_mask_size{$suffix}" => 'custom',
				),
			)
		);

		$options[ "{$base_name}_mask_position{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_position',
			array(
				'default' => 'center',
				'label'   => $i18n['mask_position'][ $label ],
				'options' => et_pb_get_background_position_options(),
				'show_if' => array(
					"{$base_name}_mask_size{$suffix}" => array(
						'cover',
						'contain',
						'custom',
					),
				),
			)
		);

		$options[ "{$base_name}_mask_horizontal_offset{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_horizontal_offset',
			array(
				'allowed_units' => et_pb_get_background_field_allowed_units(),
				'default'       => '0',
				'label'         => $i18n['mask_horizontal_offset'][ $label ],
				'show_if'       => array(
					"{$base_name}_mask_position{$suffix}" => array(
						'top_left',
						'top_right',
						'center_left',
						'center_right',
						'bottom_left',
						'bottom_right',
					),
				),
			)
		);

		$options[ "{$base_name}_mask_vertical_offset{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_vertical_offset',
			array(
				'allowed_units' => et_pb_get_background_field_allowed_units(),
				'default'       => '0',
				'label'         => $i18n['mask_vertical_offset'][ $label ],
				'show_if'       => array(
					"{$base_name}_mask_position{$suffix}" => array(
						'top_left',
						'top_center',
						'top_right',
						'bottom_left',
						'bottom_center',
						'bottom_right',
					),
				),
				'show_if_not'   => array(
					"{$base_name}_mask_size{$suffix}" => 'contain',
				),
			)
		);

		$options[ "{$base_name}_mask_blend_mode{$suffix}" ] = ET_Builder_Element::background_field_template(
			'mask_blend_mode',
			array(
				'default'     => 'normal',
				'label'       => $i18n['mask_blend_mode'][ $label ],
				'options'     => et_pb_get_background_blend_mode_options(),
				'show_if_not' => array(
					"{$base_name}_enable_mask_style{$suffix}" => 'off',
				),
			)
		);

		if ( $specialty ) {
			foreach ( array_keys( $options ) as $field ) {
				$options[ $field ]['sub_toggle'] = 'column_%s';
			}
		}

		$_cache[ $field_type ] = $options;
	}

	return isset( $_cache[ $field_type ] ) ? $_cache[ $field_type ] : array();
}

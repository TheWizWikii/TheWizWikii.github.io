<?php
/**
 * Functions needed for the Background Pattern Fields.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.15.0
 */

/**
 * Pattern Fields Language Strings.
 *
 * @since 4.15.0
 *
 * @return array
 */
function et_pb_pattern_i18n() {
	static $_i18n = null;

	if ( is_null( $_i18n ) ) {
		$_i18n = array(
			'pattern_style'             => array(
				'label'        => esc_html__( 'Pattern Style', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Style', 'et_builder' ),
			),
			'pattern_color'             => array(
				'label'        => esc_html__( 'Pattern Color', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Color', 'et_builder' ),
			),
			'pattern_transform'         => array(
				'label'        => esc_html__( 'Pattern Transform', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Transform', 'et_builder' ),
				'options'      => array(
					'flip_horizontal'  => esc_html__( 'Flip Horizontal', 'et_builder' ),
					'flip_vertical'    => esc_html__( 'Flip Vertical', 'et_builder' ),
					'rotate_90_degree' => esc_html__( 'Rotate 90 degree', 'et_builder' ),
					'invert'           => esc_html__( 'Invert', 'et_builder' ),
				),
			),
			'pattern_size'              => array(
				'label'        => esc_html__( 'Pattern Size', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Size', 'et_builder' ),
				'options'      => array(
					'initial' => et_builder_i18n( 'Actual Size' ),
					'cover'   => et_builder_i18n( 'Cover' ),
					'contain' => et_builder_i18n( 'Fit' ),
					'stretch' => et_builder_i18n( 'Stretch to Fill' ),
					'custom'  => et_builder_i18n( 'Custom Size' ),
				),
			),
			'pattern_width'             => array(
				'label'        => esc_html__( 'Pattern Width', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Width', 'et_builder' ),
			),
			'pattern_height'            => array(
				'label'        => esc_html__( 'Pattern Height', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Height', 'et_builder' ),
			),
			'pattern_repeat_origin'     => array(
				'label'        => esc_html__( 'Pattern Repeat Origin', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Repeat Origin', 'et_builder' ),
			),
			'pattern_horizontal_offset' => array(
				'label'        => esc_html__( 'Pattern Horizontal Offset', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Horizontal Offset', 'et_builder' ),
			),
			'pattern_vertical_offset'   => array(
				'label'        => esc_html__( 'Pattern Vertical Offset', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Vertical Offset', 'et_builder' ),
			),
			'pattern_repeat'            => array(
				'label'        => esc_html__( 'Pattern Repeat', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Repeat', 'et_builder' ),
			),
			'pattern_blend_mode'        => array(
				'label'        => esc_html__( 'Pattern Blend Mode', 'et_builder' ),
				'column_label' => esc_html__( 'Column %s Background Pattern Blend Mode', 'et_builder' ),
			),
		);
	}

	return $_i18n;
}

/**
 * Pattern Field Templates.
 *
 * @since 4.15.0
 *
 * @return array[]
 */
function et_ph_pattern_field_templates() {
	return array(
		'pattern_style'             => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'tab_filler'       => true,
			'type'             => 'select-pattern',
		),
		'pattern_color'             => array(
			'custom_color'     => true,
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'color-alpha',
		),
		'pattern_transform'         => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'multi_selection'  => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'toggleable'       => true,
			'type'             => 'multiple_buttons',
		),
		'pattern_size'              => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'pattern_width'             => array(
			'allow_empty'      => true,
			'default_on_child' => true,
			'default_unit'     => 'px',
			'fixed_range'      => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'range_settings'   => array(
				'min'       => 0,
				'min_limit' => 0,
				'max'       => 1000,
				'step'      => 1,
			),
			'type'             => 'range',
			'validate_unit'    => true,
		),
		'pattern_height'            => array(
			'allow_empty'      => true,
			'default_on_child' => true,
			'default_unit'     => 'px',
			'fixed_range'      => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'range_settings'   => array(
				'min'       => 0,
				'min_limit' => 0,
				'max'       => 1000,
				'step'      => 1,
			),
			'type'             => 'range',
			'validate_unit'    => true,
		),
		'pattern_repeat_origin'     => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'pattern_horizontal_offset' => array(
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
		'pattern_vertical_offset'   => array(
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
		'pattern_repeat'            => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'pattern_blend_mode'        => array(
			'default_on_child' => true,
			'hover'            => 'tabs',
			'mobile_options'   => true,
			'option_category'  => 'configuration',
			'sticky'           => true,
			'type'             => 'select',
		),
		'enable_pattern_style'      => array(
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
 * Generates Background Pattern fields.
 *
 * @since 4.15.0
 *
 * @param string $base_name background base name.
 * @param bool   $specialty whether return field for specialty section column.
 *
 * @return array
 */
function et_pb_get_pattern_fields( $base_name, $specialty = false ) {
	static $_cache = null;

	$suffix     = $specialty ? '_%s' : '';
	$field_type = "{$base_name}{$suffix}";

	if ( ! isset( $_cache[ $field_type ] ) ) {
		$i18n    = et_pb_pattern_i18n();
		$label   = $specialty ? 'column_label' : 'label';
		$options = array();

		$options[ "{$base_name}_enable_pattern_style{$suffix}" ] = ET_Builder_Element::background_field_template(
			'enable_pattern_style',
			array(
				'default' => 'off',
			)
		);

		$options[ "{$base_name}_pattern_style{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_style',
			array(
				'default'   => et_pb_background_pattern_options()->get_default_style_name(),
				'label'     => $i18n['pattern_style'][ $label ],
				'options'   => et_pb_get_pattern_style_options(),
				'copy_with' => array(
					"{$base_name}_enable_pattern_style{$suffix}",
				),
			)
		);

		$options[ "{$base_name}_pattern_color{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_color',
			array(
				'default'     => 'rgba(0,0,0,0.2)',
				'label'       => $i18n['pattern_color'][ $label ],
				'show_if_not' => array(
					"{$base_name}_enable_pattern_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_pattern_transform{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_transform',
			array(
				'default'     => '',
				'label'       => $i18n['pattern_transform'][ $label ],
				'options'     => array(
					'flip_horizontal'  => array(
						'title' => $i18n['pattern_transform']['options']['flip_horizontal'],
						'icon'  => 'flip-horizontally',
					),
					'flip_vertical'    => array(
						'title' => $i18n['pattern_transform']['options']['flip_vertical'],
						'icon'  => 'flip-vertically',
					),
					'rotate_90_degree' => array(
						'title' => $i18n['pattern_transform']['options']['rotate_90_degree'],
						'icon'  => 'rotate-90-degree',
					),
					'invert'           => array(
						'title' => $i18n['pattern_transform']['options']['invert'],
						'icon'  => 'invert',
					),
				),
				'show_if_not' => array(
					"{$base_name}_enable_pattern_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_pattern_size{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_size',
			array(
				'default'     => 'initial',
				'label'       => $i18n['pattern_size'][ $label ],
				'options'     => array(
					'initial' => $i18n['pattern_size']['options']['initial'],
					'cover'   => $i18n['pattern_size']['options']['cover'],
					'contain' => $i18n['pattern_size']['options']['contain'],
					'stretch' => $i18n['pattern_size']['options']['stretch'],
					'custom'  => $i18n['pattern_size']['options']['custom'],
				),
				'show_if_not' => array(
					"{$base_name}_enable_pattern_style{$suffix}" => 'off',
				),
			)
		);

		$options[ "{$base_name}_pattern_width" ] = ET_Builder_Element::background_field_template(
			'pattern_width',
			array(
				'allowed_units'  => et_pb_get_background_field_allowed_units(),
				'allowed_values' => et_builder_get_acceptable_css_string_values( 'background-size' ),
				'default'        => 'auto',
				'label'          => $i18n['pattern_width'][ $label ],
				'show_if'        => array(
					"{$base_name}_pattern_size{$suffix}" => 'custom',
				),
			)
		);

		$options[ "{$base_name}_pattern_height{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_height',
			array(
				'allowed_units'  => et_pb_get_background_field_allowed_units(),
				'allowed_values' => et_builder_get_acceptable_css_string_values( 'background-size' ),
				'default'        => 'auto',
				'label'          => $i18n['pattern_height'][ $label ],
				'show_if'        => array(
					"{$base_name}_pattern_size{$suffix}" => 'custom',
				),
			)
		);

		$options[ "{$base_name}_pattern_repeat_origin{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_repeat_origin',
			array(
				'default' => 'top_left',
				'label'   => $i18n['pattern_repeat_origin'][ $label ],
				'options' => et_pb_get_background_position_options(),
				'show_if' => array(
					"{$base_name}_pattern_repeat{$suffix}" => array(
						'repeat',
						'repeat-x',
						'repeat-y',
						'round',
					),
				),
			)
		);

		$options[ "{$base_name}_pattern_horizontal_offset{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_horizontal_offset',
			array(
				'allowed_units' => et_pb_get_background_field_allowed_units(),
				'default'       => '0',
				'label'         => $i18n['pattern_horizontal_offset'][ $label ],
				'show_if'       => array(
					"{$base_name}_pattern_repeat_origin{$suffix}" => array(
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

		$options[ "{$base_name}_pattern_vertical_offset{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_vertical_offset',
			array(
				'allowed_units' => et_pb_get_background_field_allowed_units(),
				'default'       => '0',
				'label'         => $i18n['pattern_vertical_offset'][ $label ],
				'show_if'       => array(
					"{$base_name}_pattern_repeat_origin{$suffix}" => array(
						'top_left',
						'top_center',
						'top_right',
						'bottom_left',
						'bottom_center',
						'bottom_right',
					),
				),
				'show_if_not'   => array(
					"{$base_name}_pattern_size{$suffix}" => 'contain',
				),
			)
		);

		$options[ "{$base_name}_pattern_repeat{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_repeat',
			array(
				'default' => 'repeat',
				'label'   => $i18n['pattern_repeat'][ $label ],
				'options' => et_pb_get_background_repeat_options( false ),
				'show_if' => array(
					"{$base_name}_pattern_size{$suffix}" => array(
						'initial',
						'cover',
						'contain',
						'custom',
					),
				),
			)
		);

		$options[ "{$base_name}_pattern_blend_mode{$suffix}" ] = ET_Builder_Element::background_field_template(
			'pattern_blend_mode',
			array(
				'default'     => 'normal',
				'label'       => $i18n['pattern_blend_mode'][ $label ],
				'options'     => et_pb_get_background_blend_mode_options(),
				'show_if_not' => array(
					"{$base_name}_enable_pattern_style{$suffix}" => 'off',
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

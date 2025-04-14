<?php

class ET_Builder_Module_Field_Transform extends ET_Builder_Module_Field_Base {

	private $processing_props = array();

	public $defaults = array(
		'scale'     => '100%',
		'translate' => '0px',
		'rotate'    => '0deg',
		'skew'      => '0deg',
		'origin'    => '50%',
	);

	public $allTransforms = array(
		'scaleX',
		'scaleY',
		'translateX',
		'translateY',
		'rotateX',
		'rotateY',
		'rotateZ',
		'skewX',
		'skewY',
		'originX',
		'originY',
	);

	public function get_fields( array $args = array() ) {
		static $i18n;

		if ( ! isset( $i18n ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n = array(
				'scale'     => array(
					'label' => esc_html__( 'Transform Scale', 'et_builder' ),
				),
				'translate' => array(
					'label' => esc_html__( 'Transform Translate', 'et_builder' ),
				),
				'rotate'    => array(
					'label' => esc_html__( 'Transform Rotate', 'et_builder' ),
				),
				'skew'      => array(
					'label' => esc_html__( 'Transform Skew', 'et_builder' ),
				),
				'origin'    => array(
					'label' => esc_html__( 'Transform Origin', 'et_builder' ),
				),
				'styles'    => array(
					'label'       => esc_html__( 'Transform', 'et_builder' ),
					'description' => esc_html__( 'Using the transform controls, you can performance visual adjustments to any element using a combination of Scale, Translation, Rotation and Skew settings. This allows you to create advanced design effects without the need of a separate graphic design program.', 'et_builder' ),
				),
			);
			// phpcs:enable
		}

		$settings_args = array(
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'transform',
			'depends_on'      => null,
			'depends_show_if' => null,
			'defaults'        => $this->defaults,
		);

		$settings = wp_parse_args( $settings_args, $args );

		$additional_options = array();
		$defaults           = $settings['defaults'];

		$tabs = array(
			'scale'     => array(
				'icon'     => 'resize',
				'controls' => array(
					'transform_scale' => array(
						'type'           => 'transform',
						'label'          => $i18n['scale']['label'],
						'default'        => "{$defaults['scale']}|{$defaults['scale']}",
						'default_unit'   => '%',
						'range_settings' => array(
							'min'  => -100,
							'max'  => 300,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
						'sticky'         => true,
					),
				),
			),
			'translate' => array(
				'icon'     => 'move',
				'controls' => array(
					'transform_translate' => array(
						'type'           => 'transform',
						'label'          => $i18n['translate']['label'],
						'default'        => "{$defaults['translate']}|{$defaults['translate']}",
						'default_unit'   => 'px',
						'range_settings' => array(
							'min'  => -300,
							'max'  => 300,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
						'sticky'         => true,
					),
				),
			),
			'rotate'    => array(
				'icon'     => 'rotate',
				'controls' => array(
					'transform_rotate' => array(
						'type'           => 'transform',
						'label'          => $i18n['rotate']['label'],
						'default'        => "{$defaults['rotate']}|{$defaults['rotate']}|{$defaults['rotate']}",
						'default_unit'   => 'deg',
						'range_settings' => array(
							'min'  => 0,
							'max'  => 360,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
						'sticky'         => true,
					),
				),
			),
			'skew'      => array(
				'icon'     => 'skew',
				'controls' => array(
					'transform_skew' => array(
						'type'           => 'transform',
						'label'          => $i18n['skew']['label'],
						'default'        => "{$defaults['skew']}|{$defaults['skew']}",
						'default_unit'   => 'deg',
						'range_settings' => array(
							'min'       => -180,
							'max'       => 180,
							'min_limit' => -180,
							'max_limit' => 180,
							'step'      => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
						'sticky'         => true,
					),
				),
			),
			'origin'    => array(
				'icon'     => 'transform-origin',
				'controls' => array(
					'transform_origin' => array(
						'type'           => 'transform',
						'label'          => $i18n['origin']['label'],
						'default'        => "{$defaults['origin']}|{$defaults['origin']}",
						'default_unit'   => '%',
						'range_settings' => array(
							'min'  => -50,
							'max'  => 150,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
						'sticky'         => true,
					),
				),
			),
		);

		$additional_options['transform_styles'] = array(
			'label'               => $i18n['styles']['label'],
			'tab_slug'            => $settings['tab_slug'],
			'toggle_slug'         => $settings['toggle_slug'],
			'type'                => 'composite',
			'attr_suffix'         => '',
			'composite_type'      => 'transforms',
			'hover'               => 'tabs',
			'mobile_options'      => true,
			'sticky'              => true,
			'responsive'          => true,
			'bb_support'          => false,
			'description'         => $i18n['styles']['description'],
			'composite_structure' => $tabs,
		);

		// Register responsive options
		$skip       = array(
			'type'        => 'skip',
			'tab_slug'    => $settings['tab_slug'],
			'toggle_slug' => $settings['toggle_slug'],
		);
		$linked_skip = $skip + array( 'default' => 'on' );

		foreach ( $additional_options['transform_styles']['composite_structure'] as $tab_name => $tab ) {
			foreach ( $tab['controls'] as $field_name => $field_options ) {
				$controls                                = $additional_options['transform_styles']['composite_structure'][ $tab_name ]['controls'];
				$controls[ "{$field_name}_tablet" ]      = $skip;
				$controls[ "{$field_name}_phone" ]       = $skip;
				$controls[ "{$field_name}_last_edited" ] = $skip;
				if ( in_array( $field_name, array( 'transform_scale', 'transform_translate', 'transform_skew' ) ) ) {
					$controls[ "{$field_name}_linked" ]         = $linked_skip;
					$controls[ "{$field_name}_linked_tablet" ]  = $linked_skip;
					$controls[ "{$field_name}_linked_phone" ]   = $linked_skip;
					$controls[ "{$field_name}_linked__hover" ]  = $linked_skip;
					$controls[ "{$field_name}_linked__sticky" ] = $linked_skip;
				}
				$additional_options['transform_styles']['composite_structure'][ $tab_name ]['controls'] = $controls;
			}
		}
		$additional_options['transform_styles_last_edited'] = $skip;

		return $additional_options;
	}

	// Processing functions

	public function percent_to_unit( $percent = 0 ) {
		if ( strpos( $percent, '%' ) === false ) {
			return $percent;
		}
		$value = (float) trim( str_replace( '%', '', $percent ) );

		return $value / 100;
	}

	public function set_props( $props ) {
		$this->processing_props = $props;
	}

	public function get_setting( $value, $default ) {
		if ( ! empty( $this->processing_props[ $value ] ) ) {
			return $this->processing_props[ $value ];
		} else {
			return $default;
		}
	}

	public function get_option( $typeAxis, $type = 'desktop' ) {
		$setting     = "transform_$typeAxis[0]";
		$interpreter = array(
			'X' => 0,
			'Y' => 1,
			'Z' => 2,
		);
		$index       = $interpreter[ $typeAxis[1] ];

		$default_value = false;
		$option_value  = $this->get_setting( $setting, false );

		if ( 'hover' === $type ) {
			$default_value = $this->get_setting( $setting, false );
			$option_value  = $this->get_setting( $setting . '__hover', false );
		} elseif ( 'sticky' === $type ) {
			$default_value = $this->get_setting( $setting, false );
			$option_value  = $this->get_setting( $setting . '__sticky', false );
		} elseif ( 'tablet' === $type ) {
			$default_value = $this->get_setting( $setting, false );
			$option_value  = $this->get_setting( $setting . '_tablet', false );
		} elseif ( 'phone' === $type ) {
			$default_value = $this->get_setting( $setting . '_tablet', false );
			$option_value  = $this->get_setting( $setting . '_phone', false );
			if ( false === $default_value ) {
				$default_value = $this->get_setting( $setting, false );
			}
		}

		if ( false === $option_value ) {
			if ( false !== $default_value ) {
				$option_value = $default_value;
			}
		}

		if ( false === $option_value ) {
			return '';
		}

		$value_array = explode( '|', $option_value );
		$value       = $value_array[ $index ];

		if ( 'scale' === $typeAxis[0] ) {
			return $this->percent_to_unit( $value );
		}

		return $value;

	}

	public function get_elements( $type ) {
		if ( empty( $this->processing_props ) ) {
			wp_die( new WP_Error( '666', 'Run set_props first' ) );
		}

		$transformElements = array();
		$originArray       = array();
		foreach ( $this->allTransforms as $option ) {
			$typeAxis    = array();
			$typeAxis[0] = substr( $option, 0, -1 );
			$typeAxis[1] = substr( $option, -1 );
			$value       = esc_attr( $this->get_option( $typeAxis, $type ) );
			if ( ! empty( $value ) ) {
				if ( 'origin' === $typeAxis[0] ) {
					if ( 'originY' === $option && empty( $originArray ) ) {
						// default value of originX
						array_push( $originArray, '50%' );
					}
					array_push( $originArray, $value );
				} else {
					$transformElements[ $option ] = $value;
				}
			}
		}

		return array(
			'transform' => $transformElements,
			'origin'    => $originArray,
		);
	}

	public function getTransformDeclaration( $transformElements, $view = 'desktop' ) {
		$declaration = array();
		unset( $transformElements['originX'], $transformElements['originY'] );
		// Perspective is included on when combining with some animations
		if ( ! empty( $transformElements['perspective'] ) ) {
			array_push( $declaration, sprintf( 'perspective(%s)', $transformElements['perspective'] ) );
		}
		// Transforms must maintain this order to blend correctly with animation rules
		foreach ( $this->allTransforms as $option ) {
			if ( ! empty( $transformElements[ $option ] ) ) {
				array_push( $declaration, sprintf( '%s(%s)', $option, $transformElements[ $option ] ) );
			}
		}
		if ( ! empty( $declaration ) ) {

			if ( $this->processing_props['transforms_important'] || 'hover' === $view || 'sticky' === $view ) {
				array_push( $declaration, '!important' );
			}

			return sprintf( 'transform: %s;', implode( ' ', $declaration ) );
		}

		return '';
	}

	/**
	 * @param $animationType
	 * @param $elements
	 * @param $function_name
	 * @param $device
	 *
	 * @return array
	 */
	public function transformedAnimation( $animationType, $elements, $function_name, $device ) {
		if ( 'hover' === $device || 'sticky' === $device ) {
			return array();
		}

		// phpcs:disable ET.Sniffs.ValidVariableName.VariableNotSnakeCase, ET.Sniffs.ValidVariableName.InterpolatedVariableNotSnakeCase -- Existing codebase
		$utils                = ET_Core_Data_Utils::instance();
		$startElements        = $elements['transform'];
		$responsive           = ET_Builder_Module_Helper_ResponsiveOptions::instance();
		$direction            = $responsive->get_any_value( $this->processing_props, 'animation_direction', 'center', true, $device );
		$animation_intensity  = $utils->array_get( $this->processing_props, "animation_intensity_$animationType", 50 );
		$module_class         = ET_Builder_Element::get_module_order_class( $function_name );
		$animationName        = "et_pb_{$animationType}_{$direction}_$module_class";
		$newKeyframe          = "@keyframes $animationName";
		$newAnimationSelector = ".$module_class.et_animated.transformAnim";
		$newAnimationRules    = "animation-name: $animationName;";
		$newKeyframeRules     = '';
		$transformDeclaration = $this->getTransformDeclaration( $elements['transform'] );
		$originDeclaration    = sprintf( 'transform-origin:%s;', implode( ' ', $elements['origin'] ) );

		$intensity = ! is_numeric( str_replace( '%', '', $animation_intensity ) )
			? 50
			: (int) str_replace( '%', '', $animation_intensity );

		// slide animation direction center is the same animation as zoom center
		if ( 'slide' === $animationType && 'center' === $direction ) {
			$animationType = 'zoom';
		}

		// animation transform gets combined with transform settings as described on et-builder-custom-output.jsx processTransform method
		switch ( $animationType ) {
			case 'zoom':
				$scale                   = ( 100 - $intensity ) * 0.01;
				$startElements['scaleX'] = $scale * $utils->array_get( $elements['transform'], 'scaleX', 1 );
				$startElements['scaleY'] = $scale * $utils->array_get( $elements['transform'], 'scaleY', 1 );
				$startDeclaration        = $this->getTransformDeclaration( $startElements );
				// replace origin declaration to preserve animation direction setting only if transform origin is not set
				if ( empty( $elements['origin'] ) ) {
					$originDeclaration = "transform-origin: $direction;";
				} else {
					$originDeclaration = sprintf( 'transform-origin: %s;', implode( ' ', $elements['origin'] ) );
				}
				$newKeyframeRules   = "0%{ $startDeclaration }";
				$newKeyframeRules  .= "100%{opacity:1;$transformDeclaration}";
				$newAnimationRules .= $originDeclaration;
				break;
			case 'slide':
				$translateY = $utils->array_get( $elements['transform'], 'translateY', '0%' );
				$translateX = $utils->array_get( $elements['transform'], 'translateX', '0%' );
				switch ( $direction ) {
					case 'top':
						$startElements['translateY'] = sprintf( 'calc(%s%% + %s)', $intensity * -2, $translateY );
						$startElements['translateX'] = $translateX;
						break;
					case 'bottom':
						$startElements['translateY'] = sprintf( 'calc(%s%% + %s)', $intensity * 2, $translateY );
						$startElements['translateX'] = $translateX;
						break;
					case 'left':
						$startElements['translateX'] = sprintf( 'calc(%s%% + %s)', $intensity * -2, $translateX );
						$startElements['translateY'] = $translateY;
						break;
					case 'right':
						$startElements['translateX'] = sprintf( 'calc(%s%% + %s)', $intensity * 2, $translateX );
						$startElements['translateY'] = $translateY;
						break;
				}
				$startDeclaration  = $this->getTransformDeclaration( $startElements );
				$newKeyframeRules  = "0%{ $startDeclaration }";
				$newKeyframeRules .= "100%{opacity:1;$transformDeclaration}";
				break;
			case 'bounce':
				$translateX = $utils->array_get( $elements['transform'], 'translateX', '0px' );
				$translateY = $utils->array_get( $elements['transform'], 'translateY', '0px' );
				switch ( $direction ) {
					case 'center':
						$scaleX = (float) $utils->array_get( $elements['transform'], 'scaleX', 1 );
						$scaleY = (float) $utils->array_get( $elements['transform'], 'scaleY', 1 );

						$newKeyframeRules = 'from, 20%, 40%, 60%, 80%, to {animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);}';

						$startElements['scaleX'] = 0.3 * $scaleX;
						$startElements['scaleY'] = 0.3 * $scaleY;
						$newKeyframeRules       .= sprintf( '0%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['scaleX'] = 1.1 * $scaleX;
						$startElements['scaleY'] = 1.1 * $scaleY;
						$newKeyframeRules       .= sprintf( '20%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['scaleX'] = 0.9 * $scaleX;
						$startElements['scaleY'] = 0.9 * $scaleY;
						$newKeyframeRules       .= sprintf( '40%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['scaleX'] = 1.03 * $scaleX;
						$startElements['scaleY'] = 1.03 * $scaleY;
						$newKeyframeRules       .= sprintf( '60%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['scaleX'] = 0.97 * $scaleX;
						$startElements['scaleY'] = 0.97 * $scaleY;
						$newKeyframeRules       .= sprintf( '80%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$newKeyframeRules       .= "100%{opacity: 1;$transformDeclaration}";
						break;
					case 'top':
						$newKeyframeRules = 'from, 60%, 75%, 90%, to {animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);}';

						$startElements['translateY'] = "calc(-200px + $translateY)";
						$newKeyframeRules           .= sprintf( '0%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateY'] = "calc(25px + $translateY)";
						$newKeyframeRules           .= sprintf( '60%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateY'] = "calc(-10px + $translateY)";
						$newKeyframeRules           .= sprintf( '75%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateY'] = "calc(5px + $translateY)";
						$newKeyframeRules           .= sprintf( '90%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$newKeyframeRules           .= "100%{opacity: 1;$transformDeclaration}";
						break;
					case 'bottom':
						$newKeyframeRules = 'from, 60%, 75%, 90%, to {animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);}';

						$startElements['translateY'] = "calc(200px + $translateY)";
						$newKeyframeRules           .= sprintf( '0%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateY'] = "calc(-25px + $translateY)";
						$newKeyframeRules           .= sprintf( '60%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateY'] = "calc(10px + $translateY)";
						$newKeyframeRules           .= sprintf( '75%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateY'] = "calc(-5px + $translateY)";
						$newKeyframeRules           .= sprintf( '90%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$newKeyframeRules           .= "100%{opacity: 1;$transformDeclaration}";
						break;
					case 'left':
						$newKeyframeRules = 'from, 60%, 75%, 90%, to {animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);}';

						$startElements['translateX'] = "calc(-200px + $translateX)";
						$newKeyframeRules           .= sprintf( '0%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateX'] = "calc(25px + $translateX)";
						$newKeyframeRules           .= sprintf( '60%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateX'] = "calc(-10px + $translateX)";
						$newKeyframeRules           .= sprintf( '75%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateX'] = "calc(5px + $translateX)";
						$newKeyframeRules           .= sprintf( '90%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$newKeyframeRules           .= "100%{opacity: 1;$transformDeclaration}";
						break;
					case 'right':
						$newKeyframeRules = 'from, 60%, 75%, 90%, to {animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);}';

						$startElements['translateX'] = "calc(200px + $translateX)";
						$newKeyframeRules           .= sprintf( '0%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateX'] = "calc(-25px + $translateX)";
						$newKeyframeRules           .= sprintf( '60%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateX'] = "calc(10px + $translateX)";
						$newKeyframeRules           .= sprintf( '75%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$startElements['translateX'] = "calc(-5px + $translateX)";
						$newKeyframeRules           .= sprintf( '90%%{%s}', $this->getTransformDeclaration( $startElements ) );
						$newKeyframeRules           .= "100%{opacity: 1;$transformDeclaration}";
						break;
				}
				break;
			case 'flip':
				$intensityAngle               = ceil( ( 90 / 100 ) * $intensity );
				$startElements['perspective'] = '2000px';

				$rotateX = (float) str_replace( 'deg', '', $utils->array_get( $elements['transform'], 'rotateX', '0' ) );
				$rotateY = (float) str_replace( 'deg', '', $utils->array_get( $elements['transform'], 'rotateY', '0' ) );
				switch ( $direction ) {
					default:
					case 'top':
						$intensityAngle         += $rotateX;
						$startElement['rotateX'] = "{$intensityAngle}deg";
						break;
					case 'bottom':
						$intensityAngle         *= -1;
						$intensityAngle         += $rotateX;
						$startElement['rotateX'] = "{$intensityAngle}deg";
						break;
					case 'left':
						$intensityAngle         *= -1;
						$intensityAngle         += $rotateY;
						$startElement['rotateY'] = "{$intensityAngle}deg";
						break;
					case 'right':
						$intensityAngle         += $rotateY;
						$startElement['rotateY'] = "{$intensityAngle}deg";
						break;
				}
				$startDeclaration  = $this->getTransformDeclaration( $startElement );
				$newKeyframeRules  = "0%{ $startDeclaration }";
				$newKeyframeRules .= "100%{opacity:1;$transformDeclaration}";
				break;

			case 'fold':
				$intensityAngle               = ceil( ( 90 / 100 ) * $intensity );
				$startElements['perspective'] = '2000px';

				$rotateX = (float) str_replace( 'deg', '', $utils->array_get( $elements['transform'], 'rotateX', '0' ) );
				$rotateY = (float) str_replace( 'deg', '', $utils->array_get( $elements['transform'], 'rotateY', '0' ) );
				switch ( $direction ) {
					case 'top':
						$intensityAngle          *= -1;
						$intensityAngle          += $rotateX;
						$startElements['rotateX'] = "{$intensityAngle}deg";
						break;
					case 'bottom':
						$intensityAngle          += $rotateX;
						$startElements['rotateX'] = "{$intensityAngle}deg";
						break;
					case 'left':
						$intensityAngle          += $rotateY;
						$startElements['rotateY'] = "{$intensityAngle}deg";
						break;
					default:
					case 'right':
						$intensityAngle          *= -1;
						$intensityAngle          += $rotateY;
						$startElements['rotateY'] = "{$intensityAngle}deg";
						break;
				}
				$startDeclaration  = $this->getTransformDeclaration( $startElements );
				$newKeyframeRules  = "0%{{$startDeclaration}}";
				$newKeyframeRules .= "100%{opacity:1;{$transformDeclaration}}";
				// replace origin declaration to preserve animation direction setting only if transform origin is not set
				if ( empty( $elements['origin'] ) ) {
					$originDeclaration = "transform-origin: $direction;";
				} else {
					$originDeclaration = sprintf( 'transform-origin: %s;', implode( ' ', $elements['origin'] ) );
				}
				$newAnimationRules .= $originDeclaration;
				break;

			case 'roll':
				$rotateZ        = (float) str_replace( 'deg', '', $utils->array_get( $elements['transform'], 'rotateZ', '0' ) );
				$intensityAngle = ceil( ( 360 / 100 ) * $intensity );

				if ( 'bottom' === $direction || 'right' === $direction ) {
					$startElements['rotateZ'] = sprintf( '%sdeg', ( $intensityAngle * -1 ) + $rotateZ );
				} else {
					$startElements['rotateZ'] = sprintf( '%sdeg', $intensityAngle + $rotateZ );
				}

				$startDeclaration  = $this->getTransformDeclaration( $startElements );
				$newKeyframeRules  = "0%{ $startDeclaration }";
				$newKeyframeRules .= "100%{opacity:1;$transformDeclaration}";
				// replace origin declaration to preserve animation direction setting only if transform origin is not set
				if ( empty( $elements['origin'] ) ) {
					$originDeclaration = "transform-origin: $direction;";
				} else {
					$originDeclaration = sprintf( 'transform-origin: %s;', implode( ' ', $elements['origin'] ) );
				}
				$newAnimationRules .= $originDeclaration;
				break;
		}

		if ( ! empty( $newKeyframeRules ) ) {
			return array(
				'keyframe'       => array(
					'selector'    => $newKeyframe,
					'declaration' => $newKeyframeRules,
				),
				'animationRules' => array(
					'selector'    => $newAnimationSelector,
					'declaration' => $newAnimationRules,
				),
				'declaration'    => $transformDeclaration . $originDeclaration,
			);
		}
		// phpcs:enable ET.Sniffs.ValidVariableName.VariableNotSnakeCase, ET.Sniffs.ValidVariableName.InterpolatedVariableNotSnakeCase

		return array();
	}

	/**
	 * Check if we need to process transform.
	 * Here we also check positions since some of it
	 * requires transform css.
	 *
	 * @param array $attrs     Module attributes.
	 * @param array $positions Position locations.
	 *
	 * @return bool
	 */
	public function is_used( $attrs, $positions ) {
		// Check for transform attrs first.
		foreach ( $attrs as $attr => $value ) {
			if ( ! $value ) {
				continue;
			}

			$is_attr = 0 === strpos( $attr, 'transform_' );

			if ( $is_attr ) {
				return true;
			}
		}

		// Then check the current positions.
		foreach ( $positions as $pos => $value ) {
			$default_strpos = strpos( $value, '_is_default' );
			if ( false === $default_strpos ) {
				return true;
			}
		}

		return false;
	}
}

return new ET_Builder_Module_Field_Transform();

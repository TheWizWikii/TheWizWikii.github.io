<?php

class ET_Builder_Module_Field_BoxShadow extends ET_Builder_Module_Field_Base {
	private static $classes = array();

	/**
	 * ET_Builder_Module_Helper_OptionTemplate class
	 *
	 * @var ET_Builder_Module_Helper_OptionTemplate
	 */
	public $template;

	/**
	 * @var ET_Builder_Module_Helper_ResponsiveOptions
	 *
	 * @since 3.23
	 */
	public static $responsive = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->template = et_pb_option_template();
		$this->set_template();
	}

	/**
	 * Set option template for Box Shadow
	 *
	 * @since 3.28
	 *
	 * @return void
	 */
	public function set_template() {
		$template = $this->template;
		if ( $template->is_enabled() && ! $template->has( 'box_shadow' ) ) {
			$template->add(
				'box_shadow',
				$this->get_fields(
					$template->placeholders(
						array(
							'suffix'              => null,
							'label'               => null,
							'option_category'     => null,
							'tab_slug'            => null,
							'toggle_slug'         => null,
							'sub_toggle'          => null,
							'depends_show_if_not' => null,
							'depends_show_if'     => null,
							'depends_on'          => null,
							'default_on_fronts'   => null,
							'show_if'             => null,
							'show_if_not'         => null,
						)
					)
				)
			);
		}
	}

	/**
	 * Get box shadow fields.
	 *
	 * @since 3.23 Add support for responsive settings. Add allowed units for range fields.
	 *
	 * @param  array $args Box shadow settings args.
	 * @return array       Box shadow fields.
	 */
	public function get_fields( array $args = array() ) {
		static $i18n;

		// Cache translations.
		if ( ! isset( $i18n ) ) {
			$i18n['label'] = esc_html__( 'Box Shadow', 'et_builder' );
		}

		$arguments = shortcode_atts(
			array(
				'suffix'              => '',
				'label'               => $i18n['label'],
				'option_category'     => '',
				'tab_slug'            => '',
				'toggle_slug'         => '',
				'sub_toggle_slug'     => null, // @deprecated Use {@see `sub_toggle`} instead. Keep it here as backward compatibility.
				'sub_toggle'          => null,
				'depends_show_if_not' => null,
				'depends_show_if'     => null,
				'depends_on'          => null,
				'default_on_fronts'   => array(),
				'show_if'             => null,
				'show_if_not'         => null,
			),
			$args
		);

		// The `sub_toggle_slug` is deprecated in favor of `sub_toggle` which are used by
		// other option groups. Keep it here for backward compatibiluty.
		if ( ! empty( $arguments['sub_toggle_slug'] ) && empty( $arguments['sub_toggle'] ) ) {
			$arguments['sub_toggle'] = $arguments['sub_toggle_slug'];
		}

		if ( $this->template->is_enabled() && $this->template->has( 'box_shadow' ) ) {
			return $this->template->create( 'box_shadow', $arguments );
		}

		$prefix     = 'box_shadow_';
		$style      = $prefix . 'style' . $arguments['suffix'];
		$horizontal = $prefix . 'horizontal' . $arguments['suffix'];
		$vertical   = $prefix . 'vertical' . $arguments['suffix'];
		$blur       = $prefix . 'blur' . $arguments['suffix'];
		$spread     = $prefix . 'spread' . $arguments['suffix'];
		$position   = $prefix . 'position' . $arguments['suffix'];
		$color      = $prefix . 'color' . $arguments['suffix'];

		$options = array();
		$option  = array(
			'option_category'  => $arguments['option_category'],
			'tab_slug'         => $arguments['tab_slug'],
			'toggle_slug'      => $arguments['toggle_slug'],
			'sub_toggle'       => $arguments['sub_toggle'],
			'show_if_not'      => array(
				"{$style}" => 'none',
			),
			'default_on_child' => true,
		);
		$range   = array_merge(
			$option,
			array(
				'type'           => 'range',
				'range_settings' => array(
					'min'  => - 80,
					'max'  => 80,
					'step' => 1,
				),
				'default'        => 0,
				'validate_unit'  => true,
				'allowed_units'  => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'   => 'px',
				'fixed_range'    => true,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			)
		);

		$presets = array();

		foreach ( $this->get_presets() as $id => $preset ) {
			if ( 'none' === $id ) {
				$presets[] = array(
					'value'  => $id,
					'icon'   => $id,
					'fields' => $this->fetch_preset( $preset, $arguments['suffix'] ),
				);
			} else {
				$presets[] = array(
					'value'   => $id,
					'content' => sprintf( '<span class="preset %1$s"></span>', esc_attr( $id ) ),
					'fields'  => $this->fetch_preset( $preset, $arguments['suffix'] ),
				);
			}
		}

		$options[ $style ] = array_merge(
			$option,
			array(
				'label'               => $arguments['label'],
				'type'                => 'select_box_shadow',
				'default'             => 'none',
				'className'           => 'box_shadow',
				'presets'             => $presets,
				'affects'             => array( $horizontal, $vertical, $blur, $spread, $color, $position ),
				'copy_with'           => array( $horizontal, $vertical, $blur, $spread, $color, $position ),
				'depends_show_if'     => $arguments['depends_show_if'],
				'depends_show_if_not' => $arguments['depends_show_if_not'],
				'depends_on'          => $arguments['depends_on'],
				'show_if'             => $arguments['show_if'],
				'show_if_not'         => $arguments['show_if_not'],
				'description'         => esc_html__( 'Pick a box shadow style to enable box shadow for this element. Once enabled, you will be able to customize your box shadow style further. To disable custom box shadow style, choose the None option.', 'et_builder' ),
			)
		);

		// Configure dependency for fields via show_if/show_if_not attribute
		if ( null === $options[ $style ]['show_if'] ) {
			unset( $options[ $style ]['show_if'] );
		}
		if ( null === $options[ $style ]['show_if_not'] ) {
			unset( $options[ $style ]['show_if_not'] );
		}

		// Field dependency via depends_on, depends_show_if, and depends_show_if_not have been deprecated
		// These remain here as backward compatibility for third party modules
		if ( null === $options[ $style ]['depends_on'] ) {
			unset( $options[ $style ]['depends_on'] );
		}
		if ( null === $options[ $style ]['depends_show_if'] ) {
			unset( $options[ $style ]['depends_show_if'] );
		}
		if ( null === $options[ $style ]['depends_show_if_not'] ) {
			unset( $options[ $style ]['depends_show_if_not'] );
		}
		if ( isset( $arguments['default_on_fronts']['style'] ) && false !== $arguments['default_on_fronts']['style'] ) {
			$options[ $style ]['default_on_front'] = $arguments['default_on_fronts']['style'];
		}

		$options[ $horizontal ] = array_merge(
			$range,
			array(
				'label'       => esc_html__( 'Box Shadow Horizontal Position', 'et_builder' ),
				'description' => esc_html__( 'Shadow\'s horizontal distance from the element. A negative value places the shadow to the left of the element.', 'et_builder' ),
			)
		);
		$options[ $vertical ]   = array_merge(
			$range,
			array(
				'label'       => esc_html__( 'Box Shadow Vertical Position', 'et_builder' ),
				'description' => esc_html__( 'Shadow\'s vertical distance from the element. A negative value places the shadow above the element.', 'et_builder' ),
			)
		);
		$options[ $blur ]       = array_merge(
			$range,
			array(
				'label'          => esc_html__( 'Box Shadow Blur Strength', 'et_builder' ),
				'range_settings' => array(
					'min'  => 0,
					'max'  => 80,
					'step' => 1,
				),
				'description'    => esc_html__( 'The higher the value, the bigger the blur, the shadow becomes wider and lighter.', 'et_builder' ),
			)
		);
		$options[ $spread ]     = array_merge(
			$range,
			array(
				'label'       => esc_html__( 'Box Shadow Spread Strength', 'et_builder' ),
				'description' => esc_html__( 'Increasing the spread strength will increase the density of the box shadow. Higher density results in a more intense shadow.', 'et_builder' ),
			)
		);
		$options[ $color ]      = array_merge(
			$option,
			array(
				'label'          => esc_html__( 'Shadow Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'hover'          => 'tabs',
				'default'        => 'rgba(0,0,0,0.3)',
				'field_template' => 'color',
				'mobile_options' => true,
				'sticky'         => true,
				'description'    => esc_html__( 'The color of the shadow.', 'et_builder' ),
			)
		);

		if ( isset( $arguments['default_on_fronts']['color'] ) && false !== $arguments['default_on_fronts']['color'] ) {
			$options[ $color ]['default_on_front'] = $arguments['default_on_fronts']['color'];
		}

		$options[ $position ] = array_merge(
			$option,
			array(
				'label'          => esc_html__( 'Box Shadow Position', 'et_builder' ),
				'description'    => esc_html__( 'Choose whether you would like the shadow to appear outside your module, lifting the module up from the page, or inside the module, setting the module downwards within the page.', 'et_builder' ),
				'type'           => 'select',
				'default'        => 'outer',
				'options'        => array(
					'outer' => esc_html__( 'Outer Shadow', 'et_builder' ),
					'inner' => esc_html__( 'Inner Shadow', 'et_builder' ),
				),
				'mobile_options' => true,
			)
		);

		if ( isset( $arguments['default_on_fronts']['position'] ) && false !== $arguments['default_on_fronts']['position'] ) {
			$options[ $position ]['default_on_front'] = $arguments['default_on_fronts']['position'];
		}

		$list = array(
			'vertical'   => $vertical,
			'horizontal' => $horizontal,
			'blur'       => $blur,
			'spread'     => $spread,
			'position'   => $position,
		);
		foreach ( $list as $id => $field ) {
			$values = array();
			foreach ( array_keys( $this->get_presets() ) as $preset ) {
				$values[ $preset ] = $this->get_preset_field( $preset, $id );
			}
			$options[ $field ]['default'] = array( $style, $values );
		}

		return $options;
	}

	/**
	 * Get box-shadow declaration style.
	 *
	 * @since 3.23 Add support for responsive settings.
	 *
	 * @param  array $atts Module attributes.
	 * @param  array $args Box-shadow arguments.
	 * @return string       Box shadow CSS declaration.
	 */
	public function get_value( $atts, array $args = array() ) {
		$args      = shortcode_atts(
			array(
				'suffix'    => '',
				'important' => false,
				'hover'     => false,
				'sticky'    => false,
				'device'    => 'desktop',
			),
			$args
		);
		$suffix    = $args['suffix'];
		$important = $args['important'] ? '!important' : '';
		$hover     = $args['hover'];
		$sticky    = $args['sticky'];
		$device    = $args['device'];
		$style     = $this->get_key_value( "style$suffix", $atts );

		if ( empty( $style ) || 'none' === $style ) {
			return '';
		}

		// 1. Get preset styles as default.
		$preset = $this->get_preset( $style );

		// 2. Get current device properties value.
		$atts = array_merge(
			array(
				"box_shadow_position{$suffix}"   => $preset['position'],
				"box_shadow_horizontal{$suffix}" => $preset['horizontal'],
				"box_shadow_vertical{$suffix}"   => $preset['vertical'],
				"box_shadow_blur{$suffix}"       => $preset['blur'],
				"box_shadow_spread{$suffix}"     => $preset['spread'],
				"box_shadow_color{$suffix}"      => 'rgba(0,0,0,0.3)',
			),
			array_filter( $atts, 'strlen' )
		);

		// All the values below sometime return null.
		$position   = $this->get_key_value( "position{$suffix}", $atts, false, $device, $sticky ) === 'inner' ? 'inset' : '';
		$horizontal = $this->get_key_value( "horizontal{$suffix}", $atts, $hover, $device, $sticky );
		$vertical   = $this->get_key_value( "vertical{$suffix}", $atts, $hover, $device, $sticky );
		$blur       = $this->get_key_value( "blur{$suffix}", $atts, $hover, $device, $sticky );
		$strength   = $this->get_key_value( "spread{$suffix}", $atts, $hover, $device, $sticky );
		$color      = $this->get_key_value( "color{$suffix}", $atts, $hover, $device, $sticky );

		// CSS declaration.
		$value = sprintf(
			'box-shadow: %1$s %2$s %3$s %4$s %5$s %6$s %7$s;',
			$position,
			$horizontal,
			$vertical,
			$blur,
			$strength,
			$color,
			$important
		);

		// Do not provider hover style if it is the same as normal style
		if ( $hover ) {
			$new_args          = $args;
			$new_args['hover'] = false;
			$normal            = $this->get_value( $atts, $new_args );

			if ( $normal === $value ) {
				return '';
			}
		}

		return $value;
	}

	public function get_presets() {
		return array(
			'none'    => array(
				'horizontal' => '',
				'vertical'   => '',
				'blur'       => '',
				'spread'     => '',
				'position'   => 'outer',
			),
			'preset1' => array(
				'horizontal' => '0px',
				'vertical'   => '2px',
				'blur'       => '18px',
				'spread'     => '0px',
				'position'   => 'outer',
			),
			'preset2' => array(
				'horizontal' => '6px',
				'vertical'   => '6px',
				'blur'       => '18px',
				'spread'     => '0px',
				'position'   => 'outer',
			),
			'preset3' => array(
				'horizontal' => '0px',
				'vertical'   => '12px',
				'blur'       => '18px',
				'spread'     => '-6px',
				'position'   => 'outer',
			),
			'preset4' => array(
				'horizontal' => '10px',
				'vertical'   => '10px',
				'blur'       => '0px',
				'spread'     => '0px',
				'position'   => 'outer',
			),
			'preset5' => array(
				'horizontal' => '0px',
				'vertical'   => '6px',
				'blur'       => '0px',
				'spread'     => '10px',
				'position'   => 'outer',
			),
			'preset6' => array(
				'horizontal' => '0px',
				'vertical'   => '0px',
				'blur'       => '18px',
				'spread'     => '0px',
				'position'   => 'inner',
			),
			'preset7' => array(
				'horizontal' => '10px',
				'vertical'   => '10px',
				'blur'       => '0px',
				'spread'     => '0px',
				'position'   => 'inner',
			),
		);
	}

	public function get_preset( $name ) {
		$presets = $this->get_presets();

		return isset( $presets[ $name ] )
			? $presets[ $name ]
			: array(
				'horizontal' => 0,
				'vertical'   => 0,
				'blur'       => 0,
				'spread'     => 0,
				'position'   => 'outer',
			);
	}

	public function get_style( $selector, array $atts = array(), array $args = array() ) {
		$value = $this->get_value( $atts, $args );

		return array(
			'selector'    => $selector,
			'declaration' => empty( $value ) ? null : $value,
		);
	}

	public function has_overlay( $atts, $args ) {
		$overlay = ET_Core_Data_Utils::instance()->array_get( $args, 'overlay', false );
		$inset   = $this->is_inset( $this->get_value( $atts, $args ) );

		return ( $inset && 'inset' === $overlay ) || 'always' === 'overlay';
	}

	public function get_overlay_selector( $selector ) {
		$selectors    = array_map( 'trim', explode( ',', $selector ) );
		$new_selector = array();

		foreach ( $selectors as $selector ) {
			$new_selector[] = $selector . '>.box-shadow-overlay, ' . $selector . '.et-box-shadow-no-overlay';
		}

		return implode( ',', $new_selector );
	}

	public function get_overlay_style( $function_name, $selector, $atts, array $args = array() ) {
		$order_class_name = ET_Builder_Element::get_module_order_class( $function_name );

		$reg_selector = str_replace( '%%order_class%%', ".{$order_class_name}", $selector );
		$reg_selector = str_replace( '%order_class%', ".{$order_class_name}", $reg_selector );

		// %%parent_class%% only works if child module's slug is `parent_slug` + _item suffix. If child module slug
		// use different slug structure, %%parent_class%% should not be used
		if ( false !== strpos( $reg_selector, '%%parent_class%%' ) ) {
			$parent_class = str_replace( '_item', '', $function_name );
			$reg_selector = str_replace( '%%parent_class%%', ".{$parent_class}", $reg_selector );
		}

		$selector = $this->get_overlay_selector( $selector );
		$value    = $this->get_value( $atts, $args );

		if ( empty( $value ) ) {
			return array(
				'selector'    => $selector,
				'declaration' => null,
			);
		}

		array_map(
			array( get_class( $this ), 'register_element' ),
			array_map( 'trim', explode( ',', $reg_selector ) )
		);

		return array(
			'selector'    => $selector,
			'declaration' => $value,
		);
	}

	public function is_inset( $style ) {
		return strpos( $style, 'inset' ) !== false;
	}

	public static function register_element( $class ) {
		self::$classes[] = $class;
	}

	public static function get_elements() {
		return self::$classes;
	}

	protected function fetch_preset( array $preset, $suffix ) {
		return array(
			"box_shadow_horizontal{$suffix}" => $preset['horizontal'],
			"box_shadow_vertical{$suffix}"   => $preset['vertical'],
			"box_shadow_blur{$suffix}"       => $preset['blur'],
			"box_shadow_spread{$suffix}"     => $preset['spread'],
			"box_shadow_position{$suffix}"   => $preset['position'],
		);
	}

	protected function get_preset_field( $name, $field ) {
		$preset = $this->get_preset( $name );

		return $preset[ $field ];
	}

	/**
	 * Get box shadow property value based on current active device.
	 *
	 * @since 3.23 Add responsive support. Check last edited value first for tablet/phone.
	 *
	 * @param  string  $key    Box shadow property.
	 * @param  array   $atts   All module attributes.
	 * @param  boolean $hover  Hover mode status.
	 * @param  string  $device Current device.
	 * @param  boolean $sticky Sticky mode status.
	 *
	 * @return string          Box shadow property value.
	 */
	protected function get_key_value( $key, $atts = array(), $hover = false, $device = 'desktop', $sticky = false ) {
		$hover_options = et_pb_hover_options();

		// Add device name as suffix.
		$is_desktop    = 'desktop' === $device;
		$device_suffix = '';
		if ( ! $hover && ! $is_desktop && ! $sticky ) {
			$device_suffix = "_{$device}";
		}

		// Get current active device value.
		$attr_value = et_pb_responsive_options()->get_any_value( $atts, "box_shadow_{$key}{$device_suffix}", '', true );

		// Bail early if current mode is sticky, hover or desktop mode.
		if ( $sticky ) {
			return et_pb_sticky_options()->get_value( "box_shadow_{$key}", $atts, $attr_value );
		} elseif ( $hover ) {
			return $hover_options->get_value( "box_shadow_{$key}", $atts, $attr_value );
		} elseif ( $is_desktop ) {
			return $attr_value;
		}

		// Ensure responsive settings is enabled before return tablet/phone value.
		$is_responsive = et_pb_responsive_options()->is_responsive_enabled( $atts, "box_shadow_{$key}" );
		if ( ! $is_responsive ) {
			// To avoid any issue when no box shadow defined on tablet and phone, we should return
			// desktop value instead. By doing this, tablet and phone box shadow will be identical
			// with desktop box shadow value.
			return et_pb_responsive_options()->get_any_value( $atts, "box_shadow_{$key}" );
		}

		return $attr_value;
	}

	/**
	 * Check if box shadow is used.
	 *
	 * @since 4.10.0
	 * @param array  $attrs All module attributes.
	 * @param string $key   Box shadow property.
	 */
	public function is_used( $attrs, $key = '' ) {
		foreach ( $attrs as $attr => $value ) {
			if ( ! $value ) {
				continue;
			}

			$has_attr = false !== strpos( $attr, 'box_shadow_style' );

			if ( ! $has_attr ) {
				continue;
			}

			return ! empty( $attr );
		}

	}

	/**
	 * Check if module has inset.
	 *
	 * @since 4.10.0
	 * @param array $attrs            All module attributes.
	 * @param array $advanced_options Advanced module options.
	 */
	public function has_inset( $attrs, $advanced_options, $_ ) {
		$has_box_inset = false;
		foreach ( $advanced_options as $option_name => $option_settings ) {
			if ( true === $has_box_inset ) {
				break;
			}
			// Enable module to explicitly disable box shadow fields (box shadow is automatically)
			// added to all module by default.
			if ( false === $option_settings ) {
				continue;
			}

			// Prepare attribute for getting box shadow's css declaration.
			$declaration_args = array(
				'suffix'    => 'default' === $option_name ? '' : "_{$option_name}",
				'important' => $_->array_get( $option_settings, 'css.important', false ),
			);

			$overlay = $_->array_get( $option_settings, 'css.overlay', false );
			$inset   = $this->is_inset( $this->get_value( $attrs, $declaration_args ) );

			$inset_hover = $this->is_inset(
				$this->get_value(
					$attrs,
					array_merge( $declaration_args, array( 'hover' => true ) )
				)
			);

			$has_video_bg = ! empty( $atts['background_video_mp4'] ) || ! empty( $atts['background_video_webm'] );

			foreach ( et_pb_responsive_options()->get_modes() as $device ) {
				// Add device argument.
				$device_declaration_args = array_merge( $declaration_args, array( 'device' => $device ) );
				if ( ( $inset && 'inset' === $overlay ) || 'always' === $overlay || $has_video_bg ) {
					$has_box_inset = true;
					break;
				}
			}
			// Get box-shadow styles.

		}
		return $has_box_inset;

	}
}

function _action_et_pb_box_shadow_overlay() {
	wp_localize_script(
		et_get_combined_script_handle(),
		'et_pb_box_shadow_elements',
		ET_Builder_Module_Field_BoxShadow::get_elements()
	);
}

add_action( 'wp_footer', '_action_et_pb_box_shadow_overlay' );

return new ET_Builder_Module_Field_BoxShadow();

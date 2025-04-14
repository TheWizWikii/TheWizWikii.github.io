<?php

class ET_Builder_Module_Field_TextShadow extends ET_Builder_Module_Field_Base {


	/**
	 * True when Divi plugin is active.
	 *
	 * @var bool
	 */
	public $is_plugin_active = false;

	/**
	 * Text shadow properties.
	 *
	 * @var array
	 */
	public $properties;

	protected $template;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->template         = et_pb_option_template();
		$this->is_plugin_active = et_is_builder_plugin_active();
		$this->properties       = array(
			'horizontal_length',
			'vertical_length',
			'blur_strength',
			'color',
		);
		$this->set_template();
	}//end __construct()

	/**
	 * Returns prefixed field names.
	 *
	 * @param string $prefix Prefix.
	 *
	 * @return array
	 */
	public function get_prefixed_field_names( $prefix ) {
		$prefix = $prefix ? "{$prefix}_" : '';

		return array(
			"{$prefix}text_shadow_style",
			"{$prefix}text_shadow_horizontal_length",
			"{$prefix}text_shadow_vertical_length",
			"{$prefix}text_shadow_blur_strength",
			"{$prefix}text_shadow_color",
		);
	}//end get_prefixed_field_names()

	/**
	 * Returns Text Shadow presets.
	 *
	 * @param string $prefix Prefix.
	 *
	 * @return array
	 */
	public function get_presets( $prefix, $suffix = '' ) {
		list(
			$text_shadow_style,
			$text_shadow_horizontal_length,
			$text_shadow_vertical_length,
			$text_shadow_blur_strength,
			$text_shadow_color
		) = $this->get_prefixed_field_names( $prefix );

		return array(
			array(
				'icon'  => 'none',
				'value' => 'none',
			),
			array(
				'value'   => 'preset1',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset1',
				),
				'fields'  => array(
					$text_shadow_horizontal_length => '0em',
					$text_shadow_vertical_length   => '0.1em',
					$text_shadow_blur_strength     => '0.1em',
				),
			),
			array(
				'value'   => 'preset2',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset2',
				),
				'fields'  => array(
					$text_shadow_horizontal_length => '0.08em',
					$text_shadow_vertical_length   => '0.08em',
					$text_shadow_blur_strength     => '0.08em',
				),
			),
			array(
				'value'   => 'preset3',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset3',
				),
				'fields'  => array(
					$text_shadow_horizontal_length => '0em',
					$text_shadow_vertical_length   => '0em',
					$text_shadow_blur_strength     => '0.3em',
				),
			),
			array(
				'value'   => 'preset4',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset4',
				),
				'fields'  => array(
					$text_shadow_horizontal_length => '0em',
					$text_shadow_vertical_length   => '0.08em',
					$text_shadow_blur_strength     => '0em',
				),
			),
			array(
				'value'   => 'preset5',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset5',
				),
				'fields'  => array(
					$text_shadow_horizontal_length => '0.08em',
					$text_shadow_vertical_length   => '0.08em',
					$text_shadow_blur_strength     => '0em',
				),
			),
		);
	}//end get_presets()

	/**
	 * Returns conditional defaults array.
	 *
	 * @param string $prefix Prefix.
	 * @param string $depend Field whose value controls which default should be used.
	 * @param string $field Field for which we're generating the defaults array.
	 * @param string $default Default value to be used when a Preset doesn't include a value for $field.
	 *
	 * @return array
	 */
	public function get_defaults( $prefix, $depend, $field, $default ) {
		$presets  = $this->get_presets( $prefix );
		$defaults = array();
		foreach ( $presets as $preset ) {
			$value              = $preset['value'];
			$defaults[ $value ] = isset( $preset['fields'][ $field ] ) ? $preset['fields'][ $field ] : $default;
		}
		return array(
			$depend,
			$defaults,
		);
	}//end get_defaults()

	/**
	 * Set option template for Text Shadow
	 *
	 * @since 3.28
	 *
	 * @return void
	 */
	public function set_template() {
		$template = $this->template;
		if ( $template->is_enabled() && ! $template->has( 'text_shadow' ) ) {
			$template_placeholder = $template->placeholders(
				array(
					'label'               => null,
					'prefix'              => null,
					'tab_slug'            => null,
					'toggle_slug'         => null,
					'sub_toggle'          => null,
					'option_category'     => null,
					'depends_show_if'     => null,
					'depends_show_if_not' => null,
					'show_if'             => null,
					'show_if_not'         => null,
				)
			);
			$template->add( 'text_shadow', $this->get_fields( $template_placeholder ) );
		}
	}

	/**
	 * Returns fields definition.
	 *
	 * @since 3.23 Add mobile_options attributes for all fields to support responsive settings, except
	 *           text_shadow_style. Add allowed units for some fields with range type.
	 *
	 * @param array $args Field configuration.
	 *
	 * @return array
	 */
	public function get_fields( array $args = array() ) {

		$config = shortcode_atts(
			array(
				'label'               => '',
				'prefix'              => '',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'text',
				'sub_toggle'          => false,
				'option_category'     => 'configuration',
				'depends_show_if'     => '',
				'depends_show_if_not' => '',
				'show_if'             => '',
				'show_if_not'         => '',
			),
			$args
		);

		if ( $this->template->is_enabled() && $this->template->has( 'text_shadow' ) ) {
			return $this->template->create( 'text_shadow', $config );
		}

		$prefix = $config['prefix'];

		list(
			$text_shadow_style,
			$text_shadow_horizontal_length,
			$text_shadow_vertical_length,
			$text_shadow_blur_strength,
			$text_shadow_color
		) = $this->get_prefixed_field_names( $prefix );

		$tab_slug        = $config['tab_slug'];
		$toggle_slug     = $config['toggle_slug'];
		$sub_toggle      = $config['sub_toggle'];
		$option_category = $config['option_category'];
		// Some option categories (like font) have custom logic that involves changing default values and we don't want that to interfere with conditional defaults. This might change in future so, for now, I'm just overriding the value while leaving the possibility to remove this line afterwards and provide custom option_category via $config.
		$option_category = 'configuration';

		$label = $config['label'];
		if ( $label ) {
			$labels = array(
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Horizontal Length', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Vertical Length', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Blur Strength', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Color', 'et_builder' ), $label ),
			);
		} else {
			$labels = array(
				esc_html__( 'Text Shadow', 'et_builder' ),
				esc_html__( 'Text Shadow Horizontal Length', 'et_builder' ),
				esc_html__( 'Text Shadow Vertical Length', 'et_builder' ),
				esc_html__( 'Text Shadow Blur Strength', 'et_builder' ),
				esc_html__( 'Text Shadow Color', 'et_builder' ),
			);
		}
		$fields = array(
			$text_shadow_style             => array(
				'label'            => $labels[0],
				'description'      => esc_html__( 'Pick a text shadow style to enable text shadow for this element. Once enabled, you will be able to customize your text shadow style further. To disable custom text shadow style, choose the None option.', 'et_builder' ),
				'type'             => 'presets_shadow',
				'option_category'  => $option_category,
				'default'          => 'none',
				'default_on_child' => true,
				'presets'          => $this->get_presets( $prefix ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'sync_affects'     => array(
					$text_shadow_horizontal_length,
					$text_shadow_vertical_length,
					$text_shadow_blur_strength,
					$text_shadow_color,
				),
				'affects'          => array(
					$text_shadow_horizontal_length,
					$text_shadow_vertical_length,
					$text_shadow_blur_strength,
					$text_shadow_color,
				),
				'copy_with'        => array(
					$text_shadow_horizontal_length,
					$text_shadow_vertical_length,
					$text_shadow_blur_strength,
					$text_shadow_color,
				),
			),
			$text_shadow_horizontal_length => array(
				'label'               => $labels[1],
				'description'         => esc_html__( 'Shadow\'s horizontal distance from the text. A negative value places the shadow to the left of the text.', 'et_builder' ),
				'type'                => 'range',
				'hover'               => 'tabs',
				'option_category'     => $option_category,
				'range_settings'      => array(
					'min'  => -2,
					'max'  => 2,
					'step' => 0.01,
				),
				'default'             => $this->get_defaults( $prefix, $text_shadow_style, $text_shadow_horizontal_length, '0em' ),
				'default_on_child'    => true,
				'hide_sync'           => true,
				'validate_unit'       => true,
				'allowed_units'       => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'        => 'em',
				'fixed_range'         => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
				'mobile_options'      => true,
				'sticky'              => true,
			),
			$text_shadow_vertical_length   => array(
				'label'               => $labels[2],
				'description'         => esc_html__( 'Shadow\'s vertical distance from the text. A negative value places the shadow above the text.', 'et_builder' ),
				'type'                => 'range',
				'hover'               => 'tabs',
				'option_category'     => $option_category,
				'range_settings'      => array(
					'min'  => -2,
					'max'  => 2,
					'step' => 0.01,
				),
				'default'             => $this->get_defaults( $prefix, $text_shadow_style, $text_shadow_vertical_length, '0em' ),
				'default_on_child'    => true,
				'hide_sync'           => true,
				'validate_unit'       => true,
				'allowed_units'       => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'        => 'em',
				'fixed_range'         => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
				'mobile_options'      => true,
				'sticky'              => true,
			),
			$text_shadow_blur_strength     => array(
				'label'               => $labels[3],
				'description'         => esc_html__( 'The higher the value, the bigger the blur, the shadow becomes wider and lighter.', 'et_builder' ),
				'type'                => 'range',
				'hover'               => 'tabs',
				'option_category'     => $option_category,
				'range_settings'      => array(
					'min'  => 0,
					'max'  => 2,
					'step' => 0.01,
				),
				'default'             => $this->get_defaults( $prefix, $text_shadow_style, $text_shadow_blur_strength, '0em' ),
				'default_on_child'    => true,
				'hide_sync'           => true,
				'validate_unit'       => true,
				'allowed_units'       => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'        => 'em',
				'fixed_range'         => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
				'mobile_options'      => true,
				'sticky'              => true,
			),
			$text_shadow_color             => array(
				'label'               => $labels[4],
				'description'         => esc_html__( 'The color of the shadow.', 'et_builder' ),
				'type'                => 'color-alpha',
				'hover'               => 'tabs',
				'option_category'     => $option_category,
				'default'             => 'rgba(0,0,0,0.4)',
				'default_on_child'    => true,
				'hide_sync'           => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
				'mobile_options'      => true,
				'sticky'              => true,
			),
		);

		// Only add sub_toggle to fields if defined
		if ( false !== $sub_toggle ) {
			$fields[ $text_shadow_style ]['sub_toggle']             = $sub_toggle;
			$fields[ $text_shadow_vertical_length ]['sub_toggle']   = $sub_toggle;
			$fields[ $text_shadow_horizontal_length ]['sub_toggle'] = $sub_toggle;
			$fields[ $text_shadow_blur_strength ]['sub_toggle']     = $sub_toggle;
			$fields[ $text_shadow_color ]['sub_toggle']             = $sub_toggle;
		}

		// add conditional settings if defined
		if ( '' !== $config['show_if'] ) {
			$fields[ $text_shadow_style ]['show_if'] = $config['show_if'];
		}

		if ( '' !== $config['show_if_not'] ) {
			$fields[ $text_shadow_style ]['show_if_not'] = $config['show_if_not'];
		}

		if ( '' !== $config['depends_show_if'] ) {
			$fields[ $text_shadow_style ]['depends_show_if'] = $config['depends_show_if'];
		}

		if ( '' !== $config['depends_show_if_not'] ) {
			$fields[ $text_shadow_style ]['depends_show_if_not'] = $config['depends_show_if_not'];
		}

		return $fields;
	}//end get_fields()

	/**
	 * Returns whether a declaration should be added !important or not.
	 *
	 * @param array  $options Field definition.
	 * @param string $key Property name.
	 *
	 * @return bool
	 */
	public function get_important( $options, $key = false ) {
		if ( ! isset( $options['css']['important'] ) ) {
			// nothing to do, bye
			return false;
		}

		$important = $options['css']['important'];
		if ( 'all' === $important || ( $this->is_plugin_active && 'plugin_only' === $important ) ) {
			return true;
		}

		if ( is_array( $important ) ) {
			if ( $this->is_plugin_active && in_array( 'plugin_all', $important ) ) {
				return true;
			}
			if ( false !== $key && in_array( $key, $important ) ) {
				return true;
			}
		}

		return false;
	}//end get_important()

	/**
	 * Returns the text-shadow declaration
	 *
	 * @since 3.23 Add responsive settings support to get the correct tablet and phone values.
	 *
	 * @param string $label Prefix.
	 * @param bool   $important Whether to add !important or not.
	 * @param array  $all_values All shortcode values.
	 * @param bool   $is_hover Hover status.
	 * @param string $device Current active device.
	 *
	 * @return string
	 */
	public function get_declaration( $label, $important, $all_values, $is_hover = false, $device = 'desktop' ) {
		$prefix     = $label ? "{$label}_" : '';
		$hover      = et_pb_hover_options();
		$sticky     = et_pb_sticky_options();
		$utils      = ET_Core_Data_Utils::instance();
		$responsive = ET_Builder_Module_Helper_ResponsiveOptions::instance();
		$is_desktop = 'desktop' === $device;
		$is_sticky  = $sticky->get_suffix() === $device;
		$suffix     = '';

		// Responsive styles. Ensure to render when at least one of the fields activate responsive
		// settings to avoid unnecessary CSS styles rendered.
		$is_any_shadow_responsive = false;

		if ( ! $is_desktop && ! $is_hover && ! $is_sticky ) {
			$is_any_shadow_responsive = $responsive->is_any_responsive_enabled(
				$all_values,
				array(
					"{$prefix}text_shadow_horizontal_length",
					"{$prefix}text_shadow_vertical_length",
					"{$prefix}text_shadow_blur_strength",
					"{$prefix}text_shadow_color",
				)
			);

			// Bail early
			if ( ! $is_any_shadow_responsive ) {
				return '';
			}

			$suffix = "_{$device}";
		}

		$text_shadow = array();
		foreach ( $this->properties as $property ) {
			// As default, we will return desktop value.
			$prop  = "{$prefix}text_shadow_{$property}";
			$value = et_()->array_get( $all_values, $prop, '' );

			if ( $is_any_shadow_responsive ) {
				// If current device is mobile (responsive settings is enabled already checked above),
				// return any value exist.
				$value = $responsive->is_responsive_enabled( $all_values, $prop ) ? $responsive->get_any_value( $all_values, "{$prop}{$suffix}", '', true ) : $value;
			} elseif ( $is_hover ) {
				$value = $hover->get_value( $prop, $all_values, $value );
			} elseif ( $is_sticky ) {
				$value = $sticky->get_value( $prop, $all_values, $value );
			}

			$text_shadow[] = $value;
		}

		return sprintf(
			'text-shadow: %s%s;',
			et_core_esc_previously( join( ' ', array_filter( $text_shadow ) ) ),
			$important ? '!important' : ''
		);
	}//end get_declaration()

	/**
	 * Adds CSS rule.
	 *
	 * @since 3.23 Add responsive settings support to render tablet and phone styles.
	 *
	 * @param ET_Builder_Element $module Module object.
	 * @param string             $label Label.
	 * @param array              $font Field definition.
	 * @param string             $function_name Shortcode function.
	 * @param bool               $is_hover Hover status.
	 * @param string             $device Current active device.
	 *
	 * @return void
	 */
	public function update_styles( $module, $label, $font, $function_name, $is_hover = false, $device = 'desktop' ) {
		$all_values            = $module->props;
		$main_element_selector = $module->main_css_element;
		$device                = '' === $device ? 'desktop' : $device;
		$hover                 = et_pb_hover_options();
		$sticky                = et_pb_sticky_options();
		$is_sticky             = $device === $sticky->get_suffix();

		// Use a different selector for plugin
		$css_element = $this->is_plugin_active && isset( $font['css']['limited_main'] ) ? 'css.limited_main' : 'css.main';

		// Use 'text_shadow' selector if defined, fallback to $css_element or default selector
		$selector            = et_()->array_get(
			$font,
			'css.text_shadow',
			et_()->array_get( $font, $css_element, $main_element_selector )
		);
		$responsive_selector = $selector;

		if ( $is_hover ) {
			if ( is_array( $selector ) ) {
				$selector = array_map( array( $this, 'add_hover_to_selectors' ), $selector );
			} else {
				$selector = $hover->add_hover_to_selectors( $selector );
			}

			$selector = et_()->array_get( $font, 'css.text_shadow_hover', et_()->array_get( $font, 'css.hover', $selector ) );
		}

		if ( $is_sticky ) {
			$has_wrapper                      = et_()->array_get( $module->wrapper_settings, 'order_class_wrapper', false );
			$is_sticky_module_without_wrapper = $has_wrapper ? false : $sticky->is_sticky_module( $all_values );
			$selector                         = $sticky->add_sticky_to_selectors( $selector, $is_sticky_module_without_wrapper, is_string( $selector ) );
			$selector                         = et_()->array_get( $font, 'css.text_shadow_sticky', et_()->array_get( $font, 'css.sticky', $selector ) );
		}

		// Get the text-shadow declaration (horizontal vertical blur color).
		$declaration = $this->get_declaration(
			$label,
			$this->get_important( $font, 'text-shadow' ),
			$all_values,
			$is_hover,
			$device
		);

		// Do not provide hover or sticky style if it is the same as normal style.
		if ( $is_hover || $is_sticky ) {
			$normal = $this->get_declaration(
				$label,
				$this->get_important( $font, 'text-shadow' ),
				$all_values,
				false
			);

			if ( $declaration === $normal ) {
				return;
			}
		}

		// Media query.
		$media_query = array();
		if ( 'desktop' !== $device && ! $is_hover && ! $is_sticky ) {
			$breakpoint  = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
			$media_query = array( 'media_query' => ET_Builder_Element::get_media_query( $breakpoint ) );
		}

		if ( is_array( $selector ) ) {
			foreach ( $selector as $single_selector ) {
				ET_Builder_Element::set_style(
					$function_name,
					array_merge(
						array(
							'selector'    => $single_selector,
							'declaration' => $declaration,
							'priority'    => $module->get_style_priority(),
						),
						$media_query
					)
				);
			}
		} else {
			ET_Builder_Element::set_style(
				$function_name,
				array_merge(
					array(
						'selector'    => $selector,
						'declaration' => $declaration,
						'priority'    => $module->get_style_priority(),
					),
					$media_query
				)
			);
		}

	}//end update_styles()

	/**
	 * Added to fix array_map can't access static class of Hover Options.
	 *
	 * @since 3.23
	 *
	 * @param string $selector Current selector.
	 *
	 * @return string Updated selector with hover suffix.
	 */
	private function add_hover_to_selectors( $selector ) {
		return et_pb_hover_options()->add_hover_to_selectors( $selector );
	}

	/**
	 * Process Text Shadow options and adds CSS rules.
	 *
	 * @since 4.6.0 Add sticky style support
	 *
	 * @param ET_Builder_Element $module Module object.
	 * @param string             $function_name Shortcode function.
	 *
	 * @return void
	 */
	public function process_advanced_css( $module, $function_name ) {
		$all_values      = $module->props;
		$advanced_fields = $module->advanced_fields;
		$hover           = et_pb_hover_options();
		$sticky          = et_pb_sticky_options();

		// Disable if module doesn't set advanced_fields property and has no VB support
		if ( ! $module->has_vb_support() && ! $module->has_advanced_fields ) {
			return;
		}

		$suffixes = array( '', 'tablet', 'phone', $hover->get_suffix(), $sticky->get_suffix() );

		foreach ( $suffixes as $suffix ) {
			$is_hover  = $hover->get_suffix() === $suffix;
			$is_sticky = $sticky->get_suffix() === $suffix;

			// Check for text shadow settings in font-options
			if ( ! empty( $advanced_fields['fonts'] ) ) {
				// We have a 'fonts' section, fetch its values
				foreach ( $advanced_fields['fonts'] as $label => $font ) {
					// label can be header / body / toggle / etc
					$shadow_style = "{$label}_text_shadow_style";

					if ( 'none' !== et_()->array_get( $all_values, $shadow_style, 'none' ) ) {
						// We have a preset selected which isn't none, need to add text-shadow style
						$this->update_styles( $module, $label, $font, $function_name, $is_hover, $suffix );
					}
				}
			}

			// Check for text shadow settings in Advanced/Text toggle
			if ( isset( $advanced_fields['text'] ) && 'none' !== et_()->array_get( $all_values, 'text_shadow_style', 'none' ) ) {
				// We have a preset selected which isn't none, need to add text-shadow style
				$text = $advanced_fields['text'];
				$this->update_styles( $module, '', $text, $function_name, $is_hover, $suffix );
			}

			// Check for text shadow settings in Advanced/Fields toggle
			if ( isset( $advanced_fields['fields'] ) && 'none' !== et_()->array_get( $all_values, 'fields_text_shadow_style', 'none' ) ) {
				// We have a preset selected which isn't none, need to add text-shadow style
				$fields = $advanced_fields['fields'];
				$this->update_styles( $module, 'fields', $fields, $function_name, $is_hover, $suffix );
			}

			// Check for text shadow settings in Advanced/Button toggle
			if ( ! empty( $advanced_fields['button'] ) ) {
				// We have a 'button' section, fetch its values
				foreach ( $advanced_fields['button'] as $label => $button ) {
					// label can be header / body / toggle / etc
					$shadow_style = "{$label}_text_shadow_style";

					if ( 'none' !== et_()->array_get( $all_values, $shadow_style, 'none' ) ) {
						// We have a preset selected which isn't none, need to add text-shadow style
						// Build a selector to only target the button
						$css_element = et_()->array_get( $button, 'css.main', "{$module->main_css_element} .et_pb_button" );
						// Make sure it has highest priority
						et_()->array_set( $button, 'css.text_shadow', $css_element );

						if ( ! isset( $button['css.hover'] ) ) {
							et_()->array_set( $button, 'css.hover', $hover->add_hover_to_selectors( $css_element ) );
						}

						$this->update_styles( $module, $label, $button, $function_name, $is_hover, $suffix );
					}
				}
			}

			// Check for text shadow settings in Advanced/Fields Input toggle
			if ( ! empty( $advanced_fields['form_field'] ) ) {
				// There are possibilities to have more than one field inputs.
				foreach ( $advanced_fields['form_field'] as $label => $form_field ) {
					// Ensure the text shadow style is selected before updating the styles.
					if ( 'none' !== et_()->array_get( $all_values, $label . '_text_shadow_style', 'none' ) ) {
						// Build a selector to only target the field input.
						$main_selector              = isset( $form_field['css']['main'] ) ? $form_field['css']['main'] : "{$module->main_css_element} .input";
						$text_shadow_selector       = isset( $form_field['css']['text_shadow'] ) ? $form_field['css']['text_shadow'] : $main_selector;
						$text_shadow_hover_selector = isset( $form_field['css']['text_shadow_hover'] ) ? $form_field['css']['text_shadow_hover'] : $hover->add_hover_to_selectors( $text_shadow_selector );

						// Make sure it has highest priority.
						$form_field['css']['text_shadow']       = $text_shadow_selector;
						$form_field['css']['text_shadow_hover'] = $text_shadow_hover_selector;

						// Check and override important status.
						if ( ! empty( $form_field['css']['important'] ) ) {
							$form_field_important = $form_field['css']['important'];
							if ( ! empty( $form_field_important['font'] ) ) {
								$form_field['css']['important'] = $form_field_important['font'];
							}

							if ( ! empty( $form_field_important['text_shadow'] ) ) {
								$form_field['css']['important'] = $form_field_important['text_shadow'];
							}
						}

						$this->update_styles( $module, $label, $form_field, $function_name, $is_hover, $suffix );
					}
				}
			}
		}

	}//end process_advanced_css()

	/**
	 * Determine if Text Shadow is used.
	 *
	 * @since 4.10.0
	 *
	 * @param array $attrs Module attributes/props.
	 *
	 * @return bool
	 */
	public function is_used( $attrs ) {
		foreach ( $attrs as $attr => $value ) {
			if ( ! $value ) {
				continue;
			}

			$is_attr = false !== strpos( $attr, 'text_shadow_' );

			if ( $is_attr ) {
				return true;
			}
		}

		return false;
	}

}

return new ET_Builder_Module_Field_TextShadow();

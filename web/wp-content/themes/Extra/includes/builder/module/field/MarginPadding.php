<?php

/**
 * Module Margin and Padding class.
 *
 * This is not how main module margin and padding is generated. Mostly used by other custom options
 * group such as Button and Field Input. Doesn't support selective padding yet.
 *
 * Copy of ET_Builder_Element::process_advanced_custom_margin_options().
 *
 * @since 3.23
 * @since 4.6.0 Add sticky style support
 */
class ET_Builder_Module_Field_MarginPadding extends ET_Builder_Module_Field_Base {

	/**
	 * True when Divi plugin is active.
	 *
	 * @since 3.23
	 *
	 * @var bool
	 */
	public $is_plugin_active = false;

	/**
	 * Margin padding properties.
	 *
	 * @since 3.23
	 *
	 * @var array
	 */
	public $properties;

	/**
	 * Margin padding config.
	 *
	 * @since 3.23
	 *
	 * @var array
	 */
	public $config;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->is_plugin_active = et_is_builder_plugin_active();
		$this->properties       = array(
			'custom_padding',
			'custom_padding_tablet',
			'custom_padding_phone',
			'custom_padding_last_edited',
			'custom_margin',
			'custom_margin_tablet',
			'custom_margin_phone',
			'custom_margin_last_edited',
			'padding_1_last_edited',
			'padding_2_last_edited',
			'padding_3_last_edited',
			'padding_4_last_edited',
		);
		$this->config           = array(
			'label'               => '',
			'prefix'              => '',
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'margin_padding',
			'sub_toggle'          => null,
			'option_category'     => 'layout',
			'depends_show_if'     => '',
			'depends_show_if_not' => '',
			'use_padding'         => true,
			'use_margin'          => true,
			'mobile_options'      => true,
			'sticky'              => true,
			'hover'               => 'tabs',
			'custom_padding'      => '',
			'custom_margin'       => '',
			'depends_show_if'     => 'on',
			'priority'            => 90,
		);
	}

	/**
	 * Returns prefixed field names.
	 *
	 * @since 3.23
	 *
	 * @param string $prefix Prefix.
	 *
	 * @return array
	 */
	public function get_prefixed_field_names( $prefix ) {
		$prefix = $prefix ? "{$prefix}_" : '';

		return array(
			"{$prefix}custom_padding",
			"{$prefix}custom_padding_tablet",
			"{$prefix}custom_padding_phone",
			"{$prefix}custom_padding_last_edited",
			"{$prefix}custom_margin",
			"{$prefix}custom_margin_tablet",
			"{$prefix}custom_margin_phone",
			"{$prefix}custom_margin_last_edited",
			"{$prefix}padding_1_last_edited",
			"{$prefix}padding_2_last_edited",
			"{$prefix}padding_3_last_edited",
			"{$prefix}padding_4_last_edited",
		);
	}

	/**
	 * Add selector prefix if needed.
	 *
	 * @since 3.23
	 *
	 * Custom margin & padding selector for button element. This is custom selector exist on
	 * ET_Builder_Element. We should do the samething to override hardcoded padding generated
	 * by button element.
	 *
	 * @see ET_Builder_Element::process_advanced_button_options
	 */
	public function get_prefixed_selector( $css_element, $type = '', $is_divi_builder_plugin = false ) {
		// See ET_Builder_Element->process_advanced_button_options() on generating $css_element_processed
		// for non Divi Builder Plugin. Explicitly add '.et_pb_section' to the selector so selector
		// splitting during prefixing does not incorrectly add third party classes before #et-boc.
		if ( 'button' === $type && ! $is_divi_builder_plugin ) {
			$css_element = "body #page-container .et_pb_section {$css_element}";
		}

		return $css_element;
	}

	/**
	 * Returns fields definition.
	 *
	 * @since 3.23
	 *
	 * @param array $args Field configuration.
	 *
	 * @return array
	 */
	public function get_fields( array $args = array() ) {
		$fields = array();
		$config = wp_parse_args( $args, $this->config );

		// Config details.
		$tab_slug    = $config['tab_slug'];
		$toggle_slug = $config['toggle_slug'];
		$sub_toggle  = $config['sub_toggle'];

		list(
			$custom_padding,
			$custom_padding_tablet,
			$custom_padding_phone,
			$custom_padding_last_edited,
			$custom_margin,
			$custom_margin_tablet,
			$custom_margin_phone,
			$custom_margin_last_edited,
			$padding_1_last_edited,
			$padding_2_last_edited,
			$padding_3_last_edited,
			$padding_4_last_edited,
		) = $this->get_prefixed_field_names( $config['prefix'] );

		// Custom margin.
		if ( $config['use_margin'] ) {
			$fields[ $custom_margin ]        = array(
				'label'           => sprintf( esc_html__( '%1$s Margin', 'et_builder' ), $config['label'] ),
				'description'     => esc_html__( 'Margin adds extra space to the outside of the element, increasing the distance between the element and other items on the page.', 'et_builder' ),
				'type'            => 'custom_margin',
				'option_category' => $config['option_category'],
				'mobile_options'  => $config['mobile_options'],
				'hover'           => $config['hover'],
				'sticky'          => $config['sticky'],
				'depends_show_if' => $config['depends_show_if'],
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'sub_toggle'      => $sub_toggle,
				'priority'        => $config['priority'],
			);
			$fields[ $custom_margin_tablet ] = array(
				'type'     => 'skip',
				'tab_slug' => $tab_slug,
			);
			$fields[ $custom_margin_phone ]  = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			// Make it possible to override/add options.
			if ( ! empty( $config['custom_margin'] ) ) {
				$fields[ $custom_margin ] = array_merge( $fields[ $custom_margin ], $config['custom_margin'] );
			}

			$fields[ $custom_margin_last_edited ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$fields[ $padding_1_last_edited ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$fields[ $padding_2_last_edited ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$fields[ $padding_3_last_edited ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$fields[ $padding_4_last_edited ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		// Custom padding.
		if ( $config['use_padding'] ) {
			$fields[ $custom_padding ]        = array(
				'label'           => sprintf( esc_html__( '%1$s Padding', 'et_builder' ), $config['label'] ),
				'description'     => esc_html__( 'Padding adds extra space to the inside of the element, increasing the distance between the edge of the element and its inner contents.', 'et_builder' ),
				'type'            => 'custom_padding',
				'option_category' => $config['option_category'],
				'mobile_options'  => $config['mobile_options'],
				'hover'           => $config['hover'],
				'sticky'          => $config['sticky'],
				'depends_show_if' => $config['depends_show_if'],
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'sub_toggle'      => $sub_toggle,
				'priority'        => $config['priority'],
			);
			if ( isset( $config['depends_on'] ) && '' !== $config['depends_on'] ) {
				$fields[ $custom_padding ]['depends_on'] = $config['depends_on'];
			}
			$fields[ $custom_padding_tablet ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$fields[ $custom_padding_phone ]  = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			// Make it possible to override/add options.
			if ( ! empty( $config['custom_padding'] ) ) {
				$fields[ $custom_padding ] = array_merge( $fields[ $custom_padding ], $config['custom_padding'] );
			}

			$fields[ $custom_padding_last_edited ] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		return $fields;
	}

	/**
	 * Adds CSS rule.
	 *
	 * @since 3.23
	 * @since 4.6.0 Add sticky style support
	 *
	 * @see ET_Builder_Element->process_advanced_custom_margin_options()
	 *
	 * @param ET_Builder_Element $module        Module object.
	 * @param string             $prefix        Label.
	 * @param array              $options       Field settings.
	 * @param string             $function_name Shortcode function.
	 * @param string             $type          Margin padding type.
	 */
	public function update_styles( $module, $prefix, $options, $function_name, $type ) {
		$utils           = ET_Core_Data_Utils::instance();
		$all_values      = $module->props;
		$hover           = et_pb_hover_options();
		$sticky          = et_pb_sticky_options();
		$responsive      = ET_Builder_Module_Helper_ResponsiveOptions::instance();
		$advanced_fields = $module->advanced_fields;
		$css             = isset( $this->advanced_fields['margin_padding']['css'] ) ? $this->advanced_fields['margin_padding']['css'] : array();

		// Conditional status.
		$is_divi_builder_plugin = et_is_builder_plugin_active();
		$is_important_set       = isset( $options['css']['important'] );
		$is_global_important    = $is_important_set && 'all' === $options['css']['important'];
		$is_use_padding         = $utils->array_get( $options, 'use_padding', true );
		$is_use_margin          = $utils->array_get( $options, 'use_margin', true );
		$is_sticky_module       = $sticky->is_sticky_module( $all_values );

		// Selectors.
		$main_selector    = ! empty( $options['css']['main'] ) ? $options['css']['main'] : $module->main_css_element;
		$limited_selector = ! empty( $options['css']['limited_main'] ) ? $options['css']['limited_main'] : '';
		$default_selector = $is_divi_builder_plugin && ! empty( $limited_selector ) ? $limited_selector : $main_selector;

		// Get important CSS list.
		$important_options = array();
		if ( $is_important_set && is_array( $options['css']['important'] ) ) {
			$important_options = $options['css']['important'];
		}

		// A. Padding.
		if ( $is_use_padding ) {
			// Padding Selectors.
			$padding_selector = ! empty( $options['css']['padding'] ) ? $options['css']['padding'] : $default_selector;

			$padding_selector_processed = $this->get_prefixed_selector( $padding_selector, $type, $is_divi_builder_plugin );
			if ( $is_divi_builder_plugin && ! empty( $limited_selector ) ) {
				$padding_selector_processed = $padding_selector;
			}

			// A.1. Responsive Padding.
			$is_padding_responsive = $responsive->is_responsive_enabled( $all_values, "{$prefix}_custom_padding" );

			$padding_desktop = $responsive->get_any_value( $all_values, "{$prefix}_custom_padding" );
			$padding_tablet  = $is_padding_responsive ? $responsive->get_any_value( $all_values, "{$prefix}_custom_padding_tablet" ) : '';
			$padding_phone   = $is_padding_responsive ? $responsive->get_any_value( $all_values, "{$prefix}_custom_padding_phone" ) : '';

			$important = in_array( 'custom_padding', $important_options ) || $is_global_important ? true : false;

			$padding_styles = array(
				'desktop' => '' !== $padding_desktop ? rtrim( et_builder_get_element_style_css( $padding_desktop, 'padding', $important ) ) : '',
				'tablet'  => '' !== $padding_tablet ? rtrim( et_builder_get_element_style_css( $padding_tablet, 'padding', $important ) ) : '',
				'phone'   => '' !== $padding_phone ? rtrim( et_builder_get_element_style_css( $padding_phone, 'padding', $important ) ) : '',
			);

			$responsive->declare_responsive_css( $padding_styles, $padding_selector_processed, $function_name, $module->get_style_priority() );

			// A.2. Hover Padding.
			$custom_padding_hover = $hover->get_value( "{$prefix}_custom_padding", $all_values, '' );

			if ( '' !== $custom_padding_hover && et_builder_is_hover_enabled( "{$prefix}_custom_padding", $all_values ) ) {

				$padding_hover_selector = $utils->array_get( $options, 'css.hover', $hover->add_hover_to_selectors( $padding_selector ) );

				$padding_hover_selector_processed = $this->get_prefixed_selector( $padding_hover_selector, $type, $is_divi_builder_plugin );
				if ( $is_divi_builder_plugin && ! empty( $limited_selector ) ) {
					$padding_hover_selector_processed = $padding_hover_selector;
				}

				$el_style = array(
					'selector'    => $padding_hover_selector_processed,
					'declaration' => rtrim( et_builder_get_element_style_css( $custom_padding_hover, 'padding', true ) ),
					'priority'    => 20,
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			// A.3. Sticky Padding.
			$custom_padding_sticky = $sticky->get_value( "{$prefix}_custom_padding", $all_values, '' );

			if ( '' !== $custom_padding_sticky && $sticky->is_enabled( "{$prefix}_custom_padding", $all_values ) ) {
				$padding_sticky_selector = $sticky->add_sticky_to_order_class(
					$padding_selector,
					$is_sticky_module
				);

				$padding_sticky_selector_processed = $this->get_prefixed_selector(
					$padding_sticky_selector,
					$type,
					$is_divi_builder_plugin
				);

				if ( $is_divi_builder_plugin && ! empty( $limited_selector ) ) {
					$padding_sticky_selector_processed = $padding_sticky_selector;
				}

				ET_Builder_Element::set_style(
					$function_name,
					array(
						'selector'    => $padding_sticky_selector_processed,
						'declaration' => rtrim(
							et_builder_get_element_style_css(
								$custom_padding_sticky,
								'padding',
								true
							)
						),
						'priority'    => 20,
					)
				);
			}
		}

		// B. Margin.
		if ( $is_use_margin ) {
			// Margin Selectors.
			$margin_selector = ! empty( $options['css']['margin'] ) ? $options['css']['margin'] : $default_selector;

			$margin_selector_processed = $this->get_prefixed_selector( $margin_selector, $type, $is_divi_builder_plugin );
			if ( $is_divi_builder_plugin && ! empty( $limited_selector ) ) {
				$margin_selector_processed = $margin_selector;
			}

			// A.1. Responsive margin.
			$is_margin_responsive = $responsive->is_responsive_enabled( $all_values, "{$prefix}_custom_margin" );

			$margin_desktop = $responsive->get_any_value( $all_values, "{$prefix}_custom_margin" );
			$margin_tablet  = $is_margin_responsive ? $responsive->get_any_value( $all_values, "{$prefix}_custom_margin_tablet" ) : '';
			$margin_phone   = $is_margin_responsive ? $responsive->get_any_value( $all_values, "{$prefix}_custom_margin_phone" ) : '';

			$important = in_array( 'custom_margin', $important_options ) || $is_global_important ? true : false;

			$margin_styles = array(
				'desktop' => '' !== $margin_desktop ? rtrim( et_builder_get_element_style_css( $margin_desktop, 'margin', $important ) ) : '',
				'tablet'  => '' !== $margin_tablet ? rtrim( et_builder_get_element_style_css( $margin_tablet, 'margin', $important ) ) : '',
				'phone'   => '' !== $margin_phone ? rtrim( et_builder_get_element_style_css( $margin_phone, 'margin', $important ) ) : '',
			);

			$responsive->declare_responsive_css( $margin_styles, $margin_selector_processed, $function_name, $module->get_style_priority() );

			// A.2. Hover margin.
			$custom_margin_hover = $hover->get_value( "{$prefix}_custom_margin", $all_values, '' );

			if ( '' !== $custom_margin_hover && et_builder_is_hover_enabled( "{$prefix}_custom_margin", $all_values ) ) {

				$margin_hover_selector = $utils->array_get( $options, 'css.hover', $hover->add_hover_to_selectors( $margin_selector ) );

				$margin_hover_selector_processed = $this->get_prefixed_selector( $margin_hover_selector, $type, $is_divi_builder_plugin );
				if ( $is_divi_builder_plugin && ! empty( $limited_selector ) ) {
					$margin_hover_selector_processed = $margin_hover_selector;
				}

				$el_style = array(
					'selector'    => $margin_hover_selector_processed,
					'declaration' => rtrim( et_builder_get_element_style_css( $custom_margin_hover, 'margin', true ) ),
					'priority'    => 20,
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			// A.3. Hover margin.
			$custom_margin_sticky = $sticky->get_value( "{$prefix}_custom_margin", $all_values, '' );

			if ( '' !== $custom_margin_sticky && $sticky->is_enabled( "{$prefix}_custom_margin", $all_values ) ) {

				$margin_sticky_selector = $sticky->add_sticky_to_order_class(
					$margin_selector,
					$is_sticky_module
				);

				$margin_sticky_selector_processed = $this->get_prefixed_selector(
					$margin_sticky_selector,
					$type,
					$is_divi_builder_plugin
				);

				if ( $is_divi_builder_plugin && ! empty( $limited_selector ) ) {
					$margin_sticky_selector_processed = $margin_sticky_selector;
				}

				ET_Builder_Element::set_style(
					$function_name,
					array(
						'selector'    => $margin_sticky_selector_processed,
						'declaration' => rtrim(
							et_builder_get_element_style_css(
								$custom_margin_sticky,
								'margin',
								true
							)
						),
						'priority'    => 20,
					)
				);
			}
		}
	}

	/**
	 * Process Margin & Padding options and adds CSS rules.
	 *
	 * @since 3.23
	 *
	 * @param ET_Builder_Element $module        Module object.
	 * @param string             $function_name Shortcode function.
	 */
	public function process_advanced_css( $module, $function_name ) {
		$utils           = ET_Core_Data_Utils::instance();
		$all_values      = $module->props;
		$advanced_fields = $module->advanced_fields;

		// Disable if module doesn't set advanced_fields property and has no VB support.
		if ( ! $module->has_vb_support() && ! $module->has_advanced_fields ) {
			return;
		}

		$allowed_advanced_fields = array( 'form_field', 'button', 'image_icon' );
		foreach ( $allowed_advanced_fields as $advanced_field ) {
			if ( empty( $advanced_fields[ $advanced_field ] ) ) {
				continue;
			}

			foreach ( $advanced_fields[ $advanced_field ] as $label => $form_field ) {
				$margin_key  = "{$label}_custom_margin";
				$padding_key = "{$label}_custom_padding";
				$multi_view  = et_pb_multi_view_options( $module );

				$has_margin         = '' !== $utils->array_get( $all_values, $margin_key, '' );
				$has_padding        = '' !== $utils->array_get( $all_values, $padding_key, '' );
				$has_margin_hover   = $multi_view->hover_is_enabled( $margin_key );
				$has_padding_hover  = $multi_view->hover_is_enabled( $padding_key );
				$has_padding_sticky = ! empty( et_pb_sticky_options()->get_value( "{$label}_custom_padding", $all_values, '' ) ) && et_pb_sticky_options()->is_enabled( "{$label}_custom_padding", $all_values );
				$has_margin_sticky  = ! empty( et_pb_sticky_options()->get_value( "{$label}_custom_margin", $all_values, '' ) ) && et_pb_sticky_options()->is_enabled( "{$label}_custom_margin", $all_values );

				if ( $has_margin || $has_padding || $has_margin_hover || $has_padding_hover || $has_padding_sticky || $has_margin_sticky ) {
					$settings = $utils->array_get( $form_field, 'margin_padding', array() );

					// Ensure main selector exists.
					$form_field_margin_padding_css = $utils->array_get( $settings, 'css.main', '' );
					if ( empty( $form_field_margin_padding_css ) ) {
						$utils->array_set( $settings, 'css.main', $utils->array_get( $form_field, 'css.main', '' ) );
					}

					$this->update_styles( $module, $label, $settings, $function_name, $advanced_field );
				}
			}
		}
	}

	/**
	 * Process Margin & Padding options and adds CSS rules.
	 *
	 * @since 4.10.0
	 * @param array $attrs Module attributes.
	 */
	public function is_used( $attrs ) {
		foreach ( $attrs as $attr => $value ) {
			if ( ! $value ) {
				continue;
			}

			$is_attr = false !== strpos( $attr, 'custom_margin' )
				|| false !== strpos( $attr, 'custom_padding' );

			if ( $is_attr ) {
				return true;
			}
		}

		return false;
	}
}

return new ET_Builder_Module_Field_MarginPadding();

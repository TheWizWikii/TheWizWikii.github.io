<?php

class ET_Builder_Module_Bar_Counters_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Bar Counter', 'et_builder' );
		$this->plural                      = esc_html__( 'Bar Counters', 'et_builder' );
		$this->slug                        = 'et_pb_counter';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'content';
		$this->advanced_setting_title_text = esc_html__( 'New Bar Counter', 'et_builder' );
		$this->settings_text               = esc_html__( 'Bar Counter Settings', 'et_builder' );
		$this->main_css_element            = '%%order_class%%';

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} span.et_pb_counter_container, {$this->main_css_element} span.et_pb_counter_amount",
							'border_styles' => "{$this->main_css_element} span.et_pb_counter_container",
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%% span.et_pb_counter_container',
						'overlay' => 'inset',
					),
				),
			),
			'fonts'          => array(
				'title'   => array(
					'label' => et_builder_i18n( 'Title' ),
					'css'   => array(
						'main' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_title",
					),
				),
				'percent' => array(
					'label' => esc_html__( 'Percentage', 'et_builder' ),
					'css'   => array(
						'main'       => ".et_pb_counters {$this->main_css_element} .et_pb_counter_amount_number",
						'text_align' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_amount",
					),
				),
			),
			'background'     => array(
				'use_background_color' => 'fields_only',
				'css'                  => array(
					'main'      => ".et_pb_counters li{$this->main_css_element} .et_pb_counter_container",
					'important' => 'all',
				),
			),
			'margin_padding' => array(
				'draggable_margin'  => false,
				'draggable_padding' => false,
				'css'               => array(
					'margin'  => ".et_pb_counters {$this->main_css_element}",
					'padding' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_amount",
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => ".et_pb_counters {$this->main_css_element}",
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => '%%order_class%% .et_pb_counter_title, %%order_class%% .et_pb_counter_amount',
				),
			),
			'button'         => false,
			'sticky'         => false,
			'height'         => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_counter_container, %%order_class%% .et_pb_counter_container .et_pb_counter_amount',
				),
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'bar' => esc_html__( 'Bar Counter', 'et_builder' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'counter_title'     => array(
				'label'    => esc_html__( 'Counter Title', 'et_builder' ),
				'selector' => '.et_pb_counter_title',
			),
			'counter_container' => array(
				'label'    => esc_html__( 'Counter Container', 'et_builder' ),
				'selector' => '.et_pb_counter_container',
			),
			'counter_amount'    => array(
				'label'    => esc_html__( 'Counter Amount', 'et_builder' ),
				'selector' => '.et_pb_counter_amount',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'content'              => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for your bar.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'percent'              => array(
				'label'            => esc_html__( 'Percent', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Define a percentage for this bar.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => '0',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'bar_background_color' => array(
				'label'          => esc_html__( 'Bar Background Color', 'et_builder' ),
				'description'    => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'hover'          => 'tabs',
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'bar',
				'mobile_options' => true,
				'sticky'         => true,
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['background_layout']    = array( 'color' => '%%order_class%% .et_pb_counter_title' );
		$fields['bar_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_counter_amount' );

		return $fields;
	}

	/**
	 * Checks if module has background.
	 *
	 * @since 4.15.0
	 *
	 * @return bool
	 */
	protected function _has_background() {
		global $et_pb_counters_settings;

		// Since background color might inherit from parent, checking for different value in child.
		$has_background_color      = ! empty( $this->props['background_color'] ) && ( isset( $et_pb_counters_settings['background_color'] ) && $this->props['background_color'] !== $et_pb_counters_settings['background_color'] );
		$has_background_gradient   = isset( $this->props['use_background_color_gradient'] ) && 'on' === $this->props['use_background_color_gradient'];
		$has_background_image      = ! empty( $this->props['background_image'] );
		$has_background_video_mp4  = ! empty( $this->props['background_video_mp4'] );
		$has_background_video_webm = ! empty( $this->props['background_video_webm'] );
		$has_background_pattern    = isset( $this->props['background_enable_pattern_style'] ) && 'on' === $this->props['background_enable_pattern_style'] && ! empty( $this->props['background_pattern_style'] );
		$has_background_mask       = isset( $this->props['background_enable_mask_style'] ) && 'on' === $this->props['background_enable_mask_style'] && ! empty( $this->props['background_mask_style'] );

		return $has_background_color
			|| $has_background_gradient
			|| $has_background_image
			|| $has_background_video_mp4
			|| $has_background_video_webm
			|| $has_background_pattern
			|| $has_background_mask;
	}

	/**
	 * Returns Parallax element.
	 *
	 * @since 4.15.0
	 *
	 * @return string JSX
	 */
	public function get_background_parallax() {
		global $et_pb_counters_settings;

		// Parallax settings derived from parent if bar counter item has no background.
		$background_props = $this->_has_background() ? $this->props : $et_pb_counters_settings;

		return $this->get_parallax_image_background( 'background', $background_props );
	}

	/**
	 * Returns Video element.
	 *
	 * @since 4.15.0
	 *
	 * @return string JSX
	 */
	public function get_background_video() {
		global $et_pb_counters_settings;

		// Video settings derived from parent if bar counter item has no background.
		$background_props = $this->_has_background() ? $this->props : $et_pb_counters_settings;

		return $this->video_background( $background_props );
	}

	/**
	 * Returns Pattern element.
	 *
	 * @since 4.15.0
	 *
	 * @return string JSX
	 */
	public function get_background_pattern() {
		global $et_pb_counters_settings;

		// Pattern settings derived from parent if bar counter item has no background.
		$background_props = $this->_has_background() ? $this->props : $et_pb_counters_settings;

		return $this->background_pattern( $background_props );
	}

	/**
	 * Returns Mask element.
	 *
	 * @since 4.15.0
	 *
	 * @return string JSX
	 */
	public function get_background_mask() {
		global $et_pb_counters_settings;

		// Pattern settings derived from parent if bar counter item has no background.
		$background_props = $this->_has_background() ? $this->props : $et_pb_counters_settings;

		return $this->background_mask( $background_props );
	}

	/**
	 * Set inheritance value for bar counters item.
	 *
	 * This method is introduced to inherit background colors values. There are some situations where not
	 * all inheritance process done here.
	 *
	 * @since 3.27.4
	 */
	function maybe_inherit_values() {
		global $et_pb_counters_settings;

		// To avoid unnecessary inheritance process, ensure to run this action on FE only.
		if ( ! empty( $et_pb_counters_settings ) && ! is_admin() && ! et_fb_is_enabled() ) {
			// Get parent and item background hover & responsive status.
			$is_background_hover             = et_pb_hover_options()->is_enabled( 'background', $this->props );
			$is_background_parent_hover      = et_pb_hover_options()->is_enabled( 'background', $et_pb_counters_settings );
			$is_background_responsive        = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'background' );
			$is_background_parent_responsive = et_pb_responsive_options()->is_responsive_enabled( $et_pb_counters_settings, 'background' );
			$is_inherit_parent_hover         = ! $is_background_hover && $is_background_parent_hover;
			$is_inherit_parent_responsive    = ! $is_background_responsive && $is_background_parent_responsive;

			// Background hover status.
			if ( $is_inherit_parent_hover ) {
				$this->props['background__hover_enabled'] = self::$_->array_get( $et_pb_counters_settings, 'background__hover_enabled', '' );
			}

			// Background responsive status.
			if ( $is_inherit_parent_responsive ) {
				$this->props['background_last_edited'] = self::$_->array_get( $et_pb_counters_settings, 'background_last_edited', '' );
			}

			// Background color and background color enable status.
			foreach ( array( 'background_color', 'background_enable_color' ) as $field ) {
				// Desktop. Simple inherit parent value if current item value is empty.
				$value                 = self::$_->array_get( $this->props, $field, '' );
				$parent_value          = self::$_->array_get( $et_pb_counters_settings, $field, '' );
				$this->props[ $field ] = empty( $value ) ? $parent_value : $value;

				// Hover. Inherit parent value only if current item hover is disabled.
				if ( $is_inherit_parent_hover ) {
					$this->props[ "{$field}__hover" ] = self::$_->array_get( $et_pb_counters_settings, "{$field}__hover", '' );
				}

				// Responsive. Inherit parent value only if current item responsive is disabled.
				if ( $is_inherit_parent_responsive ) {
					$this->props[ "{$field}_tablet" ] = self::$_->array_get( $et_pb_counters_settings, "{$field}_tablet", '' );
					$this->props[ "{$field}_phone" ]  = self::$_->array_get( $et_pb_counters_settings, "{$field}_phone", '' );
				}
			}
		}
	}

	/**
	 * Renders the module output.
	 *
	 * @param  array  $attrs       List of attributes.
	 * @param  string $content     Content being processed.
	 * @param  string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ) {
		global $et_pb_counters_settings;

		$multi_view = et_pb_multi_view_options( $this );
		$multi_view->set_custom_prop( 'use_percentages', $et_pb_counters_settings['use_percentages'] );

		$percent              = $multi_view->get_value( 'percent', 'desktop', '' );
		$bar_background_color = self::$_->array_get( $this->props, 'bar_background_color' );
		$bar_background_color = empty( $bar_background_color ) ? $et_pb_counters_settings['bar_bg_color'] : $bar_background_color;

		// Background Color.
		$background_color        = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_color', 'desktop' );
		$background_color_tablet = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_color', 'tablet' );
		$background_color_phone  = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_color', 'phone' );

		// Bar background color responsive. First of all, check if value from bar counters item is
		// exist and responsive setting is enabled. If it doesn't exist, get it from bar counters
		// and also ensure responsive setting is enabled.
		$is_bar_background_color_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'bar_background_color' );

		$bar_background_color_tablet = $is_bar_background_color_responsive ? et_pb_responsive_options()->get_any_value( $this->props, 'bar_background_color_tablet' ) : '';
		$bar_background_color_tablet = '' === $bar_background_color_tablet ? $et_pb_counters_settings['bar_bg_color_tablet'] : $bar_background_color_tablet;

		$bar_background_color_phone = $is_bar_background_color_responsive ? et_pb_responsive_options()->get_any_value( $this->props, 'bar_background_color_phone' ) : '';
		$bar_background_color_phone = '' === $bar_background_color_phone ? $et_pb_counters_settings['bar_bg_color_phone'] : $bar_background_color_phone;

		// Add % only if it hasn't been added to the attribute
		if ( '%' !== substr( trim( $percent ), -1 ) ) {
			$percent .= '%';
		}

		$background_color_style = $bar_bg_color_style = '';

		$background_image              = $this->props['background_image'];
		$use_background_color_gradient = $this->props['use_background_color_gradient'];
		$parent_bg_image               = isset( $et_pb_counters_settings['background_image'] ) ? $et_pb_counters_settings['background_image'] : '';
		$parent_use_bg_gradient        = isset( $et_pb_counters_settings['use_background_color_gradient'] ) ? $et_pb_counters_settings['use_background_color_gradient'] : 'off';
		$parent_enable_bg_image        = ! empty( $parent_bg_image ) || 'on' === $parent_use_bg_gradient;

		// In frontend, background color might inherit from parent.
		if ( $this->_has_background() && '' !== $this->props['background_color'] && $parent_enable_bg_image ) {
			// Need to override parent image that could be set, so inherited bg color can show up.
			if ( empty( $background_image ) && 'on' !== $use_background_color_gradient ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '.et_pb_counters %%order_class%% .et_pb_counter_container',
						'declaration' => 'background-image: none !important;',
					)
				);
			}
		}

		// Background color.
		$background_color_values = array(
			'desktop' => esc_html( $background_color ),
			'tablet'  => esc_html( $background_color_tablet ),
			'phone'   => esc_html( $background_color_phone ),
		);
		et_pb_responsive_options()->generate_responsive_css( $background_color_values, '.et_pb_counters %%order_class%% .et_pb_counter_container', 'background-color', $render_slug, ' !important; ', 'color' );

		// Bar background color.
		$bar_background_color_values = array(
			'desktop' => esc_html( $bar_background_color ),
			'tablet'  => esc_html( $bar_background_color_tablet ),
			'phone'   => esc_html( $bar_background_color_phone ),
		);

		foreach ( $bar_background_color_values as &$value ) {
			if ( ! empty( $value ) ) {
				$value = strtolower( et_builder_accent_color() ) === strtolower( $value ) ? $value : $value . ' !important';
			}
		}

		et_pb_responsive_options()->generate_responsive_css( $bar_background_color_values, '%%order_class%% .et_pb_counter_amount', 'background-color', $render_slug, '', 'color' );
		et_pb_responsive_options()->generate_responsive_css( $bar_background_color_values, '%%order_class%% .et_pb_counter_amount.overlay', 'color', $render_slug, '', 'color' );

		// Extended (hover & sticky) style of bar and its background color
		// Background Color.
		$this->generate_styles(
			array(
				'responsive'                      => false,
				'render_slug'                     => $render_slug,
				'base_attr_name'                  => 'background_color',
				'css_property'                    => 'background-color',
				'selector'                        => '.et_pb_counters %%order_class%% .et_pb_counter_container',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'is_sticky_module'                => $et_pb_counters_settings['is_sticky_module'],
			)
		);

		// Bar Background Color.
		$this->generate_styles(
			array(
				'responsive'                      => false,
				'render_slug'                     => $render_slug,
				'base_attr_name'                  => 'bar_background_color',
				'css_property'                    => 'background-color',
				'selector'                        => '.et_pb_counters %%order_class%% .et_pb_counter_amount',
				'hover_pseudo_selector_location'  => 'order_class',
				'sticky_pseudo_selector_location' => 'prefix',
				'is_sticky_module'                => $et_pb_counters_settings['is_sticky_module'],
			)
		);

		$this->generate_styles(
			array(
				'responsive'                      => false,
				'render_slug'                     => $render_slug,
				'attrs'                           => $this->props,
				'base_attr_name'                  => 'bar_background_color',
				'css_property'                    => 'color',
				'selector'                        => '.et_pb_counters %%order_class%% .et_pb_counter_amount.overlay',
				'hover_pseudo_selector_location'  => 'order_class',
				'sticky_pseudo_selector_location' => 'prefix',
				'is_sticky_module'                => $et_pb_counters_settings['is_sticky_module'],
			)
		);

		$video_background          = $this->get_background_video();
		$parallax_image_background = $this->get_background_parallax();
		$pattern_background        = $this->get_background_pattern();
		$mask_background           = $this->get_background_mask();

		// Module classname
		$this->add_classname( $this->get_text_orientation_classname() );

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				'et_pb_module',
				$render_slug,
			)
		);

		$multi_view_data_title = $multi_view->render_attrs(
			array(
				'content' => '{{content}}',
			)
		);

		$multi_view_data_percent_attrs = $multi_view->render_attrs(
			array(
				'attrs'  => array(
					'data-width' => '{{percent}}',
				),
				'target' => '%%order_class%% .et_pb_counter_amount',
			)
		);

		$multi_view_data_percent_content = $multi_view->render_attrs(
			array(
				'content'    => '{{percent}}',
				'visibility' => array(
					'use_percentages' => 'on',
				),
			)
		);

		$output = sprintf(
			'<li class="et_pb_counter %6$s">
				<span class="et_pb_counter_title"%9$s>%1$s</span>
				<span class="et_pb_counter_container"%4$s%10$s>
					%8$s
					%7$s
					%12$s
					%13$s
					<span class="et_pb_counter_amount" style="%5$s" data-width="%3$s"><span class="et_pb_counter_amount_number"><span class="et_pb_counter_amount_number_inner"%11$s>%2$s</span></span></span>
					<span class="et_pb_counter_amount overlay" style="%5$s" data-width="%3$s"><span class="et_pb_counter_amount_number"><span class="et_pb_counter_amount_number_inner"%11$s>%2$s</span></span></span>
				</span>
			</li>',
			sanitize_text_field( $content ), // #1
			$multi_view->has_value( 'use_percentages', 'on' ) ? esc_html( $percent ) : '',
			esc_attr( $percent ),
			$background_color_style,
			$bar_bg_color_style, // #5
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background,
			$multi_view_data_title,
			$multi_view_data_percent_attrs, // #10
			$multi_view_data_percent_content,
			et_core_esc_previously( $pattern_background ), // #12
			et_core_esc_previously( $mask_background ) // #13
		);

		return $output;
	}

	/**
	 * Filter multi view value.
	 *
	 * @since 3.27.1
	 *
	 * @see ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';

		if ( $raw_value && 'percent' === $name ) {
			if ( '%' !== substr( trim( $raw_value ), -1 ) ) {
				$raw_value .= '%';
			}
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Bar_Counters_Item();
}

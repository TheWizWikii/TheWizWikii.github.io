<?php

class ET_Builder_Module_Circle_Counter extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Circle Counter', 'et_builder' );
		$this->plural     = esc_html__( 'Circle Counters', 'et_builder' );
		$this->slug       = 'et_pb_circle_counter';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_circle_counter';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'circle' => et_builder_i18n( 'Circle' ),
					'text'   => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'title'  => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h3, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header, {$this->main_css_element} h3 a, {$this->main_css_element} h1.et_pb_module_header a, {$this->main_css_element} h2.et_pb_module_header a, {$this->main_css_element} h4.et_pb_module_header a, {$this->main_css_element} h5.et_pb_module_header a, {$this->main_css_element} h6.et_pb_module_header a",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'number' => array(
					'label'            => esc_html__( 'Number', 'et_builder' ),
					'hide_line_height' => true,
					'css'              => array(
						'main' => "{$this->main_css_element} .percent p",
					),
				),
			),
			'background'      => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_circle_counter_inner",
				),
			),
			'margin_padding'  => array(
				'css'           => array(
					'important' => array( 'custom_margin' ),
				),
				'custom_margin' => array(
					'default' => '0px|auto|30px|auto|false|false',
				),
			),
			'max_width'       => array(
				'options' => array(
					'max_width'        => array(
						'default'        => '225px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '450',
							'step' => '1',
						),
					),
					'module_alignment' => array(
						'depends_show_if_not' => array(
							'',
							'225px',
						),
					),
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main' => '%%order_class%% .percent p, %%order_class%% .et_pb_module_header',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default_on_front' => 'center',
					),
					'background_layout' => array(
						'default_on_front' => 'light',
					),
				),
			),
			'filters'         => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->custom_css_fields = array(
			'percent'              => array(
				'label'    => esc_html__( 'Percent Container', 'et_builder' ),
				'selector' => '.percent',
			),
			'circle_counter_title' => array(
				'label'    => esc_html__( 'Circle Counter Title', 'et_builder' ),
				'selector' => 'h3',
			),
			'percent_text'         => array(
				'label'    => esc_html__( 'Percent Text', 'et_builder' ),
				'selector' => '.percent p',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'GTslkWWbda0',
				'name' => esc_html__( 'An introduction to the Circle Counter module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'              => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the circle counter.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'number'             => array(
				'label'             => esc_html__( 'Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'number_validation' => true,
				'value_type'        => 'float',
				'value_min'         => 0,
				'value_max'         => 100,
				'description'       => et_get_safe_localization( __( "Define a number for the circle counter. (Don't include the percentage sign, use the option below.). <strong>Note: You can use only natural numbers from 0 to 100</strong>", 'et_builder' ) ),
				'toggle_slug'       => 'main_content',
				'default_on_front'  => '0',
				'mobile_options'    => true,
				'hover'             => 'tabs',
			),
			'percent_sign'       => array(
				'label'            => esc_html__( 'Percent Sign', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'et_builder' ),
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'bar_bg_color'       => array(
				'default'        => et_builder_accent_color(),
				'label'          => esc_html__( 'Circle Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'circle',
				'description'    => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'circle_color'       => array(
				'label'          => esc_html__( 'Circle Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used in the unfilled space of the circle.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'circle',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'circle_color_alpha' => array(
				'label'           => esc_html__( 'Circle Background Opacity', 'et_builder' ),
				'description'     => esc_html__( 'Decrease the opacity of the unfilled space of the circle to make the color fade into the background.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'range_settings'  => array(
					'min'       => '0.1',
					'max'       => '1.0',
					'step'      => '0.05',
					'min_limit' => '0.0',
					'max_limit' => '1.0',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'circle',
				'unitless'        => true,
				'mobile_options'  => true,
				'sticky'          => true,
				'hover'           => 'tabs',
			),
		);
		return $fields;
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

		$sticky                = et_pb_sticky_options();
		$multi_view            = et_pb_multi_view_options( $this );
		$number                = $multi_view->get_value( 'number', 'desktop', '' );
		$percent_sign          = $this->props['percent_sign'];
		$title                 = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['title_level'], 'h3' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_module_header',
				),
			)
		);
		$custom_padding        = $this->props['custom_padding'];
		$custom_padding_tablet = $this->props['custom_padding_tablet'];
		$custom_padding_phone  = $this->props['custom_padding_phone'];
		$header_level          = $this->props['title_level'];

		$bar_bg_color               = $this->props['bar_bg_color'];
		$bar_bg_color_values        = et_pb_responsive_options()->get_property_values( $this->props, 'bar_bg_color' );
		$bar_bg_color_tablet        = isset( $bar_bg_color_values['tablet'] ) ? $bar_bg_color_values['tablet'] : '';
		$bar_bg_color_phone         = isset( $bar_bg_color_values['phone'] ) ? $bar_bg_color_values['phone'] : '';
		$bar_bg_color_hover         = et_pb_hover_options()->get_value( 'bar_bg_color', $this->props, '' );
		$bar_bg_color_hover_enabled = et_builder_is_hover_enabled( 'bar_bg_color', $this->props );
		$bar_bg_color_sticky        = $sticky->get_value( 'bar_bg_color', $this->props, '' );

		$circle_color               = $this->props['circle_color'];
		$circle_color_values        = et_pb_responsive_options()->get_property_values( $this->props, 'circle_color' );
		$circle_color_tablet        = isset( $circle_color_values['tablet'] ) ? $circle_color_values['tablet'] : '';
		$circle_color_phone         = isset( $circle_color_values['phone'] ) ? $circle_color_values['phone'] : '';
		$circle_color_hover         = et_pb_hover_options()->get_value( 'circle_color', $this->props, '' );
		$circle_color_hover_enabled = et_builder_is_hover_enabled( 'circle_color', $this->props );
		$circle_color_sticky        = $sticky->get_value( 'circle_color', $this->props, '' );

		$circle_color_alpha              = $this->props['circle_color_alpha'];
		$circle_color_alpha_values       = et_pb_responsive_options()->get_property_values( $this->props, 'circle_color_alpha' );
		$circle_color_alpha_tablet       = isset( $circle_color_alpha_values['tablet'] ) ? $circle_color_alpha_values['tablet'] : '';
		$circle_color_alpha_phone        = isset( $circle_color_alpha_values['phone'] ) ? $circle_color_alpha_values['phone'] : '';
		$circle_color_alpha_hover        = et_pb_hover_options()->get_value( 'circle_color_alpha', $this->props, '' );
		$circle_color_alpha_hover_enable = et_builder_is_hover_enabled( 'circle_color_alpha', $this->props );
		$circle_color_alpha_sticky       = $sticky->get_value( 'circle_color_alpha', $this->props, '' );

		$number = str_ireplace( '%', '', $number );

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$bar_bg_color_data_tablet = '' !== $bar_bg_color_tablet ?
			sprintf( ' data-bar-bg-color-tablet="%1$s"', esc_attr( $bar_bg_color_tablet ) )
			: '';
		$bar_bg_color_data_phone  = '' !== $bar_bg_color_phone ?
			sprintf( ' data-bar-bg-color-phone="%1$s"', esc_attr( $bar_bg_color_phone ) )
			: '';
		$bar_bg_color_data_hover  = '' !== $bar_bg_color_hover && $bar_bg_color_hover_enabled ?
			sprintf( ' data-bar-bg-color-hover="%1$s"', esc_attr( $bar_bg_color_hover ) )
			: '';
		$bar_bg_color_data_sticky = '' !== $bar_bg_color_sticky ?
			sprintf( ' data-bar-bg-color-sticky="%1$s"', esc_attr( $bar_bg_color_sticky ) )
			: '';

		$circle_color_data        = '' !== $circle_color ?
			sprintf( ' data-color="%1$s"', esc_attr( $circle_color ) )
			: '';
		$circle_color_data_tablet = '' !== $circle_color_tablet ?
			sprintf( ' data-color-tablet="%1$s"', esc_attr( $circle_color_tablet ) )
			: '';
		$circle_color_data_phone  = '' !== $circle_color_phone ?
			sprintf( ' data-color-phone="%1$s"', esc_attr( $circle_color_phone ) )
			: '';
		$circle_color_data_hover  = '' !== $circle_color_hover && $circle_color_hover_enabled ?
			sprintf( ' data-color-hover="%1$s"', esc_attr( $circle_color_hover ) )
			: '';
		$circle_color_data_sticky = '' !== $circle_color_sticky ?
			sprintf( ' data-color-sticky="%1$s"', esc_attr( $circle_color_sticky ) )
			: '';

		$circle_color_alpha_data        = '' !== $circle_color_alpha ?
			sprintf( ' data-alpha="%1$s"', esc_attr( $circle_color_alpha ) )
			: '';
		$circle_color_alpha_data_tablet = '' !== $circle_color_alpha_tablet ?
			sprintf( ' data-alpha-tablet="%1$s"', esc_attr( $circle_color_alpha_tablet ) )
			: '';
		$circle_color_alpha_data_phone  = '' !== $circle_color_alpha_phone ?
			sprintf( ' data-alpha-phone="%1$s"', esc_attr( $circle_color_alpha_phone ) )
			: '';
		$circle_color_alpha_data_hover  = '' !== $circle_color_alpha_hover && $circle_color_alpha_hover_enable ?
			sprintf( ' data-alpha-hover="%1$s"', esc_attr( $circle_color_alpha_hover ) )
			: '';
		$circle_color_alpha_data_sticky = '' !== $circle_color_alpha_sticky ?
			sprintf( ' data-alpha-sticky="%1$s"', esc_attr( $circle_color_alpha_sticky ) )
			: '';

		// Sticky id.
		$data_sticky_id = $sticky->is_sticky_module( $this->props ) ?
			sprintf( ' data-sticky-id="%1$s"', esc_attr( $this->get_sticky_id( $render_slug ) ) )
			: '';

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$multi_view_data_attr = $multi_view->render_attrs(
			array(
				'attrs'   => array(
					'data-number-value' => '{{number}}',
					'data-percent-sign' => '{{percent_sign}}',
				),
				'classes' => array(
					'et_pb_with_title' => array(
						'title' => '__not_empty',
					),
				),
			)
		);

		// Module classnames
		$this->add_classname(
			array(
				'container-width-change-notify',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( '' !== $title ) {
			$this->add_classname( 'et_pb_with_title' );
		}

		// Check Background Image.
		$is_background_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'background' );
		$background_image         = $this->props['background_image'];
		$counter_inner_classname  = '';

		if ( '' === $background_image && $is_background_responsive ) {
			$background_image_tablet = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_image', 'tablet' );
			$background_image_phone  = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_image', 'phone' );
			$background_image        = '' !== $background_image_tablet ? $background_image_tablet : $background_image_phone;
		}

		// We need to add et_pb_with_background class for the et_pb_circle_counter_inner element,
		// when Background Image is used, so that would apply default styles for background image.
		if ( ! empty( $video_background ) || '' !== $background_image ) {
			$counter_inner_classname = ' et_pb_with_background';
		};

		$output = sprintf(
			'<div%1$s class="%2$s"%11$s>
				<div class="et_pb_circle_counter_inner%28$s" data-number-value="%3$s" data-bar-bg-color="%4$s"%7$s%8$s%12$s%13$s%14$s%15$s%16$s%17$s%18$s%19$s%20$s%21$s%22$s%23$s%24$s%25$s>
				%10$s
				%9$s
				%26$s
				%27$s
					<div class="percent"%19$s><p><span class="percent-value"></span><span class="percent-sign">%5$s</span></p></div>
					%6$s
				</div>
			</div>',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $number ),
			esc_attr( $bar_bg_color ),
			( 'on' == $multi_view->get_value( 'percent_sign' ) ? '%' : '' ), // #5
			et_core_esc_previously( $title ),
			$circle_color_data,
			$circle_color_alpha_data,
			$video_background,
			$parallax_image_background, // #10
			et_core_esc_previously( $data_background_layout ),
			$bar_bg_color_data_tablet,
			$bar_bg_color_data_phone,
			$circle_color_data_tablet,
			$circle_color_data_phone, // #15
			$circle_color_alpha_data_tablet,
			$circle_color_alpha_data_phone,
			$bar_bg_color_data_hover,
			$circle_color_data_hover,
			$circle_color_alpha_data_hover, // #20
			$multi_view_data_attr,
			$bar_bg_color_data_sticky,
			$circle_color_data_sticky,
			$circle_color_alpha_data_sticky,
			$data_sticky_id, // #25
			et_core_esc_previously( $this->background_pattern() ), // #26
			et_core_esc_previously( $this->background_mask() ), // #27
			esc_attr( $counter_inner_classname ) // #28
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
	 * @param mixed                                     $raw_value Props raw value.
	 * @param array                                     $args {
	 *                                         Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args, $multi_view ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$mode = isset( $args['mode'] ) ? $args['mode'] : '';

		if ( 'number' === $name ) {
			$raw_value = str_replace( array( '%' ), '', $raw_value );
		} elseif ( 'percent_sign' === $name ) {
			$raw_value = 'on' === $raw_value ? '%' : '&nbsp;';
		}

		$fields_need_escape = array(
			'title',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Circle_Counter();
}

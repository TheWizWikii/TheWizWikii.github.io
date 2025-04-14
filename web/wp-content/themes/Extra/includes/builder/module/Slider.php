<?php

require_once 'helpers/Slider.php';

class ET_Builder_Module_Slider extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Slider', 'et_builder' );
		$this->plural          = esc_html__( 'Sliders', 'et_builder' );
		$this->slug            = 'et_pb_slider';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_slide';
		$this->child_item_text = et_builder_i18n( 'Slide' );

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'elements' => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'overlay'    => et_builder_i18n( 'Overlay' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'image'      => et_builder_i18n( 'Image' ),
					'layout'     => et_builder_i18n( 'Layout' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'             => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title",
						'limited_main'     => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title, {$this->main_css_element} .et_pb_slide_description .et_pb_slide_title a",
						'font_size_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_description .et_pb_slide_title",
						'font_size_phone'  => "{$this->main_css_element} .et_pb_slides .et_pb_slide_description .et_pb_slide_title",
						'important'        => array( 'size', 'font-size', 'plugin_all' ),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'line_height'        => "{$this->main_css_element}",
						'main'               => "{$this->main_css_element} .et_pb_slide_content",
						'line_height_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'line_height_phone'  => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'font_size_tablet'   => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'font_size_phone'    => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'important'          => array( 'size', 'font-size' ),
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
			),
			'borders'         => array(
				'default' => array(),
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_slide_image img',
							'border_styles' => '%%order_class%% .et_pb_slide_image img',
						),
					),
					'label_prefix'    => et_builder_i18n( 'Image' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'depends_show_if' => 'off',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'      => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => '%%order_class%% .et_pb_slide_image img',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'button'          => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'alignment'    => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'background'      => array(
				'use_background_color'          => 'fields_only',
				'use_background_color_gradient' => 'fields_only',
				'use_background_image'          => 'fields_only',
				'options'                       => array(
					'parallax_method' => array(
						'default' => 'off',
					),
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'padding'   => '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'            => array(
				'css'     => array(
					'text_orientation' => '%%order_class%% .et_pb_slide .et_pb_slide_description',
					'text_shadow'      => '%%order_class%% .et_pb_slide .et_pb_slide_description',
				),
				'options' => array(
					'text_orientation' => array(
						'default' => 'center',
					),
				),
			),
			'height'          => array(
				'css' => array(
					'main' => '%%order_class%%, %%order_class%% .et_pb_slide',
				),
			),
			'image'           => array(
				'css' => array(
					'main' => array(
						'%%order_class%% .et_pb_slide_image',
						'%%order_class%% .et_pb_section_video_bg',
					),
				),
			),
			'max_width'       => array(
				'extra' => array(
					'content' => array(
						'use_module_alignment' => false,
						'css'                  => array(
							'main' => '%%order_class%% .et_pb_slide > .et_pb_container',
						),
						'options'              => array(
							'width'     => array(
								'label'   => esc_html__( 'Content Width', 'et_builder' ),
								'default' => '100%',
							),
							'max_width' => array(
								'label' => esc_html__( 'Content Max Width', 'et_builder' ),
							),
						),
					),
				),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
			'overflow'        => array(
				'default' => 'hidden',
			),
		);

		$this->custom_css_fields = array(
			'slide_description'       => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title'             => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_button'            => array(
				'label'                    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector'                 => '.et_pb_slider .et_pb_slide .et_pb_slide_description a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers'       => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image'             => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows'            => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '-YeoR2xSLOY',
				'name' => esc_html__( 'An introduction to the Slider module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_arrows'             => array(
				'label'            => esc_html__( 'Show Arrows', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_pagination'         => array(
				'label'            => esc_html__( 'Show Controls', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This setting will turn on and off the circle buttons at the bottom of the slider.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_content_on_mobile'  => array(
				'label'            => esc_html__( 'Show Content On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'tab_slug'         => 'custom_css',
				'toggle_slug'      => 'visibility',
			),
			'show_cta_on_mobile'      => array(
				'label'            => esc_html__( 'Show CTA On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'tab_slug'         => 'custom_css',
				'toggle_slug'      => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'            => esc_html__( 'Show Image / Video On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'tab_slug'         => 'custom_css',
				'toggle_slug'      => 'visibility',
			),
			'use_bg_overlay'          => array(
				'label'            => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'description'      => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
				),
				'default_on_front' => '',
				'affects'          => array(
					'bg_overlay_color',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'option_category'  => 'configuration',
			),
			'bg_overlay_color'        => array(
				'label'           => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'description'     => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'option_category' => 'configuration',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'use_text_overlay'        => array(
				'label'            => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'description'      => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
				),
				'default_on_front' => '',
				'affects'          => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'option_category'  => 'configuration',
			),
			'text_overlay_color'      => array(
				'label'           => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'description'     => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'option_category' => 'configuration',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'text_border_radius'      => array(
				'label'            => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'description'      => esc_html__( 'Increasing the border radius will increase the roundness of the overlay corners. Setting this value to 0 will result in squared corners.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '3',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'arrows_custom_color'     => array(
				'label'          => esc_html__( 'Arrow Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the slider arrows that are used to navigate through each slide.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'dot_nav_custom_color'    => array(
				'label'          => esc_html__( 'Dot Navigation Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the dot navigation that appears at the bottom of the slider to designate which slide is active.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['dot_nav_custom_color'] = array( 'background-color' => et_pb_slider_options()->get_dots_selector() );
		$fields['arrows_custom_color']  = array( 'all' => et_pb_slider_options()->get_arrows_selector() );

		return $fields;
	}

	function before_render() {
		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_custom_icon_tablet, $et_pb_slider_custom_icon_phone, $et_pb_slider_item_num, $et_pb_slider_button_rel;
		global $et_pb_slider_parent_type;

		$et_pb_slider_parent_type = $this->slug;
		$et_pb_slider_item_num    = 0;

		$sticky                 = et_pb_sticky_options();
		$parallax               = $this->props['parallax'];
		$parallax_method        = $this->props['parallax_method'];
		$show_content_on_mobile = $this->props['show_content_on_mobile'];
		$show_cta_on_mobile     = $this->props['show_cta_on_mobile'];
		$button_rel             = $this->props['button_rel'];
		$button_custom          = $this->props['custom_button'];

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$et_pb_slider_has_video = false;

		$et_pb_slider_parallax = $parallax;

		$et_pb_slider_parallax_method = $parallax_method;

		$et_pb_slider_show_mobile = array(
			'show_content_on_mobile' => $show_content_on_mobile,
			'show_cta_on_mobile'     => $show_cta_on_mobile,
		);

		$et_pb_slider_custom_icon        = 'on' === $button_custom ? $custom_icon : '';
		$et_pb_slider_custom_icon_tablet = 'on' === $button_custom ? $custom_icon_tablet : '';
		$et_pb_slider_custom_icon_phone  = 'on' === $button_custom ? $custom_icon_phone : '';

		$et_pb_slider_button_rel = $button_rel;

		// BG Overlay Color.
		$bg_overlay_color        = $this->props['bg_overlay_color'];
		$bg_overlay_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'bg_overlay_color' );
		$bg_overlay_color_tablet = isset( $bg_overlay_color_values['tablet'] ) ? $bg_overlay_color_values['tablet'] : '';
		$bg_overlay_color_phone  = isset( $bg_overlay_color_values['phone'] ) ? $bg_overlay_color_values['phone'] : '';

		// Text Overlay Color.
		$text_overlay_color        = $this->props['text_overlay_color'];
		$text_overlay_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'text_overlay_color' );
		$text_overlay_color_tablet = isset( $text_overlay_color_values['tablet'] ) ? $text_overlay_color_values['tablet'] : '';
		$text_overlay_color_phone  = isset( $text_overlay_color_values['phone'] ) ? $text_overlay_color_values['phone'] : '';

		// Text Border Radius.
		$text_border_radius        = $this->props['text_border_radius'];
		$text_border_radius_values = et_pb_responsive_options()->get_property_values( $this->props, 'text_border_radius' );
		$text_border_radius_tablet = isset( $text_border_radius_values['tablet'] ) ? $text_border_radius_values['tablet'] : '';
		$text_border_radius_phone  = isset( $text_border_radius_values['phone'] ) ? $text_border_radius_values['phone'] : '';

		// Arrows Color.
		$arrows_custom_color        = $this->props['arrows_custom_color'];
		$arrows_custom_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'arrows_custom_color' );
		$arrows_custom_color_tablet = isset( $arrows_custom_color_values['tablet'] ) ? $arrows_custom_color_values['tablet'] : '';
		$arrows_custom_color_phone  = isset( $arrows_custom_color_values['phone'] ) ? $arrows_custom_color_values['phone'] : '';

		// Dot Nav Custom Color.
		$dot_nav_custom_color        = $this->props['dot_nav_custom_color'];
		$dot_nav_custom_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'dot_nav_custom_color' );
		$dot_nav_custom_color_tablet = isset( $dot_nav_custom_color_values['tablet'] ) ? $dot_nav_custom_color_values['tablet'] : '';
		$dot_nav_custom_color_phone  = isset( $dot_nav_custom_color_values['phone'] ) ? $dot_nav_custom_color_values['phone'] : '';

		// Pass Slider Module setting to Slide Item.
		global $et_pb_slider;

		$et_pb_slider = array(
			'background_last_edited'                       => $this->props['background_last_edited'],
			'background__hover_enabled'                    => isset( $this->props['background__hover_enabled'] ) ? $this->props['background__hover_enabled'] : '',
			// Background Color.
			'background_enable_color'                      => $this->props['background_enable_color'],
			'background_enable_color_tablet'               => $this->props['background_enable_color_tablet'],
			'background_enable_color_phone'                => $this->props['background_enable_color_phone'],
			'background_enable_color__hover'               => isset( $this->props['background_enable_color__hover'] ) ? $this->props['background_enable_color__hover'] : '',
			'background_color'                             => $this->props['background_color'],
			'background_color_tablet'                      => $this->props['background_color_tablet'],
			'background_color_phone'                       => $this->props['background_color_phone'],
			'background_color__hover'                      => isset( $this->props['background_color__hover'] ) ? $this->props['background_color__hover'] : '',

			// Background Gradient.
			'use_background_color_gradient'                => $this->props['use_background_color_gradient'],
			'use_background_color_gradient_tablet'         => $this->props['use_background_color_gradient_tablet'],
			'use_background_color_gradient_phone'          => $this->props['use_background_color_gradient_phone'],
			'use_background_color_gradient__hover'         => isset( $this->props['use_background_color_gradient__hover'] )
				? $this->props['use_background_color_gradient__hover']
				: '',

			'background_color_gradient_repeat'             => isset( $this->props['background_color_gradient_repeat'] )
				? $this->props['background_color_gradient_repeat']
				: '',
			'background_color_gradient_repeat_tablet'      => isset( $this->props['background_color_gradient_repeat_tablet'] )
				? $this->props['background_color_gradient_repeat_tablet']
				: '',
			'background_color_gradient_repeat_phone'       => isset( $this->props['background_color_gradient_repeat_phone'] )
				? $this->props['background_color_gradient_repeat_phone']
				: '',
			'background_color_gradient_repeat__hover'      => isset( $this->props['background_color_gradient_repeat__hover'] )
				? $this->props['background_color_gradient_repeat__hover']
				: '',
			'background_color_gradient_repeat__sticky'     => isset( $this->props['background_color_gradient_repeat__sticky'] )
				? $this->props['background_color_gradient_repeat__sticky']
				: '',

			'background_color_gradient_type'               => $this->props['background_color_gradient_type'],
			'background_color_gradient_type_tablet'        => $this->props['background_color_gradient_type_tablet'],
			'background_color_gradient_type_phone'         => $this->props['background_color_gradient_type_phone'],
			'background_color_gradient_type__hover'        => isset( $this->props['background_color_gradient_type__hover'] )
				? $this->props['background_color_gradient_type__hover']
				: '',

			'background_color_gradient_direction'          => $this->props['background_color_gradient_direction'],
			'background_color_gradient_direction_tablet'   => $this->props['background_color_gradient_direction_tablet'],
			'background_color_gradient_direction_phone'    => $this->props['background_color_gradient_direction_phone'],
			'background_color_gradient_direction__hover'   => isset( $this->props['background_color_gradient_direction__hover'] )
				? $this->props['background_color_gradient_direction__hover']
				: '',

			'background_color_gradient_direction_radial'   => $this->props['background_color_gradient_direction_radial'],
			'background_color_gradient_direction_radial_tablet' => $this->props['background_color_gradient_direction_radial_tablet'],
			'background_color_gradient_direction_radial_phone' => $this->props['background_color_gradient_direction_radial_phone'],
			'background_color_gradient_direction_radial__hover' => isset( $this->props['background_color_gradient_direction_radial__hover'] )
				? $this->props['background_color_gradient_direction_radial__hover']
				: '',

			'background_color_gradient_stops'              => $this->props['background_color_gradient_stops'],
			'background_color_gradient_stops_tablet'       => $this->props['background_color_gradient_stops_tablet'],
			'background_color_gradient_stops_phone'        => $this->props['background_color_gradient_stops_phone'],
			'background_color_gradient_stops__hover'       => isset( $this->props['background_color_gradient_stops__hover'] )
				? $this->props['background_color_gradient_stops__hover']
				: '',
			'background_color_gradient_stops__sticky'      => isset( $this->props['background_color_gradient_stops__sticky'] )
				? $this->props['background_color_gradient_stops__sticky']
				: '',

			'background_color_gradient_overlays_image'     => $this->props['background_color_gradient_overlays_image'],
			'background_color_gradient_overlays_image_tablet' => $this->props['background_color_gradient_overlays_image_tablet'],
			'background_color_gradient_overlays_image_phone' => $this->props['background_color_gradient_overlays_image_phone'],
			'background_color_gradient_overlays_image__hover' => isset( $this->props['background_color_gradient_overlays_image__hover'] )
				? $this->props['background_color_gradient_overlays_image__hover']
				: '',

			'background_color_gradient_start'              => isset( $this->props['background_color_gradient_start'] )
				? $this->props['background_color_gradient_start']
				: '',
			'background_color_gradient_start_tablet'       => isset( $this->props['background_color_gradient_start_tablet'] )
				? $this->props['background_color_gradient_start_tablet']
				: '',
			'background_color_gradient_start_phone'        => isset( $this->props['background_color_gradient_start_phone'] )
				? $this->props['background_color_gradient_start_phone']
				: '',
			'background_color_gradient_start__hover'       => isset( $this->props['background_color_gradient_start__hover'] )
				? $this->props['background_color_gradient_start__hover']
				: '',

			'background_color_gradient_end'                => isset( $this->props['background_color_gradient_end'] )
				? $this->props['background_color_gradient_end']
				: '',
			'background_color_gradient_end_tablet'         => isset( $this->props['background_color_gradient_end_tablet'] )
				? $this->props['background_color_gradient_end_tablet']
				: '',
			'background_color_gradient_end_phone'          => isset( $this->props['background_color_gradient_end_phone'] )
				? $this->props['background_color_gradient_end_phone']
				: '',
			'background_color_gradient_end__hover'         => isset( $this->props['background_color_gradient_end__hover'] )
				? $this->props['background_color_gradient_end__hover']
				: '',

			'background_color_gradient_start_position'     => isset( $this->props['background_color_gradient_start_position'] )
				? $this->props['background_color_gradient_start_position']
				: '',
			'background_color_gradient_start_position_tablet' => isset( $this->props['background_color_gradient_start_position_tablet'] )
				? $this->props['background_color_gradient_start_position_tablet']
				: '',
			'background_color_gradient_start_position_phone' => isset( $this->props['background_color_gradient_start_position_phone'] )
				? $this->props['background_color_gradient_start_position_phone']
				: '',
			'background_color_gradient_start_position__hover' => isset( $this->props['background_color_gradient_start_position__hover'] )
				? $this->props['background_color_gradient_start_position__hover']
				: '',

			'background_color_gradient_end_position'       => isset( $this->props['background_color_gradient_end_position'] )
				? $this->props['background_color_gradient_end_position']
				: '',
			'background_color_gradient_end_position_tablet' => isset( $this->props['background_color_gradient_end_position_tablet'] )
				? $this->props['background_color_gradient_end_position_tablet']
				: '',
			'background_color_gradient_end_position_phone' => isset( $this->props['background_color_gradient_end_position_phone'] )
				? $this->props['background_color_gradient_end_position_phone']
				: '',
			'background_color_gradient_end_position__hover' => isset( $this->props['background_color_gradient_end_position__hover'] )
				? $this->props['background_color_gradient_end_position__hover']
				: '',

			// Background Image.
			'background_enable_image'                      => $this->props['background_enable_image'],
			'background_enable_image_tablet'               => $this->props['background_enable_image_tablet'],
			'background_enable_image_phone'                => $this->props['background_enable_image_phone'],
			'background_enable_image__hover'               => isset( $this->props['background_enable_image__hover'] ) ? $this->props['background_enable_image__hover'] : '',
			'background_image'                             => $this->props['background_image'],
			'background_image_tablet'                      => $this->props['background_image_tablet'],
			'background_image_phone'                       => $this->props['background_image_phone'],
			'background_image__hover'                      => isset( $this->props['background_image__hover'] ) ? $this->props['background_image__hover'] : '',
			'background_size'                              => $this->props['background_size'],
			'background_size_tablet'                       => $this->props['background_size_tablet'],
			'background_size_phone'                        => $this->props['background_size_phone'],
			'background_size__hover'                       => isset( $this->props['background_size__hover'] ) ? $this->props['background_size__hover'] : '',
			'background_position'                          => $this->props['background_position'],
			'background_position_tablet'                   => $this->props['background_position_tablet'],
			'background_position_phone'                    => $this->props['background_position_phone'],
			'background_position__hover'                   => isset( $this->props['background_position__hover'] ) ? $this->props['background_position__hover'] : '',
			'background_repeat'                            => $this->props['background_repeat'],
			'background_repeat_tablet'                     => $this->props['background_repeat_tablet'],
			'background_repeat_phone'                      => $this->props['background_repeat_phone'],
			'background_repeat__hover'                     => isset( $this->props['background_repeat__hover'] ) ? $this->props['background_repeat__hover'] : '',
			'background_blend'                             => $this->props['background_blend'],
			'background_blend_tablet'                      => $this->props['background_blend_tablet'],
			'background_blend_phone'                       => $this->props['background_blend_phone'],
			'background_blend__hover'                      => isset( $this->props['background_blend__hover'] ) ? $this->props['background_blend__hover'] : '',
			'parallax'                                     => $this->props['parallax'],
			'parallax_tablet'                              => $this->props['parallax_tablet'],
			'parallax_phone'                               => $this->props['parallax_phone'],
			'parallax__hover'                              => isset( $this->props['parallax__hover'] ) ? $this->props['parallax__hover'] : '',
			'parallax_method'                              => $this->props['parallax_method'],
			'parallax_method_tablet'                       => $this->props['parallax_method_tablet'],
			'parallax_method_phone'                        => $this->props['parallax_method_phone'],
			'parallax_method__hover'                       => isset( $this->props['parallax_method__hover'] ) ? $this->props['parallax_method__hover'] : '',
			// Background Video.
			'background_enable_video_mp4'                  => $this->props['background_enable_video_mp4'],
			'background_enable_video_mp4_tablet'           => $this->props['background_enable_video_mp4_tablet'],
			'background_enable_video_mp4_phone'            => $this->props['background_enable_video_mp4_phone'],
			'background_enable_video_mp4__hover'           => isset( $this->props['background_enable_video_mp4__hover'] ) ? $this->props['background_enable_video_mp4__hover'] : '',
			'background_enable_video_webm'                 => $this->props['background_enable_video_webm'],
			'background_enable_video_webm_tablet'          => $this->props['background_enable_video_webm_tablet'],
			'background_enable_video_webm_phone'           => $this->props['background_enable_video_webm_phone'],
			'background_enable_video_webm__hover'          => isset( $this->props['background_enable_video_webm__hover'] ) ? $this->props['background_enable_video_webm__hover'] : '',
			'background_video_mp4'                         => $this->props['background_video_mp4'],
			'background_video_mp4_tablet'                  => $this->props['background_video_mp4_tablet'],
			'background_video_mp4_phone'                   => $this->props['background_video_mp4_phone'],
			'background_video_mp4__hover'                  => isset( $this->props['background_video_mp4__hover'] ) ? $this->props['background_video_mp4__hover'] : '',
			'background_video_webm'                        => $this->props['background_video_webm'],
			'background_video_webm_tablet'                 => $this->props['background_video_webm_tablet'],
			'background_video_webm_phone'                  => $this->props['background_video_webm_phone'],
			'background_video_webm__hover'                 => isset( $this->props['background_video_webm__hover'] ) ? $this->props['background_video_webm__hover'] : '',
			'background_video_width'                       => $this->props['background_video_width'],
			'background_video_width_tablet'                => $this->props['background_video_width_tablet'],
			'background_video_width_phone'                 => $this->props['background_video_width_phone'],
			'background_video_width__hover'                => isset( $this->props['background_video_width__hover'] ) ? $this->props['background_video_width__hover'] : '',
			'background_video_height'                      => $this->props['background_video_height'],
			'background_video_height_tablet'               => $this->props['background_video_height_tablet'],
			'background_video_height_phone'                => $this->props['background_video_height_phone'],
			'background_video_height__hover'               => isset( $this->props['background_video_height__hover'] ) ? $this->props['background_video_height__hover'] : '',
			// Background Pattern.
			'background_pattern_style'                     => $this->props['background_pattern_style'],
			'background_pattern_style_tablet'              => $this->props['background_pattern_style_tablet'],
			'background_pattern_style_phone'               => $this->props['background_pattern_style_phone'],
			'background_pattern_style__hover'              => isset( $this->props['background_pattern_style__hover'] ) ? $this->props['background_pattern_style__hover'] : '',
			'background_pattern_color'                     => $this->props['background_pattern_color'],
			'background_pattern_color_tablet'              => $this->props['background_pattern_color_tablet'],
			'background_pattern_color_phone'               => $this->props['background_pattern_color_phone'],
			'background_pattern_color__hover'              => isset( $this->props['background_pattern_color__hover'] ) ? $this->props['background_pattern_color__hover'] : '',
			'background_pattern_transform'                 => $this->props['background_pattern_transform'],
			'background_pattern_transform_tablet'          => $this->props['background_pattern_transform_tablet'],
			'background_pattern_transform_phone'           => $this->props['background_pattern_transform_phone'],
			'background_pattern_transform__hover'          => isset( $this->props['background_pattern_transform__hover'] ) ? $this->props['background_pattern_transform__hover'] : '',
			'background_pattern_size'                      => $this->props['background_pattern_size'],
			'background_pattern_size_tablet'               => $this->props['background_pattern_size_tablet'],
			'background_pattern_size_phone'                => $this->props['background_pattern_size_phone'],
			'background_pattern_size__hover'               => isset( $this->props['background_pattern_size__hover'] ) ? $this->props['background_pattern_size__hover'] : '',
			'background_pattern_width'                     => $this->props['background_pattern_width'],
			'background_pattern_width_tablet'              => $this->props['background_pattern_width_tablet'],
			'background_pattern_width_phone'               => $this->props['background_pattern_width_phone'],
			'background_pattern_width__hover'              => isset( $this->props['background_pattern_width__hover'] ) ? $this->props['background_pattern_width__hover'] : '',
			'background_pattern_height'                    => $this->props['background_pattern_height'],
			'background_pattern_height_tablet'             => $this->props['background_pattern_height_tablet'],
			'background_pattern_height_phone'              => $this->props['background_pattern_height_phone'],
			'background_pattern_height__hover'             => isset( $this->props['background_pattern_height__hover'] ) ? $this->props['background_pattern_height__hover'] : '',
			'background_pattern_repeat_origin'             => $this->props['background_pattern_repeat_origin'],
			'background_pattern_repeat_origin_tablet'      => $this->props['background_pattern_repeat_origin_tablet'],
			'background_pattern_repeat_origin_phone'       => $this->props['background_pattern_repeat_origin_phone'],
			'background_pattern_repeat_origin__hover'      => isset( $this->props['background_pattern_repeat_origin__hover'] ) ? $this->props['background_pattern_repeat_origin__hover'] : '',
			'background_pattern_horizontal_offset'         => $this->props['background_pattern_horizontal_offset'],
			'background_pattern_horizontal_offset_tablet'  => $this->props['background_pattern_horizontal_offset_tablet'],
			'background_pattern_horizontal_offset_phone'   => $this->props['background_pattern_horizontal_offset_phone'],
			'background_pattern_horizontal_offset__hover'  => isset( $this->props['background_pattern_horizontal_offset__hover'] ) ? $this->props['background_pattern_horizontal_offset__hover'] : '',
			'background_pattern_vertical_offset'           => $this->props['background_pattern_vertical_offset'],
			'background_pattern_vertical_offset_tablet'    => $this->props['background_pattern_vertical_offset_tablet'],
			'background_pattern_vertical_offset_phone'     => $this->props['background_pattern_vertical_offset_phone'],
			'background_pattern_vertical_offset__hover'    => isset( $this->props['background_pattern_vertical_offset__hover'] ) ? $this->props['background_pattern_vertical_offset__hover'] : '',
			'background_pattern_repeat'                    => $this->props['background_pattern_repeat'],
			'background_pattern_repeat_tablet'             => $this->props['background_pattern_repeat_tablet'],
			'background_pattern_repeat_phone'              => $this->props['background_pattern_repeat_phone'],
			'background_pattern_repeat__hover'             => isset( $this->props['background_pattern_repeat__hover'] ) ? $this->props['background_pattern_repeat__hover'] : '',
			'background_pattern_blend_mode'                => $this->props['background_pattern_blend_mode'],
			'background_pattern_blend_mode_tablet'         => $this->props['background_pattern_blend_mode_tablet'],
			'background_pattern_blend_mode_phone'          => $this->props['background_pattern_blend_mode_phone'],
			'background_pattern_blend_mode__hover'         => isset( $this->props['background_pattern_blend_mode__hover'] ) ? $this->props['background_pattern_blend_mode__hover'] : '',
			'background_enable_pattern_style'              => $this->props['background_enable_pattern_style'],
			'background_enable_pattern_style_tablet'       => $this->props['background_enable_pattern_style_tablet'],
			'background_enable_pattern_style_phone'        => $this->props['background_enable_pattern_style_phone'],
			'background_enable_pattern_style__hover'       => isset( $this->props['background_enable_pattern_style__hover'] ) ? $this->props['background_enable_pattern_style__hover'] : '',
			// Background Mask.
			'background_mask_style'                        => $this->props['background_mask_style'],
			'background_mask_style_tablet'                 => $this->props['background_mask_style_tablet'],
			'background_mask_style_phone'                  => $this->props['background_mask_style_phone'],
			'background_mask_style__hover'                 => isset( $this->props['background_mask_style__hover'] ) ? $this->props['background_mask_style__hover'] : '',
			'background_mask_color'                        => $this->props['background_mask_color'],
			'background_mask_color_tablet'                 => $this->props['background_mask_color_tablet'],
			'background_mask_color_phone'                  => $this->props['background_mask_color_phone'],
			'background_mask_color__hover'                 => isset( $this->props['background_mask_color__hover'] ) ? $this->props['background_mask_color__hover'] : '',
			'background_mask_transform'                    => $this->props['background_mask_transform'],
			'background_mask_transform_tablet'             => $this->props['background_mask_transform_tablet'],
			'background_mask_transform_phone'              => $this->props['background_mask_transform_phone'],
			'background_mask_transform__hover'             => isset( $this->props['background_mask_transform__hover'] ) ? $this->props['background_mask_transform__hover'] : '',
			'background_mask_aspect_ratio'                 => $this->props['background_mask_aspect_ratio'],
			'background_mask_aspect_ratio_tablet'          => $this->props['background_mask_aspect_ratio_tablet'],
			'background_mask_aspect_ratio_phone'           => $this->props['background_mask_aspect_ratio_phone'],
			'background_mask_aspect_ratio__hover'          => isset( $this->props['background_mask_aspect_ratio__hover'] ) ? $this->props['background_mask_aspect_ratio__hover'] : '',
			'background_mask_size'                         => $this->props['background_mask_size'],
			'background_mask_size_tablet'                  => $this->props['background_mask_size_tablet'],
			'background_mask_size_phone'                   => $this->props['background_mask_size_phone'],
			'background_mask_size__hover'                  => isset( $this->props['background_mask_size__hover'] ) ? $this->props['background_mask_size__hover'] : '',
			'background_mask_width'                        => $this->props['background_mask_width'],
			'background_mask_width_tablet'                 => $this->props['background_mask_width_tablet'],
			'background_mask_width_phone'                  => $this->props['background_mask_width_phone'],
			'background_mask_width__hover'                 => isset( $this->props['background_mask_width__hover'] ) ? $this->props['background_mask_width__hover'] : '',
			'background_mask_height'                       => $this->props['background_mask_height'],
			'background_mask_height_tablet'                => $this->props['background_mask_height_tablet'],
			'background_mask_height_phone'                 => $this->props['background_mask_height_phone'],
			'background_mask_height__hover'                => isset( $this->props['background_mask_height__hover'] ) ? $this->props['background_mask_height__hover'] : '',
			'background_mask_position'                     => $this->props['background_mask_position'],
			'background_mask_position_tablet'              => $this->props['background_mask_position_tablet'],
			'background_mask_position_phone'               => $this->props['background_mask_position_phone'],
			'background_mask_position__hover'              => isset( $this->props['background_mask_position__hover'] ) ? $this->props['background_mask_position__hover'] : '',
			'background_mask_horizontal_offset'            => $this->props['background_mask_horizontal_offset'],
			'background_mask_horizontal_offset_tablet'     => $this->props['background_mask_horizontal_offset_tablet'],
			'background_mask_horizontal_offset_phone'      => $this->props['background_mask_horizontal_offset_phone'],
			'background_mask_horizontal_offset__hover'     => isset( $this->props['background_mask_horizontal_offset__hover'] ) ? $this->props['background_mask_horizontal_offset__hover'] : '',
			'background_mask_vertical_offset'              => $this->props['background_mask_vertical_offset'],
			'background_mask_vertical_offset_tablet'       => $this->props['background_mask_vertical_offset_tablet'],
			'background_mask_vertical_offset_phone'        => $this->props['background_mask_vertical_offset_phone'],
			'background_mask_vertical_offset__hover'       => isset( $this->props['background_mask_vertical_offset__hover'] ) ? $this->props['background_mask_vertical_offset__hover'] : '',
			'background_mask_blend_mode'                   => $this->props['background_mask_blend_mode'],
			'background_mask_blend_mode_tablet'            => $this->props['background_mask_blend_mode_tablet'],
			'background_mask_blend_mode_phone'             => $this->props['background_mask_blend_mode_phone'],
			'background_mask_blend_mode__hover'            => isset( $this->props['background_mask_blend_mode__hover'] ) ? $this->props['background_mask_blend_mode__hover'] : '',
			'background_enable_mask_style'                 => $this->props['background_enable_mask_style'],
			'background_enable_mask_style_tablet'          => $this->props['background_enable_mask_style_tablet'],
			'background_enable_mask_style_phone'           => $this->props['background_enable_mask_style_phone'],
			'background_enable_mask_style__hover'          => isset( $this->props['background_enable_mask_style__hover'] ) ? $this->props['background_enable_mask_style__hover'] : '',
			'header_level'                                 => $this->props['header_level'],
			'use_bg_overlay'                               => $this->props['use_bg_overlay'],
			'bg_overlay_color'                             => $bg_overlay_color,
			'bg_overlay_color_slider_last_edited'          => $this->props['bg_overlay_color_last_edited'],
			'bg_overlay_color_tablet'                      => $bg_overlay_color_tablet,
			'bg_overlay_color_phone'                       => $bg_overlay_color_phone,
			'bg_overlay_color__sticky'                     => $sticky->get_value( 'bg_overlay_color', $this->props ),
			'use_text_overlay'                             => $this->props['use_text_overlay'],
			'text_overlay_color'                           => $text_overlay_color,
			'text_overlay_color_slider_last_edited'        => $this->props['text_overlay_color_last_edited'],
			'text_overlay_color_tablet'                    => $text_overlay_color_tablet,
			'text_overlay_color_phone'                     => $text_overlay_color_phone,
			'text_overlay_color__sticky'                   => $sticky->get_value( 'text_overlay_color', $this->props ),
			'text_border_radius'                           => $text_border_radius,
			'text_border_radius_slider_last_edited'        => $this->props['text_border_radius_last_edited'],
			'text_border_radius_tablet'                    => $text_border_radius_tablet,
			'text_border_radius_phone'                     => $text_border_radius_phone,
			'text_border_radius__sticky'                   => $sticky->get_value( 'text_border_radius', $this->props ),
			'arrows_custom_color'                          => $arrows_custom_color,
			'arrows_custom_color_slider_last_edited'       => $this->props['arrows_custom_color_last_edited'],
			'arrows_custom_color_tablet'                   => $arrows_custom_color_tablet,
			'arrows_custom_color_phone'                    => $arrows_custom_color_phone,
			'arrows_custom_color__sticky'                  => $sticky->get_value( 'arrows_custom_color', $this->props ),
			'dot_nav_custom_color'                         => $dot_nav_custom_color,
			'dot_nav_custom_color_slider_last_edited'      => $this->props['dot_nav_custom_color_last_edited'],
			'dot_nav_custom_color_tablet'                  => $dot_nav_custom_color_tablet,
			'dot_nav_custom_color_phone'                   => $dot_nav_custom_color_phone,
			'dot_nav_custom_color__sticky'                 => $sticky->get_value( 'dot_nav_custom_color', $this->props ),

			// Sticky classname position relies to slider's sticky status if the style selector
			// begins with slider-level selector.
			'is_sticky_module'                             => $sticky->is_sticky_module( $this->props ),

			// Module item has no sticky options hence this needs to be inherited to setup transition.
			'sticky_transition'                            => et_()->array_get( $this->props, 'sticky_transition', 'on' ),
		);

		// Hover Options attribute doesn't have field definition and rendered on the fly, thus the use of array_get()
		$background_hover_enabled_key = et_pb_hover_options()->get_hover_enabled_field( 'background' );
		$background_color_hover_key   = et_pb_hover_options()->get_hover_field( 'background_color' );

		$et_pb_slider[ $background_hover_enabled_key ] = self::$_->array_get( $this->props, $background_hover_enabled_key, '' );
		$et_pb_slider[ $background_color_hover_key ]   = self::$_->array_get( $this->props, $background_color_hover_key, '' );
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
		$multi_view              = et_pb_multi_view_options( $this );
		$show_arrows             = $this->props['show_arrows'];
		$show_pagination         = $this->props['show_pagination'];
		$parallax                = $this->props['parallax'];
		$parallax_method         = $this->props['parallax_method'];
		$auto                    = $this->props['auto'];
		$auto_speed              = $this->props['auto_speed'];
		$auto_ignore_hover       = $this->props['auto_ignore_hover'];
		$body_font_size          = $this->props['body_font_size'];
		$show_content_on_mobile  = $this->props['show_content_on_mobile'];
		$show_cta_on_mobile      = $this->props['show_cta_on_mobile'];
		$show_image_video_mobile = $this->props['show_image_video_mobile'];
		$background_position     = $this->props['background_position'];
		$background_size         = $this->props['background_size'];

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_custom_icon_tablet, $et_pb_slider_custom_icon_phone, $et_pb_slider;

		$content = $this->content;

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		if ( '' !== $background_position && 'default' !== $background_position && 'off' === $parallax ) {
			$processed_position = str_replace( '_', ' ', $background_position );

			ET_Builder_Module::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-position: %1$s;',
						esc_html( $processed_position )
					),
				)
			);
		}

		// Handle slider's previous background size default value ("default") as well
		if ( '' !== $background_size && 'default' !== $background_size && 'off' === $parallax ) {
			$el_style = array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'-moz-background-size: %1$s;
					-webkit-background-size: %1$s;
					background-size: %1$s;',
					esc_html( $background_size )
				),
			);
			ET_Builder_Module::set_style( $render_slug, $el_style );
		}

		// Module classnames
		$this->add_classname( 'et_pb_slider_fullwidth_off' );

		if ( ! $multi_view->has_value( 'show_arrows', 'on' ) ) {
			$this->add_classname( 'et_pb_slider_no_arrows' );
		}

		if ( ! $multi_view->has_value( 'show_pagination', 'on' ) ) {
			$this->add_classname( 'et_pb_slider_no_pagination' );
		}

		if ( 'on' === $parallax ) {
			$this->add_classname( 'et_pb_slider_parallax' );
		}

		if ( 'on' === $auto ) {
			$this->add_classname(
				array(
					'et_slider_auto',
					"et_slider_speed_{$auto_speed}",
				)
			);
		}

		if ( 'on' === $auto_ignore_hover ) {
			$this->add_classname( 'et_slider_auto_ignore_hover' );
		}

		if ( 'on' === $show_image_video_mobile ) {
			$this->add_classname( 'et_pb_slider_show_image' );
		}

		$this->generate_responsive_hover_style( 'arrows_custom_color', et_pb_slider_options()->get_arrows_selector(), 'color' );
		$this->generate_responsive_hover_style( 'dot_nav_custom_color', et_pb_slider_options()->get_dots_selector(), 'background-color' );

		$multi_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_slider_no_arrows'     => array(
						'show_arrows' => 'off',
					),
					'et_pb_slider_no_pagination' => array(
						'show_pagination' => 'off',
					),
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%1$s"%5$s>
				<div class="et_pb_slides">
					%2$s
				</div>
				%4$s
			</div>
			',
			$this->module_classname( $render_slug ),
			$content,
			$this->module_id(),
			$this->inner_shadow_back_compatibility( $render_slug ),
			$multi_view_data_attr
		);

		// Reset passed slider item value
		$et_pb_slider = array();

		return $output;
	}

	private function inner_shadow_back_compatibility( $functions_name ) {
		$utils = ET_Core_Data_Utils::instance();
		$atts  = $this->props;
		$style = '';

		if (
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.99', 'lt' )
		) {
			$class = self::get_module_order_class( $functions_name );
			$style = sprintf(
				'<style>%1$s</style>',
				sprintf(
					'.%1$s.et_pb_slider .et_pb_slide {'
					. '-webkit-box-shadow: none; '
					. '-moz-box-shadow: none; '
					. 'box-shadow: none; '
					. '}',
					esc_attr( $class )
				)
			);

			if ( 'off' !== $utils->array_get( $atts, 'show_inner_shadow' ) ) {
				$style .= sprintf(
					'<style>%1$s</style>',
					sprintf(
						'.%1$s > .box-shadow-overlay { '
						. '-webkit-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. '-moz-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. 'box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. '}',
						esc_attr( $class )
					)
				);
			}
		}

		return $style;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Slider();
}

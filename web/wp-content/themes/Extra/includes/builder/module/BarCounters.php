<?php

class ET_Builder_Module_Bar_Counters extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Bar Counters', 'et_builder' );
		$this->plural          = esc_html__( 'Bar Counters', 'et_builder' );
		$this->slug            = 'et_pb_counters';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_counter';
		$this->child_item_text = esc_html__( 'Bar Counter', 'et_builder' );

		$this->main_css_element = '%%order_class%%.et_pb_counters';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements' => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => et_builder_i18n( 'Layout' ),
					'text'   => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'bar'    => esc_html__( 'Bar', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_counter_container, %%order_class%% .et_pb_counter_amount',
							'border_styles' => '%%order_class%% .et_pb_counter_container',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%% .et_pb_counter_container',
						'overlay' => 'inset',
					),
				),
			),
			'fonts'          => array(
				'title'   => array(
					'label' => et_builder_i18n( 'Title' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .et_pb_counter_title",
					),
				),
				'percent' => array(
					'label' => esc_html__( 'Percentage', 'et_builder' ),
					'css'   => array(
						'main'       => "{$this->main_css_element} .et_pb_counter_amount_number",
						'text_align' => "{$this->main_css_element} .et_pb_counter_amount",
					),
				),
			),
			'background'     => array(
				'use_background_color' => 'fields_only',
				'css'                  => array(
					'main' => "{$this->main_css_element} .et_pb_counter_container",
				),
				'options'              => array(
					'background_color' => array(
						'default' => '',
					),
				),
			),
			'margin_padding' => array(
				'draggable_padding' => false,
				'css'               => array(
					'margin'    => "{$this->main_css_element}",
					'padding'   => "{$this->main_css_element} .et_pb_counter_amount",
					'important' => array( 'custom_margin' ),
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'filters'        => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'scroll_effects' => array(
				'grid_support' => 'yes',
			),
			'button'         => false,
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

		$this->help_videos = array(
			array(
				'id'   => '2QLX8Lwr3cs',
				'name' => esc_html__( 'An introduction to the Bar Counter module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'bar_bg_color'    => array(
				'label'          => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'bar',
				'hover'          => 'tabs',
				'description'    => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
				'default'        => et_builder_accent_color(),
				'mobile_options' => true,
				'sticky'         => true,
			),
			'use_percentages' => array(
				'label'            => esc_html__( 'Show Percentage', 'et_builder' ),
				'description'      => esc_html__( 'Turning off percentages will remove the percentage text from within the filled portion of the bar.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['background_layout'] = array( 'color' => '%%order_class%% .et_pb_counter_title' );
		$fields['bar_bg_color']      = array( 'background-color' => '%%order_class%% .et_pb_counter_amount' );

		return $fields;
	}

	function before_render() {
		global $et_pb_counters_settings;

		$multi_view          = et_pb_multi_view_options( $this );
		$bar_bg_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'bar_bg_color' );

		$et_pb_counters_settings = array(
			'background_last_edited'                       => $this->props['background_last_edited'],
			'background__hover_enabled'                    => isset( $this->props['background__hover_enabled'] ) ? $this->props['background__hover_enabled'] : '',
			// Background Color.
			'background_color'                             => $this->props['background_color'],
			'background_color_tablet'                      => $this->props['background_color_tablet'],
			'background_color_phone'                       => $this->props['background_color_phone'],
			'background_color__hover'                      => isset( $this->props['background_color__hover'] ) ? $this->props['background_color__hover'] : '',
			'background_enable_color'                      => $this->props['background_enable_color'],
			'background_enable_color_tablet'               => $this->props['background_enable_color_tablet'],
			'background_enable_color_phone'                => $this->props['background_enable_color_phone'],
			'background_enable_color__hover'               => isset( $this->props['background_enable_color__hover'] ) ? $this->props['background_enable_color__hover'] : '',
			// Background Gradient.
			'use_background_color_gradient'                => $this->props['use_background_color_gradient'],
			'use_background_color_gradient_tablet'         => $this->props['use_background_color_gradient_tablet'],
			'use_background_color_gradient_phone'          => $this->props['use_background_color_gradient_phone'],
			'use_background_color_gradient__hover'         => isset( $this->props['use_background_color_gradient__hover'] ) ? $this->props['use_background_color_gradient__hover'] : '',
			'background_color_gradient_type'               => $this->props['background_color_gradient_type'],
			'background_color_gradient_type_tablet'        => $this->props['background_color_gradient_type_tablet'],
			'background_color_gradient_type_phone'         => $this->props['background_color_gradient_type_phone'],
			'background_color_gradient_type__hover'        => isset( $this->props['background_color_gradient_type__hover'] ) ? $this->props['background_color_gradient_type__hover'] : '',
			'background_color_gradient_direction'          => $this->props['background_color_gradient_direction'],
			'background_color_gradient_direction_tablet'   => $this->props['background_color_gradient_direction_tablet'],
			'background_color_gradient_direction_phone'    => $this->props['background_color_gradient_direction_phone'],
			'background_color_gradient_direction__hover'   => isset( $this->props['background_color_gradient_direction__hover'] ) ? $this->props['background_color_gradient_direction__hover'] : '',
			'background_color_gradient_direction_radial'   => $this->props['background_color_gradient_direction_radial'],
			'background_color_gradient_direction_radial_tablet' => $this->props['background_color_gradient_direction_radial_tablet'],
			'background_color_gradient_direction_radial_phone' => $this->props['background_color_gradient_direction_radial_phone'],
			'background_color_gradient_direction_radial__hover' => isset( $this->props['background_color_gradient_direction_radial__hover'] ) ? $this->props['background_color_gradient_direction_radial__hover'] : '',
			'background_color_gradient_start'              => $this->props['background_color_gradient_start'],
			'background_color_gradient_start_tablet'       => $this->props['background_color_gradient_start_tablet'],
			'background_color_gradient_start_phone'        => $this->props['background_color_gradient_start_phone'],
			'background_color_gradient_start__hover'       => isset( $this->props['background_color_gradient_start__hover'] ) ? $this->props['background_color_gradient_start__hover'] : '',
			'background_color_gradient_end'                => $this->props['background_color_gradient_end'],
			'background_color_gradient_end_tablet'         => $this->props['background_color_gradient_end_tablet'],
			'background_color_gradient_end_phone'          => $this->props['background_color_gradient_end_phone'],
			'background_color_gradient_end__hover'         => isset( $this->props['background_color_gradient_end__hover'] ) ? $this->props['background_color_gradient_end__hover'] : '',
			'background_color_gradient_start_position'     => $this->props['background_color_gradient_start_position'],
			'background_color_gradient_start_position_tablet' => $this->props['background_color_gradient_start_position_tablet'],
			'background_color_gradient_start_position_phone' => $this->props['background_color_gradient_start_position_phone'],
			'background_color_gradient_start_position__hover' => isset( $this->props['background_color_gradient_start_position__hover'] ) ? $this->props['background_color_gradient_start_position__hover'] : '',
			'background_color_gradient_end_position'       => $this->props['background_color_gradient_end_position'],
			'background_color_gradient_end_position_tablet' => $this->props['background_color_gradient_end_position_tablet'],
			'background_color_gradient_end_position_phone' => $this->props['background_color_gradient_end_position_phone'],
			'background_color_gradient_end_position__hover' => isset( $this->props['background_color_gradient_end_position__hover'] ) ? $this->props['background_color_gradient_end_position__hover'] : '',
			'background_color_gradient_overlays_image'     => $this->props['background_color_gradient_overlays_image'],
			'background_color_gradient_overlays_image_tablet' => $this->props['background_color_gradient_overlays_image_tablet'],
			'background_color_gradient_overlays_image_phone' => $this->props['background_color_gradient_overlays_image_phone'],
			'background_color_gradient_overlays_image__hover' => isset( $this->props['background_color_gradient_overlays_image__hover'] ) ? $this->props['background_color_gradient_overlays_image__hover'] : '',
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
			// Background Parallax Gradient.
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
			'background_video_pause_outside_viewport'      => $this->props['background_video_pause_outside_viewport'],
			'allow_player_pause'                           => $this->props['allow_player_pause'],
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
			'bar_bg_color'                                 => isset( $bar_bg_color_values['desktop'] ) ? $bar_bg_color_values['desktop'] : '',
			'bar_bg_color_tablet'                          => isset( $bar_bg_color_values['tablet'] ) ? $bar_bg_color_values['tablet'] : '',
			'bar_bg_color_phone'                           => isset( $bar_bg_color_values['phone'] ) ? $bar_bg_color_values['phone'] : '',
			'use_percentages'                              => $multi_view->get_values( 'use_percentages' ),
			// Sticky Element.
			'is_sticky_module'                             => et_pb_sticky_options()->is_sticky_module( $this->props ),
		);
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

		// Module classname
		$this->add_classname(
			array(
				'et-waypoint',
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		$this->add_classname( $this->get_text_orientation_classname() );

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Sticky & Hover style rendering.
		$this->generate_styles(
			array(
				'responsive'                      => false,
				'render_slug'                     => $render_slug,
				'base_attr_name'                  => 'background_color',
				'css_property'                    => 'background-color',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'selector'                        => '%%order_class%% .et_pb_counter_container',
			)
		);

		$this->generate_styles(
			array(
				'responsive'     => false,
				'render_slug'    => $render_slug,
				'base_attr_name' => 'bar_bg_color',
				'css_property'   => 'background-color',
				'selector'       => '%%order_class%% .et_pb_counter_amount',
			)
		);

		$output = sprintf(
			'<ul%3$s class="%2$s"%4$s>
				%1$s
			</ul>',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			et_core_esc_previously( $data_background_layout )
		);

		// Reset passed value.
		$et_pb_counters_settings = array();

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Bar_Counters();
}

<?php

class ET_Builder_Module_Social_Media_Follow extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Social Media Follow', 'et_builder' );
		$this->plural          = esc_html__( 'Social Media Follows', 'et_builder' );
		$this->slug            = 'et_pb_social_media_follow';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_social_media_follow_network';
		$this->child_item_text = esc_html__( 'Social Network', 'et_builder' );

		$this->main_css_element = 'ul%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment' => esc_html__( 'Alignment', 'et_builder' ),
					'icon'      => esc_html__( 'Icon', 'et_builder' ),
					'text'      => et_builder_i18n( 'Text' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'before'        => array(
				'label'    => et_builder_i18n( 'Before' ),
				'selector' => 'ul%%order_class%%:before',
			),
			'main_element'  => array(
				'label'    => et_builder_i18n( 'Main Element' ),
				'selector' => 'ul%%order_class%%',
			),
			'after'         => array(
				'label'    => et_builder_i18n( 'After' ),
				'selector' => 'ul%%order_class%%:after',
			),
			'social_follow' => array(
				'label'    => esc_html__( 'Social Follow', 'et_builder' ),
				'selector' => 'li',
			),
			'social_icon'   => array(
				'label'    => esc_html__( 'Social Icon', 'et_builder' ),
				'selector' => 'li a.icon',
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'et_builder' ),
				'selector' => 'li a.follow_button',
			),
		);

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} a.icon",
							'border_styles' => "{$this->main_css_element} a",
						),
					),
					'defaults' => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et_pb_social_icon a',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => 'ul%%order_class%%',
					'important' => array( 'custom_margin', 'custom_padding' ), // needed to overwrite last module margin-bottom styling and default ul padding on post
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'text_orientation'      => array(
					'exclude_options' => array( 'justified' ),
				),
				'options'               => array(
					'text_orientation'  => array(
						'label'        => esc_html__( 'Module Alignment', 'et_builder' ),
						'toggle_slug'  => 'alignment',
						'options_icon' => 'module_align',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
			),
			'fonts'          => false,
			'button'         => array(
				'button' => array(
					'label'               => esc_html__( 'Follow Button', 'et_builder' ),
					'css'                 => array(
						'main' => "{$this->main_css_element} .follow_button",
					),
					'hide_icon'           => true,
					'hide_custom_padding' => true,
					'no_rel_attr'         => true,
					'text_size'           => array(
						'default' => '14px',
					),
					'border_width'        => array(
						'default' => '0px',
					),
					'box_shadow'          => array(
						'css' => array(
							'main' => "{$this->main_css_element} .follow_button",
						),
					),
				),
			),
			'link_options'   => false,
		);

		$this->help_videos = array(
			array(
				'id'   => '8b0BlM_rlHQ',
				'name' => esc_html__( 'An introduction to the Social Media Follow module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'url_new_window'     => array(
				'label'            => esc_html__( 'Link Target', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'      => 'icon',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'on',
			),
			'follow_button'      => array(
				'label'            => esc_html__( 'Follow Button', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'Off' ),
					'on'  => et_builder_i18n( 'On' ),
				),
				'default_on_front' => 'off',
				'toggle_slug'      => 'icon',
				'description'      => esc_html__( 'Here you can choose whether or not to include the follow button next to the icon.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'icon_color'         => array(
				'label'          => esc_html__( 'Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the social network icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'use_icon_font_size' => array(
				'label'            => esc_html__( 'Use Custom Icon Size', 'et_builder' ),
				'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'     => array(
				'label'            => esc_html__( 'Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '16px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'depends_show_if'  => 'on',
				'responsive'       => true,
				'sticky'           => true,
				'hover'            => 'tabs',
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']     = array( 'color' => '%%order_class%% li a.icon:before' );
		$fields['icon_font_size'] = array(
			'font-size'   => '%%order_class%% li a.icon:before',
			'line-height' => '%%order_class%% li a.icon:before',
			'height'      => '%%order_class%% li a.icon:before',
			'width'       => '%%order_class%% li a.icon:before',
			'height'      => '%%order_class%% li a.icon',
			'width'       => '%%order_class%% li a.icon',
		);

		return $fields;
	}

	function before_render() {
		global $et_pb_social_media_follow_link,
			$et_pb_social_media_follow_sticky;

		$url_new_window = $this->props['url_new_window'];
		$follow_button  = et_pb_multi_view_options( $this )->get_values( 'follow_button' );

		$et_pb_social_media_follow_link = array(
			'url_new_window' => $url_new_window,
			'follow_button'  => $follow_button,
		);

		$et_pb_social_media_follow_sticky = et_pb_sticky_options()->is_sticky_module( $this->props );
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
		global $et_pb_social_media_follow_link;

		$multi_view                = et_pb_multi_view_options( $this );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$use_icon_font_size        = $this->props['use_icon_font_size'];

		// Icon Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'icon_color',
				'selector'                        => '%%order_class%% li.et_pb_social_icon a.icon:before',
				'selector_wrapper'                => '%%order_class%% li a.icon',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			// Calculate icon size + its wrapper dimension.
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => '%%order_class%% li a.icon:before',
					'selector_wrapper'                => '%%order_class%% li a.icon',
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
					'css_property'                    => 'right',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr.
					'processor'                       => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_social_media_icon_font_size',
					),
				)
			);
		}

		// Get custom borders, if any
		$attrs = $this->props;

		// Module classnames
		$this->add_classname(
			array(
				'clearfix',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( $multi_view->has_value( 'follow_button', 'on' ) ) {
			$this->add_classname( 'has_follow_button' );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$muti_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'has_follow_button' => array(
						'follow_button' => 'on',
					),
				),
			)
		);

		$output = sprintf(
			'<ul%3$s class="%2$s"%6$s%7$s>
				%5$s
				%4$s
				%8$s
				%9$s
				%1$s
			</ul>',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background, // #5
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $muti_view_data_attr ),
			et_core_esc_previously( $this->background_pattern() ), // #8
			et_core_esc_previously( $this->background_mask() ) // #9
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Social_Media_Follow();
}

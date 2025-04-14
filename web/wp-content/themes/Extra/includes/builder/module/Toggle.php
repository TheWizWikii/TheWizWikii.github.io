<?php

class ET_Builder_Module_Toggle extends ET_Builder_Module {
	function init() {
		$this->name                       = esc_html__( 'Toggle', 'et_builder' );
		$this->plural                     = esc_html__( 'Toggles', 'et_builder' );
		$this->slug                       = 'et_pb_toggle';
		$this->vb_support                 = 'on';
		$this->additional_shortcode_slugs = array( 'et_pb_accordion_item' );
		$this->main_css_element           = '%%order_class%%.et_pb_toggle';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'state'        => esc_html__( 'State', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'   => esc_html__( 'Icon', 'et_builder' ),
					'text'   => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'toggle' => esc_html__( 'Toggle', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'         => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => ".et_pb_module{$this->main_css_element}",
							'border_styles' => ".et_pb_module{$this->main_css_element}",
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d9d9d9',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'      => array(
				'default' => array(
					'css' => array(
						'important' => true,
					),
				),
			),
			'fonts'           => array(
				'title'        => array(
					'label'            => et_builder_i18n( 'Title' ),
					'css'              => array(
						'main'      => "{$this->main_css_element} h5, {$this->main_css_element} h1.et_pb_toggle_title, {$this->main_css_element} h2.et_pb_toggle_title, {$this->main_css_element} h3.et_pb_toggle_title, {$this->main_css_element} h4.et_pb_toggle_title, {$this->main_css_element} h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'header_level'     => array(
						'default' => 'h5',
					),
					'options_priority' => array(
						'title_text_color' => 9,
					),
				),
				'closed_title' => array(
					'label'           => esc_html__( 'Closed Title', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element}.et_pb_toggle_close h5, {$this->main_css_element}.et_pb_toggle_close h1.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h2.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h3.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h4.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'hide_text_color' => true,
					'line_height'     => array(
						'default' => '1.7em',
					),
					'font_size'       => array(
						'default' => '16px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'closed_title' => array(
					'label'           => esc_html__( 'Closed Title', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element}.et_pb_toggle_close h5, {$this->main_css_element}.et_pb_toggle_close h1.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h2.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h3.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h4.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'hide_text_color' => true,
					'default_from'    => 'title',
					'line_height'     => array(
						'default' => '1.7em',
					),
					'font_size'       => array(
						'default' => '16px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'body'         => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'         => "{$this->main_css_element}",
						'limited_main' => "{$this->main_css_element}, {$this->main_css_element} p, {$this->main_css_element} .et_pb_toggle_content",
						'line_height'  => "{$this->main_css_element} p",
						'text_shadow'  => "{$this->main_css_element} .et_pb_toggle_content",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
			),
			'background'      => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
			'z_index'         => array(
				'default' => '1',
			),
		);

		$this->custom_css_fields = array(
			'open_toggle'    => array(
				'label'                    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector'                 => '.et_pb_toggle.et_pb_toggle_open',
				'no_space_before_selector' => true,
			),
			'toggle_title'   => array(
				'label'    => esc_html__( 'Toggle Title', 'et_builder' ),
				'selector' => '.et_pb_toggle_title',
			),
			'toggle_icon'    => array(
				'label'    => esc_html__( 'Toggle Icon', 'et_builder' ),
				'selector' => '.et_pb_toggle_title:before',
			),
			'toggle_content' => array(
				'label'    => esc_html__( 'Toggle Content', 'et_builder' ),
				'selector' => '.et_pb_toggle_content',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'hFgp_A_u7mg',
				'name' => esc_html__( 'An introduction to the Toggle module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'                          => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title will appear above the content and when the toggle is closed.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'open'                           => array(
				'label'            => esc_html__( 'State', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => array(
					'off' => esc_html__( 'Close', 'et_builder' ),
					'on'  => esc_html__( 'Open', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'toggle_slug'      => 'state',
				'description'      => esc_html__( 'Choose whether or not this toggle should start in an open or closed state.', 'et_builder' ),
			),
			'content'                        => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'open_toggle_text_color'         => array(
				'label'          => esc_html__( 'Open Title Text Color', 'et_builder' ),
				'description'    => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the open state title color here.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'title',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'open_toggle_background_color'   => array(
				'label'          => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'description'    => esc_html__( 'You can pick unique background colors for toggles when they are in their open and closed states. Choose the open state background color here.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'toggle',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'closed_toggle_text_color'       => array(
				'label'          => esc_html__( 'Closed Title Text Color', 'et_builder' ),
				'description'    => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the closed state title color here.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'closed_title',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'closed_toggle_background_color' => array(
				'label'          => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'description'    => esc_html__( 'You can pick unique background colors for toggles when they are in their open and closed states. Choose the closed state background color here.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'toggle',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			// Closed icon styling.
			'icon_color'                     => array(
				'label'          => esc_html__( 'Closed Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the toggle icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'toggle_icon'                    => array(
				'label'          => esc_html__( 'Closed Icon', 'et_builder' ),
				'toggle_slug'    => 'icon',
				'type'           => 'select_icon',
				'class'          => array( 'et-pb-font-icon' ),
				'description'    => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options' => true,
				'hover'          => 'tabs',
				'sticky'         => true,
				'tab_slug'       => 'advanced',
			),
			'use_icon_font_size'             => array(
				'label'            => esc_html__( 'Use Custom Closed Icon Size', 'et_builder' ),
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
			'icon_font_size'                 => array(
				'label'            => esc_html__( 'Closed Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
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
			// Open icon styling.
			'open_icon_color'                => array(
				'label'          => esc_html__( 'Open Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the toggle icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'open_toggle_icon'               => array(
				'label'          => esc_html__( 'Open Icon', 'et_builder' ),
				'toggle_slug'    => 'icon',
				'type'           => 'select_icon',
				'class'          => array( 'et-pb-font-icon' ),
				'description'    => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options' => true,
				'hover'          => 'tabs',
				'sticky'         => true,
				'tab_slug'       => 'advanced',
			),
			'open_use_icon_font_size'        => array(
				'label'            => esc_html__( 'Use Custom Open Icon Size', 'et_builder' ),
				'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'open_icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'open_icon_font_size'            => array(
				'label'            => esc_html__( 'Open Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
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
		$title  = '%%order_class%% .et_pb_toggle .et_pb_toggle_title';

		$fields['icon_color']     = array( 'color' => '%%order_class%% .et_pb_toggle_title:before' );
		$fields['icon_font_size'] = array(
			'font-size'  => '%%order_class%% .et_pb_toggle_title:before',
			'margin-top' => '%%order_class%% .et_pb_toggle_title:before',
			'right'      => '%%order_class%% .et_pb_toggle_title:before',
		);

		$fields['toggle_text_color']        = array( 'color' => $title );
		$fields['toggle_font_size']         = array( 'font-size' => $title );
		$fields['toggle_letter_spacing']    = array( 'letter-spacing' => $title );
		$fields['toggle_line_height']       = array( 'line-height' => $title );
		$fields['toggle_text_shadow_style'] = array( 'text-shadow' => $title );

		$fields['closed_toggle_text_color']       = array( 'color' => '%%order_class%%.et_pb_toggle_close .et_pb_toggle_title' );
		$fields['closed_toggle_background_color'] = array( 'background-color' => '%%order_class%%.et_pb_toggle_close' );

		$fields['open_toggle_text_color']       = array( 'color' => '%%order_class%%.et_pb_toggle_open .et_pb_toggle_title' );
		$fields['open_toggle_background_color'] = array( 'background-color' => '%%order_class%%.et_pb_toggle_open' );

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
		$multi_view           = et_pb_multi_view_options( $this );
		$open                 = $this->props['open'];
		$header_level         = $this->props['title_level'];
		$use_icon_font_size   = $this->props['use_icon_font_size'];
		$accordion_item_class = 'et_pb_accordion_item' === $render_slug ? '.et_pb_accordion_item' : '';

		// Open Toggle Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'open_toggle_background_color',
				'selector'                        => "{$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Closed Toggle Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'closed_toggle_background_color',
				'selector'                        => "{$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Open Toggle Text Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'open_toggle_text_color',
				'selector'                        => "{$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open h5.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open h1.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open h2.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open h3.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open h4.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_open h6.et_pb_toggle_title",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Closed Toggle Text Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'closed_toggle_text_color',
				'selector'                        => "{$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close h5.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close h1.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close h2.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close h3.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close h4.et_pb_toggle_title, {$accordion_item_class}%%order_class%%.et_pb_toggle.et_pb_toggle_close h6.et_pb_toggle_title",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Icon Size.
		if ( ! empty( $this->props['open_use_icon_font_size'] ) && 'off' !== $this->props['open_use_icon_font_size'] ) {
			// Calculate icon font size and its right position.
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'open_icon_font_size',
					'selector'                        => "{$accordion_item_class}%%order_class%%.et_pb_toggle_open .et_pb_toggle_title:before",
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
					'css_property'                    => 'font-size',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr.
					'processor'                       => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_toggle_title_icon_font_size',
					),
				)
			);
		}

		// Icon Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'open_icon_color',
				'selector'       => "{$accordion_item_class}%%order_class%%.et_pb_toggle_open .et_pb_toggle_title:before",
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Toggle Icon Styles.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family_and_content',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'open_toggle_icon',
				'important'      => true,
				'selector'       => "{$accordion_item_class}%%order_class%%.et_pb_toggle_open .et_pb_toggle_title:before",
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		// Closed Icon Size.
		if ( ! empty( $this->props['use_icon_font_size'] ) && 'off' !== $this->props['use_icon_font_size'] ) {
			// Calculate icon font size and its right position.
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => "{$accordion_item_class}%%order_class%%.et_pb_toggle_close .et_pb_toggle_title:before",
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
					'css_property'                    => 'font-size',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr.
					'processor'                       => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_toggle_title_icon_font_size',
					),
				)
			);
		}

		// Closed Icon Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'icon_color',
				'selector'       => "{$accordion_item_class}%%order_class%%.et_pb_toggle_close .et_pb_toggle_title:before",
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Closed Toggle Icon Styles.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family_and_content',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'toggle_icon',
				'important'      => true,
				'selector'       => "{$accordion_item_class}%%order_class%%.et_pb_toggle_close .et_pb_toggle_title:before",
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		if ( 'et_pb_accordion_item' === $render_slug ) {
			global $et_pb_accordion_item_number, $et_pb_accordion_header_level;

			$open = 1 === $et_pb_accordion_item_number ? 'on' : 'off';

			$et_pb_accordion_item_number++;

			// Respect the individual level first.
			if ( '' !== $this->props['toggle_level'] ) {
				$header_level = $this->props['toggle_level'];
			} else {
				// If individual tag is not there choose global.
				$header_level = $et_pb_accordion_header_level;
			}

			$this->add_classname( 'et_pb_accordion_item' );
		}

		// Adding "_item" class for toggle module for customizer targetting. There's no proper selector
		// for toggle module styles since both accordion and toggle module use the same selector.
		if ( 'et_pb_toggle' === $render_slug ) {
			$this->add_classname( 'et_pb_toggle_item' );
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$heading = $multi_view->render_element(
			array(
				'tag'      => et_pb_process_header_level( $header_level, 'h5' ),
				'content'  => '{{title}}',
				'attrs'    => array(
					'class' => 'et_pb_toggle_title',
				),
				'required' => false,
			)
		);

		$multi_view_content = $multi_view->render_attrs(
			array(
				'content' => '{{content}}',
			)
		);

		// Module classnames.
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		if ( 'on' === $open ) {
			$this->add_classname( 'et_pb_toggle_open' );
		} else {
			$this->add_classname( 'et_pb_toggle_close' );
		}

		$output = sprintf(
			'<div%4$s class="%2$s">
				%6$s
				%5$s
				%8$s
				%9$s
				%1$s
				<div class="et_pb_toggle_content clearfix"%7$s>%3$s</div>
			</div>',
			$heading,
			$this->module_classname( $render_slug ),
			$this->content,
			$this->module_id(),
			$video_background, // #5
			$parallax_image_background,
			et_core_esc_previously( $multi_view_content ),
			et_core_esc_previously( $this->background_pattern() ), // #8
			et_core_esc_previously( $this->background_mask() ) // #9
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

		if ( $raw_value && 'title' === $name ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Toggle();
}

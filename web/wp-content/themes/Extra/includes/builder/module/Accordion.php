<?php

class ET_Builder_Module_Accordion extends ET_Builder_Module {

	function init() {
		$this->name       = esc_html__( 'Accordion', 'et_builder' );
		$this->plural     = esc_html__( 'Accordions', 'et_builder' );
		$this->slug       = 'et_pb_accordion';
		$this->vb_support = 'on';
		$this->child_slug = 'et_pb_accordion_item';

		$this->main_css_element = '%%order_class%%.et_pb_accordion';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'  => et_builder_i18n( 'Text' ),
					'extended_icon' => esc_html__( 'Toggle Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'          => esc_html__( 'Icon', 'et_builder' ),
					'toggle_layout' => esc_html__( 'Toggle', 'et_builder' ),
					'text'          => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_accordion_item",
							'border_styles' => "{$this->main_css_element} .et_pb_accordion_item",
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
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et_pb_toggle',
					),
				),
			),
			'fonts'          => array(
				'toggle'        => array(
					'label'            => et_builder_i18n( 'Title' ),
					'css'              => array(
						'main'      => "{$this->main_css_element} h5.et_pb_toggle_title, {$this->main_css_element} h1.et_pb_toggle_title, {$this->main_css_element} h2.et_pb_toggle_title, {$this->main_css_element} h3.et_pb_toggle_title, {$this->main_css_element} h4.et_pb_toggle_title, {$this->main_css_element} h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'header_level'     => array(
						'default' => 'h5',
					),
					'options_priority' => array(
						'toggle_text_color' => 9,
					),
				),
				'closed_toggle' => array(
					'label'           => esc_html__( 'Closed Title', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element} .et_pb_toggle_close h5.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h1.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h2.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h3.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h4.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h6.et_pb_toggle_title",
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
				'body'          => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_toggle_content",
						'limited_main' => "{$this->main_css_element} .et_pb_toggle_content, {$this->main_css_element} .et_pb_toggle_content p",
						'line_height'  => "{$this->main_css_element} .et_pb_toggle_content p",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
			),
			'margin_padding' => array(
				'draggable_padding' => false,
				'css'               => array(
					'padding'   => "{$this->main_css_element} .et_pb_toggle_content",
					'margin'    => $this->main_css_element,
					'important' => array( 'custom_margin' ),
				),
			),
			'scroll_effects' => array(
				'grid_support' => 'yes',
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'toggle'         => array(
				'label'    => esc_html__( 'Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle',
			),
			'open_toggle'    => array(
				'label'    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle_open',
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
				'id'   => 'OBbuKXTJyj8',
				'name' => esc_html__( 'An introduction to the Accordion module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'open_toggle_text_color'         => array(
				'label'          => esc_html__( 'Open Title Text Color', 'et_builder' ),
				'description'    => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the open state title color here.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'toggle',
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
				'toggle_slug'    => 'toggle_layout',
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
				'toggle_slug'    => 'closed_toggle',
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
				'toggle_slug'    => 'toggle_layout',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'toggle_icon'                    => array(
				'label'           => esc_html__( 'Icon', 'et_builder' ),
				'toggle_slug'     => 'extended_icon',
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'class'           => array( 'et-pb-font-icon' ),
				'description'     => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
				'sticky'          => true,
			),
			'icon_color'                     => array(
				'label'          => esc_html__( 'Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the toggle icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'use_icon_font_size'             => array(
				'label'            => esc_html__( 'Use Icon Font Size', 'et_builder' ),
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
				'label'            => esc_html__( 'Icon Font Size', 'et_builder' ),
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
				'sticky'           => true,
				'depends_show_if'  => 'on',
				'hover'            => 'tabs',
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();
		$title  = '%%order_class%% .et_pb_toggle .et_pb_toggle_title';

		$fields['icon_color']     = array( 'color' => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before' );
		$fields['icon_font_size'] = array(
			'font-size'  => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before',
			'margin-top' => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before',
			'right'      => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before',
		);

		$fields['toggle_text_color']        = array( 'color' => $title );
		$fields['toggle_font_size']         = array( 'font-size' => $title );
		$fields['toggle_letter_spacing']    = array( 'letter-spacing' => $title );
		$fields['toggle_line_height']       = array( 'line-height' => $title );
		$fields['toggle_text_shadow_style'] = array( 'text-shadow' => $title );

		$fields['closed_toggle_text_color']       = array( 'color' => '%%order_class%%.et_pb_accordion .et_pb_toggle_close .et_pb_toggle_title' );
		$fields['closed_toggle_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_toggle_close' );

		$fields['open_toggle_text_color']       = array( 'color' => '%%order_class%%.et_pb_accordion .et_pb_toggle_open .et_pb_toggle_title' );
		$fields['open_toggle_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_toggle_open' );

		return $fields;
	}

	function before_render() {
		global $et_pb_accordion_item_number, $et_pb_accordion_header_level;

		$et_pb_accordion_item_number  = 1;
		$et_pb_accordion_header_level = $this->props['toggle_level'];
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
		global $et_pb_accordion_item_number;

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Open Toggle Background Color.
		$this->generate_styles(
			array(
				'type'           => 'color',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'open_toggle_background_color',
				'css_property'   => 'background-color',
				'selector'       => '%%order_class%% .et_pb_toggle_open',
			)
		);

		// Closed Toggle Background Color.
		$this->generate_styles(
			array(
				'type'           => 'color',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'closed_toggle_background_color',
				'css_property'   => 'background-color',
				'selector'       => '%%order_class%% .et_pb_toggle_close',
			)
		);

		// Open Toggle Text Color.
		$this->generate_styles(
			array(
				'type'           => 'color',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'open_toggle_text_color',
				'css_property'   => 'color',
				'important'      => true,
				'selector'       => '%%order_class%%.et_pb_accordion .et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_open h6.et_pb_toggle_title',
				'hover_selector' => '%%order_class%%:hover .et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h6.et_pb_toggle_title',
			)
		);

		// Closed Toggle Text Color.
		$this->generate_styles(
			array(
				'type'           => 'color',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'closed_toggle_text_color',
				'css_property'   => 'color',
				'important'      => true,
				'selector'       => '%%order_class%%.et_pb_accordion .et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%%.et_pb_accordion .et_pb_toggle_close h6.et_pb_toggle_title',
				'hover_selector' => '%%order_class%%:hover .et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h6.et_pb_toggle_title',
			)
		);

		// Icon Size.
		$use_icon_font_size = $this->props['use_icon_font_size'];

		if ( 'off' !== $use_icon_font_size ) {
			// Calculate icon font size and its right position.
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => '%%order_class%% .et_pb_toggle_title:before',
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
				'type'                            => 'color',
				'render_slug'                     => $render_slug,
				'base_attr_name'                  => 'icon_color',
				'css_property'                    => 'color',
				'selector'                        => '%%order_class%% .et_pb_toggle_title:before',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'priority'                        => ET_Builder_Element::DEFAULT_PRIORITY,
			)
		);

		// Module classnames.
		$this->add_classname( $this->get_text_orientation_classname() );

		// Toggle Icon Styles.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family_and_content',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'toggle_icon',
				'important'      => true,
				'selector'       => '%%order_class%% .et_pb_toggle_title:before',
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%2$s">
				%5$s
				%4$s
				%6$s
				%7$s
				%1$s
			</div>',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $this->background_pattern() ), // #6
			et_core_esc_previously( $this->background_mask() ) // #7
		);

		return $output;
	}

	public function add_new_child_text() {
		return esc_html__( 'Add New Accordion Item', 'et_builder' );
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Accordion();
}

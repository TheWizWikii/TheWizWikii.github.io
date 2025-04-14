<?php

class ET_Builder_Module_Accordion_Item extends ET_Builder_Module {
	/**
	 * Should render module in Visual Builder.
	 *
	 * @var bool
	 */
	public $no_render;

	function init() {
		$this->name             = esc_html__( 'Accordion', 'et_builder' );
		$this->plural           = esc_html__( 'Accordions', 'et_builder' );
		$this->slug             = 'et_pb_accordion_item';
		$this->vb_support       = 'on';
		$this->type             = 'child';
		$this->child_title_var  = 'title';
		$this->no_render        = true;
		$this->main_css_element = '%%order_class%%.et_pb_toggle';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
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
			'fonts'           => array(
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
					'label'           => esc_html__( 'Closed Toggle', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element}.et_pb_toggle_close h5.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h1.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h2.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h3.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h4.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h6.et_pb_toggle_title",
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
			'borders'         => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							// Accordion Item can use %%parent_class%% because its slug is parent_slug + `_item` suffix
							'border_radii'  => "%%parent_class%% .et_pb_module{$this->main_css_element}",
							'border_styles' => "%%parent_class%% .et_pb_module{$this->main_css_element}",
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
			'margin_padding'  => array(
				'draggable_margin'  => false,
				'draggable_padding' => false,
				'css'               => array(
					'important' => 'all',
				),
			),
			'max_width'       => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_toggle',
				),
			),
			'text'            => array(
				'css' => array(
					'text_orientation' => '%%order_class%%',
				),
			),
			'button'          => false,
			'sticky'          => false,
			'height'          => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_toggle_content',
				),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
			'z_index'         => array(
				'default' => '1',
			),
		);

		$this->custom_css_fields = array(
			'toggle'         => array(
				'label' => esc_html__( 'Toggle', 'et_builder' ),
			),
			'open_toggle'    => array(
				'label'                    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector'                 => '.et_pb_toggle_open',
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
				'id'   => 'OBbuKXTJyj8',
				'name' => esc_html__( 'An introduction to the Accordion module', 'et_builder' ),
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
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'content'                        => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
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
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Accordion_Item();
}

<?php

class ET_Builder_Module_Fullwidth_Header extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Fullwidth Header', 'et_builder' );
		$this->plural           = esc_html__( 'Fullwidth Headers', 'et_builder' );
		$this->slug             = 'et_pb_fullwidth_header';
		$this->vb_support       = 'on';
		$this->fullwidth        = true;
		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'images'       => esc_html__( 'Images', 'et_builder' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout'      => et_builder_i18n( 'Layout' ),
					'scroll_down' => esc_html__( 'Scroll Down Icon', 'et_builder' ),
					'image'       => array(
						'title' => et_builder_i18n( 'Image' ),
					),
					'overlay'     => et_builder_i18n( 'Overlay' ),
					'text'        => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'width'       => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 80,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'title'   => array(
					'label'          => et_builder_i18n( 'Title' ),
					'css'            => array(
						'main' => '%%order_class%%.et_pb_fullwidth_header .header-content h1, %%order_class%%.et_pb_fullwidth_header .header-content h2.et_pb_module_header, %%order_class%%.et_pb_fullwidth_header .header-content h3.et_pb_module_header, %%order_class%%.et_pb_fullwidth_header .header-content h4.et_pb_module_header, %%order_class%%.et_pb_fullwidth_header .header-content h5.et_pb_module_header, %%order_class%%.et_pb_fullwidth_header .header-content h6.et_pb_module_header',
					),
					'font_size'      => array(
						'default' => '30px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level'   => array(
						'default' => 'h1',
					),
				),
				'content' => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main' => '%%order_class%%.et_pb_fullwidth_header .et_pb_header_content_wrapper',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
				'subhead' => array(
					'label'          => esc_html__( 'Subtitle', 'et_builder' ),
					'css'            => array(
						'main' => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_subhead',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'button'          => array(
				'button_one' => array(
					'label'          => esc_html__( 'Button One', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_button_one.et_pb_button",
					),
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button_one',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
				'button_two' => array(
					'label'          => esc_html__( 'Button Two', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_button_two.et_pb_button",
					),
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button_two',
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
				'css'     => array(
					'main' => '.et_pb_fullwidth_header%%order_class%%',
				),
				'options' => array(
					'background_color' => array(
						'default' => et_builder_accent_color(),
					),
					'parallax_method'  => array(
						'default' => 'off',
					),
				),
			),
			'max_width'       => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'            => array(
				'use_text_orientation'  => false,
				'use_background_layout' => true,
				'css'                   => array(
					'main'        => implode(
						', ',
						array(
							'%%order_class%% .et_pb_module_header',
							'%%order_class%% .et_pb_fullwidth_header_subhead',
							'%%order_class%% p',
							'%%order_class%% .et_pb_button',

						)
					),
					'text_shadow' => '%%order_class%% .header-content',
				),
				'options'               => array(
					'background_layout' => array(
						'default' => 'dark',
						'hover'   => 'tabs',
					),
				),
			),
			'filters'         => array(
				'css'                  => array(
					'main' => array(
						'%%order_class%%',
					),
				),
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'           => array(
				'css' => array(
					'main' => array(
						'%%order_class%% .header-logo',
						'%%order_class%% .header-image-container',
					),
				),
			),
			'borders'         => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.et_pb_fullwidth_header .header-logo, %%order_class%%.et_pb_fullwidth_header .header-image-container img',
							'border_styles' => '%%order_class%%.et_pb_fullwidth_header .header-logo, %%order_class%%.et_pb_fullwidth_header .header-image-container img',
						),
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'      => array(
				'default' => array(),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => '%%order_class%% .header-logo, %%order_class%% .header-image-container img',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->custom_css_fields = array(
			'header_container' => array(
				'label'    => esc_html__( 'Header Container', 'et_builder' ),
				'selector' => '.et_pb_fullwidth_header_container',
			),
			'header_image'     => array(
				'label'    => esc_html__( 'Header Image', 'et_builder' ),
				'selector' => '.et_pb_fullwidth_header_container .header-image img',
			),
			'logo'             => array(
				'label'    => esc_html__( 'Logo', 'et_builder' ),
				'selector' => '.header-content img.header-logo',
			),
			'title'            => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => '%%order_class%% .header-content h1,%%order_class%% .header-content .et_pb_module_header',
			),
			'content'          => array(
				'label'    => et_builder_i18n( 'Body' ),
				'selector' => '%%order_class%%.et_pb_fullwidth_header .et_pb_header_content_wrapper',
			),
			'subtitle'         => array(
				'label'    => esc_html__( 'Subtitle', 'et_builder' ),
				'selector' => '.header-content .et_pb_fullwidth_header_subhead',
			),
			'button_1'         => array(
				'label'    => esc_html__( 'Button One', 'et_builder' ),
				'selector' => '.header-content-container .header-content .et_pb_button_one.et_pb_button',
			),
			'button_2'         => array(
				'label'    => esc_html__( 'Button Two', 'et_builder' ),
				'selector' => '.header-content-container .header-content .et_pb_button_two.et_pb_button',
			),
			'scroll_button'    => array(
				'label'    => esc_html__( 'Scroll Down Button', 'et_builder' ),
				'selector' => '.et_pb_fullwidth_header_scroll a .et-pb-icon',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'llLBZCNCEGk',
				'name' => esc_html__( 'An introduction to the Fullwidth Header module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'                    => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter your page title here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'subhead'                  => array(
				'label'           => esc_html__( 'Subtitle', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to use a subhead, add it here. Your subhead will appear below your title in a small font.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'text_orientation'         => array(
				'label'            => esc_html__( 'Text & Logo Alignment', 'et_builder' ),
				'type'             => 'text_align',
				'option_category'  => 'layout',
				'options'          => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'This controls how your text is aligned within the module.', 'et_builder' ),
				'default_on_front' => 'left',
			),

			'header_fullscreen'        => array(
				'label'            => esc_html__( 'Make Fullscreen', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'content_orientation',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Here you can choose whether the header is expanded to fullscreen size.', 'et_builder' ),
			),
			'header_scroll_down'       => array(
				'label'            => esc_html__( 'Show Scroll Down Button', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'scroll_down_icon',
					'scroll_down_icon_color',
					'scroll_down_icon_size',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'scroll_down',
				'description'      => esc_html__( 'Here you can choose whether the scroll down button is shown.', 'et_builder' ),
			),
			'scroll_down_icon'         => array(
				'default'          => ';',
				'label'            => esc_html__( 'Icon', 'et_builder' ),
				'type'             => 'select_icon',
				'option_category'  => 'configuration',
				'class'            => array( 'et-pb-font-icon' ),
				'renderer_options' => array(
					'icons_list' => 'icon_down',
				),
				'description'      => esc_html__( 'Choose an icon to display for the scroll down button.', 'et_builder' ),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'scroll_down',
				'mobile_options'   => true,
			),
			'scroll_down_icon_color'   => array(
				'label'           => esc_html__( 'Scroll Down Icon Color', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom color for the scroll down icon.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'scroll_down',
				'hover'           => 'tabs',
				'depends_show_if' => 'on',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'scroll_down_icon_size'    => array(
				'label'           => esc_html__( 'Scroll Down Icon Size', 'et_builder' ),
				'description'     => esc_html__( 'Increase or decrease the size of the scroll down arrow that appears towards the bottom of the module.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'mobile_options'  => true,
				'sticky'          => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'scroll_down',
				'responsive'      => true,
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'    => 'px',
				'hover'           => 'tabs',
				'depends_show_if' => 'on',
			),
			'button_one_text'          => array(
				'label'           => sprintf( esc_html__( 'Button %1$s', 'et_builder' ), '#1' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_one_url'           => array(
				'label'           => sprintf( esc_html__( 'Button %1$s Link URL', 'et_builder' ), '#1' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'button_two_text'          => array(
				'label'           => sprintf( esc_html__( 'Button %1$s', 'et_builder' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_two_url'           => array(
				'label'           => sprintf( esc_html__( 'Button %1$s Link URL', 'et_builder' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'background_overlay_color' => array(
				'label'          => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the background overlay. Decreasing the opacity will allow background images and gradients to show through while still keeping the text readable.', 'et_builder' ),
				'type'           => 'color-alpha',
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'overlay',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'logo_image_url'           => array(
				'label'              => esc_html__( 'Logo Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'affects'            => array(
					'logo_alt_text',
					'logo_title',
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'images',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'logo_alt_text'            => array(
				'label'           => esc_html__( 'Logo Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'logo_image_url',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'logo_title'               => array(
				'label'           => esc_html__( 'Logo Image Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'logo_image_url',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'content_orientation'      => array(
				'label'            => esc_html__( 'Text Vertical Alignment', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'center' => et_builder_i18n( 'Center' ),
					'bottom' => et_builder_i18n( 'Bottom' ),
				),
				'default_on_front' => 'center',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'description'      => esc_html__( 'This setting determines the vertical alignment of your content. Your content can either be vertically centered, or aligned to the bottom.', 'et_builder' ),
				'depends_show_if'  => 'on',
			),

			'header_image_url'         => array(
				'label'              => esc_html__( 'Header Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'images',
				'affects'            => array(
					'image_alt_text',
					'image_title',
				),
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'image_alt_text'           => array(
				'label'           => esc_html__( 'Header Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'header_image_url',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'image_title'              => array(
				'label'           => esc_html__( 'Header Image Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'header_image_url',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'image_orientation'        => array(
				'label'            => esc_html__( 'Image Alignment', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'center' => esc_html__( 'Vertically Centered', 'et_builder' ),
					'bottom' => et_builder_i18n( 'Bottom' ),
				),
				'default_on_front' => 'center',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'image',
				'description'      => esc_html__( 'This controls the orientation of the image within the module.', 'et_builder' ),
			),
			'content'                  => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Content entered here will appear below the subheading text.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'content_max_width'        => array(
				'label'            => esc_html__( 'Content Width', 'et_builder' ),
				'description'      => esc_html__( 'Adjust the width of the image within the fullwidth header.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '100%',
				'default_unit'     => '%',
				'default_on_front' => '',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'responsive'       => true,
			),
			'title_font_color'         => array(
				'type' => 'hidden',
			),
			'subhead_font_color'       => array(
				'type' => 'hidden',
			),
			'content_font_color'       => array(
				'type' => 'hidden',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['scroll_down_icon_color']   = array( 'color' => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon' );
		$fields['scroll_down_icon_size']    = array( 'font-size' => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon' );
		$fields['background_overlay_color'] = array( 'background-color' => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_overlay' );

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
		$multi_view = et_pb_multi_view_options( $this );
		// Allowing full html for backwards compatibility.
		$title = $this->_esc_attr( 'title', 'full' );
		// Allowing full html for backwards compatibility.
		$subhead                       = $this->_esc_attr( 'subhead', 'full' );
		$text_orientation              = $this->get_text_orientation();
		$button_one_text               = $this->_esc_attr( 'button_one_text', 'limited' );
		$button_one_url                = $this->props['button_one_url'];
		$button_one_rel                = $this->props['button_one_rel'];
		$button_two_text               = $this->_esc_attr( 'button_two_text', 'limited' );
		$button_two_url                = $this->props['button_two_url'];
		$button_two_rel                = $this->props['button_two_rel'];
		$header_fullscreen             = $this->props['header_fullscreen'];
		$header_scroll_down            = $this->props['header_scroll_down'];
		$background_image              = $this->props['background_image'];
		$parallax                      = $this->props['parallax'];
		$parallax_method               = $this->props['parallax_method'];
		$logo_image_url                = $this->props['logo_image_url'];
		$header_image_url              = $this->props['header_image_url'];
		$content_orientation           = $this->props['content_orientation'];
		$image_orientation             = $this->props['image_orientation'];
		$button_custom_1               = $this->props['custom_button_one'];
		$button_custom_2               = $this->props['custom_button_two'];
		$logo_title                    = $this->_esc_attr( 'logo_title' );
		$logo_alt_text                 = $this->_esc_attr( 'logo_alt_text' );
		$image_alt_text                = $this->_esc_attr( 'image_alt_text' );
		$image_title                   = $this->_esc_attr( 'image_title' );
		$header_level                  = $this->props['title_level'];
		$content_max_width             = $this->props['content_max_width'];
		$content_max_width_tablet      = $this->props['content_max_width_tablet'];
		$content_max_width_phone       = $this->props['content_max_width_phone'];
		$content_max_width_last_edited = $this->props['content_max_width_last_edited'];

		$scroll_down_icon        = $this->props['scroll_down_icon'];
		$scroll_down_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'scroll_down_icon' );
		$scroll_down_icon_tablet = isset( $scroll_down_icon_values['tablet'] ) ? $scroll_down_icon_values['tablet'] : '';
		$scroll_down_icon_phone  = isset( $scroll_down_icon_values['phone'] ) ? $scroll_down_icon_values['phone'] : '';

		$custom_icon_1_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_one_icon' );
		$custom_icon_1        = isset( $custom_icon_1_values['desktop'] ) ? $custom_icon_1_values['desktop'] : '';
		$custom_icon_1_tablet = isset( $custom_icon_1_values['tablet'] ) ? $custom_icon_1_values['tablet'] : '';
		$custom_icon_1_phone  = isset( $custom_icon_1_values['phone'] ) ? $custom_icon_1_values['phone'] : '';

		$custom_icon_2_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_two_icon' );
		$custom_icon_2        = isset( $custom_icon_2_values['desktop'] ) ? $custom_icon_2_values['desktop'] : '';
		$custom_icon_2_tablet = isset( $custom_icon_2_values['tablet'] ) ? $custom_icon_2_values['tablet'] : '';
		$custom_icon_2_phone  = isset( $custom_icon_2_values['phone'] ) ? $custom_icon_2_values['phone'] : '';

		// Scroll Down Icon color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'scroll_down_icon_color',
				'selector'       => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon',
				'hover_selector' => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a:hover .et-pb-icon',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		$this->generate_styles(
			array(
				'base_attr_name' => 'scroll_down_icon_size',
				'selector'       => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon',
				'hover_selector' => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a:hover .et-pb-icon',
				'css_property'   => 'font-size',
				'render_slug'    => $render_slug,
				'type'           => 'range',
			)
		);

		if ( '' !== $content_max_width_tablet || '' !== $content_max_width_phone || '' !== $content_max_width ) {
			$content_max_width_responsive_active = et_pb_get_responsive_status( $content_max_width_last_edited );

			$content_max_width_values = array(
				'desktop' => $content_max_width,
				'tablet'  => $content_max_width_responsive_active ? $content_max_width_tablet : '',
				'phone'   => $content_max_width_responsive_active ? $content_max_width_phone : '',
			);

			et_pb_generate_responsive_css( $content_max_width_values, '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_container .header-content', 'max-width', $render_slug );
		}

		// Background Overlay color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'background_overlay_color',
				'selector'       => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_overlay',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		$button_output = '';

		$button_output .= $this->render_button(
			array(
				'button_classname'    => array( 'et_pb_more_button', 'et_pb_button_one' ),
				'button_custom'       => $button_custom_1,
				'button_rel'          => $button_one_rel,
				'button_text'         => $button_one_text,
				'button_text_escaped' => true,
				'button_url'          => $button_one_url,
				'custom_icon'         => $custom_icon_1,
				'custom_icon_tablet'  => $custom_icon_1_tablet,
				'custom_icon_phone'   => $custom_icon_1_phone,
				'has_wrapper'         => false,
				'multi_view_data'     => $multi_view->render_attrs(
					array(
						'content'    => '{{button_one_text}}',
						'visibility' => array(
							'button_one_text' => '__not_empty',
						),
					)
				),
			)
		);

		$button_output .= $this->render_button(
			array(
				'button_classname'    => array( 'et_pb_more_button', 'et_pb_button_two' ),
				'button_custom'       => $button_custom_2,
				'button_rel'          => $button_two_rel,
				'button_text'         => $button_two_text,
				'button_text_escaped' => true,
				'button_url'          => $button_two_url,
				'custom_icon'         => $custom_icon_2,
				'custom_icon_tablet'  => $custom_icon_2_tablet,
				'custom_icon_phone'   => $custom_icon_2_phone,
				'has_wrapper'         => false,
				'multi_view_data'     => $multi_view->render_attrs(
					array(
						'content'    => '{{button_two_text}}',
						'visibility' => array(
							'button_two_text' => '__not_empty',
						),
					)
				),
			)
		);

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		$header_content = '';
		if ( $multi_view->has_value( 'title' ) || $multi_view->has_value( 'subhead' ) || $multi_view->has_value( 'content' ) || '' !== $button_output || $multi_view->has_value( 'logo_image_url' ) ) {
			$title = $multi_view->render_element(
				array(
					'tag'     => et_pb_process_header_level( $header_level, 'h1' ),
					'content' => '{{title}}',
					'attrs'   => array(
						'class' => 'et_pb_module_header',
					),
				)
			);

			$subhead = $multi_view->render_element(
				array(
					'content' => '{{subhead}}',
					'attrs'   => array(
						'class' => 'et_pb_fullwidth_header_subhead',
					),
				)
			);

			$logo_image_class            = 'header-logo';
			$logo_image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'logo_image_url' );

			if ( ! empty( $logo_image_attachment_class ) ) {
				$logo_image_class .= " {$logo_image_attachment_class}";
			}

			$logo_image_url = $multi_view->render_element(
				array(
					'tag'      => 'img',
					'attrs'    => array(
						'src'   => '{{logo_image_url}}',
						'class' => esc_attr( $logo_image_class ),
						'title' => esc_attr( $logo_title ),
						'alt'   => esc_attr( $logo_alt_text ),
					),
					'required' => 'logo_image_url',
				)
			);

			$content = $multi_view->render_element(
				array(
					'tag'      => 'div',
					'content'  => '{{content}}',
					'attrs'    => array(
						'class' => 'et_pb_header_content_wrapper',
					),
					'required' => false,
				)
			);

			$header_content = sprintf(
				'<div class="header-content-container%6$s">
					<div class="header-content">
						%3$s
						%1$s
						%2$s
						%4$s
						%5$s
					</div>
				</div>',
				$title,
				$subhead,
				$logo_image_url,
				$content,
				( '' !== $button_output ? $button_output : '' ),
				( '' !== $content_orientation ? sprintf( ' %1$s', esc_attr( $content_orientation ) ) : '' )
			);
		}

		$header_image = '';

		if ( $multi_view->has_value( 'header_image_url' ) ) {
			$header_image_attrs = array(
				'src'   => '{{header_image_url}}',
				'alt'   => esc_attr( $image_alt_text ),
				'title' => esc_attr( $image_title ),
			);

			$header_image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'header_image_url' );

			if ( ! empty( $header_image_attachment_class ) ) {
				$header_image_attrs['class'] = esc_attr( $header_image_attachment_class );
			}

			$header_image = sprintf(
				'<div class="header-image-container%2$s">
					<div class="header-image">
					%1$s
					</div>
				</div>',
				$multi_view->render_element(
					array(
						'tag'   => 'img',
						'attrs' => $header_image_attrs,
					)
				),
				( '' !== $image_orientation ? sprintf( ' %1$s', esc_attr( $image_orientation ) ) : '' )
			);

			$this->add_classname( 'et_pb_header_with_image' );

		}

		// Responsive Scroll Down Icon.
		$scroll_down_output = '';
		if ( 'off' !== $header_scroll_down || '' !== $scroll_down_icon || '' !== $scroll_down_icon_tablet || '' !== $scroll_down_icon_phone ) {
			$scroll_down_container_classes = '';

			$scroll_down_icon_markup_tablet = '';
			if ( '' !== $scroll_down_icon_tablet ) {
				$scroll_down_container_classes .= ' scroll-down-container-tablet';
				$scroll_down_icon_markup_tablet = sprintf(
					'<span class="scroll-down-tablet et-pb-icon">%1$s</span>',
					esc_html( et_pb_process_font_icon( $scroll_down_icon_tablet, 'et_pb_get_font_down_icon_symbols' ) )
				);
			}

			$scroll_down_icon_markup_phone = '';
			if ( '' !== $scroll_down_icon_phone ) {
				$scroll_down_container_classes .= ' scroll-down-container-phone';
				$scroll_down_icon_markup_phone  = sprintf(
					'<span class="scroll-down-phone et-pb-icon">%1$s</span>',
					esc_html( et_pb_process_font_icon( $scroll_down_icon_phone, 'et_pb_get_font_down_icon_symbols' ) )
				);
			}

			$scroll_down_output .= sprintf(
				'<a href="#" class="scroll-down-container%4$s">
					<span class="scroll-down et-pb-icon">%1$s</span>
					%2$s
					%3$s
				</a>',
				esc_html( et_pb_process_font_icon( $scroll_down_icon, 'et_pb_get_font_down_icon_symbols' ) ),
				$scroll_down_icon_markup_tablet,
				$scroll_down_icon_markup_phone,
				$scroll_down_container_classes
			);
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Module classnames
		$this->add_classname(
			array(
				"et_pb_text_align_{$text_orientation}",
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( 'off' !== $header_fullscreen ) {
			$this->add_classname( 'et_pb_fullscreen' );
		}

		$muti_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_header_with_image' => array(
						'header_image_url' => '__not_empty',
					),
				),
			)
		);

		$output = sprintf(
			'<section%7$s class="%1$s"%9$s%10$s>
				%6$s
				%8$s
				%11$s
				%12$s
				<div class="et_pb_fullwidth_header_container%5$s">
					%2$s
					%3$s
				</div>
				<div class="et_pb_fullwidth_header_overlay"></div>
				<div class="et_pb_fullwidth_header_scroll">%4$s</div>
			</section>',
			$this->module_classname( $render_slug ),
			( '' !== $header_content ? $header_content : '' ),
			( '' !== $header_image ? $header_image : '' ),
			( 'off' !== $header_scroll_down ? $scroll_down_output : '' ),
			( '' !== $text_orientation ? sprintf( ' %1$s', esc_attr( $text_orientation ) ) : '' ), // #5
			$parallax_image_background,
			$this->module_id(),
			$video_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $muti_view_data_attr ), // #10
			et_core_esc_previously( $this->background_pattern() ), // #11
			et_core_esc_previously( $this->background_mask() ) // #12
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
		$name    = isset( $args['name'] ) ? $args['name'] : '';
		$mode    = isset( $args['mode'] ) ? $args['mode'] : '';
		$context = isset( $args['context'] ) ? $args['context'] : '';

		$fields_need_escape_full = array(
			'title',
			'subhead',
		);

		if ( $raw_value && 'content' === $context && in_array( $name, $fields_need_escape_full, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'full', $raw_value );
		}

		$fields_need_escape_limited = array(
			'button_one_text',
			'button_two_text',
		);

		if ( $raw_value && 'content' === $context && in_array( $name, $fields_need_escape_limited, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'limited' );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Header();
}

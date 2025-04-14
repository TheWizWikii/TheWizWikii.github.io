<?php

class ET_Builder_Module_Fullwidth_Menu extends ET_Builder_Module {
	/**
	 * Menu module slug.
	 *
	 * @var string
	 */
	protected static $menu_slug = 'et_pb_fullwidth_menu';

	function init() {
		$this->name       = esc_html__( 'Fullwidth Menu', 'et_builder' );
		$this->plural     = esc_html__( 'Fullwidth Menus', 'et_builder' );
		$this->slug       = self::$menu_slug;
		$this->vb_support = 'on';
		$this->fullwidth  = true;

		$this->main_css_element = '%%order_class%%.' . self::$menu_slug;

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
					'image'        => esc_html__( 'Logo', 'et_builder' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout'         => array(
						'title'    => et_builder_i18n( 'Layout' ),
						'priority' => 19,
					),
					'menu'           => array(
						'title'    => esc_html__( 'Menu Text', 'et_builder' ),
						'priority' => 29,
					),
					'dropdown'       => array(
						'title'    => esc_html__( 'Dropdown Menu', 'et_builder' ),
						'priority' => 39,
					),
					'icon_settings'  => array(
						'title'    => esc_html__( 'Icons', 'et_builder' ),
						'priority' => 49,
					),
					'image_settings' => array(
						'title'    => esc_html__( 'Logo', 'et_builder' ),
						'priority' => 59,
					),
					'cart_quantity'  => array(
						'title'    => esc_html__( 'Cart Quantity Text', 'et_builder' ),
						'priority' => 69,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation'  => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'      => array(
				'menu'          => array(
					'label'           => esc_html__( 'Menu', 'et_builder' ),
					'css'             => array(
						'main'         => "{$this->main_css_element} ul li a",
						'limited_main' => "{$this->main_css_element} ul li a, {$this->main_css_element} ul li",
						'hover'        => "{$this->main_css_element} ul li:hover > a",
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'font_size'       => array(
						'default'        => '14px',
						'range_settings' => array(
							'min'  => '12',
							'max'  => '24',
							'step' => '1',
						),
					),
					'letter_spacing'  => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '8',
							'step' => '1',
						),
					),
					'hide_text_align' => true,
				),
				'cart_quantity' => array(
					'label'           => esc_html__( 'Cart Quantity', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_menu__icon.et_pb_menu__icon__with_count .et_pb_menu__cart-count",
					),
					'font'            => array(
						'mobile_options' => false,
						'show_if'        => array(
							'show_cart_quantity' => 'on',
						),
					),
					'line_height'     => array(
						'default'        => '1em',
						'mobile_options' => false,
						'show_if'        => array(
							'show_cart_quantity' => 'on',
						),
					),
					'font_size'       => array(
						'default'        => '14px',
						'mobile_options' => false,
						'range_settings' => array(
							'min'  => '12',
							'max'  => '24',
							'step' => '1',
						),
						'show_if'        => array(
							'show_cart_quantity' => 'on',
						),
					),
					'letter_spacing'  => array(
						'default'        => '0px',
						'mobile_options' => false,
						'range_settings' => array(
							'min'  => '0',
							'max'  => '8',
							'step' => '1',
						),
						'show_if'        => array(
							'show_cart_quantity' => 'on',
						),
					),
					'text_color'      => array(
						'mobile_options' => false,
						'show_if'        => array(
							'show_cart_quantity' => 'on',
						),
					),
					'text_shadow'     => array(
						'mobile_options' => false,
						'show_if'        => array(
							'show_cart_quantity' => 'on',
						),
					),
					'hide_text_align' => true,
				),
			),
			'background' => array(
				'options' => array(
					'background_color' => array(
						'default' => '#ffffff',
					),
				),
			),
			'borders'    => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_menu__logo-wrap .et_pb_menu__logo img',
							'border_styles' => '%%order_class%% .et_pb_menu__logo-wrap .et_pb_menu__logo img',
						),
					),
					'label_prefix' => esc_html__( 'Logo', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image_settings',
				),
			),
			'box_shadow' => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%%, %%order_class%% .sub-menu',
						'overlay' => 'inset',
					),
				),
				'image'   => array(
					'label'           => esc_html__( 'Logo Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image_settings',
					'css'             => array(
						'main'    => '%%order_class%% .et_pb_menu__logo-wrap .et_pb_menu__logo',
						'overlay' => 'inset',
					),
				),
			),
			'text'       => array(
				'use_background_layout' => true,
				'toggle_slug'           => 'menu',
				'options'               => array(
					'text_orientation'  => array(
						'default_on_front' => 'left',
						'depends_show_if'  => 'left_aligned',
						'depends_on'       => array(
							'menu_style',
						),
					),
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'filters'    => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image_settings',
					'css'         => array(
						'main' => '%%order_class%% .et_pb_menu__logo-wrap img',
					),
				),
			),
			'image'      => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_menu__logo-wrap img',
				),
			),
			'button'     => false,
		);

		$this->custom_css_fields = array(
			'menu_link'          => array(
				'label'    => esc_html__( 'Menu Link', 'et_builder' ),
				'selector' => '.et-menu-nav li a',
			),
			'active_menu_link'   => array(
				'label'    => esc_html__( 'Active Menu Link', 'et_builder' ),
				'selector' => '.et-menu-nav li.current-menu-item a',
			),
			'dropdown_container' => array(
				'label'    => esc_html__( 'Dropdown Menu Container', 'et_builder' ),
				'selector' => '.et-menu-nav li ul.sub-menu',
			),
			'dropdown_links'     => array(
				'label'    => esc_html__( 'Dropdown Menu Links', 'et_builder' ),
				'selector' => '.et-menu-nav li ul.sub-menu a',
			),
			'menu_logo'          => array(
				'label'    => esc_html__( 'Menu Logo', 'et_builder' ),
				'selector' => '.et_pb_menu__logo',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'Q2heZC2GbNg',
				'name' => esc_html__( 'An introduction to the Fullwidth Menu module', 'et_builder' ),
			),
		);
	}


	function get_fields() {
		$et_accent_color = et_builder_accent_color();

		$fields = array(
			'menu_id'                         => array(
				'label'            => esc_html__( 'Menu', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => et_builder_get_nav_menus_options(),
				'description'      => sprintf(
					'<p class="description">%2$s. <a href="%1$s" target="_blank">%3$s</a>.</p>',
					esc_url( admin_url( 'nav-menus.php' ) ),
					esc_html__( 'Select a menu that should be used in the module', 'et_builder' ),
					esc_html__( 'Click here to create new menu', 'et_builder' )
				),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__menu',
				),
			),
			'menu_style'                      => array(
				'label'           => esc_html__( 'Style', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'left_aligned'         => esc_html__( 'Left Aligned', 'et_builder' ),
					'centered'             => esc_html__( 'Centered', 'et_builder' ),
					'inline_centered_logo' => esc_html__( 'Inline Centered Logo', 'et_builder' ),
				),
				'default'         => 'left_aligned',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'submenu_direction'               => array(
				'label'            => esc_html__( 'Dropdown Menu Direction', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'downwards' => esc_html__( 'Downwards', 'et_builder' ),
					'upwards'   => esc_html__( 'Upwards', 'et_builder' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Here you can choose the direction that your sub-menus will open. You can choose to have them open downwards or upwards.', 'et_builder' ),
				'computed_affects' => array(
					'__menu',
				),
			),
			'fullwidth_menu'                  => array(
				'label'           => esc_html__( 'Make Menu Links Fullwidth', 'et_builder' ),
				'description'     => esc_html__( 'Menu width is limited by your website content width. Enabling this option will extend the menu the full width of the browser window.', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'active_link_color'               => array(
				'label'          => esc_html__( 'Active Link Color', 'et_builder' ),
				'description'    => esc_html__( 'An active link is the page currently being visited. You can pick a color to be applied to active links to differentiate them from other links.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'menu',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'dropdown_menu_bg_color'          => array(
				'label'        => esc_html__( 'Dropdown Menu Background Color', 'et_builder' ),
				'description'  => esc_html__( 'Pick a color to be applied to the background of dropdown menus. Dropdown menus appear when hovering over links with sub items.', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'dropdown',
				'hover'        => 'tabs',
				'sticky'       => true,
			),
			'dropdown_menu_line_color'        => array(
				'label'          => esc_html__( 'Dropdown Menu Line Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used for the dividing line between links in dropdown menus. Dropdown menus appear when hovering over links with sub items.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'dropdown',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'dropdown_menu_text_color'        => array(
				'label'        => esc_html__( 'Dropdown Menu Text Color', 'et_builder' ),
				'description'  => esc_html__( 'Pick a color to be used for links in dropdown menus. Dropdown menus appear when hovering over links with sub items.', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'dropdown',
				'hover'        => 'tabs',
				'sticky'       => true,
			),
			'dropdown_menu_active_link_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Active Link Color', 'et_builder' ),
				'description'  => esc_html__( 'Pick a color to be used for active links in dropdown menus. Dropdown menus appear when hovering over links with sub items.', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'dropdown',
				'hover'        => 'tabs',
				'sticky'       => true,
			),
			'mobile_menu_bg_color'            => array(
				'label'          => esc_html__( 'Mobile Menu Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to be used for the menu background color when viewed on a mobile device.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'dropdown',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'mobile_menu_text_color'          => array(
				'label'          => esc_html__( 'Mobile Menu Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used for links in mobile menus.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'dropdown',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'__menu'                          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Menu', 'get_fullwidth_menu' ),
				'computed_depends_on' => array(
					'menu_id',
					'submenu_direction',
				),
			),
			'logo'                            => array(
				'label'              => esc_html__( 'Logo', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Logo', 'et_builder' ),
				'description'        => esc_html__( 'Upload an image to display beside your menu.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'logo_url'                        => array(
				'label'           => esc_html__( 'Logo Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to make your logo a link, input your destination URL here.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'logo_url_new_window'             => array(
				'label'            => esc_html__( 'Logo Link Target', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'      => 'link_options',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'logo_alt'                        => array(
				'label'           => esc_html__( 'Logo Alt Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your logo here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'logo_width'                      => array(
				'label'           => esc_html__( 'Logo Width', 'et_builder' ),
				'description'     => esc_html__( 'Adjust the width of the logo.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'allowed_values'  => et_builder_get_acceptable_css_string_values( 'width' ),
				'default'         => 'auto',
				'default_unit'    => '%',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'responsive'      => true,
				'hover'           => 'tabs',
				'sticky'          => true,
			),
			'logo_max_width'                  => array(
				'label'           => esc_html__( 'Logo Max Width', 'et_builder' ),
				'description'     => esc_html__( 'Adjust the maximum width of the logo.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'allowed_values'  => et_builder_get_acceptable_css_string_values( 'max-width' ),
				'default'         => '100%',
				'default_unit'    => '%',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'responsive'      => true,
				'hover'           => 'tabs',
				'sticky'          => true,
			),
			'logo_height'                     => array(
				'label'           => esc_html__( 'Logo Height', 'et_builder' ),
				'description'     => esc_html__( 'Adjust the height of the logo.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'allowed_values'  => et_builder_get_acceptable_css_string_values( 'height' ),
				'default'         => 'auto',
				'default_unit'    => 'px',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				),
				'responsive'      => true,
				'hover'           => 'tabs',
				'sticky'          => true,
			),
			'logo_max_height'                 => array(
				'label'           => esc_html__( 'Logo Max Height', 'et_builder' ),
				'description'     => esc_html__( 'Adjust the maximum height of the logo.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'sticky'          => true,
				'validate_unit'   => true,
				'allowed_values'  => et_builder_get_acceptable_css_string_values( 'max-height' ),
				'default'         => 'none',
				'default_unit'    => 'px',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				),
				'responsive'      => true,
				'hover'           => 'tabs',
			),
			'show_cart_icon'                  => array(
				'label'           => esc_html__( 'Show Shopping Cart Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'elements',
				'mobile_options'  => true,
				'sticky'          => true,
				'responsive'      => true,
				'hover'           => 'tabs',
				'affects'         => array( 'show_cart_quantity' ),
			),
			'show_cart_quantity'              => array(
				'label'           => esc_html__( 'Show Cart Quantity', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'elements',
				'show_if'         => array(
					'show_cart_icon' => 'on',
				),
			),
			'show_search_icon'                => array(
				'label'           => esc_html__( 'Show Search Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'         => 'off',
				'toggle_slug'     => 'elements',
				'mobile_options'  => true,
				'sticky'          => true,
				'responsive'      => true,
				'hover'           => 'tabs',
			),
			'cart_icon_color'                 => array(
				'default'        => $et_accent_color,
				'label'          => esc_html__( 'Shopping Cart Icon Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your shopping cart icon.', 'et_builder' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'search_icon_color'               => array(
				'default'        => $et_accent_color,
				'label'          => esc_html__( 'Search Icon Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your search icon.', 'et_builder' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'menu_icon_color'                 => array(
				'default'        => $et_accent_color,
				'label'          => esc_html__( 'Hamburger Menu Icon Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your hamburger menu icon.', 'et_builder' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'cart_icon_font_size'             => array(
				'label'            => esc_html__( 'Shopping Cart Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default'          => '17px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => et_builder_get_acceptable_css_string_values( 'font-size' ),
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'sticky'           => true,
				'hover'            => 'tabs',
			),
			'search_icon_font_size'           => array(
				'label'            => esc_html__( 'Search Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default'          => '17px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => et_builder_get_acceptable_css_string_values( 'font-size' ),
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'sticky'           => true,
				'hover'            => 'tabs',
			),
			'menu_icon_font_size'             => array(
				'label'            => esc_html__( 'Hamburger Menu Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default'          => '32px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => et_builder_get_acceptable_css_string_values( 'font-size' ),
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'sticky'           => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$menu_slug = self::$menu_slug;
		$fields    = parent::get_transition_fields_css_props();

		$fields['active_link_color']               = array( 'color' => "%%order_class%%.{$menu_slug} ul li.current-menu-item a" );
		$fields['dropdown_menu_text_color']        = array( 'color' => "%%order_class%%.{$menu_slug} .nav li ul a" );
		$fields['dropdown_menu_active_link_color'] = array( 'color' => "%%order_class%%.{$menu_slug} .nav li ul li.current-menu-item a" );

		$fields['logo_width']      = array( 'width' => '%%order_class%% .et_pb_row > .et_pb_menu__logo-wrap, %%order_class%% .et_pb_menu__logo-slot' );
		$fields['logo_max_width']  = array( 'max-width' => '%%order_class%% .et_pb_row > .et_pb_menu__logo-wrap, %%order_class%% .et_pb_menu__logo-slot' );
		$fields['logo_height']     = array( 'height' => '%%order_class%% .et_pb_row > .et_pb_menu__logo-wrap .et_pb_menu__logo img, %%order_class%% .et_pb_menu__logo-slot .et_pb_menu__logo-wrap img' );
		$fields['logo_max_height'] = array( 'max-height' => '%%order_class%% .et_pb_row > .et_pb_menu__logo-wrap .et_pb_menu__logo img, %%order_class%% .et_pb_menu__logo-slot .et_pb_menu__logo-wrap img' );

		$fields['menu_icon_color']   = array(
			'color' => '%%order_class%% .mobile_menu_bar:before',
		);
		$fields['search_icon_color'] = array(
			'color' => '%%order_class%% .et_pb_menu__icon.et_pb_menu__search-button, %%order_class%% .et_pb_menu__icon.et_pb_menu__close-search-button',
		);
		$fields['cart_icon_color']   = array(
			'color' => '%%order_class%% .et_pb_menu__icon.et_pb_menu__cart-button',
		);

		$fields['menu_icon_font_size']   = array(
			'font-size' => '%%order_class%% .mobile_menu_bar:before',
		);
		$fields['search_icon_font_size'] = array(
			'font-size' => '%%order_class%% .et_pb_menu__icon.et_pb_menu__search-button, %%order_class%% .et_pb_menu__icon.et_pb_menu__close-search-button',
		);
		$fields['cart_icon_font_size']   = array(
			'font-size' => '%%order_class%% .et_pb_menu__icon.et_pb_menu__cart-button',
		);

		return $fields;
	}

	/**
	 * Add the class with page ID to menu item so it can be easily found by ID in Frontend Builder
	 *
	 * @return menu item object
	 */
	static function modify_fullwidth_menu_item( $menu_item ) {
		// Since PHP 7.1 silent conversion to array is no longer supported.
		$menu_item->classes = (array) $menu_item->classes;

		if ( esc_url( home_url( '/' ) ) === $menu_item->url ) {
			$fw_menu_custom_class = 'et_pb_menu_page_id-home';
		} else {
			$fw_menu_custom_class = 'et_pb_menu_page_id-' . $menu_item->object_id;
		}

		$menu_item->classes[] = $fw_menu_custom_class;
		return $menu_item;
	}

	/**
	 * Get menu markup for menu module
	 *
	 * @return string of menu markup
	 */
	static function get_fullwidth_menu( $args = array() ) {
		$is_fullwidth = 'et_pb_fullwidth_menu' === self::$menu_slug;
		$defaults     = array(
			'submenu_direction' => '',
			'menu_id'           => '',
		);

		// modify the menu item to include the required data
		add_filter( 'wp_setup_nav_menu_item', array( 'ET_Builder_Module_Fullwidth_Menu', 'modify_fullwidth_menu_item' ) );

		$args      = wp_parse_args( $args, $defaults );
		$menu      = '<nav class="et-menu-nav">';
		$menuClass = 'et-menu nav';

		if ( $is_fullwidth ) {
			$menu      = '<nav class="et-menu-nav fullwidth-menu-nav">';
			$menuClass = 'et-menu fullwidth-menu nav';
		}

		// divi_disable_toptier option available in Divi theme only
		if ( ! et_is_builder_plugin_active() && 'on' === et_get_option( 'divi_disable_toptier' ) ) {
			$menuClass .= ' et_disable_top_tier';
		}
		$menuClass .= ( '' !== $args['submenu_direction'] ? sprintf( ' %s', esc_attr( $args['submenu_direction'] ) ) : '' );

		$menu_args = array(
			'theme_location' => '',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_class'     => $menuClass,
			'menu_id'        => '',
			'echo'           => false,
		);

		if ( '' !== $args['menu_id'] ) {
			$menu_args['menu'] = (int) $args['menu_id'];
		} else {
			// When menu ID is not preset, let's use the primary menu.
			// However, it's highly unlikely that the menu module won't have an ID.
			// When were're using menu module via the `menu_id` we dont need the menu's theme location.
			// We only need it when the menu doesn't have any ID and that occurs only used on headers and/or footers,
			// Or any other static places where we need menu by location and not by ID.
			$menu_args['theme_location'] = 'primary-menu';
		}


		$filter     = $is_fullwidth ? 'et_fullwidth_menu_args' : 'et_menu_args';
		$primaryNav = wp_nav_menu( apply_filters( $filter, $menu_args ) );

		if ( empty( $primaryNav ) ) {
			$menu .= sprintf(
				'<ul class="%1$s">
					%2$s',
				esc_attr( $menuClass ),
				( ! et_is_builder_plugin_active() && 'on' === et_get_option( 'divi_home_link' )
					? sprintf(
						'<li%1$s><a href="%2$s">%3$s</a></li>',
						( is_home() ? ' class="current_page_item"' : '' ),
						esc_url( home_url( '/' ) ),
						esc_html__( 'Home', 'et_builder' )
					)
					: ''
				)
			);

			ob_start();

			// @todo: check if Fullwidth Menu module works fine with no menu selected in settings
			if ( et_is_builder_plugin_active() ) {
				wp_page_menu();
			} else {
				show_page_menu( $menuClass, false, false );
				show_categories_menu( $menuClass, false );
			}

			$menu .= ob_get_contents();

			$menu .= '</ul>';

			ob_end_clean();
		} else {
			$menu .= $primaryNav;
		}

		$menu .= '</nav>';

		remove_filter( 'wp_setup_nav_menu_item', array( 'ET_Builder_Module_Fullwidth_Menu', 'modify_fullwidth_menu_item' ) );

		return $menu;
	}

	/**
	 * Apply logo styles.
	 *
	 * @since 4.0
	 *
	 * @param string $render_slug
	 *
	 * @return void
	 */
	protected function apply_logo_styles( $render_slug ) {
		// Remove default opacity if hover color is enabled for links.
		if ( et_builder_is_hover_enabled( 'menu_text_color', $this->props ) ) {
			$el_style = array(
				'selector'    => "{$this->main_css_element} nav > ul > li > a:hover",
				'declaration' => 'opacity: 1;',
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		if ( et_builder_is_hover_enabled( 'dropdown_menu_text_color', $this->props ) ) {
			$el_style = array(
				'selector'    => "{$this->main_css_element} nav > ul > li li a:hover",
				'declaration' => 'opacity: 1;',
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		if ( et_builder_is_hover_enabled( 'dropdown_menu_active_link_color', $this->props ) ) {
			$el_style = array(
				'selector'    => "{$this->main_css_element} nav > ul > li li.current-menu-item a:hover",
				'declaration' => 'opacity: 1;',
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		$logo_width      = $this->props['logo_width'];
		$logo_height     = $this->props['logo_height'];
		$logo_max_height = $this->props['logo_max_height'];

		// Only height or max-height is set, no width set.
		if ( 'auto' === $logo_width && 'auto' !== $logo_height || 'none' !== $logo_max_height ) {
			$el_style = array(
				'selector'    => '%%order_class%% .et_pb_menu__logo-wrap .et_pb_menu__logo img',
				'declaration' => 'width: auto;',
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		$logo_width_selector  = '%%order_class%% .et_pb_row > .et_pb_menu__logo-wrap, %%order_class%% .et_pb_menu__logo-slot';
		$logo_height_selector = '%%order_class%% .et_pb_row > .et_pb_menu__logo-wrap .et_pb_menu__logo img, %%order_class%% .et_pb_menu__logo-slot .et_pb_menu__logo-wrap img';

		// Width.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'logo_width',
				'selector'                        => $logo_width_selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'width',
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		// Max width.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'logo_max_width',
				'selector'                        => $logo_width_selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'max-width',
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		// Height.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'logo_height',
				'selector'                        => $logo_height_selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'height',
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		// Max height.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'logo_max_height',
				'selector'                        => $logo_height_selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'max-height',
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);
	}

	/**
	 * Determine if icon is enabled.
	 *
	 * @since 4.0
	 *
	 * @param string $icon
	 *
	 * @return boolean
	 */
	protected function is_icon_enabled( $icon ) {
		$icon_show_prop = "show_{$icon}_icon";
		$values         = array_values( et_pb_responsive_options()->get_property_values( $this->props, $icon_show_prop, 'off', true ) );
		$values[]       = $this->get_hover_value( $icon_show_prop );
		return false !== strpos( join( $values ), 'on' );
	}

	/**
	 * Apply icon styles.
	 *
	 * @since 4.0
	 *
	 * @param string $render_slug
	 * @param string $icon
	 * @param string $selector
	 *
	 * @return void
	 */
	protected function apply_icon_styles( $render_slug, $icon, $selector ) {
		$font_size_prop   = "{$icon}_icon_font_size";
		$color_prop       = "{$icon}_icon_color";
		$hover            = et_pb_hover_options();
		$sticky           = et_pb_sticky_options();
		$is_sticky_module = $sticky->is_sticky_module( $this->props );

		if ( 'menu' !== $icon && $this->is_icon_enabled( $icon ) ) {
			$icon_show_prop = "show_{$icon}_icon";

			if ( et_pb_responsive_options()->is_responsive_enabled( $this->props, $icon_show_prop ) ) {
				$replacements = array(
					'"off"' => '"none"',
					'"on"'  => '"flex"',
				);
				$values       = et_pb_responsive_options()->get_property_values( $this->props, $icon_show_prop, 'off', true );
				$values       = json_decode( strtr( json_encode( $values ), $replacements ) );
				et_pb_responsive_options()->generate_responsive_css( $values, $selector, 'display', $render_slug, '', '' );
			}

			if ( $hover->is_enabled( $icon_show_prop, $this->props ) ) {
				$hover_display = ( 'on' === $this->get_hover_value( $icon_show_prop ) ) ? 'flex' : 'none';
				$el_style      = array(
					'selector'    => str_replace( '%%order_class%%', '%%order_class%%:hover', $selector ),
					'declaration' => sprintf(
						'display: %1$s;',
						esc_html( $hover_display )
					),
				);

				ET_Builder_Element::set_style( $render_slug, $el_style );
			}

			if ( $sticky->is_enabled( $icon_show_prop, $this->props ) ) {
				$sticky_display = ( 'on' === $sticky->get_value( $icon_show_prop, $this->props ) ) ? 'flex' : 'none';
				$el_style       = array(
					'selector'    => $sticky->add_sticky_to_selectors( $selector, $is_sticky_module ),
					'declaration' => sprintf(
						'display: %1$s;',
						esc_html( $sticky_display )
					),
				);
				ET_Builder_Element::set_style( $render_slug, $el_style );
			}
		}

		// Font size.
		$this->generate_styles(
			array(
				'base_attr_name'                  => $font_size_prop,
				'selector'                        => $selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'font-size',
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		// Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => $color_prop,
				'selector'                        => $selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);
	}

	/**
	 * Render logo.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function render_logo() {
		$multi_view          = et_pb_multi_view_options( $this );
		$logo_alt            = $this->props['logo_alt'];
		$logo_url            = $this->props['logo_url'];
		$logo_url_new_window = $this->props['logo_url_new_window'];

		if ( empty( $logo_alt ) && ! empty( $this->props['logo'] ) ) {
			$logo_id  = attachment_url_to_postid( esc_url( $this->props['logo'] ) );
			$logo_alt = get_post_meta( $logo_id, '_wp_attachment_image_alt', true );
		}

		$logo_image_attrs = array(
			'src'    => '{{logo}}',
			'alt'    => $logo_alt,
		);

		$logo_image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'logo' );

		if ( ! empty( $logo_image_attachment_class ) ) {
			$logo_image_attrs['class'] = esc_attr( $logo_image_attachment_class );
		}

		$logo_html = $multi_view->render_element(
			array(
				'tag'            => 'img',
				'attrs'          => $logo_image_attrs,
				'required'       => 'logo',
				'hover_selector' => '%%order_class%% .et_pb_menu__logo-wrap .et_pb_menu__logo img',
			)
		);

		if ( empty( $logo_html ) ) {
			return '';
		}

		if ( ! empty( $logo_url ) ) {
			$target = ( 'on' === $logo_url_new_window ) ? 'target="_blank"' : '';

			$logo_html = sprintf(
				'<a href="%1$s" %2$s>%3$s</a>',
				esc_url( $logo_url ),
				et_core_intentionally_unescaped( $target, 'fixed_string' ),
				et_core_esc_previously( $logo_html )
			);
		}

		$logo_html = sprintf(
			'<div class="et_pb_menu__logo-wrap">
			  <div class="et_pb_menu__logo">
				%1$s
			  </div>
			</div>',
			$logo_html
		);

		return $logo_html;
	}

	/**
	 * Render cart button.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function render_cart() {
		if ( ! $this->is_icon_enabled( 'cart' ) ) {
			return '';
		}

		if ( ! class_exists( 'woocommerce' ) || ! WC()->cart ) {
			return '';
		}

		$url          = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
		$show_count   = 'on' === self::$_->array_get( $this->props, 'show_cart_quantity', '' );
		$items_number = $show_count ? WC()->cart->get_cart_contents_count() : 0;
		$output       = sprintf(
			'<a href="%1$s" class="et_pb_menu__icon et_pb_menu__cart-button%3$s">
				%2$s
			</a>',
			esc_url( $url ),
			$show_count ?
				sprintf(
					'<span class="et_pb_menu__cart-count">%1$s</span>',
					esc_html(
						sprintf(
							_nx( '%1$s Item', '%1$s Items', $items_number, 'WooCommerce items number', 'Divi' ),
							number_format_i18n( $items_number )
						)
					)
				) : '',
			$show_count ? ' et_pb_menu__icon__with_count' : ''
		);

		/**
		 * Filter the cart icon output.
		 *
		 * @since 4.0.6
		 *
		 * @param string $output
		 * @param string $menu_slug
		 *
		 * @return string
		 */
		return apply_filters( 'et_pb_menu_module_cart_output', $output, self::$menu_slug );
	}

	/**
	 * Render search button.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function render_search() {
		if ( ! $this->is_icon_enabled( 'search' ) ) {
			return '';
		}

		return '<button type="button" class="et_pb_menu__icon et_pb_menu__search-button"></button>';
	}

	/**
	 * Render search form.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function render_search_form() {
		if ( ! $this->is_icon_enabled( 'search' ) ) {
			return '';
		}

		return sprintf(
			'<div class="et_pb_menu__search-container et_pb_menu__search-container--disabled">
				<div class="et_pb_menu__search">
					<form role="search" method="get" class="et_pb_menu__search-form" action="%1$s">
						<input type="search" class="et_pb_menu__search-input" placeholder="%2$s" name="s" title="%3$s" />
					</form>
					<button type="button" class="et_pb_menu__icon et_pb_menu__close-search-button"></button>
				</div>
			</div>',
			esc_url( home_url( '/' ) ),
			esc_attr__( 'Search &hellip;', 'et_builder' ),
			esc_attr__( 'Search for:', 'et_builder' )
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
		$menu_slug         = self::$menu_slug;
		$background_color  = $this->props['background_color'];
		$menu_id           = $this->props['menu_id'];
		$submenu_direction = $this->props['submenu_direction'];
		$menu_style        = $this->props['menu_style'];

		$dropdown_menu_bg_color  = $this->props['dropdown_menu_bg_color'];
		$dropdown_menu_animation = $this->props['dropdown_menu_animation'];

		$mobile_menu_bg_color        = $this->props['mobile_menu_bg_color'];
		$mobile_menu_bg_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'mobile_menu_bg_color' );
		$mobile_menu_bg_color_tablet = isset( $mobile_menu_bg_color_values['tablet'] ) ? $mobile_menu_bg_color_values['tablet'] : '';
		$mobile_menu_bg_color_phone  = isset( $mobile_menu_bg_color_values['phone'] ) ? $mobile_menu_bg_color_values['phone'] : '';

		$style = '';

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$menu = self::get_fullwidth_menu(
			array(
				'menu_id'           => $menu_id,
				'submenu_direction' => $submenu_direction,
			)
		);

		$is_sticky_module = et_pb_sticky_options()->is_sticky_module( $this->props );

		// Active Link Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'active_link_color',
				'selector'                        => "%%order_class%%.{$menu_slug} ul li.current-menu-item a",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'important'                       => true,
				'type'                            => 'color',
			)
		);

		if ( '' !== $background_color || '' !== $dropdown_menu_bg_color ) {
			$et_menu_bg_color = '' !== $dropdown_menu_bg_color ? $dropdown_menu_bg_color : $background_color;

			$el_style = array(
				'selector'    => "%%order_class%%.{$menu_slug} .nav li ul",
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $et_menu_bg_color )
				),
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		$this->generate_styles(
			array(
				'responsive'                      => false,
				'base_attr_name'                  => 'dropdown_menu_bg_color',
				'selector'                        => "%%order_class%%.{$menu_slug} .nav li ul",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'important'                       => true,
				'type'                            => 'color',
			)
		);

		$dropdown_menu_line_color_selector = 'upwards' === $submenu_direction
			? "%%order_class%%.{$menu_slug} .et-menu-nav > ul.upwards li ul"
			: "%%order_class%%.{$menu_slug} .nav li ul";

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'dropdown_menu_line_color',
				'selector'                        => $dropdown_menu_line_color_selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'border-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Dropdown Menu Line Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'dropdown_menu_line_color',
				'selector'                        => "%%order_class%%.{$menu_slug} .et_mobile_menu",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'border-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'dropdown_menu_text_color',
				'selector'                        => "%%order_class%%.{$menu_slug} .nav li ul.sub-menu a",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'dropdown_menu_active_link_color',
				'selector'                        => "%%order_class%%.{$menu_slug} .nav li ul.sub-menu li.current-menu-item a",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Mobile Menu Background Color.
		$is_mobile_menu_bg_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'mobile_menu_bg_color' );
		$mobile_menu_bg_color         = empty( $mobile_menu_bg_color ) ? $background_color : $mobile_menu_bg_color;
		$mobile_menu_bg_color_tablet  = empty( $mobile_menu_bg_color_tablet ) ? $mobile_menu_bg_color : $mobile_menu_bg_color_tablet;
		$mobile_menu_bg_color_phone   = empty( $mobile_menu_bg_color_phone ) ? $mobile_menu_bg_color_tablet : $mobile_menu_bg_color_phone;
		$mobile_menu_bg_color_values  = array(
			'desktop' => esc_html( $mobile_menu_bg_color ),
			'tablet'  => $is_mobile_menu_bg_responsive ? esc_html( $mobile_menu_bg_color_tablet ) : '',
			'phone'   => $is_mobile_menu_bg_responsive ? esc_html( $mobile_menu_bg_color_phone ) : '',
		);
		et_pb_responsive_options()->generate_responsive_css( $mobile_menu_bg_color_values, "%%order_class%%.{$menu_slug} .et_mobile_menu, %%order_class%%.{$menu_slug} .et_mobile_menu ul", 'background-color', $render_slug, ' !important;', 'color' );

		$this->generate_styles(
			array(
				'responsive'                      => false,
				'base_attr_name'                  => 'mobile_menu_bg_color',
				'selector'                        => "%%order_class%%.{$menu_slug} .et_mobile_menu, %%order_class%%.{$menu_slug} .et_mobile_menu ul",
				'hover_selector'                  => "%%order_class%%.{$menu_slug} .et_mobile_menu:hover, %%order_class%%.{$menu_slug} .et_mobile_menu ul:hover, %%order_class%%.{$menu_slug} .et_mobile_menu:hover ul",
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Mobile Menu Text Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'mobile_menu_text_color',
				'selector'                        => "%%order_class%%.{$menu_slug} .et_mobile_menu a",
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$this->apply_logo_styles( $render_slug );
		$this->apply_icon_styles( $render_slug, 'menu', '%%order_class%% .mobile_nav .mobile_menu_bar:before' );
		$this->apply_icon_styles( $render_slug, 'search', '%%order_class%% .et_pb_menu__icon.et_pb_menu__search-button, %%order_class%% .et_pb_menu__icon.et_pb_menu__close-search-button' );
		$this->apply_icon_styles( $render_slug, 'cart', '%%order_class%% .et_pb_menu__icon.et_pb_menu__cart-button' );

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
				"et_dropdown_animation_{$dropdown_menu_animation}",
			)
		);

		if ( 'on' === $this->props['fullwidth_menu'] ) {
			$this->add_classname( "{$menu_slug}_fullwidth" );
		}

		if ( ! empty( $this->props['logo'] ) ) {
			$this->add_classname( "{$menu_slug}--with-logo" );
		} else {
			$this->add_classname( "{$menu_slug}--without-logo" );
		}

		$this->add_classname( "{$menu_slug}--style-{$menu_style}" );

		// Logo: Add CSS Filters and Mix Blend Mode rules (if set).
		if ( ! empty( $this->props['logo'] ) && array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		$mobile_menu = sprintf(
			'<div class="et_mobile_nav_menu">
				<div class="mobile_nav closed%1$s">
					<span class="mobile_menu_bar"></span>
				</div>
			</div>',
			'upwards' === $submenu_direction ? ' et_pb_mobile_menu_upwards' : ''
		);

		if ( 'inline_centered_logo' === $menu_style ) {
			$output = sprintf(
				'<div%4$s class="%3$s"%2$s%7$s>
					%6$s
					%5$s
					%13$s
					%14$s
					<div class="et_pb_row clearfix">
						%8$s
						<div class="et_pb_menu__wrap">
							%9$s
							<div class="et_pb_menu__menu">
								%1$s
							</div>
							%10$s
							%11$s
						</div>
						%12$s
					</div>
				</div>',
				$menu,
				$style,
				$this->module_classname( $render_slug ),
				$this->module_id(),
				$video_background,
				$parallax_image_background,
				et_core_esc_previously( $data_background_layout ),
				et_core_esc_previously( $this->render_logo() ),
				et_core_esc_previously( $this->render_cart() ),
				et_core_esc_previously( $this->render_search() ),
				et_core_esc_previously( $mobile_menu ),
				et_core_esc_previously( $this->render_search_form() ),
				et_core_esc_previously( $this->background_pattern() ), // #13
				et_core_esc_previously( $this->background_mask() ) // #14
			);
		} else {
			$output = sprintf(
				'<div%4$s class="%3$s"%2$s%7$s>
					%6$s
					%5$s
					%13$s
					%14$s
					<div class="et_pb_row clearfix">
						%8$s
						<div class="et_pb_menu__wrap">
							<div class="et_pb_menu__menu">
								%1$s
							</div>
							%9$s
							%10$s
							%11$s
						</div>
						%12$s
					</div>
				</div>',
				$menu,
				$style,
				$this->module_classname( $render_slug ),
				$this->module_id(),
				$video_background,
				$parallax_image_background,
				et_core_esc_previously( $data_background_layout ),
				et_core_esc_previously( $this->render_logo() ),
				et_core_esc_previously( $this->render_cart() ),
				et_core_esc_previously( $this->render_search() ),
				et_core_esc_previously( $mobile_menu ),
				et_core_esc_previously( $this->render_search_form() ),
				et_core_esc_previously( $this->background_pattern() ), // #13
				et_core_esc_previously( $this->background_mask() ) // #14
			);
		}

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Menu();
}

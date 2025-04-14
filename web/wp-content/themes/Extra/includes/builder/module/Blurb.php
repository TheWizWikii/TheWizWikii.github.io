<?php

class ET_Builder_Module_Blurb extends ET_Builder_Module {
	function init() {
		$this->name                   = esc_html__( 'Blurb', 'et_builder' );
		$this->plural                 = esc_html__( 'Blurbs', 'et_builder' );
		$this->slug                   = 'et_pb_blurb';
		$this->vb_support             = 'on';
		$this->main_css_element       = '%%order_class%%.et_pb_blurb';
		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'image'        => esc_html__( 'Image & Icon', 'et_builder' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'icon_settings' => esc_html__( 'Image & Icon', 'et_builder' ),
					'text'          => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'width'         => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
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
			'fonts'          => array(
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'  => "{$this->main_css_element} .et_pb_module_header, {$this->main_css_element} .et_pb_module_header a",
						'hover' => "{$this->main_css_element}:hover .et_pb_module_header, {$this->main_css_element}:hover .et_pb_module_header a",
					),
					'header_level' => array(
						'default' => 'h4',
					),
				),
				'body'   => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'line_height' => "{$this->main_css_element} p",
						'text_align'  => "{$this->main_css_element} .et_pb_blurb_description",
						'text_shadow' => "{$this->main_css_element} .et_pb_blurb_description",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'main' => "{$this->main_css_element} .et_pb_blurb_description",
						),
					),
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'        => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'        => '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%% .et_pb_main_blurb_image .et-pb-icon',
							'border_radii_hover'  => '%%order_class%%:hover .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%%:hover .et_pb_main_blurb_image .et-pb-icon',
							'border_styles'       => '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%% .et_pb_main_blurb_image .et-pb-icon',
							'border_styles_hover' => '%%order_class%%:hover .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%%:hover .et_pb_main_blurb_image .et-pb-icon',
						),
					),
					'label_prefix' => et_builder_i18n( 'Image/Icon' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'icon_settings',
				),
			),
			'box_shadow'     => array(
				'default' => array(),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'icon_settings',
					'show_if'           => array(
						'use_icon' => 'off',
					),
					'css'               => array(
						'main'        => '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap',
						'hover'       => '%%order_class%%:hover .et_pb_main_blurb_image .et_pb_image_wrap',
						'show_if_not' => array(
							'use_icon' => 'on',
						),
						'overlay'     => 'inset',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'max_width'      => array(
				'css' => array(
					'main'             => $this->main_css_element,
					'module_alignment' => '%%order_class%%.et_pb_blurb.et_pb_module',
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'text_shadow' => "{$this->main_css_element} .et_pb_blurb_container",
				),
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
					'text_orientation'  => array(
						'default_on_front' => 'left',
					),
				),
			),
			'filters'        => array(
				'child_filters_target' => array(
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_settings',
					'depends_show_if' => 'off',
					'label'           => et_builder_i18n( 'Image/Icon' ),
					'css'             => array(
						'main'  => '%%order_class%% .et_pb_main_blurb_image',
						'hover' => '%%order_class%%:hover .et_pb_main_blurb_image',
					),
				),
			),
			'icon_settings'  => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_main_blurb_image',
				),
			),

			'image_icon'     => array(
				'image_icon' => array(
					'margin_padding'  => array(
						'css' => array(
							'important' => 'all',
						),
					),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_settings',
					'label'           => et_builder_i18n( 'Image/Icon' ),
					'css'             => array(
						'padding' => '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%% .et_pb_main_blurb_image .et-pb-icon',
						'margin'  => '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%% .et_pb_main_blurb_image .et-pb-icon',
						'main'    => '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%% .et_pb_main_blurb_image .et-pb-icon',
					),
				),
			),

			'button'         => false,
		);

		$this->custom_css_fields = array(
			'blurb_image'   => array(
				'label'    => esc_html__( 'Blurb Image', 'et_builder' ),
				'selector' => '.et_pb_main_blurb_image',
			),
			'blurb_title'   => array(
				'label'    => esc_html__( 'Blurb Title', 'et_builder' ),
				'selector' => '.et_pb_module_header',
			),
			'blurb_content' => array(
				'label'    => esc_html__( 'Blurb Content', 'et_builder' ),
				'selector' => '.et_pb_blurb_content',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'XW7HR86lp8U',
				'name' => esc_html__( 'An introduction to the Blurb module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$et_accent_color = et_builder_accent_color();

		$image_icon_placement = array(
			'top' => et_builder_i18n( 'Top' ),
		);

		if ( ! is_rtl() ) {
			$image_icon_placement['left'] = et_builder_i18n( 'Left' );
		} else {
			$image_icon_placement['right'] = et_builder_i18n( 'Right' );
		}

		$fields = array(
			'title'                       => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title of your blurb will appear in bold below your blurb image.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'url'                         => array(
				'label'           => esc_html__( 'Title Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to make your blurb a link, input your destination URL here.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window'              => array(
				'label'            => esc_html__( 'Title Link Target', 'et_builder' ),
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
			'use_icon'                    => array(
				'label'            => esc_html__( 'Use Icon', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'toggle_slug'      => 'image',
				'affects'          => array(
					'font_icon',
					'icon_color',
					'image',
					'alt',
				),
				'description'      => esc_html__( 'Here you can choose whether icon set below should be used.', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'font_icon'                   => array(
				'label'           => esc_html__( 'Icon', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'class'           => array( 'et-pb-font-icon' ),
				'toggle_slug'     => 'image',
				'description'     => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'depends_show_if' => 'on',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'icon_color'                  => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Icon Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'image_icon_background_color' => array(
				'label'          => esc_html__( 'Image/Icon Background Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom background color.', 'et_builder' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'image'                       => array(
				'label'              => et_builder_i18n( 'Image' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display at the top of your blurb.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'alt'                         => array(
				'label'           => esc_html__( 'Image Alt Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'et_builder' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'icon_placement'              => array(
				'label'            => esc_html__( 'Image/Icon Placement', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => $image_icon_placement,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'description'      => esc_html__( 'Here you can choose where to place the icon.', 'et_builder' ),
				'default_on_front' => 'top',
				'mobile_options'   => true,
			),
			'image_icon_width'            => array(
				'label'                  => esc_html__( 'Image/Icon Width', 'et_builder' ),
				'toggle_slug'            => 'icon_settings',
				'description'            => esc_html__( 'Here you can choose icon/img width.', 'et_builder' ),
				'type'                   => 'range',
				'range_settings'         => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
				'option_category'        => 'layout',
				'tab_slug'               => 'advanced',
				'mobile_options'         => true,
				'validate_unit'          => true,
				'allowed_units'          => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'responsive'             => true,
				'mobile_options'         => true,
				'sticky'                 => true,
				'default_value_depends'  => array( 'use_icon' ),
				'default_values_mapping' => array(
					'on'  => '96px',
					'off' => '100%',
				),
			),
			'icon_alignment'              => array(
				'label'           => esc_html__( 'Image/Icon Alignment', 'et_builder' ),
				'description'     => esc_html__( 'Align image/icon to the left, right or center.', 'et_builder' ),
				'type'            => 'align',
				'option_category' => 'layout',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'default'         => 'center',
				'mobile_options'  => true,
				'sticky'          => true,
				'show_if'         => array(
					'icon_placement' => 'top',
				),
			),
			'content'                     => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'content_max_width'           => array(
				'label'            => esc_html__( 'Content Width', 'et_builder' ),
				'description'      => esc_html__( 'Adjust the width of the content within the blurb.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'default'          => '550px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '1100',
					'step' => '1',
				),
				'responsive'       => true,
				'sticky'           => true,
				'hover'            => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields               = parent::get_transition_fields_css_props();
		$fields['icon_color'] = array(
			'color' => '%%order_class%% .et-pb-icon',
		);

		$fields['image_icon_background_color'] = array(
			'background-color' => '%%order_class%% .et_pb_only_image_mode_wrap, %%order_class%% .et-pb-icon',
		);

		$fields['background_layout'] = array(
			'color' => '%%order_class%% .et_pb_module_header, %%order_class%% .et_pb_blurb_description',
		);

		$fields['body_text_color'] = array(
			'color' => '%%order_class%% .et_pb_blurb_description',
		);

		$fields['image_icon_width'] = array(
			'font-size' => '%%order_class%% .et_pb_image_wrap .et-pb-icon',
		);

		$fields['content_max_width'] = array(
			'max-width' => '%%order_class%% .et_pb_blurb_content',
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
		$multi_view                    = et_pb_multi_view_options( $this );
		$sticky                        = et_pb_sticky_options();
		$is_sticky_module              = $sticky->is_sticky_module( $this->props );
		$url                           = $this->props['url'];
		$image                         = $this->props['image'];
		$url_new_window                = $this->props['url_new_window'];
		$alt                           = $this->_esc_attr( 'alt' );
		$use_icon                      = $this->props['use_icon'];
		$header_level                  = $this->props['header_level'];
		$content_max_width             = $this->props['content_max_width'];
		$content_max_width_sticky      = $sticky->get_value( 'content_max_width', $this->props, '' );
		$content_max_width_tablet      = $this->props['content_max_width_tablet'];
		$content_max_width_phone       = $this->props['content_max_width_phone'];
		$content_max_width_last_edited = $this->props['content_max_width_last_edited'];
		$icon_placement                = $this->props['icon_placement'];
		$icon_placement_values         = et_pb_responsive_options()->get_property_values( $this->props, 'icon_placement' );
		$icon_placement_tablet         = isset( $this->props['icon_placement_tablet'] ) ? $this->props['icon_placement_tablet'] : '';
		$icon_placement_phone          = isset( $this->props['icon_placement_phone'] ) ? $this->props['icon_placement_phone'] : '';
		$is_icon_placement_responsive  = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'icon_placement' );
		$is_icon_placement_top         = ! $is_icon_placement_responsive ? 'top' === $icon_placement : in_array( 'top', $icon_placement_values, true );
		$animation                     = $this->props['animation'];
		$image_icon_width_sticky       = isset( $this->props['image_icon_width'] ) ? $sticky->get_value( 'image_icon_width', $this->props, '' ) : '';
		$image_icon_width              = isset( $this->props['image_icon_width'] ) ? $this->props['image_icon_width'] : '';
		$image_icon_width_tablet       = isset( $this->props['image_icon_width_tablet'] ) ? $this->props['image_icon_width_tablet'] : '';
		$image_icon_width_phone        = isset( $this->props['image_icon_width_phone'] ) ? $this->props['image_icon_width_phone'] : '';
		$image_icon_width_last_edited  = isset( $this->props['image_icon_width_last_edited'] ) ? $this->props['image_icon_width_last_edited'] : '';
		$image_pathinfo                = pathinfo( $image );
		$is_image_svg                  = isset( $image_pathinfo['extension'] ) ? 'svg' === $image_pathinfo['extension'] : false;

		$icon_selector = '%%order_class%% .et-pb-icon';

		// Icon/image alignment is only rendered if icon/image placement is set to `top`. Note: due
		// to responsive option, icon placement can be set to `left` on desktop but `top` on tablet;
		// this case is considered truthy for $is_icon_placement_top
		if ( $is_icon_placement_top ) {
			$is_icon                    = 'on' === $use_icon;
			$icon_alignment             = $this->props['icon_alignment'];
			$icon_alignment_values      = et_pb_responsive_options()->get_property_values( $this->props, 'icon_alignment' );
			$icon_alignment_last_edited = $this->props['icon_alignment_last_edited'];
			$icon_alignment_margins     = array(
				'left'   => 'auto auto auto 0',
				'center' => 'auto',
				'right'  => 'auto 0 auto auto',
			);

			// Icon and image use different method of aligning and DOM structure. However, if the image's
			// width is less than the wrapper width, it'll need icon's text-align style to align it
			// Hence icon's alignment styling is always being outputted, while image is only when needed
			$icon_alignment_selector  = '%%order_class%% .et_pb_blurb_content';
			$image_alignment_selector = '%%order_class%%.et_pb_blurb .et_pb_image_wrap';

			if ( et_pb_get_responsive_status( $icon_alignment_last_edited ) && '' !== implode( '', $icon_alignment_values ) ) {
				// Icon and less than wrapper width image alignment style
				et_pb_responsive_options()->generate_responsive_css(
					$icon_alignment_values,
					$icon_alignment_selector,
					'text-align',
					$render_slug,
					'',
					'align'
				);

				// Image alignment style
				if ( ! $is_icon ) {
					$image_alignment_values = array();

					foreach ( $icon_alignment_values as $breakpoint => $alignment ) {
						$image_alignment_values[ $breakpoint ] = et_()->array_get(
							$icon_alignment_margins,
							$alignment,
							''
						);
					}

					// Image alignment style
					et_pb_responsive_options()->generate_responsive_css(
						$image_alignment_values,
						$image_alignment_selector,
						'margin',
						$render_slug,
						'',
						'align'
					);
				}
			} else {
				// Let default css handle the alignment if it isn't left or right
				if ( in_array( $icon_alignment, array( 'left', 'right' ) ) ) {
					$icon_alignment_prop_value = $is_icon ? $icon_alignment : et_()->array_get( $icon_alignment_margins, $icon_alignment, '' );

					$el_style = array(
						'selector'    => $icon_alignment_selector,
						'declaration' => sprintf(
							'text-align: %1$s;',
							esc_html( $icon_alignment )
						),
					);
					// Icon and less than wrapper width image alignment style
					ET_Builder_Element::set_style( $render_slug, $el_style );

					// Image alignment style
					if ( ! $is_icon ) {
						$el_style = array(
							'selector'    => $image_alignment_selector,
							'declaration' => sprintf(
								'margin: %1$s;',
								esc_html( et_()->array_get( $icon_alignment_margins, $icon_alignment, '' ) )
							),
						);
						ET_Builder_Element::set_style( $render_slug, $el_style );
					}
				}
			}
		}

		if ( 'off' !== $use_icon ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'image_icon_width',
					'selector'       => $icon_selector,
					'css_property'   => 'font-size',
					'render_slug'    => $render_slug,
					'type'           => 'range',
				)
			);
		} elseif ( 'off' === $use_icon ) {
			// Hover image.
			$image_icon_width_selector = 'top' === $icon_placement && $is_image_svg ? '%%order_class%% .et_pb_main_blurb_image' : '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap';
			$image_icon_width_prop     = $is_image_svg || false !== strpos( $image_icon_width, 'px' ) ? 'width' : 'max-width';

			$this->generate_styles(
				array(
					'base_attr_name' => 'image_icon_width',
					'selector'       => $image_icon_width_selector,
					'css_property'   => $image_icon_width_prop,
					'render_slug'    => $render_slug,
					'type'           => 'input',
					'sticky'         => false,
					'responsive'     => false,
				)
			);
		}

		// Hover content.
		$content_max_width_selector = '%%order_class%% .et_pb_blurb_content';
		$content_max_width_prop     = 'max-width';

		$this->generate_styles(
			array(
				'base_attr_name' => 'content_max_width',
				'selector'       => $content_max_width_selector,
				'css_property'   => $content_max_width_prop,
				'render_slug'    => $render_slug,
				'type'           => 'input',
				'sticky'         => false,
				'responsive'     => false,
			)
		);

		if ( 'off' === $use_icon && ( '' !== $image_icon_width_tablet || '' !== $image_icon_width_phone || '' !== $image_icon_width || '' !== $image_icon_width_sticky || $is_image_svg ) ) {
			$is_size_px = false;

			// If size is given in px, we want to override parent width
			if (
				false !== strpos( $image_icon_width, 'px' ) ||
				false !== strpos( $image_icon_width_tablet, 'px' ) ||
				false !== strpos( $image_icon_width_phone, 'px' )
			) {
				$is_size_px = true;
			}
			// SVG image overwrite. SVG image needs its value to be explicit
			if ( '' === $image_icon_width && $is_image_svg ) {
				$image_icon_width = '100%';
			}

			// Image max width selector.
			$image_icon_width_selectors       = array();
			$image_icon_width_reset_selectors = array();
			$image_icon_width_reset_values    = array();

			$image_icon_width_selector = 'top' === $icon_placement && $is_image_svg ? '%%order_class%% .et_pb_main_blurb_image' : '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap';

			foreach ( array( 'tablet', 'phone' ) as $device ) {
				$device_icon_placement = 'tablet' === $device ? $icon_placement_tablet : $icon_placement_phone;
				if ( empty( $device_icon_placement ) ) {
					continue;
				}

				$image_icon_width_selectors[ $device ] = 'top' === $device_icon_placement && $is_image_svg ? '%%order_class%% .et_pb_main_blurb_image' : '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap';

				$prev_icon_placement = 'tablet' === $device ? $icon_placement : $icon_placement_tablet;
				if ( empty( $prev_icon_placement ) || $prev_icon_placement === $device_icon_placement || ! $is_image_svg ) {
					continue;
				}

				// Image/icon placement setting is related to image width setting. In some cases,
				// user uses different image/icon placement settings for each devices. We need to
				// reset previous device image width styles to make it works with current style.
				$image_icon_width_reset_selectors[ $device ] = '%%order_class%% .et_pb_main_blurb_image';
				$image_icon_width_reset_values[ $device ]    = array( 'width' => '32px' );

				if ( 'top' === $device_icon_placement ) {
					$image_icon_width_reset_selectors[ $device ] = '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap';
					$image_icon_width_reset_values[ $device ]    = array( 'width' => 'auto' );
				}
			}

			// Add image max width desktop selector if user sets different image/icon placement setting.
			if ( ! empty( $image_icon_width_selectors ) ) {
				$image_icon_width_selectors['desktop'] = $image_icon_width_selector;
			}

			$image_icon_width_property = ( $is_image_svg || $is_size_px ) ? 'width' : 'max-width';

			$image_icon_width_responsive_active = et_pb_get_responsive_status( $image_icon_width_last_edited );

			$image_icon_width_values = array(
				'desktop' => $image_icon_width,
				'tablet'  => $image_icon_width_responsive_active ? $image_icon_width_tablet : '',
				'phone'   => $image_icon_width_responsive_active ? $image_icon_width_phone : '',
			);

			$main_image_icon_width_selector = $image_icon_width_selector;

			// Overwrite image max width if there are image max width selectors for different devices.
			if ( ! empty( $image_icon_width_selectors ) ) {
				$main_image_icon_width_selector = $image_icon_width_selectors;

				if ( ! empty( $image_icon_width_selectors['tablet'] ) && empty( $image_icon_width_values['tablet'] ) ) {
					$image_icon_width_values['tablet'] = $image_icon_width_responsive_active ? esc_attr( et_pb_responsive_options()->get_any_value( $this->props, 'image_icon_width_tablet', '100%', true ) ) : esc_attr( $image_icon_width );
				}

				if ( ! empty( $image_icon_width_selectors['phone'] ) && empty( $image_icon_width_values['phone'] ) ) {
					$image_icon_width_values['phone'] = $image_icon_width_responsive_active ? esc_attr( et_pb_responsive_options()->get_any_value( $this->props, 'image_icon_width_phone', '100%', true ) ) : esc_attr( $image_icon_width );
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $image_icon_width_values, $main_image_icon_width_selector, $image_icon_width_property, $render_slug );

			// Reset custom image max width styles.
			if ( ! empty( $image_icon_width_selectors ) && ! empty( $image_icon_width_reset_selectors ) ) {
				et_pb_responsive_options()->generate_responsive_css( $image_icon_width_reset_values, $image_icon_width_reset_selectors, $image_icon_width_property, $render_slug, '', 'input' );
			}

			// Sticky styles.
			if ( ! empty( $image_icon_width_sticky ) ) {
				$sticky_main_image_icon_width_selector   = array();
				$sticky_image_icon_width_reset_selectors = array();
				$sticky_image_icon_width_property        = ( $is_image_svg || false !== strpos( $image_icon_width_sticky, 'px' ) ) ? 'width' : 'max-width';

				if ( is_array( $main_image_icon_width_selector ) ) {
					foreach ( $main_image_icon_width_selector as $device => $selector ) {
						$sticky_main_image_icon_width_selector[ $device ] = $sticky->add_sticky_to_selectors( $selector, $is_sticky_module );
					}
				} else {
					$sticky_main_image_icon_width_selector = $sticky->add_sticky_to_selectors( $main_image_icon_width_selector, $is_sticky_module );
				}

				if ( ! empty( $image_icon_width_reset_selectors ) ) {
					foreach ( $image_icon_width_reset_selectors as $device => $selector ) {
						$sticky_image_icon_width_reset_selectors[ $device ] = $sticky->add_sticky_to_selectors( $selector, $is_sticky_module );
					}
				}

				et_pb_responsive_options()->generate_responsive_css( array_fill_keys( array( 'desktop', 'phone', 'tablet' ), $image_icon_width_sticky ), $sticky_main_image_icon_width_selector, $sticky_image_icon_width_property, $render_slug );

				if ( ! empty( $image_icon_width_reset_values ) && ! empty( $sticky_image_icon_width_reset_selectors ) ) {
					et_pb_responsive_options()->generate_responsive_css( $image_icon_width_reset_values, $sticky_image_icon_width_reset_selectors, $sticky_image_icon_width_property, $render_slug, '', 'input' );
				}
			}
		}

		if ( '' !== $content_max_width_tablet || '' !== $content_max_width_phone || '' !== $content_max_width ) {
			$content_max_width_responsive_active = et_pb_get_responsive_status( $content_max_width_last_edited );

			$content_max_width_values = array(
				'desktop' => $content_max_width,
				'tablet'  => $content_max_width_responsive_active ? $content_max_width_tablet : '',
				'phone'   => $content_max_width_responsive_active ? $content_max_width_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $content_max_width_values, '%%order_class%% .et_pb_blurb_content', 'max-width', $render_slug );

		}

		// Sticky Content Width.
		if ( ! empty( $content_max_width_sticky ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $sticky->add_sticky_to_selectors( '%%order_class%% .et_pb_blurb_content', $is_sticky_module ),
					'declaration' => sprintf(
						'max-width: %1$s;',
						esc_html( $content_max_width_sticky )
					),
				)
			);
		}

		if ( is_rtl() && 'left' === $icon_placement ) {
			$icon_placement = 'right';
		}

		if ( is_rtl() && 'left' === $icon_placement_tablet ) {
			$icon_placement_tablet = 'right';
		}

		if ( is_rtl() && 'left' === $icon_placement_phone ) {
			$icon_placement_phone = 'right';
		}

		$title_tag   = '' !== $url ? 'a' : 'span';
		$title_attrs = array();

		if ( 'a' === $title_tag ) {
			$title_attrs['href'] = $url;

			if ( 'on' === $url_new_window ) {
				$title_attrs['target'] = '_blank';
			}
		}

		$title = $multi_view->render_element(
			array(
				'tag'     => $title_tag,
				'content' => '{{title}}',
				'attrs'   => $title_attrs,
			)
		);

		if ( '' !== $title ) {
			$title = sprintf(
				'<%1$s class="et_pb_module_header">%2$s</%1$s>',
				et_pb_process_header_level( $header_level, 'h4' ),
				et_core_esc_previously( $title )
			);
		}

		// Added for backward compatibility
		if ( empty( $animation ) ) {
			$animation = 'top';
		}

		$image_classes = array( 'et-waypoint', 'et_pb_animation_' . $multi_view->get_value_desktop( 'animation', 'top' ) );

		$animations = $multi_view->get_values( 'animation', false );
		foreach ( $animations as $mode => $animation ) {
			if ( ! in_array( $mode, array( 'tablet', 'phone' ), true ) ) {
				continue;
			}

			$image_classes[] = "et_pb_animation_{$animation}_{$mode}";
		}

		$this->generate_styles(
			array(
				'base_attr_name' => 'image_icon_background_color',
				'selector'       => '%%order_class%% .et_pb_main_blurb_image .et_pb_only_image_mode_wrap, %%order_class%% .et_pb_main_blurb_image .et-pb-icon',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'image' );

		if ( ! empty( $image_attachment_class ) ) {
			$image_classes[] = esc_attr( $image_attachment_class );
		}

		if ( 'off' === $use_icon ) {
			$image = $multi_view->render_element(
				array(
					'tag'      => 'img',
					'attrs'    => array(
						'src'   => '{{image}}',
						'class' => implode( ' ', $image_classes ),
						'alt'   => $alt,
					),
					'required' => 'image',
				)
			);
		} elseif ( 'on' === $use_icon ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_color',
					'selector'       => $icon_selector,
					'css_property'   => 'color',
					'render_slug'    => $render_slug,
					'type'           => 'color',
				)
			);

			$image_classes[] = 'et-pb-icon';

			$use_circle        = ( ! empty( $this->props['border_radii_image'] ) && 'on|100%|100%|100%|100%' === $this->props['border_radii_image'] ) || ( isset( $this->props['use_circle'] ) && 'on' === $this->props['use_circle'] );
			$use_circle_border = $use_circle && ( ! empty( $this->props['border_width_all_image'] ) || ( isset( $this->props['use_circle_border'] ) && 'on' === $this->props['use_circle_border'] ) );

			// We decided to leave these classes for icons since users
			// can declare special css-styles for them throw "custom css" options.
			if ( $use_circle ) {
				$image_classes[] = 'et-pb-icon-circle';
			}

			if ( $use_circle_border ) {
				$image_classes[] = 'et-pb-icon-circle-border';
			}

			// Font Icon Styles.
			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'font_icon',
					'important'      => true,
					'selector'       => $icon_selector,
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);

			$image = $multi_view->render_element(
				array(
					'content' => '{{font_icon}}',
					'attrs'   => array(
						'class' => implode( ' ', $image_classes ),
					),
				)
			);
		}

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		$generate_css_image_filters = '';
		if ( $image && array_key_exists( 'icon_settings', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['icon_settings'] ) ) {
			$generate_css_image_filters = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['icon_settings']['css'], 'main', '%%order_class%%' )
			);
		}

		$image_icon_container = ( 'off' === $use_icon ) ? '<span class="et_pb_image_wrap et_pb_only_image_mode_wrap">%1$s</span>' : '<span class="et_pb_image_wrap">%1$s</span>';
		$image                = $image ? sprintf( $image_icon_container, $image ) : '';
		$image                = $image ? sprintf(
			'<div class="et_pb_main_blurb_image%2$s">%1$s</div>',
			( '' !== $url
				? sprintf(
					'<a href="%1$s"%3$s>%2$s</a>',
					esc_attr( $url ),
					$image,
					( 'on' === $url_new_window ? ' target="_blank"' : '' )
				)
				: $image
			),
			esc_attr( $generate_css_image_filters )
		) : '';

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
				sprintf( ' et_pb_blurb_position_%1$s', esc_attr( $icon_placement ) ),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( ! empty( $icon_placement_tablet ) ) {
			$this->add_classname( "et_pb_blurb_position_{$icon_placement_tablet}_tablet" );
		}
		if ( ! empty( $icon_placement_phone ) ) {
			$this->add_classname( "et_pb_blurb_position_{$icon_placement_phone}_phone" );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => 'et_pb_blurb_description',
				),
			)
		);

		$output = sprintf(
			'<div%5$s class="%4$s"%8$s>
				%7$s
				%6$s
				%9$s
				%10$s
				<div class="et_pb_blurb_content">
					%2$s
					<div class="et_pb_blurb_container">
						%3$s
						%1$s
					</div>
				</div>
			</div>',
			$content,
			et_core_esc_previously( $image ),
			et_core_esc_previously( $title ),
			$this->module_classname( $render_slug ),
			$this->module_id(), // #5
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $this->background_pattern() ), // #9
			et_core_esc_previously( $this->background_mask() ) // #10
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

		if ( $raw_value && 'font_icon' === $name ) {
			return et_pb_get_extended_font_icon_value( $raw_value, true );
		}

		$fields_need_escape = array(
			'button_text',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Blurb();
}

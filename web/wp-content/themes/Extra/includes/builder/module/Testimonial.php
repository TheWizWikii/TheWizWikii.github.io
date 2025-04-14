<?php

class ET_Builder_Module_Testimonial extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Testimonial', 'et_builder' );
		$this->plural           = esc_html__( 'Testimonials', 'et_builder' );
		$this->slug             = 'et_pb_testimonial';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%.et_pb_testimonial';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'image'        => et_builder_i18n( 'Image' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'      => esc_html__( 'Quote Icon', 'et_builder' ),
					'text'      => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 51,
					),
					'image'     => array(
						'title'    => et_builder_i18n( 'Image' ),
						'priority' => 49,
					),
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 100,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'body'     => array(
					'label'            => et_builder_i18n( 'Body' ),
					'css'              => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_content",
					),
					'hide_text_shadow' => true,
					'block_elements'   => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
				'author'   => array(
					'label'          => esc_html__( 'Author', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_author",
					),
					'font'           => array(
						'default' => '|700|||||||',
					),
					'line_height'    => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.5' ) ) . 'em',
					),
					'font_size'      => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'position' => array(
					'label'           => et_builder_i18n( 'Position' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_position, {$this->main_css_element} .et_pb_testimonial_separator",
					),
					'hide_text_align' => true,
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.5' ) ) . 'em',
					),
					'font_size'       => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'company'  => array(
					'label'           => esc_html__( 'Company', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_company, {$this->main_css_element} .et_pb_testimonial_company a",
					),
					'hide_text_align' => true,
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.5' ) ) . 'em',
					),
					'font_size'       => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
			),
			'background'      => array(
				'has_background_color_toggle' => true,
				'use_background_color'        => true,
				'options'                     => array(
					'use_background_color' => array(
						'default' => 'on',
					),
					'background_color'     => array(
						'depends_show_if' => 'on',
						'default'         => '#f5f5f5',
					),
				),
				'settings'                    => array(
					'color' => 'alpha',
				),
			),
			'borders'         => array(
				'default'  => array(),
				'portrait' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_testimonial_portrait, %%order_class%% .et_pb_testimonial_portrait:before',
							'border_styles' => '%%order_class%% .et_pb_testimonial_portrait',
						),
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
					'defaults'     => array(
						'border_radii'  => 'on|90px|90px|90px|90px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
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
						'main' => '%%order_class%% .et_pb_testimonial_portrait:before',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
				'css'                   => array(
					'main' => implode(
						', ',
						array(
							'%%order_class%% .et_pb_testimonial_description p',
							'%%order_class%% .et_pb_testimonial_description a',
							'%%order_class%% .et_pb_testimonial_description .et_pb_testimonial_author',
						)
					),
				),
			),
			'filters'         => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'           => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_testimonial_portrait',
				),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->custom_css_fields = array(
			'testimonial_portrait'    => array(
				'label'    => esc_html__( 'Testimonial Portrait', 'et_builder' ),
				'selector' => '.et_pb_testimonial_portrait',
			),
			'testimonial_description' => array(
				'label'    => esc_html__( 'Testimonial Description', 'et_builder' ),
				'selector' => '.et_pb_testimonial_description',
			),
			'testimonial_author'      => array(
				'label'    => esc_html__( 'Testimonial Author', 'et_builder' ),
				'selector' => '.et_pb_testimonial_author',
			),
			'testimonial_meta'        => array(
				'label'    => esc_html__( 'Testimonial Meta', 'et_builder' ),
				'selector' => '.et_pb_testimonial_meta',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'FkQuawiGWUw',
				'name' => esc_html__( 'An introduction to the Testimonial module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'author'                      => array(
				'label'           => esc_html__( 'Author', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the testimonial author.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'job_title'                   => array(
				'label'           => esc_html__( 'Job Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the job title.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'company_name'                => array(
				'label'           => esc_html__( 'Company', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the company.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'url'                         => array(
				'label'           => esc_html__( 'Company Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the website of the author or leave blank for no link.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window'              => array(
				'label'            => esc_html__( 'Company Link Target', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'      => 'link_options',
				'description'      => esc_html__( 'Choose whether or not the URL should open in a new window.', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'portrait_url'                => array(
				'label'              => et_builder_i18n( 'Image' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'quote_icon'                  => array(
				'label'            => esc_html__( 'Show Quote Icon', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'description'      => esc_html__( 'Choose whether or not the quote icon should be visible.', 'et_builder' ),
				'toggle_slug'      => 'elements',
				'mobile_options'   => true,
				'hover'            => 'tabs',
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
			'quote_icon_color'            => array(
				'label'          => esc_html__( 'Quote Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the quote icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'quote_icon_background_color' => array(
				'label'            => esc_html__( 'Quote Icon Background Color', 'et_builder' ),
				'description'      => esc_html__( 'Pick a color to use for the circular background area behind the quote icon.', 'et_builder' ),
				'type'             => 'color-alpha',
				'custom_color'     => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'default'          => '#f5f5f5',
				'default_on_front' => '',
				'hover'            => 'tabs',
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'font_icon'                   => array(
				'label'          => esc_html__( 'Icon', 'et_builder' ),
				'toggle_slug'    => 'icon',
				'type'           => 'select_icon',
				'class'          => array( 'et-pb-font-icon' ),
				'description'    => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options' => true,
				'hover'          => 'tabs',
				'sticky'         => true,
				'tab_slug'       => 'advanced',
			),
			'portrait_width'              => array(
				'label'           => esc_html__( 'Image Width', 'et_builder' ),
				'description'     => esc_html__( "Adjust the width of the person's portrait photo within the testimonial.", 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image',
				'default_unit'    => 'px',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'portrait_height'             => array(
				'label'           => esc_html__( 'Image Height', 'et_builder' ),
				'description'     => esc_html__( "Adjust the height of the person's portrait photo within the testimonial.", 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image',
				'allowed_units'   => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'use_icon_font_size'          => array(
				'label'            => esc_html__( 'Use Custom Quote Icon Size', 'et_builder' ),
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
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'              => array(
				'label'            => esc_html__( 'Quote Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'default'          => '32px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
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

		$fields['portrait_width']              = array( 'width' => '%%order_class%% .et_pb_testimonial_portrait' );
		$fields['portrait_height']             = array( 'height' => '%%order_class%% .et_pb_testimonial_portrait' );
		$fields['quote_icon_color']            = array( 'color' => '%%order_class%%.et_pb_testimonial:before' );
		$fields['quote_icon_background_color'] = array( 'background-color' => '%%order_class%%.et_pb_testimonial:before' );
		$fields['icon_font_size']              = array(
			'font-size'     => '%%order_class%%:before',
			'border-radius' => '%%order_class%%:before',
			'height'        => '%%order_class%% .et-fb-quick-access-item-testimonial-icon',
			'width'         => '%%order_class%% .et-fb-quick-access-item-testimonial-icon',
			'top'           => '%%order_class%%:before, %%order_class%% .et-fb-quick-access-item-testimonial-icon',
			'margin-left'   => '%%order_class%%:before, %%order_class%% .et-fb-quick-access-item-testimonial-icon',
		);

		return $fields;
	}

	public function get_transition_image_fields_css_props() {
		$fields = parent::get_transition_image_fields_css_props();
		$fields = array_merge( $this->get_transition_borders_fields_css_props( 'portrait' ), $fields );

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
		$author       = $this->_esc_attr( 'author', 'full' );
		$job_title    = $this->_esc_attr( 'job_title' );
		$portrait_url = $this->props['portrait_url'];
		// Allowing full html for backwards compatibility.
		$company_name           = $this->_esc_attr( 'company_name', 'full' );
		$url                    = $this->props['url'];
		$quote_icon             = $this->props['quote_icon'];
		$url_new_window         = $this->props['url_new_window'];
		$use_background_color   = $this->props['use_background_color'];
		$background_color       = $this->props['background_color'];
		$background_color_hover = $this->get_hover_value( 'background_color' );
		$use_icon_font_size     = $this->props['use_icon_font_size'];

		// Potrait Width.
		$this->generate_styles(
			array(
				'hover'                           => false,
				'base_attr_name'                  => 'portrait_width',
				'selector'                        => '%%order_class%% .et_pb_testimonial_portrait',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'width',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		// Potrait Height.
		$this->generate_styles(
			array(
				'hover'                           => false,
				'base_attr_name'                  => 'portrait_height',
				'selector'                        => '%%order_class%% .et_pb_testimonial_portrait',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'height',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		// Quote Icon Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'quote_icon_color',
				'selector'                        => '%%order_class%%.et_pb_testimonial:before',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Quote Icon Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'quote_icon_background_color',
				'selector'                        => '%%order_class%%.et_pb_testimonial:before',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Quote Icon Styles.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family_and_content',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'font_icon',
				'important'      => true,
				'selector'       => '%%order_class%%.et_pb_testimonial:before',
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		// Icon Size.
		// $icon_selector = '%%order_class%%:before';.
		if ( 'off' !== $quote_icon && 'off' !== $use_icon_font_size ) {
			// Icon Font Size.
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => '%%order_class%%:before',
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
					'processor_declaration_format'    => 'font-size:%1$s; border-radius:%1$s; top:-%2$s; margin-left:-%2$s;',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr.
					'processor'                       => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_overlay_icon_font_size',
					),
				)
			);
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$portrait_image = $multi_view->render_element(
			array(
				'tag'      => 'div',
				'attrs'    => array(
					'class' => 'et_pb_testimonial_portrait',
				),
				'styles'   => array(
					'background-image' => 'url({{portrait_url}})',
				),
				'required' => 'portrait_url',
			)
		);

		$metas = array();

		// Job title data.
		$job_title = $multi_view->render_element(
			array(
				'content' => '{{job_title}}',
				'attrs'   => array(
					'class' => 'et_pb_testimonial_position',
				),
			)
		);

		if ( $job_title ) {
			$metas['job_title'] = $job_title;
		}

		// Company name data.
		$company_name = $multi_view->render_element(
			array(
				'content' => '{{company_name}}',
				'attrs'   => array(
					'class' => 'et_pb_testimonial_company',
				),
			)
		);

		if ( $company_name ) {
			$metas['company_name'] = $company_name;
		}

		// Author data.
		$author = $multi_view->render_element(
			array(
				'tag'     => 'span',
				'content' => '{{author}}',
				'attrs'   => array(
					'class' => 'et_pb_testimonial_author',
				),
			)
		);

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

		if ( ! $multi_view->has_value( 'quote_icon', 'on', 'desktop' ) ) {
			$this->add_classname( 'et_pb_icon_off' );
		}

		if ( ! $multi_view->has_value( 'portrait_url', 'desktop' ) ) {
			$this->add_classname( 'et_pb_testimonial_no_image' );
		}

		if ( 'off' === $use_background_color ) {
			$this->add_classname( 'et_pb_testimonial_no_bg' );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		if ( 'on' === $use_background_color ) {
			$el_style = array(
				'selector'    => '%%order_class%%.et_pb_testimonial',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color )
				),
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );

			if ( et_builder_is_hover_enabled( 'background_color', $this->props ) ) {
				$el_style = array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%%.et_pb_testimonial' ),
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $background_color_hover )
					),
				);
				ET_Builder_Element::set_style( $render_slug, $el_style );
			}
		}

		$multi_view_testimonial_content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => 'et_pb_testimonial_content',
				),
			)
		);

		$multi_view_icon_off_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_icon_off'             => array(
						'quote_icon' => 'off',
					),
					'et_pb_testimonial_no_image' => array(
						'portrait_url' => '__empty',
					),
				),
			)
		);
		// Added span wrapper for comma between Job Title and Company Title
		$testimonials_metas_string = implode( '<span class="et_pb_testimonial_separator">,</span> ', $metas );
		$output                    = sprintf(
			'<div%3$s class="%4$s"%10$s%11$s>
				%9$s
				%8$s
				%12$s
				%13$s
				%7$s
				<div class="et_pb_testimonial_description">
					<div class="et_pb_testimonial_description_inner">%1$s</div>
					%2$s
					<p class="et_pb_testimonial_meta">%5$s</p>
				</div>
			</div>',
			$multi_view_testimonial_content,
			et_core_esc_previously( $author ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			et_core_esc_previously( $testimonials_metas_string ), // #5
			'', // Deprecated
			$portrait_image,
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ), // #10
			et_core_esc_previously( $multi_view_icon_off_data_attr ),
			et_core_esc_previously( $this->background_pattern() ), // #12
			et_core_esc_previously( $this->background_mask() ) // #13
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
		$context            = et_()->array_get( $args, 'context', '' );
		$name               = et_()->array_get( $args, 'name', '' );
		$mode               = et_()->array_get( $args, 'mode', '' );
		$url                = $this->props['url'];
		$link_target        = 'on' === $this->props['url_new_window'] ? 'target="_blank"' : '';
		$fields_need_escape = array(
			'author',
			'job_title',
			'company_name',
		);

		if ( ! $raw_value ) {
			return $raw_value;
		}

		if ( $raw_value && 'content' === $context && in_array( $name, $fields_need_escape, true ) ) {
			$raw_value = $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );

			if ( $url && $raw_value ) {
				if ( 'author' === $name && ! $this->_esc_attr( $multi_view->get_name_by_mode( 'company_name', $mode ) ) ) {
					$raw_value = sprintf(
						'<a href="%2$s" %3$s>%1$s</a>',
						$raw_value,
						esc_url( $url ),
						et_core_intentionally_unescaped( $link_target, 'fixed_string' )
					);
				} elseif ( 'company_name' === $name ) {
					$raw_value = sprintf(
						'<a href="%2$s" %3$s>%1$s</a>',
						$raw_value,
						esc_url( $url ),
						et_core_intentionally_unescaped( $link_target, 'fixed_string' )
					);
				}
			}
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Testimonial();
}

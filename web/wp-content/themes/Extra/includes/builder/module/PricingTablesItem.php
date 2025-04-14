<?php

class ET_Builder_Module_Pricing_Tables_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pricing Table', 'et_builder' );
		$this->plural                      = esc_html__( 'Pricing Tables', 'et_builder' );
		$this->slug                        = 'et_pb_pricing_table';
		$this->vb_support                  = 'on';
		$this->main_css_element            = '%%order_class%%';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';
		$this->advanced_setting_title_text = esc_html__( 'New Pricing Table', 'et_builder' );
		$this->settings_text               = esc_html__( 'Pricing Table Settings', 'et_builder' );

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => et_builder_i18n( 'Layout' ),
					'bullet' => esc_html__( 'Bullet', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => '.et_pb_pricing .et_pb_pricing_table%%order_class%%',
							'border_styles' => '.et_pb_pricing .et_pb_pricing_table%%order_class%%',
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#bebebe',
							'style' => 'solid',
						),
					),
				),
				'price'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '.et_pb_pricing %%order_class%%  .et_pb_pricing_content_top',
							'border_styles' => '.et_pb_pricing %%order_class%%  .et_pb_pricing_content_top',
						),
					),
					'option_category' => 'border',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'price',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#bebebe',
							'style' => 'solid',
						),
						'composite'     => array(
							'border_bottom' => array(
								'border_width_bottom' => '1px',
							),
						),
					),
				),
			),
			'fonts'          => array(
				'header'             => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .et_pb_pricing_heading h2, {$this->main_css_element} .et_pb_pricing_heading h1.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h3.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h4.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h5.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h6.et_pb_pricing_title,
						           {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h2, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h1.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h3.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h4.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h5.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h6.et_pb_pricing_title",
						'important' => 'all',
					),
					'line_height'  => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'               => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} ul.et_pb_pricing li",
						'limited_main' => "{$this->main_css_element} ul.et_pb_pricing li, {$this->main_css_element} ul.et_pb_pricing li span, {$this->main_css_element} ul.et_pb_pricing li a",
					),
					'line_height'    => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
				'subheader'          => array(
					'label'       => esc_html__( 'Subtitle', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} .et_pb_pricing_heading .et_pb_best_value",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'price'              => array(
					'label'            => esc_html__( 'Price', 'et_builder' ),
					'css'              => array(
						'main'       => "{$this->main_css_element} .et_pb_et_price .et_pb_sum",
						'text_align' => "{$this->main_css_element} .et_pb_pricing_content_top",
					),
					'line_height'      => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'options_priority' => array(
						'price_text_color' => 8,
					),
				),
				'currency_frequency' => array(
					'label'           => esc_html__( 'Currency &amp; Frequency', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_et_price .et_pb_dollar_sign, {$this->main_css_element} .et_pb_et_price .et_pb_frequency",
					),
					'hide_text_align' => true,
				),
				'excluded'           => array(
					'label'       => esc_html__( 'Excluded Item', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% ul.et_pb_pricing li.et_pb_not_available, %%order_class%% ul.et_pb_pricing li.et_pb_not_available span, %%order_class%% ul.et_pb_pricing li.et_pb_not_available a',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'font_size'   => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
				),
			),
			'background'     => array(
				'css'      => array(
					'main' => "{$this->main_css_element}.et_pb_pricing_table",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button'         => array(
				'button' => array(
					'label'         => et_builder_i18n( 'Button' ),
					'css'           => array(
						'main'         => ".et_pb_pricing {$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
						'limited_main' => ".et_pb_pricing {$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
						'alignment'    => ".et_pb_pricing {$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment' => true,
					'box_shadow'    => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button.et_pb_pricing_table_button',
						),
					),
				),
			),
			'margin_padding' => array(
				'use_margin' => false,
				'css'        => array(
					'important'      => 'all', // Need to overwrite pricing table's styling
					'main'           => '.et_pb_pricing %%order_class%% .et_pb_pricing_heading, .et_pb_pricing %%order_class%% .et_pb_pricing_content_top, .et_pb_pricing %%order_class%% .et_pb_pricing_content',

					'padding-right'  => '%%order_class%% .et_pb_button_wrapper',
					'padding-bottom' => '.et_pb_pricing %%order_class%%',
					'padding-left'   => '%%order_class%% .et_pb_button_wrapper',
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => '%%order_class%%.et_pb_pricing_table, %%order_class%% .et_pb_pricing_content',
					'text_shadow'      => '%%order_class%% .et_pb_pricing_heading, %%order_class%% .et_pb_pricing_content_top, %%order_class%% .et_pb_pricing_content',
				),
			),
			'max_width'      => false,
			'height'         => false,
			'sticky'         => false,
		);

		$this->custom_css_fields = array(
			'pricing_heading'       => array(
				'label'    => esc_html__( 'Pricing Heading', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading',
			),
			'pricing_title'         => array(
				'label'    => esc_html__( 'Pricing Title', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading h2',
			),
			'pricing_subtitle'      => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading .et_pb_best_value',
			),
			'pricing_top'           => array(
				'label'    => esc_html__( 'Pricing Top', 'et_builder' ),
				'selector' => '.et_pb_pricing_content_top',
			),
			'price'                 => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => '.et_pb_et_price',
			),
			'currency'              => array(
				'label'    => esc_html__( 'Currency', 'et_builder' ),
				'selector' => '.et_pb_dollar_sign',
			),
			'frequency'             => array(
				'label'    => esc_html__( 'Frequency', 'et_builder' ),
				'selector' => '.et_pb_frequency',
			),
			'pricing_content'       => array(
				'label'    => esc_html__( 'Pricing Content', 'et_builder' ),
				'selector' => '.et_pb_pricing_content',
			),
			'pricing_item'          => array(
				'label'    => esc_html__( 'Pricing Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li.et_pb_not_available',
			),
			'pricing_button'        => array(
				'label'    => esc_html__( 'Pricing Button', 'et_builder' ),
				'selector' => '.et_pb_pricing_table_button',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured'                => array(
				'label'            => esc_html__( 'Make This Table Featured', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Featuring a table will make it stand out from the rest.', 'et_builder' ),
			),
			'title'                   => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title for the pricing table.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'subtitle'                => array(
				'label'           => esc_html__( 'Subtitle', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a sub title for the table if desired.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'currency'                => array(
				'label'           => esc_html__( 'Currency', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired currency symbol here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'per'                     => array(
				'label'           => esc_html__( 'Frequency', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If your pricing is subscription based, input the subscription payment cycle here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'sum'                     => array(
				'label'           => esc_html__( 'Price', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the value of the product here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_url'              => array(
				'label'           => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for the signup button.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window'          => array(
				'label'            => esc_html__( 'Button Link Target', 'et_builder' ),
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
			'button_text'             => array(
				'label'           => et_builder_i18n( 'Button' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Adjust the text used from the signup button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'content'                 => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => sprintf(
					'%1$s<br/> + %2$s<br/> - %3$s',
					esc_html__( 'Input a list of features that are/are not included in the product. Separate items on a new line, and begin with either a + or - symbol: ', 'et_builder' ),
					esc_html__( 'Included option', 'et_builder' ),
					esc_html__( 'Excluded option', 'et_builder' )
				),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'bullet_color'            => array(
				'label'          => esc_html__( 'Bullet Color', 'et_builder' ),
				'description'    => esc_html__( "Pick a color to use for the bullets that appear next to each list item within the pricing table's feature area.", 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'bullet',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'price_background_color'  => array(
				'label'          => esc_html__( 'Pricing Area Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the background area that appears behind the pricing text.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'price',
				'priority'       => 21,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'header_background_color' => array(
				'label'          => esc_html__( 'Table Header Background Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'header',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['bullet_color']            = array( 'border-color' => '%%order_class%% ul.et_pb_pricing li span:before' );
		$fields['header_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_pricing_heading' );
		$fields['price_background_color']  = array( 'background-color' => '%%order_class%% .et_pb_pricing_content_top' );

		return $fields;
	}

	/**
	 * Inherit value from pricing tables (parent) module
	 *
	 * @since 4.6.0
	 */
	public function maybe_inherit_values() {
		global $et_pb_pricing_tables_sticky_transition;

		// Module item has no sticky option so we can go ahead and inherit parent's sticky attr.
		$this->props['sticky_transition'] = $et_pb_pricing_tables_sticky_transition;
	}

	/**
	 * Generates the module's HTML output based on {@see self::$props}.
	 *
	 * @since 1.0
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string The module's HTML output.
	 */
	public function render( $attrs, $content, $render_slug ) {
		global $et_pb_pricing_tables_num,
			$et_pb_pricing_tables_icon,
			$et_pb_pricing_tables_icon_tablet,
			$et_pb_pricing_tables_icon_phone,
			$et_pb_pricing_tables_button_rel,
			$et_pb_pricing_tables_header_level,
			$et_pb_pricing_tables_sticky;

		$multi_view             = et_pb_multi_view_options( $this );
		$featured               = $this->props['featured'];
		$button_url             = $this->props['button_url'];
		$button_rel             = $this->props['button_rel'];
		$button_text            = $this->_esc_attr( 'button_text', 'limited' );
		$url_new_window         = $this->props['url_new_window'];
		$button_custom          = $this->props['custom_button'];
		$header_level           = $this->props['header_level'];
		$body_text_align_values = et_pb_responsive_options()->get_property_values( $this->props, 'body_text_align' );

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		// Overwrite button rel with pricin tables' button_rel if needed
		if ( in_array( $button_rel, array( '', 'off|off|off|off|off' ) ) && '' !== $et_pb_pricing_tables_button_rel ) {
			$button_rel = $et_pb_pricing_tables_button_rel;
		}

		$et_pb_pricing_tables_num++;

		$custom_table_icon        = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $et_pb_pricing_tables_icon;
		$custom_table_icon_tablet = 'on' === $button_custom && '' !== $custom_icon_tablet ? $custom_icon_tablet : $et_pb_pricing_tables_icon_tablet;
		$custom_table_icon_phone  = 'on' === $button_custom && '' !== $custom_icon_phone ? $custom_icon_phone : $et_pb_pricing_tables_icon_phone;

		// Bullet color.
		$this->generate_styles(
			array(
				'base_attr_name'   => 'bullet_color',
				'selector'         => '%%order_class%% .et_pb_pricing_content ul.et_pb_pricing li span:before',
				'hover_selector'   => '%%order_class%% .et_pb_pricing_content ul.et_pb_pricing:hover li span:before',
				'css_property'     => 'border-color',
				'render_slug'      => $render_slug,
				'type'             => 'color',

				// Selector begins with current module item selector so this will never be sticky.
				'is_sticky_module' => false,
			)
		);

		// Header Background Color. In the parent item, header BG color doesn't has higher selector
		// because it uses .et_pb_pricing_table as hover location. So, we should append the same
		// parent class here because there is no class can be used to make current selector higher.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'header_background_color',
				'selector'                        => '.et_pb_pricing %%order_class%%.et_pb_pricing_table .et_pb_pricing_heading',
				'hover_selector'                  => '.et_pb_pricing %%order_class%%.et_pb_pricing_table:hover .et_pb_pricing_heading',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',

				// Selector begins with parent module selector so this should use value inherited
				// from the parent module for accuracy.
				'is_sticky_module'                => $et_pb_pricing_tables_sticky,
			)
		);

		// Pricing Area Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'   => 'price_background_color',
				'selector'         => '%%order_class%%.et_pb_pricing_table .et_pb_pricing_content_top',
				'hover_selector'   => '%%order_class%%.et_pb_pricing_table:hover .et_pb_pricing_content_top',
				'css_property'     => 'background-color',
				'render_slug'      => $render_slug,
				'type'             => 'color',

				// Selector begins with current module item selector so this will never be sticky.
				'is_sticky_module' => false,
			)
		);

		// Custom Padding Left On Center.
		if ( ! empty( $body_text_align_values ) ) {
			foreach ( $body_text_align_values as $body_text_align_device => $body_text_align_value ) {
				if ( 'center' !== $body_text_align_value ) {
					continue;
				}

				$padding_left_style = array(
					'selector'    => '%%order_class%%.et_pb_pricing_table .et_pb_pricing li',
					'declaration' => esc_html( 'padding-left: 0;' ),
				);

				if ( 'desktop' !== $body_text_align_device ) {
					$current_media_query               = 'tablet' === $body_text_align_device ? 'max_width_980' : 'max_width_767';
					$padding_left_style['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
				}

				ET_Builder_Element::set_style( $render_slug, $padding_left_style );
			}
		}

		$button_url = trim( $button_url );

		$button = $this->render_button(
			array(
				'button_classname'    => array( 'et_pb_pricing_table_button' ),
				'button_custom'       => '' !== $custom_table_icon || '' !== $custom_table_icon_tablet || '' !== $custom_table_icon_phone ? 'on' : 'off',
				'button_rel'          => $button_rel,
				'button_text'         => $button_text,
				'button_text_escaped' => true,
				'button_url'          => $button_url,
				'custom_icon'         => $custom_table_icon,
				'custom_icon_tablet'  => $custom_table_icon_tablet,
				'custom_icon_phone'   => $custom_table_icon_phone,
				'url_new_window'      => $url_new_window,
				'display_button'      => ( '' !== $button_url && $multi_view->has_value( 'button_text' ) ),
				'multi_view_data'     => $multi_view->render_attrs(
					array(
						'content'    => '{{button_text}}',
						'visibility' => array(
							'button_text' => '__not_empty',
							'button_url'  => '__not_empty',
						),
					)
				),
			)
		);

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// inherit header level from parent settings
		$header_level = '' === $header_level && '' !== $et_pb_pricing_tables_header_level ? $et_pb_pricing_tables_header_level : $header_level;

		$title = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $header_level, 'h2' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_pricing_title',
				),
			)
		);

		$subtitle = $multi_view->render_element(
			array(
				'content' => '{{subtitle}}',
				'attrs'   => array(
					'class' => 'et_pb_best_value',
				),
			)
		);

		$currency = $multi_view->render_element(
			array(
				'content' => '{{currency}}',
				'attrs'   => array(
					'class' => 'et_pb_dollar_sign',
				),
			)
		);

		$per = $multi_view->render_element(
			array(
				'content' => '<span class="et_pb_frequency_slash">/</span>{{per}}',
				'attrs'   => array(
					'class' => 'et_pb_frequency',
				),
			)
		);

		$sum = $multi_view->render_element(
			array(
				'content' => '{{sum}}',
				'attrs'   => array(
					'class' => 'et_pb_sum',
				),
			)
		);

		// Module classnames
		if ( 'off' !== $featured ) {
			$this->add_classname( 'et_pb_featured_table' );
		}

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				'et_pb_module',
			)
		);

		$content = $multi_view->render_element(
			array(
				'tag'     => 'ul',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => 'et_pb_pricing',
				),
			)
		);

		$output = sprintf(
			'<div class="%1$s">
				%10$s
				%9$s
				%11$s
				%12$s
				<div class="et_pb_pricing_heading">
					%2$s
					%3$s
				</div>
				<div class="et_pb_pricing_content_top">
					<span class="et_pb_et_price">%6$s%7$s%8$s</span>
				</div>
				<div class="et_pb_pricing_content">
					%4$s
				</div>
				%5$s
			</div>',
			$this->module_classname( $render_slug ),
			et_core_esc_previously( $title ),
			et_core_esc_previously( $subtitle ),
			et_core_esc_previously( $content ),
			et_core_esc_previously( $button ),
			et_core_esc_previously( $currency ),
			et_core_esc_previously( $sum ),
			et_core_esc_previously( $per ),
			$video_background,
			$parallax_image_background,
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

		if ( $raw_value && 'content' === $name && 'content' === $context ) {
			return do_shortcode( et_pb_fix_shortcodes( et_pb_extract_items( $raw_value ) ) );
		}

		$fields_need_escape = array(
			'title',
			'subtitle',
			'currency',
			'per',
			'sum',
			'button_text',
		);

		if ( $raw_value && 'content' === $context && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Pricing_Tables_Item();
}

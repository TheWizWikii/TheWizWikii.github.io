<?php

abstract class ET_Builder_Module_Type_PostContent extends ET_Builder_Module {
	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'text'     => array(
					'label'       => et_builder_i18n( 'Text' ),
					'css'         => array(
						'line_height' => "{$this->main_css_element} p",
						'color'       => "{$this->main_css_element}",
					),
					'line_height' => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font_size'   => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'p',
				),
				'link'     => array(
					'label'       => et_builder_i18n( 'Link' ),
					'css'         => array(
						'main'  => "{$this->main_css_element} a",
						'color' => "{$this->main_css_element}.{$this->slug} a",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'a',
				),
				'ul'       => array(
					'label'       => esc_html__( 'Unordered List', 'et_builder' ),
					'css'         => array(
						'main'        => "{$this->main_css_element} ul li",
						'color'       => "{$this->main_css_element}.{$this->slug} ul li",
						'line_height' => "{$this->main_css_element} ul li",
						'item_indent' => "{$this->main_css_element} ul",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'ul',
				),
				'ol'       => array(
					'label'       => esc_html__( 'Ordered List', 'et_builder' ),
					'css'         => array(
						'main'        => "{$this->main_css_element} ol",
						'color'       => "{$this->main_css_element}.{$this->slug} ol",
						'line_height' => "{$this->main_css_element} ol li",
						'item_indent' => "{$this->main_css_element} ol",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'ol',
				),
				'quote'    => array(
					'label'       => esc_html__( 'Blockquote', 'et_builder' ),
					'css'         => array(
						'main'  => "{$this->main_css_element} blockquote",
						'color' => "{$this->main_css_element}.{$this->slug} blockquote",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'quote',
				),
				'header'   => array(
					'label'       => esc_html__( 'Heading', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} h1",
					),
					'font_size'   => array(
						'default' => absint( et_get_option( 'body_header_size', '30' ) ) . 'px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h1',
				),
				'header_2' => array(
					'label'       => esc_html__( 'Heading 2', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} h2",
					),
					'font_size'   => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h2',
				),
				'header_3' => array(
					'label'       => esc_html__( 'Heading 3', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} h3",
					),
					'font_size'   => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h3',
				),
				'header_4' => array(
					'label'       => esc_html__( 'Heading 4', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} h4",
					),
					'font_size'   => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h4',
				),
				'header_5' => array(
					'label'       => esc_html__( 'Heading 5', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} h5",
					),
					'font_size'   => array(
						'default' => '16px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h5',
				),
				'header_6' => array(
					'label'       => esc_html__( 'Heading 6', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} h6",
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h6',
				),
			),
			'borders'        => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} img",
							'border_styles' => "{$this->main_css_element} img",
						),
						'important' => 'all',
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
				'css'     => array(
					'important' => 'all',
				),
			),
			'box_shadow'     => array(
				'default' => array(),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => "{$this->main_css_element} img",
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'filters'        => array(
				'css' => array(
					'main' => "{$this->main_css_element} img",
				),
			),
			'scroll_effects' => false,
			'link_options'   => false,
		);
	}

	public function get_fields() {
		$fields = array(
			'ul_type'             => array(
				'label'            => esc_html__( 'Unordered List Style Type', 'et_builder' ),
				'description'      => esc_html__( 'This setting adjusts the shape of the bullet point that begins each list item.', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'disc'   => et_builder_i18n( 'Disc' ),
					'circle' => et_builder_i18n( 'Circle' ),
					'square' => et_builder_i18n( 'Square' ),
					'none'   => et_builder_i18n( 'None' ),
				),
				'priority'         => 80,
				'default'          => 'disc',
				'default_on_front' => '',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'ul',
				'mobile_options'   => true,
			),
			'ul_position'         => array(
				'label'            => esc_html__( 'Unordered List Style Position', 'et_builder' ),
				'description'      => esc_html__( 'The bullet point that begins each list item can be placed either inside or outside the parent list wrapper. Placing list items inside will indent them further within the list.', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'outside' => et_builder_i18n( 'Outside' ),
					'inside'  => et_builder_i18n( 'Inside' ),
				),
				'priority'         => 85,
				'default'          => 'outside',
				'default_on_front' => '',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'ul',
				'mobile_options'   => true,
			),
			'ul_item_indent'      => array(
				'label'            => esc_html__( 'Unordered List Item Indent', 'et_builder' ),
				'description'      => esc_html__( 'Increasing indentation will push list items further towards the center of the text content, giving the list more visible separation from the the rest of the text.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'ul',
				'priority'         => 90,
				'default'          => '0px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'mobile_options'   => true,
			),
			'ol_type'             => array(
				'label'            => esc_html__( 'Ordered List Style Type', 'et_builder' ),
				'description'      => esc_html__( 'Here you can choose which types of characters are used to distinguish between each item in the ordered list.', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'decimal'              => 'decimal',
					'armenian'             => 'armenian',
					'cjk-ideographic'      => 'cjk-ideographic',
					'decimal-leading-zero' => 'decimal-leading-zero',
					'georgian'             => 'georgian',
					'hebrew'               => 'hebrew',
					'hiragana'             => 'hiragana',
					'hiragana-iroha'       => 'hiragana-iroha',
					'katakana'             => 'katakana',
					'katakana-iroha'       => 'katakana-iroha',
					'lower-alpha'          => 'lower-alpha',
					'lower-greek'          => 'lower-greek',
					'lower-latin'          => 'lower-latin',
					'lower-roman'          => 'lower-roman',
					'upper-alpha'          => 'upper-alpha',
					'upper-greek'          => 'upper-greek',
					'upper-latin'          => 'upper-latin',
					'upper-roman'          => 'upper-roman',
					'none'                 => 'none',
				),
				'priority'         => 80,
				'default'          => 'decimal',
				'default_on_front' => '',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'ol',
				'mobile_options'   => true,
			),
			'ol_position'         => array(
				'label'            => esc_html__( 'Ordered List Style Position', 'et_builder' ),
				'description'      => esc_html__( 'The characters that begins each list item can be placed either inside or outside the parent list wrapper. Placing list items inside will indent them further within the list.', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'inside'  => et_builder_i18n( 'Inside' ),
					'outside' => et_builder_i18n( 'Outside' ),
				),
				'priority'         => 85,
				'default'          => 'inside',
				'default_on_front' => '',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'ol',
				'mobile_options'   => true,
			),
			'ol_item_indent'      => array(
				'label'            => esc_html__( 'Ordered List Item Indent', 'et_builder' ),
				'description'      => esc_html__( 'Increasing indentation will push list items further towards the center of the text content, giving the list more visible separation from the the rest of the text.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'ol',
				'priority'         => 90,
				'default'          => '0px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'mobile_options'   => true,
			),
			'quote_border_weight' => array(
				'label'            => esc_html__( 'Blockquote Border Weight', 'et_builder' ),
				'description'      => esc_html__( 'Block quotes are given a border to separate them from normal text. You can increase or decrease the size of that border using this setting.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'sub_toggle'       => 'quote',
				'priority'         => 85,
				'default'          => '5px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'quote_border_color'  => array(
				'label'           => esc_html__( 'Blockquote Border Color', 'et_builder' ),
				'description'     => esc_html__( 'Block quotes are given a border to separate them from normal text. Pick a color to use for that border.', 'et_builder' ),
				'type'            => 'color-alpha',
				'option_category' => 'configuration',
				'custom_color'    => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'sub_toggle'      => 'quote',
				'field_template'  => 'color',
				'priority'        => 90,
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
		);

		return $fields;
	}

	public function get_settings_modal_toggles() {
		return array(
			'advanced' => array(
				'toggles' => array(
					'image'  => et_builder_i18n( 'Image' ),
					'text'   => array(
						'title'             => et_builder_i18n( 'Text' ),
						'priority'          => 45,
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'sub_toggles'       => array(
							'p'     => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
							'ul'    => array(
								'name' => 'UL',
								'icon' => 'list',
							),
							'ol'    => array(
								'name' => 'OL',
								'icon' => 'numbered-list',
							),
							'quote' => array(
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							),
						),
					),
					'header' => array(
						'title'             => esc_html__( 'Heading Text', 'et_builder' ),
						'priority'          => 49,
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'h1' => array(
								'name' => 'H1',
								'icon' => 'text-h1',
							),
							'h2' => array(
								'name' => 'H2',
								'icon' => 'text-h2',
							),
							'h3' => array(
								'name' => 'H3',
								'icon' => 'text-h3',
							),
							'h4' => array(
								'name' => 'H4',
								'icon' => 'text-h4',
							),
							'h5' => array(
								'name' => 'H5',
								'icon' => 'text-h5',
							),
							'h6' => array(
								'name' => 'H6',
								'icon' => 'text-h6',
							),
						),
					),
				),
			),
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
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );

		$video_background                 = $this->video_background();
		$parallax_image_background        = $this->get_parallax_image_background();
		$data_background_layout           = '';
		$data_background_layout_hover     = '';
		$data_remove_top_window_classname = '';

		if ( $background_layout_hover_enabled ) {
			$background_layout       = isset( $this->props['background_layout'] ) ? $this->props['background_layout'] :
				array();
			$background_layout_hover = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );

			$data_background_layout = sprintf(
				' data-background-layout="%1$s"',
				esc_attr( $background_layout )
			);

			$data_background_layout_hover = sprintf(
				' data-background-layout-hover="%1$s"',
				esc_attr( $background_layout_hover )
			);
		}

		// Added data attribute which tells VB to remove oder classname after DOMs have been moved
		// and builder is loaded to prevent builder UI style from being overwritten
		if ( et_fb_enabled() ) {
			$data_remove_top_window_classname = sprintf(
				' data-remove-top-window-classname="%1$s""',
				esc_attr( ET_Builder_Element::get_module_order_class( $render_slug ) )
			);
		}

		$output = sprintf(
			'<div%3$s class="%2$s"%6$s%7$s%8$s>
				%5$s
				%4$s
				%9$s
				%10$s
				%1$s
			</div>',
			et_theme_builder_frontend_render_post_content(),
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background, // #5
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover ),
			et_core_esc_previously( $data_remove_top_window_classname ),
			et_core_esc_previously( $this->background_pattern() ), // #9
			et_core_esc_previously( $this->background_mask() ) // #10
		);

		return $output;
	}
}

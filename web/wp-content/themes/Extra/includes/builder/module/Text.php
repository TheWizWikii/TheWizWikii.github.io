<?php

class ET_Builder_Module_Text extends ET_Builder_Module {
	function init() {
		$this->name       = et_builder_i18n( 'Text' );
		$this->plural     = esc_html__( 'Texts', 'et_builder' );
		$this->slug       = 'et_pb_text';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
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
					'width'  => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';

		$this->advanced_fields = array(
			'fonts'          => array(
				'text'     => array(
					'label'           => et_builder_i18n( 'Text' ),
					'css'             => array(
						'line_height' => "{$this->main_css_element}",
						'color'       => "{$this->main_css_element}.et_pb_text",
					),
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font_size'       => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug'     => 'text',
					'sub_toggle'      => 'p',
					'hide_text_align' => true,
				),
				'link'     => array(
					'label'       => et_builder_i18n( 'Link' ),
					'css'         => array(
						'main'  => "{$this->main_css_element} a",
						'color' => "{$this->main_css_element}.et_pb_text a",
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
						'color'       => "{$this->main_css_element}.et_pb_text ul li, {$this->main_css_element}.et_pb_text ol li > ul li",
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
						'main'        => "{$this->main_css_element} ol li",
						'color'       => "{$this->main_css_element}.et_pb_text ol li",
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
						'color' => "{$this->main_css_element}.et_pb_text blockquote",
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
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'sub_toggle'            => 'p',
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options
				'default' => false,
			),
			'button'         => false,
		);

		$this->help_videos = array(
			array(
				'id'   => 'oL00RjEKZaU',
				'name' => esc_html__( 'An introduction to the Text module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'content'             => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
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
				'sticky'           => true,
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
				'sticky'          => true,
				'hover'           => 'tabs',
			),
		);

		return $fields;
	}

	function convert_embeds( $matches ) {
		$url = $matches[1];

		if ( strpos( $url, '?v=' ) ) {
			// e.g. https://www.youtube.com/watch?v=Wx6bTxiOmRc
			$pieces   = explode( 'v=', $url );
			$video_id = $pieces[1];
		} else {
			// e.g. https://youtu.be/UABdOJQ3pdo
			$pieces   = explode( '/', $url );
			$video_id = end( $pieces );
		}

		return sprintf(
			'<p><iframe width="1080" height="608" src="%s" allow="%s" allowfullscreen></iframe></p>',
			sprintf( 'https://www.youtube.com/embed/%s', esc_attr( $video_id ) ),
			'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture'
		);
	}

	/**
	 * Transition fields for Text module.
	 *
	 * @since 3.26
	 *
	 * @return array Fields list in array.
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['quote_border_weight'] = array( 'border-width' => '%%order_class%% blockquote' );
		$fields['quote_border_color']  = array( 'border-color' => '%%order_class%% blockquote' );

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
		$multi_view                      = et_pb_multi_view_options( $this );
		$ul_type_values                  = et_pb_responsive_options()->get_property_values( $this->props, 'ul_type' );
		$ul_position_values              = et_pb_responsive_options()->get_property_values( $this->props, 'ul_position' );
		$ul_item_indent_values           = et_pb_responsive_options()->get_property_values( $this->props, 'ul_item_indent' );
		$ol_type_values                  = et_pb_responsive_options()->get_property_values( $this->props, 'ol_type' );
		$ol_position_values              = et_pb_responsive_options()->get_property_values( $this->props, 'ol_position' );
		$ol_item_indent_values           = et_pb_responsive_options()->get_property_values( $this->props, 'ol_item_indent' );
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		$this->content = et_builder_replace_code_content_entities( $this->content );
		// Un-autop converted GB block comments
		$this->content = preg_replace( '/(<p>)?<!-- (\/)?divi:(.+?) (\/?)-->(<\/p>)?/', '<!-- $2divi:$3 $4-->', $this->content );

		// Convert GB embeds to iframes
		$this->content = preg_replace_callback(
			'/<!-- divi:core-embed\/youtube {"url":"([^"]+)"[\s\S]+?<!-- \/divi:core-embed\/youtube -->/',
			array( $this, 'convert_embeds' ),
			$this->content
		);

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// UL.
		et_pb_responsive_options()->generate_responsive_css( $ul_type_values, '%%order_class%% ul', 'list-style-type', $render_slug, ' !important;', 'type' );
		et_pb_responsive_options()->generate_responsive_css( $ul_position_values, '%%order_class%% ul', 'list-style-position', $render_slug, '', 'type' );
		et_pb_responsive_options()->generate_responsive_css( $ul_item_indent_values, '%%order_class%% ul', 'padding-left', $render_slug, ' !important;' );

		// OL.
		et_pb_responsive_options()->generate_responsive_css( $ol_type_values, '%%order_class%% ol', 'list-style-type', $render_slug, ' !important;', 'type' );
		et_pb_responsive_options()->generate_responsive_css( $ol_position_values, '%%order_class%% ol', 'list-style-position', $render_slug, ' !important;', 'type' );
		et_pb_responsive_options()->generate_responsive_css( $ol_item_indent_values, '%%order_class%% ol', 'padding-left', $render_slug, ' !important;' );

		// Quote.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'quote_border_weight',
				'selector'                        => '%%order_class%% blockquote',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'border-width',
				'important'                       => array( 'hover' ),
				'render_slug'                     => $render_slug,
				'type'                            => 'range',
			)
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'quote_border_color',
				'selector'                        => '%%order_class%% blockquote',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'border-color',
				'important'                       => array( 'hover' ),
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => 'et_pb_text_inner',
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%2$s"%6$s>
				%5$s
				%4$s
				%7$s
				%8$s
				%1$s
			</div>',
			$content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background, // #5
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $this->background_pattern() ), // #7
			et_core_esc_previously( $this->background_mask() ) // #8
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Text();
}

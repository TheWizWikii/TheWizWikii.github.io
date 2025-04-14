<?php

class ET_Builder_Module_Cta extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Call To Action', 'et_builder' );
		$this->plural     = esc_html__( 'Call To Actions', 'et_builder' );
		$this->slug       = 'et_pb_cta';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_promo';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text'  => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 80,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_promo_description div",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'main' => "{$this->main_css_element}",
						),
					),
				),
			),
			'background'     => array(
				'has_background_color_toggle' => true,
				'use_background_color'        => true,
				'options'                     => array(
					'background_color'     => array(
						'depends_show_if' => 'on',
						'default'         => et_builder_accent_color(),
					),
					'use_background_color' => array(
						'default' => 'on',
					),
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_promo.et_pb_module',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'         => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_promo_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_promo_button.et_pb_button",
						'alignment'    => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'main'      => "{$this->main_css_element} .et_pb_button_wrapper .et_pb_promo_button.et_pb_button",
							'important' => 'all',
						),
					),
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'        => '%%order_class%% .et_pb_promo_description, %%order_class%% .et_pb_module_header',
					'text_shadow' => '%%order_class%% .et_pb_promo_description',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'center',
					),
					'background_layout' => array(
						'default' => 'dark',
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'promo_description' => array(
				'label'    => esc_html__( 'Promo Description', 'et_builder' ),
				'selector' => '.et_pb_promo_description',
			),
			'promo_button'      => array(
				'label'                    => esc_html__( 'Promo Button', 'et_builder' ),
				'selector'                 => '.et_pb_promo .et_pb_button.et_pb_promo_button',
				'no_space_before_selector' => true,
			),
			'promo_title'       => array(
				'label'    => esc_html__( 'Promo Title', 'et_builder' ),
				'selector' => '.et_pb_promo_description h2',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'E3AEllqnCus',
				'name' => esc_html__( 'An introduction to the Call To Action module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'          => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your value to action title here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_url'     => array(
				'label'           => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your CTA button.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window' => array(
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
			'button_text'    => array(
				'label'           => et_builder_i18n( 'Button' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text, or leave blank for no button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'content'        => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
		);

		return $fields;
	}

	function get_max_width_additional_css() {
		$additional_css = 'center' === $this->get_text_orientation() ? '; margin: 0 auto;' : '';

		return $additional_css;
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
		$multi_view                 = et_pb_multi_view_options( $this );
		$title                      = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['header_level'], 'h2' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_module_header',
				),
			)
		);
		$button_url                 = $this->props['button_url'];
		$button_rel                 = $this->props['button_rel'];
		$button_text                = $this->_esc_attr( 'button_text', 'limited' );
		$background_color           = $this->props['background_color'];
		$use_background_colors      = et_pb_responsive_options()->get_composite_property_values( $this->props, 'background', 'use_background_color' );
		$use_background_color_hover = et_pb_hover_options()->get_compose_value( 'use_background_color', 'background', $this->props, '' );
		$url_new_window             = $this->props['url_new_window'];
		$button_custom              = $this->props['custom_button'];
		$header_level               = $this->props['header_level'];

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$button_url                = trim( $button_url );

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_promo',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		// Background color class.
		foreach ( $use_background_colors as $mode => $value ) {
			// Ensure value is not empty.
			if ( empty( $value ) ) {
				continue;
			}

			$is_value_on = 'on' === $value;
			$infix_class = $is_value_on ? 'has' : 'no';

			// Desktop doesn't need has background class.
			if ( 'desktop' === $mode && $is_value_on ) {
				continue;
			}

			$this->add_classname( et_pb_responsive_options()->get_field_name( "et_pb_{$infix_class}_bg", $mode ) );
		}

		if ( ! empty( $use_background_color_hover ) ) {
			$is_value_hover_on = 'on' === $use_background_color_hover;
			$infix_class_hover = $is_value_hover_on ? 'has' : 'on';
			$this->add_classname( "et_pb_{$infix_class_hover}_bg_hover" );
		}

		// Remove automatically added classname
		$this->remove_classname( 'et_pb_cta' );

		// Render button
		$button = $this->render_button(
			array(
				'button_classname'    => array( 'et_pb_promo_button' ),
				'button_custom'       => $button_custom,
				'button_rel'          => $button_rel,
				'button_text'         => $button_text,
				'button_text_escaped' => true,
				'button_url'          => $button_url,
				'custom_icon'         => $custom_icon,
				'custom_icon_tablet'  => $custom_icon_tablet,
				'custom_icon_phone'   => $custom_icon_phone,
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

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
			)
		);

		$content_wrapper = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => "{$title}{$content}",
				'attrs'   => array(
					'class' => 'et_pb_promo_description',
				),
				'classes' => array(
					'et_multi_view_hidden' => array(
						'title'   => '__empty',
						'content' => '__empty',
					),
				),
			)
		);

		// Render module output
		$output = sprintf(
			'<div%5$s class="%4$s"%8$s>
				%7$s
				%6$s
				%10$s
				%11$s
				%9$s
				%3$s
			</div>',
			et_core_esc_previously( $title ),
			et_core_esc_previously( $content ),
			$button,
			$this->module_classname( $render_slug ),
			$this->module_id(), // #5
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $content_wrapper ),
			et_core_esc_previously( $this->background_pattern() ), // #10
			et_core_esc_previously( $this->background_mask() ) // #11
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Cta();
}

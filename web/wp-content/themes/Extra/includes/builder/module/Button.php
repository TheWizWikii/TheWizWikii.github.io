<?php

class ET_Builder_Module_Button extends ET_Builder_Module {
	function init() {
		$this->name             = et_builder_i18n( 'Button' );
		$this->plural           = esc_html__( 'Buttons', 'et_builder' );
		$this->slug             = 'et_pb_button';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%';
		$this->wrapper_settings = array(
			// Flag that indicates that this module's wrapper where order class is declared
			// has another wrapper (mostly for button alignment purpose).
			'order_class_wrapper' => true,
		);

		$this->custom_css_fields = array(
			'main_element' => array(
				'label'                    => et_builder_i18n( 'Main Element' ),
				'no_space_before_selector' => true,
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'link'         => et_builder_i18n( 'Link' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment' => esc_html__( 'Alignment', 'et_builder' ),
					'text'      => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'         => array(
				'default' => false,
			),
			'button'          => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => $this->main_css_element,
						'limited_main' => "{$this->main_css_element}.et_pb_button",
					),
					'box_shadow'     => false,
					'margin_padding' => false,
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'padding'   => "{$this->main_css_element}_wrapper {$this->main_css_element}, {$this->main_css_element}_wrapper {$this->main_css_element}:hover",
					'margin'    => "{$this->main_css_element}_wrapper",
					'important' => 'all',
				),
			),
			'text'            => array(
				'use_text_orientation'  => false,
				'use_background_layout' => true,
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'text_shadow'     => array(
				// Text Shadow settings are already included on button's advanced style
				'default' => false,
			),
			'background'      => false,
			'fonts'           => false,
			'max_width'       => false,
			'height'          => false,
			'link_options'    => false,
			'position_fields' => array(
				'css' => array(
					'main' => "{$this->main_css_element}_wrapper",
				),
			),
			'transform'       => array(
				'css' => array(
					'main' => "{$this->main_css_element}_wrapper",
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'XpM2G7tQQIE',
				'name' => esc_html__( 'An introduction to the Button module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'button_url'       => array(
				'label'           => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your button.', 'et_builder' ),
				'toggle_slug'     => 'link',
				'dynamic_content' => 'url',
			),
			'url_new_window'   => array(
				'label'            => esc_html__( 'Button Link Target', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'      => 'link',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'button_text'      => array(
				'label'           => et_builder_i18n( 'Button' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_alignment' => array(
				'label'           => esc_html__( 'Button Alignment', 'et_builder' ),
				'description'     => esc_html__( 'Align your button to the left, right or center of the module.', 'et_builder' ),
				'type'            => 'text_align',
				'option_category' => 'configuration',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'Here you can define the alignment of Button', 'et_builder' ),
				'mobile_options'  => true,
			),
		);

		return $fields;
	}

	/**
	 * Get button alignment.
	 *
	 * @since 3.23 Add responsive support by adding device parameter.
	 *
	 * @param  string $device Current device name.
	 * @return string         Alignment value, rtl or not.
	 */
	public function get_button_alignment( $device = 'desktop' ) {
		$suffix           = 'desktop' !== $device ? "_{$device}" : '';
		$text_orientation = isset( $this->props[ "button_alignment{$suffix}" ] ) ? $this->props[ "button_alignment{$suffix}" ] : '';

		return et_pb_get_alignment( $text_orientation );
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
		$multi_view     = et_pb_multi_view_options( $this );
		$button_url     = $this->props['button_url'];
		$button_rel     = $this->props['button_rel'];
		$button_text    = $this->_esc_attr( 'button_text', 'limited' );
		$url_new_window = $this->props['url_new_window'];
		$button_custom  = $this->props['custom_button'];

		$button_alignment              = $this->get_button_alignment();
		$is_button_aligment_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'button_alignment' );
		$button_alignment_tablet       = $is_button_aligment_responsive ? $this->get_button_alignment( 'tablet' ) : '';
		$button_alignment_phone        = $is_button_aligment_responsive ? $this->get_button_alignment( 'phone' ) : '';

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		// Button Alignment.
		$button_alignments = array();
		if ( ! empty( $button_alignment ) ) {
			array_push( $button_alignments, sprintf( 'et_pb_button_alignment_%1$s', esc_attr( $button_alignment ) ) );
		}

		if ( ! empty( $button_alignment_tablet ) ) {
			array_push( $button_alignments, sprintf( 'et_pb_button_alignment_tablet_%1$s', esc_attr( $button_alignment_tablet ) ) );
		}

		if ( ! empty( $button_alignment_phone ) ) {
			array_push( $button_alignments, sprintf( 'et_pb_button_alignment_phone_%1$s', esc_attr( $button_alignment_phone ) ) );
		}

		$button_alignment_classes = join( ' ', $button_alignments );

		// Nothing to output if neither Button Text nor Button URL defined
		$button_url = trim( $button_url );

		if ( '' === $button_text && '' === $button_url ) {
			return '';
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		// Module classnames
		$this->remove_classname( 'et_pb_module' );

		// Render Button
		$button = $this->render_button(
			array(
				'button_id'           => $this->module_id( false ),
				'button_classname'    => explode( ' ', $this->module_classname( $render_slug ) ),
				'button_custom'       => $button_custom,
				'button_rel'          => $button_rel,
				'button_text'         => $button_text,
				'button_text_escaped' => true,
				'button_url'          => $button_url,
				'custom_icon'         => $custom_icon,
				'custom_icon_tablet'  => $custom_icon_tablet,
				'custom_icon_phone'   => $custom_icon_phone,
				'has_wrapper'         => false,
				'url_new_window'      => $url_new_window,
				'multi_view_data'     => $multi_view->render_attrs(
					array(
						'content'        => '{{button_text}}',
						'hover_selector' => '%%order_class%%.et_pb_button',
						'visibility'     => array(
							'button_text' => '__not_empty',
						),
					)
				),
			)
		);

		// Render module output
		$output = sprintf(
			'<div class="et_pb_button_module_wrapper %3$s_wrapper %2$s et_pb_module "%4$s>
				%1$s
			</div>',
			et_core_esc_previously( $button ),
			esc_attr( $button_alignment_classes ),
			esc_attr( ET_Builder_Element::get_module_order_class( $this->slug ) ),
			et_core_esc_previously( $data_background_layout )
		);

		$transition_style = $this->get_transition_style( array( 'all' ) );
		self::set_style(
			$render_slug,
			array(
				'selector'    => '%%order_class%%, %%order_class%%:after',
				'declaration' => esc_html( $transition_style ),
			)
		);

		// Tablet.
		$transition_style_tablet = $this->get_transition_style( array( 'all' ), 'tablet' );
		if ( $transition_style_tablet !== $transition_style ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%%, %%order_class%%:after',
					'declaration' => esc_html( $transition_style_tablet ),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		// Phone.
		$transition_style_phone = $this->get_transition_style( array( 'all' ), 'phone' );
		if ( $transition_style_phone !== $transition_style || $transition_style_phone !== $transition_style_tablet ) {
			$el_style = array(
				'selector'    => '%%order_class%%, %%order_class%%:after',
				'declaration' => esc_html( $transition_style_phone ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			);
			self::set_style( $render_slug, $el_style );
		}

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

		$fields_need_escape = array(
			'title',
		);

		if ( $raw_value && 'content' === $context && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Button();
}

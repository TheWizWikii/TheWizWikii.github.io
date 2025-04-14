<?php

class ET_Builder_Module_Number_Counter extends ET_Builder_Module {
	function init() {
		$this->name              = esc_html__( 'Number Counter', 'et_builder' );
		$this->plural            = esc_html__( 'Number Counters', 'et_builder' );
		$this->slug              = 'et_pb_number_counter';
		$this->vb_support        = 'on';
		$this->custom_css_fields = array(
			'percent'              => array(
				'label'    => esc_html__( 'Percent', 'et_builder' ),
				'selector' => '.percent',
			),
			'number_counter_title' => array(
				'label'    => esc_html__( 'Number Counter Title', 'et_builder' ),
				'selector' => 'h3',
			),
		);

		$this->main_css_element = '%%order_class%%.et_pb_number_counter';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'title'  => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h3, {$this->main_css_element} h1.title, {$this->main_css_element} h2.title, {$this->main_css_element} h4.title, {$this->main_css_element} h5.title, {$this->main_css_element} h6.title",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'number' => array(
					'label'       => esc_html__( 'Number', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} .percent p",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'text_color'  => array(
						'old_option_ref' => 'counter_color',
						'default'        => et_builder_accent_color(),
					),
				),
			),
			'background'      => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'important' => array( 'custom_margin' ),
				),
			),
			'max_width'       => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_number_counter.et_pb_module',
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'center',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
				'css'                   => array(
					'main' => '%%order_class%% .title, %%order_class%% .percent',
				),
			),
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		if ( et_builder_has_limitation( 'force_use_global_important' ) ) {
			$this->advanced_fields['fonts']['number']['css']['important'] = 'all';
		}

		$this->help_videos = array(
			array(
				'id'   => 'qEE6z2t2oJ8',
				'name' => esc_html__( 'An introduction to the Number Counter module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'         => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the counter.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'number'        => array(
				'label'            => esc_html__( 'Number', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'value_type'       => 'float',
				'description'      => esc_html__( "Define a number for the counter. (Don't include the percentage sign, use the option below.)", 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => '0',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'percent_sign'  => array(
				'label'            => esc_html__( 'Percent Sign', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'counter_color' => array(
				'type'     => 'hidden',
				'default'  => '',
				'tab_slug' => 'advanced',
			),
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

		$multi_view    = et_pb_multi_view_options( $this );
		$number        = $this->props['number'];
		$percent_sign  = $this->props['percent_sign'];
		$title         = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['title_level'], 'h3' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'title',
				),
			)
		);
		$counter_color = $this->props['counter_color'];

		if ( et_builder_has_limitation( 'register_fittext_script' ) ) {
			wp_enqueue_script( 'fittext' );
		}

		$separator                 = strpos( $number, ',' ) ? ',' : '';
		$number                    = str_ireplace( array( '%', ',' ), '', $number );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( '' !== $title ) {
			$this->add_classname( 'et_pb_with_title' );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$multi_view_data_attr = $multi_view->render_attrs(
			array(
				'attrs'   => array(
					'data-number-value'     => '{{number}}',
					'data-number-separator' => '{{number}}',
					'data-percent-sign'     => '{{percent_sign}}',
				),
				'classes' => array(
					'et_pb_with_title' => array(
						'title' => '__not_empty',
					),
				),
			)
		);

		$output = sprintf(
			'<div%1$s class="%2$s" data-number-value="%3$s" data-number-separator="%7$s"%10$s>
				%9$s
				%8$s
				%12$s
				%13$s
				<div class="percent" %4$s%11$s><p><span class="percent-value"></span><span class="percent-sign">%5$s</span></p></div>
				%6$s
			</div>',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $number ),
			( '' !== $counter_color ? sprintf( ' style="color:%s"', esc_attr( $counter_color ) ) : '' ),
			( 'on' == $multi_view->get_value( 'percent_sign' ) ? '%' : '' ), // #5
			$title,
			esc_attr( $separator ),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ), // #10
			$multi_view_data_attr,
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
		$name     = isset( $args['name'] ) ? $args['name'] : '';
		$mode     = isset( $args['mode'] ) ? $args['mode'] : '';
		$attr_key = isset( $args['attr_key'] ) ? $args['attr_key'] : '';

		if ( 'number' === $name ) {
			if ( 'data-number-separator' === $attr_key ) {
				return strpos( $raw_value, ',' ) ? ',' : '';
			}

			return str_replace( '%', '', $raw_value );
		} elseif ( 'percent_sign' === $name ) {
			return 'on' === $raw_value ? '%' : '&nbsp;';
		}

		$fields_need_escape = array(
			'title',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Number_Counter();
}

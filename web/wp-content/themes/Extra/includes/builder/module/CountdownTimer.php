<?php

class ET_Builder_Module_Countdown_Timer extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Countdown Timer', 'et_builder' );
		$this->plural     = esc_html__( 'Countdown Timers', 'et_builder' );
		$this->slug       = 'et_pb_countdown_timer';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_countdown_timer';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => et_builder_i18n( 'Text' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header'    => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h4, {$this->main_css_element} h1.title, {$this->main_css_element} h2.title, {$this->main_css_element} h3.title, {$this->main_css_element} h5.title, {$this->main_css_element} h6.title",
						'important' => array( 'size', 'plugin_all' ),
					),
					'header_level' => array(
						'default' => 'h4',
					),
				),
				'numbers'   => array(
					'label'       => esc_html__( 'Numbers', 'et_builder' ),
					'css'         => array(
						'main'      => ".et_pb_column {$this->main_css_element} .section p.value, .et_pb_column {$this->main_css_element} .section.sep p",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'separator' => array(
					'label'           => esc_html__( 'Separator', 'et_builder' ),
					'css'             => array(
						'main'      => ".et_pb_column {$this->main_css_element} .et_pb_countdown_timer_container .section.sep p",
						'important' => 'all',
					),
					'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'hide_text_align' => true,
				),
				'label'     => array(
					'label'       => esc_html__( 'Label', 'et_builder' ),
					'css'         => array(
						'main'      => ".et_pb_column {$this->main_css_element} .section p.label",
						'important' => array(
							'size',
							'line-height',
						),
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
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
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'             => '%%order_class%% .et_pb_countdown_timer_container, %%order_class%% .title',
					'text_orientation' => '%%order_class%% .et_pb_countdown_timer_container, %%order_class%% .title',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'center',
					),
					'background_layout' => array(
						'default' => 'dark',
						'hover'   => 'tabs',
					),
				),
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'container'     => array(
				'label'    => esc_html__( 'Container', 'et_builder' ),
				'selector' => '.et_pb_countdown_timer_container',
			),
			'title'         => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => '.title',
			),
			'timer_section' => array(
				'label'    => esc_html__( 'Timer Section', 'et_builder' ),
				'selector' => '.section',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'irIXKlOw6JA',
				'name' => esc_html__( 'An introduction to the Countdown Timer module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'     => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This is the title displayed for the countdown timer.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'date_time' => array(
				'label'           => esc_html__( 'Date', 'et_builder' ),
				'type'            => 'date_picker',
				'option_category' => 'basic_option',
				'description'     => et_get_safe_localization( sprintf( __( 'This is the date the countdown timer is counting down to. Your countdown timer is based on your timezone settings in your <a href="%1$s" target="_blank" title="WordPress General Settings">WordPress General Settings</a>', 'et_builder' ), esc_url( admin_url( 'options-general.php' ) ) ) ),
				'toggle_slug'     => 'main_content',
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
		$multi_view           = et_pb_multi_view_options( $this );
		$title                = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['header_level'], 'h4' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'title',
				),
			)
		);
		$date_time            = $this->props['date_time'];
		$use_background_color = $this->props['use_background_color'];
		$end_date             = gmdate( 'M d, Y H:i:s', strtotime( $date_time ) );
		$gmt_offset           = strval( get_option( 'gmt_offset' ) );
		$gmt_divider          = '-' === substr( $gmt_offset, 0, 1 ) ? '-' : '+';
		$gmt_offset_hour      = str_pad( abs( intval( $gmt_offset ) ), 2, '0', STR_PAD_LEFT );
		$gmt_offset_minute    = str_pad( ( ( abs( $gmt_offset ) * 100 ) % 100 ) * ( 60 / 100 ), 2, '0', STR_PAD_LEFT );
		$gmt                  = "GMT{$gmt_divider}{$gmt_offset_hour}{$gmt_offset_minute}";

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Module classnames
		if ( 'on' !== $use_background_color ) {
			$this->add_classname( 'et_pb_no_bg' );
		}

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		$output = sprintf(
			'<div%1$s class="%2$s"%3$s data-end-timestamp="%4$s"%16$s>
				%15$s
				%14$s
				%17$s
				%18$s
				<div class="et_pb_countdown_timer_container clearfix">
					%5$s
					<div class="days section values" data-short="%13$s" data-full="%6$s">
						<p class="value"></p>
						<p class="label">%6$s</p>
					</div><div class="sep section">
						<p>:</p>
					</div><div class="hours section values" data-short="%8$s" data-full="%7$s">
						<p class="value"></p>
						<p class="label">%7$s</p>
					</div><div class="sep section">
						<p>:</p>
					</div><div class="minutes section values" data-short="%10$s" data-full="%9$s">
						<p class="value"></p>
						<p class="label">%9$s</p>
					</div><div class="sep section">
						<p>:</p>
					</div><div class="seconds section values" data-short="%12$s" data-full="%11$s">
						<p class="value"></p>
						<p class="label">%11$s</p>
					</div>
				</div>
			</div>',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			'',
			esc_attr( strtotime( "{$end_date} {$gmt}" ) ),
			et_core_esc_previously( $title ), // #5
			esc_html__( 'Day(s)', 'et_builder' ),
			esc_html__( 'Hour(s)', 'et_builder' ),
			esc_attr__( 'Hrs', 'et_builder' ),
			esc_html__( 'Minute(s)', 'et_builder' ),
			esc_attr__( 'Min', 'et_builder' ), // #10
			esc_html__( 'Second(s)', 'et_builder' ),
			esc_attr__( 'Sec', 'et_builder' ),
			esc_attr__( 'Day', 'et_builder' ),
			$video_background,
			$parallax_image_background, // #15
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $this->background_pattern() ), // #17
			et_core_esc_previously( $this->background_mask() ) // #18
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
	new ET_Builder_Module_Countdown_Timer();
}

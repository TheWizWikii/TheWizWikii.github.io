<?php

class ET_Builder_Module_Video extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Video', 'et_builder' );
		$this->plural     = esc_html__( 'Videos', 'et_builder' );
		$this->slug       = 'et_pb_video';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Video', 'et_builder' ),
					'overlay'      => et_builder_i18n( 'Overlay' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'play_icon' => esc_html__( 'Play Icon', 'et_builder' ),
					'overlay'   => et_builder_i18n( 'Overlay' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'video_icon' => array(
				'label'    => esc_html__( 'Video Icon', 'et_builder' ),
				'selector' => '.et_pb_video_play',
			),
		);

		$this->advanced_fields = array(
			'background'      => array(
				'options' => array(
					'background_color' => array(
						'depends_on'            => array(
							'custom_padding',
						),
						'depends_on_responsive' => array(
							'custom_padding',
						),
						'depends_show_if_not'   => array(
							'',
							'|||',
						),
						'is_toggleable'         => true,
					),
				),
			),
			'borders'         => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii' => '%%order_class%%, %%order_class%% iframe',
						),
					),
				),
			),
			'box_shadow'      => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
			),
			'margin_padding'  => array(
				'css'            => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
				'custom_padding' => array(
					'responsive_affects' => array(
						'background_color',
					),
				),
			),
			'fonts'           => false,
			'text'            => false,
			'button'          => false,
			'link_options'    => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '3jXN8CBz0TU',
				'name' => esc_html__( 'An introduction to the Video module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src'                     => array(
				'label'              => esc_html__( 'Video MP4 File Or Youtube URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects'   => array(
					'__video',
				),
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'src_webm'                => array(
				'label'              => esc_html__( 'Video WEBM File', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects'   => array(
					'__video',
				),
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'image_src'               => array(
				'label'                   => esc_html__( 'Overlay Image', 'et_builder' ),
				'type'                    => 'upload',
				'option_category'         => 'basic_option',
				'upload_button_text'      => et_builder_i18n( 'Upload an image' ),
				'choose_text'             => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'             => esc_attr__( 'Set As Image', 'et_builder' ),
				'additional_button'       => sprintf(
					'<input type="button" class="button et-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate Image From Video', 'et_builder' )
				),
				'additional_button_type'  => 'generate_image_url_from_video',
				'additional_button_attrs' => array(
					'video_source' => 'src',
				),
				'classes'                 => 'et_pb_video_overlay',
				'description'             => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'et_builder' ),
				'toggle_slug'             => 'overlay',
				'computed_affects'        => array(
					'__video_cover_src',
				),
				'dynamic_content'         => 'image',
				'mobile_options'          => true,
				'hover'                   => 'tabs',
			),
			'play_icon_color'         => array(
				'label'          => esc_html__( 'Play Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the play icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'play_icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'font_icon'      => array(
				'label'          => esc_html__( 'Icon', 'et_builder' ),
				'toggle_slug'    => 'play_icon',
				'type'           => 'select_icon',
				'class'          => array( 'et-pb-font-icon' ),
				'description'    => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options' => true,
				'hover'          => 'tabs',
				'sticky'         => true,
				'tab_slug'       => 'advanced',
			),
			'__video'                 => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video', 'get_video' ),
				'computed_depends_on' => array(
					'src',
					'src_webm',
				),
				'computed_minimum'    => array(
					'src',
					'src_webm',
				),
			),
			'__video_cover_src'       => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video', 'get_video_cover_src' ),
				'computed_depends_on' => array(
					'image_src',
				),
				'computed_minimum'    => array(
					'image_src',
				),
			),
			'use_icon_font_size'      => array(
				'label'            => esc_html__( 'Use Custom Icon Size', 'et_builder' ),
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
				'toggle_slug'      => 'play_icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'          => array(
				'label'            => esc_html__( 'Play Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'play_icon',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '96px',
				'default_unit'     => 'px',
				'default_on_front' => '',
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
			'thumbnail_overlay_color' => array(
				'label'            => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'description'      => esc_html__( 'Pick a color to use for the overlay that appears behind the play icon when hovering over the video.', 'et_builder' ),
				'type'             => 'color-alpha',
				'custom_color'     => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'default_on_front' => 'rgba(0,0,0,.6)',
				'mobile_options'   => true,
				'sticky'           => true,
			),

		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['play_icon_color'] = array( 'color' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play' );
		$fields['icon_font_size']  = array(
			'font-size'   => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
			'line-height' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
			'margin-top'  => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
			'margin-left' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
		);

		return $fields;
	}


	static function get_video( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'src'      => '',
			'src_webm' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['src'] ) && empty( $args['src_webm'] ) ) {
			return '';
		}

		$video_src = '';

		if ( false !== et_pb_check_oembed_provider( esc_url( $args['src'] ) ) ) {
			$video_src = et_builder_get_oembed( esc_url( $args['src'] ) );
		} elseif ( false !== et_pb_validate_youtube_url( esc_url( $args['src'] ) ) ) {
			$args['src'] = et_pb_normalize_youtube_url( esc_url( $args['src'] ) );
			$video_src   = et_builder_get_oembed( esc_url( $args['src'] ) );
		} else {
			$video_src = sprintf(
				'
				<video controls>
					%1$s
					%2$s
				</video>',
				( '' !== $args['src'] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args['src'] ) ) : '' ),
				( '' !== $args['src_webm'] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args['src_webm'] ) ) : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_src;
	}

	static function get_video_cover_src( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$post_id  = isset( $current_page['id'] ) ? $current_page['id'] : self::get_current_post_id();
		$defaults = array(
			'image_src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( isset( $args['image_src'] ) ) {
			$dynamic_value = et_builder_parse_dynamic_content( stripslashes( $args['image_src'] ) );
			if ( $dynamic_value->is_dynamic() && current_user_can( 'edit_post', $post_id ) ) {
				$args['image_src'] = $dynamic_value->resolve( $post_id );
			}
		}

		$image_output = '';

		if ( '' !== $args['image_src'] ) {
			$image_output = et_pb_set_video_oembed_thumbnail_resolution( $args['image_src'], 'high' );
		}

		return $image_output;
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
		$multi_view         = et_pb_multi_view_options( $this );
		$src                = $this->props['src'];
		$src_webm           = $this->props['src_webm'];
		$image_src          = $this->props['image_src'];
		$use_icon_font_size = $this->props['use_icon_font_size'];

		foreach ( $multi_view->get_modes() as $mode ) {
			$video_srcs[ $mode ] = self::get_video(
				array(
					'src'      => $multi_view->get_inherit_value( 'src', $mode ),
					'src_webm' => $multi_view->get_inherit_value( 'src_webm', $mode ),
				)
			);
		}

		$multi_view->set_custom_prop( 'video_srcs', $video_srcs );
		$video_src = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{video_srcs}}',
				'attrs'   => array(
					'class' => 'et_pb_video_box',
				),
			)
		);

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Play Icon color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'play_icon_color',
				'selector'                        => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Play Icon Styles.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family_and_content',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'font_icon',
				'important'      => true,
				'selector'       => '%%order_class%% .et_pb_video_overlay .et_pb_video_play:before',
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			// Icon Font Size.
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr.
					'processor'                       => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_overlay_icon_font_size',
					),
				)
			);
		}

		// Thumbnail Overlay Color.
		$this->generate_styles(
			array(
				'hover'                           => 'false',
				'base_attr_name'                  => 'thumbnail_overlay_color',
				'selector'                        => '%%order_class%% .et_pb_video_overlay_hover:hover',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$muti_view_video_overlay = $multi_view->render_element(
			array(
				'tag'        => 'div',
				'content'    => '<div class="et_pb_video_overlay_hover"><a href="#" class="et_pb_video_play"></a></div>',
				'attrs'      => array(
					'class' => 'et_pb_video_overlay',
				),
				'styles'     => array(
					'background-image' => 'url({{image_src}})',
				),
				'visibility' => array(
					'image_src' => '__not_empty',
				),
				'required'   => 'image_src',
			)
		);

		$output = sprintf(
			'<div%2$s class="%3$s">
				%6$s
				%5$s
				%7$s
				%8$s
				%1$s
				%4$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$muti_view_video_overlay,
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $this->background_pattern() ), // #7
			et_core_esc_previously( $this->background_mask() ) // #8
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
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';

		if ( $raw_value && 'image_src' === $name ) {
			$raw_value = self::get_video_cover_src(
				array(
					'image_src' => $raw_value,
				)
			);
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Video();
}

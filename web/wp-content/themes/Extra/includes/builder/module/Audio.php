<?php

class ET_Builder_Module_Audio extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Audio', 'et_builder' );
		$this->plural     = esc_html__( 'Audios', 'et_builder' );
		$this->slug       = 'et_pb_audio';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_audio_module';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'audio'        => esc_html__( 'Audio', 'et_builder' ),
					'image'        => et_builder_i18n( 'Image' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'image' => array(
						'title' => et_builder_i18n( 'Image' ),
					),
					'text'  => array(
						'title' => et_builder_i18n( 'Text' ),
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'title'   => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'caption' => array(
					'label' => esc_html__( 'Caption', 'et_builder' ),
					'css'   => array(
						'line_height'  => "{$this->main_css_element} p",
						'main'         => "{$this->main_css_element} p",
						'limited_main' => "{$this->main_css_element} p, {$this->main_css_element} p strong",
					),
				),
			),
			'background'      => array(
				'settings' => array(
					'color' => 'alpha',
				),
				'css'      => array(
					'important' => true,
				),
				'options'  => array(
					'background_color' => array(
						'default' => et_builder_accent_color(),
					),
				),
			),
			'borders'         => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_audio_cover_art',
							'border_styles' => '%%order_class%% .et_pb_audio_cover_art',
						),
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'      => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main'    => '%%order_class%% .et_pb_audio_cover_art',
						'overlay' => 'inset',
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
					'padding'   => '.et_pb_column %%order_class%% .et_pb_audio_module_content',
				),
			),
			'max_width'       => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_audio_module.et_pb_module',
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'css'                   => array(
					'text_orientation' => '%%order_class%% .et_pb_audio_module_content',
					'text_shadow'      => '%%order_class%% .et_pb_audio_module_content',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default_on_front' => 'center',
					),
					'background_layout' => array(
						'default_on_front' => 'dark',
						'hover'            => 'tabs',
					),
				),
			),
			'filters'         => array(
				'css'                  => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'           => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_audio_cover_art',
				),
			),
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->custom_css_fields = array(
			'audio_cover_art'       => array(
				'label'    => esc_html__( 'Audio Cover Art', 'et_builder' ),
				'selector' => '.et_pb_audio_cover_art',
			),
			'audio_content'         => array(
				'label'    => esc_html__( 'Audio Content', 'et_builder' ),
				'selector' => '.et_pb_audio_module_content',
			),
			'audio_title'           => array(
				'label'    => esc_html__( 'Audio Title', 'et_builder' ),
				'selector' => '.et_pb_audio_module_content h2',
			),
			'audio_meta'            => array(
				'label'    => esc_html__( 'Audio Meta', 'et_builder' ),
				'selector' => '.et_audio_module_meta',
			),
			'audio_buttons'         => array(
				'label'    => esc_html__( 'Player Buttons', 'et_builder' ),
				'selector' => "{$this->main_css_element} .mejs-button.mejs-playpause-button button:before,{$this->main_css_element} .mejs-button.mejs-volume-button.mejs-mute button:before",
			),
			'audio_timer'           => array(
				'label'    => esc_html__( 'Player Timer', 'et_builder' ),
				'selector' => '.mejs-time.mejs-duration-container .mejs-duration',
			),
			'audio_sliders'         => array(
				'label'    => esc_html__( 'Player Sliders', 'et_builder' ),
				'selector' => "{$this->main_css_element} .et_audio_container .mejs-controls .mejs-time-rail .mejs-time-total,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-total",
			),
			'audio_sliders_current' => array(
				'label'    => esc_html__( 'Player Sliders Current', 'et_builder' ),
				'selector' => "{$this->main_css_element} .et_audio_container .mejs-controls .mejs-time-rail .mejs-time-current,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-time-rail .mejs-time-handle,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-handle",
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '3bg1qUaSZ5I',
				'name' => esc_html__( 'An introduction to the Audio Player module', 'et_builder' ),
			),
		);
	}

	public function get_transition_fields_css_props() {
		$title     = "{$this->main_css_element} .et_pb_module_header";
		$meta      = "{$this->main_css_element} .et_audio_module_meta";
		$container = "{$this->main_css_element} .et_audio_container";

		$fields                      = parent::get_transition_fields_css_props();
		$fields['background_layout'] = array(
			'color'            => implode(
				', ',
				array(
					$title,
					$meta,
					"{$container} .mejs-playpause-button button:before",
					"{$container} .mejs-volume-button button:before",
					"{$container} .mejs-container .mejs-controls .mejs-time span",
				)
			),
			'background-color' => implode(
				', ',
				array(
					$title,
					"{$container} .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-total",
					"{$container} .mejs-controls .mejs-time-rail .mejs-time-total",
					"{$container} .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current",
					"{$container} .mejs-controls .mejs-time-rail .mejs-time-current",
					"{$container} .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-handle",
				)
			),

		);
		$fields['text_shadow_style'] = array(
			'text-shadow' => implode(
				', ',
				array(
					$title,
					$meta,
					"{$this->main_css_element} .et_audio_container .mejs-container .mejs-controls .mejs-time span",
				)
			),
		);

		return $fields;
	}

	function get_fields() {
		$fields = array(
			'audio'       => array(
				'label'              => esc_html__( 'Audio File', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'audio',
				'upload_button_text' => esc_attr__( 'Upload an audio file', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Audio file', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Audio for the module', 'et_builder' ),
				'description'        => esc_html__( 'Define the audio file for use in the module. To remove an audio file from the module, simply delete the URL from the settings field.', 'et_builder' ),
				'toggle_slug'        => 'audio',
				'computed_affects'   => array(
					'__audio',
				),
			),
			'title'       => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'artist_name' => array(
				'label'           => esc_html__( 'Artist', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define an artist name.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'album_name'  => array(
				'label'           => esc_html__( 'Album', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define an album name.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'image_url'   => array(
				'label'              => et_builder_i18n( 'Image' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'computed_affects'   => array(
					'__audio',
				),
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'__audio'     => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Audio', 'get_audio' ),
				'computed_depends_on' => array(
					'audio',
				),
				'computed_minimum'    => array(
					'audio',
				),
			),
		);

		return $fields;
	}

	static function get_audio( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'audio' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class' );

		return do_shortcode( sprintf( '[audio src="%s" /]', $args['audio'] ) );
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
		global $wp_version;

		$multi_view  = et_pb_multi_view_options( $this );
		$audio       = $this->props['audio'];
		$title       = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['title_level'], 'h2' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_module_header',
				),
			)
		);
		$artist_name = $this->_esc_attr( 'artist_name' );
		$album_name  = $this->_esc_attr( 'album_name' );
		$image_url   = $multi_view->render_element(
			array(
				'tag'      => 'div',
				'styles'   => array(
					'background-image' => 'url({{image_url}})',
				),
				'classes'  => array(
					'et_pb_audio_cover_art' => array(
						'image_url' => '__not_empty',
					),
				),
				'required' => 'image_url',
			)
		);

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$meta  = '';
		$metas = array();

		$artist_name = $multi_view->render_element(
			array(
				'tag'     => 'strong',
				'content' => '{{artist_name}}',
			)
		);

		if ( $artist_name ) {
			$metas[] = sprintf(
				et_get_safe_localization( _x( 'by %1$s', 'Audio Module meta information', 'et_builder' ) ),
				et_core_esc_previously( $artist_name )
			);
		}

		$album_name = $multi_view->render_element(
			array(
				'content' => '{{album_name}}',
			)
		);

		if ( $album_name ) {
			$metas[] = $album_name;
		}

		if ( $metas ) {
			$meta = sprintf( '<p class="et_audio_module_meta">%1$s</p>', implode( ' | ', $metas ) );
		}

		$parallax_image_background = $this->get_parallax_image_background();

		// some themes do not include these styles/scripts so we need to enqueue them in this module
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'et-builder-mediaelement' );

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class' );

		$video_background = $this->video_background();

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_audio_module',
				'clearfix',
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props, false, true );
		$this->add_classname( $background_layout_class_names );

		if ( '' === $image_url ) {
			$this->add_classname( 'et_pb_audio_no_image' );
		}

		// Remove automatically added module (backward compat)
		$this->remove_classname( $render_slug );

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

		// WP 4.8 or lower compatibility
		if ( version_compare( $wp_version, '4.9' ) === -1 ) {
			$this->add_classname( 'et_pb_audio_legacy' );
		}

		$muti_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_audio_no_image' => array(
						'image_url' => '__empty',
					),
				),
			)
		);

		$output = sprintf(
			'<div%6$s class="%4$s"%9$s%10$s>
				%8$s
				%7$s
				%11$s
				%12$s
				%5$s
				<div class="et_pb_audio_module_content et_audio_container">
					%1$s
					%2$s
					%3$s
				</div>
			</div>',
			$title, // #1
			et_core_esc_previously( $meta ), // #2
			self::get_audio(
				array(
					'audio' => $audio,
				)
			), // #3
			$this->module_classname( $render_slug ), // #4
			et_core_esc_previously( $image_url ), // #5
			$this->module_id(), // #6
			$video_background, // #7
			$parallax_image_background, // #8
			et_core_esc_previously( $data_background_layout ), // #9
			et_core_esc_previously( $muti_view_data_attr ), // #10
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
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$mode = isset( $args['mode'] ) ? $args['mode'] : '';

		$fields_need_escape = array(
			'title',
			'artist_name',
			'album_name',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Audio();
}

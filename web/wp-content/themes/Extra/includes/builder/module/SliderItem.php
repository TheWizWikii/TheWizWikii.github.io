<?php

require_once 'helpers/Slider.php';

class ET_Builder_Module_Slider_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = et_builder_i18n( 'Slide' );
		$this->plural                      = esc_html__( 'Slides', 'et_builder' );
		$this->slug                        = 'et_pb_slide';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'heading';
		$this->advanced_setting_title_text = esc_html__( 'New Slide', 'et_builder' );
		$this->settings_text               = esc_html__( 'Slide Settings', 'et_builder' );
		$this->main_css_element            = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'image_video'  => esc_html__( 'Image & Video', 'et_builder' ),
					'player_pause' => esc_html__( 'Player Pause', 'et_builder' ),
					'admin_label'  => array(
						'title'    => et_builder_i18n( 'Admin Label' ),
						'priority' => 99,
					),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'overlay'    => et_builder_i18n( 'Overlay' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'image'      => array(
						'title' => et_builder_i18n( 'Image' ),
					),
					'text'       => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'         => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title",
						'limited_main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title, .et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title a",
						'important'    => 'all',
					),
					'line_height'  => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'        => ".et_pb_slider.et_pb_module {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
						'important'   => 'all',
					),
					'line_height'    => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
			),
			'button'          => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_more_button.et_pb_button",
						'limited_main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_more_button.et_pb_button",
						'alignment'    => ".et_pb_slider {$this->main_css_element} .et_pb_slide_description .et_pb_button_wrapper",
					),
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main'      => '%%order_class%% .et_pb_button',
							'important' => true,
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'background'      => array(
				'css'     => array(
					'main' => '.et_pb_slider %%order_class%%',
				),
				'options' => array(
					'background_color' => array(
						'default'          => et_builder_accent_color(),
						'default_on_child' => true,
					),
				),
			),
			'borders'         => array(
				'default' => false,
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.et_pb_slide .et_pb_slide_image img',
							'border_styles' => '%%order_class%%.et_pb_slide .et_pb_slide_image img',
						),
					),
					'label_prefix'    => et_builder_i18n( 'Image' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'depends_show_if' => 'off',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'margin_padding'  => array(
				'use_margin' => false,
				'css'        => array(
					'padding'   => '.et_pb_slider %%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off %%order_class%% .et_pb_slide_description',
					'important' => array( 'custom_padding' ), // Important is needed to overwrite parent and column-specific padding specificity
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'             => implode(
						', ',
						array(
							'%%order_class%% .et_pb_slide_description .et_pb_slide_title',
							'%%order_class%% .et_pb_slide_description .et_pb_slide_title a',
							'%%order_class%% .et_pb_slide_description .et_pb_slide_content',
							'%%order_class%% .et_pb_slide_description .et_pb_slide_content .post-meta',
							'%%order_class%% .et_pb_slide_description .et_pb_slide_content .post-meta a',
							'%%order_class%% .et_pb_slide_description .et_pb_slide_content .et_pb_button',
						)
					),
					'text_orientation' => '.et_pb_slides %%order_class%%.et_pb_slide .et_pb_slide_description',
					'text_shadow'      => '.et_pb_slides %%order_class%%.et_pb_slide .et_pb_slide_description',
				),
				'options'               => array(
					'background_layout' => array(
						'default'          => 'dark',
						'default_on_child' => true,
						'hover'            => 'tabs',
					),
				),
			),
			'box_shadow'      => array(
				'default' => false,
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => '%%order_class%%.et_pb_slide .et_pb_slide_image img',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
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
					'main' => array(
						'%%order_class%% .et_pb_slide_image',
						'%%order_class%% .et_pb_section_video_bg',
					),
				),
			),
			'max_width'       => array(
				'use_module_alignment' => false,
				'css'                  => array(
					'main' => '.et_pb_slider %%order_class%%.et_pb_slide > .et_pb_container',
				),
				'options'              => array(
					'width'     => array(
						'label' => esc_html__( 'Content Width', 'et_builder' ),
					),
					'max_width' => array(
						'label' => esc_html__( 'Content Max Width', 'et_builder' ),
					),
				),
			),
			'height'          => false,
			'scroll_effects'  => false,
			'position_fields' => false,
			'sticky'          => false,
		);

		$this->custom_css_fields = array(
			'slide_title'       => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_container'   => array(
				'label'    => esc_html__( 'Slide Description Container', 'et_builder' ),
				'selector' => '.et_pb_container',
			),
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_button'      => array(
				'label'                    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector'                 => '.et_pb_slide .et_pb_container a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_image'       => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '-YeoR2xSLOY',
				'name' => esc_html__( 'An introduction to the Slider module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'heading'              => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the title text for your slide.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_text'          => array(
				'label'           => et_builder_i18n( 'Button' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the text for the slide button', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_link'          => array(
				'label'            => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input a destination URL for the slide button.', 'et_builder' ),
				'toggle_slug'      => 'link_options',
				'default_on_front' => '#',
				'dynamic_content'  => 'url',
			),
			'url_new_window'       => array(
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
			'image'                => array(
				'label'              => et_builder_i18n( 'Image' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose a Slide Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Slide Image', 'et_builder' ),
				'affects'            => array(
					'image_alt',
				),
				'description'        => esc_html__( 'If defined, this slide image will appear to the left of your slide text. Upload an image, or leave blank for a text-only slide.', 'et_builder' ),
				'toggle_slug'        => 'image_video',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'use_bg_overlay'       => array(
				'label'            => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
				),
				'affects'          => array(
					'bg_overlay_color',
				),
				'default_on_front' => '',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'description'      => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
			),
			'bg_overlay_color'     => array(
				'label'           => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'use_text_overlay'     => array(
				'label'            => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
				),
				'default_on_front' => '',
				'affects'          => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'description'      => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
			),
			'text_overlay_color'   => array(
				'label'           => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'alignment'            => array(
				'label'            => esc_html__( 'Image Alignment', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'center' => et_builder_i18n( 'Center' ),
					'bottom' => et_builder_i18n( 'Bottom' ),
				),
				'default_on_front' => 'center',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'image',
				'description'      => esc_html__( 'This setting determines the vertical alignment of your slide image. Your image can either be vertically centered, or aligned to the bottom of your slide.', 'et_builder' ),
			),
			'video_url'            => array(
				'label'              => esc_html__( 'Video', 'et_builder' ),
				'type'               => 'upload',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'option_category'    => 'basic_option',
				'description'        => esc_html__( 'If defined, this video will appear to the left of your slide text. Enter youtube or vimeo page url, or leave blank for a text-only slide.', 'et_builder' ),
				'toggle_slug'        => 'image_video',
				'computed_affects'   => array(
					'__video_embed',
				),
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'image_alt'            => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'image',
				),
				'description'     => esc_html__( 'If you have a slide image defined, input your HTML ALT text for the image here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'allow_player_pause'   => array(
				'label'            => esc_html__( 'Pause Video When Another Video Plays', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => '',
				'toggle_slug'      => 'player_pause',
				'description'      => esc_html__( 'Allow video to be paused by other players when they begin playing', 'et_builder' ),
			),
			'content'              => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your main slide text content here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'arrows_custom_color'  => array(
				'label'          => esc_html__( 'Arrow Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the slider arrows that are used to navigate through each slide.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'dot_nav_custom_color' => array(
				'label'          => esc_html__( 'Dot Navigation Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the dot navigation that appears at the bottom of the slider to designate which slide is active.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'admin_title'          => array(
				'label'       => et_builder_i18n( 'Admin Label' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the slide in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'text_border_radius'   => array(
				'label'            => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'description'      => esc_html__( 'Increasing the border radius will increase the roundness of the overlay corners. Setting this value to 0 will result in squared corners.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '3',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'__video_embed'        => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Slider_Item', 'get_video_embed' ),
				'computed_depends_on' => array(
					'video_url',
				),
				'computed_minimum'    => array(
					'video_url',
				),
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields                      = parent::get_transition_fields_css_props();
		$fields['background_layout'] = array(
			'background-color' => '%%order_class%% .et_pb_slide_overlay_container, %%order_class%% .et_pb_text_overlay_wrapper',
			'color'            => self::$_->array_get( $this->advanced_fields, 'text.css.main', '%%order_class%%' ),
		);

		$fields['bg_overlay_color']   = array( 'background-color' => '%%order_class%%.et_pb_slide .et_pb_slide_overlay_container' );
		$fields['text_overlay_color'] = array( 'background-color' => '%%order_class%%.et_pb_slide .et_pb_text_overlay_wrapper' );
		$fields['text_border_radius'] = array( 'border-radius' => '%%order_class%%.et_pb_slide .et_pb_text_overlay_wrapper' );

		$fields['dot_nav_custom_color'] = array( 'background-color' => et_pb_slider_options()->get_dots_selector() );
		$fields['arrows_custom_color']  = array( 'all' => et_pb_slider_options()->get_arrows_selector() );

		return $fields;
	}

	static function get_video_embed( $args = array(), $conditonal_args = array(), $current_page = array() ) {
		global $wp_embed;

		$video_url = esc_url( $args['video_url'] );

		// Bail early if video URL is empty.
		if ( empty( $video_url ) ) {
			return '';
		}

		$autoembed      = $wp_embed->autoembed( $video_url );
		$is_local_video = has_shortcode( $autoembed, 'video' );
		$video_embed    = '';

		if ( $is_local_video ) {
			$video_embed = wp_video_shortcode( array( 'src' => $video_url ) );
		} else {
			$video_embed = et_builder_get_oembed( $video_url );

			$video_embed = preg_replace( '/<embed /', '<embed wmode="transparent" ', $video_embed );

			$video_embed = preg_replace( '/<\/object>/', '<param name="wmode" value="transparent" /></object>', $video_embed );
		}

		return $video_embed;
	}

	function maybe_inherit_values() {
		// Inheriting slider attribute
		global $et_pb_slider;

		// Helper, makes things neater.
		$sticky = et_pb_sticky_options();

		// Check if current slide item version is made before Divi v3.2 (UI Improvement release). v3.2 changed default
		// background color for slide item for usability and inheritance mechanism requires custom treatment on FE and VB
		$is_prior_v32 = version_compare( self::$_->array_get( $this->props, '_builder_version', '3.0.47' ), '3.2', '<' );

		// Attribute inheritance should be done on front-end / published page only.
		// Don't run attribute inheritance in VB and Backend to avoid attribute inheritance accidentally being saved on VB / BB
		if ( ! empty( $et_pb_slider ) && ! is_admin() && ! et_fb_is_enabled() ) {
			$view_modes = array( '', '__hover', '_phone', '_tablet' );
			foreach ( $et_pb_slider as $slider_attr => $slider_attr_value ) {
				$maybe_next_slider_attr = false;
				foreach ( $view_modes as $mode ) {
					$current_view_bc_enable_attr = 'background_enable_color' . $mode;
					if ( $slider_attr === $current_view_bc_enable_attr ) {
						// Do not inherit the background color off from the parent slider
						$this->props[ $current_view_bc_enable_attr ] = isset( $this->props[ $current_view_bc_enable_attr ] ) && 'off' === $this->props[ $current_view_bc_enable_attr ] ? 'off' : 'on';
						$maybe_next_slider_attr                      = true;
					}
				}
				if ( $maybe_next_slider_attr ) {
					continue;
				}

				// Get default value
				$default = isset( $this->fields_unprocessed[ $slider_attr ]['default'] ) ? $this->fields_unprocessed[ $slider_attr ]['default'] : '';

				// Slide item isn't empty nor default
				if ( ! in_array( self::$_->array_get( $this->props, $slider_attr, '' ), array( '', $default ) ) ) {
					continue;
				}

				// Slider value is equal to empty or slide item's default
				if ( in_array( $slider_attr_value, array( '', $default ) ) ) {
					continue;
				}

				// Overwrite slider item's empty / default value
				$this->props[ $slider_attr ] = $slider_attr_value;
			}
		}

		// In VB, inheritance is done in VB side. However in migrating changing default that is affected by inheritance, the value
		// needs to be modified before being set to avoid sudden color change when _builder_version is bumped when settings modal
		// is opened. This making prior saved value changed but it is the safest option considering old Divi doesn't trim background_color
		if ( ! empty( $et_pb_slider ) && ( is_admin() || et_core_is_fb_enabled() ) && $is_prior_v32 ) {
			$slider_background_color           = self::$_->array_get( $et_pb_slider, 'background_color', '' );
			$is_slide_background_color_empty   = in_array( $this->props['background_color'], array( '', '#ffffff', et_builder_accent_color() ) );
			$is_slider_background_color_filled = '' !== $slider_background_color;

			if ( $is_slide_background_color_empty && $is_slider_background_color_filled ) {
				$this->props['background_color'] = '';
			}
		}

		// For background, text overlay, arrow, and dot colors, we have to consider about the
		// responsive settings status to inherit the value. If it's disabled on slider item, we
		// have to use the value from slider instead.
		if ( ! empty( $et_pb_slider ) ) {
			// Background Overlay Color.
			$is_bg_overlay_color_slider_responsive = et_pb_responsive_options()->is_responsive_enabled( $et_pb_slider, 'bg_overlay_color_slider' );
			$is_bg_overlay_color_responsive        = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'bg_overlay_color' );

			if ( ! $is_bg_overlay_color_responsive && $is_bg_overlay_color_slider_responsive ) {
				$this->props['bg_overlay_color_tablet']      = ! empty( $et_pb_slider['bg_overlay_color_tablet'] ) ? $et_pb_slider['bg_overlay_color_tablet'] : $this->props['bg_overlay_color_tablet'];
				$this->props['bg_overlay_color_phone']       = ! empty( $et_pb_slider['bg_overlay_color_phone'] ) ? $et_pb_slider['bg_overlay_color_phone'] : $this->props['bg_overlay_color_phone'];
				$this->props['bg_overlay_color_last_edited'] = ! empty( $et_pb_slider['bg_overlay_color_slider_last_edited'] ) ? $et_pb_slider['bg_overlay_color_slider_last_edited'] : $this->props['bg_overlay_color_last_edited'];
			}

			// Inherit value from parent if current sticky value is empty.
			$slide_bg_overlay_color__sticky = $sticky->get_value( 'bg_overlay_color', $this->props, '' );

			if ( '' === $slide_bg_overlay_color__sticky && ! empty( $et_pb_slider['bg_overlay_color__sticky'] ) ) {
				$this->props['bg_overlay_color__sticky_enabled'] = 'on';
				$this->props['bg_overlay_color__sticky']         = $et_pb_slider['bg_overlay_color__sticky'];
			}

			// Text Overlay Color.
			$is_text_overlay_color_slider_responsive = et_pb_responsive_options()->is_responsive_enabled( $et_pb_slider, 'text_overlay_color_slider' );
			$is_text_overlay_color_responsive        = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'text_overlay_color' );

			if ( ! $is_text_overlay_color_responsive && $is_text_overlay_color_slider_responsive ) {
				$this->props['text_overlay_color_tablet']      = ! empty( $et_pb_slider['text_overlay_color_tablet'] ) ? $et_pb_slider['text_overlay_color_tablet'] : $this->props['text_overlay_color_tablet'];
				$this->props['text_overlay_color_phone']       = ! empty( $et_pb_slider['text_overlay_color_phone'] ) ? $et_pb_slider['text_overlay_color_phone'] : $this->props['text_overlay_color_phone'];
				$this->props['text_overlay_color_last_edited'] = ! empty( $et_pb_slider['text_overlay_color_slider_last_edited'] ) ? $et_pb_slider['text_overlay_color_slider_last_edited'] : $this->props['text_overlay_color_last_edited'];
			}

			// Inherit value from parent if current sticky value is empty.
			$slide_text_overlay_color__sticky = $sticky->get_value( 'text_overlay_color', $this->props, '' );

			if ( '' === $slide_text_overlay_color__sticky && ! empty( $et_pb_slider['text_overlay_color__sticky'] ) ) {
				$this->props['text_overlay_color__sticky_enabled'] = 'on';
				$this->props['text_overlay_color__sticky']         = $et_pb_slider['text_overlay_color__sticky'];
			}

			// Text Border Radius.
			$is_text_border_radius_slider_responsive = et_pb_responsive_options()->is_responsive_enabled( $et_pb_slider, 'text_border_radius_slider' );
			$is_text_border_radius_responsive        = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'text_border_radius' );

			if ( ! $is_text_border_radius_responsive && $is_text_border_radius_slider_responsive ) {
				$this->props['text_border_radius_tablet']      = ! empty( $et_pb_slider['text_border_radius_tablet'] ) ? $et_pb_slider['text_border_radius_tablet'] : $this->props['text_border_radius_tablet'];
				$this->props['text_border_radius_phone']       = ! empty( $et_pb_slider['text_border_radius_phone'] ) ? $et_pb_slider['text_border_radius_phone'] : $this->props['text_border_radius_phone'];
				$this->props['text_border_radius_last_edited'] = ! empty( $et_pb_slider['text_border_radius_slider_last_edited'] ) ? $et_pb_slider['text_border_radius_slider_last_edited'] : $this->props['text_border_radius_last_edited'];
			}

			// Inherit value from parent if current sticky value is empty.
			$slide_text_border_radius__sticky = $sticky->get_value( 'text_border_radius', $this->props, '' );

			if ( '' === $slide_text_border_radius__sticky && ! empty( $et_pb_slider['text_border_radius__sticky'] ) ) {
				$this->props['text_border_radius__sticky_enabled'] = 'on';
				$this->props['text_border_radius__sticky']         = $et_pb_slider['text_border_radius__sticky'];
			}

			// Arrow Custom Color.
			$is_arrows_custom_color_slider_responsive = et_pb_responsive_options()->is_responsive_enabled( $et_pb_slider, 'arrows_custom_color_slider' );
			$is_arrows_custom_color_responsive        = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'arrows_custom_color' );

			if ( ! $is_arrows_custom_color_responsive && $is_arrows_custom_color_slider_responsive ) {
				$this->props['arrows_custom_color_tablet']      = ! empty( $et_pb_slider['arrows_custom_color_tablet'] ) ? $et_pb_slider['arrows_custom_color_tablet'] : $this->props['arrows_custom_color_tablet'];
				$this->props['arrows_custom_color_phone']       = ! empty( $et_pb_slider['arrows_custom_color_phone'] ) ? $et_pb_slider['arrows_custom_color_phone'] : $this->props['arrows_custom_color_phone'];
				$this->props['arrows_custom_color_last_edited'] = ! empty( $et_pb_slider['arrows_custom_color_slider_last_edited'] ) ? $et_pb_slider['arrows_custom_color_slider_last_edited'] : $this->props['arrows_custom_color_last_edited'];
			}

			// Inherit value from parent if current sticky value is empty.
			$slide_arrows_custom_color__sticky = $sticky->get_value( 'arrows_custom_color', $this->props, '' );

			if ( '' === $slide_arrows_custom_color__sticky && ! empty( $et_pb_slider['arrows_custom_color__sticky'] ) ) {
				$this->props['arrows_custom_color__sticky_enabled'] = 'on';
				$this->props['arrows_custom_color__sticky']         = $et_pb_slider['arrows_custom_color__sticky'];
			}

			// Dot Navigation Color.
			$is_dot_nav_custom_color_slider_responsive = et_pb_responsive_options()->is_responsive_enabled( $et_pb_slider, 'dot_nav_custom_color_slider' );
			$is_dot_nav_custom_color_responsive        = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'dot_nav_custom_color' );

			if ( ! $is_dot_nav_custom_color_responsive && $is_dot_nav_custom_color_slider_responsive ) {
				$this->props['dot_nav_custom_color_tablet']      = ! empty( $et_pb_slider['dot_nav_custom_color_tablet'] ) ? $et_pb_slider['dot_nav_custom_color_tablet'] : $this->props['dot_nav_custom_color_tablet'];
				$this->props['dot_nav_custom_color_phone']       = ! empty( $et_pb_slider['dot_nav_custom_color_phone'] ) ? $et_pb_slider['dot_nav_custom_color_phone'] : $this->props['dot_nav_custom_color_phone'];
				$this->props['dot_nav_custom_color_last_edited'] = ! empty( $et_pb_slider['dot_nav_custom_color_slider_last_edited'] ) ? $et_pb_slider['dot_nav_custom_color_slider_last_edited'] : $this->props['dot_nav_custom_color_last_edited'];
			}

			// Inherit value from parent if current sticky value is empty.
			$slide_dot_nav_custom_color__sticky = $sticky->get_value( 'dot_nav_custom_color', $this->props, '' );

			if ( '' === $slide_dot_nav_custom_color__sticky && ! empty( $et_pb_slider['dot_nav_custom_color__sticky'] ) ) {
				$this->props['dot_nav_custom_color__sticky_enabled'] = 'on';
				$this->props['dot_nav_custom_color__sticky']         = $et_pb_slider['dot_nav_custom_color__sticky'];
			}

			// Module item has no sticky option hence sticky_transition which is needed to setup
			// transition needs to be inherited from parent (slider) module.
			if ( ! empty( $et_pb_slider['sticky_transition'] ) ) {
				$this->props['sticky_transition'] = $et_pb_slider['sticky_transition'];
			}
		}
	}

	/**
	 * Get slider item normal or global background color.
	 *
	 * @since 4.9.0
	 *
	 * @return string
	 */
	public function get_slider_item_background_color() {
		$background_color = $this->props['background_color'];

		if ( strpos( $background_color, 'gcid-' ) === 0 ) {
			$global_color_info = et_builder_get_global_color_info( $background_color );

			return esc_attr( $global_color_info['color'] );
		}

		return $background_color;
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
		$alignment  = $this->props['alignment'];
		// Allowing full html for backwards compatibility.
		$heading                   = $this->_esc_attr( 'heading', 'full' );
		$button_text               = $this->_esc_attr( 'button_text', 'limited' );
		$button_link               = $this->props['button_link'];
		$url_new_window            = $this->props['url_new_window'];
		$image                     = $this->props['image'];
		$image_alt                 = $this->props['image_alt'];
		$video_url                 = $this->props['video_url'];
		$button_custom             = $this->props['custom_button'];
		$button_rel                = $this->props['button_rel'];
		$use_bg_overlay            = $this->props['use_bg_overlay'];
		$use_text_overlay          = $this->props['use_text_overlay'];
		$header_level              = $this->props['header_level'];
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$pattern_background        = $this->background_pattern();
		$mask_background           = $this->background_mask();
		$background_color          = $this->get_slider_item_background_color();
		$custom_icon_values        = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon               = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet        = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone         = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		global $et_pb_slider, $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_custom_icon_tablet, $et_pb_slider_custom_icon_phone, $et_pb_slider_item_num, $et_pb_slider_button_rel;

		$et_pb_slider_item_num++;

		$hide_on_mobile_class = self::HIDE_ON_MOBILE;

		$is_text_overlay_applied = 'on' === $use_text_overlay;

		$custom_slide_icon        = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $et_pb_slider_custom_icon;
		$custom_slide_icon_tablet = 'on' === $button_custom && '' !== $custom_icon_tablet ? $custom_icon_tablet : $et_pb_slider_custom_icon_tablet;
		$custom_slide_icon_phone  = 'on' === $button_custom && '' !== $custom_icon_phone ? $custom_icon_phone : $et_pb_slider_custom_icon_phone;

		if ( '' !== $heading ) {
			if ( '#' !== $button_link ) {
				$heading = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $button_link ),
					et_core_esc_previously( $heading )
				);
			}

			$heading = sprintf(
				'<%1$s class="et_pb_slide_title">%2$s</%1$s>',
				et_pb_process_header_level( $header_level, 'h2' ),
				et_core_esc_previously( $heading )
			);
		}

		// Overwrite button rel with pricin tables' button_rel if needed
		if ( in_array( $button_rel, array( '', 'off|off|off|off|off' ) ) && '' !== $et_pb_slider_button_rel ) {
			$button_rel = $et_pb_slider_button_rel;
		}

		// render button
		$button_classname = array( 'et_pb_more_button' );

		if ( 'on' !== $et_pb_slider_show_mobile['show_cta_on_mobile'] ) {
			$button_classname[] = $hide_on_mobile_class;
		}

		$button = $this->render_button(
			array(
				'button_classname'    => $button_classname,
				'button_custom'       => '' !== $custom_slide_icon || '' !== $custom_slide_icon_tablet || '' !== $custom_slide_icon_phone ? 'on' : 'off',
				'button_rel'          => $button_rel,
				'button_text'         => $button_text,
				'button_text_escaped' => true,
				'button_url'          => $button_link,
				'url_new_window'      => $url_new_window,
				'custom_icon'         => $custom_slide_icon,
				'custom_icon_tablet'  => $custom_slide_icon_tablet,
				'custom_icon_phone'   => $custom_slide_icon_phone,
				'display_button'      => true,
				'multi_view_data'     => $multi_view->render_attrs(
					array(
						'content'    => '{{button_text}}',
						'visibility' => array(
							'button_text' => '__not_empty',
						),
					)
				),
			)
		);

		if ( 'on' === $use_bg_overlay ) {
			// Background Overlay Color.
			$this->generate_styles(
				array(
					'hover'            => false,
					'base_attr_name'   => 'bg_overlay_color',
					'attrs'            => $this->props,
					'selector'         => '%%order_class%%.et_pb_slide .et_pb_slide_overlay_container',
					'css_property'     => 'background-color',
					'render_slug'      => $render_slug,
					'type'             => 'color',

					// The selector points to module item, thus current module is definitely not sticky.
					'is_sticky_module' => false,
				)
			);
		}

		if ( ! empty( $background_color ) && 'off' !== $this->props['background_enable_color'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $background_color )
					),
				)
			);
		}

		if ( $is_text_overlay_applied ) {
			// Text Overlay Color.
			$this->generate_styles(
				array(
					'hover'            => false,
					'base_attr_name'   => 'text_overlay_color',
					'attrs'            => $this->props,
					'selector'         => '%%order_class%%.et_pb_slide .et_pb_text_overlay_wrapper',
					'css_property'     => 'background-color',
					'render_slug'      => $render_slug,
					'type'             => 'color',

					// The selector points to module item, thus current module is definitely not sticky.
					'is_sticky_module' => false,
				)
			);
		}

		// Text Border Radius.
		$this->generate_styles(
			array(
				'hover'            => false,
				'base_attr_name'   => 'text_border_radius',
				'attrs'            => $this->props,
				'selector'         => '%%order_class%%.et_pb_slide .et_pb_text_overlay_wrapper',
				'css_property'     => 'border-radius',
				'render_slug'      => $render_slug,
				'type'             => 'range',

				// The selector points to module item, thus current module is definitely not sticky.
				'is_sticky_module' => false,
			)
		);

		$image = '';

		if ( $multi_view->has_value( 'image' ) ) {
			$image_attrs = array(
				'src'    => '{{image}}',
				'alt'    => esc_attr( $image_alt ),
			);

			$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'image' );

			if ( ! empty( $image_attachment_class ) ) {
				$image_attrs['class'] = esc_attr( $image_attachment_class );
			}

			$image_html = $multi_view->render_element(
				array(
					'tag'      => 'img',
					'attrs'    => $image_attrs,
					'required' => 'image',
				)
			);

			$image = $multi_view->render_element(
				array(
					'tag'      => 'div',
					'content'  => $image_html,
					'attrs'    => array(
						'class' => 'et_pb_slide_image',
					),
					'required' => 'image',
				)
			);
		}

		if ( $multi_view->has_value( 'video_url' ) ) {
			$image = $multi_view->render_element(
				array(
					'tag'     => 'div',
					'content' => '{{video_url}}',
					'attrs'   => array(
						'class' => 'et_pb_slide_video',
					),
				)
			);
		}

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

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		// Module classnames
		if ( $multi_view->has_value( 'image' ) || $multi_view->has_value( 'video_url' ) ) {
			$this->add_classname( 'et_pb_slide_with_image' );
		}

		if ( $multi_view->has_value( 'video_url' ) ) {
			$this->add_classname( 'et_pb_slide_with_video' );
		}

		if ( 'bottom' !== $alignment ) {
			$this->add_classname( "et_pb_media_alignment_{$alignment}" );
		}

		if ( 'on' === $use_bg_overlay ) {
			$this->add_classname( 'et_pb_slider_with_overlay' );
		}

		if ( 'on' === $use_text_overlay ) {
			$this->add_classname( 'et_pb_slider_with_text_overlay' );
		}

		if ( 1 === $et_pb_slider_item_num ) {
			$this->add_classname( 'et-pb-active-slide' );
		}

		$parent_class = self::$_->array_get( $et_pb_slider, 'order_class', 'et_pb_slider' );
		$order_class  = self::get_module_order_class( $render_slug );
		$prefix       = sprintf( '.%1$s[data-active-slide="%2$s"]', $parent_class, $order_class );

		$is_slider_sticky = et_()->array_get( $et_pb_slider, 'is_sticky_module', false );

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'arrows_custom_color',
				'attrs'                           => $this->props,
				'selector'                        => et_pb_slider_options()->get_arrows_selector( $prefix ),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',

				// Selector is started by slider parent's DOM, hence sticky classname prefix depends
				// to whether the slider parent is sticky or not.
				'is_sticky_module'                => $is_slider_sticky,
			)
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'dot_nav_custom_color',
				'attrs'                           => $this->props,
				'selector'                        => et_pb_slider_options()->get_dots_selector( $prefix ),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',

				// Selector is started by slider parent's DOM, hence sticky classname prefix depends
				// to whether the slider parent is sticky or not.
				'is_sticky_module'                => $is_slider_sticky,
			)
		);

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				'et_pb_module',
			)
		);

		$heading = $multi_view->has_value( 'heading' ) ? '{{heading}}' : '';

		if ( $heading ) {
			if ( $button_link && '#' !== $button_link ) {
				$heading = $multi_view->render_element(
					array(
						'tag'     => 'a',
						'content' => $heading,
						'attrs'   => array(
							'href' => esc_url( $button_link ),
						),
					)
				);
			}

			$heading = $multi_view->render_element(
				array(
					'tag'     => et_pb_process_header_level( $header_level, 'h2' ),
					'content' => $heading,
					'attrs'   => array(
						'class' => 'et_pb_slide_title',
					),
				)
			);
		}

		$slide_content_class = array( 'et_pb_slide_content' );

		if ( 'on' !== $et_pb_slider_show_mobile['show_content_on_mobile'] ) {
			$slide_content_class[] = $hide_on_mobile_class;
		}

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => implode( ' ', $slide_content_class ),
				),
			)
		);

		$slide_content = sprintf(
			'%1$s%2$s',
			et_core_esc_previously( $heading ),
			et_core_esc_previously( $content )
		);

		// apply text overlay wrapper
		if ( $is_text_overlay_applied ) {
			$slide_content = sprintf(
				'<div class="et_pb_text_overlay_wrapper">
					%1$s
				</div>',
				$slide_content
			);
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$multi_view_classes = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_slide_with_image' => array(
						'image' => '__not_empty',
					),
					'et_pb_slide_with_video' => array(
						'video_url' => '__not_empty',
					),
				),
			)
		);

		$output = sprintf(
			'<div class="%4$s"%7$s%8$s%10$s data-slide-id="%11$s"%12$s>
				%6$s
				%9$s
				<div class="et_pb_container clearfix">
					<div class="et_pb_slider_container_inner">
						%3$s
						<div class="et_pb_slide_description">
							%1$s
							%2$s
						</div>
					</div>
				</div>
				%5$s
				%13$s
				%14$s
			</div>
			',
			$slide_content,
			$button,
			$image,
			$this->module_classname( $render_slug ),
			$video_background, // #5
			$parallax_image_background,
			'',
			'',
			'on' === $use_bg_overlay ? '<div class="et_pb_slide_overlay_container"></div>' : '',
			et_core_esc_previously( $data_background_layout ), // #10
			self::get_module_order_class( $render_slug ),
			$multi_view_classes,
			et_core_esc_previously( $pattern_background ), // #13
			et_core_esc_previously( $mask_background ) // #14
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

		if ( 'heading' === $name ) {
			$raw_value = $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'full' );
		} elseif ( 'button_text' === $name && 'content' === $context ) {
			$raw_value = $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'limited' );
		} elseif ( 'image' === $name && 'classes' === $context ) {
			$raw_value = $raw_value ? $raw_value : $multi_view->get_inherit_value( 'video_url', $mode );
		} elseif ( 'video_url' === $name ) {
			$raw_value = self::get_video_embed(
				array(
					'video_url' => esc_url( $raw_value ),
				)
			);
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Slider_Item();
}

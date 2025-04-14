<?php
/**
 * Global modules settings.
 *
 * @package Divi
 * @subpackage Builder
 */

/**
 * Global settings class.
 *
 * @todo Rename this class to `ET_Builder_Module_Settings` so that its name clearly indicates its purpose.
 */
class ET_Global_Settings {
	/**
	 * List of default settings.
	 *
	 * @var array
	 */
	private static $_settings = array();

	/**
	 * Whether reinit default setting values.
	 *
	 * @var bool
	 */
	private static $_reinit_values = false;

	/**
	 * Initialize the global settings.
	 */
	public static function init() {
		// The class can only be initialized once.
		if ( ! empty( self::$_settings ) && ! self::$_reinit_values ) {
			return;
		}

		// Reset _reinit_values property. It should only used once for every reinit() method call.
		if ( self::$_reinit_values ) {
			self::$_reinit_values = false;
		}

		self::set_values();
	}

	/**
	 * Allow global settings value to be reinitialized. Initially added a to make global
	 * settings modifieable during unit/integration testing which uses PHPUnit & wp-browser
	 */
	public static function reinit() {
		self::$_reinit_values = true;
	}

	/**
	 * Set default global setting value
	 */
	private static function set_values() {
		$hover = et_pb_hover_options();

		$font_defaults_h1 = array(
			'size'           => '30px',
			'letter_spacing' => '0px',
			'line_height'    => '1em',
		);

		$font_defaults_h2 = array(
			'size'           => '26px',
			'letter_spacing' => '0px',
			'line_height'    => '1em',
		);

		$font_defaults = array(
			'size'           => '14',
			'color'          => '#666666',
			'letter_spacing' => '0px',
			'line_height'    => '1.7em',
		);

		$background_gradient_defaults = array(
			'stops'            => '#2b87da 0%|#29c4a9 100%',
			'type'             => 'linear',
			'direction'        => '180deg',
			'direction_radial' => 'center',
			'repeat'           => 'off',
			'unit'             => '%',
			'overlays_image'   => 'off',
			'start'            => '#2b87da',
			'end'              => '#29c4a9',
			'start_position'   => '0%',
			'end_position'     => '100%',
		);

		$background_image_defaults = array(
			'size'     => 'cover',
			'position' => 'center',
			'repeat'   => 'no-repeat',
			'blend'    => 'normal',
		);

		$background_blend_mode_defaults = array(
			'background_blend_mode' => $background_image_defaults['blend'],
		);

		$filter_defaults = array(
			'filter_hue_rotate' => '0deg',
			'filter_saturate'   => '100%',
			'filter_brightness' => '100%',
			'filter_contrast'   => '100%',
			'filter_invert'     => '0%',
			'filter_sepia'      => '0%',
			'filter_opacity'    => '100%',
			'filter_blur'       => '0px',
		);

		$defaults = array(
			// Global: Buttons.
			'all_buttons_font_size'                        => '20',
			'all_buttons_border_width'                     => '2',
			'all_buttons_border_radius'                    => '3',
			'all_buttons_spacing'                          => '0',
			'all_buttons_font_style'                       => '',
			$hover->get_hover_field( 'all_buttons_border_radius' ) => '3',
			$hover->get_hover_field( 'all_buttons_spacing' ) => '0',
			// Global: Background Gradients.
			'all_background_gradient_repeat'               => $background_gradient_defaults['repeat'],
			'all_background_gradient_type'                 => $background_gradient_defaults['type'],
			'all_background_gradient_direction'            => $background_gradient_defaults['direction'],
			'all_background_gradient_direction_radial'     => $background_gradient_defaults['direction_radial'],
			'all_background_gradient_stops'                => $background_gradient_defaults['stops'],
			'all_background_gradient_unit'                 => $background_gradient_defaults['unit'],
			'all_background_gradient_overlays_image'       => $background_gradient_defaults['overlays_image'],
			// Deprecated.
			'all_background_gradient_start'                => $background_gradient_defaults['start'],
			// Deprecated.
			'all_background_gradient_end'                  => $background_gradient_defaults['end'],
			// Deprecated.
			'all_background_gradient_start_position'       => $background_gradient_defaults['start_position'],
			// Deprecated.
			'all_background_gradient_end_position'         => $background_gradient_defaults['end_position'],
			// Global: Filters.
			'all_filter_hue_rotate'                        => $filter_defaults['filter_hue_rotate'],
			'all_filter_saturate'                          => $filter_defaults['filter_saturate'],
			'all_filter_brightness'                        => $filter_defaults['filter_brightness'],
			'all_filter_contrast'                          => $filter_defaults['filter_contrast'],
			'all_filter_invert'                            => $filter_defaults['filter_invert'],
			'all_filter_sepia'                             => $filter_defaults['filter_sepia'],
			'all_filter_opacity'                           => $filter_defaults['filter_opacity'],
			'all_filter_blur'                              => $filter_defaults['filter_blur'],
			// Global: Mix Blend Mode.
			'all_mix_blend_mode'                           => 'normal',
			// Module: Accordion.
			'et_pb_accordion-toggle_font_size'             => '16',
			'et_pb_accordion-toggle_font_style'            => '',
			'et_pb_accordion-inactive_toggle_font_style'   => '',
			'et_pb_accordion-toggle_icon_size'             => '16',
			'et_pb_accordion-custom_padding'               => '20',
			'et_pb_accordion-toggle_line_height'           => '1em',
			'et_pb_accordion-toggle_letter_spacing'        => $font_defaults['letter_spacing'],
			'et_pb_accordion-body_font_size'               => $font_defaults['size'],
			'et_pb_accordion-body_line_height'             => $font_defaults['line_height'],
			'et_pb_accordion-body_letter_spacing'          => $font_defaults['letter_spacing'],
			// Module: Audio.
			'et_pb_audio-title_font_size'                  => '26',
			'et_pb_audio-title_letter_spacing'             => $font_defaults['letter_spacing'],
			'et_pb_audio-title_line_height'                => $font_defaults['line_height'],
			'et_pb_audio-title_font_style'                 => '',
			'et_pb_audio-caption_font_size'                => $font_defaults['size'],
			'et_pb_audio-caption_letter_spacing'           => $font_defaults['letter_spacing'],
			'et_pb_audio-caption_line_height'              => $font_defaults['line_height'],
			'et_pb_audio-caption_font_style'               => '',
			'et_pb_audio-title_text_color'                 => '#666666',
			'et_pb_audio-background_size'                  => $background_image_defaults['size'],
			'et_pb_audio-background_position'              => $background_image_defaults['position'],
			'et_pb_audio-background_repeat'                => $background_image_defaults['repeat'],
			'et_pb_audio-background_blend'                 => $background_image_defaults['blend'],
			// Module: Blog.
			'et_pb_blog-header_font_size'                  => '18',
			'et_pb_blog-header_font_style'                 => '',
			'et_pb_blog-meta_font_size'                    => '14',
			'et_pb_blog-meta_font_style'                   => '',
			'et_pb_blog-meta_line_height'                  => $font_defaults['line_height'],
			'et_pb_blog-meta_letter_spacing'               => $font_defaults['letter_spacing'],
			'et_pb_blog-header_color'                      => '#333333',
			'et_pb_blog-header_line_height'                => '1em',
			'et_pb_blog-header_letter_spacing'             => $font_defaults['letter_spacing'],
			'et_pb_blog-body_font_size'                    => $font_defaults['size'],
			'et_pb_blog-body_line_height'                  => $font_defaults['line_height'],
			'et_pb_blog-body_letter_spacing'               => $font_defaults['letter_spacing'],
			'et_pb_blog-pagination_font_size'              => $font_defaults['size'],
			'et_pb_blog-pagination_line_height'            => $font_defaults['line_height'],
			'et_pb_blog-pagination_letter_spacing'         => $font_defaults['letter_spacing'],
			'et_pb_blog_masonry-header_font_size'          => '26',
			'et_pb_blog_masonry-header_font_style'         => '',
			'et_pb_blog_masonry-meta_font_size'            => '14',
			'et_pb_blog_masonry-meta_font_style'           => '',
			'et_pb_blog-read_more_font_size'               => '14px',
			'et_pb_blog-read_more_line_height'             => $font_defaults['line_height'],
			// Module: Blurb.
			'et_pb_blurb-header_font_size'                 => '18',
			'et_pb_blurb-header_color'                     => '#333333',
			'et_pb_blurb-header_letter_spacing'            => $font_defaults['letter_spacing'],
			'et_pb_blurb-header_line_height'               => '1em',
			'et_pb_blurb-body_font_size'                   => $font_defaults['size'],
			'et_pb_blurb-body_color'                       => '#666666',
			'et_pb_blurb-body_letter_spacing'              => $font_defaults['letter_spacing'],
			'et_pb_blurb-body_line_height'                 => $font_defaults['line_height'],
			'et_pb_blurb-text_orientation'                 => 'left',
			'et_pb_blurb-background_size'                  => $background_image_defaults['size'],
			'et_pb_blurb-background_position'              => $background_image_defaults['position'],
			'et_pb_blurb-background_repeat'                => $background_image_defaults['repeat'],
			'et_pb_blurb-background_blend'                 => $background_image_defaults['blend'],
			// Module: Circle Counter.
			'et_pb_circle_counter-title_font_size'         => '16',
			'et_pb_circle_counter-title_letter_spacing'    => $font_defaults['letter_spacing'],
			'et_pb_circle_counter-title_line_height'       => '1em',
			'et_pb_circle_counter-title_font_style'        => '',
			'et_pb_circle_counter-number_font_size'        => '46',
			'et_pb_circle_counter-number_font_style'       => '',
			'et_pb_circle_counter-title_color'             => '#333333',
			'et_pb_circle_counter-number_line_height'      => '225px',
			'et_pb_circle_counter-number_letter_spacing'   => $font_defaults['letter_spacing'],
			'et_pb_circle_counter-circle_color_alpha'      => '0.1',
			// Module: Comments.
			'et_pb_comments-header_font_size'              => $font_defaults_h1['size'],
			'et_pb_comments-header_line_height'            => $font_defaults_h1['line_height'],
			// Module: Contact Form.
			'et_pb_contact_form-title_font_size'           => '26',
			'et_pb_contact_form-title_font_style'          => '',
			'et_pb_contact_form-form_field_font_size'      => '14',
			'et_pb_contact_form-form_field_font_style'     => '',
			'et_pb_contact_form-captcha_font_size'         => '14',
			'et_pb_contact_form-captcha_font_style'        => '',
			'et_pb_contact_form-padding'                   => '16',
			'et_pb_contact_form-title_color'               => '#333333',
			'et_pb_contact_form-title_line_height'         => '1em',
			'et_pb_contact_form-title_letter_spacing'      => $font_defaults['letter_spacing'],
			'et_pb_contact_form-form_field_color'          => '#999999',
			'et_pb_contact_form-form_field_line_height'    => $font_defaults['line_height'],
			'et_pb_contact_form-form_field_letter_spacing' => $font_defaults['letter_spacing'],
			// Module: Countdown Timer.
			'et_pb_countdown_timer-header_font_size'       => '22',
			'et_pb_countdown_timer-header_font_style'      => '',
			'et_pb_countdown_timer-header_color'           => '#333333',
			'et_pb_countdown_timer-header_line_height'     => '1em',
			'et_pb_countdown_timer-header_letter_spacing'  => $font_defaults['letter_spacing'],
			'et_pb_countdown_timer-numbers_font_size'      => '54px',
			'et_pb_countdown_timer-numbers_line_height'    => '54px',
			'et_pb_countdown_timer-numbers_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_countdown_timer-separator_font_size'    => '54px',
			'et_pb_countdown_timer-separator_line_height'  => '54px',
			'et_pb_countdown_timer-separator_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_countdown_timer-label_line_height'      => '25px',
			'et_pb_countdown_timer-label_letter_spacing'   => $font_defaults['letter_spacing'],
			'et_pb_countdown_timer-label_font_size'        => $font_defaults['size'],
			'et_pb_countdown_timer-background_size'        => $background_image_defaults['size'],
			'et_pb_countdown_timer-background_position'    => $background_image_defaults['position'],
			'et_pb_countdown_timer-background_repeat'      => $background_image_defaults['repeat'],
			'et_pb_countdown_timer-background_blend'       => $background_image_defaults['blend'],
			// Module: Bar Counters Item.
			'et_pb_counter-background_size'                => $background_image_defaults['size'],
			'et_pb_counter-background_position'            => $background_image_defaults['position'],
			'et_pb_counter-background_repeat'              => $background_image_defaults['repeat'],
			'et_pb_counter-background_blend'               => $background_image_defaults['blend'],
			// Module: Bar Counters.
			'et_pb_counters-title_font_size'               => '12',
			'et_pb_counters-title_letter_spacing'          => $font_defaults['letter_spacing'],
			'et_pb_counters-title_line_height'             => $font_defaults['line_height'],
			'et_pb_counters-title_font_style'              => '',
			'et_pb_counters-percent_font_size'             => '12',
			'et_pb_counters-percent_letter_spacing'        => $font_defaults['letter_spacing'],
			'et_pb_counters-percent_line_height'           => $font_defaults['line_height'],
			'et_pb_counters-percent_font_style'            => '',
			'et_pb_counters-border_radius'                 => '0',
			'et_pb_counters-padding'                       => '0',
			'et_pb_counters-title_color'                   => '#999999',
			'et_pb_counters-percent_color'                 => '#ffffff',
			'et_pb_counters-background_size'               => $background_image_defaults['size'],
			'et_pb_counters-background_position'           => $background_image_defaults['position'],
			'et_pb_counters-background_repeat'             => $background_image_defaults['repeat'],
			'et_pb_counters-background_blend'              => $background_image_defaults['blend'],
			// Module: CTA.
			'et_pb_cta-header_font_size'                   => '26',
			'et_pb_cta-header_font_style'                  => '',
			'et_pb_cta-custom_padding'                     => '40',
			'et_pb_cta-header_text_color'                  => '#333333',
			'et_pb_cta-header_line_height'                 => '1em',
			'et_pb_cta-header_letter_spacing'              => $font_defaults['letter_spacing'],
			'et_pb_cta-body_font_size'                     => $font_defaults['size'],
			'et_pb_cta-body_line_height'                   => $font_defaults['line_height'],
			'et_pb_cta-body_letter_spacing'                => $font_defaults['letter_spacing'],
			'et_pb_cta-text_orientation'                   => 'center',
			'et_pb_cta-background_size'                    => $background_image_defaults['size'],
			'et_pb_cta-background_position'                => $background_image_defaults['position'],
			'et_pb_cta-background_repeat'                  => $background_image_defaults['repeat'],
			'et_pb_cta-background_blend'                   => $background_image_defaults['blend'],
			// Module: Divider.
			'et_pb_divider-divider_style'                  => 'solid',
			'et_pb_divider-divider_weight'                 => '1',
			'et_pb_divider-height'                         => '1',
			'et_pb_divider-divider_position'               => 'top',
			// Module: Filterable Portfolio.
			'et_pb_filterable_portfolio-hover_overlay_color' => 'rgba(255,255,255,0.9)',
			'et_pb_filterable_portfolio-title_font_size'   => '18',
			'et_pb_filterable_portfolio-title_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_filterable_portfolio-title_line_height' => $font_defaults['line_height'],
			'et_pb_filterable_portfolio-title_font_style'  => '',
			'et_pb_filterable_portfolio-title_color'       => '#333333',
			'et_pb_filterable_portfolio-caption_font_size' => '14',
			'et_pb_filterable_portfolio-caption_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_filterable_portfolio-caption_line_height' => $font_defaults['line_height'],
			'et_pb_filterable_portfolio-caption_font_style' => '',
			'et_pb_filterable_portfolio-filter_font_size'  => '14',
			'et_pb_filterable_portfolio-filter_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_filterable_portfolio-filter_line_height' => $font_defaults['line_height'],
			'et_pb_filterable_portfolio-filter_font_style' => '',
			'et_pb_filterable_portfolio-pagination_font_size' => '14',
			'et_pb_filterable_portfolio-pagination_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_filterable_portfolio-pagination_line_height' => $font_defaults['line_height'],
			'et_pb_filterable_portfolio-pagination_font_style' => '',
			'et_pb_filterable_portfolio-background_size'   => $background_image_defaults['size'],
			'et_pb_filterable_portfolio-background_position' => $background_image_defaults['position'],
			'et_pb_filterable_portfolio-background_repeat' => $background_image_defaults['repeat'],
			'et_pb_filterable_portfolio-background_blend'  => $background_image_defaults['blend'],
			'et_pb_filterable_portfolio-zoom_icon_color'   => '',
			// Module: Fullwidth Header.
			'et_pb_fullwidth_header-scroll_down_icon_size' => '50px',
			'et_pb_fullwidth_header-subhead_font_size'     => '18px',
			'et_pb_fullwidth_header-button_one_font_size'  => '20px',
			'et_pb_fullwidth_header-button_one_border_radius' => '3px',
			'et_pb_fullwidth_header-button_two_font_size'  => '20px',
			'et_pb_fullwidth_header-button_two_border_radius' => '3px',
			'et_pb_fullwidth_header-background_size'       => $background_image_defaults['size'],
			'et_pb_fullwidth_header-background_position'   => $background_image_defaults['position'],
			'et_pb_fullwidth_header-background_repeat'     => $background_image_defaults['repeat'],
			'et_pb_fullwidth_header-background_blend'      => $background_image_defaults['blend'],
			// Module: Fullwidth Menu.
			'et_pb_fullwidth_menu-background_size'         => $background_image_defaults['size'],
			'et_pb_fullwidth_menu-background_position'     => $background_image_defaults['position'],
			'et_pb_fullwidth_menu-background_repeat'       => $background_image_defaults['repeat'],
			'et_pb_fullwidth_menu-background_blend'        => $background_image_defaults['blend'],
			// Module: Fullwidth Portfolio.
			'et_pb_fullwidth_portfolio-background_size'    => $background_image_defaults['size'],
			'et_pb_fullwidth_portfolio-background_position' => $background_image_defaults['position'],
			'et_pb_fullwidth_portfolio-background_repeat'  => $background_image_defaults['repeat'],
			'et_pb_fullwidth_portfolio-background_blend'   => $background_image_defaults['blend'],
			'et_pb_fullwidth_portfolio-zoom_icon_color'    => '',
			// Module: Fullwidth Post Title.
			'et_pb_fullwidth_post_title-title_font_size'   => '26px',
			'et_pb_fullwidth_post_title-title_line_height' => '1em',
			'et_pb_fullwidth_post_title-title_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_fullwidth_post_title-meta_font_size'    => $font_defaults['size'],
			'et_pb_fullwidth_post_title-meta_line_height'  => '1em',
			'et_pb_fullwidth_post_title-meta_letter_spacing' => $font_defaults['letter_spacing'],
			// Module: Fullwidth Slider.
			'et_pb_fullwidth_slider-header_font_size'      => '46',
			'et_pb_fullwidth_slider-header_font_style'     => '',
			'et_pb_fullwidth_slider-body_font_size'        => '16',
			'et_pb_fullwidth_slider-body_font_style'       => '',
			'et_pb_fullwidth_slider-body_line_height'      => $font_defaults['line_height'],
			'et_pb_fullwidth_slider-body_letter_spacing'   => $font_defaults['letter_spacing'],
			'et_pb_fullwidth_slider-padding'               => '16',
			'et_pb_fullwidth_slider-header_color'          => '#ffffff',
			'et_pb_fullwidth_slider-header_line_height'    => '1em',
			'et_pb_fullwidth_slider-header_letter_spacing' => $font_defaults['letter_spacing'],
			'et_pb_fullwidth_slider-body_color'            => '#ffffff',
			'et_pb_fullwidth_slider-background_size'       => $background_image_defaults['size'],
			'et_pb_fullwidth_slider-background_position'   => $background_image_defaults['position'],
			'et_pb_fullwidth_slider-background_repeat'     => $background_image_defaults['repeat'],
			'et_pb_fullwidth_slider-background_blend'      => $background_image_defaults['blend'],
			// Module: Gallery.
			'et_pb_gallery-hover_overlay_color'            => 'rgba(255,255,255,0.9)',
			'et_pb_gallery-title_font_size'                => '16',
			'et_pb_gallery-title_color'                    => '#333333',
			'et_pb_gallery-title_letter_spacing'           => $font_defaults['letter_spacing'],
			'et_pb_gallery-title_line_height'              => '1em',
			'et_pb_gallery-title_font_style'               => '',
			'et_pb_gallery-caption_font_size'              => '14',
			'et_pb_gallery-caption_font_style'             => '',
			'et_pb_gallery-caption_color'                  => '#f3f3f3',
			'et_pb_gallery-caption_line_height'            => '18px',
			'et_pb_gallery-caption_letter_spacing'         => $font_defaults['letter_spacing'],
			'et_pb_gallery-pagination_font_size'           => '16px',
			'et_pb_gallery-pagination_line_height'         => '1em',
			'et_pb_gallery-zoom_icon_color'                => '',
			// Module: Image.
			'et_pb_image-animation'                        => 'left',
			// Module: Login.
			'et_pb_login-header_font_size'                 => '26',
			'et_pb_login-header_letter_spacing'            => $font_defaults['letter_spacing'],
			'et_pb_login-header_line_height'               => $font_defaults['line_height'],
			'et_pb_login-body_font_size'                   => $font_defaults['size'],
			'et_pb_login-body_letter_spacing'              => $font_defaults['letter_spacing'],
			'et_pb_login-body_line_height'                 => $font_defaults['line_height'],
			'et_pb_login-header_font_style'                => '',
			'et_pb_login-custom_padding'                   => '40',
			'et_pb_login-focus_border_color'               => '#ffffff',
			'et_pb_login-background_size'                  => $background_image_defaults['size'],
			'et_pb_login-background_position'              => $background_image_defaults['position'],
			'et_pb_login-background_repeat'                => $background_image_defaults['repeat'],
			'et_pb_login-background_blend'                 => $background_image_defaults['blend'],
			// Module: Menu.
			'et_pb_menu-background_size'                   => $background_image_defaults['size'],
			'et_pb_menu-background_position'               => $background_image_defaults['position'],
			'et_pb_menu-background_repeat'                 => $background_image_defaults['repeat'],
			'et_pb_menu-background_blend'                  => $background_image_defaults['blend'],
			// Module: Number Counter.
			'et_pb_number_counter-title_font_size'         => '16',
			'et_pb_number_counter-title_line_height'       => '1em',
			'et_pb_number_counter-title_letter_spacing'    => $font_defaults['letter_spacing'],
			'et_pb_number_counter-title_font_style'        => '',
			'et_pb_number_counter-number_font_size'        => '72',
			'et_pb_number_counter-number_line_height'      => '72px',
			'et_pb_number_counter-number_letter_spacing'   => $font_defaults['letter_spacing'],
			'et_pb_number_counter-number_font_style'       => '',
			'et_pb_number_counter-title_color'             => '#333333',
			'et_pb_number_counter-background_size'         => $background_image_defaults['size'],
			'et_pb_number_counter-background_position'     => $background_image_defaults['position'],
			'et_pb_number_counter-background_repeat'       => $background_image_defaults['repeat'],
			'et_pb_number_counter-background_blend'        => $background_image_defaults['blend'],
			// Module: Portfolio.
			'et_pb_portfolio-hover_overlay_color'          => 'rgba(255,255,255,0.9)',
			'et_pb_portfolio-title_font_size'              => '18',
			'et_pb_portfolio-title_letter_spacing'         => $font_defaults['letter_spacing'],
			'et_pb_portfolio-title_line_height'            => $font_defaults['line_height'],
			'et_pb_portfolio-title_font_style'             => '',
			'et_pb_portfolio-title_color'                  => '#333333',
			'et_pb_portfolio-caption_font_size'            => '14',
			'et_pb_portfolio-caption_letter_spacing'       => $font_defaults['letter_spacing'],
			'et_pb_portfolio-caption_line_height'          => $font_defaults['line_height'],
			'et_pb_portfolio-caption_font_style'           => '',
			'et_pb_portfolio-pagination_font_size'         => '14',
			'et_pb_portfolio-pagination_letter_spacing'    => $font_defaults['letter_spacing'],
			'et_pb_portfolio-pagination_line_height'       => $font_defaults['line_height'],
			'et_pb_portfolio-pagination_font_style'        => '',
			'et_pb_portfolio-background_size'              => $background_image_defaults['size'],
			'et_pb_portfolio-background_position'          => $background_image_defaults['position'],
			'et_pb_portfolio-background_repeat'            => $background_image_defaults['repeat'],
			'et_pb_portfolio-background_blend'             => $background_image_defaults['blend'],
			'et_pb_portfolio-zoom_icon_color'              => '',
			// Module: Post Title.
			'et_pb_post_title-title_font_size'             => '26px',
			'et_pb_post_title-title_line_height'           => '1em',
			'et_pb_post_title-title_letter_spacing'        => $font_defaults['letter_spacing'],
			'et_pb_post_title-meta_font_size'              => $font_defaults['size'],
			'et_pb_post_title-meta_line_height'            => '1em',
			'et_pb_post_title-meta_letter_spacing'         => $font_defaults['letter_spacing'],
			'et_pb_post_title-parallax'                    => 'off',
			'et_pb_post_title-background_size'             => $background_image_defaults['size'],
			'et_pb_post_title-background_position'         => $background_image_defaults['position'],
			'et_pb_post_title-background_repeat'           => $background_image_defaults['repeat'],
			'et_pb_post_title-background_blend'            => $background_image_defaults['blend'],
			// Module: Post Slider.
			'et_pb_post_slider-background_size'            => $background_image_defaults['size'],
			'et_pb_post_slider-background_position'        => $background_image_defaults['position'],
			'et_pb_post_slider-background_repeat'          => $background_image_defaults['repeat'],
			'et_pb_post_slider-background_blend'           => $background_image_defaults['blend'],
			// Module: Pricing Tables Item (Pricing Table).
			'et_pb_pricing_table-header_font_size'         => '22px',
			'et_pb_pricing_table-header_color'             => '#ffffff',
			'et_pb_pricing_table-header_line_height'       => '1em',
			'et_pb_pricing_table-subheader_font_size'      => '16px',
			'et_pb_pricing_table-subheader_color'          => '#ffffff',
			'et_pb_pricing_table-price_font_size'          => '80px',
			'et_pb_pricing_table-price_color'              => '#2EA3F2',
			'et_pb_pricing_table-price_line_height'        => '82px',
			'et_pb_pricing_table-body_line_height'         => '24px',
			'et_pb_pricing_table-background_size'          => $background_image_defaults['size'],
			'et_pb_pricing_table-background_position'      => $background_image_defaults['position'],
			'et_pb_pricing_table-background_repeat'        => $background_image_defaults['repeat'],
			'et_pb_pricing_table-background_blend'         => $background_image_defaults['blend'],
			'et_pb_pricing_table-excluded_letter_spacing'  => '0px',
			'et_pb_pricing_table-excluded_line_height'     => '1.7em',
			// Module: Pricing Tables.
			'et_pb_pricing_tables-header_font_size'        => '22',
			'et_pb_pricing_tables-header_font_style'       => '',
			'et_pb_pricing_tables-subheader_font_size'     => '16',
			'et_pb_pricing_tables-subheader_font_style'    => '',
			'et_pb_pricing_tables-price_font_size'         => '80',
			'et_pb_pricing_tables-price_font_style'        => '',
			'et_pb_pricing_tables-header_color'            => '#ffffff',
			'et_pb_pricing_tables-header_line_height'      => '1em',
			'et_pb_pricing_tables-subheader_color'         => '#ffffff',
			'et_pb_pricing_tables-currency_frequency_font_size' => '16px',
			'et_pb_pricing_tables-currency_frequency_letter_spacing' => '0px',
			'et_pb_pricing_tables-currency_frequency_line_height' => '1.7em',
			'et_pb_pricing_tables-price_letter_spacing'    => '0px',
			'et_pb_pricing_tables-price_color'             => '#2EA3F2',
			'et_pb_pricing_tables-price_line_height'       => '82px',
			'et_pb_pricing_tables-body_line_height'        => '24px',
			'et_pb_pricing_tables-background_size'         => $background_image_defaults['size'],
			'et_pb_pricing_tables-background_position'     => $background_image_defaults['position'],
			'et_pb_pricing_tables-background_repeat'       => $background_image_defaults['repeat'],
			'et_pb_pricing_tables-background_blend'        => $background_image_defaults['blend'],
			'et_pb_pricing_tables-excluded_letter_spacing' => '0px',
			'et_pb_pricing_tables-excluded_line_height'    => '1.7em',
			// Module: Shop.
			'et_pb_shop-title_font_size'                   => '16',
			'et_pb_shop-title_font_style'                  => '',
			'et_pb_shop-sale_badge_font_size'              => '16',
			'et_pb_shop-sale_badge_font_style'             => '',
			'et_pb_shop-price_font_size'                   => '14',
			'et_pb_shop-price_font_style'                  => '',
			'et_pb_shop-sale_price_font_size'              => '14',
			'et_pb_shop-sale_price_font_style'             => '',
			'et_pb_shop-title_color'                       => '#333333',
			'et_pb_shop-title_line_height'                 => '1em',
			'et_pb_shop-title_letter_spacing'              => $font_defaults['letter_spacing'],
			'et_pb_shop-price_line_height'                 => '26px',
			'et_pb_shop-price_letter_spacing'              => $font_defaults['letter_spacing'],
			// Module: Sidebar.
			'et_pb_sidebar-header_font_size'               => '18',
			'et_pb_sidebar-header_font_style'              => '',
			'et_pb_sidebar-header_color'                   => '#333333',
			'et_pb_sidebar-header_line_height'             => '1em',
			'et_pb_sidebar-header_letter_spacing'          => $font_defaults['letter_spacing'],
			'et_pb_sidebar-remove_border'                  => 'off',
			'et_pb_sidebar-body_font_size'                 => $font_defaults['size'],
			'et_pb_sidebar-body_line_height'               => $font_defaults['line_height'],
			'et_pb_sidebar-body_letter_spacing'            => $font_defaults['letter_spacing'],
			// Module: Signup.
			'et_pb_signup-header_font_size'                => '26',
			'et_pb_signup-header_letter_spacing'           => $font_defaults['letter_spacing'],
			'et_pb_signup-header_line_height'              => $font_defaults['line_height'],
			'et_pb_signup-body_font_size'                  => $font_defaults['size'],
			'et_pb_signup-body_letter_spacing'             => $font_defaults['letter_spacing'],
			'et_pb_signup-body_line_height'                => $font_defaults['line_height'],
			'et_pb_signup-result_message_font_size'        => $font_defaults_h2['size'],
			'et_pb_signup-result_message_line_height'      => $font_defaults_h2['line_height'],
			'et_pb_signup-header_font_style'               => '',
			'et_pb_signup-padding'                         => '20',
			'et_pb_signup-focus_border_color'              => '#ffffff',
			'et_pb_signup-background_size'                 => $background_image_defaults['size'],
			'et_pb_signup-background_position'             => $background_image_defaults['position'],
			'et_pb_signup-background_repeat'               => $background_image_defaults['repeat'],
			'et_pb_signup-background_blend'                => $background_image_defaults['blend'],
			'et_pb_signup-form_field_font_size'            => '14',
			'et_pb_signup-form_field_line_height'          => $font_defaults['line_height'],
			'et_pb_signup_form-form_field_letter_spacing'  => $font_defaults['letter_spacing'],
			// Module: Slider Item (Slide).
			'et_pb_slide-header_font_size'                 => '26px',
			'et_pb_slide-header_color'                     => '#ffffff',
			'et_pb_slide-header_line_height'               => '1em',
			'et_pb_slide-body_font_size'                   => '16px',
			'et_pb_slide-body_color'                       => '#ffffff',
			'et_pb_slide-background_size'                  => $background_image_defaults['size'],
			'et_pb_slide-background_position'              => $background_image_defaults['position'],
			'et_pb_slide-background_repeat'                => $background_image_defaults['repeat'],
			'et_pb_slide-background_blend'                 => $background_image_defaults['blend'],
			// Module: Slider.
			'et_pb_slider-header_font_size'                => '46',
			'et_pb_slider-header_line_height'              => '1em',
			'et_pb_slider-header_letter_spacing'           => $font_defaults['letter_spacing'],
			'et_pb_slider-header_font_style'               => '',
			'et_pb_slider-body_font_size'                  => '16',
			'et_pb_slider-body_letter_spacing'             => $font_defaults['letter_spacing'],
			'et_pb_slider-body_line_height'                => $font_defaults['line_height'],
			'et_pb_slider-body_font_style'                 => '',
			'et_pb_slider-padding'                         => '16',
			'et_pb_slider-header_color'                    => '#ffffff',
			'et_pb_slider-body_color'                      => '#ffffff',
			'et_pb_slider-background_size'                 => $background_image_defaults['size'],
			'et_pb_slider-background_position'             => $background_image_defaults['position'],
			'et_pb_slider-background_repeat'               => $background_image_defaults['repeat'],
			'et_pb_slider-background_blend'                => $background_image_defaults['blend'],
			// Module: Social Media Follow.
			'et_pb_social_media_follow-icon_size'          => '14',
			'et_pb_social_media_follow-button_font_style'  => '',
			// Module: Tabs.
			'et_pb_tabs-tab_font_size'                     => $font_defaults['size'],
			'et_pb_tabs-tab_line_height'                   => $font_defaults['line_height'],
			'et_pb_tabs-tab_letter_spacing'                => $font_defaults['letter_spacing'],
			'et_pb_tabs-title_font_size'                   => $font_defaults['size'],
			'et_pb_tabs-body_font_size'                    => $font_defaults['size'],
			'et_pb_tabs-body_line_height'                  => $font_defaults['line_height'],
			'et_pb_tabs-body_letter_spacing'               => $font_defaults['letter_spacing'],
			'et_pb_tabs-title_font_style'                  => '',
			'et_pb_tabs-padding'                           => '30',
			'et_pb_tabs-background_size'                   => $background_image_defaults['size'],
			'et_pb_tabs-background_position'               => $background_image_defaults['position'],
			'et_pb_tabs-background_repeat'                 => $background_image_defaults['repeat'],
			'et_pb_tabs-background_blend'                  => $background_image_defaults['blend'],
			// Module: Tabs Item (Tab).
			'et_pb_tab-background_size'                    => $background_image_defaults['size'],
			'et_pb_tab-background_position'                => $background_image_defaults['position'],
			'et_pb_tab-background_repeat'                  => $background_image_defaults['repeat'],
			'et_pb_tab-background_blend'                   => $background_image_defaults['blend'],
			// Module: Team Member (Person).
			'et_pb_team_member-header_font_size'           => '18',
			'et_pb_team_member-header_font_style'          => '',
			'et_pb_team_member-subheader_font_size'        => '14',
			'et_pb_team_member-subheader_font_style'       => '',
			'et_pb_team_member-social_network_icon_size'   => '16',
			'et_pb_team_member-header_color'               => '#333333',
			'et_pb_team_member-header_line_height'         => '1em',
			'et_pb_team_member-header_letter_spacing'      => $font_defaults['letter_spacing'],
			'et_pb_team_member-body_font_size'             => $font_defaults['size'],
			'et_pb_team_member-body_line_height'           => $font_defaults['line_height'],
			'et_pb_team_member-body_letter_spacing'        => $font_defaults['letter_spacing'],
			'et_pb_team_member-background_size'            => $background_image_defaults['size'],
			'et_pb_team_member-background_position'        => $background_image_defaults['position'],
			'et_pb_team_member-background_repeat'          => $background_image_defaults['repeat'],
			'et_pb_team_member-background_blend'           => $background_image_defaults['blend'],
			// Module: Testimonial.
			'et_pb_testimonial-portrait_border_radius'     => '90',
			'et_pb_testimonial-portrait_width'             => '90',
			'et_pb_testimonial-portrait_height'            => '90',
			'et_pb_testimonial-author_name_font_style'     => 'bold',
			'et_pb_testimonial-author_details_font_style'  => 'bold',
			'et_pb_testimonial-border_color'               => '#ffffff',
			'et_pb_testimonial-border_width'               => '1px',
			'et_pb_testimonial-body_font_size'             => $font_defaults['size'],
			'et_pb_testimonial-body_line_height'           => '1.5em',
			'et_pb_testimonial-body_letter_spacing'        => $font_defaults['letter_spacing'],
			'et_pb_testimonial-background_size'            => $background_image_defaults['size'],
			'et_pb_testimonial-background_position'        => $background_image_defaults['position'],
			'et_pb_testimonial-background_repeat'          => $background_image_defaults['repeat'],
			'et_pb_testimonial-background_blend'           => $background_image_defaults['blend'],
			'et_pb_testimonial-quote_icon_background_color' => '#f5f5f5',
			// Module: Text.
			'et_pb_text-header_font_size'                  => $font_defaults_h1['size'],
			'et_pb_text-header_letter_spacing'             => $font_defaults_h1['letter_spacing'],
			'et_pb_text-header_line_height'                => $font_defaults_h1['line_height'],
			'et_pb_text-text_font_size'                    => $font_defaults['size'],
			'et_pb_text-text_letter_spacing'               => $font_defaults['letter_spacing'],
			'et_pb_text-text_line_height'                  => $font_defaults['line_height'],
			'et_pb_text-border_color'                      => '#ffffff',
			'et_pb_text-border_width'                      => '1px',
			'et_pb_text-background_size'                   => $background_image_defaults['size'],
			'et_pb_text-background_position'               => $background_image_defaults['position'],
			'et_pb_text-background_repeat'                 => $background_image_defaults['repeat'],
			'et_pb_text-background_blend'                  => $background_image_defaults['blend'],
			// Module: Toggle.
			'et_pb_toggle-title_font_size'                 => '16',
			'et_pb_toggle-title_letter_spacing'            => $font_defaults['letter_spacing'],
			'et_pb_toggle-title_font_style'                => '',
			'et_pb_toggle-inactive_title_font_style'       => '',
			'et_pb_toggle-toggle_icon_size'                => '16',
			'et_pb_toggle-title_color'                     => '#333333',
			'et_pb_toggle-title_line_height'               => '1em',
			'et_pb_toggle-custom_padding'                  => '20',
			'et_pb_toggle-body_font_size'                  => $font_defaults['size'],
			'et_pb_toggle-body_line_height'                => $font_defaults['line_height'],
			'et_pb_toggle-body_letter_spacing'             => $font_defaults['letter_spacing'],
			'et_pb_toggle-background_size'                 => $background_image_defaults['size'],
			'et_pb_toggle-background_position'             => $background_image_defaults['position'],
			'et_pb_toggle-background_repeat'               => $background_image_defaults['repeat'],
			'et_pb_toggle-background_blend'                => $background_image_defaults['blend'],
			// Module: Woo Title.
			'et_pb_wc_title-header_font_size'              => $font_defaults_h1['size'],
			'et_pb_wc_title-header_line_height'            => '1em',
			'et_pb_wc_stock-in_stock_text_color'           => '#77a464',
			// Global: Field Input.
			'all_field_font_size'                          => '16',
			'all_field_border_width'                       => '0',
			'all_field_border_radius'                      => '3',
			'all_field_spacing'                            => '0',
			'all_field_font_style'                         => '',
			$hover->get_hover_field( 'all_field_border_radius' ) => '3',
			$hover->get_hover_field( 'all_field_spacing' ) => '0',
		);

		if ( et_builder_has_limitation( 'forced_icon_color_default' ) ) {
			$defaults['et_pb_gallery-zoom_icon_color']              = et_get_option( 'accent_color', '#2ea3f2' );
			$defaults['et_pb_portfolio-zoom_icon_color']            = et_get_option( 'accent_color', '#2ea3f2' );
			$defaults['et_pb_fullwidth-portfolio-zoom_icon_color']  = et_get_option( 'accent_color', '#2ea3f2' );
			$defaults['et_pb_filterable_portfolio-zoom_icon_color'] = et_get_option( 'accent_color', '#2ea3f2' );
		}

		$module_presets_manager = ET_Builder_Global_Presets_Settings::instance();
		if ( ! et_is_builder_plugin_active() && ! ET_Builder_Global_Presets_Settings::is_customizer_migrated() ) {
			$module_presets_manager->migrate_customizer_settings( $defaults );
		}

		$custom_defaults_unmigrated = et_get_option( ET_Builder_Global_Presets_Settings::CUSTOM_DEFAULTS_UNMIGRATED_OPTION, false );

		// reformat defaults array and add actual values to it.
		foreach ( $defaults as $setting_name => $default_value ) {
			$defaults[ $setting_name ] = array(
				'default' => $default_value,
			);

			if ( ! et_is_builder_plugin_active() ) {
				$actual_value  = (string) et_get_option( $setting_name, '', '', true );
				$add_as_actual = false;

				// Pass Theme Customizer non module specific settings.
				$setting_array = explode( '-', $setting_name );
				$module_name   = $setting_array[0];

				if ( empty( $setting_array[1] ) || ! empty( $custom_defaults_unmigrated->$module_name ) && in_array( $setting_array[1], ET_Builder_Global_Presets_Settings::$phase_two_settings, true ) ) {
					$add_as_actual = true;
				}

				if ( $add_as_actual && '' !== $actual_value ) {
					$defaults[ $setting_name ]['actual'] = $actual_value;
				}
			}
		}

		self::$_settings = apply_filters( 'et_set_default_values', $defaults );
	}

	/**
	 * Get default global setting value
	 *
	 * @param  string $name Setting name.
	 * @param  string $get_value Defines the value it should get: actual or default.
	 *
	 * @return mixed             Global setting value or FALSE
	 */
	public static function get_value( $name, $get_value = 'actual' ) {
		$settings = self::$_settings;

		if ( ! isset( $settings[ $name ] ) ) {
			return false;
		}

		if ( isset( $settings[ $name ][ $get_value ] ) ) {
			$result = $settings[ $name ][ $get_value ];
		} elseif ( 'actual' === $get_value && isset( $settings[ $name ]['default'] ) ) {
			$result = $settings[ $name ]['default'];
		} else {
			$result = false;
		}

		return $result;
	}

	/**
	 * Translate 'on'/'off' into true/false
	 * Pagebuilder use pseudo checkbox with 'on'/'off' value while customizer use true/false
	 * which cause ET_Global_Settings' default value incompatibilities.
	 *
	 * @param string $name Setting name.
	 * @param string $get_value Defines the value it should get: actual or default.
	 * @param string $source pagebuilder or customizer.
	 *
	 * @return bool|string
	 */
	public static function get_checkbox_value( $name, $get_value = 'actual', $source = 'pagebuilder' ) {
		// Get value.
		$value = self::get_value( $name, $get_value );

		// customizer to pagebuilder || pagebuilder to customizer.
		if ( 'customizer' === $source ) {
			if ( false === $value ) {
				return 'off';
			} else {
				return 'on';
			}
		} else {
			if ( 'off' === $value || false === $value ) {
				return false;
			} else {
				return true;
			}
		}
	}
}

/**
 * Initialize the global settings.
 */
function et_builder_init_global_settings() {
	ET_Global_Settings::init();
}

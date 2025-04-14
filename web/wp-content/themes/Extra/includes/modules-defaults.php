<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Setup default values for Extra specific modules. Default values are also define
 * the unit used in advanced design settings
 * @return array
 */
function extra_set_modules_default_values( $defaults ) {
	$extra_defaults = array(
			// Posts
		'et_pb_posts-header_font_size'                            => '16px',
		'et_pb_posts-header_font_style'                           => 'uppercase',
		'et_pb_posts-header_text_color'                           => '',
		'et_pb_posts-header_line_height'                          => '1',
		'et_pb_posts-header_letter_spacing'                       => '1.2px',

		'et_pb_posts-subheader_font_size'                         => '14px',
		'et_pb_posts-subheader_font_style'                        => '',
		'et_pb_posts-subheader_text_color'                        => '',
		'et_pb_posts-subheader_line_height'                       => '1em',
		'et_pb_posts-subheader_letter_spacing'                    => '0px',

		'et_pb_posts-main_title_font_size'                        => '16px',
		'et_pb_posts-main_title_font_style'                       => '',
		'et_pb_posts-main_title_text_color'                       => 'rgba(0,0,0,0.75)',
		'et_pb_posts-main_title_line_height'                      => '1.3em',
		'et_pb_posts-main_title_letter_spacing'                   => '0.5px',

		'et_pb_posts-main_meta_font_size'                         => '12px',
		'et_pb_posts-main_meta_font_style'                        => '',
		'et_pb_posts-main_meta_text_color'                        => 'rgba(0, 0, 0, 0.5)',
		'et_pb_posts-main_meta_line_height'                       => '1.7em',
		'et_pb_posts-main_meta_letter_spacing'                    => '0px',

		'et_pb_posts-main_body_font_size'                         => '14px',
		'et_pb_posts-main_body_font_style'                        => '',
		'et_pb_posts-main_body_text_color'                        => 'rgba(0, 0, 0, 0.6)',
		'et_pb_posts-main_body_line_height'                       => '1.8em',
		'et_pb_posts-main_body_letter_spacing'                    => '0px',

		'et_pb_posts-list_title_font_size'                        => '14px',
		'et_pb_posts-list_title_font_style'                       => '',
		'et_pb_posts-list_title_text_color'                       => 'rgba(0,0,0,0.75)',
		'et_pb_posts-list_title_line_height'                      => '1.3em',
		'et_pb_posts-list_title_letter_spacing'                   => '0px',

		'et_pb_posts-list_meta_font_size'                         => '12px',
		'et_pb_posts-list_meta_font_style'                        => '',
		'et_pb_posts-list_meta_text_color'                        => 'rgba(0, 0, 0, 0.5)',
		'et_pb_posts-list_meta_line_height'                       => '1.7em',
		'et_pb_posts-list_meta_letter_spacing'                    => '0px',

		'et_pb_posts-remove_drop_shadow'                          => 'off',
		'et_pb_posts-border_radius'                               => '3px',

			// Tabbed Posts
		'et_pb_tabbed_posts-active_tab_background_color'          => '#ffffff',
		'et_pb_tabbed_posts-inactive_tab_background_color'        => '#f6f9fd',

		'et_pb_tabbed_posts-tab_font_size'                        => '16px',
		'et_pb_tabbed_posts-tab_font_style'                       => '',
		'et_pb_tabbed_posts-tab_text_color'                       => '',
		'et_pb_tabbed_posts-tab_line_height'                      => '1em',
		'et_pb_tabbed_posts-tab_letter_spacing'                   => '1.2px',

		'et_pb_tabbed_posts-main_title_font_size'                 => '16px',
		'et_pb_tabbed_posts-main_title_font_style'                => '',
		'et_pb_tabbed_posts-main_title_text_color'                => 'rgba(0,0,0,0.75)',
		'et_pb_tabbed_posts-main_title_line_height'               => '1.3em',
		'et_pb_tabbed_posts-main_title_letter_spacing'            => '0.5px',

		'et_pb_tabbed_posts-main_meta_font_size'                  => '12px',
		'et_pb_tabbed_posts-main_meta_font_style'                 => '',
		'et_pb_tabbed_posts-main_meta_text_color'                 => 'rgba(0, 0, 0, 0.5)',
		'et_pb_tabbed_posts-main_meta_line_height'                => '1.7em',
		'et_pb_tabbed_posts-main_meta_letter_spacing'             => '0px',

		'et_pb_tabbed_posts-main_body_font_size'                  => '14px',
		'et_pb_tabbed_posts-main_body_font_style'                 => '',
		'et_pb_tabbed_posts-main_body_text_color'                 => 'rgba(0, 0, 0, 0.6)',
		'et_pb_tabbed_posts-main_body_line_height'                => '1.8em',
		'et_pb_tabbed_posts-main_body_letter_spacing'             => '0px',

		'et_pb_tabbed_posts-list_title_font_size'                 => '14px',
		'et_pb_tabbed_posts-list_title_font_style'                => '',
		'et_pb_tabbed_posts-list_title_text_color'                => 'rgba(0,0,0,0.75)',
		'et_pb_tabbed_posts-list_title_line_height'               => '1.3em',
		'et_pb_tabbed_posts-list_title_letter_spacing'            => '0px',

		'et_pb_tabbed_posts-list_meta_font_size'                  => '12px',
		'et_pb_tabbed_posts-list_meta_font_style'                 => '',
		'et_pb_tabbed_posts-list_meta_text_color'                 => 'rgba(0, 0, 0, 0.5)',
		'et_pb_tabbed_posts-list_meta_line_height'                => '1.7em',
		'et_pb_tabbed_posts-list_meta_letter_spacing'             => '0px',

		'et_pb_tabbed_posts-remove_drop_shadow'                   => 'off',
		'et_pb_tabbed_posts-border_radius'                        => '3px',

			// Posts Carousel
		'et_pb_posts_carousel-hover_overlay_color'                => 'rgba(0, 0, 0, 0.3)',
		'et_pb_posts_carousel-hover_overlay_icon_color'           => '#FFFFFF',
		'et_pb_posts_carousel-hover_overlay_icon'                 => '\e050',

		'et_pb_posts_carousel-header_font_size'                   => '16px',
		'et_pb_posts_carousel-header_font_style'                  => 'uppercase',
		'et_pb_posts_carousel-header_text_color'                  => '',
		'et_pb_posts_carousel-header_line_height'                 => '1',
		'et_pb_posts_carousel-header_letter_spacing'              => '1.2px',

		'et_pb_posts_carousel-subheader_font_size'                => '14px',
		'et_pb_posts_carousel-subheader_font_style'               => '',
		'et_pb_posts_carousel-subheader_text_color'               => '',
		'et_pb_posts_carousel-subheader_line_height'              => '1em',
		'et_pb_posts_carousel-subheader_letter_spacing'           => '0px',

		'et_pb_posts_carousel-title_font_size'                    => '14px',
		'et_pb_posts_carousel-title_font_style'                   => '',
		'et_pb_posts_carousel-title_text_color'                   => 'rgba(0,0,0,0.75)',
		'et_pb_posts_carousel-title_line_height'                  => '1.3em',
		'et_pb_posts_carousel-title_letter_spacing'               => '0.5px',

		'et_pb_posts_carousel-meta_font_size'                     => '12px',
		'et_pb_posts_carousel-meta_font_style'                    => '',
		'et_pb_posts_carousel-meta_text_color'                    => 'rgba(0, 0, 0, 0.5)',
		'et_pb_posts_carousel-meta_line_height'                   => '1.9em',
		'et_pb_posts_carousel-meta_letter_spacing'                => '0px',

		'et_pb_posts_carousel-remove_drop_shadow'                 => 'off',
		'et_pb_posts_carousel-border_radius'                      => '3px',

			// Featured Posts Slider
		'et_pb_featured_posts_slider-title_font_size'             => '20px',
		'et_pb_featured_posts_slider-title_font_style'            => '',
		'et_pb_featured_posts_slider-title_text_color'            => '#ffffff',
		'et_pb_featured_posts_slider-title_line_height'           => '1.3em',
		'et_pb_featured_posts_slider-title_letter_spacing'        => '0.5px',

		'et_pb_featured_posts_slider-meta_font_size'              => '12px',
		'et_pb_featured_posts_slider-meta_font_style'             => '',
		'et_pb_featured_posts_slider-meta_text_color'             => 'rgba(255, 255, 255, 0.6)',
		'et_pb_featured_posts_slider-meta_line_height'            => '1.7em',
		'et_pb_featured_posts_slider-meta_letter_spacing'         => '0px',

		'et_pb_featured_posts_slider-slide_caption_background'    => 'rgba(0, 0, 0, 0.6)',
		'et_pb_featured_posts_slider-remove_drop_shadow'          => 'off',
		'et_pb_featured_posts_slider-border_radius'               => '3px',

			// Blog Feed Standard
		'et_pb_posts_blog_feed_standard-header_font_size'         => '16px',
		'et_pb_posts_blog_feed_standard-header_font_style'        => 'uppercase',
		'et_pb_posts_blog_feed_standard-header_text_color'        => '',
		'et_pb_posts_blog_feed_standard-header_line_height'       => '1',
		'et_pb_posts_blog_feed_standard-header_letter_spacing'    => '1.2px',

		'et_pb_posts_blog_feed_standard-title_font_size'          => '18px',
		'et_pb_posts_blog_feed_standard-title_font_style'         => '',
		'et_pb_posts_blog_feed_standard-title_text_color'         => '',
		'et_pb_posts_blog_feed_standard-title_letter_spacing'     => '0.5px',
		'et_pb_posts_blog_feed_standard-title_line_height'        => '1.3em',

		'et_pb_posts_blog_feed_standard-meta_font_size'           => '12px',
		'et_pb_posts_blog_feed_standard-meta_font_style'          => '',
		'et_pb_posts_blog_feed_standard-meta_text_color'          => 'rgba(0, 0, 0, 0.5)',
		'et_pb_posts_blog_feed_standard-meta_letter_spacing'      => '0px',
		'et_pb_posts_blog_feed_standard-meta_line_height'         => '1.7em',

		'et_pb_posts_blog_feed_standard-body_font_size'           => '14px',
		'et_pb_posts_blog_feed_standard-body_font_style'          => '',
		'et_pb_posts_blog_feed_standard-body_text_color'          => 'rgba(0, 0, 0, 0.6)',
		'et_pb_posts_blog_feed_standard-body_letter_spacing'      => '0px',
		'et_pb_posts_blog_feed_standard-body_line_height'         => '1.7em',

		'et_pb_posts_blog_feed_standard-read_more_text_size'      => '14px',
		'et_pb_posts_blog_feed_standard-read_more_font'           => '',
		'et_pb_posts_blog_feed_standard-read_more_text_color'     => 'rgba(0, 0, 0, 0.6)',
		'et_pb_posts_blog_feed_standard-read_more_letter_spacing' => '0px',
		'et_pb_posts_blog_feed_standard-read_more_bg_color'       => 'rgba(0, 0, 0, 0.1)',
		'et_pb_posts_blog_feed_standard-read_more_border_radius'  => '3px',

		'et_pb_posts_blog_feed_standard-remove_drop_shadow'       => 'off',
		'et_pb_posts_blog_feed_standard-border_radius'            => '3px',

		'et_pb_posts_blog_feed_standard-hover_overlay_color'      => 'rgba(0, 0, 0, 0.3)',
		'et_pb_posts_blog_feed_standard-hover_overlay_icon_color' => '#FFFFFF',
		'et_pb_posts_blog_feed_standard-hover_overlay_icon'       => '\e050',

			// Blog Feed Masonry
		'et_pb_posts_blog_feed_masonry-title_font_size'           => '16px',
		'et_pb_posts_blog_feed_masonry-title_font_style'          => '',
		'et_pb_posts_blog_feed_masonry-title_text_color'          => 'rgba(0,0,0,0.75)',
		'et_pb_posts_blog_feed_masonry-title_letter_spacing'      => '0.5px',
		'et_pb_posts_blog_feed_masonry-title_line_height'         => '1.3em',

		'et_pb_posts_blog_feed_masonry-meta_font_size'            => '12px',
		'et_pb_posts_blog_feed_masonry-meta_font_style'           => '',
		'et_pb_posts_blog_feed_masonry-meta_text_color'           => 'rgba(0, 0, 0, 0.5)',
		'et_pb_posts_blog_feed_masonry-meta_letter_spacing'       => '0px',
		'et_pb_posts_blog_feed_masonry-meta_line_height'          => '1.7em',

		'et_pb_posts_blog_feed_masonry-body_font_size'            => '14px',
		'et_pb_posts_blog_feed_masonry-body_font_style'           => '',
		'et_pb_posts_blog_feed_masonry-body_text_color'           => '',
		'et_pb_posts_blog_feed_masonry-body_letter_spacing'       => '0px',
		'et_pb_posts_blog_feed_masonry-body_line_height'          => '1.7em',

		'et_pb_posts_blog_feed_masonry-read_more_text_size'       => '14px',
		'et_pb_posts_blog_feed_masonry-read_more_font'            => '',
		'et_pb_posts_blog_feed_masonry-read_more_text_color'      => 'rgba(0, 0, 0, 0.6)',
		'et_pb_posts_blog_feed_masonry-read_more_letter_spacing'  => '0px',
		'et_pb_posts_blog_feed_masonry-read_more_bg_color'        => 'rgba(0, 0, 0, 0.1)',
		'et_pb_posts_blog_feed_masonry-read_more_border_radius'   => '3px',

		'et_pb_posts_blog_feed_masonry-remove_drop_shadow'        => 'off',
		'et_pb_posts_blog_feed_masonry-border_radius'             => '3px',

		'et_pb_posts_blog_feed_masonry-hover_overlay_color'       => 'rgba(0, 0, 0, 0.3)',
		'et_pb_posts_blog_feed_masonry-hover_overlay_icon_color'  => '#FFFFFF',
		'et_pb_posts_blog_feed_masonry-hover_overlay_icon'        => '\e050',

			// Ads
		'et_pb_ads-background_color'                              => '#FFFFFF',
		'et_pb_ads-remove_drop_shadow'                            => 'off',
		'et_pb_ads-border_radius'                                 => '3px',

		'et_pb_ads-header_font_size'                              => '16px',
		'et_pb_ads-header_font_style'                             => 'uppercase',
		'et_pb_ads-header_text_color'                             => '',
		'et_pb_ads-header_line_height'                            => '1',
		'et_pb_ads-header_letter_spacing'                         => '1.2px',
	);

	foreach ( $extra_defaults as $setting_name => $default_value ) {
		$extra_defaults[ $setting_name ] = array(
			'default' => $default_value,
		);

		$actual_value = ! et_is_builder_plugin_active() ? et_get_option( $setting_name, '', '', true ) : '';
		if ( '' !== $actual_value ) {
			$extra_defaults[ $setting_name ]['actual']  = $actual_value;
		}
	}

	return array_merge( $defaults, $extra_defaults );
}

add_filter( 'et_set_default_values', 'extra_set_modules_default_values' );

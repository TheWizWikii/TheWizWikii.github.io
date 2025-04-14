<?php
class ET_Builder_Module_Settings_Migration_OptionsHarmony extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.74';

	public function __construct() {
		parent::__construct();

		self::$_bb_excluded_name_changes[] = 'text_orientation';
	}

	public function get_fields() {
		return array(
			'parallax'                   => array(
				'affected_fields' => array(
					'parallax_effect' => $this->get_modules( 'post_title' ),
				),
			),
			'parallax_method'            => array(
				'affected_fields' => array(
					'parallax_effect' => $this->get_modules( 'post_title' ),
					'parallax_method' => $this->get_modules( 'slide' ),
				),
				'values'          => array(
					'off' => 'on',
					'on'  => 'off',
				),
			),
			'background_color'           => array(
				'affected_fields' => array(
					'module_bg_color'           => $this->get_modules( 'post_title' ),
					'field_bg'                  => $this->get_modules( 'search' ),
					'bg_color'                  => $this->get_modules( 'social_media_follow_network' ),
					'transparent_background'    => $this->get_modules( 'section' ),
					'transparent_background_fb' => $this->get_modules( 'section' ),
				),
			),
			'custom_padding'             => array(
				'affected_fields' => array(
					'bar_top_padding'    => $this->get_modules( 'bar_counters' ),
					'bar_bottom_padding' => $this->get_modules( 'bar_counters' ),
					'top_padding'        => $this->get_modules( 'slider' ),
					'bottom_padding'     => $this->get_modules( 'slider' ),
					'custom_padding'     => $this->get_modules( 'section' ),
				),
			),
			'custom_padding_tablet'      => array(
				'affected_fields' => array(
					'bar_top_padding_tablet'    => $this->get_modules( 'bar_counters' ),
					'bar_bottom_padding_tablet' => $this->get_modules( 'bar_counters' ),
					'top_padding_tablet'        => $this->get_modules( 'slider' ),
					'bottom_padding_tablet'     => $this->get_modules( 'slider' ),
					'padding_mobile'            => $this->get_modules( 'section' ),
				),
			),
			'custom_padding_phone'       => array(
				'affected_fields' => array(
					'bar_top_padding_phone'    => $this->get_modules( 'bar_counters' ),
					'bar_bottom_padding_phone' => $this->get_modules( 'bar_counters' ),
					'top_padding_phone'        => $this->get_modules( 'slider' ),
					'bottom_padding_phone'     => $this->get_modules( 'slider' ),
				),
			),
			'custom_padding_last_edited' => array(
				'affected_fields' => array(
					'bar_top_padding_last_edited'    => $this->get_modules( 'bar_counters' ),
					'bar_bottom_padding_last_edited' => $this->get_modules( 'bar_counters' ),
					'top_padding_last_edited'        => $this->get_modules( 'slider' ),
					'bottom_padding_last_edited'     => $this->get_modules( 'slider' ),
					'padding_mobile'                 => $this->get_modules( 'section' ),
				),
			),
			'background_video_mp4'       => array(
				'affected_fields' => array(
					'video_bg_mp4' => $this->get_modules( 'slide' ),
				),
			),
			'background_video_webm'      => array(
				'affected_fields' => array(
					'video_bg_webm' => $this->get_modules( 'slide' ),
				),
			),
			'background_video_width'     => array(
				'affected_fields' => array(
					'video_bg_width' => $this->get_modules( 'slide' ),
				),
			),
			'background_video_height'    => array(
				'affected_fields' => array(
					'video_bg_height' => $this->get_modules( 'slide' ),
				),
			),
			'disabled_on'                => array(
				'affected_fields' => array(
					'hide_on_mobile' => $this->get_modules( 'divider' ),
				),
			),
			'show_prev'                  => array(
				'affected_fields' => array(
					'hide_prev' => $this->get_modules( 'post_nav' ),
				),
			),
			'show_next'                  => array(
				'affected_fields' => array(
					'hide_next' => $this->get_modules( 'post_nav' ),
				),
			),
			'show_inner_shadow'          => array(
				'affected_fields' => array(
					'remove_inner_shadow' => $this->get_modules( 'slider' ),
				),
			),
			'show_featured_drop_shadow'  => array(
				'affected_fields' => array(
					'remove_featured_drop_shadow' => $this->get_modules( 'pricing_table' ),
				),
			),
			'show_button'                => array(
				'affected_fields' => array(
					'hide_button' => $this->get_modules( 'search' ),
				),
			),
			'show_border'                => array(
				'affected_fields' => array(
					'remove_border' => $this->get_modules( 'sidebar' ),
				),
			),
			'show_image_overlay'         => array(
				'affected_fields' => array(
					'show_image_overlay' => $this->get_modules( 'video_slider' ),
				),
			),
			'show_content_on_mobile'     => array(
				'affected_fields' => array(
					'hide_content_on_mobile' => $this->get_modules( 'slider' ),
				),
			),
			'show_cta_on_mobile'         => array(
				'affected_fields' => array(
					'hide_cta_on_mobile' => $this->get_modules( 'slider' ),
				),
			),
			'show_bottom_space'          => array(
				'affected_fields' => array(
					'sticky' => $this->get_modules( 'image' ),
				),
			),
			'image_max_width'            => array(
				'affected_fields' => array(
					'max_width' => $this->get_modules( 'blurb' ),
				),
			),
			'content_max_width'          => array(
				'affected_fields' => array(
					'max_width' => $this->get_modules( 'fullwidth_header' ),
				),
			),
			'max_width'                  => array(
				'affected_fields' => array(
					'max_width' => $this->get_modules( 'reset_max_width' ),
				),
			),
			'title_text_color'           => array(
				'affected_fields' => array(
					'label_color' => $this->get_modules( 'bar_counter' ),
				),
			),
			'percent_text_color'         => array(
				'affected_fields' => array(
					'percentage_color' => $this->get_modules( 'bar_counter' ),
				),
			),
			'module_alignment'           => array(
				'affected_fields' => array(
					'text_orientation' => $this->get_modules( 'text_orientation' ),
				),
			),
		);
	}

	public function get_modules( $attr = '' ) {
		$modules = array();
		// Section
		if ( in_array( $attr, array( '', 'section' ) ) ) {
			$modules[] = 'et_pb_section';
		}

		// Post title & Fullwidth post title migration settings
		if ( in_array( $attr, array( '', 'post_title' ) ) ) {
			$modules[] = 'et_pb_post_title';
			$modules[] = 'et_pb_fullwidth_post_title';
		}

		// Blurb migration setting
		if ( in_array( $attr, array( '', 'blurb' ) ) ) {
			$modules[] = 'et_pb_blurb';
		}

		// Fullwidth Header
		if ( in_array( $attr, array( '', 'fullwidth_header' ) ) ) {
			$modules[] = 'et_pb_fullwidth_header';
		}

		if ( 'reset_max_width' === $attr ) {
			$modules[] = 'et_pb_blurb';
			$modules[] = 'et_pb_fullwidth_header';
		}

		if ( in_array( $attr, array( '', 'text_orientation' ) ) ) {
			$modules[] = 'et_pb_text';
			$modules[] = 'et_pb_search';
		}

		// Social media follow item migration settings
		if ( in_array( $attr, array( '', 'social_media_follow_network' ) ) ) {
			$modules[] = 'et_pb_social_media_follow_network';
		}

		// Bar Counters
		if ( in_array( $attr, array( '', 'bar_counters' ) ) ) {
			$modules[] = 'et_pb_counters';
		}

		// Bar Counter Item
		if ( in_array( $attr, array( '', 'bar_counter' ) ) ) {
			$modules[] = 'et_pb_counter';
		}

		// Slider
		if ( in_array( $attr, array( '', 'slider' ) ) ) {
			$modules[] = 'et_pb_post_slider';
			$modules[] = 'et_pb_fullwidth_post_slider';
			$modules[] = 'et_pb_slider';
			$modules[] = 'et_pb_fullwidth_slider';
		}

		// Slide Item
		if ( in_array( $attr, array( '', 'slide' ) ) ) {
			$modules[] = 'et_pb_slide';
		}

		// Divider
		if ( in_array( $attr, array( '', 'divider' ) ) ) {
			$modules[] = 'et_pb_divider';
		}

		// Post Nav
		if ( in_array( $attr, array( '', 'post_nav' ) ) ) {
			$modules[] = 'et_pb_post_nav';
		}

		// Pricing Tables
		if ( in_array( $attr, array( '', 'pricing_table' ) ) ) {
			$modules[] = 'et_pb_pricing_tables';
		}

		// Search
		if ( in_array( $attr, array( '', 'search' ) ) ) {
			$modules[] = 'et_pb_search';
		}

		// Sidebar
		if ( in_array( $attr, array( '', 'sidebar' ) ) ) {
			$modules[] = 'et_pb_sidebar';
		}

		// Video Slider
		if ( in_array( $attr, array( '', 'video_slider' ) ) ) {
			$modules[] = 'et_pb_video_slider';
		}

		// Image
		if ( in_array( $attr, array( '', 'image' ) ) ) {
			$modules[] = 'et_pb_image';
		}

		return $modules;
	}

	public function migrate_spacing( $current_value, $saved_value, $location, $saved_field_name = '', $module_slug = '' ) {
		$locations      = array( 'top', 'right', 'bottom', 'left' );
		$location_index = array_search( $location, $locations );
		$spacings       = array( '', '', '', '' );

		if ( -1 === $location_index ) {
			return $current_value;
		}

		if ( '' !== $saved_value ) {
			$spacings                    = explode( '|', $saved_value );
			$spacings[ $location_index ] = $saved_value;
		}

		$spacings[ $location_index ] = $current_value;

		// Automatically add zero pixel to padding-right & padding-left of slider & post slider if custom top/bottom padding exist
		if ( '' !== $current_value && in_array( $saved_field_name, array( 'top_padding', 'bottom_padding' ) ) && in_array( $module_slug, array( 'et_pb_slider', 'et_pb_post_slider' ) ) ) {
			$spacings[1] = '0px';
			$spacings[3] = '0px';
		}

		return implode( '|', $spacings );
	}

	/**
	 * Re-map value to new value. Originally add to reverse yes/no button value due to option migration
	 *
	 * @param string saved value
	 * @param array  map of value, expected mapped value in key-value relationship
	 * @param string default value
	 * @return string mapped value
	 */
	public function migrate_remap_value( $value, $map = array(), $default = 'on' ) {
		$map = wp_parse_args(
			$map,
			array(
				''    => 'on',
				'off' => 'on',
				'on'  => 'off',
			)
		);

		return isset( $map[ $value ] ) ? $map[ $value ] : $default;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Padding top & bottom migration to full paddings
		if ( in_array( $module_slug, array( 'et_pb_counters', 'et_pb_post_slider', 'et_pb_fullwidth_post_slider', 'et_pb_slider', 'et_pb_fullwidth_slider' ) ) ) {

			$top_padding_selectors = array(
				'bar_top_padding',
				'bar_top_padding_tablet',
				'bar_top_padding_phone',
				'top_padding',
				'top_padding_tablet',
				'top_padding_phone',
			);

			if ( in_array( $saved_field_name, $top_padding_selectors ) ) {
				return $this->migrate_spacing( $current_value, $saved_value, 'top', $saved_field_name, $module_slug );
			}

			$bottom_padding_selectors = array(
				'bar_bottom_padding',
				'bar_bottom_padding_tablet',
				'bar_bottom_padding_phone',
				'bottom_padding',
				'bottom_padding_tablet',
				'bottom_padding_phone',
			);

			if ( in_array( $saved_field_name, $bottom_padding_selectors ) ) {
				return $this->migrate_spacing( $current_value, $saved_value, 'bottom', $saved_field_name, $module_slug );
			}

			if ( in_array( $saved_field_name, array( 'bar_top_padding_last_edited', 'bar_bottom_padding_last_edited' ) ) ) {
				$last_edited_current_value = '' === $current_value ? array( '', '' ) : explode( '|', $current_value );
				$last_edited_saved_value   = '' === $saved_value ? array( '', '' ) : explode( '|', $saved_value );
				$last_edited_responsive    = in_array( 'on', array( $last_edited_current_value[0], $last_edited_saved_value[0] ) ) ? 'on' : 'off';
				$last_edited_tabs          = array_filter( array( $last_edited_current_value[1], $last_edited_saved_value[1] ) );
				$last_edited_tab           = empty( $last_edited_tabs ) ? 'desktop' : end( $last_edited_tabs );

				return "{$last_edited_responsive}|{$last_edited_tab}";
			}
		}

		// Section's padding related migration
		if ( 'et_pb_section' === $module_slug ) {

			// Migrating two side paddings (prior to Divi v2.4) into full paddings
			if ( 'custom_padding' === $field_name && 'custom_padding' === $saved_field_name ) {
				$padding_sides = explode( '|', $current_value );

				if ( 2 === count( $padding_sides ) ) {
					$full_padding_sides = array(
						$padding_sides[0],
						'',
						$padding_sides[1],
						'',
					);

					return implode( '|', $full_padding_sides );
				}
			}

			// Translating "Keep padding on mobile" options into *_last_edited value
			if ( 'custom_padding_last_edited' === $field_name && 'padding_mobile' === $saved_field_name ) {

				if ( 'off' === $current_value && '' === $saved_value ) {
					return 'on|desktop';
				}

				return $saved_value;
			}

			// Translating "Keep padding on mobile" options into custom_padding_tablet value
			if ( 'custom_padding_tablet' === $field_name && 'padding_mobile' === $saved_field_name ) {
				// Keep padding on mobile is disabled
				if ( '' === $saved_value && 'off' === $current_value ) {
					return '50px|0|50px|0';
				}

				// Keep padding on mobile is enabled and custom padding is defined. If no custom_padding value is defined,
				// the value is expected to be empty string, which makes letting $saved_value to be returned is ok
				if ( '' === $saved_value && 'on' === $current_value && isset( $attrs['custom_padding'] ) ) {
					return $attrs['custom_padding'];
				}

				return $saved_value;
			}

			// Translating transparent_background into background_color
			if ( 'background_color' === $field_name && in_array( $saved_field_name, array( 'transparent_background', 'transparent_background_fb' ) ) ) {
				// Transparent is default for Builder Plugin, but not for theme
				if ( 'on' === $current_value || ( et_is_builder_plugin_active() && 'default' === $current_value ) ) {
					return 'rgba(255,255,255,0)';
				} else {
					return $saved_value;
				}
			}
		}

		// Divider's hide_on_mobile migration
		if ( 'et_pb_divider' === $module_slug && 'disabled_on' === $field_name ) {
			// Set disabled_on if it is previously empty and hide_on_mobile is empty / on (its default value is on)
			if ( in_array( $current_value, array( '', 'on' ) ) && '' === $saved_value ) {
				return 'on|on|off';
			}

			// Translate hide_on_mobile into disabled_on if hide_on_mobile is empty / on and saved_value isn't empty
			if ( in_array( $current_value, array( '', 'on' ) ) && '' !== $saved_value ) {
				$disabled_on_array    = explode( '|', $saved_value );
				$disabled_on_array[0] = 'on';
				$disabled_on_array[1] = 'on';

				return implode( '|', $disabled_on_array );
			}

			// Otherwise, return saved value
			return $saved_value;
		}

		if ( 'parallax_method' === $field_name ) {
			// Reverse post title and fullwidth post title's parallax_method value because its previous select option values (on = CSS, off = True)
			// were reversed compared to background UI's parallax_method select options values (on = True, off = CSS)
			if ( in_array( $module_slug, $this->get_modules( 'post_title' ) ) && isset( $this->fields[ $field_name ] ) && isset( $this->fields[ $field_name ]['values'][ $saved_value ] ) ) {
				return $this->fields[ $field_name ]['values'][ $saved_value ];
			}

			// Previous version of slide item has parallax_method options order reversed which causing CSS Parallax can't be rendered.
			// If the attribute has been saved, it will be rendered just fine
			// $saved_value reflects the correct value, now it inherits value from Slider / Fullwidth Slider module
			if ( in_array( $module_slug, $this->get_modules( 'slide' ) ) && '' === $current_value ) {
				return $saved_value;
			}
		}

		// Normalizing video slider's display image overlay's hide/show & select-based field into yes_no_button + on/off-based option
		if ( 'et_pb_video_slider' === $module_slug && 'show_image_overlay' === $field_name ) {
			return $this->migrate_remap_value(
				$current_value,
				array(
					'hide' => 'off',
					'show' => 'on',
					''     => 'off',
				),
				'off'
			);
		}

		// Search's input background migration
		if ( 'et_pb_search' === $module_slug && 'background_color' === $field_name ) {
			if ( '' !== $saved_value && '' === $current_value ) {
				return $saved_value;
			}
		}

		// Only Text & Search module's text_orientation = center that is being migrated to each module's module_alignment attribute
		// If module's text_orientation has different value, let it be. This is needed because on previous version, .et_pb_text_align_center class
		// set text-align: center; AND margin: 0 auto; margin: 0 auto; belongs to module alignment now, hence the migration
		if ( in_array( $module_slug, $this->get_modules( 'text_orientation' ) ) && 'text_orientation' === $saved_field_name && 'module_alignment' === $field_name ) {
			if ( 'center' === $current_value && '' === $saved_value ) {
				return $current_value;
			}

			return $saved_value;
		}

		// Normalizing "remove_*" & "hide_*" option, to ensure that "Yes" === "Show".
		// saved value needs to be reversed / remapped, off == on, vice versa
		$is_slider_remove         = in_array( $module_slug, $this->get_modules( 'slider' ) ) && in_array( $saved_field_name, array( 'remove_inner_shadow', 'hide_content_on_mobile', 'hide_cta_on_mobile' ) );
		$is_post_nav_hides        = 'et_pb_post_nav' === $module_slug && in_array( $saved_field_name, array( 'hide_prev', 'hide_next' ) );
		$is_pricing_tables_remove = 'et_pb_pricing_tables' === $module_slug && in_array( $saved_field_name, array( 'remove_featured_drop_shadow' ) );
		$is_search_hide           = 'et_pb_search' === $module_slug && in_array( $saved_field_name, array( 'hide_button' ) );
		$is_sidebar_remove        = 'et_pb_sidebar' === $module_slug && in_array( $saved_field_name, array( 'remove_border' ) );
		$is_image_sticky          = 'et_pb_image' === $module_slug && in_array( $saved_field_name, array( 'sticky' ) );

		if ( $is_slider_remove || $is_post_nav_hides || $is_pricing_tables_remove || $is_search_hide || $is_sidebar_remove || $is_image_sticky ) {
			return $this->migrate_remap_value( $current_value );
		}

		// Blurb's max_width needs to be removed, since older max_width is new image_max_width
		if ( in_array( $module_slug, $this->get_modules( 'reset_max_width' ) ) && 'max_width' === $field_name && 'max_width' === $saved_field_name ) {
			return '';
		}

		return $current_value;
	}
}

return new ET_Builder_Module_Settings_Migration_OptionsHarmony();

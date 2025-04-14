<?php
/**
 * Methods needed for the Icon Manager QF.
 *
 * @package Divi
 * @subpackage Builder
 * @since ?
 */

/**
 * Methods needed for the Icon Manager QF.
 *
 * @since ?
 */

if ( ! function_exists( 'et_pb_maybe_fa_font_icon' ) ) {
	/**
	 * Checking if the passed icon value is Font Awesome type.
	 *
	 * @since ?
	 *
	 * @param string $icon_data extended icon value from shortcode or presets.
	 *
	 * @return bool
	 */
	function et_pb_maybe_fa_font_icon( $icon_data ) {
		return et_pb_maybe_extended_icon( $icon_data ) && 'fa' === et_pb_get_extended_font_icon_type( $icon_data );
	}
}

if ( ! function_exists( 'et_pb_maybe_old_single_utf_divi_icon' ) ) {
	/**
	 * Checking if the passed icon value is old Divi single unicode value.
	 *
	 * @since ?
	 *
	 * @param string $icon_value icon value from shortcode or presets.
	 *
	 * @return bool
	 */
	function et_pb_maybe_old_single_utf_divi_icon( $icon_value ) {
		// Attempt to convert the icon value to ISO-8859-1.
		$icon_maybe_convert = function_exists( 'mb_convert_encoding' ) ? mb_convert_encoding( $icon_value, 'ISO-8859-1', 'UTF-8' ) : $icon_value;

		return 1 === strlen( $icon_maybe_convert ) && in_array( $icon_value, et_pb_get_decoded_divi_icons(), true );
	}
}

if ( ! function_exists( 'et_pb_maybe_old_divi_font_icon' ) ) {
	/**
	 * Checking if the passed icon value is in the old Divi icon format.
	 *
	 * @since ?
	 *
	 * @param string $icon_value icon value from shortcode or presets.
	 * @param bool   $check_single_utf_value will check for the old Divi single unicode icon format.
	 *
	 * @return bool
	 */
	function et_pb_maybe_old_divi_font_icon( $icon_value, $check_single_utf_value = true ) {
		return 1 === preg_match( '/^%%[0-9]{1,3}%%$/', trim( $icon_value ) ) || ( $check_single_utf_value && et_pb_maybe_old_single_utf_divi_icon( $icon_value ) );
	}
}
if ( ! function_exists( 'et_pb_maybe_divi_font_icon' ) ) {
	/**
	 * Checking if the passed icon value is Divi type.
	 *
	 * @since ?
	 *
	 * @param string $icon_data extended icon value from shortcode or presets.
	 *
	 * @return bool
	 */
	function et_pb_maybe_divi_font_icon( $icon_data ) {
		return ( et_pb_maybe_extended_icon( $icon_data ) && 'divi' === et_pb_get_extended_font_icon_type( $icon_data ) ) || et_pb_maybe_old_divi_font_icon( $icon_data );
	}
}
if ( ! function_exists( 'et_pb_get_extended_font_icon_type' ) ) {
	/**
	 * Get icon type ('fa' or 'divi').
	 *
	 * @since ?
	 *
	 * @param string $icon_data icon data.
	 *
	 * @return string may be either 'fa' or 'divi'.
	 */
	function et_pb_get_extended_font_icon_type( $icon_data ) {
		return esc_attr( et_pb_get_extended_icon_data( $icon_data, 'icon_type' ) );
	}
}
if ( ! function_exists( 'et_pb_get_extended_font_icon_value' ) ) {
	/**
	 * Get icon unicode value.
	 *
	 * @since ?
	 *
	 * @param string $icon_data icon data.
	 * @param bool   $do_decode return value with decoding.
	 * @param bool   $return_raw_unicode return raw icon unicode value.
	 *
	 * @return string
	 */
	function et_pb_get_extended_font_icon_value( $icon_data, $do_decode = false, $return_raw_unicode = false ) {
		if ( ! et_pb_maybe_extended_icon( $icon_data ) ) {
			$icon_data = et_pb_build_extended_font_icon_value( $icon_data );
		}

		$icon_value = et_pb_get_extended_icon_data( $icon_data, 'icon_value' );

		$icons_list = et_pb_get_decoded_extended_font_icon_symbols();
		foreach ( $icons_list as $font_icon ) {
			if ( ! empty( $icon_value ) && ( $font_icon['unicode'] === $icon_value || str_replace( '&amp;', '&', $icon_value ) === $font_icon['unicode'] || et_pb_get_decode_extended_font_icon_symbol( $icon_value ) === $font_icon['decoded_unicode'] || $font_icon['decoded_unicode'] === $icon_value ) ) {
				if ( $return_raw_unicode ) {
					return $font_icon['unicode'];
				}
				return $do_decode ? et_pb_get_decode_extended_font_icon_symbol( $icon_value ) : $icon_value;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'et_pb_check_and_convert_icon_raw_value' ) ) {
	/**
	 * It checks available of icon in the defined icon list by Unicode and returns
	 * raw non-decoded icon value.
	 *
	 * @since ?
	 *
	 * @param string $icon icon value saved in the shortcode.
	 *
	 * @return string
	 */
	function et_pb_check_and_convert_icon_raw_value( $icon ) {
		$icon_type      = et_pb_get_extended_icon_data( $icon, 'icon_type' );
		$font_weight    = et_pb_get_extended_icon_data( $icon, 'font_weight' );
		$icon_raw_value = et_pb_get_extended_font_icon_value( $icon, false, true );

		if ( ! empty( $icon_raw_value ) && in_array( $icon_type, array( 'fa', 'divi' ), true ) && in_array( (int) $font_weight, array( 400, 900 ), true ) ) {
			return et_pb_build_extended_font_icon_value( $icon_raw_value, $icon_type, $font_weight );
		}
		return '';
	}
}

if ( ! function_exists( 'et_pb_build_extended_font_icon_value' ) ) {
	/**
	 * Create extended font icon value.
	 *
	 * @since ?
	 *
	 * @param string $icon_value      icon value (if passed icon value in the old divi format it will be convertetd to unicode value).
	 * @param string $icon_type type of icon (divi is default).
	 * @param string $font_weight type of icon (400 is default).
	 * @param bool   $decode_amp do or not ampersand decoding (&amp; -> &).
	 * @return string
	 */
	function et_pb_build_extended_font_icon_value( $icon_value, $icon_type = null, $font_weight = null, $decode_amp = false ) {

		if ( et_pb_maybe_extended_icon( $icon_value ) ) {
			return $icon_value;
		}

		if ( ! $icon_type ) {
			$icon_type = 'divi';
		}

		if ( ! $font_weight ) {
			$font_weight = et_pb_get_normal_font_weight_value();
		}

		if ( et_pb_maybe_old_divi_font_icon( $icon_value, false ) ) {
			// the font icon value is saved in the following format: %%index_number%%.
			$icon_index   = (int) str_replace( '%', '', $icon_value );
			$icon_symbols = et_pb_get_font_icon_symbols();
			$icon_value   = isset( $icon_symbols[ $icon_index ] ) ? $icon_symbols[ $icon_index ] : '';
		} elseif ( et_pb_maybe_old_single_utf_divi_icon( $icon_value ) ) {
			$index      = array_search( $icon_value, et_pb_get_decoded_divi_icons(), true );
			$font_icons = et_pb_get_extended_font_icon_symbols();
			if ( $index && ! empty( $font_icons[ $index ] ) && ! empty( $font_icons[ $index ]['unicode'] ) ) {
				$icon_value = $font_icons[ $index ]['unicode'];
			}
		}

		if ( $decode_amp ) {
			$icon_value = str_replace( '&amp;', '&', $icon_value );
		}

		return $icon_value . '||' . $icon_type . '||' . $font_weight;
	}
}

if ( ! function_exists( 'et_pb_get_extended_icon_data' ) ) {
	/**
	 * Depending on the $icon_data_type, returns string unicode icon value or icon type.
	 *
	 * @since ?
	 *
	 * @param string $icon_data      the string value of the icon.
	 * @param string $icon_data_type could be either 'icon_value' or 'icon_type'.
	 *
	 * @return string
	 */
	function et_pb_get_extended_icon_data( $icon_data, $icon_data_type ) {
		if ( et_pb_maybe_extended_icon( $icon_data ) ) {
			$extended_icon_data = explode( '||', $icon_data );
			if ( ! empty( $extended_icon_data ) ) {
				switch ( $icon_data_type ) {
					case 'icon_value':
						return ( ! empty( $extended_icon_data[0] ) ) ? $extended_icon_data[0] : false;
					case 'icon_type':
						return ( ! empty( $extended_icon_data[1] ) ) ? $extended_icon_data[1] : false;
					case 'font_weight':
						return ( ! empty( $extended_icon_data[2] ) ) ? $extended_icon_data[2] : false;
				}
			}
		}
	}
}

if ( ! function_exists( 'et_pb_maybe_extended_icon' ) ) {
	/**
	 * Checking if the passed icon value is extended icon type ( like a '&#x30;||divi' ).
	 *
	 * @since ?
	 *
	 * @param string $icon icon data.
	 *
	 * @return bool
	 */
	function et_pb_maybe_extended_icon( $icon ) {
		return ! empty( $icon ) && false !== strpos( $icon, '||' );
	}
}

if ( ! function_exists( 'et_pb_get_icon_font_family' ) ) {
	/**
	 * Return CSS font-family property for icon.
	 *
	 * @since ?
	 *
	 * @param string $icon icon data.
	 *
	 * @return string
	 */
	function et_pb_get_icon_font_family( $icon ) {
		return et_pb_maybe_fa_font_icon( $icon ) ? 'FontAwesome' : 'ETmodules';
	}
}

if ( ! function_exists( 'et_pb_get_normal_font_weight_value' ) ) {
	/**
	 * Return CSS `normal` font-weight value.
	 *
	 * @since ?
	 *
	 * @return int
	 */
	function et_pb_get_normal_font_weight_value() {
		return 400;
	}
}

if ( ! function_exists( 'et_pb_get_icon_font_weight' ) ) {
	/**
	 * Return CSS font-weight property for icon.
	 *
	 * @since ?
	 *
	 * @param string $icon icon data.
	 *
	 * @return int
	 */
	function et_pb_get_icon_font_weight( $icon ) {
		return 900 === (int) et_pb_get_extended_icon_data( $icon, 'font_weight' ) ? 900 : et_pb_get_normal_font_weight_value();
	}
}

if ( ! function_exists( 'et_pb_extended_process_font_icon' ) ) {
	/**
	 * Return CSS font-family property for icon.
	 *
	 * @since ?
	 *
	 * @param string $icon icon data.
	 *
	 * @return string
	 */
	function et_pb_extended_process_font_icon( $icon ) {
		return et_pb_maybe_extended_icon( $icon ) ? et_pb_get_extended_font_icon_value( $icon, true ) : esc_attr( et_pb_process_font_icon( $icon ) );
	}
}

if ( ! function_exists( 'et_pb_get_extended_icon_value_for_css' ) ) {
	/**
	 * Getting icon's unicode value from passed icon data and converting the icon value to the CSS-value
	 * for the CSS property 'content:'( for example, '&#x30;||divi' will be converted to '"\30"' ).
	 *
	 * @since ?
	 *
	 * @param string $icon_data icon_data.
	 *
	 * @return string
	 */
	function et_pb_get_extended_icon_value_for_css( $icon_data ) {
		$icon_value = et_pb_get_extended_font_icon_value( $icon_data );
		$icon_value = strtolower( str_replace( array( '&amp;#x', '&#x' ), '\\', $icon_value ) );
		$icon_value = str_replace( ';', '', $icon_value );

		return '"' . $icon_value . '"';
	}
}
if ( ! function_exists( 'et_pb_get_social_net_fa_icons' ) ) {
	/**
	 * List of Social Networks Font Awesome icons.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	function et_pb_get_social_net_fa_icons() {
		return array(
			'amazon',
			'bandcamp',
			'telegram',
			'bitbucket',
			'behance',
			'buffer',
			'codepen',
			'deviantart',
			'flipboard',
			'foursquare',
			'github',
			'goodreads',
			'google',
			'houzz',
			'itunes',
			'last_fm',
			'line',
			'medium',
			'meetup',
			'odnoklassniki',
			'patreon',
			'periscope',
			'quora',
			'researchgate',
			'reddit',
			'snapchat',
			'soundcloud',
			'spotify',
			'steam',
			'tripadvisor',
			'tiktok',
			'twitch',
			'vk',
			'weibo',
			'whatsapp',
			'xing',
			'yelp',
		);
	}
}
if ( ! function_exists( 'et_pb_get_social_net_divi_icons' ) ) {
	/**
	 * List of Social Networks used in the icons_base_social.scss file.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	function et_pb_get_social_net_divi_icons() {
		return array(
			'pinterest',
			'linkedin',
			'tumblr',
			'skype',
			'flikr',
			'myspace',
			'dribbble',
			'youtube',
			'vimeo',
		);
	}
}
if ( ! function_exists( 'et_pb_get_decode_extended_font_icon_symbol' ) ) :
	/**
	 * Returns decoded font icon value.
	 *
	 * @since ?
	 *
	 * @param string $font_icon_value font icon value.
	 *
	 * @return array
	 */
	function et_pb_get_decode_extended_font_icon_symbol( $font_icon_value ) {

		return html_entity_decode( $font_icon_value, ENT_QUOTES, 'UTF-8' );
	}
endif;
if ( ! function_exists( 'et_pb_get_decoded_extended_font_icon_symbols' ) ) :
	/**
	 * Returns full list of all icons with decoded utf values.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	function et_pb_get_decoded_extended_font_icon_symbols() {
		$cache_key = 'et_pb_get_decoded_extended_font_icon_symbols';
		if ( ! et_core_cache_has( $cache_key ) ) {
			$font_icons = et_pb_get_extended_font_icon_symbols();
			foreach ( $font_icons as &$font_icon ) {
				$font_icon['decoded_unicode'] = et_pb_get_decode_extended_font_icon_symbol( $font_icon['unicode'] );
			}
			et_core_cache_set( $cache_key, $font_icons );
		} else {
			$font_icons = et_core_cache_get( $cache_key );
		}
		return $font_icons;
	}
endif;

if ( ! function_exists( 'et_pb_get_decoded_divi_icons' ) ) :
	/**
	 * Returns Divi icons decoded utf values.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	function et_pb_get_decoded_divi_icons() {
		$cache_key = 'et_pb_get_decoded_divi_icons';
		if ( ! et_core_cache_has( $cache_key ) ) {
			$font_icons         = et_pb_get_extended_font_icon_symbols();
			$decoded_divi_icons = array();
			foreach ( $font_icons as $font_icon ) {
				if ( ! $font_icon['is_divi_icon'] ) {
					break;
				}
				$decoded_divi_icons[] = et_pb_get_decode_extended_font_icon_symbol( $font_icon['unicode'] );
			}
			et_core_cache_set( $cache_key, $decoded_divi_icons );
		} else {
			$decoded_divi_icons = et_core_cache_get( $cache_key );
		}
		return $decoded_divi_icons;
	}
endif;


if ( ! function_exists( 'et_pb_get_font_icon_field_names' ) ) :
	/**
	 * Returns the list of font icon fields with `select_icon` option.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	function et_pb_get_font_icon_field_names() {
		return array(
			'font_icon',
			'button_icon',
			'hover_icon',
			'scroll_down_icon',
			'open_toggle_icon',
			'toggle_icon',
			'button_one_icon',
			'button_two_icon',
		);
	}

endif;

if ( ! function_exists( 'et_pb_get_all_font_icon_option_names_string' ) ) :
	/**
	 * Returns string with names of all font_icon fields in all responsive, sticky, hover modes separated by '|'.
	 *
	 * @since ?
	 *
	 * @return string
	 */
	function et_pb_get_all_font_icon_option_names_string() {
		$cache_key = 'et_pb_get_font_icon_option_names';
		if ( ! et_core_cache_has( $cache_key ) ) {
			$font_icon_names = array();
			foreach ( et_pb_get_font_icon_field_names() as $font_icon_field ) {
				$font_icon_names[] = $font_icon_field;
				$font_icon_names[] = $font_icon_field . '_tablet';
				$font_icon_names[] = $font_icon_field . '_phone';
				$font_icon_names[] = $font_icon_field . et_pb_hover_options()->get_suffix();
				$font_icon_names[] = $font_icon_field . et_pb_sticky_options()->get_suffix();
			}
			$font_icon_option_names = implode( '|', $font_icon_names );
			et_core_cache_set( $cache_key, $font_icon_option_names );
		} else {
			$font_icon_option_names = et_core_cache_get( $cache_key );
		}

		return $font_icon_option_names;
	}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_names_regex' ) ) :
	/**
	 * Returns the regex that will be used to check containing of the font icon option in the content.
	 *
	 * @since ?
	 *
	 * @param bool $maybe_fa_icon_type define what kind of font icon we need o get regex.
	 * @param bool $use_only_defined_icon_fields values will be searched only in certain fields that can contain icon values (see: `et_pb_get_font_icon_field_names()`).
	 *
	 * @return string
	 */
	function et_pb_get_font_icon_names_regex( $maybe_fa_icon_type = false, $use_only_defined_icon_fields = false ) {
		$icon_type = $maybe_fa_icon_type ? 'fa' : 'divi';
		return ! $use_only_defined_icon_fields ? '/\=\"([^"]*)\|\|(' . $icon_type . ')\|\|(400|900)\"/mi' : '/(' . et_pb_get_all_font_icon_option_names_string() . ')\=\"([^"]*)\|\|(' . $icon_type . ')\|\|(400|900)\"/mi';
	}
endif;

if ( ! function_exists( 'et_pb_check_if_post_contains_fa_font_icon' ) ) :
	/**
	 * Check if post content contains FontAwesome icon.
	 *
	 * @since ?
	 *
	 * @param string $content post's content.
	 *
	 * @return bool
	 */
	function et_pb_check_if_post_contains_fa_font_icon( $content ) {
		return ! empty( preg_match_all( et_pb_get_font_icon_names_regex( true ), $content ) );
	}
endif;

if ( ! function_exists( 'et_pb_check_if_post_contains_divi_font_icon' ) ) :
	/**
	 * Check if post content contains Divi icon.
	 *
	 * @since ?
	 *
	 * @param string $content post's content.
	 * @param bool   $use_only_defined_icon_fields values will be searched only in certain fields that can contain icon values (see: `et_pb_get_font_icon_field_names()`).
	 *
	 * @return bool
	 */
	function et_pb_check_if_post_contains_divi_font_icon( $content, $use_only_defined_icon_fields = false ) {
		// Check the non-extended icon value.
		$old_divi_icon_regex         = ! $use_only_defined_icon_fields ? '/\=\"%%([^"]*)%%\"/mi' : '/(' . et_pb_get_all_font_icon_option_names_string() . ')\=\"%%([^"]*)%%\"/mi';
		$single_char_divi_icon_regex = '/(' . et_pb_get_all_font_icon_option_names_string() . ')\=\"[\s\S]\"/miu';

		return ! empty( preg_match_all( et_pb_get_font_icon_names_regex(), $content ) ) || ! empty( preg_match_all( $old_divi_icon_regex, $content ) ) || ! empty( preg_match_all( $single_char_divi_icon_regex, $content ) );
	}
endif;

if ( ! function_exists( 'et_pb_check_if_post_contains_network_with_fa_icon' ) ) :
	/**
	 * Check if post content contains a social network attribute with FA icon.
	 *
	 * @param string $content to search for attribute in.
	 *
	 * @since ?
	 *
	 * @return bool
	 */
	function et_pb_check_if_post_contains_network_with_fa_icon( $content ) {
		$regex = '/social_network\=\"(' . implode( '|', et_pb_get_social_net_fa_icons() ) . ')\"/mi';
		return ! empty( preg_match_all( $regex, $content ) );
	}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_modules' ) ) :
	/**
	 * Returns the list of Divi modules with `select_icon` option.
	 *
	 * @param string $group certain group of modules .
	 *
	 * @since ?
	 *
	 * @return array|null
	 */
	function et_pb_get_font_icon_modules( $group = false ) {

		$font_icon_modules_used_in_migrations = array(
			'button'  => array(
				'et_pb_button',
				'et_pb_comments',
				'et_pb_contact_form',
				'et_pb_cta',
				'et_pb_fullwidth_header',
				'et_pb_fullwidth_post_slider',
				'et_pb_login',
				'et_pb_post_slider',
				'et_pb_pricing_tables',
				'et_pb_pricing_table',
				'et_pb_signup',
				'et_pb_slider',
				'et_pb_slide',
				'et_pb_wc_add_to_cart',
				'et_pb_wc_cart_notice',
			),
			'blurb'   => array(
				'et_pb_blurb',
			),
			'overlay' => array(
				'et_pb_blog',
				'et_pb_filterable_portfolio',
				'et_pb_fullwidth_image',
				'et_pb_fullwidth_portfolio',
				'et_pb_gallery',
				'et_pb_image',
				'et_pb_portfolio',
				'et_pb_shop',
				'et_pb_wc_related_products',
				'et_pb_wc_upsells',
			),
			'toggle'  => array(
				'et_pb_toggle',
			),
		);

		$other_select_icon_modules = array(
			'select_icon' => array(
				'et_pb_icon',
				'et_pb_video',
				'et_pb_video_slider',
				'et_pb_video_slider_item',
				'et_pb_testimonial',
				'et_pb_accordion',
				'et_pb_accordion_item',
			),
		);

		if ( false === $group ) {
			// Return all modules that use select_icon.
			$all_modules             = array();
			$all_select_icon_modules = array_merge( $font_icon_modules_used_in_migrations, $other_select_icon_modules );
			foreach ( $all_select_icon_modules as $select_icon_module ) {
				$all_modules = array_merge( $all_modules, $select_icon_module );
			}
			return $all_modules;
		} elseif ( isset( $font_icon_modules_used_in_migrations[ $group ] ) ) {
			// Return certain modules list by $group flag.
			return $font_icon_modules_used_in_migrations[ $group ];
		}

		return null;
	}
endif;

if ( ! function_exists( 'et_pb_get_extended_font_icon_symbols' ) ) :
	/**
	 * Returns full list of all icons used in the Divi with ['search_terms'],
	 * unicode icon value ['unicode'], icon name ['name']
	 * groups in which the icon is included ['styles'],
	 * bool flag which determined is this icon a divi icon or FontAwesome icon['is_divi_icon'].
	 *
	 * @since ?
	 *
	 * @return array
	 */
	function et_pb_get_extended_font_icon_symbols() {
		$cache_key = 'et_pb_get_extended_font_icon_symbols';
		if ( ! et_core_cache_has( $cache_key ) ) {
			$full_icons_list_path = __DIR__ . '/full_icons_list.json';
			if ( file_exists( $full_icons_list_path ) ) {
				// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Can't use wp_remote_get() for local file
				$icons_data = json_decode( file_get_contents( $full_icons_list_path ), true );
				// phpcs:enable
				if ( JSON_ERROR_NONE === json_last_error() ) {
					et_core_cache_set( $cache_key, $icons_data );
					return $icons_data;
				}
			}
			et_wrong( 'Problem with loading the icon data on this path: ' . $full_icons_list_path );
		} else {
			return et_core_cache_get( $cache_key );
		}
	}
endif;

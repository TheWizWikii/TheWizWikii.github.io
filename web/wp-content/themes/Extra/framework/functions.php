<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'ET_TAXONOMY_META_OPTION_KEY', "et_taxonomy_meta" );

require dirname( __FILE__ ) . '/widgets.php';

if ( is_admin() ) {
	require dirname( __FILE__ ) . '/admin/admin.php';
}

function et_framework_setup() {
	if ( current_theme_supports( 'et-post-formats' ) ) {
		require dirname( __FILE__ ) . '/post-formats.php';
	}

	if ( is_admin() ) {
		if ( current_theme_supports( 'et-post-formats' ) ) {
			require dirname( __FILE__ ) . '/admin/post-formats.php';
		}
	}
}

add_action( 'after_setup_theme', 'et_framework_setup', 11 );

function et_extra_get_framework_directory_uri() {
	$template_template_dir = get_template_directory_uri();
	$framework_dir = apply_filters( 'et_framework_directory', 'framework' );
	return esc_url( $template_template_dir . '/' . $framework_dir );
}

function et_load_scripts_styles(){
	$theme_version = et_get_theme_version();
	$framework_template_dir = et_extra_get_framework_directory_uri();

	wp_register_script( 'hash-persistance', $framework_template_dir . '/scripts/jquery.hash-persistance.min.js', array( 'jquery' ), $theme_version, true );
}

add_action( 'wp_enqueue_scripts', 'et_load_scripts_styles' );

/**
 * Get Theme Version
 */
if ( ! function_exists( 'et_get_theme_version' ) ) :

	function et_get_theme_version() {
		$theme = wp_get_theme();

		// Get parent theme info if a child theme is used.
		if ( is_child_theme() ) {
			$theme = wp_get_theme( $theme->parent_theme );
		}

		return $theme->display( 'Version' );
	}

endif;

/**
 * Get the author post link
 *
 * @return string     The author post link
 */

if ( ! function_exists( 'et_get_the_author_posts_link' ) ) :

	function et_get_the_author_posts_link(){
		global $authordata, $themename;

		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			esc_attr( sprintf( et_get_safe_localization( __( 'Posts by %s', $themename ) ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}

endif;

/**
 * Get Post Info Meta
 *
 * @param  array     $postinfo   ...
 * @param  ...       $...        ...
 * @return array     $postinfo   Structured postinfo meta
 */
if ( ! function_exists( 'et_postinfo_meta' ) ) :

	function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
		global $themename;

		$postinfo_meta = '';

		if ( in_array( 'author', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__( 'by', $themename ) . ' ' . et_get_the_author_posts_link() . ' | ';

		if ( in_array( 'date', $postinfo ) )
		$postinfo_meta .= get_the_time( $date_format ) . ' | ';

		if ( in_array( 'categories', $postinfo ) )
		$postinfo_meta .= get_the_category_list( ', ' ) . ' | ';

		if ( in_array( 'comments', $postinfo ) )
		$postinfo_meta .= et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

		echo $postinfo_meta;
	}

endif;

/**
 * Deprecated! Create post excerpt of a given length.
 *
 * @deprecated Use {@see truncate_post()} instead.
 *
 * @param  int       $amount   amount of characters to truncate to
 * @param  bool      $echo     whether to echo or return the result. Default: true
 * @param  object    $post     the post in which to create an excerpt for, if not passed global $post is used.
 */
if ( ! function_exists( 'et_truncate_post' ) ):
function et_truncate_post( $amount, $echo = true, $post = '' ) {
	return truncate_post( $amount, $echo, $post );
}
endif;

if ( ! function_exists( 'et_wp_trim_words' ) ):

	function et_wp_trim_words( $text, $num_words = 55, $more = null ) {
		if ( null === $more )
		$more = esc_html__( '&hellip;' );
		$original_text = $text;
		// Completely remove icons so that unicode hex entities representing the icons do not get included in words.
		$text = preg_replace( '/<span class="et-pb-icon .*<\/span>/', '', $text );
		$text = wp_strip_all_tags( $text );

		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';

		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
	}

endif;

if ( ! function_exists( 'et_get_current_url' ) ) :

	function et_get_current_url() {
		return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

endif;

if ( ! function_exists( 'et_options_stored_in_one_row' ) ):

	function et_options_stored_in_one_row(){
		global $et_store_options_in_one_row;

		return isset( $et_store_options_in_one_row ) ? (bool) $et_store_options_in_one_row : false;
	}

endif;

/**
 * Transforms an array of posts, pages, post_tags or categories ids
 * into corresponding "objects" ids, if WPML plugin is installed
 *
 * @param array $ids_array Posts, pages, post_tags or categories ids.
 * @param string $type "Object" type.
 * @return array IDs.
 */
if ( ! function_exists( 'et_generate_wpml_ids' ) ):

	function et_generate_wpml_ids( $ids_array, $type ) {
		if ( function_exists( 'icl_object_id' ) ) {
			$wpml_ids = array();
			foreach ( $ids_array as $id ) {
				$translated_id = icl_object_id( $id, $type, false );
				if ( ! is_null( $translated_id ) ) $wpml_ids[] = $translated_id;
			}
			$ids_array = $wpml_ids;
		}

		return array_map( 'intval', $ids_array );
	}

endif;

if ( !function_exists( 'et_init_options' ) ):

	function et_init_options() {
		global $et_theme_options, $shortname, $et_theme_options_defaults;

		if ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) ) {
				$et_theme_options = get_option( $et_theme_options_name );
				if ( empty( $et_theme_options ) ) {
					update_option( $et_theme_options_name, $et_theme_options_defaults );
				}
			}
		}
	}

endif;
add_action( 'et_theme_init_first', 'et_init_options' );
add_action( 'et_theme_init_upgrade', 'et_init_options' );

if ( ! function_exists( 'et_list_pings' ) ) :
	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
	<?php }
endif;

if ( !function_exists( 'et_get_childmost_taxonomy_meta' ) ):

	function et_get_childmost_taxonomy_meta( $term_id, $meta_key, $single = false, $default = '',  $taxonomy = 'category' ) {
		global $et_taxonomy_meta;

		if ( !$term = get_term( $term_id, $taxonomy ) ) {
			return $default;
		}

		$result = et_get_taxonomy_meta( $term_id, $meta_key, $single );

		if ( empty( $result ) && isset( $term->parent ) && $term->parent !== 0 ) {
			return et_get_childmost_taxonomy_meta( $term->parent, $meta_key, $single, $default, $taxonomy );
		}

		if ( !empty( $result ) ) {
			return $result;
		}

		return $default;
	}

endif;

if ( !function_exists( 'et_get_taxonomy_meta' ) ):

	function et_get_taxonomy_meta( $term_id, $meta_key = '', $single = false ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		if ( !isset( $et_taxonomy_meta[ $term_id ] ) ) {
			$et_taxonomy_meta[ $term_id ] = array();
		}

		if ( empty( $meta_key ) ) {
			return $et_taxonomy_meta[ $term_id ];
		}

		$result = $single ? '' : array();

		foreach ( $et_taxonomy_meta[ $term_id ] as $tax_meta_key => $tax_meta ) {
			foreach ( $tax_meta as $_meta_key => $_meta_value ) {
				if ( $_meta_key === $meta_key ) {
					if ( $single ) {
						$result = $_meta_value;
						break;
					}
					$result[] = $_meta_value;
				}
			}
		}

		return $result;
	}

endif;

if ( !function_exists( 'et_update_taxonomy_meta' ) ):

	function et_update_taxonomy_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		if ( !isset( $et_taxonomy_meta[ $term_id ] ) ) {
			$et_taxonomy_meta[ $term_id ] = array();
		}

		$meta_key_found = false;
		foreach ( $et_taxonomy_meta[ $term_id ] as $tax_meta_key => $tax_meta ) {
			foreach ( $tax_meta as $_meta_key => $_meta_value ) {
				if ( $meta_key === $_meta_key ) {
					$meta_key_found = true;
					if ( empty( $prev_value ) ) {
						$et_taxonomy_meta[ $term_id ][ $tax_meta_key ][ $_meta_key  ] = $meta_value;
					} else {
						if ( $prev_value === $_meta_value  ) {
							$et_taxonomy_meta[ $term_id ][ $tax_meta_key ][ $_meta_key  ] = $meta_value;
						}
					}
				}
			}
		}

		if ( !$meta_key_found ) {
			et_add_taxonomy_meta( $term_id, $meta_key, $meta_value );
		}

		_et_update_taxonomy_meta();
	}

endif;

if ( !function_exists( 'et_add_taxonomy_meta' ) ):

	function et_add_taxonomy_meta( $term_id, $meta_key, $meta_value ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		if ( !isset( $et_taxonomy_meta[ $term_id ] ) ) {
			$et_taxonomy_meta[ $term_id ] = array();
		}

		$et_taxonomy_meta[ $term_id ][] = array( $meta_key => $meta_value );

		_et_update_taxonomy_meta();
	}

endif;

if ( !function_exists( 'et_delete_taxonomy_meta' ) ):

	function et_delete_taxonomy_meta( $term_id, $meta_key, $meta_value = '' ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		foreach ( $et_taxonomy_meta[ $term_id ] as $tax_meta_key => $tax_meta ) {
			foreach ( $tax_meta as $_meta_key => $_meta_value ) {
				if ( $meta_key === $_meta_key ) {
					if ( empty( $meta_value ) ) {
						unset( $et_taxonomy_meta[ $term_id ][ $tax_meta_key ] );
					} else {
						if ( $meta_value === $_meta_value  ) {
							unset( $et_taxonomy_meta[ $term_id ][ $tax_meta_key ] );
						}
					}
				}
			}
		}

		_et_update_taxonomy_meta();
	}

endif;

/*
 * Internal use helper function to get and populate the global $et_taxonomy_meta
 *
 * This function is hooked into and called during init hook
 */

function _et_get_taxonomy_meta() {
	global $et_taxonomy_meta;

	if ( !isset( $et_taxonomy_meta ) ) {
		$et_taxonomy_meta = maybe_unserialize( get_option( ET_TAXONOMY_META_OPTION_KEY, null ) );
		if ( null === $et_taxonomy_meta ) {
			update_option( ET_TAXONOMY_META_OPTION_KEY, array() );
			$et_taxonomy_meta = array();
		}
	}
}

add_action( 'init', '_et_get_taxonomy_meta', 9 );

/*
 * Internal use helper function to update and re-populate the global $et_taxonomy_meta
 */

function _et_update_taxonomy_meta() {
	global $et_taxonomy_meta;
	update_option( ET_TAXONOMY_META_OPTION_KEY, $et_taxonomy_meta );
}

function _et_register_sidebar( $args ) {
	global $themename;

	$default_args = array(
		'name'          => '',
		'id'            => '',
		'before_widget' => '<div id="%1$s" class="et_pb_widget %2$s">',
		'after_widget'  => '</div> <!-- end .et_pb_widget -->',
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
	);

	$args = wp_parse_args( $args, $default_args );

	if ( empty( $args['name'] ) ) {
		$version = sprintf( '%s, Theme: %s', et_get_theme_version(), $themename );
		_doing_it_wrong( __FUNCTION__, "'name' argument required", $version );
		return;
	}

	if ( empty( $args['id'] ) ) {
		$args['id'] = sanitize_title_with_dashes( $args['name'] );
		if ( strpos( $args['id'], '-sidebar' ) !== false ) {
			$args['id'] = 'sidebar-' . str_replace( '-sidebar', '', $args['id'] );
		}
	}

	register_sidebar( $args );
}

function et_register_widget_areas() {
	if ( !current_theme_supports( 'et_widget_areas' ) ) {
		return;
	}

	$et_widget_areas = get_option( 'et_widget_areas' );

	if ( !empty( $et_widget_areas ) ) {
		foreach ( $et_widget_areas['areas'] as $id => $name ) {
			_et_register_sidebar( array(
				'id'   => $id,
				'name' => $name,
			) );

		}
	}
}

add_action( 'widgets_init', 'et_register_widget_areas', 11 );

function et_add_wp_version( $classes ) {
	global $wp_version;

	$is_admin_body_class = 'admin_body_class' === current_filter();

	// add 'et-wp-pre-3_8' class if the current WordPress version is less than 3.8
	if ( version_compare( $wp_version, '3.7.2', '<=' ) ) {
		if ( 'body_class' === current_filter() ) {
			$classes[] = 'et-wp-pre-3_8';
		} else {
			$classes .= ' et-wp-pre-3_8';
		}
	} else if ( $is_admin_body_class ) {
		$classes .= ' et-wp-after-3_8';
	}

	if ( $is_admin_body_class ) {
		$classes = ltrim( $classes );
	}

	return $classes;
}

add_filter( 'body_class', 'et_add_wp_version' );
add_filter( 'admin_body_class', 'et_add_wp_version' );

function et_register_customizer_section( $wp_customize, $settings, $section, $section_options = '', $panel = '' ) {
	global $shortname;

	if ( empty( $settings ) ) {
		return;
	}

	$section_args = wp_parse_args( $section_options, array(
		'title'    => $section,
		'priority' => 10,
	) );

	if ( !empty( $panel ) ) {
		$section_args['panel'] = $panel;
	}

	$wp_customize->add_section( $section, $section_args );

	foreach ($settings as $option_key => $options) {

		if ( !is_array( $options ) ) {
			$label = $options;
			$options = array();
			$options['label'] = $label;
		}

		$default_options = array(
			'setting_type'   => 'option',
			'type'           => 'text',
			'transport'      => 'postMessage',
			'capability'     => 'edit_theme_options',
			'default'        => '',
			'description'    => '',
			'choices'        => array(),
			'priority'       => 10,
			'global_option'  => false,
			'theme_supports' => '',
		);

		$options = wp_parse_args( $options, $default_options );

		$option_key = true == $options['global_option'] ? $option_key : sprintf( 'et_%s[%s]', $shortname, $option_key );

		switch ( $options['type'] ) {
			case 'dropdown-font-styles':
				$sanitize_callback = 'et_sanitize_font_style';
				break;

			case 'dropdown-fonts':
				$sanitize_callback = 'et_sanitize_font_choices';
				break;

			case 'color':
				$sanitize_callback = 'sanitize_hex_color';
				break;

			case 'et_coloralpha':
				$sanitize_callback = 'et_sanitize_alpha_color';
				break;

			case 'checkbox':
				$sanitize_callback = 'wp_validate_boolean';
				break;

			case 'range':
				if ( isset( $options['input_attrs']['step'] ) && $options['input_attrs']['step'] < 1 ) {
					$sanitize_callback = 'et_sanitize_float_number';
				} else {
					$sanitize_callback = 'et_sanitize_int_number';
				}
				break;
			default:
				$sanitize_callback = '';
				break;
		}

		$wp_customize->add_setting( $option_key, array(
			'default'           => $options['default'],
			'type'              => $options['setting_type'],
			'capability'        => $options['capability'],
			'transport'         => $options['transport'],
			'theme_supports'    => $options['theme_supports'],
			'sanitize_callback' => $sanitize_callback,
		) );

		$control_options = array(
			'label'       => $options['label'],
			'section'     => $section,
			'description' => $options['description'],
			'settings'    => $option_key,
			'type'        => $options['type'],
			'priority'    => $options['priority'],
		);

		switch ( $options['type'] ) {
			case 'color':
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'et_coloralpha':
				$wp_customize->add_control( new ET_Color_Alpha_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'range':
				$control_options = array_merge( $control_options, array(
					'input_attrs' => $options['input_attrs'],
				) );
				$wp_customize->add_control( new ET_Range_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'radio':
				$control_options = array_merge( $control_options, array(
					'choices' => $options['choices'],
				) );
				$wp_customize->add_control( $option_key, $control_options );
				break;
			case 'dropdown-font-styles':
				$control_options = array_merge( $control_options, array(
					'type'    => 'select',
					'choices' => et_extra_font_style_choices(),
				) );
				$wp_customize->add_control( new ET_Font_Style_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'dropdown-fonts':
				if ( et_is_one_font_language() ) {
					break;
				}
				$control_options = array_merge( $control_options, array(
					'type'    => 'select',
					'choices' => et_dropdown_google_font_choices(),
				) );
				$wp_customize->add_control( new ET_Font_Select_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'select':
			default:
				$control_options = array_merge( $control_options, array(
					'choices' => $options['choices'],
				) );
				$wp_customize->add_control( $option_key, $control_options );
				break;
		}

		$options['priority']++;
	}
}

if ( !function_exists( 'et_is_one_font_language' ) ) {

	function et_is_one_font_language() {
		static $et_is_one_font_language = null;

		if ( is_null( $et_is_one_font_language ) ) {
			$site_domain = get_locale();
			$et_one_font_languages = et_get_one_font_languages();

			$et_is_one_font_language = (bool) isset( $et_one_font_languages[$site_domain] );
		}

		return $et_is_one_font_language;
	}

}

if ( !function_exists( 'et_get_one_font_languages' ) ) {

	function et_get_one_font_languages() {
		$one_font_languages = array(
			'he_IL' => array(
				'language_name'   => 'Hebrew',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/alefhebrew.css',
				'font_family'     => "'Alef Hebrew', serif",
			),
			'ja'    => array(
				'language_name'   => 'Japanese',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansjapanese.css',
				'font_family'     => "'Noto Sans Japanese', serif",
			),
			'ko_KR' => array(
				'language_name'   => 'Korean',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/hanna.css',
				'font_family'     => "'Hanna', serif",
			),
			'ar'    => array(
				'language_name'   => 'Arabic',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/lateef.css',
				'font_family'     => "'Lateef', serif",
			),
			'th'    => array(
				'language_name'   => 'Thai',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansthai.css',
				'font_family'     => "'Noto Sans Thai', serif",
			),
			'ms_MY' => array(
				'language_name'   => 'Malay',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansmalayalam.css',
				'font_family'     => "'Noto Sans Malayalam', serif",
			),
			'zh_CN' => array(
				'language_name'   => 'Chinese',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/cwtexfangsong.css',
				'font_family'     => "'cwTeXFangSong', serif",
			),
		);

		return $one_font_languages;
	}

}

if ( !function_exists( 'et_dropdown_google_font_choices' ) ) {

	function et_dropdown_google_font_choices() {
		static $et_dropdown_google_font_choices = null;

		if ( is_null( $et_dropdown_google_font_choices ) ) {
			$site_domain = get_locale();

			$user_fonts = et_builder_get_custom_fonts();

			$google_fonts = et_builder_get_fonts( array(
				'prepend_standard_fonts' => false,
			) );

			// combine google fonts with custom user fonts
			$google_fonts = array_merge( $user_fonts, $google_fonts );

			$et_domain_fonts = array(
				'ru_RU' => 'cyrillic',
				'uk'    => 'cyrillic',
				'bg_BG' => 'cyrillic',
				'vi'    => 'vietnamese',
				'el'    => 'greek',
				'ar'    => 'arabic',
				'he_IL' => 'hebrew',
				'th'    => 'thai',
				'si_lk' => 'sinhala',
				'bn_bd' => 'bengali',
				'ta_lk' => 'tamil',
				'te'    => 'telegu',
				'km'    => 'khmer',
				'kn'    => 'kannada',
				'ml_in' => 'malayalam',
			);

			$font_choices = array();
			$font_choices['none'] = array(
				'label' => 'Default Theme Font',
			);

			$removed_fonts_mapping = et_builder_old_fonts_mapping();

			foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
				$use_parent_font = false;

				if ( isset( $removed_fonts_mapping[ $google_font_name ] ) ) {
					$parent_font = $removed_fonts_mapping[ $google_font_name ]['parent_font'];
					$google_font_properties['character_set'] = $google_fonts[ $parent_font ]['character_set'];
					$use_parent_font = true;
				}

				if ( '' !== $site_domain && isset( $et_domain_fonts[$site_domain] ) && false === strpos( $google_font_properties['character_set'], $et_domain_fonts[$site_domain] ) ) {
					continue;
				}
				$font_choices[ $google_font_name ] = array(
					'label' => $google_font_name,
					'data'  => array(
						'parent_font'    => $use_parent_font ? $google_font_properties['parent_font'] : '',
						'parent_styles'  => $use_parent_font ? $google_fonts[$parent_font]['styles'] : $google_font_properties['styles'],
						'current_styles' => $use_parent_font && isset( $google_fonts[$parent_font]['styles'] ) && isset( $google_font_properties['styles'] ) ? $google_font_properties['styles'] : '',
						'parent_subset'  => $use_parent_font && isset( $google_fonts[$parent_font]['character_set'] ) ? $google_fonts[$parent_font]['character_set'] : '',
					),
				);
			}

			$et_dropdown_google_font_choices = $font_choices;
		}

		return $et_dropdown_google_font_choices;
	}

}

/**
 * Outputting font-style attributes & values saved by ET_Font_Style_Control  on customizer
 *
 * @return string
 */
function et_print_font_style( $styles = '', $important = '', $boldness = 'bold' ) {
	// Prepare variable
	$font_styles = "";

	if ( '' !== $styles && false !== $styles ) {
		// Convert string into array
		$styles_array = explode( '|', $styles );

		// If $important is in use, give it a space
		if ( $important && '' !== $important ) {
			$important = " " . $important;
		}

		// Use in_array to find values in strings. Otherwise, display default text

		// Font weight
		if ( in_array( 'bold', $styles_array ) ) {
			$font_styles .= "font-weight: {$boldness}{$important}; ";
		} else {
			$font_styles .= "font-weight: normal{$important}; ";
		}

		// Font style
		if ( in_array( 'italic', $styles_array ) ) {
			$font_styles .= "font-style: italic{$important}; ";
		} else {
			$font_styles .= "font-style: normal{$important}; ";
		}

		// Text-transform
		if ( in_array( 'uppercase', $styles_array ) ) {
			$font_styles .= "text-transform: uppercase{$important}; ";
		} else {
			$font_styles .= "text-transform: none{$important}; ";
		}

		// Text-decoration
		if ( in_array( 'underline', $styles_array ) ) {
			$font_styles .= "text-decoration: underline{$important}; ";
		} else {
			$font_styles .= "text-decoration: none{$important}; ";
		}
	}

	return esc_html( $font_styles );
}

/**
 * Add custom customizer control
 * Check for WP_Customizer_Control existence before adding custom control because WP_Customize_Control is loaded on customizer page only
 *
 * @see _wp_customize_include()
 */
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Font style control for Customizer
	 */
	class ET_Font_Style_Control extends WP_Customize_Control {

		public $type = 'font_style';

		public function render_content() {
			if ( $this->setting->default ) {
				$this->input_attrs['data-default'] = $this->setting->default ;
			}

			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<?php $current_values = explode( '|', $this->value() );
			if ( empty( $this->choices ) )
				return;
			foreach ( $this->choices as $value => $label ) :
				$checked_class = in_array( $value, $current_values ) ? ' et_font_style_checked' : '';
				?>
					<span class="et_font_style et_font_value_<?php echo esc_attr( $value ); echo $checked_class; ?>">
						<input type="checkbox" class="et_font_style_checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $current_values ) ); ?> />
					</span>
				<?php
			endforeach;
			?>
			<input type="hidden" class="et_font_styles" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			<?php
		}

	}

	/**
	 * Icon picker control for Customizer
	 */
	class ET_Icon_Picker_Control extends WP_Customize_Control {

		public $type = 'icon_picker';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			et_pb_font_icon_list(); ?>
			<input type="hidden" class="et_selected_icon" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
		</label>
		<?php
		}

	}

	/**
	 * Range-based sliding value picker for Customizer
	 */
	class ET_Range_Control extends WP_Customize_Control {

		public $type = 'range';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> data-reset_value="<?php echo esc_attr( $this->setting->default ); ?>" />
			<input type="number" <?php $this->input_attrs(); ?> class="et-pb-range-input" value="<?php echo esc_attr( $this->value() ); ?>" />
			<span class="et_divi_reset_slider"></span>
		</label>
		<?php
		}

	}

	/**
	 * Custom Select option which supports data attributes for the <option> tags
	 */
	class ET_Select_Control extends WP_Customize_Control {

		public $type = 'select';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<?php $this->_render_select_start_el(); ?>
				<?php
				foreach ( $this->choices as $value => $attributes ) {
					$data_output = '';

					if ( ! empty( $attributes['data'] ) ) {
						foreach ( $attributes['data'] as $data_name => $data_value ) {
							if ( '' !== $data_value ) {
								$data_output .= sprintf( ' data-%1$s="%2$s"',
									esc_attr( $data_name ),
									esc_attr( $data_value )
								);
							}
						}
					}

					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . $data_output . '>' . esc_html( $attributes['label'] ) . '</option>';
				}
				?>
			</select>
		</label>
		<?php
		}

		public function _render_select_start_el() {
			?>
			<select <?php $this->link(); ?>>
			<?php
		}

	}

	/**
	 * Custom Select option which supports data attributes for the <option> tags
	 */
	class ET_Font_Select_Control extends ET_Select_Control {

		public function _render_select_start_el() {
			?>
			<select <?php $this->link(); ?> class="et-font-select-control">
			<?php
		}

	}

	/**
	 * Color picker with alpha color support for Customizer
	 */
	class ET_Color_Alpha_Control extends WP_Customize_Control {

		public $type = 'et_coloralpha';

		public $statuses;

		public function __construct( $manager, $id, $args = array() ) {
			$this->statuses = array( '' => esc_html__( 'Default', 'extra' ) );
			parent::__construct( $manager, $id, $args );

			// Printed saved value should always be in lowercase
			add_filter( "customize_sanitize_js_{$id}", array( $this, 'sanitize_saved_value' ) );
		}

		public function enqueue() {
			wp_enqueue_script( 'wp-color-picker-alpha' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		public function to_json() {
			parent::to_json();
			$this->json['statuses'] = $this->statuses;
			$this->json['defaultValue'] = $this->setting->default;
		}

		public function render_content() {}

		public function content_template() {
			?>
			<# var defaultValue = '';
			if ( data.defaultValue ) {
				if ( '#' !== data.defaultValue.substring( 0, 1 ) && 'rgba' !== data.defaultValue.substring( 0, 4 ) ) {
					defaultValue = '#' + data.defaultValue;
				} else {
					defaultValue = data.defaultValue;
				}
				defaultValue = ' data-default-color=' + defaultValue; // Quotes added automatically.
			} #>
			<label>
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
				<div class="customize-control-content">
					<input class="color-picker-hex" data-alpha="true" type="text" maxlength="30" placeholder="<?php esc_attr_e( 'Hex Value', 'extra' ); ?>" {{ defaultValue }} />
				</div>
			</label>
			<?php
		}

		/**
		 * Ensure saved value to be printed in lowercase.
		 * Mismatched case causes broken 4.7 in Customizer. Color Alpha control only saves string.
		 * @param string  saved value
		 * @return string formatted value
		 */
		public function sanitize_saved_value( $value ) {
			return strtolower( $value );
		}
	}
}

/* Mostly copied from paginate_links() */

function et_paginate_links( $args = '' ) {
	$defaults = array(
		'base'               => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format'             => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
		'total'              => 1,
		'current'            => 0,
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => esc_html__( '&laquo; Previous', 'extra' ),
		'next_text'          => esc_html__( 'Next &raquo;', 'extra' ),
		'beg_size'           => 1,
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => false, // array of query args to add
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	// Who knows what else people pass in $args
	$args['total'] = (int) $args['total'];
	if ( $args['total'] < 2 )
		return;
	$args['current']  = (int) $args['current'];
	$args['beg_size'] = 0 < (int) $args['beg_size'] ? (int) $args['beg_size'] : 1; // Out of bounds?  Make it the default.
	$args['end_size'] = 0 < (int) $args['end_size'] ? (int) $args['end_size'] : 1; // Out of bounds?  Make it the default.
	$args['mid_size'] = 0 <= (int) $args['mid_size'] ? (int) $args['mid_size'] : 2;
	$args['add_args'] = is_array( $args['add_args'] ) ? $args['add_args'] : false;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	if ( $args['prev_next'] && $args['current'] && 1 < $args['current'] ) :
		$link = str_replace( '%_%', 2 == $args['current'] ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] - 1, $link );
		if ( $args['add_args'] )
			$link = add_query_arg( $args['add_args'], $link );
		$link .= $args['add_fragment'];

		/**
		 * Filter the paginated links for the given archive pages.
		 *
		 * @since 3.0.0
		 *
		 * @param string $link The paginated link URL.
		 */

		$html = '<a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
		$html = $args['type'] == "list" ? '<li class="prev">' . $html . '</li>' : $html;
		$page_links[] = $html;

	endif;
	for ( $n = 1; $n <= $args['total']; $n++ ) :
		if ( $n == $args['current'] ) :
			$html = "<span class='page-numbers current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>";
			$html = $args['type'] == "list" ? '<li class="current">' . $html . '</li>' : $html;
			$page_links[] = $html;
			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $args['beg_size'] || ( $args['current'] && $n >= $args['current'] - $args['mid_size'] && $n <= $args['current'] + $args['mid_size'] ) || $n > $args['total'] - $args['end_size'] ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $args['add_args'] )
					$link = add_query_arg( $args['add_args'], $link );
				$link .= $args['add_fragment'];

				/** This filter is documented in wp-includes/general-template.php */
				$html = "<a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>";
				$html = $args['type'] == "list" ? '<li>' . $html . '</li>' : $html;
				$page_links[] = $html;
				$dots = true;
			elseif ( $dots && !$args['show_all'] ) :
				$html = '<span class="page-numbers dots">' . esc_html__( '&hellip;', 'extra' ) . '</span>';
				$html = $args['type'] == "list" ? '<li class="dots">' . $html . '</li>' : $html;
				$page_links[] = $html;
				$dots = false;
			endif;
		endif;
	endfor;
	if ( $args['prev_next'] && $args['current'] && ( $args['current'] < $args['total'] || -1 == $args['total'] ) ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] + 1, $link );
		if ( $args['add_args'] )
			$link = add_query_arg( $args['add_args'], $link );
		$link .= $args['add_fragment'];

		/** This filter is documented in wp-includes/general-template.php */
		$html = '<a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
		$html = $args['type'] == "list" ? '<li class="next">' . $html . '</li>' : $html;
		$page_links[] = $html;
	endif;
	switch ( $args['type'] ) :
		case 'array' :
			return $page_links;
			break;
		case 'list' :
			$r .= "<ul class='page-numbers'>\n\t";
			$r .= join( "\n\t", $page_links );
			$r .= "\n</ul>\n";
			break;
		default :
			$r = join( "\n", $page_links );
			break;
	endswitch;
	return $r;
}

if ( ! function_exists( 'et_show_cart_total' ) ) {

	function et_show_cart_total( $args = array() ) {
		global $shortname;

		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$defaults = array(
			'no_text' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		$cart_count = WC()->cart->get_cart_contents_count();
		$url        = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();

		printf(
			'<a href="%1$s" class="et-cart" title="%2$s">
				<span>%3$s</span>
			</a>',
			esc_url( $url ),
			esc_attr( sprintf( _n( '%d Item in Cart', '%d Items in Cart', $cart_count, $shortname ), $cart_count ) ),
			esc_html( ! $args['no_text'] ? sprintf( _n( '%d Item', '%d Items', $cart_count, $shortname ), $cart_count ) : $cart_count )
		);
	}

}

if ( ! function_exists( 'et_cart_has_total' ) ) {

	function et_cart_has_total() {
		global $shortname;

		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$cart_count = WC()->cart->get_cart_contents_count();

		return (bool) $cart_count;
	}

}

if ( ! function_exists( 'et_extra_activate_features' ) ) {

	function et_extra_activate_features(){
		define( 'ET_SHORTCODES_VERSION', et_get_theme_version() );

		/* activate shortcodes */
		require_once( get_template_directory() . '/epanel/shortcodes/shortcodes.php' );
	}

}
add_action( 'init', 'et_extra_activate_features' );

if ( ! function_exists( 'et_extra_theme_options_link' ) ) {
	function et_extra_theme_options_link() {
		return admin_url( 'admin.php?page=et_extra_options' );
	}
}
// correct the theme options link via filter
add_filter( 'et_pb_theme_options_link', 'et_extra_theme_options_link' );

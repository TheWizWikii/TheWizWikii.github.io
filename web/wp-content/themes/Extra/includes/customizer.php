<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function extra_customize_preview_enqueue_scripts() {
	$theme_version = SCRIPT_DEBUG ? time() : et_get_theme_version();
	wp_enqueue_script( 'extra-customizer', get_template_directory_uri() . '/scripts/theme-customizer.js', array( 'customize-preview' ), $theme_version, true );

	$home_layout_id = extra_get_home_layout_id();
	$post_id        = et_core_page_resource_is_singular() ? et_core_page_resource_get_the_ID() : 0;

	wp_localize_script( 'extra-customizer', 'EXTRA', array(
		'ajaxurl'                => set_url_scheme( admin_url( 'admin-ajax.php' ) ),
		'social_networks'        => extra_get_social_networks(),
		'settings'               => extra_get_customizer_value_bound_settings(),
		'current_home_layout_id' => $home_layout_id,
		'extra_customizer_nonce' => wp_create_nonce( 'extra_customizer_nonce' ),
		'is_custom_post_type'    => et_builder_post_is_of_custom_post_type( $post_id ) ? 'yes' : 'no',
		'css_selector_wrapper'   => ET_BUILDER_CSS_PREFIX,
	) );
}

add_action( 'customize_preview_init', 'extra_customize_preview_enqueue_scripts' );

function extra_customizer_value_formatted_property_selector_callback() {
	if ( !isset( $_POST['extra_customizer_nonce'] ) || !wp_verify_nonce( $_POST['extra_customizer_nonce'], 'extra_customizer_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	define( 'EXTRA_CUSTOMIZER_CALLBACK', true );

	$callback = sanitize_text_field( $_POST['callback'] );
	$setting_name = sanitize_text_field( $_POST['setting_name'] );
	$property = sanitize_text_field( $_POST['property'] );
	$unformatted_value = sanitize_text_field( $_POST['unformatted_value'] );

	// dynamic_selectors_shared_paramless_callback passes the whole customizer value as stringified JSON
	if ( isset( $_POST['style'] ) && 'dynamic_selectors_shared_paramless_callback' === $_POST['style'] ) {
		$unformatted_value = json_decode( stripslashes( $unformatted_value ) );
	}

	if ( is_callable( $callback ) ) {
		die( $callback( $setting_name, $property, $unformatted_value ) );
	} else {
		die( -1 );
	}
}

function extra_callback_is_customizer() {
	return defined( 'EXTRA_CUSTOMIZER_CALLBACK' );
}

function extra_get_google_font_css_value( $setting_name, $property, $unformatted_value ) {
	if ( !extra_callback_is_customizer() ) {
		et_builder_enqueue_font( $unformatted_value );
	}

	if ( empty( $unformatted_value ) || 'none' == $unformatted_value ) {
		$formatted_value = '';
	} else {
		$formatted_value = et_builder_get_font_family( $unformatted_value, false );
	}

	return $formatted_value;
}

function extra_sidebar_width_css_value( $setting_name, $property, $unformatted_value ) {
	$sidebar = extra_get_dynamic_selector( 'sidebar_column' );
	$main_column_with_sidebar = extra_get_dynamic_selector( 'main_column_with_sidebar' );

	if ( empty( $unformatted_value ) ) {
		$et_sidebar_width = extra_get_customizer_value_bound_setting( 'sidebar_width' );

		$formatted_value = isset( $et_sidebar_width['default'] ) ? $et_sidebar_width['default'] : '';
	} else {
		$formatted_value = $unformatted_value;
	}

	$sidebar_width = $unformatted_value;
	$main_column_with_sidebar_width = 100 - $sidebar_width;

	$formatted_value = sprintf('
		@media only screen and (min-width: 1025px) {
		%1$s {
			min-width: %2$d%%;
			max-width: %2$d%%;
			width: %2$d%%;
			flex-basis: %2$d%%;
		}
		%3$s {
			min-width: %4$d%%;
			max-width: %4$d%%;
			width: %4$d%%;
			flex-basis: %4$d%%;
		}
		}
		',
		esc_attr( $sidebar ),
		esc_attr( $sidebar_width ),
		esc_attr( $main_column_with_sidebar ),
		esc_attr( $main_column_with_sidebar_width )
	);

	return $formatted_value;
}

function extra_et_print_font_style_css_value( $setting_name, $property, $unformatted_value ) {
	$setting = extra_get_customizer_value_bound_setting( $setting_name );
	$default = isset( $setting['default'] ) ? $setting['default'] : "";
	$boldness = isset( $setting['boldness'] ) ? $setting['boldness'] : 'bold';

	if ( "" === $unformatted_value && $default !== $unformatted_value ) {
		$unformatted_value = "||||";
	}

	if ( empty( $unformatted_value ) ) {
		if ( extra_callback_is_customizer() ) {
			$formatted_value = -1;
		} else {
			$formatted_value = '';
		}
	} else {
		$formatted_value = et_print_font_style( $unformatted_value, false, $boldness );
	}
	return $formatted_value;
}

/**
 * Paramless callback for primary nav height. The parameter is kept as is to pass customizer settings
 * for AJAX-based fallback (hence the $customizer_settings naming)
 */
function extra_nav_height_value( $setting_name, $property, $customizer_settings ) {
	$default_nav_height    = 124;
	$default_logo_height   = 51;
	$default_font_size     = 16;
	$prefixes              = array(
		'primary',
		'fixed',
	);
	$formatted_value       = '';
	$header_style          = et_get_option( 'header_style', 'left-right' );
	$primary_nav_hide_logo_image = intval( et_get_option( 'primary_nav_hide_logo_image', 0 ) ) === 1;

	foreach ( $prefixes as $prefix ) {
		if ( 'fixed' === $prefix ) {
			$default_nav_height = 80;
		}

		$prefix_nav_height     = isset( $customizer_settings->{"et_extra[{$prefix}_nav_height]"} ) ? intval( $customizer_settings->{"et_extra[{$prefix}_nav_height]"} ) : intval( et_get_option( "{$prefix}_nav_height", $default_nav_height ) );
		$logo_height_value     = isset( $customizer_settings->{"et_extra[{$prefix}_logo_height]"} ) ? intval( $customizer_settings->{"et_extra[{$prefix}_nav_logo_height]"} ) : intval( et_get_option( "{$prefix}_nav_logo_height", $default_logo_height ) );
		$prefix_nav_font_size  = isset( $customizer_settings->{"et_extra[{$prefix}_font_size]"} ) ? intval( $customizer_settings->{"et_extra[{$prefix}_nav_font_size]"} ) : intval( et_get_option( "{$prefix}_nav_font_size", $default_font_size ) );
		$logo_height           = ( $logo_height_value / 100 ) * $prefix_nav_height;
		$logo_margin           = ( $prefix_nav_height - $logo_height ) / 2;
		$menu_padding          = ( $prefix_nav_height / 2 ) - ( $prefix_nav_font_size / 2 );
		$wrapper               = 'fixed' === $prefix ? '.et-fixed-header ' : '';

		$formatted_value .= sprintf('
			@media only screen and (min-width: 768px) {
				%4$s#main-header .logo {
					height: %1$spx;
					margin: %2$spx 0;
				}
				%4$s.header%5$s #et-navigation > ul > li > a {
					padding-bottom: %3$spx;
				}
			}',
			esc_attr( $logo_height ),
			esc_attr( $logo_margin ),
			esc_attr( $menu_padding ),
			esc_attr( $wrapper ),
			'centered' === $header_style && $primary_nav_hide_logo_image ? '.centered' : '.left-right'
		);

		if ( extra_display_ad( 'header', false ) ) {
			$formatted_value .= sprintf('
				@media only screen and (min-width: 768px) {
					%1$s#main-header .etad {
						max-height: %2$spx;
						margin: %3$spx 0;
					}
				}',
				esc_attr( $wrapper ),
				esc_attr( $logo_height ),
				esc_attr( $logo_margin )
			);
		}

		if ( $primary_nav_hide_logo_image ) {
			$menu_item_before_top = $menu_padding + 10;

			$formatted_value .= sprintf('
				@media only screen and (min-width: 768px) {
					%1$s.header%4$s #et-navigation > ul > li > a {
						padding-top: %2$spx;
					}
					%1$s.header%4$s #et-navigation > ul > li > a:before {
						top: %3$spx;
					}
				}',
				esc_attr( $wrapper ),
				esc_attr( $menu_padding ),
				esc_attr( $menu_item_before_top ),
				'centered' === $header_style ? '.centered' : '.left-right'
			);
		}
	}

	// Automatically hide logo in customizer so it can be toggled quickly.
	// The logo is not even printed on published front end
	if ( $primary_nav_hide_logo_image && is_customize_preview() ) {
		$formatted_value .= '#main-header .logo { display: none !important; }';
	}

	// Add margin for mobile show menu button when logo is hidden
	if ( $primary_nav_hide_logo_image ) {
		$formatted_value .= '
			@media only screen and (max-width: 1025px) {
				#et-mobile-navigation .show-menu-button { margin: 20px 0; }
			}
		';
	}

	return $formatted_value;
}

function et_pb_module_tabs_padding_css_value( $setting_name, $property, $unformatted_value ) {
	$padding_tab_top_bottom    = intval( $unformatted_value ) * 0.133333333;
	$padding_tab_active_top    = $padding_tab_top_bottom + 1;
	$padding_tab_active_bottom = $padding_tab_top_bottom - 1;
	$padding_tab_content       = intval( $unformatted_value ) * 0.8;
	$css                       = 'et_builder_maybe_wrap_css_selector';

	// negative result will cause layout issue
	if ( $padding_tab_active_bottom < 0 ) {
		$padding_tab_active_bottom = 0;
	}

	return sprintf(
		'%1$s{ padding: %2$spx %3$spx %4$spx; } %5$s{ padding: %6$spx %3$spx; } %7$s{ padding: %8$spx %3$spx; }\n',
		et_intentionally_unescaped( $css( '.et_pb_tabs_controls li', false ), 'fixed_string' ),
		esc_html( $padding_tab_active_top ),
		esc_html( $unformatted_value ), // #3
		esc_html( $padding_tab_active_bottom ),
		et_intentionally_unescaped( $css( '.et_pb_tabs_controls li.et_pb_tab_active', false ), 'fixed_string' ), // #5
		esc_html( $padding_tab_top_bottom ),
		et_intentionally_unescaped( $css( '.et_pb_all_tabs', false ), 'fixed_string' ),
		esc_html( $padding_tab_content ) // #8
	);
}

function et_pb_module_cta_padding_css_value( $setting_name, $property, $unformatted_value ) {
	$value = intval( $unformatted_value );
	$css   = 'et_builder_maybe_wrap_css_selectors';

	$formatted_value = sprintf(
		'%s { padding: %spx %spx !important; }',
		et_intentionally_unescaped( $css( '.et_pb_promo', false ), 'fixed_string' ),
		esc_html( $value ),
		esc_html( $value * ( 60 / 40 ) )
	);

	$formatted_value .= sprintf(
		' %s { padding: %spx; }',
		et_intentionally_unescaped( $css( '.et_pb_column_1_2 .et_pb_promo, .et_pb_column_1_3 .et_pb_promo, .et_pb_column_1_4 .et_pb_promo', false ), 'fixed_string' ),
		esc_html( $value )
	);

	return $formatted_value;
}

function et_pb_social_media_follow_font_size_css_value( $setting_name, $property, $unformatted_value ) {
	$icon_margin    = intval( $unformatted_value ) * 0.57;
	$icon_dimension = intval( $unformatted_value ) * 2;
	$css            = 'et_builder_maybe_wrap_css_selector';

	return "
		{$css( '.et_pb_social_media_follow li a.icon', false )} {
			margin-right: {$icon_margin}px;
			width: {$icon_dimension}px;
			height: {$icon_dimension}px;
		}
		{$css( '.et_pb_social_media_follow li a.icon::before', false )} {
			width: {$icon_dimension}px;
			height: {$icon_dimension}px;
			font-size: {$unformatted_value}px;
			line-height: {$icon_dimension}px;
		}
		{$css( '.et_pb_social_media_follow li a.follow_button', false )} {
			font-size: {$unformatted_value}px;
		}
	";
}

add_action( 'wp_ajax_extra_customizer_value_formatted_property_selector', 'extra_customizer_value_formatted_property_selector_callback' );

function et_extra_secondary_nav_icon_search_cart_font_size_css_value( $setting_name, $property, $unformatted_value ) {
	$value = intval( $unformatted_value );
	$icon_dimension = floor( $value * ( 16 / 12 ) );
	$icon_box_dimension = $value * ( 30 / 12 );
	$cart_padding_top_bottom = floor( $value * ( 9 / 12 ) );
	$cart_padding_right_left = $value * ( 10 / 12 );
	$search_padding_top_bottom = floor( $value * ( 7 / 12 ) );
	$search_padding_right_left = $value * ( 10 / 12 );
	$search_button_margin = 0 - ( $value / 2 );
	$search_width = $value * ( 120 / 12 );

	return sprintf(
		'#et-info .et-cart,
        #et-info .et-cart:before,
        #et-info .et-top-search .et-search-field,
        #et-info .et-top-search .et-search-submit:before {
            font-size: %1$fpx
        }
        #et-info .et-extra-social-icons .et-extra-icon {
            font-size: %2$fpx;
            line-height: %3$fpx;
            width: %3$fpx;
            height: %3$fpx;
        }
        #et-info .et-cart {
            padding: %4$fpx %5$fpx;
        }
        #et-info .et-top-search .et-search-field {
            padding: %6$fpx %7$fpx;
        }
        #et-info .et-top-search .et-search-field {
            width: %8$fpx;
        }
        #et-info .et-top-search .et-search-submit:before {
            margin-top: %9$fpx;
        }',
		esc_html( $unformatted_value ),
		esc_html( $icon_dimension ),
		esc_html( $icon_box_dimension ),
		esc_html( $cart_padding_top_bottom ),
		esc_html( $cart_padding_right_left ),
		esc_html( $search_padding_top_bottom ),
		esc_html( $search_padding_right_left ),
		esc_html( $search_width ),
		esc_html( $search_button_margin )
	);
}

function et_extra_secondary_nav_trending_font_size_css_value( $setting_name, $property, $unformatted_value ) {
	$value = intval( $unformatted_value );
	$trending_button_width = $value * ( 20 / 14 );
	$trending_button_height = $value * ( 2 / 14 );
	$trending_button_clicked_first_translateY = 6 + ( ( ( ( $value * ( 6 / 14 ) ) ) - 6 ) / 2 );
	$trending_button_clicked_last_translateY = 0 - $trending_button_clicked_first_translateY;

	return sprintf(
		'#et-trending-label,
		.et-trending-post a {
			font-size: %1$fpx;
		}
		#et-trending-button {
			width: %2$fpx;
			height: %2$fpx;
		}
		#et-trending-button span {
			width: %2$fpx;
			height: %3$fpx;
		}
		#et-trending-button.toggled span:first-child {
			-webkit-transform: translateY(%4$fpx) rotate(45deg);
			transform: translateY(%4$fpx) rotate(45deg);
		}
		#et-trending-button.toggled span:last-child {
			-webkit-transform: translateY(%5$fpx) rotate(-45deg);
			transform: translateY(%5$fpx) rotate(-45deg);
		}',
		esc_html( $value ),
		esc_html( $trending_button_width ),
		esc_html( $trending_button_height ),
		esc_html( $trending_button_clicked_first_translateY ),
		esc_html( $trending_button_clicked_last_translateY )
	);
}

function extra_customize_controls_enqueue_scripts() {
	$theme_version = et_get_theme_version();

	wp_enqueue_style( 'extra-customizer-controls-styles', get_template_directory_uri() . '/includes/admin/styles/theme-customizer-controls.css', array(), $theme_version );
	wp_enqueue_script( 'extra-customizer-controls-js', get_template_directory_uri() . '/scripts/theme-customizer-controls.js', array( 'jquery' ), $theme_version, true );

	wp_localize_script( 'extra-customizer-controls-js', 'extra_customizer_control_params', array(
		'footer_sidebar_names' => array(
			'column-1' => array(
				esc_html__( 'Footer Sidebar', 'extra' ),
				esc_html__( 'Inactive Footer Sidebar', 'extra' ),
				esc_html__( 'Inactive Footer Sidebar', 'extra' ),
				esc_html__( 'Inactive Footer Sidebar', 'extra' ),
			),
			'column-2' => array(
				esc_html__( 'Footer Sidebar Left', 'extra' ),
				esc_html__( 'Inactive Footer Sidebar', 'extra' ),
				esc_html__( 'Inactive Footer Sidebar', 'extra' ),
				esc_html__( 'Footer Sidebar Right', 'extra' ),
			),
			'column-3' => array(
				esc_html__( 'Footer Sidebar Left', 'extra' ),
				esc_html__( 'Footer Sidebar Middle', 'extra' ),
				esc_html__( 'Inactive Footer Sidebar', 'extra' ),
				esc_html__( 'Footer Sidebar Right', 'extra' ),
			),
			'column-4' => array(
				esc_html__( 'Footer Sidebar Left', 'extra' ),
				esc_html__( 'Footer Sidebar Middle Left', 'extra' ),
				esc_html__( 'Footer Sidebar Middle Right', 'extra' ),
				esc_html__( 'Footer Sidebar Right', 'extra' ),
			),
		),
		'user_fonts' => et_builder_get_custom_fonts(),
	) );
}

add_action( 'customize_controls_enqueue_scripts', 'extra_customize_controls_enqueue_scripts' );

function extra_get_customizer_dynamic_selectors_settings() {
	$all_settings = extra_get_customizer_value_bound_settings();
	$settings = array();

	$all_dynamic_selectors = array(
		'dynamic_selectors',
		'dynamic_selectors_value_format',
		'dynamic_selectors_value_format_callback',
		'dynamic_selectors_shared_paramless_callback',
	);

	foreach ($all_settings as $setting => $setting_options) {
		if ( in_array( $setting_options['value_bind']['style'], $all_dynamic_selectors ) ) {
			$settings[ $setting ] = $all_settings[ $setting ];
		}
	}

	return $settings;
}

function extra_get_customizer_option_dynamic_selector( $option ) {
	$all_settings = extra_get_customizer_value_bound_settings();
	$settings = array();

	$all_dynamic_selectors = array(
		'dynamic_selectors',
		'dynamic_selectors_value_format',
		'dynamic_selectors_value_format_callback',
	);

	foreach ($all_settings as $setting => $setting_options) {
		if ( in_array( $setting_options['value_bind']['style'], $all_dynamic_selectors ) ) {
			if ( $setting == $option ) {
				return $setting_options['value_bind']['selector'];
			}
		}
	}

	return false;
}

function extra_customizer_selector_classes( $selector, $return_array = true ) {
	$all_settings = extra_get_customizer_value_bound_settings();
	$classes = array();

	foreach ($all_settings as $setting => $setting_options) {
		if ( 'class_toggle' == $setting_options['value_bind']['style'] ) {
			if ( $selector == $setting_options['value_bind']['selector'] ) {
				$class = !empty( $setting_options['value_bind']['class'] ) ? $setting_options['value_bind']['class'] : $setting;

				$default = ! empty( $setting_options['default'] ) ? $setting_options['default'] : false;

				$value = et_get_option( $setting, $default );

				if ( '_value_bind_to_value' == $class ) {
					if ( !empty( $setting_options['value_bind']['format'] ) ) {
						$class = str_ireplace( '%value%', strval( $value ), $setting_options['value_bind']['format'] );
					} else {
						$class = $value;
					}
				}

				if ( !empty( $value ) ) {
					$classes[] = $class;
				}
			}
		}
	}

	$classes = extra_classes( $classes, $selector );

	if ( empty( $classes ) ) {
		return;
	}

	return $return_array ? $classes : implode( ' ', $classes );
}

function extra_customizer_el_visible( $selector ) {
	$all_settings = extra_get_customizer_value_bound_settings();
	$classes = array();

	foreach ($all_settings as $setting => $setting_options) {
		$style = $setting_options['value_bind']['style'];

		// Allow any field style to perform toggle / reverse toggle. Specifically needed
		// If a field triggers dynamic_selectors_shared_paramless_callback and toggle
		if ( isset( $setting_options['value_bind']['perform_toggle'] ) ) {
			$style = $setting_options['value_bind']['perform_toggle'];
		}

		if ( 'el_toggle' == $style || 'el_toggle_reverse' == $style ) {
			if ( $selector == $setting_options['value_bind']['selector'] ) {
				$default = ! empty( $setting_options['default'] ) ? $setting_options['default'] : false;
				$option = et_get_option( $setting, $default );
				return 'el_toggle_reverse' == $style ? !$option : $option;
			}
		}
	}
}

function extra_visible_display_css( $should_display, $echo = true ) {
	$output = !$should_display ? 'display:none;' : '';
	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function extra_get_customizer_value_bound_setting( $setting ) {
	$all_settings = extra_get_customizer_value_bound_settings();
	return isset( $all_settings[ $setting ] ) ? $all_settings[ $setting ] : '';
}

function extra_get_customizer_value_bound_settings() {
	static $settings = null;

	if ( is_null( $settings ) ) {
		$settings = array();
		foreach ( extra_customizer_settings( 'all' ) as $set => $set_options ) {
			foreach ( $set_options as $_panel => $panel_options ) {
				$panel = '';

				if ( count( $panel_options['sections'] ) > 1 ) {
					$panel = 'extra_' . $_panel;
				}

				foreach ( $panel_options['sections'] as $section => $section_options ) {

					$section_settings = !empty( $section_options['settings'] ) ? $section_options['settings'] : array();
					unset( $section_options['settings'] );

					if ( !empty( $panel ) ) {
						$section_settings = apply_filters( 'extra_customizer_register_' . $_panel . '_' . $section . '_settings', $section_settings );
						$section = 'et_extra_' . $_panel . '_' . $section . '_settings';
					} else {
						$section_settings = apply_filters( 'extra_customizer_register_' . $section . '_settings', $section_settings );
						$section = 'et_extra_' . $section . '_settings';
					}

					$section_settings = apply_filters( 'extra_customizer_register_settings', $section_settings, $section, $panel );

					foreach ( $section_settings as $setting => $setting_options ) {
						if ( !empty( $setting_options['value_bind'] ) ) {
							$settings[$setting] = $section_settings[ $setting ];
						}
					}
				}
			}
		}
	}

	return $settings;
}

function extra_customizer_font_size_setting( $args ) {
	$args = wp_parse_args( $args, array(
		'setting'   => '',
		'label'     => '',
		'min'       => 1,
		'max'       => 32,
		'step'      => 1,
		'selectors' => array(),
	) );

	return array(
		$args['setting'] => array(
			'label'       => $args['label'],
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( $args['setting'], 'default' ),
			'input_attrs' => array(
				'min'  => $args['min'],
				'max'  => $args['max'],
				'step' => $args['step'],
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => $args['selectors'],
					),
				),
			),
		),
	);
}

function extra_customizer_font_style_setting( $args ) {
	$args = wp_parse_args( $args, array(
		'setting'   => '',
		'label'     => '',
		'selectors' => array(),
	) );

	return array(
		$args['setting'] => array(
			'label'      => $args['label'],
			'type'       => 'dropdown-font-styles',
			'default'    => ET_Global_Settings::get_value( $args['setting'], 'default' ),
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => $args['selectors'],
				),
			),
		),
	);
}

function extra_customizer_register_general_layout_settings() {
	return array(
		'boxed_layout'  => array(
			'label'      => esc_html__( 'Enable Boxed Layout', 'extra' ),
			'type'       => 'checkbox',
			'value_bind' => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
			),
		),
		'content_width' => array(
			'label'       => esc_html__( 'Website Content Width', 'extra' ),
			'type'        => 'range',
			'default'     => '' == et_get_option( 'boxed_layout' ) ? '1280' : '1360',
			'input_attrs' => array(
				'min'  => 960,
				'max'  => 1920,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'max-width',
						'format'    => '%value%px',
						'selectors' => array(
							'#main-content .container',
							'.boxed_layout #page-container',
							'.boxed_layout',
							'.boxed_layout.et_fixed_nav .et-fixed-header #main-header',
						),
					),
				),
			),
		),
		'gutter_width'  => array(
			'label'       => esc_html__( 'Website Gutter Width', 'extra' ),
			'type'        => 'range',
			'default'     => '3',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 4,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => '_value_bind_to_value',
				'format'   => 'et_pb_gutters%value%',
			),
		),
		'sidebar_width' => array(
			'label'       => esc_html__( 'Sidebar Width', 'extra' ),
			'type'        => 'range',
			'default'     => '25',
			'input_attrs' => array(
				'min'  => 19,
				'max'  => 33,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                                 => 'dynamic_selectors_value_format_callback',
				'value_format_callback'                 => 'extra_sidebar_width_css_value',
				'use_formatted_value_as_css_expression' => true,
				'property_selectors'                    => array(
					'width' => array(),
				),
			),
		),
		'accent_color'  => array(
			'label'      => esc_html__( 'Accent color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#00a8ff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color'            => extra_get_dynamic_selector( 'accent_color_color' ),
					'background-color' => extra_get_dynamic_selector( 'accent_color_background_color' ),
					'border-color'     => extra_get_dynamic_selector( 'accent_color_border_color' ),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_general_layout_settings', 'extra_customizer_register_general_layout_settings', 1 );

function extra_customizer_register_general_typography_settings() {
	return array(
		'body_heading_font_size'     => array(
			'label'       => esc_html__( 'Heading Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '16',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 72,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'body_heading' ),
					),
				),
			),
		),
		'body_font_size'             => array(
			'label'       => esc_html__( 'Body Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '14',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							'body',
						),
					),
				),
			),
		),
		'body_heading_line_height'   => array(
			'label'       => esc_html__( 'Heading Line Height', 'extra' ),
			'type'        => 'range',
			'default'     => '1.7',
			'input_attrs' => array(
				'min'  => 0.8,
				'max'  => 3,
				'step' => 0.1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'line-height',
						'format'    => '%value%em',
						'selectors' => extra_get_dynamic_selector( 'body_heading' ),
					),
				),
			),
		),
		'body_line_height'           => array(
			'label'       => esc_html__( 'Body Line Height', 'extra' ),
			'type'        => 'range',
			'default'     => '1.7',
			'input_attrs' => array(
				'min'  => 0.8,
				'max'  => 3,
				'step' => 0.1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'line-height',
						'format'    => '%value%em',
						'selectors' => array(
							'body',
							'p',
						),
					),
				),
			),
		),
		'body_heading_font_style'    => array(
			'label'      => esc_html__( 'Heading Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'default'    => 'bold|uppercase',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => extra_get_dynamic_selector( 'body_heading' ),
				),
			),
		),
		'body_heading_font'          => array(
			'label'      => esc_html__( 'Heading Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => extra_get_dynamic_selector( 'body_heading' ),
				),
			),
		),
		'body_font'                  => array(
			'label'      => esc_html__( 'Body Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => array(
						'body',
					),
				),
			),
		),
		'body_link_color'            => array(
			'label'      => esc_html__( 'Body Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.75)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'a',
						'a:visited',
						'.post-content a',
						'.et_pb_pagebuilder_layout .et_pb_blurb_container p a', // Blurb
						'.et_pb_pagebuilder_layout .et_pb_code a', // Code
						'.et_pb_pagebuilder_layout .et_pb_promo_description a', // CTA, transparent background
						'.et_pb_pagebuilder_layout .et_pb_newsletter_description a', // Email Optin, transparent background
						'.et_pb_pagebuilder_layout .et_pb_team_member_description > a', // Person, transparent background
						'.et_pb_pagebuilder_layout .et_pb_pricing li a', // Pricing Table
						'.et_pb_pagebuilder_layout .et_pb_slide_content a', // Slider
						'.et_pb_pagebuilder_layout .et_pb_tab a', // Tabs
						'.et_pb_pagebuilder_layout .et_pb_text a', // Text
						'.et_pb_pagebuilder_layout .et_pb_toggle_content a', // Toggle
						'.et_pb_pagebuilder_layout .et_pb_fullwidth_code a', // Fullwidth Blurb
					),
				),
			),
		),
		'body_text_color'            => array(
			'label'      => esc_html__( 'Body Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'body',
					),
				),
			),
		),
		'heading_text_color'         => array(
			'label'      => esc_html__( 'Heading Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.75)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'body_heading' ),
				),
			),
		),
		'archive_heading_text_color' => array(
			'label'      => esc_html__( 'Archive Heading Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.75)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'archive_heading' ),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_general_typography_settings', 'extra_customizer_register_general_typography_settings', 1 );

function extra_customizer_register_general_background_settings() {
	return array(
		'boxed_layout_background_color' => array(
			'label'      => esc_html__( 'Boxed Layout - Background color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'body',
					),
				),
			),
		),
		'background_color'              => array(
			'label'          => esc_html__( 'Background Color', 'extra' ),
			'type'           => 'et_coloralpha',
			'default'        => '#ecf0f5',
			'theme_supports' => 'custom-background',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#page-container',
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_general_background_settings', 'extra_customizer_register_general_background_settings', 1 );

function extra_customizer_register_header_and_navigation_header_format_settings() {
	return array(
		'header_style'          => array(
			'label'             => esc_html__( 'Left/Right or Centered?', 'extra' ),
			'type'              => 'radio',
			'sanitize_callback' => 'et_sanitize_header_style',
			'default'           => 'left-right',
			'choices'           => et_extra_header_style_choices(),
			'value_bind'  => array(
				'style'                 => 'dynamic_selectors_shared_paramless_callback',
				'value_format_callback' => 'extra_nav_height_value',
				'property_selectors'    => array(
					'height' => array(),
				),
				'perform_toggle'        => 'class_toggle',
				'selector'              => 'header',
				'class'                 => '_value_bind_to_value',
			),
		),
		'hide_nav_until_scroll' => array(
			'label'      => esc_html__( 'Hide Navigation Until Scroll', 'extra' ),
			'type'       => 'checkbox',
			'value_bind' => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => 'et_hide_nav',
			),
		),
	);
}

add_filter( 'extra_customizer_register_header_and_navigation_header_format_settings', 'extra_customizer_register_header_and_navigation_header_format_settings', 1 );

function extra_customizer_register_header_and_navigation_primary_nav_settings() {
	return array(
		'primary_nav_fullwidth'                  => array(
			'label'      => esc_html__( 'Make Fullwidth', 'extra' ),
			'type'       => 'checkbox',
			'value_bind' => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => 'et_fullwidth_nav',
			),
		),
		'primary_nav_hide_logo_image'            => array(
			'label'      => esc_html__( 'Hide Logo Image', 'extra' ),
			'type'       => 'checkbox',
			'value_bind'  => array(
				'style'                 => 'dynamic_selectors_shared_paramless_callback',
				'value_format_callback' => 'extra_nav_height_value',
				'property_selectors'    => array(
					'height' => array(),
				),
				'perform_toggle'        => 'el_toggle_reverse',
				'selector'              => extra_get_dynamic_selector( 'logo' ),
			),
		),
		'primary_nav_height'                     => array(
			'label'       => esc_html__( 'Menu Height', 'extra' ),
			'type'        => 'range',
			'default'     => 124,
			'input_attrs' => array(
				'min'  => 80,
				'max'  => 300,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                 => 'dynamic_selectors_shared_paramless_callback',
				'value_format_callback' => 'extra_nav_height_value',
				'property_selectors'    => array(
					'height' => array(),
				),
			),
		),
		'primary_nav_logo_height'                => array(
			'label'       => esc_html__( 'Logo Height', 'extra' ),
			'type'        => 'range',
			'default'     => 51,
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 100,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                 => 'dynamic_selectors_shared_paramless_callback',
				'value_format_callback' => 'extra_nav_height_value',
				'property_selectors'    => array(
					'height' => array(),
				),
			),
		),
		'primary_nav_font_size'                  => array(
			'label'       => esc_html__( 'Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => 16,
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 24,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							'#et-menu li a',
						),
					),
				),
			),
		),
		'primary_nav_letter_spacing'             => array(
			'label'       => esc_html__( 'Letter Spacing', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 8,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'letter-spacing',
						'format'    => '%value%px',
						'selectors' => array(
							'#et-menu li',
						),
					),
				),
			),
		),
		'primary_nav_font'                       => array(
			'label'      => esc_html__( 'Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value', // todo, make a js version of this so its faster!, SEE: maybe_request_font()
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => array(
						'#et-menu li',
					),
				),
			),
		),
		'primary_nav_font_style'                 => array(
			'label'      => esc_html__( 'Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'default'    => 'bold|uppercase',
			'boldness'   => '600',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => array(
						'#et-navigation ul li',
						'#et-navigation li a',
						'#et-navigation > ul > li > a',
					),
				),
			),
		),
		'primary_nav_text_color'                 => array(
			'label'      => esc_html__( 'Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-menu > li a',
						'#et-menu > li.menu-item-has-children > a:after',
						'#et-menu > li.mega-menu-featured > a:after',
						'#et-extra-mobile-menu > li.mega-menu-featured > a:after',
					),
				),
			),
		),
		'primary_nav_active_link_color'          => array(
			'label'      => esc_html__( 'Hover/Active Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-menu > li > a:hover',
						'#et-menu > li.menu-item-has-children > a:hover:after',
						'#et-menu > li.mega-menu-featured > a:hover:after',
						'#et-navigation > ul#et-menu > li.current-menu-item > a',
						'#et-navigation > ul#et-menu > li.current_page_item > a',
						'#et-extra-mobile-menu > li.mega-menu-featured > a:hover:after',
						'#et-extra-mobile-menu > li.current-menu-item > a',
						'#et-extra-mobile-menu > li.current_page_item > a',
						'#et-extra-mobile-menu > li > a:hover',
					),
					'background-color' => array(
						'#et-navigation > ul > li > a:before',
					),
				),
			),
		),
		'primary_nav_background_color'           => array(
			'label'      => esc_html__( 'Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#3e5062',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#main-header',
					),
				),
			),
		),
		'primary_nav_dropdown_background_color'  => array(
			'label'      => esc_html__( 'Dropdown Menu Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#232323',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#et-menu > li > ul',
						'#et-menu li > ul',
						'#et-menu > li > ul > li > ul',
						'#et-mobile-navigation nav',
						'.et-top-search-primary-menu-item .et-top-search',
					),
				),
			),
		),
		'primary_nav_dropdown_line_color'        => array(
			'label'      => esc_html__( 'Dropdown Menu Line Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.1)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'border-color' => array(
						'#et-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post',
						'#et-menu li.mega-menu > ul > li > a',
						'#et-menu li.mega-menu > ul li:last-child a',
						'#et-menu li > ul li a',
					),
				),
			),
		),
		'primary_nav_dropdown_text_color'        => array(
			'label'      => esc_html__( 'Dropdown Menu Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-menu li > ul li a',
						'#et-menu li.mega-menu > ul > li > a',
						'#et-menu .sub-menu li.mega-menu-featured > a:after',
						'#et-menu .sub-menu li.menu-item-has-children > a:after',
						'#et-extra-mobile-menu .sub-menu li.mega-menu-featured > a:after',
						'#et-extra-mobile-menu li a',
						'#et-menu li.mega-menu-featured > ul li .title',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li .title',
						'#et-menu li.mega-menu-featured > ul li .featured-post h2',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li .featured-post h2',
						'#et-menu li.mega-menu-featured > ul li .featured-post .post-meta a',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li .featured-post .post-meta a',
						'#et-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post .post-content .post-meta',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post .post-content .post-meta',
						'#et-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post .post-content .post-meta a',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post .post-content .post-meta a',
					),
				),
			),
		),
		'primary_nav_dropdown_active_link_color' => array(
			'label'      => esc_html__( 'Dropdown Menu Hover/Active Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-menu li > ul li a:hover',
						'#et-extra-mobile-menu li > ul li a:hover',
						'#et-menu li > ul li.current-menu-item a',
						'#et-menu li > ul li.current_page_item a',
						'#et-extra-mobile-menu li > ul li.current-menu-item a',
						'#et-extra-mobile-menu li > ul li.current_page_item a',
						'#et-menu li.mega-menu > ul > li > a:hover',
						'#et-menu .sub-menu li.mega-menu-featured > a:hover:after',
						'#et-menu .sub-menu li.menu-item-has-children > a:hover:after',
						'#et-extra-mobile-menu .sub-menu li.mega-menu-featured > a:hover:after',
						'#et-menu li.mega-menu-featured > ul li .featured-post .post-meta a:hover',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li .featured-post .post-meta a:hover',
						'#et-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post .post-content .post-meta a:hover',
						'#et-extra-mobile-menu li.mega-menu-featured > ul li.menu-item .recent-list .recent-post .post-content .post-meta a:hover',
					),
				),
			),
		),
		'primary_nav_dropdown_animation'         => array(
			'label'             => esc_html__( 'Dropdown Menu Animation', 'extra' ),
			'type'              => 'select',
			'sanitize_callback' => 'et_sanitize_dropdown_animation',
			'default'           => 'Default',
			'choices'           => et_extra_dropdown_animation_choices(),
			'value_bind'        => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => '_value_bind_to_value',
				'format'   => 'et_primary_nav_dropdown_animation_%value%',
			),
		),
	);
}

add_filter( 'extra_customizer_register_header_and_navigation_primary_nav_settings', 'extra_customizer_register_header_and_navigation_primary_nav_settings', 1 );

function extra_customizer_register_header_and_navigation_secondary_nav_settings() {
	return array(
		'secondary_nav_fullwidth'                         => array(
			'label'      => esc_html__( 'Make Fullwidth', 'extra' ),
			'type'       => 'checkbox',
			'value_bind' => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => 'et_fullwidth_secondary_nav',
			),
		),
		'secondary_nav_font_size'                         => array(
			'label'       => esc_html__( 'Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => 14,
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 24,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							'#et-secondary-menu li',
						),
					),
				),
			),
		),
		'secondary_nav_letter_spacing'                    => array(
			'label'       => esc_html__( 'Letter Spacing', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 8,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'letter-spacing',
						'format'    => '%value%px',
						'selectors' => array(
							'#et-secondary-menu li',
						),
					),
				),
			),
		),
		'secondary_nav_font'                              => array(
			'label'      => esc_html__( 'Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => array(
						'#et-secondary-menu li',
					),
				),
			),
		),
		'secondary_nav_font_style'                        => array(
			'label'      => esc_html__( 'Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => array(
						'#et-secondary-menu li',
						'#et-secondary-menu li a',
					),
				),
			),
		),
		'secondary_nav_background_color'                  => array(
			'label'      => esc_html__( 'Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#2b3843',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#top-header',
					),
				),
			),
		),
		'secondary_nav_text_color'                        => array(
			'label'      => esc_html__( 'Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-secondary-menu a',
						'#et-secondary-menu li.menu-item-has-children > a:after',
					),
				),
			),
		),
		'secondary_nav_active_link_color'                 => array(
			'label'      => esc_html__( 'Hover/Active Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'color',
						'format'    => '%value%',
						'selectors' => array(
							'#et-secondary-menu li a:hover',
							'#et-secondary-menu > li > a:hover:before',
							'#et-secondary-menu li.menu-item-has-children > a:hover:after',
							'#et-secondary-menu li.current-menu-item > a',
						),
					),
					array(
						'property'  => 'text-shadow',
						'format'    => '10px 0 %value%, -10px 0 %value%',
						'selectors' => array(
							'#et-secondary-menu > li > a:hover:before',
						),
					),
				),
			),
		),
		'secondary_nav_dropdown_background_color'         => array(
			'label'      => esc_html__( 'Dropdown Menu Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#2b3843',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#et-secondary-nav #et-secondary-menu',
					),
				),
			),
		),
		'secondary_nav_dropdown_text_color'               => array(
			'label'      => esc_html__( 'Dropdown Menu Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-secondary-menu ul a',
						'#et-secondary-menu li > ul li.menu-item-has-children > a:after',
					),
				),
			),
		),
		'secondary_nav_dropdown_active_link_color'        => array(
			'label'      => esc_html__( 'Dropdown Menu Hover/Active Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-secondary-menu li > ul li a:hover',
						'#et-secondary-menu li > ul li.menu-item-has-children > a:hover:after',
						'#et-secondary-menu li > ul li.current-menu-item a',
						'#et-secondary-menu li > ul li.current_page_item a',
					),
				),
			),
		),
		'secondary_nav_dropdown_animation'                => array(
			'label'             => esc_html__( 'Dropdown Menu Animation', 'extra' ),
			'type'              => 'select',
			'default'           => 'Default',
			'sanitize_callback' => 'et_sanitize_dropdown_animation',
			'choices'           => et_extra_dropdown_animation_choices(),
			'value_bind'        => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => '_value_bind_to_value',
				'format'   => 'et_secondary_nav_dropdown_animation_%value%',
			),
		),
		'secondary_nav_trending_font_size'                => array(
			'label'       => esc_html__( 'Trending Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => 14,
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 24,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                                 => 'dynamic_selectors_value_format_callback',
				'value_format_callback'                 => 'et_extra_secondary_nav_trending_font_size_css_value',
				'use_formatted_value_as_css_expression' => true,
				'property_selectors'                    => array(
					'custom' => array(),
				),
			),
		),
		'secondary_nav_trending_font'                     => array(
			'label'      => esc_html__( 'Trending Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => array(
						'#et-trending-label',
						'.et-trending-post a',
					),
				),
			),
		),
		'secondary_nav_trending_font_style'               => array(
			'label'      => esc_html__( 'Trending Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => array(
						'#et-trending-label',
						'.et-trending-post a',
					),
				),
			),
		),
		'secondary_nav_trending_label_text_color'         => array(
			'label'      => esc_html__( 'Trending Label Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color'            => array(
						'#et-trending-label',
						'#et-trending-button',
					),
					'background-color' => array(
						'#et-trending-button span',
					),
				),
			),
		),
		'secondary_nav_trending_title_text_color'         => array(
			'label'      => esc_html__( 'Trending Title Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'header.header .et-trending-post a',
						'header.header .et-trending-post a:visited',
					),
				),
			),
		),
		'secondary_nav_icon_search_cart_font_size'        => array(
			'label'       => esc_html__( 'Search, Cart, & Icon Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => 12,
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 24,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                                 => 'dynamic_selectors_value_format_callback',
				'value_format_callback'                 => 'et_extra_secondary_nav_icon_search_cart_font_size_css_value',
				'use_formatted_value_as_css_expression' => true,
				'property_selectors'                    => array(
					'custom' => array(),
				),
			),
		),
		'secondary_nav_icon_search_cart_letter_spacing'   => array(
			'label'       => esc_html__( 'Search, Cart, & Icon Letter Spacing', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 8,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'letter-spacing',
						'format'    => '%value%px',
						'selectors' => array(
							'#et-info .et-cart',
							'#et-info .et-top-search .et-search-field',
						),
					),
				),
			),
		),
		'secondary_nav_icon_search_cart_font'             => array(
			'label'      => esc_html__( 'Search & Cart Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => array(
						'#et-info .et-cart span',
						'#et-info .et-top-search .et-search-field',
					),
				),
			),
		),
		'secondary_nav_icon_search_cart_font_style'       => array(
			'label'      => esc_html__( 'Search & Cart Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style'                        => array(
						'#et-info .et-cart span',
						'#et-info .et-top-search .et-search-field',
					),
					'font-style-placeholder-webkit'     => array(
						'#et-info .et-top-search .et-search-field::-webkit-input-placeholder',
					),
					'font-style-placeholder-moz-legacy' => array(
						'#et-info .et-top-search .et-search-field:-moz-placeholder',
					),
					'font-style-placeholder-moz'        => array(
						'#et-info .et-top-search .et-search-field::-moz-placeholder',
					),
					'font-style-placeholder-ms'         => array(
						'#et-info .et-top-search .et-search-field:-ms-input-placeholder',
					),
				),
			),
		),
		'secondary_nav_icon_search_cart_background_color' => array(
			'label'      => esc_html__( 'Search, Cart, & Icon Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.1)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#et-info .et-cart',
						'#et-info .et-top-search .et-search-field',
						'#et-info .et-extra-social-icons .et-extra-icon',
					),
				),
			),
		),
		'secondary_nav_icon_search_cart_text_color'       => array(
			'label'      => esc_html__( 'Search, Cart, & Icon Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#et-info .et-cart',
						'#et-info .et-top-search .et-search-field',
						'#et-info .et-top-search .et-search-submit:before',
						'#et-info .et-extra-social-icons .et-extra-icon',
						'#et-info .et-extra-social-icons .et-extra-icon:before',
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_header_and_navigation_secondary_nav_settings', 'extra_customizer_register_header_and_navigation_secondary_nav_settings', 1 );

function extra_customizer_register_header_and_navigation_fixed_nav_settings() {
	return array(
		'fixed_nav_hide_logo_image'           => array(
			'label'      => esc_html__( 'Hide Logo Image', 'extra' ),
			'type'       => 'checkbox',
			'value_bind' => array(
				'style'    => 'class_toggle',
				'selector' => 'body',
				'class'    => 'et_fixed_nav_hide_logo_image',
			),
		),
		'fixed_nav_height'                    => array(
			'label'       => esc_html__( 'Menu Height', 'extra' ),
			'type'        => 'range',
			'default'     => 80,
			'input_attrs' => array(
				'min'  => 60,
				'max'  => 300,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                 => 'dynamic_selectors_shared_paramless_callback',
				'value_format_callback' => 'extra_nav_height_value',
				'property_selectors'    => array(
					'height' => array(),
				),
			),
		),
		'fixed_nav_logo_height'               => array(
			'label'       => esc_html__( 'Logo Height', 'extra' ),
			'type'        => 'range',
			'default'     => 51,
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 100,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                 => 'dynamic_selectors_shared_paramless_callback',
				'value_format_callback' => 'extra_nav_height_value',
				'property_selectors'    => array(
					'height' => array(),
				),
			),
		),
		'fixed_nav_font_size'                 => array(
			'label'       => esc_html__( 'Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => 16,
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 24,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							'.et-fixed-header #et-menu li a',
						),
					),
				),
			),
		),
		'fixed_primary_nav_text_color'        => array(
			'label'      => esc_html__( 'Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'.et-fixed-header #et-menu > li a',
						'.et-fixed-header #et-menu > li.menu-item-has-children > a:after',
						'.et-fixed-header #et-menu > li.mega-menu-featured > a:after',
						'.et-fixed-header #et-extra-mobile-menu > li.mega-menu-featured > a:after',
					),
				),
			),
		),
		'fixed_primary_nav_active_link_color' => array(
			'label'      => esc_html__( 'Hover/Active Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'.et-fixed-header #et-navigation > ul#et-menu > li.current-menu-item > a',
						'.et-fixed-header #et-navigation > ul#et-menu > li.li.current_page_item > a',
						'.et-fixed-header #et-navigation > ul#et-menu > li > a:hover',
						'.et-fixed-header #et-navigation > ul#et-menu > li.menu-item-has-children > a:hover:after',
						'.et-fixed-header #et-navigation > ul#et-menu > li.mega-menu-featured > a:hover:after',
						'.et-fixed-header #et-extra-mobile-menu > li.mega-menu-featured > a:hover:after',
					),
					'background-color' => array(
						'.et-fixed-header #et-navigation > ul > li > a:before',
					),
				),
			),
		),
		'fixed_primary_nav_background_color'  => array(
			'label'      => esc_html__( 'Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#3e5062',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'.et-fixed-header #main-header',
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_header_and_navigation_fixed_nav_settings', 'extra_customizer_register_header_and_navigation_fixed_nav_settings', 1 );

function extra_customizer_register_header_and_navigation_header_elements_settings() {
	$settings = array(
		'show_header_social_icons' => array(
			'label'      => esc_html__( 'Show Social Icons', 'extra' ),
			'type'       => 'checkbox',
			'default'    => 'on',
			'value_bind' => array(
				'style'    => 'el_toggle',
				'selector' => extra_get_dynamic_selector( 'header_social_icons' ),
			),
		),
		'show_header_search'       => array(
			'label'      => esc_html__( 'Show Search Bar', 'extra' ),
			'type'       => 'checkbox',
			'default'    => 'on',
			'value_bind' => array(
				'style'    => 'el_toggle',
				'selector' => extra_get_dynamic_selector( 'header_search_field' ),
			),
		),
		'show_header_trending'     => array(
			'label'      => esc_html__( 'Show Trending Bar', 'extra' ),
			'type'       => 'checkbox',
			'default'    => 'on',
			'value_bind' => array(
				'style'    => 'class_toggle',
				'selector' => extra_get_dynamic_selector( 'top_navigation' ),
				'class'    => 'et-trending',
			),
		),
		'show_header_cart_total'   => array(
			'label'      => esc_html__( 'Show WooCommerce Cart', 'extra' ),
			'type'       => 'checkbox',
			'default'    => 'on',
			'value_bind' => array(
				'style'    => 'el_toggle',
				'selector' => extra_get_dynamic_selector( 'header_cart_total' ),
			),
		),
	);

	if ( ! class_exists( 'woocommerce' ) ) {
		unset( $settings['show_header_cart_total'] );
	}

	return $settings;
}

add_filter( 'extra_customizer_register_header_and_navigation_header_elements_settings', 'extra_customizer_register_header_and_navigation_header_elements_settings', 1 );

function extra_customizer_register_social_networks_settings( $settings ) {
	$settings = array();
	foreach ( extra_get_social_networks() as $social_network => $social_network_title ) {
		$settings[sprintf( '%s_url', $social_network )] = array(
			'label'             => sprintf( esc_html__( '%s URL', 'extra' ), $social_network_title ),
			'sanitize_callback' => 'esc_url_raw',
		);
	}
	return $settings;
}

add_filter( 'extra_customizer_register_social_networks_settings', 'extra_customizer_register_social_networks_settings' );

function extra_customizer_register_footer_layout_settings() {
	return array(
		'footer_columns' => array(
			'label'             => esc_html__( 'Column Layout', 'extra' ),
			'type'              => 'select',
			'default'           => '3',
			'sanitize_callback' => 'et_sanitize_footer_column',
			'choices'           => et_extra_footer_column_choices(),
			'value_bind'        => array(
				'style'    => 'class_toggle',
				'selector' => '#footer',
				'class'    => '_value_bind_to_value',
				'format'   => 'footer_columns_%value%',
			),
		),
	);
}

add_filter( 'extra_customizer_register_footer_layout_settings', 'extra_customizer_register_footer_layout_settings', 1 );

function extra_customizer_register_footer_typography_settings() {
	return array(
		'footer_heading_font_size'    => array(
			'label'       => esc_html__( 'Heading Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '14',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'footer_heading' ),
					),
				),
			),
		),
		'footer_heading_font_style'   => array(
			'label'      => esc_html__( 'Heading Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'default'    => 'bold|uppercase',
			'boldness'   => '600',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => extra_get_dynamic_selector( 'footer_heading' ),
				),
			),
		),
		'footer_font_size'            => array(
			'label'       => esc_html__( 'Body/Link Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '14',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'footer_body_and_links' ),
					),
				),
			),
		),
		'footer_line_height'          => array(
			'label'       => esc_html__( 'Body/Link Line Height', 'extra' ),
			'type'        => 'range',
			'default'     => '1.7',
			'input_attrs' => array(
				'min'  => 0.8,
				'max'  => 3,
				'step' => 0.1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'line-height',
						'format'    => '%value%em',
						'selectors' => extra_get_dynamic_selector( 'footer_body_and_links' ),
					),
				),
			),
		),
		'footer_font_style'           => array(
			'label'      => esc_html__( 'Body/Link Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => extra_get_dynamic_selector( 'footer_body_and_links' ),
				),
			),
		),
		'footer_widget_text_color'    => array(
			'label'      => esc_html__( 'Widget Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'footer_widget_body' ),
				),
			),
		),
		'footer_widget_link_color'    => array(
			'label'      => esc_html__( 'Widget Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'footer_widget_links' ),
				),
			),
		),
		'footer_widget_heading_color' => array(
			'label'      => esc_html__( 'Widget Heading Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'footer_heading' ),
				),
			),
		),
		'footer_widget_bullet_color'  => array(
			'label'      => esc_html__( 'Widget Bullet Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'.et_pb_widget ul li .children a:before',
						'.et_pb_widget .widget-list li .children a:before',
						'.widget.woocommerce .product_list_widget li .children a:before',
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_footer_typography_settings', 'extra_customizer_register_footer_typography_settings', 1 );

function extra_customizer_register_footer_footer_elements_settings() {
	return array(
		'show_footer_social_icons' => array(
			'label'      => esc_html__( 'Show Social Icons', 'extra' ),
			'type'       => 'checkbox',
			'default'    => 'on',
			'value_bind' => array(
				'style'    => 'el_toggle',
				'selector' => extra_get_dynamic_selector( 'footer_social_icons' ),
			),
		),
	);
}

add_filter( 'extra_customizer_register_footer_footer_elements_settings', 'extra_customizer_register_footer_footer_elements_settings', 1 );

function extra_customizer_register_footer_bottom_bar_settings() {
	return array(
		'footer_bottom_background_color'       => array(
			'label'      => esc_html__( 'Background Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.3)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => array(
						'#footer-bottom',
					),
				),
			),
		),
		'footer_bottom_credit_text_color'      => array(
			'label'      => esc_html__( 'Footer Credit Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#footer-info',
						'#footer-info a',
					),
				),
			),
		),
		'footer_bottom_credit_font_style'      => array(
			'label'      => esc_html__( 'Footer Credit Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => array(
						'#footer-info a',
					),
				),
			),
		),
		'footer_bottom_credit_font_size'       => array(
			'label'       => esc_html__( 'Footer Credit Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => 13,
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 15,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							'#footer-info a',
						),
					),
				),
			),
		),
		'footer_bottom_menu_link_color'        => array(
			'label'      => esc_html__( 'Footer Menu Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#footer-nav ul li a',
					),
				),
			),
		),
		'footer_bottom_menu_active_link_color' => array(
			'label'      => esc_html__( 'Footer Menu Active Link Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						'#footer-nav ul li a:hover',
						'#footer-nav ul li a:active',
					),
				),
			),
		),
		'footer_bottom_menu_letter_spacing'    => array(
			'label'       => esc_html__( 'Footer Menu Letter Spacing', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 8,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'letter-spacing',
						'format'    => '%value%px',
						'selectors' => array(
							'#footer-nav ul li a',
						),
					),
				),
			),
		),
		'footer_bottom_menu_font_size'         => array(
			'label'       => esc_html__( 'Footer Menu Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '14',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							'#footer-nav a',
						),
					),
				),
			),
		),
		'footer_bottom_menu_font_style'        => array(
			'label'      => esc_html__( 'Footer Menu Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => array(
						'#footer-nav a',
					),
				),
			),
		),
		'footer_bottom_social_icon_size'       => array(
			'label'       => esc_html__( 'Social Icons Size', 'extra' ),
			'type'        => 'range',
			'default'     => '16',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							extra_get_dynamic_selector( 'footer_social_icons_icon' ),
						),
					),
					array(
						'property'  => 'line-height',
						'format'    => '%value%px',
						'selectors' => array(
							extra_get_dynamic_selector( 'footer_social_icons_icon' ),
						),
					),
					array(
						'property'  => 'height',
						'format'    => '%value%px',
						'selectors' => array(
							extra_get_dynamic_selector( 'footer_social_icons_icon' ),
						),
					),
					array(
						'property'  => 'width',
						'format'    => '%value%px',
						'selectors' => array(
							extra_get_dynamic_selector( 'footer_social_icons_icon' ),
						),
					),
				),
			),
		),
		'footer_bottom_social_icon_color'      => array(
			'label'      => esc_html__( 'Social Icons Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(255,255,255,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => array(
						extra_get_dynamic_selector( 'footer_social_icons_icon', ':before' ),
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_footer_bottom_bar_settings', 'extra_customizer_register_footer_bottom_bar_settings', 1 );

function extra_customizer_register_buttons_styles_settings() {
	return array(
		'button_font_size'        => array(
			'label'       => esc_html__( 'Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '14',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons' ),
					),
				),
			),
		),
		'button_text_color'       => array(
			'label'      => esc_html__( 'Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'buttons' ),
				),
			),
		),
		'button_background_color' => array(
			'label'      => esc_html__( 'Background color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.1)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => extra_get_dynamic_selector( 'buttons' ),
				),
			),
		),
		'button_border_width'     => array(
			'label'       => esc_html__( 'Border Width', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 10,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'border-width',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons' ),
					),
				),
			),
		),
		'button_border_color'     => array(
			'label'      => esc_html__( 'Border Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'border-color' => extra_get_dynamic_selector( 'buttons' ),
				),
			),
		),
		'button_border_radius'    => array(
			'label'       => esc_html__( 'Border Radius', 'extra' ),
			'type'        => 'range',
			'default'     => 3,
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'border-radius',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons' ),
					),
				),
			),
		),
		'button_letter_spacing'   => array(
			'label'       => esc_html__( 'Letter Spacing', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 8,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'letter-spacing',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons' ),
					),
				),
			),
		),
		'button_font_style'       => array(
			'label'      => esc_html__( 'Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'default'    => 'bold|uppercase',
			'boldness'   => '600',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => extra_get_dynamic_selector( 'buttons' ),
				),
			),
		),
		'button_font'             => array(
			'label'      => esc_html__( 'Buttons Font', 'extra' ),
			'type'       => 'dropdown-fonts',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_get_google_font_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-family' => extra_get_dynamic_selector( 'buttons' ),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_buttons_styles_settings', 'extra_customizer_register_buttons_styles_settings', 1 );

function extra_customizer_register_buttons_hover_styles_settings() {
	return array(
		'button_hover_font_size'        => array(
			'label'       => esc_html__( 'Text Size', 'extra' ),
			'type'        => 'range',
			'default'     => '14',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons', ':hover' ),
					),
				),
			),
		),
		'button_hover_text_color'       => array(
			'label'      => esc_html__( 'Text Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.6)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'color' => extra_get_dynamic_selector( 'buttons', ':hover' ),
				),
			),
		),
		'button_hover_background_color' => array(
			'label'      => esc_html__( 'Background color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => 'rgba(0,0,0,0.2)',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'background-color' => extra_get_dynamic_selector( 'buttons', ':hover' ),
				),
			),
		),
		'button_hover_border_width'     => array(
			'label'       => esc_html__( 'Border Width', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 10,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'border-width',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons', ':hover' ),
					),
				),
			),
		),
		'button_hover_border_color'     => array(
			'label'      => esc_html__( 'Border Color', 'extra' ),
			'type'       => 'et_coloralpha',
			'default'    => '#ffffff',
			'value_bind' => array(
				'style'              => 'dynamic_selectors',
				'property_selectors' => array(
					'border-color' => extra_get_dynamic_selector( 'buttons', ':hover' ),
				),
			),
		),
		'button_hover_border_radius'    => array(
			'label'       => esc_html__( 'Border Radius', 'extra' ),
			'type'        => 'range',
			'default'     => 3,
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'border-radius',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons', ':hover' ),
					),
				),
			),
		),
		'button_hover_letter_spacing'   => array(
			'label'       => esc_html__( 'Letter Spacing', 'extra' ),
			'type'        => 'range',
			'default'     => 0,
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 8,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'letter-spacing',
						'format'    => '%value%px',
						'selectors' => extra_get_dynamic_selector( 'buttons', ':hover' ),
					),
				),
			),
		),
		'button_hover_font_style'       => array(
			'label'      => esc_html__( 'Font Style', 'extra' ),
			'type'       => 'dropdown-font-styles',
			'default'    => 'bold|uppercase',
			'boldness'   => '600',
			'value_bind' => array(
				'style'                    => 'dynamic_selectors_value_format_callback',
				'value_format_callback'    => 'extra_et_print_font_style_css_value',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					'font-style' => extra_get_dynamic_selector( 'buttons', ':hover' ),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_buttons_hover_styles_settings', 'extra_customizer_register_buttons_hover_styles_settings', 1 );

function extra_customizer_register_module_gallery_settings() {
	$css = 'et_builder_maybe_wrap_css_selector';

	return array(
		'et_pb_gallery-title_font_size'     => array(
			'label'       => esc_html__( 'Title Font Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_gallery-title_font_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 72,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title', false ),
						),
					),
				),
			),
		),
		'et_pb_gallery-caption_font_size'   => array(
			'label'       => esc_html__( 'Caption Font Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_gallery-caption_font_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption', false ),
						),
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_module_gallery_settings', 'extra_customizer_register_module_gallery_settings', 1 );

function extra_customizer_register_module_blurb_settings() {
	$css = 'et_builder_maybe_wrap_css_selector';

	return array(
		'et_pb_blurb-header_font_size' => array(
			'label'       => esc_html__( 'Header Font Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_blurb-header_font_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 72,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_blurb h4', false ),
						),
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_module_blurb_settings', 'extra_customizer_register_module_blurb_settings', 1 );

function extra_customizer_register_module_tabs_settings() {
	$css = 'et_builder_maybe_wrap_css_selector';

	return array(
		'et_pb_tabs-title_font_size'  => array(
			'label'       => esc_html__( 'Title Font Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_tabs-title_font_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_tabs_controls li', false ),
						),
					),
				),
			),
		),
		'et_pb_tabs-padding'          => array(
			'label'       => esc_html__( 'Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_tabs-padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                                 => 'dynamic_selectors_value_format_callback',
				'value_format_callback'                 => 'et_pb_module_tabs_padding_css_value',
				'use_formatted_value_as_css_expression' => true,
				'property_selectors'                    => array(
					'custom_padding' => array(),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_module_tabs_settings', 'extra_customizer_register_module_tabs_settings', 1 );

function extra_customizer_register_module_slider_settings() {
	$css = 'et_builder_maybe_wrap_css_selector';

	return array(
		'et_pb_slider-padding'           => array(
			'label'       => esc_html__( 'Top & Bottom Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_slider-padding', 'default' ),
			'input_attrs' => array(
				'min'  => 5,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                    => 'dynamic_selectors_value_format',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					array(
						'property'  => 'padding-top-bottom',
						'format'    => 'padding-top: %value%%; padding-bottom: %value%%;',
						'selectors' => array(
							$css( '.et_pb_slider_fullwidth_off .et_pb_slide_description', false ),
						),
					),
				),
			),
		),
		'et_pb_slider-header_font_size'  => array(
			'label'       => esc_html__( 'Header Font Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_slider-header_font_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 72,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_slider_fullwidth_off .et_pb_slide_description h2', false ),
						),
					),
				),
			),
		),
		'et_pb_slider-body_font_size'    => array(
			'label'       => esc_html__( 'Content Font Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_slider-body_font_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 32,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'font-size',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_slider_fullwidth_off .et_pb_slide_content', false ),
						),
					),
				),
			),
		),
	);
}

add_filter( 'extra_customizer_register_module_slider_settings', 'extra_customizer_register_module_slider_settings', 1 );

function extra_customizer_register_module_pricing_table_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_pricing_tables-header_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_pricing_heading h2', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_pricing_tables-subheader_font_size',
		'label'     => esc_html__( 'Subheader Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_best_value', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_pricing_tables-price_font_size',
		'label'     => esc_html__( 'Price Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 100,
		'selectors' => array(
			$css( '.et_pb_sum', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_pricing_table_settings', 'extra_customizer_register_module_pricing_table_settings', 1 );

function extra_customizer_register_module_call_to_action_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_cta-header_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 100,
		'selectors' => array(
			$css( '.et_pb_promo h2', false ),
		),
	) ) + array(
		'et_pb_cta-custom_padding' => array(
			'label'       => esc_html__( 'Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_cta-custom_padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                                 => 'dynamic_selectors_value_format_callback',
				'value_format_callback'                 => 'et_pb_module_cta_padding_css_value',
				'use_formatted_value_as_css_expression' => true,
				'property_selectors'                    => array(
					'custom_padding' => array(),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_call_to_action_settings', 'extra_customizer_register_module_call_to_action_settings', 1 );

function extra_customizer_register_module_audio_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_audio-title_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_audio_module_content h2', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_audio-caption_font_size',
		'label'     => esc_html__( 'Subheader Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_audio_module p', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_audio_settings', 'extra_customizer_register_module_audio_settings', 1 );

function extra_customizer_register_module_subscribe_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_signup-header_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_subscribe h2', false ),
		),
	) ) + array(
		'et_pb_signup-padding' => array(
			'label'       => esc_html__( 'Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_signup-padding' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'padding',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_subscribe', false ),
						),
					),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_subscribe_settings', 'extra_customizer_register_module_subscribe_settings', 1 );

function extra_customizer_register_module_login_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_login-header_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_login h2', false ),
		),
	) ) + array(
		'et_pb_login-padding' => array(
			'label'       => esc_html__( 'Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_login-custom_padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'padding',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_login', false ),
						),
					),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_login_settings', 'extra_customizer_register_module_login_settings', 1 );

function extra_customizer_register_module_portfolio_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_portfolio-title_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_portfolio .et_pb_portfolio_item h2', false ),
			$css( '.et_pb_fullwidth_portfolio .et_pb_portfolio_item h3', false ),
			$css( '.et_pb_portfolio_grid .et_pb_portfolio_item h2', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_portfolio-caption_font_size',
		'label'     => esc_html__( 'Caption Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_portfolio .et_pb_portfolio_item .post-meta', false ),
			$css( '.et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta', false ),
			$css( '.et_pb_portfolio_grid .et_pb_portfolio_item .post-meta', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_portfolio_settings', 'extra_customizer_register_module_portfolio_settings', 1 );

function extra_customizer_register_module_filterable_portfolio_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_filterable_portfolio-title_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_filterable_portfolio .et_pb_portfolio_item h2', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_filterable_portfolio-caption_font_size',
		'label'     => esc_html__( 'Caption Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_filterable_portfolio-filter_font_size',
		'label'     => esc_html__( 'Filter Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_filterable_portfolio_settings', 'extra_customizer_register_module_filterable_portfolio_settings', 1 );

function extra_customizer_register_module_bar_counter_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_counters-title_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_counters .et_pb_counter_title', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_counters-percent_font_size',
		'label'     => esc_html__( 'Percent Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_counters .et_pb_counter_amount', false ),
		),
	) ) + array(
		'et_pb_counters-padding'       => array(
			'label'       => esc_html__( 'Bar Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_counters-padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'padding',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_counter_amount', false ),
						),
					),
				),
			),
		)
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_bar_counter_settings', 'extra_customizer_register_module_bar_counter_settings', 1 );

function extra_customizer_register_module_circle_counter_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_circle_counter-number_font_size',
		'label'     => esc_html__( 'Number Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_circle_counter .percent p', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_circle_counter-title_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_circle_counter h3', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_circle_counter_settings', 'extra_customizer_register_module_circle_counter_settings', 1 );

function extra_customizer_register_module_number_counter_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_number_counter-number_font_size',
		'label'     => esc_html__( 'Number Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_number_counter .percent p', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_number_counter-title_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_number_counter h3', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_number_counter_settings', 'extra_customizer_register_module_number_counter_settings', 1 );

function extra_customizer_register_module_accordion_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_accordion-toggle_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_accordion .et_pb_toggle_title', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_accordion-toggle_icon_size',
		'label'     => esc_html__( 'Toggle Icon Size', 'extra' ),
		'type'      => 'range',
		'min'       => 16,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_accordion .et_pb_toggle_title:before', false ),
		),
	) ) + array(
		'et_pb_accordion-custom_padding' => array(
			'label'       => esc_html__( 'Toggle Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_accordion-custom_padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'padding',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_accordion .et_pb_toggle_open', false ),
							$css( '.et_pb_accordion .et_pb_toggle_close', false ),
						),
					),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_accordion_settings', 'extra_customizer_register_module_accordion_settings', 1 );

function extra_customizer_register_module_toggle_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_toggle-title_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_toggle.et_pb_toggle_item h5', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_toggle-toggle_icon_font_size',
		'label'     => esc_html__( 'Toggle Icon Size', 'extra' ),
		'type'      => 'range',
		'min'       => 16,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_toggle.et_pb_toggle_item .et_pb_toggle_title:before', false ),
		),
	) ) + array(
		'et_pb_toggle-custom_padding' => array(
			'label'       => esc_html__( 'Toggle Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_toggle-custom_padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'padding',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_toggle.et_pb_toggle_item', false ),
						),
					),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_toggle_settings', 'extra_customizer_register_module_toggle_settings', 1 );

function extra_customizer_register_module_contact_form_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_contact_form-title_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_contact_form_container .et_pb_contact_main_title', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_contact_form-form_field_font_size',
		'label'     => esc_html__( 'Input Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_contact_form_container .et_pb_contact p input', false ),
			$css( '.et_pb_contact_form_container .et_pb_contact p textarea', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_contact_form-captcha_font_size',
		'label'     => esc_html__( 'Captcha Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_contact_captcha_question', false ),
		),
	) ) + array(
		'et_pb_contact_form-padding' => array(
			'label'       => esc_html__( 'Input Field Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_contact_form-padding', 'default' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'              => 'dynamic_selectors_value_format',
				'property_selectors' => array(
					array(
						'property'  => 'padding',
						'format'    => '%value%px',
						'selectors' => array(
							$css( '.et_pb_contact p input', false ),
							$css( '.et_pb_contact p textarea', false ),
						),
					),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_contact_form_settings', 'extra_customizer_register_module_contact_form_settings', 1 );

function extra_customizer_register_module_sidebar_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_sidebar-header_font_size',
		'label'     => esc_html__( 'Widget Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_widget_area h4', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_sidebar_settings', 'extra_customizer_register_module_sidebar_settings', 1 );

function extra_customizer_register_module_person_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_team_member-header_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_team_member h4', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_team_member-subheader_font_size',
		'label'     => esc_html__( 'Subheader Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_team_member .et_pb_member_position', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_person_settings', 'extra_customizer_register_module_person_settings', 1 );

function extra_customizer_register_module_blog_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_blog-header_font_size',
		'label'     => esc_html__( 'Post Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_posts .et_pb_post h2', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_blog-meta_font_size',
		'label'     => esc_html__( 'Meta Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_posts .et_pb_post .post-meta', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_blog_settings', 'extra_customizer_register_module_blog_settings', 1 );

function extra_customizer_register_module_masonry_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_blog_masonry-header_font_size',
		'label'     => esc_html__( 'Title Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.et_pb_blog_grid .et_pb_post h2', false ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_blog_masonry-meta_font_size',
		'label'     => esc_html__( 'Meta Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			'.et_pb_blog_grid .et_pb_post .post-meta',
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_masonry_settings', 'extra_customizer_register_module_masonry_settings', 1 );

function extra_customizer_register_module_shop_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array();

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_shop-title_font_size',
		'label'     => esc_html__( 'Product Name Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.woocommerce', 'ul.products li.product .product-wrapper h3' ),
			$css( '.woocommerce-page', 'ul.products li.product h3' ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_shop-sale_badge_font_size',
		'label'     => esc_html__( 'Sale Badge Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.woocommerce', 'span.onsale' ),
			$css( '.woocommerce-page', 'span.onsale' ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_shop-price_font_size',
		'label'     => esc_html__( 'Price Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.woocommerce', 'ul.products li.product .price .amount' ),
			$css( '.woocommerce-page', 'ul.products li.product .price .amount' ),
		),
	) ) + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_shop-sale_price_font_size',
		'label'     => esc_html__( 'Sale Price Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 32,
		'selectors' => array(
			$css( '.woocommerce', 'ul.products li.product .price ins .amount' ),
			$css( '.woocommerce-page', 'ul.products li.product .price ins .amount' ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_shop_settings', 'extra_customizer_register_module_shop_settings', 1 );

function extra_customizer_register_module_social_follow_settings() {
	$settings = array(
		'et_pb_social_media_follow-icon_size' => array(
			'label'       => esc_html__( 'Follow Font & Icon Size', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_social_media_follow-icon_size', 'default' ),
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 72,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                                 => 'dynamic_selectors_value_format_callback',
				'value_format_callback'                 => 'et_pb_social_media_follow_font_size_css_value',
				'use_formatted_value_as_css_expression' => true,
				'property_selectors'                    => array(
					'custom' => array(),
				),
			),
		),
	);

	return $settings;
}

add_filter( 'extra_customizer_register_module_social_follow_settings', 'extra_customizer_register_module_social_follow_settings', 1 );

function extra_customizer_register_module_fullwidth_slider_settings() {
	$css      = 'et_builder_maybe_wrap_css_selector';
	$settings = array(
		'et_pb_fullwidth_slider-padding' => array(
			'label'       => esc_html__( 'Top & Bottom Padding', 'extra' ),
			'type'        => 'range',
			'default'     => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-padding', 'default' ),
			'input_attrs' => array(
				'min'  => 5,
				'max'  => 50,
				'step' => 1,
			),
			'value_bind'  => array(
				'style'                    => 'dynamic_selectors_value_format',
				'use_only_formatted_value' => true,
				'property_selectors'       => array(
					array(
						'property'  => 'padding-top-bottom',
						'format'    => 'padding-top: %value%%; padding-bottom: %value%%;',
						'selectors' => array(
							$css( '.et_pb_fullwidth_section .et_pb_slide_description', false ),
						),
					),
				),
			),
		),
	);

	$settings = $settings + extra_customizer_font_size_setting( array(
		'setting'   => 'et_pb_fullwidth_slider-header_font_size',
		'label'     => esc_html__( 'Header Font Size', 'extra' ),
		'type'      => 'range',
		'min'       => 10,
		'max'       => 72,
		'selectors' => array(
			$css( '.et_pb_fullwidth_section .et_pb_slide_description h2', false ),
		),
	) );

	return $settings;
}

add_filter( 'extra_customizer_register_module_fullwidth_slider_settings', 'extra_customizer_register_module_fullwidth_slider_settings', 1 );

function extra_customizer_settings( $set = 'theme' ) {
	$customizer_settings = array();

	$customizer_settings['theme'] = array(
		'general'               => array(
			'title'    => esc_html__( 'General Settings', 'extra' ),
			'priority' => 1,
			'sections' => array(
				'layout'     => array(
					'title' => esc_html__( 'Layout Settings', 'extra' ),
				),
				'typography' => array(
					'title' => esc_html__( 'Typography Settings', 'extra' ),
				),
				'background' => array(
					'title' => esc_html__( 'Background Settings', 'extra' ),
				),
			),
		),
		'header_and_navigation' => array(
			'title'    => esc_html__( 'Header & Navigation Settings', 'extra' ),
			'priority' => 2,
			'sections' => array(
				'header_format'   => array(
					'title' => esc_html__( 'Header Format Settings', 'extra' ),
				),
				'primary_nav'     => array(
					'title' => esc_html__( 'Primary Menu Bar Settings', 'extra' ),
				),
				'secondary_nav'   => array(
					'title' => esc_html__( 'Secondary Menu Bar Settings', 'extra' ),
				),
				'fixed_nav'       => array(
					'title' => esc_html__( 'Fixed Navigation Settings', 'extra' ),
				),
				'header_elements' => array(
					'title' => esc_html__( 'Header Elements Settings', 'extra' ),
				),
			),
		),
		'social_networks'       => array(
			'title'    => esc_html__( 'Social Media Links', 'extra' ),
			'priority' => 3,
			'sections' => array(
				'social_networks' => array(
					'title' => esc_html__( 'Social Media Networks', 'extra' ),
				),
			),
		),
		'footer'                => array(
			'title'    => esc_html__( 'Footer Settings', 'extra' ),
			'priority' => 4,
			'sections' => array(
				'layout'          => array(
					'title' => esc_html__( 'Layout', 'extra' ),
				),
				'typography'      => array(
					'title' => esc_html__( 'Typography', 'extra' ),
				),
				'footer_elements' => array(
					'title' => esc_html__( 'Footer Elements', 'extra' ),
				),
				'bottom_bar'      => array(
					'title' => esc_html__( 'Bottom Bar', 'extra' ),
				),
			),
		),
		'buttons'               => array(
			'title'    => esc_html__( 'Buttons', 'extra' ),
			'priority' => 5,
			'sections' => array(
				'styles'       => array(
					'title' => esc_html__( 'Button Styles', 'extra' ),
				),
				'hover_styles' => array(
					'title' => esc_html__( 'Button Hover Styles', 'extra' ),
				),
			),
		),
	);

	/**
	 * @since ??
	 *
	 * Module Customizer contains only CSS responsible for the font size options.
	 * The CSS is printed only if website has an unmigrated Module Customizer font size values into the
	 * new Global Defaults system. The values suppose to be migrated at the first time when Global Defaults
	 * changes are saved.
	 */
	$custom_defaults_unmigrated = et_get_option( 'builder_custom_defaults_unmigrated', false );

	if ( $custom_defaults_unmigrated ) {
		$customizer_settings['modules'] = array(
			'module_image'                => array(
				'sections' => array(
					'module_image' => array(
						'title' => esc_html__( 'Image', 'extra' ),
					),
				),
			),
			'module_gallery'              => array(
				'sections' => array(
					'module_gallery' => array(
						'title' => esc_html__( 'Gallery', 'extra' ),
					),
				),
			),
			'module_blurb'                => array(
				'sections' => array(
					'module_blurb' => array(
						'title' => esc_html__( 'Blurb', 'extra' ),
					),
				),
			),
			'module_tabs'                 => array(
				'sections' => array(
					'module_tabs' => array(
						'title' => esc_html__( 'Tabs', 'extra' ),
					),
				),
			),
			'module_slider'               => array(
				'sections' => array(
					'module_slider' => array(
						'title' => esc_html__( 'Slider', 'extra' ),
					),
				),
			),
			'module_testimonial'          => array(
				'sections' => array(
					'module_testimonial' => array(
						'title' => esc_html__( 'Testimonial', 'extra' ),
					),
				),
			),
			'module_pricing_table'        => array(
				'sections' => array(
					'module_pricing_table' => array(
						'title' => esc_html__( 'Pricing Table', 'extra' ),
					),
				),
			),
			'module_call_to_action'       => array(
				'sections' => array(
					'module_call_to_action' => array(
						'title' => esc_html__( 'Call To Action', 'extra' ),
					),
				),
			),
			'module_audio'                => array(
				'sections' => array(
					'module_audio' => array(
						'title' => esc_html__( 'Audio', 'extra' ),
					),
				),
			),
			'module_subscribe'            => array(
				'sections' => array(
					'module_subscribe' => array(
						'title' => esc_html__( 'Subscribe', 'extra' ),
					),
				),
			),
			'module_login'                => array(
				'sections' => array(
					'module_login' => array(
						'title' => esc_html__( 'Login', 'extra' ),
					),
				),
			),
			'module_portfolio'            => array(
				'sections' => array(
					'module_portfolio' => array(
						'title' => esc_html__( 'Portfolio', 'extra' ),
					),
				),
			),
			'module_filterable_portfolio' => array(
				'sections' => array(
					'module_filterable_portfolio' => array(
						'title' => esc_html__( 'Filterable Portfolio', 'extra' ),
					),
				),
			),
			'module_bar_counter'          => array(
				'sections' => array(
					'module_bar_counter' => array(
						'title' => esc_html__( 'Bar Counter', 'extra' ),
					),
				),
			),
			'module_circle_counter'       => array(
				'sections' => array(
					'module_circle_counter' => array(
						'title' => esc_html__( 'Circle Counter', 'extra' ),
					),
				),
			),
			'module_number_counter'       => array(
				'sections' => array(
					'module_number_counter' => array(
						'title' => esc_html__( 'Number Counter', 'extra' ),
					),
				),
			),
			'module_accordion'            => array(
				'sections' => array(
					'module_accordion' => array(
						'title' => esc_html__( 'Accordion', 'extra' ),
					),
				),
			),
			'module_toggle'               => array(
				'sections' => array(
					'module_toggle' => array(
						'title' => esc_html__( 'Toggle', 'extra' ),
					),
				),
			),
			'module_contact_form'         => array(
				'sections' => array(
					'module_contact_form' => array(
						'title' => esc_html__( 'Contact Form', 'extra' ),
					),
				),
			),
			'module_sidebar'              => array(
				'sections' => array(
					'module_sidebar' => array(
						'title' => esc_html__( 'Sidebar', 'extra' ),
					),
				),
			),
			'module_divider'              => array(
				'sections' => array(
					'module_divider' => array(
						'title' => esc_html__( 'Divider', 'extra' ),
					),
				),
			),
			'module_person'               => array(
				'sections' => array(
					'module_person' => array(
						'title' => esc_html__( 'Person', 'extra' ),
					),
				),
			),
			'module_blog'                 => array(
				'sections' => array(
					'module_blog' => array(
						'title' => esc_html__( 'Blog', 'extra' ),
					),
				),
			),
			'module_masonry'              => array(
				'sections' => array(
					'module_masonry' => array(
						'title' => esc_html__( 'Masonry', 'extra' ),
					),
				),
			),
			'module_shop'                 => array(
				'sections' => array(
					'module_shop' => array(
						'title' => esc_html__( 'Shop', 'extra' ),
					),
				),
			),
			'module_countdown_timer'      => array(
				'sections' => array(
					'module_countdown_timer' => array(
						'title' => esc_html__( 'Countdown', 'extra' ),
					),
				),
			),
			'module_social_follow'        => array(
				'sections' => array(
					'module_social_follow' => array(
						'title' => esc_html__( 'Social Follow', 'extra' ),
					),
				),
			),
			'module_fullwidth_slider'     => array(
				'sections' => array(
					'module_fullwidth_slider' => array(
						'title' => esc_html__( 'Fullwidth Slider', 'extra' ),
					),
				),
			),
		);
	}


	if ( 'all' === $set ) {
		return $customizer_settings;
	}

	return isset( $customizer_settings[ $set ] ) ? $customizer_settings[ $set ] : array();
}

function extra_customize_register( $wp_customize ) {	
	global $wp_version;

	// Get WP major version
	$wp_major_version = substr( $wp_version, 0, 3 );

	$wp_customize->remove_section( 'colors' );
	$wp_customize->register_control_type( 'ET_Color_Alpha_Control' );

	if ( version_compare( $wp_major_version, '4.9', '>=' ) ) {
		wp_register_script( 'wp-color-picker-alpha', get_template_directory_uri() . '/includes/builder/scripts/ext/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ) );
		wp_localize_script( 'wp-color-picker-alpha', 'et_pb_color_picker_strings', apply_filters( 'et_pb_color_picker_strings_builder', array(
			'legacy_pick'    => esc_html__( 'Select', 'et_builder' ),
			'legacy_current' => esc_html__( 'Current Color', 'et_builder' ),
		) ) );
	} else {
		wp_register_script( 'wp-color-picker-alpha', get_template_directory_uri() . '/includes/builder/scripts/ext/wp-color-picker-alpha-48.min.js', array( 'jquery', 'wp-color-picker' ) );
	}

	$option_set_name           = 'et_customizer_option_set';
	$option_set_allowed_values = apply_filters( 'et_customizer_option_set_allowed_values', array( 'module', 'theme' ) );

	// init global settings class to apply default values in customizer properly
	et_builder_init_global_settings();

	/**
	 * Set a transient,
	 * if 'et_customizer_option_set' query parameter is set to one of the allowed values
	 */
	if ( isset( $_GET[ $option_set_name ] ) && in_array( $_GET[ $option_set_name ], $option_set_allowed_values ) ) {
		$customizer_option_set = $_GET[ $option_set_name ];

		set_transient( 'et_extra_customizer_option_set', $customizer_option_set, DAY_IN_SECONDS );
	}

	$options = extra_customizer_settings();

	foreach ( $options as $_panel => $panel_options ) {
		$panel = '';

		if ( count( $panel_options['sections'] ) > 1 ) {
			$panel = 'extra_' . $_panel;

			$wp_customize->add_panel( $panel, array(
				'title'    => $panel_options['title'],
				'priority' => $panel_options['priority'],
			) );
		}

		foreach ( $panel_options['sections'] as $section => $section_options ) {

			$section_settings = !empty( $section_options['settings'] ) ? $section_options['settings'] : array();
			unset( $section_options['settings'] );

			if ( !empty( $panel ) ) {
				$section_settings = apply_filters( 'extra_customizer_register_' . $_panel . '_' . $section . '_settings', $section_settings );
				$section = 'et_extra_' . $_panel . '_' . $section . '_settings';
			} else {
				$section_settings = apply_filters( 'extra_customizer_register_' . $section . '_settings', $section_settings );
				$section = 'et_extra_' . $section . '_settings';
			}

			$section_settings = apply_filters( 'extra_customizer_register_settings', $section_settings, $section, $panel );

			et_register_customizer_section( $wp_customize, $section_settings, $section, $section_options, $panel );
		}
	}
}

add_action( 'customize_register', 'extra_customize_register', 15 );

function extra_customize_static_front_page_options_register( $wp_customize ) {
	$wp_customize->remove_control( 'show_on_front' );
	$wp_customize->add_control( 'show_on_front', array(
		'label'    => esc_html__( 'Front page displays', 'extra' ),
		'section'  => 'static_front_page',
		'type'     => 'radio',
		'priority' => 5,
		'choices'  => array(
			'posts'  => esc_html__( 'Your latest posts', 'extra' ),
			'page'   => esc_html__( 'A static page', 'extra' ),
			'layout' => esc_html__( 'An Extra Category Layout', 'extra' ),
		),
	) );

	$layouts_query = extra_get_layouts(array(
		'posts_per_page' => -1,
		'nopaging'       => true,
		'post_status'    => 'publish',
	));

	$layouts = array();

	if ( $layouts_query->posts ) {
		foreach ( $layouts_query->posts as $post ) {
			$layouts[$post->ID] = $post->post_title;
		}
	}

	wp_reset_postdata();

	$wp_customize->add_setting( 'show_on_front_layout', array(
		'type'       => 'hook',
		'capability' => 'manage_options',
	) );

	$wp_customize->add_control( 'show_on_front_layout', array(
		'label'   => esc_html__( 'Extra Category Layout', 'extra' ),
		'section' => 'static_front_page',
		'type'    => 'select',
		'choices' => $layouts,
	) );
}

add_action( 'customize_register', 'extra_customize_static_front_page_options_register' );

function extra_save_show_on_front_layout() {
	global $wp_customize;

	if ( is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview() ) {
		$show_on_front_layout = $wp_customize->get_setting( 'show_on_front_layout' )->post_value();
		$show_on_front = $wp_customize->get_setting( 'show_on_front' )->post_value( get_option( 'show_on_front' ) );

		$args = array(
			'meta_key'       => EXTRA_HOME_LAYOUT_META_KEY,
			'meta_value'     => 1,
			'posts_per_page' => 1,
		);

		$layouts = extra_get_layouts( $args );
		wp_reset_postdata();

		if ( !empty( $layouts->post ) ) {
			delete_post_meta( $layouts->post->ID, EXTRA_HOME_LAYOUT_META_KEY );
		}

		if ( $show_on_front == 'layout' && !empty( $show_on_front_layout ) ) {
			update_post_meta( absint( $show_on_front_layout ), EXTRA_HOME_LAYOUT_META_KEY, 1 );
		}

	}
}

add_action( "customize_save_show_on_front_layout", 'extra_save_show_on_front_layout' );
add_action( "customize_save_show_on_front", 'extra_save_show_on_front_layout' );

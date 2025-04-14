<?php

class ET_Module_Customizer_Migrations {
	protected static $_instance;

	protected static $_migration_map = array(
		// Font Styles
		'author_name_font_style'     => array(
			'name'   => array(
				'default' => 'author_font',
			),
			'method' => '_migrate_font_style',
		),
		'author_details_font_style'  => array(
			'name'   => array(
				'default' => array(
					'position_font',
					'company_font',
				),
			),
			'method' => '_migrate_font_style',
		),
		'body_font_style'            => array(
			'name'   => array(
				'default' => 'body_font',
			),
			'method' => '_migrate_font_style',
		),
		'caption_font_style'         => array(
			'name'   => array(
				'default' => 'caption_font',
			),
			'method' => '_migrate_font_style',
		),
		'captcha_font_style'         => array(
			'name'   => array(
				'default' => 'captcha_font',
			),
			'method' => '_migrate_font_style',
		),
		'filter_font_style'          => array(
			'name'   => array(
				'default' => 'filter_font',
			),
			'method' => '_migrate_font_style',
		),
		'form_field_font_style'      => array(
			'name'   => array(
				'default' => 'form_field_font',
			),
			'method' => '_migrate_font_style',
		),
		'header_font_style'          => array(
			'name'   => array(
				'default' => 'header_font',
			),
			'method' => '_migrate_font_style',
		),
		'inactive_title_font_style'  => array(
			'name'   => array(
				'default' => 'closed_title_font',
			),
			'method' => '_migrate_font_style',
		),
		'inactive_toggle_font_style' => array(
			'name'   => array(
				'default' => 'closed_toggle_font',
			),
			'method' => '_migrate_font_style',
		),
		'meta_font_style'            => array(
			'name'   => array(
				'default' => 'meta_font',
			),
			'method' => '_migrate_font_style',
		),
		'number_font_style'          => array(
			'name'   => array(
				'default' => 'number_font',
			),
			'method' => '_migrate_font_style',
		),
		'title_font_style'           => array(
			'name'   => array(
				'et_pb_tabs' => 'tab_font',
				'default'    => 'title_font',
			),
			'method' => '_migrate_font_style',
		),
		'percent_font_style'         => array(
			'name'   => array(
				'default' => 'percent_font',
			),
			'method' => '_migrate_font_style',
		),
		'price_font_style'           => array(
			'name'   => array(
				'default' => 'price_font',
			),
			'method' => '_migrate_font_style',
		),
		'sale_badge_font_style'      => array(
			'name'   => array(
				'default' => 'sale_badge_font',
			),
			'method' => '_migrate_font_style',
		),
		'sale_price_font_style'      => array(
			'name'   => array(
				'default' => 'sale_price_font',
			),
			'method' => '_migrate_font_style',
		),
		'subheader_font_style'       => array(
			'name'   => array(
				'et_pb_team_member' => 'position_font',
				'default'           => 'subheader_font',
			),
			'method' => '_migrate_font_style',
		),
		'toggle_font_style'          => array(
			'name'   => array(
				'default' => 'toggle_font',
			),
			'method' => '_migrate_font_style',
		),

		// Font Sizes
		'header_font_size'           => array(
			'name'     => array(
				'default' => 'header_font_size',
			),
			'skip_for' => array(
				'et_pb_countdown_timer'   => '',
			),
		),
		'title_font_size'            => array(
			'name' => array(
				'et_pb_tabs' => 'tab_font_size',
			),
		),
		'toggle_icon_size'           => array(
			'name'   => array(
				'et_pb_accordion' => 'icon_font_size',
				'et_pb_toggle'    => 'icon_font_size',
			),
			'method' => '_migrate_px_value',
		),
		'icon_size'                  => array(
			'name'   => array(
				'et_pb_social_media_follow' => 'icon_font_size',
			),
			'method' => '_migrate_px_value',
		),
		'subheader_font_size'        => array(
			'name' => array(
				'et_pb_team_member' => 'position_font_size',
			),
		),

		// Border radii
		'border_radius'              => array(
			'name'   => array(
				'default' => 'border_radii',
			),
			'method' => '_migrate_border_radii',
		),
		'portrait_border_radius'     => array(
			'name'   => array(
				'default' => 'border_radii_portrait',
			),
			'method' => '_migrate_border_radii',
		),

		// Padding
		'padding'                    => array(
			'name'     => array(
				'et_pb_contact_form' => 'form_field_custom_padding',
				'default'            => 'custom_padding'
			),
			'method'   => array(
				'et_pb_slider'           => '_migrate_slider_padding',
				'et_pb_cta'              => '_migrate_cta_padding',
				'et_pb_contact_form'     => '_migrate_padding',
				'et_pb_counters'         => '_migrate_padding',
				'et_pb_login'            => '_migrate_padding',
				'et_pb_fullwidth_slider' => '_migrate_slider_padding',
			),
			'skip_for' => array(
				'et_pb_tabs'   => '',
				'et_pb_signup' => '',
			),
		),
		'custom_padding'             => array(
			'name'   => array(
				'default' => 'custom_padding'
			),
			'method' => array(
				'et_pb_cta'       => '_migrate_cta_padding',
				'et_pb_accordion' => '_migrate_padding',
				'et_pb_toggle'    => '_migrate_padding',
			),
		),

		// All other specific
		'remove_border'              => array(
			'name'   => array(
				'default' => 'show_border',
			),
			'method' => '_migrate_inverted_checkbox_value',
		),
		'divider_height'                     => array(
			'name'   => array(
				'et_pb_divider' => 'height',
			),
			'method' => '_migrate_px_value',
		),
		'divider_position'           => array(
			'skip_for'   => array(
				'et_pb_divider' => '',
			),
		),
	);

	protected static $_modules_to_skip = array();

	protected static $_migration_aliases = array(
		'et_pb_slider'           => array(
			'et_pb_post_slider',
		),
		'et_pb_fullwidth_slider' => array(
			'et_pb_fullwidth_post_slider',
		),
		'et_pb_portfolio'        => array(
			'et_pb_fullwidth_portfolio',
		),
	);

	protected static $_module_overrides = array(
		'et_pb_blog_masonry' => 'et_pb_blog',
	);

	protected static $_global_settings_overrides = array(
		'et_pb_divider-height' => 'et_pb_divider-divider_height',
	);

	protected static $_additional_settings = array(
		// Migrate blog grid option over
		'et_pb_blog_masonry-header_font_size'  => '',
		'et_pb_blog_masonry-header_font_style' => '',
		'et_pb_blog_masonry-meta_font_size'    => '',
		'et_pb_blog_masonry-meta_font_style'   => '',

		// The padding for the Login module stored a different way
		'et_pb_login-padding'                  => '',
	);

	protected static $_migrate_with = array(
		'toggle_icon_size' => array(
			'use_icon_font_size' => 'on',
		),
		'icon_size'        => array(
			'use_icon_font_size' => 'on',
		),
	);

	protected function __construct() {
	}

	/**
	 * Returns instance of the singleton class
	 *
	 * @since ??
	 *
	 * @return ET_Module_Customizer_Migrations
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Migrates Module Customizer settings to Custom Defaults
	 *
	 * @since ??
	 *
	 * @param array $defaults - The list of modules default settings
	 *
	 * @return array          - The list of migrated and unmigrated settings
	 */
	public function migrate( $defaults ) {
		// The migration of the Module Customizer settings consists of two phases.
		// While the first phase all not defaults Module Customizer settings will be migrated except the Font Size options.
		// If the Modules Customizer has any not default Font Size setting, the phase two will be executed at the moment when
		// someone will try to save Global Defaults settings. In this case a special text will be shown warning about a slight text
		// size changes in 1/3 and 1/4 columns.
		$custom_defaults            = array();
		$custom_defaults_unmigrated = array();
		$iterate_over               = array( $defaults, self::$_additional_settings );
		foreach ( $iterate_over as $_defaults ) {
			foreach ( $_defaults as $setting => $value ) {
				if ( isset( self::$_global_settings_overrides[ $setting ] ) ) {
					$customizer_value = (string) et_get_option( self::$_global_settings_overrides[ $setting ], '', '', true );
				} else {
					$customizer_value = (string) et_get_option( $setting, '', '', true );
				}
				if ( '' !== $customizer_value && $value !== $customizer_value && false !== $customizer_value ) {
					$module_setting = explode( '-', $setting );

					if ( ! isset( $module_setting[1] ) ) {
						continue;
					}

					$module_name    = $module_setting[0];
					$setting_name   = $module_setting[1];

					if ( in_array( $setting_name, ET_Builder_Global_Presets_Settings::$phase_two_settings ) ) {
						$active_array = &$custom_defaults_unmigrated;
					} else {
						$active_array = &$custom_defaults;
					}

					if ( array_key_exists( $module_name, self::$_modules_to_skip ) ) {
						continue;
					}

					if ( array_key_exists( $module_name, self::$_module_overrides ) ) {
						$module_name = self::$_module_overrides[ $module_name ];
					}

					if ( ! isset( $active_array[ $module_name ] ) ) {
						$active_array[ $module_name ] = array();
					}

					if ( array_key_exists( $setting_name, self::$_migration_map ) ) {
						if ( isset( self::$_migration_map[ $setting_name ]['skip_for'][ $module_name ] ) ) {
							continue;
						}
						if ( array_key_exists( 'method', self::$_migration_map[ $setting_name ] ) ) {
							if ( is_array( self::$_migration_map[ $setting_name ]['method'] ) ) {
								$customizer_value = call_user_func_array(
									array( $this, self::$_migration_map[ $setting_name ]['method'][ $module_name ] ),
									array( $customizer_value )
								);
							} else {
								$customizer_value = call_user_func_array(
									array( $this, self::$_migration_map[ $setting_name ]['method'] ),
									array( $customizer_value )
								);
							}
						}

						if ( array_key_exists( $setting_name, self::$_migrate_with ) ) {
							foreach ( self::$_migrate_with[ $setting_name ] as $extra_setting => $extra_value ) {
								$active_array[ $module_name ][ $extra_setting ] = $extra_value;
							}
						}

						if ( array_key_exists( $module_name, self::$_migration_map[ $setting_name ]['name'] ) ) {
							$setting_name = self::$_migration_map[ $setting_name ]['name'][ $module_name ];
						} else if ( array_key_exists( 'default', self::$_migration_map[ $setting_name ]['name'] ) ) {
							$setting_name = self::$_migration_map[ $setting_name ]['name']['default'];
						}
					}

					if ( is_array( $setting_name ) ) {
						foreach ( $setting_name as $setting_value ) {
							$active_array[ $module_name ][ $setting_value ] = (string) $customizer_value;
						}
					} else {
						$active_array[ $module_name ][ $setting_name ] = (string) $customizer_value;
						if ( array_key_exists( $module_name, self::$_migration_aliases ) ) {
							foreach ( self::$_migration_aliases[ $module_name ] as $alias ) {
								$active_array[ $alias ][ $setting_name ] = (string) $customizer_value;
							}
						}
					}

				}
			}
		}

		// prepare to store custom defaults as stdClass
		foreach ( $custom_defaults as &$module_settings ) {
			$module_settings = (object) $module_settings;
		}

		foreach ( $custom_defaults_unmigrated as &$module_settings ) {
			$module_settings = (object) $module_settings;
		}

		return array(
			$custom_defaults,
			$custom_defaults_unmigrated,
		);
	}

	/**
	 * Migrates customizer font style value.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_font_style( $value ) {
		$result_array   = array_fill( 0, 9, '' );
		$styles_array   = explode( '|', $value );
		$conversion_map = array(
			'bold'      => array(
				'position' => 1,
				'value'    => '700'
			),
			'italic'    => array(
				'position' => 2,
				'value'    => 'on'
			),
			'uppercase' => array(
				'position' => 3,
				'value'    => 'on'
			),
			'underline' => array(
				'position' => 4,
				'value'    => 'on'
			),
		);

		foreach ( $styles_array as $style ) {
			if ( array_key_exists( $style, $conversion_map ) ) {
				$position                  = $conversion_map[ $style ]['position'];
				$converted_value           = $conversion_map[ $style ]['value'];
				$result_array[ $position ] = $converted_value;
			}
		}

		$result = implode( '|', $result_array );

		return $result;
	}

	/**
	 * Migrates customizer padding value for the Tabs Module.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_padding( $value ) {
		return "{$value}px|{$value}px|{$value}px|{$value}px|true|true";
	}

	/**
	 * Migrates customizer padding value for the Tabs Module.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_tabs_padding( $value ) {
		$left_right_padding = (int) $value + 30;
		$top_bottom_padding = ceil( $value * 0.8 + 24 );

		return "{$top_bottom_padding}px|{$left_right_padding}px|{$top_bottom_padding}px|{$left_right_padding}px|true|true";
	}

	/**
	 * Migrates customizer padding value for the Slider Module types.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_slider_padding( $value ) {
		return "{$value}%||{$value}%||true";
	}

	/**
	 * Migrates customizer padding value for the Call To Action Module types.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_cta_padding( $value ) {
		$top_bottom_padding = (int) $value;
		$left_right_padding = ceil( $value * 1.5 );

		return "{$top_bottom_padding}px|{$left_right_padding}px|{$top_bottom_padding}px|{$left_right_padding}px|true|true";
	}

	/**
	 * Migrates customizer border radius values.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_border_radii( $value ) {
		return "on|{$value}px|{$value}px|{$value}px|{$value}px";
	}

	/**
	 * Migrates customizer inverted checkbox values.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_inverted_checkbox_value( $value ) {
		return $value ? 'off' : 'on';
	}

	/**
	 * Migrates customizer range control value.
	 *
	 * @since ??
	 *
	 * @param string $value - The value to be migrated.
	 *
	 * @return string
	 */
	protected function _migrate_px_value( $value ) {
		return "{$value}px";
	}
}

ET_Module_Customizer_Migrations::instance();

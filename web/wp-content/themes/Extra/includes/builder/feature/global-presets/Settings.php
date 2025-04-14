<?php

class ET_Builder_Global_Presets_Settings {
	const CUSTOM_DEFAULTS_OPTION            = 'builder_custom_defaults';
	const CUSTOM_DEFAULTS_UNMIGRATED_OPTION = 'builder_custom_defaults_unmigrated';
	const CUSTOMIZER_SETTINGS_MIGRATED_FLAG = 'customizer_settings_migrated_flag';
	const PRESETS_STORAGE_MIGRATED_FLAG     = 'presets_storage_migrated_flag';

	const GLOBAL_PRESETS_OPTION_LEGACY  = 'builder_global_presets';
	const GLOBAL_PRESETS_OPTION         = 'builder_global_presets_ng';
	const GLOBAL_PRESETS_OPTION_TEMP    = 'builder_global_presets_temp';
	const CUSTOM_DEFAULTS_MIGRATED_FLAG = 'custom_defaults_migrated_flag';
	const MODULE_PRESET_ATTRIBUTE       = '_module_preset';
	const MODULE_INITIAL_PRESET_ID      = '_initial';

	/**
	 * @var array - The list of the product short names we allowing to do a Module Customizer settings migration rollback.
	 */
	public static $allowed_products = array(
		'customizer_settings' => array(
			'divi'  => '4.5',
			'extra' => '4.5',
		),
		'storage_migration'   => array(
			'divi'         => '4.19.2',
			'extra'        => '4.19.2',
			'divi-builder' => '4.19.2',
		),
	);

	// Migration phase two settings
	public static $phase_two_settings = array(
		'body_font_size',
		'captcha_font_size',
		'caption_font_size',
		'filter_font_size',
		'form_field_font_size',
		'header_font_size',
		'meta_font_size',
		'number_font_size',
		'percent_font_size',
		'price_font_size',
		'sale_badge_font_size',
		'sale_price_font_size',
		'subheader_font_size',
		'title_font_size',
		'toggle_font_size',
		'icon_size',
		'padding',
		'custom_padding',
	);

	protected static $_module_additional_slugs = array(
		'et_pb_section' => array(
			'et_pb_section_fullwidth',
			'et_pb_section_specialty',
		),
		'et_pb_slide'   => array(
			'et_pb_slide_fullwidth',
		),
		'et_pb_column'  => array(
			'et_pb_column_specialty',
		),
	);

	protected static $_module_types_conversion_map = array(
		'et_pb_section'      => '_convert_section_type',
		'et_pb_column'       => '_convert_column_type',
		'et_pb_column_inner' => '_convert_column_type',
		'et_pb_slide'        => '_convert_slide_type',
	);

	protected static $_module_import_types_conversion_map = array(
		'et_pb_section_specialty' => 'et_pb_section',
		'et_pb_section_fullwidth' => 'et_pb_section',
		'et_pb_column_inner'      => 'et_bp_column',
		'et_pb_slide_fullwidth'   => 'et_pb_slide',
		'et_pb_column_specialty'  => 'et_pb_column',
	);

	protected static $_instance;
	protected $_settings;

	protected function __construct() {
		$this->_migrate_presets_storage();

		// Get option from product setting (last attr in args list).
		$global_presets = et_get_option( self::GLOBAL_PRESETS_OPTION, (object) array(), '', true, false, '', '', true );

		$this->_settings = $this->_normalize_global_presets( $global_presets );

		$this->_register_hooks();
	}

	/**
	 * Migrates global presets into a separate setting.
	 *
	 * @since 4.19.3
	 *
	 * @return void
	 */
	protected function _migrate_presets_storage() {
		if ( self::is_presets_storage_migrated() ) {
			return;
		}

		$global_presets_legacy = et_get_option( self::GLOBAL_PRESETS_OPTION_LEGACY, array(), '', true );
		// Get option from product setting (last attr in args list).
		$global_presets_ng = et_get_option( self::GLOBAL_PRESETS_OPTION, array(), '', true, false, '', '', true );

		// Nothing to migrate or presets already exist in new storage.
		if ( empty( $global_presets_legacy ) || ! empty( $global_presets_ng ) ) {
			et_update_option( self::PRESETS_STORAGE_MIGRATED_FLAG, true );
			return;
		}

		$global_presets_legacy_fixed = self::_fix_presets_before_migration( $global_presets_legacy );

		// Update option for product setting (last attr in args list).
		et_update_option( self::GLOBAL_PRESETS_OPTION, (object) $global_presets_legacy_fixed, false, '', '', true );

		$global_presets_ng_migrated = et_get_option( self::GLOBAL_PRESETS_OPTION, array(), '', true, false, '', '', true );

		// Remove old option if presets migrated.
		if ( ! empty( $global_presets_ng_migrated ) ) {
			et_update_option( self::PRESETS_STORAGE_MIGRATED_FLAG, true );
			// Remove legacy presets from settings.
			et_delete_option( self::GLOBAL_PRESETS_OPTION_LEGACY );
		}
	}

	/**
	 * Fix global colors in Global Presets.
	 * Clean up global_colors_info array which may contain duplicates.
	 *
	 * @since 4.19.3
	 *
	 * @param object|array $presets The object representing Global Presets settings.
	 *
	 * @return object
	 */
	protected function _fix_presets_before_migration( $presets ) {
		$result = $presets;

		foreach ( $presets as $module => $preset_structure ) {
			if ( isset( $preset_structure->presets ) ) {
				foreach ( $preset_structure->presets as $preset_id => $preset ) {
					if ( isset( $preset->settings ) ) {
						// Look for settings in this module that use global colors.
						if ( isset( $preset->settings->global_colors_info ) ) {
							$module_global_colors_info = json_decode( $preset->settings->global_colors_info, true );
						} else {
							// Nothing more to be done here if this module's `global_colors_info` setting is empty,
							// so advance the `$preset_structure->presets as $preset_id => $preset` loop.
							continue;
						}

						$fixed_global_colors_info = array();

						foreach ( $module_global_colors_info as $gcid => $settings_that_use_this_gcid_raw ) {
							if ( empty( $settings_that_use_this_gcid_raw ) ) {
								continue;
							}

							// Fix possible issue with duplicated gc in global_colors_info field.
							$settings_that_use_this_gcid       = array_values( array_unique( $settings_that_use_this_gcid_raw ) );
							$fixed_global_colors_info[ $gcid ] = $settings_that_use_this_gcid;
						}

						// Insert fixed global_colors_info into preset settings.
						if ( ! empty( $fixed_global_colors_info ) ) {
							$result->$module->presets->$preset_id->settings->global_colors_info = wp_json_encode( $fixed_global_colors_info );
						}
					}
				}
			}
		}

		return $result;
	}

	protected function _register_hooks() {
		add_action( 'et_after_version_rollback', array( $this, 'after_version_rollback' ), 10, 3 );

		// If migration is needed, ensure that all modules get fully loaded.
		// phpcs:disable PEAR.Functions.FunctionCallSignature -- Anonymous functions.
		add_action( 'et_builder_framework_loaded', function() {
			if ( ! self::are_custom_defaults_migrated() ) {
				add_filter( 'et_builder_should_load_all_module_data', '__return_true' );
			}
		});
		// phpcs:enable

		add_action( 'et_builder_ready', array( $this, 'migrate_custom_defaults' ), 100 );
		add_action( 'et_builder_ready', array( $this, 'apply_attribute_migrations' ), 101 );
	}

	/**
	 * Returns instance of the singleton class
	 *
	 * @since 4.5.0
	 *
	 * @return ET_Builder_Global_Presets_Settings
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Returns the list of additional module slugs used to separate Global Presets settings.
	 * For example defaults for sections must be separated depends on the section type (regular, fullwidth or specialty).
	 *
	 * @since 4.5.0
	 *
	 * @param $module_slug - The module slug for which additional slugs are looked up.
	 *
	 * @return array       - The list of the additional slugs.
	 */
	public function get_module_additional_slugs( $module_slug ) {
		if ( ! empty( self::$_module_additional_slugs[ $module_slug ] ) ) {
			return self::$_module_additional_slugs[ $module_slug ];
		}

		return array();
	}

	/**
	 * Returns builder Global Presets settings.
	 *
	 * @since 4.5.0
	 *
	 * @return object
	 */
	public function get_global_presets() {
		return $this->_settings;
	}

	/**
	 * Returns builder Temp Presets settings.
	 *
	 * @since 4.17.0
	 *
	 * @return object
	 */
	public function get_temp_presets() {
		$global_presets_temp = et_get_option( self::GLOBAL_PRESETS_OPTION_TEMP, array(), '', true );

		return $global_presets_temp;
	}

	/**
	 * Remove Temp Presets settings from the database.
	 *
	 * @since 4.17.0
	 *
	 * @return object
	 */
	public function clear_temp_presets() {
		$all_presets = self::get_global_presets();
		$temp_preset = self::get_temp_presets();

		if ( empty( $temp_preset ) ) {
			return;
		}

		foreach ( $temp_preset as $module => $preset_structure ) {
			if ( isset( $preset_structure['presets'] ) ) {
				foreach ( $preset_structure['presets'] as $preset_id => $preset ) {
					if ( isset( $all_presets->$module->presets->$preset_id ) ) {
						unset( $all_presets->$module->presets->$preset_id );
					}
				}
			}
		}

		// Save presets without temp.
		et_update_option( self::GLOBAL_PRESETS_OPTION, $all_presets, false, '', '', true );

		// Clean all temp presets.
		et_update_option( self::GLOBAL_PRESETS_OPTION_TEMP, array() );
	}

	/**
	 * Checks if the gives preset ID exists
	 *
	 * @since 4.5.0
	 *
	 * @param string $module_slug
	 * @param string $preset_id
	 *
	 * @return bool
	 */
	protected function is_module_preset_exist( $module_slug, $preset_id ) {
		return isset( $this->_settings->{$module_slug}->presets->{$preset_id} );
	}

	/**
	 * Returns a default preset ID for the given module type
	 *
	 * @since 4.5.0
	 *
	 * @param string $module_slug
	 *
	 * @return string
	 */
	public function get_module_default_preset_id( $module_slug ) {
		return isset( $this->_settings->{$module_slug}->default )
			? $this->_settings->{$module_slug}->default
			: self::MODULE_INITIAL_PRESET_ID;
	}

	/**
	 * Returns the module preset ID
	 * If the preset ID doesn't exist it will return the default preset ID
	 *
	 * @since 4.5.0
	 *
	 * @param string $module_slug
	 * @param array  $module_attrs
	 *
	 * @return string
	 */
	public function get_module_preset_id( $module_slug, $module_attrs ) {
		$preset_id = et_()->array_get( $module_attrs, self::MODULE_PRESET_ATTRIBUTE, false );

		if ( ! $preset_id || ! $this->is_module_preset_exist( $module_slug, $preset_id ) ) {
			return $this->get_module_default_preset_id( $module_slug );
		}

		return $preset_id;
	}

	/**
	 * Returns the module preset by the given preset ID
	 * Returns an empty object if no preset found
	 *
	 * @since 4.5.0
	 *
	 * @param string $module_slug
	 * @param string $preset_id
	 *
	 * @return stdClass
	 */
	public function get_module_preset( $module_slug, $preset_id ) {
		if ( isset( $this->_settings->{$module_slug}->presets->{$preset_id} ) ) {
			return (object) $this->_settings->{$module_slug}->presets->{$preset_id};
		}

		return (object) array();
	}

	/**
	 * Returns Global Presets settings for the particular module.
	 *
	 * @since 4.5.0
	 *
	 * @param string $module_slug The module slug.
	 * @param array  $attrs       The module attributes.
	 *
	 * @return array
	 */
	public function get_module_presets_settings( $module_slug, $attrs ) {
		$result = array();

		$real_preset_id = $this->get_module_preset_id( $module_slug, $attrs );

		if ( isset( $this->_settings->{$module_slug}->presets->{$real_preset_id}->settings ) ) {
			$result = (array) $this->_settings->{$module_slug}->presets->{$real_preset_id}->settings;
		}

		$result = self::maybe_set_global_colors( $result );

		return $result;
	}

	/**
	 * Returns Global Presets settings with global colors injected.
	 *
	 * @since 4.10.0
	 * @since 4.17.2 Perform substring replacement (for compound settings like background gradient stops).
	 *
	 * @param array $attrs - The module attributes.
	 *
	 * @return array
	 */
	public static function maybe_set_global_colors( $attrs ) {
		if ( empty( $attrs['global_colors_info'] ) ) {
			return $attrs;
		}

		$gc_info = json_decode( $attrs['global_colors_info'], true );

		// Gather system-wide Global Colors info (including CSS color values and 'active' status).
		$all_global_colors_info = et_builder_get_all_global_colors( true );

		foreach ( $gc_info as $color_id => $option_names ) {
			foreach ( $option_names as $option_name ) {
				// Get the CSS color value assiciated with this GCID.
				if ( ! empty( $all_global_colors_info[ $color_id ]['color'] ) && isset( $attrs[ $option_name ] ) ) {
					$gcid_color_value = $all_global_colors_info[ $color_id ]['color'];
				} else {
					// We can't inject the CSS color value if we don't have record of it.
					continue;
				}

				// Replace CSS color value with GCID wherever it's found within the settings string.
				$attrs[ $option_name ] = str_replace( $color_id, $gcid_color_value, $attrs[ $option_name ] );
			}
		}

		return $attrs;
	}

	/**
	 * Checks whether customizer settings migrated or not
	 *
	 * @since 4.5.0
	 *
	 * @return bool
	 */
	public static function is_customizer_migrated() {
		return et_get_option( self::CUSTOMIZER_SETTINGS_MIGRATED_FLAG, false );
	}

	/**
	 * Checks whether Custom Defaults settings migrated or not
	 *
	 * @since 4.5.0
	 *
	 * @return bool
	 */
	public static function are_custom_defaults_migrated() {
		return et_get_option( self::CUSTOM_DEFAULTS_MIGRATED_FLAG, false );
	}

	/**
	 * Checks whether presets storage migrated or not.
	 *
	 * @since 4.19.3
	 *
	 * @return bool
	 */
	public static function is_presets_storage_migrated() {
		return et_get_option( self::PRESETS_STORAGE_MIGRATED_FLAG, false );
	}

	/**
	 * Migrates Module Customizer settings to Custom Defaults
	 *
	 * @since 4.5.0
	 *
	 * @param array $defaults - The list of modules default settings
	 */
	public function migrate_customizer_settings( $defaults ) {
		$template_directory = get_template_directory();

		require_once $template_directory . '/includes/module-customizer/migrations.php';

		$migrations = ET_Module_Customizer_Migrations::instance();

		list (
			$custom_defaults,
			$custom_defaults_unmigrated,
			) = $migrations->migrate( $defaults );

		et_update_option( self::CUSTOM_DEFAULTS_OPTION, (object) $custom_defaults );
		et_update_option( self::CUSTOMIZER_SETTINGS_MIGRATED_FLAG, true );

		if ( ! empty( $custom_defaults_unmigrated ) ) {
			et_update_option( self::CUSTOM_DEFAULTS_UNMIGRATED_OPTION, (object) $custom_defaults_unmigrated );
		} else {
			et_update_option( self::CUSTOM_DEFAULTS_UNMIGRATED_OPTION, false );
		}
	}

	/**
	 * Generates `_initial` module presets structure
	 *
	 * @since 4.5.0
	 *
	 * @param string $module_slug
	 * @param array  $all_modules
	 *
	 * @return object
	 */
	public static function generate_module_initial_presets_structure( $module_slug, $all_modules ) {
		$structure             = (object) array();
		$module_slug_converted = isset( self::$_module_import_types_conversion_map[ $module_slug ] )
			? self::$_module_import_types_conversion_map[ $module_slug ]
			: $module_slug;

		$preset_name = isset( $all_modules[ $module_slug_converted ]->name )
			? sprintf( esc_html__( '%s Preset', 'et_builder' ), $all_modules[ $module_slug_converted ]->name )
			: esc_html__( 'Preset', 'et_builder' );

		$structure->default                     = '_initial';
		$structure->presets                     = (object) array();
		$structure->presets->_initial           = (object) array();
		$structure->presets->_initial->name     = et_core_esc_previously( "{$preset_name} 1" );
		$structure->presets->_initial->created  = 0;
		$structure->presets->_initial->updated  = 0;
		$structure->presets->_initial->version  = ET_BUILDER_PRODUCT_VERSION;
		$structure->presets->_initial->settings = (object) array();

		return $structure;
	}

	/**
	 * Converts Custom Defaults to the new Global Presets format
	 *
	 * @since 4.5.0
	 *
	 * @param object $custom_defaults - The previous Custom Defaults.
	 *
	 * @return object
	 */
	public static function migrate_custom_defaults_to_global_presets( $custom_defaults ) {
		$all_modules = ET_Builder_Element::get_modules();
		$presets     = (object) array();

		foreach ( $custom_defaults as $module => $settings ) {
			$presets->$module = self::generate_module_initial_presets_structure( $module, $all_modules );

			foreach ( $settings as $setting => $value ) {
				$presets->$module->presets->_initial->settings->$setting = $value;
			}
		}

		return $presets;
	}

	/**
	 * Migrates existing Custom Defaults to the Global Presets structure
	 *
	 * @since 4.5.0
	 */
	public function migrate_custom_defaults() {
		if ( self::are_custom_defaults_migrated() ) {
			return;
		}

		$this->_settings = (array) $this->_settings;

		// Re-run migration to Global Presets if a user has not yet saved any presets.
		if ( et_is_builder_plugin_active() && ! empty( $this->_settings ) ) {
			et_update_option( self::CUSTOM_DEFAULTS_MIGRATED_FLAG, true );
			return;
		}

		$custom_defaults = et_get_option( self::CUSTOM_DEFAULTS_OPTION, false );

		if ( ! $custom_defaults ) {
			$custom_defaults = (object) array();
		}

		$global_presets = self::migrate_custom_defaults_to_global_presets( $custom_defaults );

		et_update_option( self::GLOBAL_PRESETS_OPTION, $global_presets, false, '', '', true );
		$this->_settings = $global_presets;

		et_update_option( self::CUSTOM_DEFAULTS_MIGRATED_FLAG, true );
	}

	/**
	 * Apply attribute migrations.
	 *
	 * @since 4.14.0
	 */
	public function apply_attribute_migrations() {
		foreach ( $this->_settings as $module => $preset_structure ) {
			foreach ( $preset_structure->presets as $preset_id => $preset ) {
				self::migrate_settings_as_module_attributes( $preset, $module );
			}
		}
	}

	/**
	 * Configuring and running migration of global presets via "et_pb_module_shortcode_attributes".
	 *
	 * @since 4.14.0
	 *
	 * @param object $preset Global preset object.
	 * @param string $module_slug Module slug.
	 *
	 * @return void
	 */
	public static function migrate_settings_as_module_attributes( $preset, $module_slug ) {
		$settings = (array) $preset->settings;

		// Mimic preset settings as module attributes to re-use standard migration mechanism.
		$settings['_builder_version'] = $preset->version;

		// This flag will be used in migrations (see: ET_Builder_Module_Settings_Migration::_maybe_global_presets_migration ).
		$maybe_global_presets_migration = true;

		$migrated_settings = apply_filters( 'et_pb_module_shortcode_attributes', $settings, $settings, $module_slug, '0.0.0.0', '', $maybe_global_presets_migration );
		if ( $settings['_builder_version'] !== $migrated_settings['_builder_version'] ) {
			$migrated_version = $migrated_settings['_builder_version'];
			unset( $migrated_settings['_builder_version'] );
			$preset->version  = $migrated_version;
			$preset->settings = (object) $migrated_settings;
		}
	}

	/**
	 * Handles Presets Storage Rollback.
	 *
	 * @since 4.19.3
	 *
	 * @param string $product_name - The short name of the product rolling back.
	 * @param string $rollback_from_version - Rollback from version.
	 * @param string $rollback_to_version - Rollback to version.
	 */
	public function rollback_presets_storage( $product_name, $rollback_from_version, $rollback_to_version ) {
		if ( ! isset( self::$allowed_products['storage_migration'][ $product_name ] ) ) {
			return;
		}

		if ( 0 > version_compare( $rollback_to_version, self::$allowed_products['storage_migration'][ $product_name ] ) ) {
			// Get option from product setting (last attr in args list).
			$global_presets_ng = et_get_option( self::GLOBAL_PRESETS_OPTION, array(), '', true, false, '', '', true );

			// Nothing to rollback, just reset the flag.
			if ( empty( $global_presets_ng ) ) {
				et_update_option( self::PRESETS_STORAGE_MIGRATED_FLAG, false );
				return;
			}

			// Remove data from the new storage and reset flag.
			et_update_option( self::GLOBAL_PRESETS_OPTION, array(), false, '', '', true );
			et_update_option( self::PRESETS_STORAGE_MIGRATED_FLAG, false );

			// Save presets to legacy setting.
			et_update_option( self::GLOBAL_PRESETS_OPTION_LEGACY, (object) $global_presets_ng );
		}
	}

	/**
	 * Handles theme version rollback.
	 *
	 * @since 4.5.0
	 *
	 * @param string $product_name - The short name of the product rolling back.
	 * @param string $rollback_from_version
	 * @param string $rollback_to_version
	 */
	public function after_version_rollback( $product_name, $rollback_from_version, $rollback_to_version ) {
		// Rollback starage migration.
		self::rollback_presets_storage( $product_name, $rollback_from_version, $rollback_to_version );

		if ( ! isset( self::$allowed_products['customizer_settings'][ $product_name ] ) ) {
			return;
		}

		if ( 0 > version_compare( $rollback_to_version, self::$allowed_products['customizer_settings'][ $product_name ] ) ) {
			et_delete_option( self::CUSTOM_DEFAULTS_MIGRATED_FLAG );
		}
	}

	/**
	 * Converts module type (slug).
	 *
	 * Used to separate Global Presets settings for modules sharing the same slug but having different meaning
	 * For example: Regular, Fullwidth and Specialty section types
	 *
	 * @since 4.5.0
	 *
	 * @param string $type  The module type (slug).
	 * @param array  $attrs The module attributes.
	 *
	 * @return string      The converted module type (slug)
	 */
	public function maybe_convert_module_type( $type, $attrs ) {
		if ( isset( self::$_module_types_conversion_map[ $type ] ) ) {
			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			$type = call_user_func_array(
				array( $this, self::$_module_types_conversion_map[ $type ] ),
				array( $attrs, $type )
			);
		}

		return $type;
	}

	/**
	 * Converts Section module slug to appropriate slug used in Global Presets
	 *
	 * @since 4.5.0
	 *
	 * @param array $attrs - The section attributes
	 *
	 * @return string      - The converted section type depends on the section attributes
	 */
	protected function _convert_section_type( $attrs ) {
		if ( isset( $attrs['fullwidth'] ) && 'on' === $attrs['fullwidth'] ) {
			return 'et_pb_section_fullwidth';
		}

		if ( isset( $attrs['specialty'] ) && 'on' === $attrs['specialty'] ) {
			return 'et_pb_section_specialty';
		}

		return 'et_pb_section';
	}

	/**
	 * Converts Slide module slug to appropriate slug used in Global Presets
	 *
	 * @since 4.5.0
	 *
	 * @return string - The converted slide type depends on the parent slider type
	 */
	protected function _convert_slide_type() {
		global $et_pb_slider_parent_type;

		if ( 'et_pb_fullwidth_slider' === $et_pb_slider_parent_type ) {
			return 'et_pb_slide_fullwidth';
		}

		return 'et_pb_slide';
	}

	/**
	 * Converts Column module slug to appropriate slug used in Global Presets
	 *
	 * @since 4.5.0
	 *
	 * @return string - The converted column type
	 */
	protected function _convert_column_type( $attrs, $type ) {
		global $et_pb_parent_section_type;

		if ( 'et_pb_column_inner' === $type ) {
			return 'et_pb_column';
		}

		if ( 'et_pb_specialty_section' === $et_pb_parent_section_type
			 || ( isset( $attrs['specialty_columns'] ) && '' !== $attrs['specialty_columns'] ) ) {
			return 'et_pb_column_specialty';
		}

		return 'et_pb_column';
	}

	/**
	 * Filters Global Presets setting to avoid non plain values like arrays or objects.
	 *
	 * Returns FALSE when the value is an Object or an array.
	 *
	 * @since 4.13.0 Included PHPDoc description.
	 * @since 4.5.0
	 *
	 * @param $value - The Global Presets setting value
	 *
	 * @return bool
	 */
	protected static function _filter_global_presets_setting_value( $value ) {
		return ! is_object( $value ) && ! is_array( $value );
	}

	/**
	 * Performs Global Presets format normalization.
	 * Usually used to cast format from array to object
	 * Also used to normalize global colors
	 *
	 * @since 4.5.0
	 * @since 4.17.2 Modified the global color option check to perform a substring match on multipart settings (like gradient stops).
	 *
	 * @param object|array $presets The object representing Global Presets settings.
	 *
	 * @return object
	 */
	protected function _normalize_global_presets( $presets ) {
		$result      = (object) array();
		$temp_preset = self::get_temp_presets();

		foreach ( $presets as $module => $preset_structure ) {
			if ( isset( $preset_structure->presets ) ) {
				$result->$module          = (object) array();
				$result->$module->presets = (object) array();

				foreach ( $preset_structure->presets as $preset_id => $preset ) {
					$result->$module->presets->$preset_id          = (object) array();
					$result->$module->presets->$preset_id->name    = $preset->name;
					$result->$module->presets->$preset_id->created = $preset->created;
					$result->$module->presets->$preset_id->updated = $preset->updated;
					$result->$module->presets->$preset_id->version = $preset->version;
					$result->$module->presets->$preset_id->is_temp = isset( $temp_preset[ $module ]['presets'][ $preset_id ] );

					if ( isset( $preset->settings ) ) {
						$result->$module->presets->$preset_id->settings = (object) array();

						$settings_filtered = array_filter(
							(array) $preset->settings,
							array(
								$this,
								'_filter_global_presets_setting_value',
							)
						);

						// Since we still support PHP 5.2 we can't use `array_filter`
						// with array keys, so use this to skip any empty key that's found.
						if ( isset( $settings_filtered[''] ) ) {
							continue;
						}

						foreach ( $settings_filtered as $setting_name => $value ) {
							$result->$module->presets->$preset_id->settings->$setting_name = $value;
						}

						// Look for settings in this module that use global colors.
						if ( isset( $settings_filtered['global_colors_info'] ) ) {
							$module_global_colors_info = json_decode( $settings_filtered['global_colors_info'], true );
						} else {
							// Nothing more to be done here if this module's `global_colors_info` setting is empty,
							// so advance the `$preset_structure->presets as $preset_id => $preset` loop.
							continue;
						}

						/**
						 * Presets: Global Color injection.
						 *
						 * Find GCID references and replace them with their CSS color values.
						 */

						// Gather system-wide Global Colors info (including CSS color values and 'active' status).
						$all_global_colors_info = et_builder_get_all_global_colors( true );

						$fixed_global_colors_info = array();

						if ( empty( $module_global_colors_info ) ) {
							$module_global_colors_info = array();
						}

						foreach ( $module_global_colors_info as $gcid => $settings_that_use_this_gcid_raw ) {
							if ( empty( $settings_that_use_this_gcid_raw ) ) {
								continue;
							}

							// Fix possible issue with duplicated gc in global_colors_info field.
							$settings_that_use_this_gcid       = array_values( array_unique( $settings_that_use_this_gcid_raw ) );
							$fixed_global_colors_info[ $gcid ] = $settings_that_use_this_gcid;

							// Get the CSS color value assiciated with this GCID.
							if ( ! empty( $all_global_colors_info[ $gcid ]['color'] ) && 'yes' === $all_global_colors_info[ $gcid ]['active'] ) {
								$gcid_color_value = $all_global_colors_info[ $gcid ]['color'];
							} else {
								// We can't inject the CSS color value if we don't have record of it.
								continue;
							}

							// For matching settings, replace CSS color values with their GCIDs.
							foreach ( $settings_that_use_this_gcid as $uses_this_gcid ) {
								if ( isset( $settings_filtered[ $uses_this_gcid ] ) ) {
									$settings_match = $settings_filtered[ $uses_this_gcid ];

									// Replace CSS color value with GCID wherever it's found within the settings string.
									// If string contains multiple values use strp_replace, otherwise just replace the value.
									$injected_gcid = false === strpos( $settings_match, '|' ) ? $gcid : str_replace( $gcid_color_value, $gcid, $settings_match );

									// Pass the GCID-injected string back to the preset setting.
									$result->$module->presets->$preset_id->settings->$uses_this_gcid = $injected_gcid;
								}
							}
						}

						// Insert fixed global_colors_info into preset settings.
						if ( ! empty( $fixed_global_colors_info ) ) {
							$result->$module->presets->$preset_id->settings->global_colors_info = wp_json_encode( $fixed_global_colors_info );
						}
					} else {
						$result->$module->presets->$preset->settings = (object) array();
					}
				}

				$result->$module->default = $preset_structure->default;
			}
		}

		return $result;
	}
}

ET_Builder_Global_Presets_Settings::instance();

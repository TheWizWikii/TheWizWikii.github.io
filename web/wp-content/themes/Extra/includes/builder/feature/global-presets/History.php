<?php

class ET_Builder_Global_Presets_History {
	const CUSTOM_DEFAULTS_HISTORY_OPTION       = 'builder_custom_defaults_history';
	const GLOBAL_PRESETS_HISTORY_OPTION_LEGACY = 'builder_global_presets_history';
	const GLOBAL_PRESETS_HISTORY_OPTION        = 'builder_global_presets_history_ng';
	const HISTORY_STORAGE_MIGRATED_FLAG        = 'builder_global_presets_history_migrated';
	const GLOBAL_PRESETS_HISTORY_LENGTH        = 100;

	private static $instance;

	private function __construct() {
		$this->_migrate_history_storage();
		$this->_register_ajax_callbacks();
		$this->_register_hooks();
	}

	/**
	 * Returns instance of the singleton class
	 *
	 * @since 4.5.0
	 *
	 * @return ET_Builder_Global_Presets_History
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function _register_ajax_callbacks() {
		add_action(
			'wp_ajax_et_builder_save_global_presets_history',
			array(
				$this,
				'ajax_save_global_presets_history',
			)
		);
		add_action(
			'wp_ajax_et_builder_retrieve_global_presets_history',
			array(
				$this,
				'ajax_retrieve_global_presets_history',
			)
		);
	}

	private function _register_hooks() {
		add_action( 'et_after_version_rollback', array( $this, 'after_version_rollback' ), 10, 3 );

		// If migration is needed, ensure that all modules get fully loaded.
		// phpcs:disable PEAR.Functions.FunctionCallSignature -- Anonymous functions.
		add_action( 'et_builder_framework_loaded', function() {
			if ( ! ET_Builder_Global_Presets_Settings::are_custom_defaults_migrated() ) {
				add_filter( 'et_builder_should_load_all_module_data', '__return_true' );
			}
		});
		// phpcs:enable

		add_action( 'et_builder_ready', array( $this, 'migrate_custom_defaults_history' ), 99 );
	}

	/**
	 * Handles AJAX requests to save history of Global Presets settings changes
	 *
	 * @since 4.5.0
	 *
	 * @return void
	 */
	public function ajax_save_global_presets_history() {
		// Allow saving Global Presets for admins and support elevated users only
		if ( ! et_core_security_check_passed( 'switch_themes', 'et_builder_save_global_presets_history' ) ) {
			wp_send_json_error(
				array(
					'code'    => 'et_forbidden',
					'message' => esc_html__( 'You do not have sufficient permissions to edit Divi Presets.', 'et_builder' ),
				)
			);
		}

		$history = self::_get_global_presets_history();

		$history_update = empty( $_POST['history'] ) ? (object) array() : json_decode( stripslashes( $_POST['history'] ) ); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- self::sanitize_and_validate function does sanitization.

		if ( empty( $history_update->current_state ) ) {
			et_core_die( esc_html__( 'Global History data is empty.', 'et_builder' ) );
		}

		$history->index = $history_update->index;

		if ( $history_update->is_new_record ) {
			$history->history[ $history->index ] = $history_update->current_state;
		}

		if ( self::sanitize_and_validate( $history ) ) {
			$current_settings = $history->history[ $history->index ];
			// Update option for product setting (last attr in args list).
			et_update_option( ET_Builder_Global_Presets_Settings::GLOBAL_PRESETS_OPTION, $current_settings->settings, false, '', '', true );
			et_update_option( self::GLOBAL_PRESETS_HISTORY_OPTION, $history, false, '', '', true );

			ET_Core_PageResource::remove_static_resources( 'all', 'all' );

			if ( et_get_option( ET_Builder_Global_Presets_Settings::CUSTOM_DEFAULTS_UNMIGRATED_OPTION, false ) ) {
				et_delete_option( ET_Builder_Global_Presets_Settings::CUSTOM_DEFAULTS_UNMIGRATED_OPTION );
				et_fb_delete_builder_assets();
			}

			ET_Builder_Ajax_Cache::instance()->unset_( 'ET_Builder_Global_Presets_History' );

			wp_send_json_success();
		} else {
			et_core_die( esc_html__( 'Global History data is corrupt.', 'et_builder' ) );
		}
	}

	/**
	 * Handles AJAX requests to retrieve history of Global Presets settings changes
	 *
	 * @since 4.5.0
	 *
	 * @return void
	 */
	public function ajax_retrieve_global_presets_history() {
		if ( ! et_core_security_check_passed( 'edit_posts', 'et_builder_retrieve_global_presets_history' ) ) {
			wp_send_json_error();
		}

		$history = $this->_get_global_presets_history();

		ET_Builder_Ajax_Cache::instance()->set( 'ET_Builder_Global_Presets_History', $history );

		wp_send_json_success( $history );
	}

	/**
	 * Adds a new Global Presets settings history record
	 *
	 * @since 4.5.0
	 *
	 * @param {Object} $defaults
	 */
	public function add_global_history_record( $defaults ) {
		if ( empty( $defaults ) ) {
			return;
		}

		$new_record = (object) array(
			'settings' => $defaults,
			'time'     => time() * 1000,
			'label'    => esc_html__( 'Imported From Layout', 'et_builder' ),
		);

		$history       = $this->_get_global_presets_history();
		$history_index = (int) $history->index;

		$history->history = array_slice( $history->history, 0, $history_index + 1 );
		array_push( $history->history, $new_record );
		$history->index++;

		if ( count( $history->history ) > self::GLOBAL_PRESETS_HISTORY_LENGTH ) {
			$history->history = array_slice( $history->history, -self::GLOBAL_PRESETS_HISTORY_LENGTH );
			$history->index   = min( $history->index, self::GLOBAL_PRESETS_HISTORY_LENGTH - 1 );
		}

		et_update_option( self::GLOBAL_PRESETS_HISTORY_OPTION, $history, false, '', '', true );

		ET_Core_PageResource::remove_static_resources( 'all', 'all' );
	}

	/**
	 * Get the active Global Presets settings history index
	 *
	 * @since 4.10.0
	 *
	 * @return int History index.
	 */
	public function get_global_history_index() {
		$history = $this->_get_global_presets_history();
		return is_object( $history ) ? (int) $history->index : md5( wp_json_encode( $history ) );
	}

	/**
	 * Performs validation and sanitizing history object.
	 * Returns false if data is invalid or corrupt.
	 *
	 * @since 4.5.0
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public static function sanitize_and_validate( &$data ) {
		if ( ! is_object( $data ) ) {
			return false;
		}

		$properties = array(
			'history',
			'index',
		);

		foreach ( $properties as $property ) {
			if ( ! property_exists( $data, $property ) ) {
				return false;
			}
		}

		if ( ! is_array( $data->history ) ) {
			return false;
		}

		foreach ( $data->history as &$record ) {
			if ( ! is_object( $record ) ) {
				return false;
			}

			$properties = array(
				'settings',
				'time',
				'label',
			);

			if ( count( (array) $record ) !== count( $properties ) ) {
				return false;
			}

			foreach ( $properties as $property ) {
				if ( ! property_exists( $record, $property ) ) {
					return false;
				}
			}

			foreach ( $record->settings as &$module ) {
				if ( ! is_object( $module ) ) {
					return false;
				}
			}

			if ( ! is_numeric( $record->time ) ) {
				return false;
			}

			$record->label = sanitize_text_field( $record->label );
		}

		$data->index = sanitize_text_field( $data->index );

		return true;
	}

	/**
	 * Handles History Storage Rollback.
	 *
	 * @since 4.19.3
	 *
	 * @param string $product_name - The short name of the product rolling back.
	 * @param string $rollback_from_version - Rollback from version.
	 * @param string $rollback_to_version - Rollback to version.
	 */
	public function rollback_history_storage( $product_name, $rollback_from_version, $rollback_to_version ) {
		if ( ! isset( ET_Builder_Global_Presets_Settings::$allowed_products['storage_migration'][ $product_name ] ) ) {
			return;
		}

		if ( 0 > version_compare( $rollback_to_version, ET_Builder_Global_Presets_Settings::$allowed_products['storage_migration'][ $product_name ] ) ) {
			// Get option from product setting (last attr in args list).
			$global_history_ng = et_get_option( self::GLOBAL_PRESETS_HISTORY_OPTION, array(), '', true, false, '', '', true );

			// Nothing to rollback, just reset the flag.
			if ( empty( $global_history_ng ) ) {
				et_update_option( self::HISTORY_STORAGE_MIGRATED_FLAG, false );
				return;
			}

			// Remove data from the new storage and reset flag.
			et_update_option( self::GLOBAL_PRESETS_HISTORY_OPTION, array(), false, '', '', true );
			et_update_option( self::HISTORY_STORAGE_MIGRATED_FLAG, false );

			// Save history to legacy setting.
			et_update_option( self::GLOBAL_PRESETS_HISTORY_OPTION_LEGACY, $global_history_ng );
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
		self::rollback_history_storage( $product_name, $rollback_from_version, $rollback_to_version );

		if ( ! isset( ET_Builder_Global_Presets_Settings::$allowed_products['customizer_settings'][ $product_name ] ) ) {
			return;
		}

		if ( 0 > version_compare( $rollback_to_version, ET_Builder_Global_Presets_Settings::$allowed_products['customizer_settings'][ $product_name ] ) ) {
			et_delete_option( self::GLOBAL_PRESETS_HISTORY_OPTION_LEGACY );
		}
	}

	/**
	 * Returns the Global Presets history object from DB
	 *
	 * @since 4.5.0
	 *
	 * @return object
	 */
	private function _get_global_presets_history() {
		$history = et_get_option( self::GLOBAL_PRESETS_HISTORY_OPTION, false, '', false, false, '', '', true );
		if ( ! $history ) {
			$history = (object) array(
				'history' => array(),
				'index'   => - 1,
			);
		}

		// Ensure history is an object.
		$history = is_object( $history ) ? $history : (object) $history;

		$this->_apply_attribute_migrations( $history );

		return $history;
	}

	/**
	 * Migrates global presets history into a separate setting.
	 *
	 * @since 4.19.3
	 *
	 * @return void
	 */
	protected function _migrate_history_storage() {
		if ( self::_is_history_storage_migrated() ) {
			return;
		}

		$global_history_legacy = et_get_option( self::GLOBAL_PRESETS_HISTORY_OPTION_LEGACY, array(), '', true );
		// Get option from product setting (last attr in args list).
		$global_history_ng = et_get_option( self::GLOBAL_PRESETS_HISTORY_OPTION, array(), '', true, false, '', '', true );

		// Nothing to migrate or history already exist in new storage.
		if ( empty( $global_history_legacy ) || ! empty( $global_history_ng ) ) {
			et_update_option( self::HISTORY_STORAGE_MIGRATED_FLAG, true );
			return;
		}

		$global_history_legacy_fixed = self::_fix_presets_history_before_migration( $global_history_legacy );

		// Update option for product setting (last attr in args list).
		et_update_option( self::GLOBAL_PRESETS_HISTORY_OPTION, $global_history_legacy_fixed, false, '', '', true );

		$global_history_ng_migrated = et_get_option( self::GLOBAL_PRESETS_HISTORY_OPTION, array(), '', true, false, '', '', true );

		// Remove old option if presets migrated.
		if ( ! empty( $global_history_ng_migrated ) ) {
			et_update_option( self::HISTORY_STORAGE_MIGRATED_FLAG, true );
			// Remove legacy history from settings.
			et_delete_option( self::GLOBAL_PRESETS_HISTORY_OPTION_LEGACY );
		}
	}

	/**
	 * Fix global colors in Global Presets History.
	 * Clean up global_colors_info array which may contain duplicates.
	 *
	 * @since 4.19.3
	 *
	 * @param object|array $history The object representing Global Presets History.
	 *
	 * @return object
	 */
	protected function _fix_presets_history_before_migration( $history ) {
		$result = $history;

		if ( isset( $history->history ) ) {
			foreach ( $history->history as $history_index => $presets_settings ) {
				foreach ( $presets_settings->settings as $module => $preset_structure ) {
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
									$result->history[ $history_index ]->settings->$module->presets->$preset_id->settings->global_colors_info = wp_json_encode( $fixed_global_colors_info );
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Checks whether history storage migrated or not.
	 *
	 * @since 4.19.3
	 *
	 * @return bool
	 */
	protected function _is_history_storage_migrated() {
		return et_get_option( self::HISTORY_STORAGE_MIGRATED_FLAG, false );
	}

	/**
	 * Migrates Custom Defaults history format to Global Presets history format
	 *
	 * @since 4.5.0
	 */
	public static function migrate_custom_defaults_history() {
		if ( et_is_builder_plugin_active() || ET_Builder_Global_Presets_Settings::are_custom_defaults_migrated() ) {
			return;
		}

		$history = et_get_option( self::CUSTOM_DEFAULTS_HISTORY_OPTION, false );

		if ( ! $history ) {
			return;
		}

		$all_modules               = ET_Builder_Element::get_modules();
		$migrated_history          = (object) array();
		$migrated_history->history = array();

		foreach ( $history->history as $record ) {
			$migrated_record           = (object) array();
			$migrated_record->settings = (object) array();

			foreach ( $record->settings as $module => $settings ) {
				$migrated_record->settings->$module = ET_Builder_Global_Presets_Settings::generate_module_initial_presets_structure( $module, $all_modules );

				foreach ( $settings as $setting => $value ) {
					$migrated_record->settings->$module->presets->_initial->settings->$setting = $value;
				}
			}

			$migrated_record->time  = $record->time;
			$migrated_record->label = $record->label;

			$migrated_history->history[] = $migrated_record;
		}

		$migrated_history->index = $history->index;

		et_update_option( self::GLOBAL_PRESETS_HISTORY_OPTION, $migrated_history, false, '', '', true );
	}

	/**
	 * Fire migration via "ET_Builder_Global_Presets_Settings::migrate_settings_as_module_attributes".
	 *
	 * @since ?
	 *
	 * @param object $history History object.
	 *
	 * @return void
	 */
	protected function _apply_attribute_migrations( $history ) {
		if ( empty( $history->history ) ) {
			return;
		}

		foreach ( $history->history as $record ) {
			if ( empty( $record->settings ) ) {
				continue;
			}
			foreach ( $record->settings as $module => $preset_structure ) {
				foreach ( $preset_structure->presets as $preset_id => $preset ) {
					ET_Builder_Global_Presets_Settings::migrate_settings_as_module_attributes( $preset, $module );
				}
			}
		}
	}
}

ET_Builder_Global_Presets_History::instance();

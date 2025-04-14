<?php
/**
 * Icon manager migartion class.
 *
 * @package Divi
 * @subpackage Builder
 * @since ?
 */

/**
 * Migration process to handle all the changes done in Icon Manager QF.
 *
 * @since ?
 */
class ET_Builder_Module_Settings_Migration_IconManager extends ET_Builder_Module_Settings_Migration {

	/**
	 * Migration Version.
	 *
	 * @var string
	 */
	public $version = '4.13.0';

	/**
	 * Proccessing field name.
	 *
	 * @var string
	 */
	private $_field_name;

	/**
	 * Current field value.
	 *
	 * @var string
	 */
	private $_current_value;

	/**
	 * Saved field value.
	 *
	 * @var string
	 */
	private $_saved_value;

	/**
	 * Current field name.
	 *
	 * @var string
	 */
	private $_saved_field_name;

	/**
	 * Array of attributes.
	 *
	 * @var array
	 */
	private $_attrs;

	/**
	 * All responsive modes suffixes.
	 *
	 * @var array
	 */
	private $_responsive_modes_suffixes = array( '', '_tablet', '_phone', '__hover', '__hover_enabled', '_last_edited' );

	/**
	 * Responsive modes.
	 *
	 * @var array
	 */
	private $_responsive_modes = array( '', '_tablet', '_phone' );

	/**
	 * Default values for the fields related to icon placement migration.
	 *
	 * @var array
	 */
	private $_icon_placement_migration_values = array(
		'image_icon_width'          => array(
			'left' => '16px',
			'top'  => '48px',
		),
		'image_icon_custom_padding' => array(
			'left' => '8px|8px|8px|8px|false|false',
			'top'  => '25px|25px|25px|25px|false|false',
		),
		'border_width_all_image'    => array(
			'left' => '2px',
			'top'  => '3px',
		),
	);

	/**
	 * Border radii value for the circle icon attr migration.
	 *
	 * @var string
	 */
	private $_circle_border_radii_value = 'on|100%|100%|100%|100%';

	/**
	 * Attrs sticky suffixes.
	 *
	 * @var array
	 */
	private $_sticky_suffixes = array( '__sticky_enabled', '__sticky' );

	/**
	 * Get array with Blurb module slug.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_blurb_modules() {
		return et_pb_get_font_icon_modules( 'blurb' );
	}

	/**
	 * Returns the list of Divi modules with buttons that have a custom icons.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_button_modules() {
		return et_pb_get_font_icon_modules( 'button' );
	}

	/**
	 * Get array with slugs for modules that supported Hover Overlay functionality.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_overlay_modules() {
		return et_pb_get_font_icon_modules( 'overlay' );
	}

	/**
	 * Get array with slugs for modules that supported Toggle Icon functionality.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_toggle_modules() {
		return et_pb_get_font_icon_modules( 'toggle' );
	}

	/**
	 * Get Blurb fields.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_blurb_fields() {

		// The order of the array keys and the order of the field names in the values ​​is important when
		// executing the migration logic, since it uses the "return early" principle.
		if ( parent::$_maybe_global_presets_migration ) {
			// Migrations rules for the Global Presets migrations case.
			$migration_options = array(
				'icon_font_size'         => array( 'image_icon_width' ),
				'border_color_all_image' => array( 'border_color_all_image' ),
				'border_width_all_image' => array( 'border_width_all_image' ),
				'font_icon'              => array( 'font_icon' ),
				'image_max_width'        => array( 'image_icon_width' ),
				'circle_border_color'    => array( 'border_color_all_image' ),
				'circle_color'           => array( 'image_icon_background_color' ),
				'use_circle_border'      => array( 'border_color_all_image' ),
				'use_circle'             => array( 'image_icon_custom_padding', 'border_color_all_image', 'border_radii_image', 'image_icon_background_color', 'border_width_all_image' ),
				'icon_placement'         => array( 'image_icon_width', 'border_width_all_image' ),
			);
		} else {
			$migration_options = array(
				'icon_font_size'         => array( 'image_icon_width' ),
				'border_color_all_image' => array( 'border_color_all_image' ),
				'border_width_all_image' => array( 'border_width_all_image' ),
				'font_icon'              => array( 'font_icon' ),
				'image_max_width'        => array( 'image_icon_width' ),
				'circle_border_color'    => array( 'border_color_all_image' ),
				'circle_color'           => array( 'image_icon_background_color' ),
				'use_circle_border'      => array( 'border_color_all_image' ),
				'use_circle'             => array( 'border_color_all_image', 'border_radii_image', 'image_icon_background_color', 'border_width_all_image' ),
				'icon_placement'         => array( 'image_icon_width', 'image_icon_custom_padding', 'border_width_all_image' ),
			);
		}

		$sticky_options = array( 'circle_color', 'circle_border_color', 'image_max_width', 'icon_font_size' );

		$result_fields_keys = array();

		$modes_with_sticky = array_merge( $this->_responsive_modes_suffixes, $this->_sticky_suffixes );

		foreach ( $migration_options as $saved_field_name => $field_names ) {
			foreach ( $modes_with_sticky as $mode_suffix ) {

				$maybe_border_width_all_image_saved_field = 'border_width_all_image' === $saved_field_name && in_array( $mode_suffix, array( '__hover', '__hover_enabled' ), true );
				$maybe_use_circle_border_saved_field      = 'use_circle_border' === $saved_field_name && in_array( $mode_suffix, array( '_tablet', '_phone', '__hover', '__hover_enabled', '_last_edited' ), true );
				$maybe_use_circle_responsive              = 'use_circle' === $saved_field_name && '' !== $mode_suffix;
				$maybe_icon_placement_in_hover            = 'icon_placement' === $saved_field_name && in_array( $mode_suffix, array( '__hover', '__hover_enabled' ), true );
				$maybe_not_sticky_option_when_sticky_mode = in_array( $mode_suffix, $this->_sticky_suffixes, true ) && ! in_array( $saved_field_name, $sticky_options, true );

				if ( $maybe_icon_placement_in_hover || $maybe_border_width_all_image_saved_field || $maybe_use_circle_border_saved_field || $maybe_use_circle_responsive || $maybe_not_sticky_option_when_sticky_mode ) {
					continue;
				}
				foreach ( $field_names as $field_name ) {
					$affected_fields = 'use_circle' === $saved_field_name ? array( $saved_field_name => $this->_get_blurb_modules() ) : array( $saved_field_name . $mode_suffix => $this->_get_blurb_modules() );
					if ( ! empty( $result_fields_keys[ $field_name . $mode_suffix ]['affected_fields'] ) ) {
						$affected_fields += $result_fields_keys[ $field_name . $mode_suffix ]['affected_fields'];
					};

					$result_fields_keys[ $field_name . $mode_suffix ] = array(
						'affected_fields' => $affected_fields,
					);
				}
			}
		}

		return $result_fields_keys;
	}

	/**
	 * Get fields for modules with Button support.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_button_fields() {
		$result_button_fields          = array();
		$button_field_names_for_module = array(
			'et_pb_fullwidth_header' => array( 'button_one_icon', 'button_two_icon' ),
			'one_button_modules'     => array( 'button_icon' ),
		);
		foreach ( $button_field_names_for_module as $field_module => $field_names ) {
			foreach ( $field_names as $field_name ) {
				foreach ( $this->_responsive_modes as $mode_suffix ) {
					$modules                                  = 'et_pb_fullwidth_header' === $field_module ? array( 'et_pb_fullwidth_header' ) : array_diff( $this->_get_button_modules(), array( 'et_pb_fullwidth_header' ) );
					$full_field_name                          = $field_name . $mode_suffix;
					$result_button_fields[ $full_field_name ] = array(
						'affected_fields' => array( $full_field_name => $modules ),
					);
				}
			}
		}

		return $result_button_fields;
	}


	/**
	 * Get modules which are using overlay functionality by responsive suffix.
	 *
	 * @since ?
	 * @param string $suffix responsive suffix.
	 *
	 * @return array
	 */
	private function _get_overlay_modules_by_suffix( $suffix ) {
		return '' === $suffix ? $this->_get_overlay_modules() : array_filter(
			$this->_get_overlay_modules(),
			function( $module ) {
				return 'et_pb_filterable_portfolio' !== $module;
			}
		);
	}

	/**
	 * Get fields for modules with Hover Overlay functionality.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_overlay_fields() {
		$result_overlay_fields = array();
		foreach ( $this->_responsive_modes as $mode_suffix ) {
			$full_field_name                           = 'hover_icon' . $mode_suffix;
			$modules                                   = $this->_get_overlay_modules_by_suffix( $mode_suffix );
			$result_overlay_fields[ $full_field_name ] = array(
				'affected_fields' => array( $full_field_name => $modules ),
			);
		}
		return $result_overlay_fields;
	}

	/**
	 * Get fields for modules with Toggle icon functionality.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	private function _get_toggle_fields() {
		$result_overlay_fields = array();
		$toggle_icon_fields    = array( 'use_icon_font_size', 'icon_color', 'icon_font_size' );
		foreach ( $toggle_icon_fields as $toggle_icon_field ) {
			foreach ( $this->_responsive_modes_suffixes as $mode_suffix ) {
				if ( 'use_icon_font_size' === $toggle_icon_field && '' !== $mode_suffix ) {
					continue;
				}
				$full_field_name = $toggle_icon_field . $mode_suffix;
				$modules         = $this->_get_toggle_modules();
				$result_overlay_fields[ 'open_' . $full_field_name ] = array(
					'affected_fields' => array( $full_field_name => $modules ),
				);
			}
		}
		return $result_overlay_fields;
	}

	/**
	 * Get all affected fields.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	public function get_fields() {
		return array_merge( $this->_get_blurb_fields(), $this->_get_button_fields(), $this->_get_overlay_fields(), $this->_get_toggle_fields() );
	}

	/**
	 * Get all affected modules.
	 *
	 * @since ?
	 *
	 * @return array
	 */
	public function get_modules() {
		return array_merge( $this->_get_blurb_modules(), $this->_get_overlay_modules(), $this->_get_button_modules(), $this->_get_toggle_modules() );
	}

	/**
	 * Base migrate method, which launching during migration.
	 *
	 * @since ?
	 *
	 * @param string $field_name field name.
	 * @param string $current_value current value.
	 * @param string $module_slug module slug.
	 * @param string $saved_value saved value.
	 * @param string $saved_field_name saved field name.
	 * @param array  $attrs attrs array.
	 * @param string $content content.
	 * @param string $module_address module_address.
	 *
	 * @return array
	 */
	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Initialize migration params from received args.
		$this->_init_params( $field_name, $current_value, $saved_value, $saved_field_name, $attrs );

		// For the non Blurb modules we only migarte `font_icon` option.
		if ( ! in_array( $module_slug, $this->_get_blurb_modules(), true ) ) {
			// Simple migrate filed names for toggle icons.
			if ( in_array( $module_slug, $this->_get_toggle_modules(), true ) ) {
				return $current_value;
			}
			return $this->_migrate_font_icon();
		}

		// Run different scripts depending on the type of migration.
		return ( parent::$_maybe_global_presets_migration ) ? $this->_blurb_global_presets_attrs_migration() : $this->_blurb_post_attrs_migration();
	}

	/**
	 * Initialize migration params from received args.
	 *
	 * @since ?
	 *
	 * @param string $field_name field_name.
	 * @param string $current_value current_value.
	 * @param string $saved_value saved_value.
	 * @param string $saved_field_name saved_field_name.
	 * @param string $attrs attrs.
	 *
	 * @return void
	 */
	private function _init_params( $field_name, $current_value, $saved_value, $saved_field_name, $attrs ) {
		$this->_field_name       = $field_name;
		$this->_current_value    = $current_value;
		$this->_saved_value      = $saved_value;
		$this->_saved_field_name = $saved_field_name;
		$this->_attrs            = $attrs;
	}

	/**
	 * Check if an passed attribute is an on\off type via its name.
	 *
	 * @since ?
	 *
	 * @param string $attr_name attribute name.
	 *
	 * @return bool
	 */
	private function _maybe_on( $attr_name ) {
		return ! empty( $this->_attrs[ $attr_name ] ) && 'on' === ( $this->_attrs[ $attr_name ] );
	}

	/**
	 * Check if current migrated `field_name` contains passed attribute name.
	 *
	 * @since ?
	 *
	 * @param string $attr_name attribute name.
	 * @return bool
	 */
	private function _field_name_contains( $attr_name ) {
		return false !== strpos( $this->_field_name, $attr_name );
	}

	/**
	 * Check if current migrated `saved_name` contains passed attribute name.
	 *
	 * @since ?
	 *
	 * @param string $attr_name attr_name.
	 * @return bool
	 */
	private function _saved_name_contains( $attr_name ) {
		return false !== strpos( $this->_saved_field_name, $attr_name );
	}

	/**
	 * Get current migrated field name suffix.
	 *
	 * @param bool $use_sticky_suffixes use or not sticky_suffixes.
	 * @since ?
	 *
	 * @return string
	 */
	private function _get_field_suffix( $use_sticky_suffixes = false ) {
		$suffixes = $use_sticky_suffixes ? array_merge( $this->_sticky_suffixes, array_reverse( $this->_responsive_modes_suffixes ) ) : array_reverse( $this->_responsive_modes_suffixes );
		array_pop( $suffixes );
		foreach ( $suffixes as $suffix ) {
			if ( $this->_field_name_contains( $suffix ) ) {
				return $suffix;
			}
		}
		return '';
	}

	/**
	 * Get current migrated field name without suffix.
	 *
	 * @since ?
	 *
	 * @return string
	 */
	private function _get_field_name_without_suffix() {
		return str_replace( $this->_get_field_suffix(), '', $this->_field_name );
	}

	/**
	 * Migrate fields value "as is" if the current field is one of the
	 * service fields ( fields with suffix '__hover_enabled' or '_last_edited' ) or sticky fields.
	 *
	 * @since ?
	 *
	 * @return bool
	 */
	private function _maybe_service_or_sticky_field() {
		$service_suffixes = array_merge( $this->_sticky_suffixes, array( '__hover_enabled', '_last_edited' ) );
		return in_array( $this->_get_field_suffix( true ), $service_suffixes, true ) && ! empty( $this->_current_value );
	}

	/**
	 * Returns an attribute value from by attribute's name.
	 *
	 * @since ?
	 *
	 * @param string $attr_name attribute name.
	 * @return bool
	 */
	private function _get_attr( $attr_name ) {
		return ! empty( $this->_attrs[ $attr_name ] ) ? $this->_attrs[ $attr_name ] : '';
	}

	/**
	 * Migrate fields which depends on the icon placement option.
	 *
	 * @since ?
	 *
	 * @param string $for_empty_current_value setting returned value for empty icon_placement.
	 * @return string
	 */
	private function _migrate_icon_placement( $for_empty_current_value = '' ) {
		$result_values                = $this->_icon_placement_migration_values[ $this->_get_field_name_without_suffix() ];
		$result_values['']            = $for_empty_current_value;
		$current_icon_placement_value = $this->_get_attr( 'icon_placement' . $this->_get_field_suffix() );
		if ( '' === $this->_get_field_suffix() || ! empty( $current_icon_placement_value ) ) {
			return $result_values[ $current_icon_placement_value ];
		}
		return '';
	}

	/**
	 * Migrate background color with circle color.
	 *
	 * @since ?
	 *
	 * @return string
	 */
	private function _migrate_background_color_with_circle_color() {

		if ( empty( $this->_current_value ) && '' === $this->_get_field_suffix() ) {
			return et_builder_accent_color();
		}

		return $this->_current_value;
	}

	/**
	 * Migrate font icon.
	 *
	 * @since ?
	 *
	 * @return string
	 */
	private function _migrate_font_icon() {
		if ( empty( $this->_current_value ) ) {
			return '';
		}

		return et_pb_build_extended_font_icon_value( $this->_current_value );
	}

	/**
	 * Run Blurb's attributes migration when post's shortcodes attributes migrating.
	 *
	 * @since ?
	 *
	 * @return string
	 */
	private function _blurb_post_attrs_migration() {
		// Determine the conditions for the migration of various options.
		$is_icon_mode                                      = $this->_maybe_on( 'use_icon' );
		$maybe_set_border_color_all_image_as_is            = ! $is_icon_mode && $this->_field_name_contains( 'border_color_all_image' ) && ! empty( $this->_attrs[ $this->_field_name ] );
		$_migrate_font_icon                                = $is_icon_mode && $this->_field_name_contains( 'font_icon' ) && ! $this->_field_name_contains( '__hover_enabled' ) && ! $this->_field_name_contains( '_last_edited' );
		$migrate_border_radii_image                        = $is_icon_mode && $this->_field_name_contains( 'border_radii_image' ) && $this->_maybe_on( 'use_circle' );
		$migrate_image_icon_custom_padding                 = $is_icon_mode && $this->_field_name_contains( 'image_icon_custom_padding' ) && $this->_maybe_on( 'use_circle' );
		$migrate_border_width_all_image_as_is              = $this->_field_name_contains( 'border_width_all_image' ) && $this->_saved_name_contains( 'border_width_all_image' );
		$migrate_border_width_all_image_with_circle_border = $is_icon_mode && $this->_field_name_contains( 'border_width_all_image' ) && $this->_maybe_on( 'use_circle' ) && $this->_maybe_on( 'use_circle_border' );
		$migrate_border_color_all_image                    = ! $maybe_set_border_color_all_image_as_is && $is_icon_mode && $this->_field_name_contains( 'border_color_all_image' ) && $this->_maybe_on( 'use_circle' ) && $this->_maybe_on( 'use_circle_border' );
		$migrate_image_icon_width_with_circle              = $is_icon_mode && $this->_field_name_contains( 'image_icon_width' ) && $this->_saved_name_contains( 'icon_placement' ) && $this->_maybe_on( 'use_circle' );
		$migrate_icon_font_size_as_is                      = $is_icon_mode && $this->_field_name_contains( 'image_icon_width' ) && $this->_saved_name_contains( 'icon_font_size' ) && ! empty( $this->_current_value );
		$_migrate_background_color_with_circle_color       = $is_icon_mode && $this->_field_name_contains( 'image_icon_background_color' ) && $this->_saved_name_contains( 'circle_color' ) && $this->_maybe_on( 'use_circle' );
		$maybe_return_default_accent_color_for_default_border = empty( $this->_get_attr( 'circle_border_color' ) ) && empty( $this->_get_attr( 'circle_border_color' . $this->_get_field_suffix() ) ) && empty( $this->_get_attr( 'border_color_all_image' ) ) && empty( $this->_get_attr( 'border_color_all_image' . $this->_get_field_suffix() ) );
		$migrate_image_max_width_as_is                        = ! $is_icon_mode && $this->_field_name_contains( 'image_icon_width' ) && $this->_saved_name_contains( 'image_max_width' );

		if ( $this->_maybe_service_or_sticky_field() ) {
			return $this->_current_value;
		}

		if ( $maybe_set_border_color_all_image_as_is && ! empty( $this->_saved_value ) ) {
			return $this->_saved_value;
		}

		if ( $migrate_icon_font_size_as_is ) {
			return $this->_current_value;
		}

		if ( $_migrate_background_color_with_circle_color ) {
			return $this->_migrate_background_color_with_circle_color();
		}

		if ( $migrate_image_icon_width_with_circle ) {
			return $this->_migrate_icon_placement();
		}

		if ( $migrate_border_color_all_image && $maybe_return_default_accent_color_for_default_border ) {
			return et_builder_accent_color();
		}

		if ( $_migrate_font_icon ) {
			return $this->_migrate_font_icon();
		}

		if ( $migrate_border_radii_image ) {
			return $this->_circle_border_radii_value;
		}

		if ( $migrate_image_icon_custom_padding ) {
			return $this->_migrate_icon_placement();
		}

		if ( $migrate_border_width_all_image_with_circle_border ) {
			return $this->_migrate_icon_placement( $this->_icon_placement_migration_values['border_width_all_image']['top'] );
		}

		if ( $migrate_border_width_all_image_as_is && $is_icon_mode ) {
			return in_array( $this->_saved_value, $this->_icon_placement_migration_values['border_width_all_image'], true ) ? $this->_saved_value : '';
		}

		if ( $migrate_border_color_all_image ) {
			return $this->_current_value;
		}

		if ( $migrate_image_max_width_as_is ) {
			return $this->_current_value;
		}

		return $this->_saved_value;
	}

	/**
	 * Run Blurb's attributes migration when Global Presets attributes migrating.
	 *
	 * @since ?
	 *
	 * @return string
	 */
	private function _blurb_global_presets_attrs_migration() {
		// Determine the conditions for the migration of various options.
		$_migrate_font_icon                                = $this->_field_name_contains( 'font_icon' ) && ! $this->_field_name_contains( '__hover_enabled' ) && ! $this->_field_name_contains( '_last_edited' );
		$migrate_border_radii_image                        = $this->_field_name_contains( 'border_radii_image' ) && $this->_maybe_on( 'use_circle' );
		$migrate_image_icon_custom_padding                 = $this->_field_name_contains( 'image_icon_custom_padding' ) && $this->_maybe_on( 'use_circle' );
		$migrate_border_width_all_image_as_is              = $this->_field_name_contains( 'border_width_all_image' ) && $this->_saved_name_contains( 'border_width_all_image' );
		$migrate_border_width_all_image_with_circle_border = ! $migrate_border_width_all_image_as_is && $this->_field_name_contains( 'border_width_all_image' ) && $this->_maybe_on( 'use_circle' ) && $this->_maybe_on( 'use_circle_border' );
		$migrate_no_border_width_all_image_field           = ! $migrate_border_width_all_image_with_circle_border && $this->_field_name_contains( 'border_width_all_image' ) && $this->_maybe_on( 'use_circle' );
		$migrate_border_color_all_image                    = $this->_field_name_contains( 'border_color_all_image' ) && $this->_maybe_on( 'use_circle' ) && $this->_maybe_on( 'use_circle_border' );
		$migrate_image_icon_width_with_circle              = $this->_field_name_contains( 'image_icon_width' ) && $this->_saved_name_contains( 'icon_placement' ) && $this->_maybe_on( 'use_circle' );
		$migrate_icon_font_size_as_is                      = $this->_field_name_contains( 'image_icon_width' ) && $this->_saved_name_contains( 'icon_font_size' ) && ! empty( $this->_current_value );
		$_migrate_background_color_with_circle_color       = $this->_field_name_contains( 'image_icon_background_color' ) && $this->_saved_name_contains( 'circle_color' ) && $this->_maybe_on( 'use_circle' );
		$migrate_default_background_color                  = ! $_migrate_background_color_with_circle_color && $this->_field_name_contains( 'image_icon_background_color' ) && $this->_maybe_on( 'use_circle' );
		$maybe_return_accent_color_for_default_border      = empty( $this->_attrs[ 'circle_border_color' . $this->_get_field_suffix() ] ) && in_array( $this->_get_field_suffix(), array( '', '_tablet', '_phone', '__hover' ), true );
		$migrate_circle_border_color_as_is                 = $this->_field_name_contains( 'border_color_all_image' ) && $this->_saved_name_contains( 'circle_border_color' ) && ! empty( $this->_current_value );
		$migrate_image_max_width_as_is                     = $this->_field_name_contains( 'image_icon_width' ) && $this->_saved_name_contains( 'image_max_width' );

		if ( $this->_maybe_service_or_sticky_field() ) {
			return $this->_current_value;
		}

		if ( $migrate_icon_font_size_as_is ) {
			return $this->_current_value;
		}

		if ( $migrate_default_background_color ) {
			return et_builder_accent_color();
		}

		if ( $_migrate_background_color_with_circle_color ) {
			return $this->_migrate_background_color_with_circle_color();
		}

		if ( $migrate_image_icon_width_with_circle ) {
			return $this->_migrate_icon_placement();
		}

		if ( $migrate_border_color_all_image && $maybe_return_accent_color_for_default_border ) {
			return et_builder_accent_color();
		}

		if ( $_migrate_font_icon ) {
			return $this->_migrate_font_icon();
		}

		if ( $migrate_border_radii_image ) {
			return $this->_circle_border_radii_value;
		}

		if ( $migrate_image_icon_custom_padding ) {
			return $this->_migrate_icon_placement( $this->_icon_placement_migration_values['image_icon_custom_padding']['top'] );
		}

		if ( $migrate_border_width_all_image_with_circle_border ) {
			return $this->_migrate_icon_placement( $this->_icon_placement_migration_values['border_width_all_image']['top'] );
		}

		if ( $migrate_no_border_width_all_image_field ) {
			return in_array( $this->_saved_value, $this->_icon_placement_migration_values['border_width_all_image'], true ) ? $this->_saved_value : '';
		}

		if ( $migrate_border_color_all_image || $migrate_circle_border_color_as_is ) {
			return $this->_current_value;
		}

		if ( $migrate_image_max_width_as_is ) {
			return $this->_current_value;
		}

		return $this->_saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_IconManager();

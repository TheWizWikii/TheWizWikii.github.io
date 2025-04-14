<?php
/**
 * Option Templates helper methods.
 *
 * @since 3.28
 *
 * Class ET_Builder_Module_Helper_OptionTemplate
 */
class ET_Builder_Module_Helper_OptionTemplate {

	private $map          = array();
	private $templates    = array();
	private $data         = array();
	private $cache        = array();
	private $tab_slug_map = array();

	public $template_prefix = '%t';

	protected static $_ = null;

	public static function instance() {
		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	private function __construct() {
		self::$_ = ET_Core_Data_Utils::instance();
	}

	private function uniq( $prefix, $content ) {
		$key = md5( $prefix . serialize( $content ) );
		if ( isset( $this->map[ $key ] ) ) {
			return $this->map[ $key ];
		}

		return ( $this->map[ $key ] = $this->template_prefix . $key );
	}

	/**
	 * Determine whether option template is enabled on current request or not
	 *
	 * @since 3.28
	 *
	 * @return bool
	 */
	public function is_enabled() {
		// Option template tends to be enabled on most request to speed up performance
		$status = true;

		// Option template is disabled on:
		// 1. AJAX request for fetching classic builder (BB)'s module data. BB data is shipped as
		// optimized template markup which is rendered on server then sent as string. Hence
		// Option Template's sent-config-rebuild-on-js won't be usable for BB
		// 2. BB's editing page. BB edit page scans for field dependency and generates visibility
		// setting on `window.et_pb_module_field_dependencies` variable for field depency thus
		// actual field should be rendered here instead of templateId
		if ( et_builder_is_loading_bb_data() || et_builder_is_bb_page() ) {
			$status = false;
		}

		/**
		 * Filters option template status
		 *
		 * @since 3.28
		 *
		 * @param bool $status
		 */
		return apply_filters( 'et_builder_option_template_is_active', $status );
	}

	/**
	 * Determine whether given field name is option template field based on its first two characters
	 *
	 * @since 3.28
	 *
	 * @return bool
	 */
	public function is_option_template_field( $field_name = '' ) {
		return $this->template_prefix === substr( $field_name, 0, 2 );
	}

	public function has( $key ) {
		return isset( $this->templates[ $key ] );
	}

	public function add( $key, $template ) {
		$fields_template = array_merge( $template, et_pb_responsive_options()->create( $template ) );

		// Populate tab_slug of given template because advance fields can be rendered on any tab
		foreach ( $fields_template as $field_name => $field ) {
			if ( isset( $field['tab_slug'] ) ) {
				$tab_slug = '' === $field['tab_slug'] ? 'advanced' : $field['tab_slug'];

				if ( ! isset( $this->tab_slug_map[ $tab_slug ] ) ) {
					$this->tab_slug_map[ $tab_slug ] = array( $key );

					continue;
				}

				if ( ! in_array( $key, $this->tab_slug_map[ $tab_slug ] ) ) {
					$this->tab_slug_map[ $tab_slug ][] = $key;
				}
			}
		}

		$this->templates[ $key ] = $fields_template;
	}

	public function create( $key, $config, $return_template_id = false ) {
		$data = array( $key, $config );
		$id   = $this->uniq( $key, $data );
		// Alternative, this will save the values directly in the Module $this->unprocessed_fields
		// instead of this Calls $this->data and hence require a simpler logic.
		// Theoretically it should require more memory but blackfire begs to differ.
		$this->data[ $id ] = $data;

		// Return as template id instead of id => key if needed
		if ( $return_template_id ) {
			return $id;
		}

		return array( $id => $key );
	}

	/**
	 * Create placeholders for template's params
	 *
	 * @return string[]
	 */
	public function placeholders( $config, $idx = 1, $path = array() ) {
		$placeholders = array();
		foreach ( $config as $key => $value ) {
			if ( is_array( $value ) ) {
				// Prepend current key as path so placeholder later can correctly fetch correct
				// value from template data using dot notation path (both lodash get() or utils's
				// array_get() support this).
				$path[] = $key;

				$value = $this->placeholders( $value, $idx, $path );
			} else {
				// Prepend dot notation path as prefix if needed
				$prefix = empty( $path ) ? '' : implode( '.', $path ) . '.';

				$value = "%%{$prefix}{$key}%%";
			}
			$placeholders[ $key ] = $value;
			$idx++;
		}
		return $placeholders;
	}

	/**
	 * Get module's data
	 *
	 * @return array[]
	 */
	public function all() {
		return $this->data;
	}

	/**
	 * Get templates
	 *
	 * @since 3.28
	 *
	 * @return array
	 */
	public function templates() {
		return $this->templates;
	}

	/**
	 * Set `$this->data` property from external source (ie: static field definition cache).
	 *
	 * @since 3.28
	 *
	 * @param array $cached_data
	 */
	public function set_data( $cached_data = array() ) {
		$this->data = wp_parse_args(
			$cached_data,
			$this->data
		);
	}

	/**
	 * Set `$this->templates` property from external source (ie: static field definition cache).
	 *
	 * @since 3.28
	 *
	 * @param array $cached_template
	 */
	public function set_templates( $cached_templates = array() ) {
		$this->templates = wp_parse_args(
			$cached_templates,
			$this->templates
		);
	}

	/**
	 * Set `$this->tab_slug_map` from external source (ie: static field definition cache).
	 *
	 * @since 3.29
	 *
	 * @param array $cached_tab_slug_map
	 */
	public function set_tab_slug_map( $cached_tab_slug_map = array() ) {
		$this->tab_slug_map = wp_parse_args(
			$cached_tab_slug_map,
			$this->tab_slug_map
		);
	}

	/**
	 * Get template data based on given template id
	 *
	 * @since 3.28
	 *
	 * @param string $template_id
	 *
	 * @return array
	 */
	public function get_data( $template_id = '' ) {
		return isset( $this->data[ $template_id ] ) ? $this->data[ $template_id ] : array();
	}

	/**
	 * Get template based on given template type
	 *
	 * @since 3.28
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	public function get_template( $type = '' ) {
		return isset( $this->templates[ $type ] ) ? $this->templates[ $type ] : array();
	}

	/**
	 * Get hashed cache key based on params given
	 *
	 * @since 3.28
	 *
	 * @param mixed $params
	 *
	 * @return string
	 */
	public function get_cache_key( $params ) {
		$params = is_string( $params ) ? $params : serialize( $params );

		return md5( $params );
	}

	/**
	 * Get cached value
	 * Return null if no cached value found
	 *
	 * @since 3.28
	 *
	 * @param string $name
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get_cache( $name, $key ) {
		if (
			! empty( $this->cache )
			&& ! empty( $this->cache[ $name ] )
			&& ! empty( $this->cache[ $name ][ $key ] )
		) {
			return $this->cache[ $name ][ $key ];
		}

		return null;
	}

	/**
	 * Set value to be cached
	 *
	 * @since 3.28
	 *
	 * @param string $name
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function set_cache( $name, $key, $value ) {
		self::$_->array_set( $this->cache, "{$name}.{$key}", $value );
	}

	/**
	 * Get placeholder of given template
	 *
	 * @since 3.28
	 *
	 * @param string $template
	 *
	 * @return array|bool
	 */
	public function get_template_placeholder( $template ) {
		// Check for cached result first for faster performance
		$cache_name = 'template_placeholder';
		$cache_key  = $this->get_cache_key( $template );
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		preg_match( '/(?<=%%).*(?=%%)/', $template, $placeholder );

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $placeholder );

		return $placeholder;
	}

	/**
	 * Get tab slug maps
	 *
	 * @since 3.29
	 *
	 * @return array
	 */
	public function get_tab_slug_map() {
		return $this->tab_slug_map;
	}

	public function is_template_inside_tab( $tab_name, $template_type ) {
		// Template which has `%%tab_slug%%` tab_slug can exist on any tab
		if ( in_array( $template_type, self::$_->array_get( $this->tab_slug_map, '%%tab_slug%%', array() ) ) ) {
			return true;
		}

		if ( in_array( $template_type, self::$_->array_get( $this->tab_slug_map, $tab_name, array() ) ) ) {
			return true;
		}

		return false;
	}

	public function rebuild_string_placeholder( $template, $data = array(), $settings = array() ) {
		// Placeholder settings
		$default_settings = array(
			'suffix'                 => '',
			'remove_suffix_if_empty' => false,
		);

		$placeholder_settings = wp_parse_args( $settings, $default_settings );

		// Check for cached result first for faster performance
		$cache_name = 'string_placeholder';
		$cache_key  = $this->get_cache_key( array( $template, $data, $placeholder_settings ) );
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		// Get placeholder
		$placeholder = is_string( $template ) ? $this->get_template_placeholder( $template ) : false;

		// If found, replace placeholder with correct value from data
		if ( is_array( $placeholder ) && isset( $placeholder[0] ) ) {
			// Get placeholder replacement
			$replacement = ! empty( $data[1] ) && ! empty( $data[1][ $placeholder[0] ] ) ? $data[1][ $placeholder[0] ] : '';

			// Pass null as empty string; null as attribute affect builder differently.
			// Attribute with empty string will be omitted later.
			if ( is_null( $replacement ) ) {
				$replacement = '';
			}

			// If placeholder is identical to template, return replacement early. This also
			// handles the case where replacement as array type
			if ( "%%{$placeholder[0]}%%" === $template ) {

				// Cache result
				$this->set_cache( $cache_name, $cache_key, $replacement );

				return $replacement;
			}

			// Get placeholder suffix
			$has_suffix = '' === $replacement && $placeholder_settings['remove_suffix_if_empty'];
			$suffix     = $has_suffix ? $placeholder_settings['suffix'] : '';

			// Make sure replacement is string before proceed;
			if ( is_string( $replacement ) ) {
				$rebuilt_string = str_replace( "%%{$placeholder[0]}%%{$suffix}", $replacement, $template );

				// Cache result
				$this->set_cache( $cache_name, $cache_key, $rebuilt_string );

				return $rebuilt_string;
			}
		}

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $template );

		return $template;
	}

	public function rebuild_preset_placeholder( $template, $data = array(), $settings = array() ) {
		// Check for cached result first for faster performance
		$cache_name = 'preset_placeholder';
		$cache_key  = $this->get_cache_key( array( $template, $data, $settings ) );
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$rebuild_attr = array();

		foreach ( $template as $preset_attr_key => $preset_attr_value ) {
			// Object inside preset array mostly contains fields attribute which its object key
			// contains placeholder while its object value contains actual value without placeholder.
			if ( is_array( $preset_attr_value ) ) {
				$rebuilt_preset_attr_object = array();

				foreach ( $preset_attr_value as $name => $value ) {
					$object_item_name = $this->rebuild_string_placeholder( $name, $data, $settings );

					$rebuilt_preset_attr_object[ $object_item_name ] = $value;
				}

				$rebuild_attr[ $preset_attr_key ] = $rebuilt_preset_attr_object;

				continue;
			}

			$rebuild_attr[ $preset_attr_key ] = $preset_attr_value;
		}

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $rebuild_attr );

		return $rebuild_attr;
	}

	public function rebuild_composite_structure_placeholder( $template_type, $template, $data = array() ) {
		// Check for cached result first for faster performance
		$cache_name = 'composite_structure_placeholder';
		$cache_key  = $this->get_cache_key( array( $template_type, $template, $data ) );
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$rebuilt_composite_structure_field = $template;

		// Replaces placeholder with actual value on border's nested composite structure fields
		if ( 'border' === $template_type ) {
			// Reset `controls` attribute output
			$rebuilt_composite_structure_field['controls'] = array();

			// Loop composite structure's original `controls` from template
			foreach ( $template['controls'] as $field_name => $field ) {
				$rebuilt_field_name = $this->rebuild_string_placeholder( $field_name, $data );
				$rebuilt_field      = $field;

				// Loop field on composite structure controls
				foreach ( $rebuilt_field as $attr_name => $attr_value ) {
					$settings                    = array(
						'suffix'                 => 'label' === $attr_name ? ' ' : '',
						'remove_suffix_if_empty' => 'label' === $attr_name,
					);
					$rebuilt_field[ $attr_name ] = $this->rebuild_string_placeholder( $attr_value, $data, $settings );
				}

				$rebuilt_composite_structure_field['controls'][ $rebuilt_field_name ] = $rebuilt_field;
			}
		}

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $rebuilt_composite_structure_field );

		return $rebuilt_composite_structure_field;
	}

	public function rebuild_field_attr_value( $attr_name, $attr_value, $template_data ) {
		// Check for cached result first for faster performance
		$cache_name = 'field_attr_value';
		$cache_key  = $this->get_cache_key( array( $attr_name, $attr_value, $template_data ) );
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$template_type = ! empty( $template_data[0] ) ? $template_data[0] : '';
		$prefix        = ! empty( $template_data[1]['prefix'] ) && ! empty( $template_data[1]['prefix'] ) ? $template_data[1]['prefix'] : '';

		// Certain advanced field (ie. Text Shadow) automatically adds underscore
		$auto_add_prefix_underscore = isset( $template_data[0] ) && 'text_shadow' === $template_data[0] && '' === $prefix;

		// 1. Field attribute value's type is string
		if ( is_string( $attr_value ) ) {
			$placeholder_has_space_suffix = 'label' === $attr_name && in_array( $template_type, array( 'border', 'text_shadow' ) );

			$settings            = array(
				'suffix'                 => $placeholder_has_space_suffix ? ' ' : '',
				'remove_suffix_if_empty' => $placeholder_has_space_suffix ? true : false,
			);
			$rebuilt_placeholder = $this->rebuild_string_placeholder( $attr_value, $template_data, $settings );

			// Cache result
			$this->set_cache( $cache_name, $cache_key, $rebuilt_placeholder );

			return $rebuilt_placeholder;
		}

		// 2. Field attribute value's type is array (sequential)
		if ( is_array( $attr_value ) && isset( $attr_value[0] ) ) {
			$rebuild_attr_value = array();

			foreach ( $attr_value as $array_value ) {
				// Array consists of string is most likely used for defining field relationship
				// such as `show_if` attribute; Replace prefix and suffix placeholder with
				// placeholder replacement also consider that text_shadow advanced field
				// automatically adds underscore after prefix so it needs to be adjusted as well
				if ( is_string( $array_value ) ) {
					$settings             = array(
						'suffix'                 => '_',
						'remove_suffix_if_empty' => $auto_add_prefix_underscore,
					);
					$rebuild_attr_value[] = $this->rebuild_string_placeholder( $array_value, $template_data, $settings );
				} elseif ( 'presets' === $attr_name ) {
					// Handle preset attribute specifically due to how it is structured
					$settings             = array(
						'suffix'                 => '_',
						'remove_suffix_if_empty' => $auto_add_prefix_underscore,
					);
					$rebuild_attr_value[] = $this->rebuild_preset_placeholder( $array_value, $template_data, $settings );
				} else {
					// Non string and `presets` attribute less likely contains placeholder
					$rebuild_attr_value[] = $array_value;
				}
			}

			// Cache result
			$this->set_cache( $cache_name, $cache_key, $rebuild_attr_value );

			return $rebuild_attr_value;
		}

		// 3. Field attribute value's type is array (associative)
		if ( is_array( $attr_value ) && ! isset( $attr_value[0] ) ) {
			$attr_object = array();

			// Loop existing attrValue and populate the rebuilt result on `attrObject`.
			foreach ( $attr_value as $item_key => $item_value ) {
				$attr_object_key = $this->rebuild_string_placeholder( $item_key, $template_data );

				// Replaces placeholder with actual value on border's nested composite structure fields
				if ( 'composite_structure' === $attr_name ) {
					$item_value = $this->rebuild_composite_structure_placeholder( $template_type, $item_value, $template_data );
				}

				$attr_object[ $attr_object_key ] = $item_value;
			}

			// Cache result
			$this->set_cache( $cache_name, $cache_key, $attr_object );

			return $attr_object;
		}

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $attr_value );

		// 4. Unknown attribute value type; directly pass it
		return $attr_value;
	}

	public function rebuild_field_template( $template_id, $parent_template_id = false ) {
		// Check for cached result first for faster performance
		$cache_name = 'field_template';
		$cache_key  = $parent_template_id ? "{$template_id}-inherits-{$parent_template_id}" : $template_id;
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$fields        = array();
		$template_data = $this->get_data( $template_id );

		// No fields will be found without template data. Return early;
		if ( empty( $template_data ) ) {
			return $fields;
		}

		$template_type     = ! empty( $template_data[0] ) ? $template_data[0] : '';
		$template_settings = ! empty( $template_data[1] ) ? $template_data[1] : array();
		$prefix            = ! empty( $template_settings['prefix'] ) ? $template_settings['prefix'] : '';

		$parent_template_data = false;

		// If rebuilt parent template is inside another templateId (ie. Text Shadow inside Font)
		// its placeholder is passed for data; The expected structure becomes
		// `[templateType, parentTemplateSettings]` instead of `[templateType, templateSettings]`;
		// Thus get parent template's settings and use it
		if ( $parent_template_id ) {
			$parent_template_data     = $this->get_data( $parent_template_id );
			$parent_template_settings = ! empty( $parent_template_data[1] ) ? $parent_template_data[1] : true;

			$template_settings_inherits_from_parant = array();

			foreach ( $template_settings as $name => $value ) {
				$placeholder = $this->get_template_placeholder( $value );

				if ( is_array( $placeholder ) && isset( $placeholder[0] ) ) {
					$template_settings_inherits_from_parant[ $name ] = self::$_->array_get(
						$parent_template_settings,
						$placeholder[0],
						$value
					);
				}
			}

			$parent_template_data = array(
				$template_type,
				$template_settings_inherits_from_parant,
			);
		}

		// Get fields template for given template type
		$fields_template = $this->get_template( $template_type );

		// Loop fields template and replace placeholder with actual value
		foreach ( $fields_template as $field_name_template => $field_template ) {
			// Certain advanced field (ie. Text Shadow) automatically adds underscore
			// Related template type needs to be adjusted
			$remove_suffix_if_empty = 'text_shadow' === $template_type && '' === $prefix;

			// Replace field attribute name's placeholder
			$field_template_data = $parent_template_id ? $parent_template_data : $template_data;
			$field_name          = $this->rebuild_string_placeholder(
				$field_name_template,
				$field_template_data,
				array(
					'remove_suffix_if_empty' => $remove_suffix_if_empty,
					// placeholder's suffix, not placeholder named %%suffix%%
					'suffix'                 => '_',
				)
			);

			// Replace field attribute value's placeholder
			$field = array();
			if ( is_array( $field_template ) ) {
				foreach ( $field_template as $attr_name => $attr_value ) {
					$rebuilt_attr_value = $this->rebuild_field_attr_value(
						$attr_name,
						$attr_value,
						$field_template_data
					);

					// Omit attribute with empty value: existance of attribute even with empty
					// string value is handled differently in many field (ie, `show_if`)
					if ( '' === $rebuilt_attr_value ) {
						continue;
					}

					$field[ $attr_name ] = $rebuilt_attr_value;
				}
			} else {
				$fields = array_merge(
					$fields,
					$this->rebuild_field_template( $field_name_template, $template_id )
				);
			}

			// `name` attribute is dynamically added based on field's array key
			$field['name'] = $field_name;

			// Populate rebuilt field
			$fields[ $field_name ] = $field;
		}

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $fields );

		return $fields;
	}

	public function rebuild_default_props( $template_id ) {
		// Check for cached result first for faster performance
		$cache_name = 'default_props';
		$cache_key  = $template_id;
		$cache      = $this->get_cache( $cache_name, $cache_key );

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$default_props  = array();
		$rebuilt_fields = $this->rebuild_field_template( $template_id );

		foreach ( $rebuilt_fields as $field_name => $field ) {
			$value = '';

			if ( isset( $field['composite_type'], $field['composite_structure'] ) ) {
				require_once ET_BUILDER_DIR . 'module/field/attribute/composite/Parser.php';
				$composite_atts = ET_Builder_Module_Field_Attribute_Composite_Parser::parse( $field['composite_type'], $field['composite_structure'] );
				$default_props  = array_merge( $default_props, $composite_atts );
			} else {
				if ( isset( $field['default_on_front'] ) ) {
					$value = $field['default_on_front'];
				} elseif ( isset( $field['default'] ) ) {
					$value = $field['default'];
				}

				$default_props[ $field_name ] = $value;
			}
		}

		// Cache result
		$this->set_cache( $cache_name, $cache_key, $default_props );

		return $default_props;
	}
}



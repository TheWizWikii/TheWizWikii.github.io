<?php
/**
 * Multi View Options helper class file
 *
 * @package ET/Builder
 *
 * @since 3.27.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Multi View Options helper class
 *
 * Class ET_Builder_Module_Helper_MultiViewOptions
 *
 * @since 3.27.1
 */
class ET_Builder_Module_Helper_MultiViewOptions {

	/**
	 * HTML data attribute key.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $data_attr_key = 'data-et-multi-view';

	/**
	 * Find and replace data regex pattern.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $pattern = '/\{\{(.+)\}\}/';

	/**
	 * Module Object.
	 *
	 * @since 4.10.0
	 *
	 * @var ET_Builder_Element
	 */
	protected $module;

	/**
	 * Module props data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $props = array();

	/**
	 * Module slug.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Custom props data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $custom_props = array();

	/**
	 * Conditional values data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $conditional_values = array();

	/**
	 * Default values data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $default_values = array();

	/**
	 * Cached values data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $cached_values = array();

	/**
	 * Set list of props keys that need to inherit the value
	 *
	 * @since 4.0.2
	 *
	 * @var array
	 */
	protected $inherited_values = array();

	/**
	 * Hover enabled option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $hover_enabled_suffix = '__hover_enabled';

	/**
	 * Responsive enabled option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $responsive_enabled_suffix = '_last_edited';

	/**
	 * Hover option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $hover_suffix = '__hover';

	/**
	 * Tablet option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $tablet_suffix = '_tablet';

	/**
	 * Phone option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $phone_suffix = '_phone';

	/**
	 * List of props that inherited from previous breakpoint
	 *
	 * @var array
	 */
	public $inherited_props;

	/**
	 * Class constructor
	 *
	 * @since 3.27.1
	 *
	 * @param ET_Builder_Element $module             Module object.
	 * @param array              $custom_props       Defined custom props data.
	 * @param array              $conditional_values Defined options conditional values.
	 * @param array              $default_values     Defined options default values.
	 */
	public function __construct( $module = false, $custom_props = array(), $conditional_values = array(), $default_values = array() ) {
		$this->set_module( $module );
		$this->set_custom_props( $custom_props );
		$this->set_conditional_values( $conditional_values );
		$this->set_default_values( $default_values );
	}

	/**
	 * Get props name by mode
	 *
	 * @since 3.27.1
	 *
	 * @param string $name Props name.
	 * @param string $mode Selected view mode.
	 *
	 * @return string
	 */
	public static function get_name_by_mode( $name, $mode ) {
		if ( 'tablet' === $mode || 'phone' === $mode ) {
			return "{$name}_{$mode}";
		}

		if ( 'hover' === $mode ) {
			return "{$name}__hover";
		}

		return $name;
	}

	/**
	 * Get regex field name suffix
	 *
	 * @since 4.0.1
	 *
	 * @return string
	 */
	public static function get_regex_suffix() {
		return '/(__hover|__hover_enabled|__sticky|__sticky_enabled|_last_edited|_tablet|_phone)$/';
	}

	/**
	 * Get props name base
	 *
	 * @since 4.0.1
	 *
	 * @param string $name Props name.
	 *
	 * @return string
	 */
	public static function get_name_base( $name ) {
		return preg_replace( self::get_regex_suffix(), '', $name );
	}

	/**
	 * Get view modes
	 *
	 * @since 3.27.1
	 *
	 * @return array
	 */
	public static function get_modes() {
		return array( 'desktop', 'tablet', 'phone', 'hover' );
	}

	/**
	 * Check if mode is enabled
	 *
	 * @since 3.27.1
	 *
	 * @param string $name Props name.
	 * @param string $mode Selected view mode.
	 *
	 * @return bool
	 */
	public function mode_is_enabled( $name, $mode ) {
		switch ( $mode ) {
			case 'hover':
				return $this->hover_is_enabled( $name );

			case 'tablet':
			case 'phone':
				return $this->responsive_is_enabled( $name );

			default:
				return true;
		}
	}

	/**
	 * Get responsive options filed suffixes
	 *
	 * @since 3.27.1
	 *
	 * @param bool $include_enabled_suffix Whether to include the responsive enabled suffix or not.
	 *
	 * @return array
	 */
	public static function responsive_suffixes( $include_enabled_suffix = true ) {
		$suffixes = array( self::$tablet_suffix, self::$phone_suffix );

		if ( $include_enabled_suffix ) {
			$suffixes[] = self::$responsive_enabled_suffix;
		}

		return $suffixes;
	}

	/**
	 * Get hover options filed suffixes
	 *
	 * @since 3.27.1
	 *
	 * @param bool $include_enabled_suffix Whether to include the hover enabled suffix or not.
	 *
	 * @return array
	 */
	public static function hover_suffixes( $include_enabled_suffix = true ) {
		$suffixes = array( self::$hover_suffix );

		if ( $include_enabled_suffix ) {
			$suffixes[] = self::$hover_enabled_suffix;
		}

		return $suffixes;
	}

	/**
	 * Check whether an option is responsive enabled.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name options name.
	 *
	 * @return bool
	 */
	public function responsive_is_enabled( $name ) {
		return et_pb_responsive_options()->is_enabled( $name, $this->get_module_props() );
	}

	/**
	 * Check whether an option is hover enabled.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name options name.
	 *
	 * @return bool
	 */
	public function hover_is_enabled( $name ) {
		return et_pb_hover_options()->is_enabled( $name, $this->get_module_props() );
	}

	/**
	 * Get module props desktop mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_desktop( $name, $default_value = null ) {
		return $this->get_value( $name, 'desktop', $default_value );
	}

	/**
	 * Get module props tablet mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_tablet( $name, $default_value = null ) {
		return $this->get_value( $name, 'tablet', $default_value );
	}

	/**
	 * Get module props phone mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_phone( $name, $default_value = null ) {
		return $this->get_value( $name, 'phone', $default_value );
	}

	/**
	 * Get module props hover mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_hover( $name, $default_value = null ) {
		return $this->get_value( $name, 'hover', $default_value );
	}

	/**
	 * Get module props value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param string $mode          Select only specified modes: desktop, tablet, phone, hover.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value( $name, $mode = 'desktop', $default_value = null ) {
		return et_()->array_get( $this->get_values( $name ), $mode, $default_value );
	}

	/**
	 * Get module props values.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name     Props name.
	 * @param bool   $distinct Wether to distinct the values or not.
	 *
	 * @return array Values of all view modes: desktop, tablet, phone, hover.
	 */
	public function get_values( $name, $distinct = true ) {
		if ( ! isset( $this->cached_values[ $name ] ) ) {
			$values = array();

			if ( isset( $this->custom_props[ $name ] ) ) {
				foreach ( self::get_modes() as $mode ) {
					$value = et_()->array_get( $this->custom_props[ $name ], $mode, '' );

					if ( '' === $value && isset( $this->default_values[ $name ][ $mode ] ) ) {
						$value = $this->default_values[ $name ][ $mode ];
					}

					if ( ! $this->is_props_inherited( self::get_name_by_mode( $name, $mode ), $value ) ) {
						$values[ $mode ] = $value;
					}
				}
			} else {
				foreach ( self::get_modes() as $mode ) {
					if ( ! $this->mode_is_enabled( $name, $mode ) ) {
						continue;
					}

					$value = $this->get_prop( self::get_name_by_mode( $name, $mode ) );

					if ( '' === $value && isset( $this->default_values[ $name ][ $mode ] ) ) {
						$value = $this->default_values[ $name ][ $mode ];
					}

					if ( ! $this->is_props_inherited( self::get_name_by_mode( $name, $mode ), $value ) ) {
						$values[ $mode ] = $value;
					}
				}
			}

			// Normalize the values to make to all the view modes has own data.
			$this->cached_values[ $name ] = $this->normalize_values( $values );
		}

		// Distinct the values to omit duplicate values across modes.
		if ( $distinct ) {
			return $this->distinct_values( $this->cached_values[ $name ] );
		}

		return $this->cached_values[ $name ];
	}

	/**
	 * Compare values
	 *
	 * @since 3.27.1
	 *
	 * @param string $value Source value.
	 * @param [type] $value_compare Target value to compare.
	 *
	 * @return bool
	 */
	protected static function compare_value( $value, $value_compare = null ) {
		$match = false;

		if ( is_null( $value_compare ) ) {
			$match = is_string( $value ) || is_numeric( $value ) ? strlen( $value ) : ! empty( $value );
		} elseif ( is_bool( $value_compare ) ) {
			$match = $value_compare === $value;
		} elseif ( is_array( $value_compare ) ) {
			$match = in_array( $value, $value_compare, true );
		} elseif ( '__empty' === $value_compare ) {
			$match = empty( $value );
		} elseif ( '__not_empty' === $value_compare ) {
			$match = ! empty( $value );
		} else {
			$match = strtolower( strval( $value_compare ) ) === strtolower( strval( $value ) );
		}

		return $match ? true : false;
	}

	/**
	 * Check if module props has value in any of data breakpoint: desktop, tablet, phone, hover.
	 *
	 * @since 3.27.1
	 *
	 * @param string          $name           Field key.
	 * @param string|callable $value_compare  The value to compare.
	 * @param string          $selected_mode  Selected view mode.
	 * @param bool            $inherit       Should the value inherited from previous breakpoint.
	 *
	 * @return bool
	 */
	public function has_value( $name, $value_compare = null, $selected_mode = false, $inherit = false ) {
		$has_value = false;

		if ( $selected_mode && is_string( $selected_mode ) ) {
			$selected_mode = false !== strpos( $selected_mode, ',' ) ? explode( ',', $selected_mode ) : array( $selected_mode );
		}

		if ( $selected_mode && ! is_array( $selected_mode ) ) {
			$selected_mode = array( $selected_mode );
		}

		$values = $this->get_values( $name, false );

		foreach ( $values as $mode => $value ) {
			if ( $selected_mode && ! in_array( $mode, $selected_mode, true ) ) {
				continue;
			}

			$has_value = self::compare_value( $value, $value_compare );

			if ( ! $has_value && 'desktop' !== $mode && $inherit ) {
				$has_value = self::compare_value( $this->get_inherit_value( $name, $mode ), $value_compare );
			}

			if ( $has_value ) {
				break;
			}
		}

		return $has_value;
	}

	/**
	 * Get props inherit value
	 *
	 * @since 3.27.1
	 *
	 * @param string $name           Field key.
	 * @param string $selected_mode  Selected view mode.
	 *
	 * @return mixed
	 */
	public function get_inherit_value( $name, $selected_mode ) {
		$values = $this->get_values( $name, false );

		if ( isset( $values[ $selected_mode ] ) ) {
			return $values[ $selected_mode ];
		}

		return '';
	}

	/**
	 * Get module props conditional value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param string $mode          Select only specified modes: desktop, tablet, phone, hover.
	 * @param mixed  $conditionals  Extra data to compare.
	 *
	 * @return mixed Calculated conditional value. Will return null if not match any comparison.
	 */
	public function get_conditional_value( $name, $mode = 'desktop', $conditionals = array() ) {
		if ( ! $this->conditional_values ) {
			return null;
		}

		$value = null;

		foreach ( $this->conditional_values as $compare ) {
			if ( ! isset( $compare['name'] ) || $compare['name'] !== $name ) {
				continue;
			}

			if ( isset( $compare['conditionals'] ) && $compare['conditionals'] ) {
				$is_conditionals_match = true;

				foreach ( $compare['conditionals'] as $conditional_key => $conditional_value ) {
					if ( ! isset( $conditionals[ $conditional_key ] ) || $conditionals[ $conditional_key ] !== $conditional_value ) {
						$is_conditionals_match = false;
						break;
					}
				}

				if ( ! $is_conditionals_match ) {
					continue;
				}
			}

			if ( isset( $compare['props'] ) && $compare['props'] ) {
				$is_props_match = true;

				foreach ( $compare['props'] as $prop_key => $prop_value ) {
					if ( ! $prop_key && ! is_numeric( $prop_key ) ) {
						$is_props_match = false;
						break;
					}

					if ( 'hover' === $mode && ! $this->hover_is_enabled( $prop_key ) ) {
						$mode = false;
					}

					if ( in_array( $mode, array( 'tablet', 'phone' ), true ) && ! $this->responsive_is_enabled( $prop_key ) ) {
						$mode = false;
					}

					if ( ! $this->has_value( $prop_key, $prop_value, $mode ) ) {
						$is_props_match = false;
						break;
					}
				}

				if ( ! $is_props_match ) {
					continue;
				}
			}

			$value = $compare['value'];

			if ( preg_match_all( $this->pattern, $value, $matches, PREG_SET_ORDER, 0 ) ) {
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$value = str_replace( $match[0], $this->get_value( $match[1], $mode ), $value );
				}
			}
		}

		return $value;
	}

	/**
	 * Set module object.
	 *
	 * @since 3.27.1
	 *
	 * @param ET_Builder_Element $module Module object.
	 */
	public function set_module( $module ) {
		if ( ! $module instanceof ET_Builder_Element ) {
			return et_debug( __( 'Invalid module instance passed to ET_Builder_Module_Helper_MultiViewOptions::set_module', 'et_builder' ) );
		}

		$this->module = $module;

		if ( property_exists( $module, 'slug' ) ) {
			$this->slug = $module->slug;
		}

		$this->set_inherited_props();
	}

	/**
	 * Set props data.
	 *
	 * @since 4.0
	 *
	 * @param string $name  Props key.
	 * @param array  $value Props value.
	 */
	public function set_props( $name, $value ) {
		// Always clear cached values to keep the data up to date
		// in case the props defined in looping
		$this->clear_cached_values( $name );

		// Set the props data.
		$this->props[ $name ] = $value;
	}

	/**
	 * Clear cached values
	 *
	 * @since 4.0
	 *
	 * @param string $name Props key.
	 *
	 * @return void
	 */
	public function clear_cached_values( $name ) {
		if ( isset( $this->cached_values[ $name ] ) ) {
			unset( $this->cached_values[ $name ] );
		}
	}

	/**
	 * Set list props that inherited.
	 *
	 * @since 4.0.2
	 */
	public function set_inherited_props() {
		if ( ! property_exists( $this->module, 'mv_inherited_props' ) || ! is_array( $this->module->mv_inherited_props ) ) {
			return;
		}

		$this->inherited_props = $this->module->mv_inherited_props;
	}

	/**
	 * Check if props value suppose to be inherited
	 *
	 * @since 4.0.2
	 *
	 * @param string $name_by_mode Full name of the props.
	 * @param string $value Props value.
	 *
	 * @return boolean
	 */
	public function is_props_inherited( $name_by_mode, $value ) {
		return isset( $this->inherited_props[ $name_by_mode ] ) && '' === $value;
	}

	/**
	 * Set option default value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Data key.
	 * @param array  $default_value Default value.
	 */
	public function set_default_value( $name, $default_value ) {
		$this->default_values[ $name ] = $this->normalize_values( $default_value );
	}

	/**
	 * Set options default values.
	 *
	 * @since 3.27.1
	 *
	 * @param array $default_values Default values.
	 */
	public function set_default_values( $default_values ) {
		if ( $default_values && is_array( $default_values ) ) {
			foreach ( $default_values as $name => $value ) {
				$this->set_default_value( $name, $value );
			}
		}
	}

	/**
	 * Set option conditional value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Prop key.
	 * @param string $value         Custom conditional value.
	 * @param array  $props         Key value pair of props list to compare.
	 * @param array  $conditionals  Conditionals parameter go compare to calculate the value.
	 */
	public function set_conditional_value( $name, $value, $props, $conditionals = array() ) {
		if ( ! $props || ! is_array( $props ) ) {
			return;
		}

		if ( ! is_array( $conditionals ) ) {
			return;
		}

		$conditional = array(
			// Order index is used to preserve original order when sorting "equal" items
			// as the order of "equal" items in PHP is "undefined" after sorting.
			'order'        => count( $this->conditional_values ),
			'name'         => $name,
			'value'        => $value,
			'props'        => $props,
			'conditionals' => $conditionals,
		);

		$this->conditional_values[] = $conditional;

		// Sort by count of props and count of conditionals.
		usort( $this->conditional_values, array( $this, 'sort_conditional_values' ) );
	}

	/**
	 * Set option conditional values.
	 *
	 * @since 3.27.1
	 *
	 * @param array $conditional_values Default values.
	 */
	public function set_conditional_values( $conditional_values ) {
		if ( ! $conditional_values || ! is_array( $conditional_values ) ) {
			return;
		}

		foreach ( $conditional_values as  $conditional_key => $param ) {
			if ( ! isset( $param['value'] ) ) {
				continue;
			}

			if ( ! isset( $param['props'] ) ) {
				continue;
			}

			$conditionals = isset( $param['conditionals'] ) ? $param['conditionals'] : array();

			$this->set_conditional_value( $conditional_key, $param['value'], $param['props'], $conditionals );
		}
	}

	/**
	 * Set custom variable data.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name   Data key.
	 * @param array  $values The values to inject.
	 */
	public function set_custom_prop( $name, $values ) {
		// Always clear cached values to keep the data up to date
		// in case the props defined in looping
		$this->clear_cached_values( $name );

		// Set the custom props data.
		$this->custom_props[ $name ] = $this->normalize_values( $values );
	}

	/**
	 * Set custom variables data.
	 *
	 * @since 3.27.1
	 *
	 * @param array $custom_props Defined custom props data.
	 */
	public function set_custom_props( $custom_props ) {
		if ( $custom_props && is_array( $custom_props ) ) {
			foreach ( $custom_props as $name => $values ) {
				$this->set_custom_prop( $name, $values );
			}
		}
	}

	/**
	 * Render the multi view HTML element
	 *
	 *      Example:
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Hello {{name}}', // Assume name props value is John
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <div>Hello John</div>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'p',
	 *          'content' => 'get_the_title', // Assume current page title is Hello World
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <p>Hello World</p>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'h3',
	 *          'content' => get_the_title(), // Assume current page title is Hello World
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <h3>Hello World</h3>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'img',
	 *          'attrs'   => array(
	 *              'src'    => '{{image_url}}, // Assume image_url props value is test.jpg
	 *              'width'  => '{{image_width}}px', // Assume image_width props value is 50
	 *              'height' => '{{image_height}}px', // Assume image_height props value is 100
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <img src="test.jpg" width="50px" height="100px" />
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Lorem Ipsum',
	 *          'styles'  => array(
	 *              'background-image' => 'url({{image_url}})', // Assume image_url props value is test.jpg
	 *              'font-size'        => '{{title_font_size}}px', // Assume title_font_size props value is 20
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <div style="background-image: url(test.jpg); font-size: 20px;">Lorem Ipsum</div>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Lorem Ipsum',
	 *          'classes' => array(
	 *              'et_pb_slider_no_arrows' => array
	 *                 'show_arrows' => 'off', // Assume show_arrows props value is off
	 *              ),
	 *              'et_pb_slider_carousel'  => array
	 *                  'show_thumbnails' => 'on', // Assume show_thumbnails props value is on
	 *              ),
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <div class=et_pb_slider_no_arrows et_pb_slider_carousel">Lorem Ipsum</div>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Lorem Ipsum',
	 *          'visibility' => array(
	 *              'show_arrows'     => 'on',
	 *              'show_thumbnails' => 'off',
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output that will visible when show_arrows is on and show_thumbnails is off:
	 *        <div>Lorem Ipsum</div>
	 *
	 * @param array   $contexts {
	 *       Data contexts.
	 *
	 *     @type string          $tag                HTML element tag name. Example: div, img, p. Default is span.
	 *
	 *     @type string          $content            Param that will be used to populate the content data.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $attrs              Param that will be used to populate the attributes data.
	 *                                               Associative array key used as attribute name and the value will be used as attribute value.
	 *                                               Special case for 'class' and 'style' attribute name will only generating output for desktop mode.
	 *                                               Use 'styles' or 'classes' context for multi modes usage.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $styles             Param that will be used to populate the inline style attributes data.
	 *                                               Associative array key used as style property name and the value will be used as inline style property value.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $classes            Param that will be used to populate the class data.
	 *                                               Associative array key used as class name and the value is associative array as the conditional check compared with prop value.
	 *                                               The conditional check array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The class will be added if all conditional check is true and will be removed if any of conditional check is false.
	 *
	 *     @type array           $visibility         Param that will be used to populate the visibility data.
	 *                                               Associative array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The element will visible if all conditional check is true and will be hidden if any of conditional check is false.
	 *
	 *     @type string          $target             HTML element selector target which the element will be modified. Default is empty string.
	 *                                               Dynamic module order class wildcard string is accepted: %%order_class%%
	 *
	 *     @type string          $hover_selector     HTML element selector which trigger the hover event. Default is empty string.
	 *                                               Dynamic module order class wildcard string is accepted: %%order_class%%
	 *
	 *     @type string          $render_slug        Render slug that will be used to calculate the module order class. Default is current module slug.
	 *
	 *     @type array           $custom_props       Defined custom props data.
	 *
	 *     @type array           $conditional_values Defined data sources for data toggle.
	 *
	 *     @type array           $required           List of required props key to render the element.
	 *                                               Will render the element if all of the props required keys is fulfilled.
	 *                                               Default is empty array it will try to gather any props name set in the 'content' context.
	 *                                               Set to false to disable conditional check.
	 *
	 *     @type array           $required_some      List of props key need to be fulfilled to render the element.
	 *                                               Will render the element if any one of the required props keys is fulfilled.
	 *                                               When defined, $required_some parameter will be prioritized over $required parameter.
	 * }
	 * @param boolean $echo Whether to print the output instead returning it.
	 *
	 * @return string|void
	 *
	 * @since 3.27.1
	 */
	public function render_element( $contexts = array(), $echo = false ) {
		// Define the array of defaults.
		$defaults = array(
			'tag'            => 'span',
			'content'        => '',
			'attrs'          => array(),
			'styles'         => array(),
			'classes'        => array(),
			'visibility'     => array(),
			'target'         => '',
			'hover_selector' => '',
			'render_slug'    => '',
			'custom_props'   => array(),
			'required'       => array(),
		);

		// Parse incoming $args into an array and merge it with $defaults.
		$contexts = wp_parse_args( $contexts, $defaults );

		// Set custom props data.
		if ( $contexts['custom_props'] && is_array( $contexts['custom_props'] ) ) {
			$this->set_custom_props( $contexts['custom_props'] );
		}
		unset( $contexts['custom_props'] );

		// Validate element tag.
		$tag = et_core_sanitize_element_tag( $contexts['tag'] );

		// Bail early when the tag is invalid.
		if ( ! $tag || is_wp_error( $tag ) ) {
			return '';
		}

		// Bail early when required props is not fulfilled.
		if ( ! $this->is_required_props_fulfilled( $contexts ) ) {
			return '';
		}

		// Populate the element data.
		$data = $this->populate_data( $contexts );

		// Bail early when data is empty.
		if ( ! $data ) {
			return '';
		}

		$desktop_attrs   = '';
		$desktop_styles  = array();
		$desktop_classes = array();

		// Generate desktop attribute.
		foreach ( et_()->array_get( $data, 'attrs.desktop', array() ) as $attr_key => $attr_value ) {

			if ( 'style' === $attr_key ) {
				foreach ( explode( ';', $attr_value ) as $inline_style ) {
					$inline_styles = explode( ':', $inline_style );

					if ( count( $inline_styles ) === 2 ) {
						$desktop_styles[ $inline_styles[0] ] = $inline_styles[1];
					}
				}

				continue;
			} elseif ( 'class' === $attr_key ) {
				if ( is_string( $attr_value ) ) {
					$desktop_classes = array_merge( $desktop_classes, explode( ' ', $attr_value ) );
				} elseif ( is_array( $attr_value ) ) {
					$desktop_classes = array_merge( $desktop_classes, $attr_value );
				}
			} else {
				if ( ! is_string( $attr_value ) ) {
					$attr_value = esc_attr( wp_json_encode( $attr_value ) );
				}

				/**
				 * Hide image tag instead showing broken image tag output
				 * This is needed because there is a case image
				 * just displayed on non desktop mode only.
				 */
				if ( 'src' === $attr_key && ! $attr_value ) {
					$desktop_classes[] = 'et_multi_view_hidden_image';
				}

				$desktop_attrs .= ' ' . esc_attr( $attr_key ) . '="' . et_core_esc_previously( $attr_value ) . '"';
			}
		}

		// Inject desktop inline style attribute.
		foreach ( et_()->array_get( $data, 'styles.desktop', array() ) as $style_key => $style_value ) {
			$desktop_styles[ $style_key ] = $style_value;
		}

		if ( $desktop_styles ) {
			$styles = array();

			foreach ( $desktop_styles as $style_key => $style_value ) {
				$styles[] = esc_attr( $style_key ) . ':' . et_core_esc_previously( $style_value );
			}

			$desktop_attrs .= ' style="' . implode( ';', $styles ) . '"';
		}

		// Inject desktop class attribute.
		foreach ( et_()->array_get( $data, 'classes.desktop', array() ) as $class_action => $class_names ) {
			foreach ( $class_names as $class_name ) {
				if ( 'remove' === $class_action && in_array( $class_name, $desktop_classes, true ) ) {
					$desktop_classes = array_diff( $desktop_classes, array( $class_name ) );
				}

				if ( 'add' === $class_action && ! in_array( $class_name, $desktop_classes, true ) ) {
					$desktop_classes[] = $class_name;
				}
			}
		}

		// Inject desktop visibility class attribute.
		if ( ! et_()->array_get( $data, 'visibility.desktop', true ) ) {
			$desktop_classes[] = 'et_multi_view_hidden';
		}

		if ( $desktop_classes ) {
			$desktop_attrs .= ' class="' . implode( ' ', array_unique( $desktop_classes ) ) . '"';
		}

		// Render the output.
		if ( $this->is_self_closing_tag( $tag ) ) {
			$output = sprintf(
				'<%1$s%2$s%3$s />',
				et_core_esc_previously( $tag ), // #1
				et_core_esc_previously( $desktop_attrs ), // #2
				et_core_esc_previously(
					$this->render_attrs(
						array(
							'target'         => $contexts['target'],
							'hover_selector' => $contexts['hover_selector'],
							'render_slug'    => $contexts['render_slug'],
						),
						false,
						$data
					)
				) // #3
			);
		} else {
			$output = sprintf(
				'<%1$s%2$s%3$s>%4$s</%1$s>',
				et_core_esc_previously( $tag ), // #1
				et_core_esc_previously( $desktop_attrs ), // #2
				et_core_esc_previously(
					$this->render_attrs(
						array(
							'target'         => $contexts['target'],
							'hover_selector' => $contexts['hover_selector'],
							'render_slug'    => $contexts['render_slug'],
						),
						false,
						$data
					)
				), // #3
				et_core_esc_previously( et_()->array_get( $data, 'content.desktop', '' ) ) // #4
			);
		}

		if ( ! $echo ) {
			return $output;
		}

		echo et_core_esc_previously( $output );
	}

	/**
	 * Get or render the multi content attribute.
	 *
	 * @param array $contexts {
	 *     Data contexts.
	 *
	 *     @type string          $content            Param that will be used to populate the content data.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $attrs              Param that will be used to populate the attributes data.
	 *                                               Associative array key used as attribute name and the value will be used as attribute value.
	 *                                               Special case for 'class' and 'style' attribute name will only generating output for desktop mode.
	 *                                               Use 'styles' or 'classes' context for multi modes usage.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $styles             Param that will be used to populate the inline style attributes data.
	 *                                               Associative array key used as style property name and the value will be used as inline style property value.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $classes            Param that will be used to populate the class data.
	 *                                               Associative array key used as class name and the value is associative array as the conditional check compared with prop value.
	 *                                               The conditional check array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The class will be added if all conditional check is true and will be removed if any of conditional check is false.
	 *
	 *     @type array           $visibility         Param that will be used to populate the visibility data.
	 *                                               Associative array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The element will visible if all conditional check is true and will be hidden if any of conditional check is false.
	 *
	 *     @type string          $target             HTML element selector target which the element will be modified. Default is empty string.
	 *                                               Dynamic module order class wildcard string is accepted: %%order_class%%
	 *
	 *     @type string          $hover_selector     HTML element selector which trigger the hover event. Default is empty string.
	 *                                               Dynamic module order class wildcard string is accepted: %%order_class%%
	 *
	 *     @type string          $render_slug        Render slug that will be used to calculate the module order class. Default is current module slug.
	 *
	 *     @type array           $custom_props       Defined custom props data.
	 *
	 *     @type array           $conditional_values Defined data sources for data toggle.
	 *
	 *     @type array           $required           List of required props key to render the element.
	 *                                               Will returning empty string if any required props is empty.
	 *                                               Default is empty array it will try to gather any props name set in the 'content' context.
	 *                                               Set to false to disable conditional check.
	 * }
	 * @param bool  $echo Whether to print the output instead returning it.
	 * @param array $populated_data Pre populated data in case just need to format the attributes output.
	 * @param bool  $as_array Whether to return the output as array or string.
	 *
	 * @return string|void
	 *
	 * @since 3.27.1
	 */
	public function render_attrs( $contexts = array(), $echo = false, $populated_data = null, $as_array = false ) {
		// Define the array of defaults.
		$defaults = array(
			'content'        => '',
			'attrs'          => array(),
			'styles'         => array(),
			'classes'        => array(),
			'visibility'     => array(),
			'target'         => '',
			'hover_selector' => '',
			'render_slug'    => '',
			'custom_props'   => array(),
		);

		// Parse incoming $args into an array and merge it with $defaults.
		$contexts = wp_parse_args( $contexts, $defaults );

		if ( $contexts['custom_props'] && is_array( $contexts['custom_props'] ) ) {
			$this->set_custom_props( $contexts['custom_props'] );
		}

		unset( $contexts['custom_props'] );

		$data = is_null( $populated_data ) ? $this->populate_data( $contexts ) : $populated_data;

		if ( $data ) {
			foreach ( $data as $context => $modes ) {
				// Distinct the values to omit duplicate values across modes.
				$data[ $context ] = $this->distinct_values( $modes );

				// Remove context data if there is only desktop mode data available.
				// This intended to avoid unnecessary multi view attribute rendered if there is only desktop
				// mode data is available.
				if ( 1 === count( $data[ $context ] ) && isset( $data[ $context ]['desktop'] ) ) {
					unset( $data[ $context ] );
				}
			}
		}

		$output = '';

		if ( $data ) {
			if ( isset( $data['content'] ) ) {
				foreach ( $data['content'] as $mode => $content ) {
					if ( ! $content ) {
						continue;
					}

					$content = str_replace( '&lt;', htmlentities( '&lt;' ), $content );
					$content = str_replace( '&gt;', htmlentities( '&gt;' ), $content );

					$data['content'][ $mode ] = $content;
				}
			}

			$content_desktop = et_()->array_get( $data, 'content.desktop', null );
			$content_tablet  = et_()->array_get( $data, 'content.tablet', null );
			$content_phone   = et_()->array_get( $data, 'content.phone', null );

			$visibility_desktop = et_()->array_get( $data, 'visibility.desktop', null );
			$visibility_tablet  = et_()->array_get( $data, 'visibility.tablet', null );
			$visibility_phone   = et_()->array_get( $data, 'visibility.phone', null );

			$is_hidden_on_load_tablet = false;
			if ( ! is_null( $content_tablet ) && $content_desktop !== $content_tablet ) {
				$is_hidden_on_load_tablet = true;
			}

			if ( ! is_null( $visibility_tablet ) && $visibility_desktop !== $visibility_tablet ) {
				$is_hidden_on_load_tablet = true;
			}

			$is_hidden_on_load_phone = false;
			if ( ! is_null( $content_phone ) && $content_desktop !== $content_phone ) {
				$is_hidden_on_load_phone = true;
			}

			if ( ! is_null( $visibility_phone ) && $visibility_desktop !== $visibility_phone ) {
				$is_hidden_on_load_phone = true;
			}

			$data = array(
				'schema' => $data,
				'slug'   => $this->slug,
			);

			if ( ! empty( $contexts['target'] ) ) {
				if ( false !== strpos( $contexts['target'], '%%order_class%%' ) ) {
					$render_slug = ! empty( $contexts['render_slug'] ) ? $contexts['render_slug'] : $this->slug;
					$order_class = ET_Builder_Element::get_module_order_class( $render_slug );

					if ( $order_class ) {
						$data['target'] = str_replace( '%%order_class%%', ".{$order_class}", $contexts['target'] );
					}
				} else {
					$data['target'] = $contexts['target'];
				}
			}

			if ( ! empty( $contexts['hover_selector'] ) ) {
				if ( false !== strpos( $contexts['hover_selector'], '%%order_class%%' ) ) {
					$render_slug = ! empty( $contexts['render_slug'] ) ? $contexts['render_slug'] : $this->slug;
					$order_class = ET_Builder_Element::get_module_order_class( $render_slug );

					if ( $order_class ) {
						$data['hover_selector'] = str_replace( '%%order_class%%', ".{$order_class}", $contexts['hover_selector'] );
					}
				} else {
					$data['hover_selector'] = $contexts['hover_selector'];
				}
			}

			$data_attr_key = esc_attr( $this->data_attr_key );

			if ( $as_array ) {
				$output = array();

				$output[ $data_attr_key ] = esc_attr( wp_json_encode( $data ) );

				if ( $is_hidden_on_load_tablet ) {
					$output[ $data_attr_key . '-load-tablet-hidden' ] = 'true';
				}

				if ( $is_hidden_on_load_phone ) {
					$output[ $data_attr_key . '-load-phone-hidden' ] = 'true';
				}
			} else {
				// Format the html data attribute output.
				$output = sprintf( ' %1$s="%2$s"', $data_attr_key, esc_attr( wp_json_encode( $data ) ) );

				if ( $is_hidden_on_load_tablet ) {
					$output .= sprintf( ' %1$s="%2$s"', $data_attr_key . '-load-tablet-hidden', 'true' );
				}

				if ( $is_hidden_on_load_phone ) {
					$output .= sprintf( ' %1$s="%2$s"', $data_attr_key . '-load-phone-hidden', 'true' );
				}
			}
		}

		if ( ! $echo || $as_array ) {
			return $output;
		}

		echo et_core_esc_previously( $output );
	}

	/**
	 * Populate the multi view data.
	 *
	 * @param array $contexts {
	 *     Data contexts.
	 *
	 *     @type string          $content            Param that will be used to populate the content data.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $attrs              Param that will be used to populate the attributes data.
	 *                                               Associative array key used as attribute name and the value will be used as attribute value.
	 *                                               Special case for 'class' and 'style' attribute name will only generating output for desktop mode.
	 *                                               Use 'styles' or 'classes' context for multi modes usage.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $styles             Param that will be used to populate the inline style attributes data.
	 *                                               Associative array key used as style property name and the value will be used as inline style property value.
	 *                                               Use props name wrapped with 2 curly brackets within the value for find & replace wildcard: {{props_name}}
	 *
	 *     @type array           $classes            Param that will be used to populate the class data.
	 *                                               Associative array key used as class name and the value is associative array as the conditional check compared with prop value.
	 *                                               The conditional check array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The class will be added if all conditional check is true and will be removed if any of conditional check is false.
	 *
	 *     @type array           $visibility         Param that will be used to populate the visibility data.
	 *                                               Associative array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The element will visible if all conditional check is true and will be hidden if any of conditional check is false.
	 * }
	 *
	 * @return array
	 *
	 * @since 3.27.1
	 */
	public function populate_data( $contexts = array() ) {
		$data = array();

		// Define the array of defaults.
		$defaults = array(
			'content'    => '',
			'attrs'      => array(),
			'styles'     => array(),
			'classes'    => array(),
			'visibility' => array(),
		);

		// Parse incoming $args into an array and merge it with $defaults.
		$contexts = wp_parse_args( $contexts, $defaults );

		foreach ( $contexts as $context => $context_args ) {
			// Skip if the context is not listed as default.
			if ( ( ! isset( $defaults[ $context ] ) ) ) {
				continue;
			}

			$callback = array( $this, "populate_data__{$context}" );

			// Skip if the context has no callback handler.
			if ( ! is_callable( $callback ) ) {
				continue;
			}

			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			$context_data = call_user_func( $callback, $context_args );

			// Skip if the context data is empty or WP_Error object.
			if ( ! $context_data || is_wp_error( $context_data ) ) {
				continue;
			}

			// Set the context data for each breakpoints.
			foreach ( $context_data as $mode => $context_value ) {
				$data[ $context ][ $mode ] = $context_value;
			}
		}

		return $this->filter_data( $data );
	}

	/**
	 * Populate content data context.
	 *
	 * @since 3.27.1
	 *
	 * @param string $content Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__content( $content ) {
		if ( ! $content || ! is_string( $content ) ) {
			return new WP_Error();
		}

		$data = array();

		if ( preg_match_all( $this->pattern, $content, $matches, PREG_SET_ORDER, 0 ) ) {
			$replacements = array();

			foreach ( $matches as $match ) {
				if ( ! isset( $match[1] ) ) {
					continue;
				}

				$values = $this->get_values( $match[1] );

				if ( $values ) {
					$replacements[ $match[0] ] = array(
						'context' => 'content',
						'name'    => $match[1],
						'values'  => $values,
					);
				}
			}

			if ( $replacements ) {
				foreach ( $replacements as $find => $replacement ) {
					foreach ( $replacement['values'] as $mode => $value ) {
						// Manipulate the value if needed.
						$value = $this->filter_value(
							$value,
							array_merge(
								$replacement,
								array(
									'mode' => $mode,
								)
							)
						);

						if ( ! is_wp_error( $value ) ) {
							if ( ! isset( $data[ $mode ] ) ) {
								$data[ $mode ] = $content;
							}

							$data[ $mode ] = str_replace( $find, $value, $data[ $mode ] );
						}
					}
				}
			}
		} else {
			// Manipulate the value if needed.
			$value = $this->filter_value(
				$content,
				array(
					'context' => 'content',
					'mode'    => 'desktop',
				)
			);

			if ( ! is_wp_error( $value ) ) {
				// Update the multi content data.
				$data['desktop'] = $value;
			}
		}

		return $data;
	}

	/**
	 * Populate attrs data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $attrs Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__attrs( $attrs ) {
		if ( ! $attrs || ! is_array( $attrs ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( $attrs as $attr_key => $attr_value ) {
			if ( preg_match_all( $this->pattern, $attr_value, $matches, PREG_SET_ORDER, 0 ) ) {
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$values = $this->get_values( $match[1] );

					if ( $values ) {
						foreach ( $values as $mode => $value ) {
							// Manipulate the value if needed.
							$value = $this->filter_value(
								$value,
								array(
									'context'  => 'attrs',
									'mode'     => $mode,
									'name'     => $match[1],
									'attr_key' => $attr_key,
								)
							);

							if ( ! is_wp_error( $value ) ) {
								$value = et_core_esc_attr( $attr_key, $value );
							}

							if ( ! is_wp_error( $value ) ) {
								if ( ! isset( $data[ $mode ][ $attr_key ] ) ) {
									$data[ $mode ][ $attr_key ] = $attr_value;
								}

								$data[ $mode ][ $attr_key ] = str_replace( $match[0], $value, $data[ $mode ][ $attr_key ] );
							}
						}
					}
				}
			} else {
				// Manipulate the value if needed.
				$attr_value = $this->filter_value(
					$attr_value,
					array(
						'context'  => 'attrs',
						'mode'     => 'desktop',
						'attr_key' => $attr_key,
					)
				);

				if ( ! is_wp_error( $attr_value ) ) {
					$attr_value = et_core_esc_attr( $attr_key, $attr_value );
				}

				if ( ! is_wp_error( $attr_value ) ) {
					// Update the multi content data.
					$data['desktop'][ $attr_key ] = $attr_value;
				}
			}
		}

		return $data;
	}

	/**
	 * Populate styles data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $styles Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__styles( $styles ) {
		if ( ! $styles || ! is_array( $styles ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( $styles as $style_key => $style_value ) {
			if ( preg_match_all( $this->pattern, $style_value, $matches, PREG_SET_ORDER, 0 ) ) {
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$values = $this->get_values( $match[1] );

					if ( $values ) {
						foreach ( $values as $mode => $value ) {
							// Manipulate the value if needed.
							$value = $this->filter_value(
								$value,
								array(
									'context'   => 'styles',
									'mode'      => $mode,
									'name'      => $match[1],
									'style_key' => $style_key,
								)
							);

							if ( ! is_wp_error( $value ) ) {
								if ( ! isset( $data[ $mode ][ $style_key ] ) ) {
									$data[ $mode ][ $style_key ] = $style_value;
								}

								$full_style_value = str_replace( $match[0], $value, $data[ $mode ][ $style_key ] );

								if ( ! is_wp_error( et_core_esc_attr( 'style', $style_key . ':' . $full_style_value ) ) ) {
									$data[ $mode ][ $style_key ] = $full_style_value;
								}
							}
						}
					}
				}
			} else {
				// Manipulate the value if needed.
				$style_value = $this->filter_value(
					$style_value,
					array(
						'context'   => 'styles',
						'mode'      => 'desktop',
						'style_key' => $style_key,
					)
				);

				if ( ! is_wp_error( $style_value ) && ! is_wp_error( et_core_esc_attr( 'style', $style_key . ':' . $style_value ) ) ) {
					$data['desktop'][ $style_key ] = $style_value;
				}
			}
		}

		return $data;
	}

	/**
	 * Populate classes data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $classes Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__classes( $classes ) {
		if ( ! $classes || ! is_array( $classes ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( self::get_modes() as $mode ) {
			foreach ( $classes as $class_name => $conditionals ) {
				$class_name = et_core_esc_attr( 'class', $class_name );

				if ( is_wp_error( $class_name ) ) {
					continue;
				}

				$conditionals_match = array();

				foreach ( $conditionals as $name => $value_compare ) {
					$value = $this->get_inherit_value( $name, $mode );

					// Manipulate the value if needed.
					$value = $this->filter_value(
						$value,
						array(
							'context' => 'classes',
							'mode'    => $mode,
							'name'    => $name,
						)
					);

					if ( ! is_wp_error( $value ) ) {
						$conditionals_match[ $name ] = self::compare_value( $value, $value_compare ) ? 1 : 0;
					}
				}

				$action = count( $conditionals ) === array_sum( $conditionals_match ) ? 'add' : 'remove';

				if ( ! isset( $data[ $mode ][ $action ] ) ) {
					$data[ $mode ][ $action ] = array();
				}

				$data[ $mode ][ $action ][] = $class_name;
			}
		}

		return $data;
	}

	/**
	 * Populate visibility data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $visibility Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__visibility( $visibility ) {
		if ( ! $visibility || ! is_array( $visibility ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( self::get_modes() as $mode ) {
			if ( ! isset( $data[ $mode ] ) ) {
				$data[ $mode ] = array();
			}

			foreach ( $visibility as $name => $value_compare ) {
				$value = $this->get_inherit_value( $name, $mode );

				// Manipulate the value if needed.
				$value = $this->filter_value(
					$value,
					array(
						'context' => 'visibility',
						'mode'    => $mode,
						'name'    => $name,
					)
				);

				if ( ! is_wp_error( $value ) ) {
					$data[ $mode ][ $name ] = self::compare_value( $value, $value_compare ) ? 1 : 0;
				}
			}
		}

		foreach ( $data as $mode => $value ) {
			$data[ $mode ] = count( $value ) === array_sum( $value );
		}

		return $data;
	}

	/**
	 * Props value filter.
	 *
	 * @since 3.27.1
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that available when passing attrs value as array such as styes. Example: padding-top, margin-bottom, etc.
	 * }
	 *
	 * @return mixed|WP_Error return WP_Error to skip the data.
	 */
	protected function filter_value( $raw_value, $args = array() ) {
		if ( $this->module instanceof ET_Builder_Element && method_exists( $this->module, 'multi_view_filter_value' ) && is_callable( array( $this->module, 'multi_view_filter_value' ) ) ) {
			/**
			 * Execute the filter value function defined for current module.
			 *
			 * @since 3.27.1
			 *
			 * @param mixed $raw_value Props raw value.
			 * @param array $args {
			 *     Context data.
			 *
			 *     @type string $context      Context param: content, attrs, visibility, classes.
			 *     @type string $name         Module options props name.
			 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
			 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
			 *     @type string $attr_sub_key Attribute sub key that available when passing attrs value as array such as styes. Example: padding-top, margin-bottom, etc.
			 * }
			 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Current instance.
			 *
			 * @return mixed
			 */
			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			$raw_value = call_user_func( array( $this->module, 'multi_view_filter_value' ), $raw_value, $args, $this );

			// Bail early if the $raw_value is WP_error object.
			if ( is_wp_error( $raw_value ) ) {
				return $raw_value;
			}
		}

		$context = isset( $args['context'] ) ? $args['context'] : '';
		$name    = isset( $args['name'] ) ? $args['name'] : '';
		$mode    = isset( $args['mode'] ) ? $args['mode'] : 'desktop';

		$content_fields = array(
			'content',
			'raw_content',
			'description',
			'footer_content',
		);

		if ( $raw_value && 'content' === $context && 'desktop' !== $mode && in_array( $name, $content_fields, true ) ) {
			$raw_value = str_replace( array( '%22', '%92', '%91', '%93' ), array( '"', '\\', '&#91;', '&#93;' ), $raw_value );

			// Cleaning up invalid starting <\p> tag.
			$cleaned_value = preg_replace( '/(^<\/p>)(.*)/ius', '$2', $raw_value );

			// Cleaning up invalid ending <p> tag.
			$cleaned_value = preg_replace( '/(.*)(<p>$)/ius', '$1', $cleaned_value );

			// Override the raw value.
			if ( $raw_value !== $cleaned_value ) {
				$raw_value = trim( $cleaned_value, "\n" );

				if ( 'raw_content' !== $name ) {
					$raw_value = force_balance_tags( $raw_value );
				}
			}

			// Try to process shortcode.
			if ( false !== strpos( $raw_value, '&#91;' ) && false !== strpos( $raw_value, '&#93;' ) ) {
				$raw_value = do_shortcode( et_pb_fix_shortcodes( str_replace( array( '&#91;', '&#93;' ), array( '[', ']' ), $raw_value ), true ) );
			}
		}

		return $raw_value;
	}

	/**
	 * Filter populated multi view data
	 *
	 * The use case of this method is to manipulate populated data such as injecting srcset attributes.
	 *
	 * @since 3.27.1
	 *
	 * @param array $data All populated raw data. The value value passed to this method has been processed by filter_value method.
	 *
	 * @return array
	 */
	protected function filter_data( $data ) {
		static $defaults = array( false, false, false, false );

		// Inject the image srcset and sizes attributes data.
		if ( ! empty( $data['attrs'] ) && et_is_responsive_images_enabled() ) {
			foreach ( $data['attrs'] as $mode => $attrs ) {
				// Skip if src attr is empty.
				if ( ! isset( $attrs['src'] ) ) {
					continue;
				}

				$srcset_sizes = et_get_image_srcset_sizes( $attrs['src'] );

				if ( isset( $srcset_sizes['srcset'], $srcset_sizes['sizes'] ) && $srcset_sizes['srcset'] && $srcset_sizes['sizes'] ) {
					$data['attrs'][ $mode ]['srcset'] = $srcset_sizes['srcset'];
					$data['attrs'][ $mode ]['sizes']  = $srcset_sizes['sizes'];
				} else {
					unset( $data['attrs'][ $mode ]['srcset'] );
					unset( $data['attrs'][ $mode ]['sizes'] );
				}
			}
		}

		if ( $this->module instanceof ET_Builder_Element && method_exists( $this->module, 'multi_view_filter_data' ) && is_callable( array( $this->module, 'multi_view_filter_data' ) ) ) {
			/**
			 * Execute the filter data function defined for current module.
			 *
			 * @since 3.27.1
			 *
			 * @param mixed                                     $data       All populated raw data.
			 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Current instance.
			 *
			 * @return mixed
			 */
			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			$data = call_user_func( array( $this->module, 'multi_view_filter_data' ), $data, $this );
		}

		return $data;
	}

	/**
	 * Normalize values to inject value for all modes
	 *
	 * @since 3.27.1
	 *
	 * @param array $values Raw values.
	 *
	 * @return array Normalized values for all modes.
	 */
	protected function normalize_values( $values = array() ) {
		$normalized = array();

		if ( is_array( $values ) ) {
			if ( ! isset( $values['desktop'] ) ) {
				$values['desktop'] = '';
			}

			if ( ! isset( $values['tablet'] ) ) {
				$values['tablet'] = isset( $values['desktop'] ) ? $values['desktop'] : '';
			}

			if ( ! isset( $values['phone'] ) ) {
				$values['phone'] = isset( $values['tablet'] ) ? $values['tablet'] : ( isset( $values['desktop'] ) ? $values['desktop'] : '' );
			}

			if ( ! isset( $values['hover'] ) ) {
				$values['hover'] = isset( $values['desktop'] ) ? $values['desktop'] : '';
			}

			foreach ( self::get_modes() as $mode ) {
				if ( ! isset( $values[ $mode ] ) ) {
					continue;
				}

				$normalized[ $mode ] = $values[ $mode ];
			}
		} else {
			foreach ( self::get_modes() as $mode ) {
				$normalized[ $mode ] = $values;
			}
		}

		return $normalized;
	}

	/**
	 * Distinct values
	 *
	 * @since 3.27.1
	 *
	 * @param array $values Raw values.
	 *
	 * @return array Filtered out for mode that has duplicate values.
	 */
	public function distinct_values( $values ) {
		$temp_values = array();

		foreach ( $values as $mode => $value ) {
			// Decode HTML special characters such as "&amp;" to "&"
			// to make the value consistence and the comparison is accurate.
			// $temp_values variable is not used anywhere except to compare
			// the values of each view mode. It will not printed anywhere.
			// So we won't need to sanitize it.
			if ( is_string( $value ) ) {
				$value = htmlspecialchars_decode( $value );
			}

			// Stringify the value so can be easily compared.
			$temp_values[ $mode ] = wp_json_encode( $value );
		}

		// Unset hover mode if same with desktop mode.
		if ( isset( $temp_values['desktop'], $temp_values['hover'] ) && $temp_values['desktop'] === $temp_values['hover'] ) {
			unset( $temp_values['hover'] );
		}

		// Unset tablet mode if same with desktop mode.
		if ( isset( $temp_values['desktop'], $temp_values['tablet'] ) && $temp_values['desktop'] === $temp_values['tablet'] ) {
			unset( $temp_values['tablet'] );
		}

		// Unset phone mode if same with tablet mode.
		if ( isset( $temp_values['tablet'], $temp_values['phone'] ) && $temp_values['tablet'] === $temp_values['phone'] ) {
			unset( $temp_values['phone'] );
		}

		// Unset phone mode if same with desktop mode but no tablet mode defined.
		if ( isset( $temp_values['desktop'], $temp_values['phone'] ) && ! isset( $temp_values['tablet'] ) && $temp_values['desktop'] === $temp_values['phone'] ) {
			unset( $temp_values['phone'] );
		}

		$filtered_values = array();

		foreach ( self::get_modes() as $mode ) {
			if ( ! isset( $temp_values[ $mode ] ) ) {
				continue;
			}

			if ( ! isset( $values[ $mode ] ) ) {
				continue;
			}

			$filtered_values[ $mode ] = $values[ $mode ];
		}

		return $filtered_values;
	}

	/**
	 * Check wether self closing tag or not
	 *
	 * @since 3.27.1?
	 *
	 * @param string $tag Element tag.
	 *
	 * @return boolean
	 */
	protected function is_self_closing_tag( $tag ) {
		$self_closing_tags = array(
			'area',
			'base',
			'br',
			'col',
			'embed',
			'hr',
			'img',
			'input',
			'link',
			'meta',
			'param',
			'source',
			'track',
			'wbr',
		);

		return in_array( $tag, $self_closing_tags, true );
	}

	/**
	 * Check if required props is fulfilled
	 *
	 * @since 3.27.1?
	 *
	 * @param string $contexts Element contexts data.
	 *
	 * @return bool
	 */
	protected function is_required_props_fulfilled( $contexts ) {
		$required_some_keys = et_()->array_get( $contexts, 'required_some', false );

		if ( $required_some_keys ) {
			if ( ! is_array( $required_some_keys ) ) {
				$required_some_keys = explode( ',', $required_some_keys );
			}

			$fulfilled = false;

			foreach ( $required_some_keys as $required_key => $required_value_compare ) {
				// Handle zero indexed data.
				if ( is_numeric( $required_key ) ) {
					$fulfilled = $this->has_value( $required_value_compare );
				} else {
					$fulfilled = $this->has_value( $required_key, $required_value_compare );
				}

				// Break the loop once any of required props key is fulfilled.
				if ( $fulfilled ) {
					break;
				}
			}

			// Bail early if required_some param is defined, no need to process further.
			// The required_some param is prioritized over required param.
			return $fulfilled;
		}

		$required_keys = et_()->array_get( $contexts, 'required', array() );

		// Bail early when the required parameter defined as false.
		if ( false === $required_keys ) {
			return true;
		}

		if ( $required_keys && ! is_array( $required_keys ) ) {
			$required_keys = explode( ',', $required_keys );
		}

		// Populate the required keys from the content if it is empty.
		if ( ! $required_keys ) {
			$content = et_()->array_get( $contexts, 'content', '' );

			if ( ! empty( $content ) && preg_match_all( $this->pattern, $content, $matches, PREG_SET_ORDER, 0 ) ) {
				// Populate the required keys from the content.
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$required_keys[] = $match[1];
				}
			}
		}

		// Bail early when the required keys is empty.
		if ( ! $required_keys ) {
			return true;
		}

		$fulfilled = true;

		foreach ( $required_keys as $required_key => $required_value_compare ) {
			if ( ( ! $required_value_compare && is_numeric( $required_key ) ) || ( ! $required_key && ! is_numeric( $required_key ) ) ) {
				$fulfilled = false;
				break;
			}

			// Handle zero indexed data.
			if ( is_numeric( $required_key ) ) {
				$fulfilled = $this->has_value( $required_value_compare );
			} else {
				$fulfilled = $this->has_value( $required_key, $required_value_compare );
			}

			// Break the loop once any of required props key is not fulfilled.
			if ( ! $fulfilled ) {
				break;
			}
		}

		return $fulfilled;
	}

	/**
	 * Sort conditionals values list by number of props and conditionals params.
	 *
	 * @since 3.27.1
	 *
	 * @param array $a Array data to compare.
	 * @param array $b Array data to compare.
	 *
	 * @return array
	 */
	public function sort_conditional_values( $a, $b ) {
		$a_priority = count( $a['props'] ) + count( $a['conditionals'] );
		$b_priority = count( $b['props'] ) + count( $b['conditionals'] );

		if ( $a_priority === $b_priority ) {
			return $a['order'] - $b['order'];
		}

		return ( $a_priority < $b_priority ) ? -1 : 1;
	}

	/**
	 * Gets a prop from Module.
	 *
	 * @since 4.10.0
	 *
	 * @param string $name    Prop name.
	 * @param string $default Default value. Defaults to ''.
	 *
	 * @return string
	 */
	public function get_prop( $name, $default = '' ) {
		$props = $this->get_prepped_props();
		return isset( $props[ $name ] ) ? $props[ $name ] : '';
	}

	/**
	 * Prepares the modules props to be consumed by this helper.
	 *
	 * @since 4.10.0
	 *
	 * @return array
	 */
	public function get_prepped_props() {
		$props = [];

		if ( property_exists( $this->module, 'props' ) && $this->module->props && is_array( $this->module->props ) ) {
			$props = $this->module->props;

			if ( empty( $props['content'] ) && property_exists( $this->module, 'content' ) ) {
				$props['content'] = $this->module->content;
			}

			if ( in_array( $this->module->slug, array( 'et_pb_code', 'et_pb_fullwidth_code' ), true ) ) {
				if ( isset( $props['content'] ) ) {
					$props['raw_content'] = $props['content'];
				}

				if ( isset( $props[ 'content' . self::$hover_enabled_suffix ] ) ) {
					$props[ 'raw_content' . self::$hover_enabled_suffix ] = $props[ 'content' . self::$hover_enabled_suffix ];
				}

				if ( isset( $props[ 'content' . self::$responsive_enabled_suffix ] ) ) {
					$props[ 'raw_content' . self::$responsive_enabled_suffix ] = $props[ 'content' . self::$responsive_enabled_suffix ];
				}
			}
		}

		return $props;
	}

	/**
	 * Gets the Module props.
	 *
	 * The Module is restricted in scope. Hence we use this getter.
	 *
	 * @since 3.29
	 *
	 * @used-by ET_Builder_Module_Woocommerce_Description::multi_view_filter_value()
	 *
	 * @return array
	 */
	public function get_module_props() {
		return $this->get_prepped_props();
	}
}

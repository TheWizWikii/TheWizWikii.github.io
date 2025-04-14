<?php
/**
 * Sticky Helper
 *
 * @package     Divi
 * @sub-package Builder
 * @since 4.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Sticky Options helper methods
 *
 * @since 4.6.0
 *
 * Class ET_Builder_Module_Sticky_Options
 */
class ET_Builder_Module_Helper_Sticky_Options {

	/**
	 * Class instance object
	 *
	 * @var object Class instance.
	 */
	private static $instance;

	/**
	 * Get instance of ET_Builder_Module_Sticky_Options.
	 *
	 * @since 4.6.0
	 *
	 * @return object|ET_Builder_Module_Sticky_Options
	 */
	public static function get() {
		if ( empty( self::$instance ) ) {
			self::$instance = new ET_Builder_Module_Helper_Sticky_Options();
		}

		return self::$instance;
	}

	/**
	 * Get Sticky field suffix
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	public function get_suffix() {
		return '__sticky';
	}

	/**
	 * Get Sticky field enabled suffix
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	public function get_enabled_suffix() {
		return '__sticky_enabled';
	}

	/**
	 * Returns the field original name by removing the `_sticky` or `__sticky_enabled` suffix if it exists.
	 *
	 * @since 4.6.0
	 *
	 * @param string $name Field name.
	 *
	 * @return string
	 */
	public function get_field_base_name( $name ) {
		$regex   = "/(.*)({$this->get_suffix()}|{$this->get_enabled_suffix()})$/";
		$replace = '${1}';

		return preg_replace( $regex, $replace, $name );
	}

	/**
	 * Get valid sticky_position which implies module is sticky element
	 *
	 * @since 4.6.0
	 *
	 * @return array
	 */
	public function get_valid_sticky_positions() {
		return array( 'top', 'bottom', 'top_bottom' );
	}

	/**
	 * Check if the setting has enabled sticky options
	 *
	 * @since 4.6.0
	 *
	 * @param string $setting Field name.
	 * @param array  $attrs   Module attributes.
	 *
	 * @return bool
	 */
	public function is_enabled( $setting, $attrs ) {
		$name = 'background_color' === $setting ? 'background' : $setting;

		$field = $this->get_sticky_enabled_field( $name );

		$value = ! empty( $attrs[ $field ] ) ? $attrs[ $field ] : '';

		return ! empty( $value ) && strpos( $value, 'on' ) === 0;
	}

	/**
	 * Check if current module is inside sticky module
	 *
	 * @since 4.6.2
	 *
	 * @return bool
	 */
	public function is_inside_sticky_module() {
		global $is_inside_sticky_module;

		return $is_inside_sticky_module;
	}

	/**
	 * Check if module with given attributes is a sticky module. Need to consider responsive value:
	 * desktop might have non sticky element value but its smaller breakpoint has sticky element value
	 *
	 * @since 4.6.0
	 *
	 * @param array $attrs Module attributes.
	 *
	 * @return bool
	 */
	public function is_sticky_module( $attrs ) {
		// No nested sticky element.
		if ( $this->is_inside_sticky_module() ) {
			return false;
		}

		// Bail if there is fields which its selected value are incompatible to sticky mechanism.
		if ( $this->has_incompatible_attrs( $attrs ) ) {
			return false;
		}

		$sticky_position = et_pb_responsive_options()->get_checked_property_value( $attrs, 'sticky_position', 'none', true );

		// Non responsive.
		if ( is_string( $sticky_position ) ) {
			return in_array( $sticky_position, $this->get_valid_sticky_positions(), true );
		}

		if ( ! is_array( $sticky_position ) ) {
			return false;
		}

		// Responsive.
		$is_sticky = false;

		foreach ( $sticky_position as $device => $position ) {
			if ( in_array( $position, $this->get_valid_sticky_positions(), true ) ) {
				$is_sticky = true;

				break;
			}
		}

		return $is_sticky;
	}

	/**
	 * Returns the field / setting name with sticky suffix
	 * E.g.: get_sticky_enabled_field('test') => 'test__sticky'
	 *
	 * @since 4.6.0
	 *
	 * @param string $setting Field name.
	 *
	 * @return string
	 */
	public function get_sticky_field( $setting ) {
		return "{$this->get_field_base_name($setting)}{$this->get_suffix()}";
	}

	/**
	 * Returns the sticky enabled setting field name
	 * E.g.: get_sticky_enabled_field('test') => 'test__sticky_enabled'
	 *
	 * @since 4.6.0
	 *
	 * @param string $setting Field name.
	 *
	 * @return string
	 */
	public function get_sticky_enabled_field( $setting ) {
		return "{$this->get_field_base_name($setting)}{$this->get_enabled_suffix()}";
	}

	/**
	 * Returns setting value for sticky if enabled, otherwise return the default value
	 *
	 * @since 4.6.0
	 *
	 * @param string $setting Field name.
	 * @param array  $attrs   Module attributes.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get_value( $setting, $attrs, $default = null ) {
		return $this->is_enabled( $setting, $attrs )
			? $this->get_raw_value( $setting, $attrs, $default )
			: $default;
	}

	/**
	 * Returns setting sticky value if sticky is enabled for a compose option;
	 * If it does not exist, return $default specified value
	 *
	 * @since 4.6.0
	 *
	 * @param string $setting Field name.
	 * @param string $option  Option.
	 * @param array  $attrs   Module attributes.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get_compose_value( $setting, $option, $attrs, $default = null ) {
		return $this->is_enabled( $option, $attrs )
			? $this->get_raw_value( $setting, $attrs, $default )
			: $default;
	}

	/**
	 * Returns setting sticky raw value;
	 * If it does not exist, return $default specified value
	 *
	 * @since 4.6.0
	 *
	 * @param string $setting Field name.
	 * @param array  $attrs   Module attributes.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get_raw_value( $setting, $attrs, $default = null ) {
		return et_()->array_get( $attrs, $this->get_sticky_field( $setting ), $default );
	}

	/**
	 * Adds sticky state selector prefix before given selectors
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $selector      CSS Selector.
	 * @param bool         $is_sticky     Whether current module is sticky or not, based on
	 *                                    `sticky_position` prop value.
	 * @param bool         $return_string Return modified selector as string or not.
	 *
	 * @return string
	 */
	public function add_sticky_to_selectors( $selector, $is_sticky = true, $return_string = true ) {
		$selectors         = is_array( $selector ) ? $selector : explode( ',', $selector );
		$space             = $is_sticky ? '' : ' ';
		$prefix            = ".et_pb_sticky{$space}";
		$prefixed_selector = array();

		foreach ( $selectors as $selector ) {
			$prefixed_selector[] = $prefix . trim( $selector );
		}

		return $return_string ? implode( ', ', $prefixed_selector ) : $prefixed_selector;
	}

	/**
	 * Add sticky state selector prefix to given selector
	 *
	 * @since 4.6.0
	 *
	 * @param string $selector  CSS Selector.
	 * @param bool   $is_sticky whether current module is sticky or not, based on `sticky_position`
	 *                          prop value.
	 *
	 * @return string
	 */
	public function add_sticky_to_order_class( $selector, $is_sticky = true ) {
		$selectors = explode( ',', $selector );
		$selectors = array_map(
			function( $selector ) use ( $is_sticky ) {
				$selector = trim( $selector );

				// If current selector is sticky module, sticky selector is directly attached; if it isn't
				// it is safe to assume that the sticky selector is one of its parent DOM, hence the space.
				if ( ! $is_sticky ) {
					$selector = ' ' . $selector;
				}

				return '.et_pb_sticky' . $selector;
			},
			$selectors
		);

		return implode( ', ', $selectors );
	}

	/**
	 * Check if given attrs has incompatible attribute value which makes sticky mechanism can't
	 * be used on current module
	 *
	 * @since 4.6.0
	 *
	 * @param array $attrs Module attributes.
	 *
	 * @return bool
	 */
	public function has_incompatible_attrs( $attrs = array() ) {
		$incompatible = false;
		$fields       = $this->get_incompatible_fields();

		foreach ( $fields as $name => $options ) {
			// Get attribute value of current incompatible field from attributes.
			$attr = ! empty( $attrs[ $name ] ) ? $attrs[ $name ] : false;

			// If the value exist on current incompatible field's options, stop loop and return true.
			if ( in_array( $attr, $options, true ) ) {
				$incompatible = true;
				break;
			}
		}

		return $incompatible;
	}

	/**
	 * List of fields and its value which prevent sticky mechanism to work due to how it behaves
	 *
	 * @since 4.6.0
	 *
	 * @return array
	 */
	public function get_incompatible_fields() {
		return array(
			// Position Options.
			'positioning'                     => array( 'absolute', 'fixed' ),

			// Motion Effects.
			'scroll_vertical_motion_enable'   => array( 'on' ),
			'scroll_horizontal_motion_enable' => array( 'on' ),
			'scroll_fade_enable'              => array( 'on' ),
			'scroll_scaling_enable'           => array( 'on' ),
			'scroll_rotating_enable'          => array( 'on' ),
			'scroll_blur_enable'              => array( 'on' ),
		);
	}
}

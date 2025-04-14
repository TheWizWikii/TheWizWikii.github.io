<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Hover Options helper methods
 *
 * Class ET_Builder_Module_Hover_Options
 */
class ET_Builder_Module_Helper_Hover_Options {

	private static $instance;

	public static function get() {
		if ( empty( self::$instance ) ) {
			self::$instance = new ET_Builder_Module_Helper_Hover_Options();
		}

		return self::$instance;
	}

	private function util_get( $key, $list, $default = null ) {
		return ET_Core_Data_Utils::instance()->array_get( $list, $key, $default );
	}

	/**
	 * Returns `__hover`
	 *
	 * @return string
	 */
	public function get_suffix() {
		return '__hover';
	}

	/**
	 * Return `__hover_enabled`
	 *
	 * @return string
	 */
	public function get_enabled_suffix() {
		return '__hover_enabled';
	}

	/**
	 * Returns the field original name by removing the `__hover` or `__hover_enabled` suffix if it exists.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function get_field_base_name( $name ) {
		// Do not use rtim as it removes by character not by string
		// So cases like `key__hoveree` will be reduced to `key`
		$regex   = "/(.*)({$this->get_suffix()}|{$this->get_enabled_suffix()})$/";
		$replace = '${1}';

		return preg_replace( $regex, $replace, $name );
	}

	/**
	 * Check if the setting has enabled hover options
	 *
	 * @param string $setting
	 * @param array  $attrs
	 *
	 * @return bool
	 */
	public function is_enabled( $setting, $attrs ) {
		$name = in_array( $setting, [ 'background_color', 'background_image' ], true ) ? 'background' : $setting;

		$field = $this->get_hover_enabled_field( $name );

		$value = ! empty( $attrs[ $field ] ) ? $attrs[ $field ] : '';

		$result = ! empty( $value ) && strpos( $value, 'on' ) === 0;

		return $result;
	}

	/**
	 * Check if hover settings are enabled on one of the options list.
	 *
	 * @since 4.5.1
	 *
	 * @param  array $attrs All module attributes.
	 * @param  array $list  Options list.
	 * @return boolean      Hover settings status.
	 */
	public function is_any_hover_enabled( $attrs, $list ) {
		// Ensure list is not empty and valid array.
		if ( empty( $list ) || ! is_array( $list ) ) {
			return false;
		}

		// Check the hover status one by one.
		$is_any_hover_enabled = false;
		foreach ( $list as $name ) {
			if ( $this->is_enabled( $name, $attrs ) ) {
				$is_any_hover_enabled = true;
				break;
			}
		}

		return $is_any_hover_enabled;
	}

	/**
	 * Returns the hover setting field name
	 * E.g.: get_hover_enabled_field('test') => 'test__hover'
	 *
	 * @param string $setting
	 *
	 * @return string
	 */
	public function get_hover_field( $setting ) {
		return "{$this->get_field_base_name($setting)}{$this->get_suffix()}";
	}

	/**
	 * Returns the hover enabled setting field name
	 * E.g.: get_hover_enabled_field('test') => 'test__hover_enabled'
	 *
	 * @param string $setting
	 *
	 * @return string
	 */
	public function get_hover_enabled_field( $setting ) {
		return "{$this->get_field_base_name($setting)}{$this->get_enabled_suffix()}";
	}

	/**
	 * Returns setting hover value if hover is enabled;
	 * If it does not exist, return $default specified value
	 *
	 * @param string $setting
	 * @param array  $attrs
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_value( $setting, $attrs, $default = null ) {
		return $this->is_enabled( $setting, $attrs )
			? $this->get_raw_value( $setting, $attrs, $default )
			: $default;
	}

	/**
	 * Returns setting hover value if hover is enabled for a compose option;
	 * If it does not exist, return $default specified value
	 *
	 * @param string $setting
	 * @param string $option
	 * @param array  $attrs
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_compose_value( $setting, $option, $attrs, $default = null ) {
		return $this->is_enabled( $option, $attrs )
			? $this->get_raw_value( $setting, $attrs, $default )
			: $default;
	}

	/**
	 * Returns setting hover value;
	 * If it does not exist, return $default specified value
	 *
	 * @param string $setting
	 * @param array  $attrs
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_raw_value( $setting, $attrs, $default = null ) {
		return $this->util_get( $this->get_hover_field( $setting ), $attrs, $default );
	}

	/**
	 * Adds `:hover` in selector at the end of the selector
	 * E.g: add_hover_to_selectors('%%order_class%%% .image') >>> '%%order_class%%% .image:hover'
	 *
	 * @since 4.6.0 moved the order of `-` in capturing group 4's character set so it captures
	 *           `::-` prefixed pseudo selector like `::-moz-placeholder` correctly
	 *
	 * @param string $selector
	 *
	 * @return string
	 */
	public function add_hover_to_selectors( $selector ) {
		$selectors = explode( ',', $selector );
		$selectors = array_map( 'trim', $selectors );
		// Add hover to the end of the selector, but prevent specific situations like this:
		// .my-class:after => .my-class:after:hover, should be .my-class:hover:after
		$selectors = preg_replace( '/(.+\s)*([^\::?]+)((::?[-|a-z|\(|\)|\[|\]]+)+)?$/i', '$1$2:hover$3', $selectors );

		return implode( ', ', $selectors );
	}

	/**
	 * Adds `:hover` in selector after `%%order_class%%`
	 * E.g: add_hover_to_order_class('%%order_class%%% .image') >>> '%%order_class%%%:hover .image'
	 *
	 * @param string $selector
	 *
	 * @return string
	 */
	public function add_hover_to_order_class( $selector ) {
		$selectors = explode( ',', $selector );
		$selectors = array_map( 'trim', $selectors );
		// Add hover to the end of the selector, but prevent specific situations like this:
		// .my-class:after => .my-class:after:hover, should be .my-class:hover:after
		$selectors = preg_replace( '/(.*%%order_class%%[^\s|^:]*)(.*)/i', '$1:hover$2', $selectors );

		return implode( ', ', $selectors );
	}
}


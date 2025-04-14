<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Overflow helper methods
 *
 * Class ET_Builder_Module_Helper_Overflow
 */
class ET_Builder_Module_Helper_Overflow {

	const OVERFLOW_DEFAULT = '';
	const OVERFLOW_VISIBLE = 'visible';
	const OVERFLOW_HIDDEN  = 'hidden';
	const OVERFLOW_SCROLL  = 'scroll';
	const OVERFLOW_AUTO    = 'auto';

	private static $instance;

	public static function get() {
		if ( empty( self::$instance ) ) {
			return self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Returns overflow settings X axis field
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_field_x( $prefix = '' ) {
		return $prefix . 'overflow-x';
	}

	/**
	 * Returns overflow settings Y axis field
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_field_y( $prefix = '' ) {
		return $prefix . 'overflow-y';
	}

	/**
	 * Return overflow X axis value
	 *
	 * @param array  $props
	 * @param mixed  $default
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_value_x( $props, $default = null, $prefix = '' ) {
		return et_()->array_get( $props, $this->get_field_x( $prefix ), $default );
	}

	/**
	 * Return overflow Y axis value
	 *
	 * @param array  $props
	 * @param mixed  $default
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_value_y( $props, $default = null, $prefix = '' ) {
		return et_()->array_get( $props, $this->get_field_y( $prefix ), $default );
	}

	/**
	 * Returns overflow valid values
	 *
	 * @return array
	 */
	public function get_overflow_values() {
		return array(
			self::OVERFLOW_DEFAULT,
			self::OVERFLOW_VISIBLE,
			self::OVERFLOW_HIDDEN,
			self::OVERFLOW_AUTO,
			self::OVERFLOW_SCROLL,
		);
	}
}

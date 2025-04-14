<?php

/**
 * Helper class that provides necessary functions for managing Sizing option
 *
 * Class ET_Builder_Module_Helper_Sizing
 */
abstract class ET_Builder_Module_Helper_Sizing {
	/**
	 * @var string The prefix string that may be added to field name
	 */
	private $prefix;

	/**
	 * Return raw field name to create the field
	 *
	 * @return string
	 */
	abstract public function get_raw_field();

	public function __construct( $prefix = '' ) {
		$this->prefix = $prefix;
	}

	/**
	 * Returns sizing options fields prefix
	 *
	 * @return string
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Returns field name of the sizing option
	 *
	 * @return string
	 */
	public function get_field() {
		return $this->get_prefix() . $this->get_raw_field();
	}

	/**
	 * Check if the sizing feature option is enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return true;
	}

	/**
	 * Returns sizing value
	 *
	 * @param array  $props
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_value( array $props, $default = '' ) {
		return (string) et_()->array_get( $props, $this->get_field(), $default );
	}
}

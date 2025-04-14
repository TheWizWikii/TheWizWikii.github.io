<?php

class ET_Builder_Module_Helper_Multi_Value {
	private static $instance;

	public static function instance() {
		return self::$instance ? self::$instance : ( self::$instance = new self() );
	}

	public function get_delimiter() {
		return '|';
	}

	/**
	 * Parses an array and transforms it in an valid multi value array
	 *
	 * @param array $value
	 * @param int   $elements
	 *
	 * @return array
	 */
	public function parse( array $value, $elements = null ) {
		$length = (int) $elements;

		if ( ! $elements || count( $value ) === (int) $length ) {
			return $value;
		}

		$new = array();

		for ( $i = 0; $i < (int) $length; $i ++ ) {
			$new[ $i ] = isset( $value[ $i ] ) && $value[ $i ] !== null ? $value[ $i ] : '';
		}

		return array_map( 'strval', $new );
	}

	/**
	 * Splits the multi value string in to an array of primitive values
	 * User can provide also the required number of elements that value must have
	 * In case the array original length will be larger then the required elements number
	 * the array will be cut from head to tail
	 * In cas the array length will be shorter, the array tail will be filled with empty strings `''`,
	 * till array length will match the requested elements number
	 *
	 * @param string $value
	 * @param int    $elements
	 *
	 * @return array
	 */
	public function split( $value, $elements = null ) {
		return $this->parse( explode( $this->get_delimiter(), $value ), $elements );
	}

	/**
	 * Takes an array and converts it to a valid multi value
	 * Provide the `elements` parameter to get the result string of the necessary length
	 *
	 * @param array $value
	 * @param int   $elements
	 *
	 * @return string
	 */
	public function to_value( array $value, $elements = null ) {
		return implode( $this->get_delimiter(), $this->parse( $value, $elements ) );
	}

	/**
	 * Check if the multi value is not empty.
	 * A multi value is empty when all sub values are empty strings
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function has_value( $value ) {
		return trim( implode( '', $this->split( $value ) ) ) !== '';
	}

	/**
	 * Merges two multi values in to one.
	 * If value1 nth element is empty, value2 nth element will be used
	 *
	 * @param $value_1
	 * @param $value_2
	 * @param int     $elements
	 *
	 * @return string
	 */
	public function merge( $value_1, $value_2, $elements = null ) {
		$v1  = $this->split( $value_1, $elements );
		$v2  = $this->split( $value_2, $elements );
		$max = max( count( $v1 ), count( $v2 ) );
		$new = array();

		for ( $i = 0; $i < $max; $i ++ ) {
			$new[ $i ] = ! isset( $v1[ $i ] ) ? $v2[ $i ] : et_builder_get_or( $v1[ $i ], $v2[ $i ] );
		}

		return $this->to_value( $new, $elements );
	}

	/**
	 * Sets a value at specific position in provided multiValue.
	 *
	 * @param int    $key
	 * @param string $value
	 * @param string $motion_value
	 * @param int    $elements
	 *
	 * @return string
	 */
	public function set( $key, $value, $motion_value, $elements = null ) {
		$arr = $this->split( $motion_value, $elements );

		if ( ! isset( $arr[ $key ] ) ) {
			return $motion_value;
		}

		$arr[ $key ] = $value;

		return implode( $this->get_delimiter(), $arr );
	}
}

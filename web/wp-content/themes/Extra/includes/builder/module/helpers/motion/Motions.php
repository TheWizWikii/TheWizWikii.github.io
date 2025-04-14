<?php
/**
 * Motion Helper class.
 *
 * @package Divi
 * @subpackage Builder
 */

/**
 * Motion Helpers.
 */
class ET_Builder_Module_Helper_Motion_Motions {
	private $START_LIMIT  = 0;
	private $START_MIDDLE = 1;
	private $END_MIDDLE   = 2;
	private $END_LIMIT    = 3;
	private $START_VALUE  = 4;
	private $MIDDLE_VALUE = 5;
	private $END_VALUE    = 6;
	private $LENGTH       = 7;

	private static $instance;

	public static function instance() {
		return self::$instance ? self::$instance : ( self::$instance = new self() );
	}

	/**
	 * Returns the Motion Effect value. Which is the merge of Saved and Default values.
	 *
	 * @param string $value, $default_value
	 *
	 * @return string
	 */
	public function getValue( $saved_value, $default_value ) {
		if ( $saved_value === $default_value ) {
			return $saved_value;
		}

		$saved_array   = explode( '|', $saved_value );
		$default_array = explode( '|', $default_value );

		if ( sizeof( $saved_array ) !== sizeof( $default_array ) ) {
			return $saved_value;
		}

		return implode( '|', array_map( array( 'ET_Builder_Module_Helper_Motion_Motions', 'getNonEmpty' ), $saved_array, $default_array ) );
	}

	/**
	 * Returns the non-empty value or default.
	 *
	 * @param string $value, $default
	 *
	 * @return string
	 */
	public static function getNonEmpty( $value, $default ) {
		if ( '' === $value ) {
			return $default;
		}

		return $value;
	}

	/**
	 * Returns start limit.
	 *
	 * @param string $value
	 *
	 * @return int
	 */
	public function getStartLimit( $value ) {
		return $this->get( $this->START_LIMIT, $value );
	}

	/**
	 * Set start limit.
	 *
	 * If limit:
	 * - is not a numeric value, return original motionValue
	 * - is lower then 0, set limit to 0
	 * - is higher then start middle, set limit equal to start middle
	 *
	 * @param int    $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setStartLimit( $value, $multi_value ) {
		$value  = $this->to_int( $value, $this->getStartLimit( $multi_value ) );
		$ranged = $this->range( 0, $this->getStartMiddle( $multi_value ), $value );

		return $this->set( $this->START_LIMIT, $ranged, $multi_value );
	}

	/**
	 * Returns start limit.
	 *
	 * @param string $value
	 *
	 * @return int
	 */
	public function getEndLimit( $value ) {
		return $this->get( $this->END_LIMIT, $value );
	}

	/**
	 * Set end limit.
	 *
	 * If limit:
	 * - is not a numeric value, return original motionValue
	 * - is lower then end middle, set limit equal to end middle
	 * - is higher then 100, set limit equal to 100
	 *
	 * @param int    $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setEndLimit( $value, $multi_value ) {
		$value  = $this->to_int( $value, $this->getEndLimit( $multi_value ) );
		$ranged = $this->range( $this->getEndMiddle( $multi_value ), 100, $value );

		return $this->set( $this->END_LIMIT, $ranged, $multi_value );
	}

	/**
	 * Get start middle.
	 *
	 * @param $value
	 *
	 * @return int
	 */
	public function getStartMiddle( $value ) {
		return $this->get( $this->START_MIDDLE, $value );
	}

	/**
	 * Set start middle limit.
	 *
	 * If limit:
	 * - is not a numeric value, return original motionValue
	 * - is lower then start limit, set limit equal to start limit
	 * - is higher then end middle, set limit equal to end middle
	 *
	 * @param int    $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setStartMiddle( $value, $multi_value ) {
		$value  = $this->to_int( $value, $this->getStartMiddle( $multi_value ) );
		$ranged = $this->range( $this->getStartLimit( $value ), $this->getEndMiddle( $value ), $value );

		return $this->set( $this->START_MIDDLE, $ranged, $multi_value );
	}

	/**
	 * Get end middle.
	 *
	 * @param $value
	 *
	 * @return int
	 */
	public function getEndMiddle( $value ) {
		return $this->get( $this->END_MIDDLE, $value );
	}

	/**
	 * Set end middle limit.
	 *
	 * If limit:
	 * - is not a numeric value, return original motionValue
	 * - is lower then start middle limit, set limit equal to start middle limit
	 * - is higher then end limit, set limit equal to end limit
	 *
	 * @param int    $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setEndMiddle( $value, $multi_value ) {
		$value  = $this->to_int( $value, $this->getEndMiddle( $multi_value ) );
		$ranged = $this->range( $this->getStartMiddle( $value ), $this->getEndLimit( $value ), $value );

		return $this->set( $this->END_MIDDLE, $ranged, $multi_value );
	}

	/**
	 * Returns option value for start.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function getStartValue( $value ) {
		return $this->get( $this->START_VALUE, $value );
	}

	/**
	 * Sets option value for start.
	 *
	 * @param string $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setStartValue( $value, $multi_value ) {
		return $this->set( $this->START_VALUE, $value, $multi_value );
	}

	/**
	 * Returns option value for middle.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function getMiddleValue( $value ) {
		return $this->get( $this->MIDDLE_VALUE, $value );
	}

	/**
	 * Sets option value for middle.
	 *
	 * @param string $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setMiddleValue( $value, $multi_value ) {
		return $this->set( $this->MIDDLE_VALUE, $value, $multi_value );
	}

	/**
	 * Returns option value for end.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function getEndValue( $value ) {
		return $this->get( $this->END_VALUE, $value );
	}

	/**
	 * Sets option value for end.
	 *
	 * @param string $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setEndValue( $value, $multi_value ) {
		return $this->set( $this->END_VALUE, $value, $multi_value );
	}

	/**
	 * Same as Multi.merge, but applies the elements parameter
	 *
	 * @param string $value_1
	 * @param string $value_2
	 *
	 * @return string
	 */
	public function merge( $value_1, $value_2 ) {
		return $this->to_value( $this->split( $this->multi()->merge( $value_1, $value_2, $this->LENGTH ) ) );
	}

	/**
	 * Parses array value and converts it to a valid motion array
	 * - array length should be 7
	 * - first 4 values should be numeric values
	 * - first 4 values should respect 0-100 boundaries
	 * - first 4 values should be ordered in ascending order
	 * - last 3 values should be string values
	 *
	 * @param array $value
	 *
	 * @return array
	 */
	protected function parse( array $value ) {
		$arr   = $this->multi()->parse( $value, $this->LENGTH );
		$range = array();

		$range[ $this->START_LIMIT ]  = $this->to_int( array_shift( $arr ), 0 );
		$range[ $this->START_MIDDLE ] = $this->to_int( array_shift( $arr ), 50 );
		$range[ $this->END_MIDDLE ]   = $this->to_int( array_shift( $arr ), 50 );
		$range[ $this->END_LIMIT ]    = $this->to_int( array_shift( $arr ), 100 );

		sort( $range, SORT_NUMERIC );

		$range[ $this->START_LIMIT ]  = max( $range[ $this->START_LIMIT ], 0 );
		$range[ $this->END_LIMIT ]    = min( $range[ $this->END_LIMIT ], 100 );
		$range[ $this->START_MIDDLE ] = max( $range[ $this->START_MIDDLE ], $range[ $this->START_LIMIT ] );
		$range[ $this->END_MIDDLE ]   = min( $range[ $this->END_LIMIT ], $range[ $this->END_MIDDLE ] );

		return array_merge( $range, $arr );
	}

	/**
	 * Converts a value to a valid motion array value.
	 *
	 * @param string $value
	 *
	 * @return array
	 */
	protected function split( $value ) {
		return $this->parse( $this->multi()->split( $value, $this->LENGTH ) );
	}

	/**
	 * Converts a value to a valid motion string value.
	 *
	 * @param array $value
	 *
	 * @return string
	 */
	protected function to_value( array $value ) {
		return $this->multi()->to_value( $this->parse( $value ), $this->LENGTH );
	}

	/**
	 * @return ET_Builder_Module_Helper_Multi_Value
	 */
	protected function multi() {
		return ET_Builder_Module_Helper_Multi_Value::instance();
	}

	/**
	 * Returns specific key value
	 *
	 * @param int    $key
	 * @param string $value
	 *
	 * @return mixed
	 */
	protected function get( $key, $value ) {
		$arr = $this->parse( $this->multi()->split( $value, $this->LENGTH ) );

		return $arr[ $key ];
	}

	/**
	 * @param int    $key
	 * @param $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	protected function set( $key, $value, $multi_value ) {
		return $this->multi()->set( $key, $value, $multi_value, $this->LENGTH );
	}

	private function to_int( $value, $default ) {
		return is_numeric( $value ) ? (int) $value : $default;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @param int $value
	 *
	 * @return int
	 */
	private function range( $min, $max, $value ) {
		return min( $max, max( $min, $value ) );
	}
}

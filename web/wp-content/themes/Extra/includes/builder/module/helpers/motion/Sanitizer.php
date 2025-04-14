<?php

abstract class ET_Builder_Module_Helper_Motion_Sanitizer extends ET_Builder_Module_Helper_Motion {
	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getStartValue( $value ) {
		return $this->sanitize( parent::getStartValue( $value ) );
	}

	/**
	 * @param string $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setStartValue( $value, $multi_value ) {
		return parent::setStartValue( $this->sanitize( $value ), $multi_value );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getMiddleValue( $value ) {
		return $this->sanitize( parent::getMiddleValue( $value ) );
	}

	/**
	 * @param string $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setMiddleValue( $value, $multi_value ) {
		return parent::setMiddleValue( $this->sanitize( $value ), $multi_value );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getEndValue( $value ) {
		return $this->sanitize( parent::getEndValue( $value ) );
	}

	/**
	 * @param string $value
	 * @param string $multi_value
	 *
	 * @return string
	 */
	public function setEndValue( $value, $multi_value ) {
		return parent::setEndValue( $this->sanitize( $value ), $multi_value );
	}

	abstract protected function sanitize( $value );
}

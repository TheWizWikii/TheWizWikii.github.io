<?php

class ET_Builder_Module_Helper_Motion_Blur extends ET_Builder_Module_Helper_Motion_Sanitizer {

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function sanitize( $value ) {
		$unit = et_pb_get_value_unit( $value );
		$unit = in_array( $unit, $this->get_units() ) ? $unit : $this->get_default_unit();

		return (float) $value . $unit;
	}

	protected function get_units() {
		return array( 'cm', 'em', 'mm', 'in', 'pc', 'pt', 'px', 'rem' );
	}

	protected function get_default_unit() {
		return 'px';
	}
}

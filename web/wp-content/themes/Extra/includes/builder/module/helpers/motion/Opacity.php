<?php

class ET_Builder_Module_Helper_Motion_Opacity extends ET_Builder_Module_Helper_Motion_Sanitizer {

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function sanitize( $value ) {
		return min( 100, max( 0, (int) $value ) ) . '%';
	}
}

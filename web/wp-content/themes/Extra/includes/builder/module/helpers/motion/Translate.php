<?php

class ET_Builder_Module_Helper_Motion_Translate extends ET_Builder_Module_Helper_Motion_Sanitizer {

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function sanitize( $value ) {
		return (float) $value;
	}
}

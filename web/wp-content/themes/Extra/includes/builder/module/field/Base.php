<?php

abstract class ET_Builder_Module_Field_Base {
	/**
	 * @param array $args
	 *
	 * @return array
	 */
	abstract public function get_fields( array $args = array() );
}

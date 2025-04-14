<?php

/**
 * Utility class for replacing scripts in a string.
 *
 * @since 3.18.5
 *
 * @package ET\Core\Data
 */
class ET_Core_Data_ScriptReplacer {

	private $_map = array();

	public function replace( $matches ) {
		$script            = $matches[0];
		$id                = md5( $script );
		$this->_map[ $id ] = $script;
		return $id;
	}

	public function map() {
		return $this->_map;
	}
}

<?php

namespace Yoast\WHIPv2\Interfaces;

/**
 * Interface DismissStorage.
 */
interface DismissStorage {

	/**
	 * Saves the value.
	 *
	 * @param int $dismissedValue The value to save.
	 *
	 * @return bool True when successful.
	 */
	public function set( $dismissedValue );

	/**
	 * Returns the value.
	 *
	 * @return int The stored value.
	 */
	public function get();
}

<?php

namespace Yoast\WHIPv2\Interfaces;

/**
 * Interface Listener.
 */
interface Listener {

	/**
	 * Method that should implement the listen functionality.
	 *
	 * @return void
	 */
	public function listen();
}

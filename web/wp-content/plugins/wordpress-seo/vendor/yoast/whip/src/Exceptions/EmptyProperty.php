<?php

namespace Yoast\WHIPv2\Exceptions;

use Exception;

/**
 * Class EmptyProperty.
 */
class EmptyProperty extends Exception {

	/**
	 * EmptyProperty constructor.
	 *
	 * @param string $property Property name.
	 */
	public function __construct( $property ) {
		parent::__construct( \sprintf( '%s cannot be empty.', (string) $property ) );
	}
}

<?php

namespace Yoast\WHIPv2\Exceptions;

use Exception;

/**
 * Exception for an invalid version comparison string.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded -- Name should be descriptive and was historically (before namespacing) already set to this.
 */
class InvalidVersionComparisonString extends Exception {

	/**
	 * InvalidVersionComparisonString constructor.
	 *
	 * @param string $value The passed version comparison string.
	 */
	public function __construct( $value ) {
		parent::__construct(
			\sprintf(
				'Invalid version comparison string. Example of a valid version comparison string: >=5.3. Passed version comparison string: %s',
				$value
			)
		);
	}
}

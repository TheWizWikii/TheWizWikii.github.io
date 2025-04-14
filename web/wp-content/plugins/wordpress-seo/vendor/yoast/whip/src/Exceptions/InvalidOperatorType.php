<?php

namespace Yoast\WHIPv2\Exceptions;

use Exception;

/**
 * Class InvalidOperatorType.
 */
class InvalidOperatorType extends Exception {

	/**
	 * InvalidOperatorType constructor.
	 *
	 * @param string   $value          Invalid operator.
	 * @param string[] $validOperators Valid operators.
	 */
	public function __construct( $value, $validOperators = array( '=', '==', '===', '<', '>', '<=', '>=' ) ) {
		parent::__construct(
			\sprintf(
				'Invalid operator of %s used. Please use one of the following operators: %s',
				$value,
				\implode( ', ', $validOperators )
			)
		);
	}
}

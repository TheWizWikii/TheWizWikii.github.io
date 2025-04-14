<?php

namespace Yoast\WHIPv2\Interfaces;

/**
 * Interface Requirement.
 */
interface Requirement {

	/**
	 * Retrieves the component name defined for the requirement.
	 *
	 * @return string The component name.
	 */
	public function component();

	/**
	 * Gets the components version defined for the requirement.
	 *
	 * @return string
	 */
	public function version();

	/**
	 * Gets the operator to use when comparing version numbers.
	 *
	 * @return string The comparison operator.
	 */
	public function operator();
}

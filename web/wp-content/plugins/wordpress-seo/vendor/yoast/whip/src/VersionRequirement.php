<?php

namespace Yoast\WHIPv2;

use Yoast\WHIPv2\Exceptions\EmptyProperty;
use Yoast\WHIPv2\Exceptions\InvalidOperatorType;
use Yoast\WHIPv2\Exceptions\InvalidType;
use Yoast\WHIPv2\Exceptions\InvalidVersionComparisonString;
use Yoast\WHIPv2\Interfaces\Requirement;

/**
 * A value object containing a version requirement for a component version.
 */
class VersionRequirement implements Requirement {

	/**
	 * The component name.
	 *
	 * @var string
	 */
	private $component;

	/**
	 * The component version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The operator to use when comparing version.
	 *
	 * @var string
	 */
	private $operator;

	/**
	 * Requirement constructor.
	 *
	 * @param string $component The component name.
	 * @param string $version   The component version.
	 * @param string $operator  The operator to use when comparing version.
	 */
	public function __construct( $component, $version, $operator = '=' ) {
		$this->validateParameters( $component, $version, $operator );

		$this->component = $component;
		$this->version   = $version;
		$this->operator  = $operator;
	}

	/**
	 * Retrieves the component name defined for the requirement.
	 *
	 * @return string The component name.
	 */
	public function component() {
		return $this->component;
	}

	/**
	 * Gets the components version defined for the requirement.
	 *
	 * @return string
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Gets the operator to use when comparing version numbers.
	 *
	 * @return string The comparison operator.
	 */
	public function operator() {
		return $this->operator;
	}

	/**
	 * Creates a new version requirement from a comparison string.
	 *
	 * @param string $component        The component for this version requirement.
	 * @param string $comparisonString The comparison string for this version requirement.
	 *
	 * @return VersionRequirement The parsed version requirement.
	 *
	 * @throws InvalidVersionComparisonString When an invalid version comparison string is passed.
	 */
	public static function fromCompareString( $component, $comparisonString ) {

		$matcher = '`
			(
				>=?     # Matches >= and >.
				|
				<=?     # Matches <= and <.
			)
			([^>=<\s]+) # Matches anything except >, <, =, and whitespace.
		`x';

		if ( ! \preg_match( $matcher, $comparisonString, $match ) ) {
			throw new InvalidVersionComparisonString( $comparisonString );
		}

		$version  = $match[2];
		$operator = $match[1];

		return new VersionRequirement( $component, $version, $operator );
	}

	/**
	 * Validates the parameters passed to the requirement.
	 *
	 * @param string $component The component name.
	 * @param string $version   The component version.
	 * @param string $operator  The operator to use when comparing version.
	 *
	 * @return void
	 *
	 * @throws EmptyProperty       When any of the parameters is empty.
	 * @throws InvalidOperatorType When the $operator parameter is invalid.
	 * @throws InvalidType         When any of the parameters is not of the expected type.
	 */
	private function validateParameters( $component, $version, $operator ) {
		if ( empty( $component ) ) {
			throw new EmptyProperty( 'Component' );
		}

		if ( ! \is_string( $component ) ) {
			throw new InvalidType( 'Component', $component, 'string' );
		}

		if ( empty( $version ) ) {
			throw new EmptyProperty( 'Version' );
		}

		if ( ! \is_string( $version ) ) {
			throw new InvalidType( 'Version', $version, 'string' );
		}

		if ( empty( $operator ) ) {
			throw new EmptyProperty( 'Operator' );
		}

		if ( ! \is_string( $operator ) ) {
			throw new InvalidType( 'Operator', $operator, 'string' );
		}

		$validOperators = array( '=', '==', '===', '<', '>', '<=', '>=' );
		if ( ! \in_array( $operator, $validOperators, true ) ) {
			throw new InvalidOperatorType( $operator, $validOperators );
		}
	}
}

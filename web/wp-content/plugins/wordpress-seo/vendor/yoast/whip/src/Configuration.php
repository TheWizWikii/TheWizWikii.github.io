<?php

namespace Yoast\WHIPv2;

use Yoast\WHIPv2\Exceptions\InvalidType;
use Yoast\WHIPv2\Interfaces\Requirement;

/**
 * Class Configuration.
 */
class Configuration {

	/**
	 * The configuration to use.
	 *
	 * @var array<string>
	 */
	private $configuration;

	/**
	 * Configuration constructor.
	 *
	 * @param array<string, string> $configuration The configuration to use.
	 *
	 * @throws InvalidType When the $configuration parameter is not of the expected type.
	 */
	public function __construct( $configuration = array() ) {
		if ( ! \is_array( $configuration ) ) {
			throw new InvalidType( 'Configuration', $configuration, 'array' );
		}

		$this->configuration = $configuration;
	}

	/**
	 * Retrieves the configured version of a particular requirement.
	 *
	 * @param Requirement $requirement The requirement to check.
	 *
	 * @return string|int The version of the passed requirement that was detected as a string.
	 *                    If the requirement does not exist, this returns int -1.
	 */
	public function configuredVersion( Requirement $requirement ) {
		if ( ! $this->hasRequirementConfigured( $requirement ) ) {
			return -1;
		}

		return $this->configuration[ $requirement->component() ];
	}

	/**
	 * Determines whether the passed requirement is present in the configuration.
	 *
	 * @param Requirement $requirement The requirement to check.
	 *
	 * @return bool Whether or not the requirement is present in the configuration.
	 */
	public function hasRequirementConfigured( Requirement $requirement ) {
		return \array_key_exists( $requirement->component(), $this->configuration );
	}
}

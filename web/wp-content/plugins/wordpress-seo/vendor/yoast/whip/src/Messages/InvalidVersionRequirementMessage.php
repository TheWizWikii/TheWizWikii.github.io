<?php

namespace Yoast\WHIPv2\Messages;

use Yoast\WHIPv2\Interfaces\Message;
use Yoast\WHIPv2\VersionRequirement;

/**
 * Class Whip_InvalidVersionMessage.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded -- Name should be descriptive and was historically (before namespacing) already set to this.
 */
class InvalidVersionRequirementMessage implements Message {

	/**
	 * Object containing the version requirement for a component.
	 *
	 * @var VersionRequirement
	 */
	private $requirement;

	/**
	 * Detected version requirement or -1 if not found.
	 *
	 * @var string|int
	 */
	private $detected;

	/**
	 * InvalidVersionRequirementMessage constructor.
	 *
	 * @param VersionRequirement $requirement Object containing the version requirement for a component.
	 * @param string|int         $detected    Detected version requirement or -1 if not found.
	 */
	public function __construct( VersionRequirement $requirement, $detected ) {
		$this->requirement = $requirement;
		$this->detected    = $detected;
	}

	/**
	 * Retrieves the message body.
	 *
	 * @return string Message.
	 */
	public function body() {
		return \sprintf(
			'Invalid version detected for %s. Found %s but expected %s.',
			$this->requirement->component(),
			$this->detected,
			$this->requirement->version()
		);
	}
}

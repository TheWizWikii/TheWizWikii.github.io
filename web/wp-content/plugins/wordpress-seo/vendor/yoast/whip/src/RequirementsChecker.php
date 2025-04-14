<?php

namespace Yoast\WHIPv2;

use Yoast\WHIPv2\Exceptions\InvalidType;
use Yoast\WHIPv2\Interfaces\Message;
use Yoast\WHIPv2\Interfaces\Requirement;
use Yoast\WHIPv2\Messages\InvalidVersionRequirementMessage;
use Yoast\WHIPv2\Messages\UpgradePhpMessage;

/**
 * Main controller class to require a certain version of software.
 */
class RequirementsChecker {

	/**
	 * Requirements the environment should comply with.
	 *
	 * @var array<Requirement>
	 */
	private $requirements;

	/**
	 * The configuration to check.
	 *
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * Message Manager.
	 *
	 * @var MessagesManager
	 */
	private $messageManager;

	/**
	 * The text domain to use for translations.
	 *
	 * @var string
	 */
	private $textdomain;

	/**
	 * RequirementsChecker constructor.
	 *
	 * @param array<string, string> $configuration The configuration to check.
	 * @param string                $textdomain    The text domain to use for translations.
	 *
	 * @throws InvalidType When the $configuration parameter is not of the expected type.
	 */
	public function __construct( $configuration = array(), $textdomain = 'default' ) {
		$this->requirements   = array();
		$this->configuration  = new Configuration( $configuration );
		$this->messageManager = new MessagesManager();
		$this->textdomain     = $textdomain;
	}

	/**
	 * Adds a requirement to the list of requirements if it doesn't already exist.
	 *
	 * @param Requirement $requirement The requirement to add.
	 *
	 * @return void
	 */
	public function addRequirement( Requirement $requirement ) {
		// Only allow unique entries to ensure we're not checking specific combinations multiple times.
		if ( $this->requirementExistsForComponent( $requirement->component() ) ) {
			return;
		}

		$this->requirements[] = $requirement;
	}

	/**
	 * Determines whether or not there are requirements available.
	 *
	 * @return bool Whether or not there are requirements.
	 */
	public function hasRequirements() {
		return $this->totalRequirements() > 0;
	}

	/**
	 * Gets the total amount of requirements.
	 *
	 * @return int The total amount of requirements.
	 */
	public function totalRequirements() {
		return \count( $this->requirements );
	}

	/**
	 * Determines whether or not a requirement exists for a particular component.
	 *
	 * @param string $component The component to check for.
	 *
	 * @return bool Whether or not the component has a requirement defined.
	 */
	public function requirementExistsForComponent( $component ) {
		foreach ( $this->requirements as $requirement ) {
			if ( $requirement->component() === $component ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines whether a requirement has been fulfilled.
	 *
	 * @param Requirement $requirement The requirement to check.
	 *
	 * @return bool Whether or not the requirement is fulfilled.
	 */
	private function requirementIsFulfilled( Requirement $requirement ) {
		$availableVersion = $this->configuration->configuredVersion( $requirement );
		$requiredVersion  = $requirement->version();

		if ( \in_array( $requirement->operator(), array( '=', '==', '===' ), true ) ) {
			return \version_compare( $availableVersion, $requiredVersion, '>=' );
		}

		return \version_compare( $availableVersion, $requiredVersion, $requirement->operator() );
	}

	/**
	 * Checks if all requirements are fulfilled and adds a message to the message manager if necessary.
	 *
	 * @return void
	 */
	public function check() {
		foreach ( $this->requirements as $requirement ) {
			// Match against config.
			$requirementFulfilled = $this->requirementIsFulfilled( $requirement );

			if ( $requirementFulfilled ) {
				continue;
			}

			$this->addMissingRequirementMessage( $requirement );
		}
	}

	/**
	 * Adds a message to the message manager for requirements that cannot be fulfilled.
	 *
	 * @param Requirement $requirement The requirement that cannot be fulfilled.
	 *
	 * @return void
	 */
	private function addMissingRequirementMessage( Requirement $requirement ) {
		switch ( $requirement->component() ) {
			case 'php':
				$this->messageManager->addMessage( new UpgradePhpMessage( $this->textdomain ) );
				break;
			default:
				$this->messageManager->addMessage( new InvalidVersionRequirementMessage( $requirement, $this->configuration->configuredVersion( $requirement ) ) );
				break;
		}
	}

	/**
	 * Determines whether or not there are messages available.
	 *
	 * @return bool Whether or not there are messages to display.
	 */
	public function hasMessages() {
		return $this->messageManager->hasMessages();
	}

	/**
	 * Gets the most recent message from the message manager.
	 *
	 * @return Message The latest message.
	 */
	public function getMostRecentMessage() {
		return $this->messageManager->getLatestMessage();
	}
}

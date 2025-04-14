<?php

namespace Yoast\WHIPv2;

use Yoast\WHIPv2\Interfaces\DismissStorage;

/**
 * A class to dismiss messages.
 */
class MessageDismisser {

	/**
	 * Storage object to manage the dismissal state.
	 *
	 * @var DismissStorage
	 */
	protected $storage;

	/**
	 * The current time.
	 *
	 * @var int
	 */
	protected $currentTime;

	/**
	 * The number of seconds the message will be dismissed.
	 *
	 * @var int
	 */
	protected $threshold;

	/**
	 * MessageDismisser constructor.
	 *
	 * @param int            $currentTime The current time.
	 * @param int            $threshold   The number of seconds the message will be dismissed.
	 * @param DismissStorage $storage     Storage object to manage the dismissal state.
	 */
	public function __construct( $currentTime, $threshold, DismissStorage $storage ) {
		$this->currentTime = $currentTime;
		$this->threshold   = $threshold;
		$this->storage     = $storage;
	}

	/**
	 * Saves the version number to the storage to indicate the message as being dismissed.
	 *
	 * @return void
	 */
	public function dismiss() {
		$this->storage->set( $this->currentTime );
	}

	/**
	 * Checks if the current time is lower than the stored time extended by the threshold.
	 *
	 * @return bool True when current time is lower than stored value + threshold.
	 */
	public function isDismissed() {
		return ( $this->currentTime <= ( $this->storage->get() + $this->threshold ) );
	}

	/**
	 * Checks the nonce.
	 *
	 * @param string $nonce  The nonce to check.
	 * @param string $action The action to check.
	 *
	 * @return bool True when the nonce is valid.
	 */
	public function verifyNonce( $nonce, $action ) {
		return (bool) \wp_verify_nonce( $nonce, $action );
	}
}

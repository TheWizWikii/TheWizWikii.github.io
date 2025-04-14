<?php

namespace Yoast\WHIPv2;

use Yoast\WHIPv2\Interfaces\Message;
use Yoast\WHIPv2\Messages\NullMessage;

/**
 * Manages messages using a global to prevent duplicate messages.
 */
class MessagesManager {

	/**
	 * MessagesManager constructor.
	 */
	public function __construct() {
		if ( ! \array_key_exists( 'whip_messages', $GLOBALS ) ) {
			$GLOBALS['whip_messages'] = array();
		}
	}

	/**
	 * Adds a message to the Messages Manager.
	 *
	 * @param Message $message The message to add.
	 *
	 * @return void
	 */
	public function addMessage( Message $message ) {
		$whipVersion = require __DIR__ . '/Configs/version.php';

		$GLOBALS['whip_messages'][ $whipVersion ] = $message;
	}

	/**
	 * Determines whether or not there are messages available.
	 *
	 * @return bool Whether or not there are messages available.
	 */
	public function hasMessages() {
		return isset( $GLOBALS['whip_messages'] ) && \count( $GLOBALS['whip_messages'] ) > 0;
	}

	/**
	 * Lists the messages that are currently available.
	 *
	 * @return array<Message> The messages that are currently set.
	 */
	public function listMessages() {
		return $GLOBALS['whip_messages'];
	}

	/**
	 * Deletes all messages.
	 *
	 * @return void
	 */
	public function deleteMessages() {
		unset( $GLOBALS['whip_messages'] );
	}

	/**
	 * Gets the latest message.
	 *
	 * @return Message The message. Returns a NullMessage if none is found.
	 */
	public function getLatestMessage() {
		if ( ! $this->hasMessages() ) {
			return new NullMessage();
		}

		$messages = $this->sortByVersion( $this->listMessages() );

		$this->deleteMessages();

		return \array_pop( $messages );
	}

	/**
	 * Sorts the list of messages based on the version number.
	 *
	 * @param array<Message> $messages The list of messages to sort.
	 *
	 * @return array<Message> The sorted list of messages.
	 */
	private function sortByVersion( array $messages ) {
		\uksort( $messages, 'version_compare' );

		return $messages;
	}
}

<?php

namespace Yoast\WHIPv2\Messages;

use Yoast\WHIPv2\Exceptions\EmptyProperty;
use Yoast\WHIPv2\Exceptions\InvalidType;
use Yoast\WHIPv2\Interfaces\Message;

/**
 * Class BasicMessage.
 */
class BasicMessage implements Message {

	/**
	 * Message body.
	 *
	 * @var string
	 */
	private $body;

	/**
	 * Message constructor.
	 *
	 * @param string $body Message body.
	 */
	public function __construct( $body ) {
		$this->validateParameters( $body );

		$this->body = $body;
	}

	/**
	 * Retrieves the message body.
	 *
	 * @return string Message.
	 */
	public function body() {
		return $this->body;
	}

	/**
	 * Validates the parameters passed to the constructor of this class.
	 *
	 * @param string $body Message body.
	 *
	 * @return void
	 *
	 * @throws EmptyProperty When the $body parameter is empty.
	 * @throws InvalidType   When the $body parameter is not of the expected type.
	 */
	private function validateParameters( $body ) {
		if ( empty( $body ) ) {
			throw new EmptyProperty( 'Message body' );
		}

		if ( ! \is_string( $body ) ) {
			throw new InvalidType( 'Message body', $body, 'string' );
		}
	}
}

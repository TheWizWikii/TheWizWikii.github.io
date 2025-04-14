<?php

namespace Yoast\WHIPv2\Messages;

use Yoast\WHIPv2\Interfaces\Message;

/**
 * Class NullMessage.
 */
class NullMessage implements Message {

	/**
	 * Retrieves the message body.
	 *
	 * @return string Message.
	 */
	public function body() {
		return '';
	}
}

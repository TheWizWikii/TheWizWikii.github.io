<?php

namespace Yoast\WHIPv2;

use Yoast\WHIPv2\Interfaces\Listener;

/**
 * Listener for dismissing a message.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded -- Sniff does not count acronyms correctly.
 */
class WPMessageDismissListener implements Listener {

	/**
	 * The name of the dismiss action expected to be passed via $_GET.
	 *
	 * @var string
	 */
	const ACTION_NAME = 'whip_dismiss';

	/**
	 * The object for dismissing a message.
	 *
	 * @var MessageDismisser
	 */
	protected $dismisser;

	/**
	 * Sets the dismisser attribute.
	 *
	 * @param MessageDismisser $dismisser The object for dismissing a message.
	 */
	public function __construct( MessageDismisser $dismisser ) {
		$this->dismisser = $dismisser;
	}

	/**
	 * Listens to a GET request to fetch the required attributes.
	 *
	 * @return void
	 */
	public function listen() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is verified in the dismisser.
		$action = ( isset( $_GET['action'] ) && \is_string( $_GET['action'] ) ) ? \sanitize_text_field( \wp_unslash( $_GET['action'] ) ) : null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is verified in the dismisser.
		$nonce = ( isset( $_GET['nonce'] ) && \is_string( $_GET['nonce'] ) ) ? \sanitize_text_field( \wp_unslash( $_GET['nonce'] ) ) : '';

		if ( $action === self::ACTION_NAME && $this->dismisser->verifyNonce( $nonce, self::ACTION_NAME ) ) {
			$this->dismisser->dismiss();
		}
	}

	/**
	 * Creates an url for dismissing the notice.
	 *
	 * @return string The url for dismissing the message.
	 */
	public function getDismissURL() {
		return \sprintf(
			\admin_url( 'index.php?action=%1$s&nonce=%2$s' ),
			self::ACTION_NAME,
			\wp_create_nonce( self::ACTION_NAME )
		);
	}
}

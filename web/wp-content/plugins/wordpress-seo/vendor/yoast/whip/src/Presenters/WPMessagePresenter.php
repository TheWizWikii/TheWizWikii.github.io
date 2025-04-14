<?php

namespace Yoast\WHIPv2\Presenters;

use Yoast\WHIPv2\Interfaces\Message;
use Yoast\WHIPv2\Interfaces\MessagePresenter;
use Yoast\WHIPv2\MessageDismisser;
use Yoast\WHIPv2\WPMessageDismissListener;

/**
 * A message presenter to show a WordPress notice.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded -- Sniff does not count acronyms correctly.
 */
class WPMessagePresenter implements MessagePresenter {

	/**
	 * The string to show to dismiss the message.
	 *
	 * @var string
	 */
	private $dismissMessage;

	/**
	 * The message to be displayed.
	 *
	 * @var Message
	 */
	private $message;

	/**
	 * Dismisser object.
	 *
	 * @var MessageDismisser
	 */
	private $dismisser;

	/**
	 * WPMessagePresenter constructor.
	 *
	 * @param Message          $message        The message to use in the presenter.
	 * @param MessageDismisser $dismisser      Dismisser object.
	 * @param string           $dismissMessage The copy to show to dismiss the message.
	 */
	public function __construct( Message $message, MessageDismisser $dismisser, $dismissMessage ) {
		$this->message        = $message;
		$this->dismisser      = $dismisser;
		$this->dismissMessage = $dismissMessage;
	}

	/**
	 * Registers hooks to WordPress.
	 *
	 * This is a separate function so you can control when the hooks are registered.
	 *
	 * @return void
	 */
	public function registerHooks() {
		\add_action( 'admin_notices', array( $this, 'renderMessage' ) );
	}

	/**
	 * Renders the messages present in the global to notices.
	 *
	 * @return void
	 */
	public function renderMessage() {
		$dismissListener = new WPMessageDismissListener( $this->dismisser );
		$dismissListener->listen();

		if ( $this->dismisser->isDismissed() ) {
			return;
		}

		$dismissButton = \sprintf(
			'<a href="%2$s">%1$s</a>',
			\esc_html( $this->dismissMessage ),
			\esc_url( $dismissListener->getDismissURL() )
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- output correctly escaped directly above and in the `kses()` method.
		\printf(
			'<div class="error"><p>%1$s</p><p>%2$s</p></div>',
			$this->kses( $this->message->body() ),
			$dismissButton
		);
		// phpcs:enable
	}

	/**
	 * Removes content from the message that we don't want to show.
	 *
	 * @param string $message The message to clean.
	 *
	 * @return string The cleaned message.
	 */
	public function kses( $message ) {
		return \wp_kses(
			$message,
			array(
				'a'      => array(
					'href'   => true,
					'target' => true,
				),
				'strong' => true,
				'p'      => true,
				'ul'     => true,
				'li'     => true,
			)
		);
	}
}

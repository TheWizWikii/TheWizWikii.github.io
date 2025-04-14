<?php

namespace Easy_Plugins\Table_Of_Contents;

use WP_Error;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Debug
 *
 * @package Easy_Plugins\Table_Of_Contents
 */
final class Debug extends WP_Error {

	/**
	 * @since 2.0.13
	 * @var bool
	 */
	protected $display = false;

	/**
	 * @since 2.0.13
	 * @var bool
	 */
	protected $enabled = false;

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * Debug constructor.
	 *
	 * @since 2.0.13
	 *
	 * @param string $code
	 * @param string $message
	 * @param string $data
	 */
	public function __construct( $code = '', $message = '', $data = '' ) {

		parent::__construct( $code, $message, $data );
	}

	/**
	 * @since 2.0.14
	 *
	 * @param string $code
	 * @param string $message
	 * @param string $data
	 *
	 * @return Debug
	 */
	public static function log( $code = '', $message = '', $data = '' ) {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self( $code, $message, $data );

			self::$instance->display = apply_filters(
				'Easy_Plugins/Table_Of_Contents/Debug/Display',
				defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY
			);

			self::$instance->enabled = apply_filters(
				'Easy_Plugins/Table_Of_Contents/Debug/Enabled',
				( defined( 'WP_DEBUG' ) && WP_DEBUG ) && current_user_can( 'manage_options' )
			);

		} else {

			if ( ! empty( $code ) && ! empty( $message ) ) {

				self::$instance->add( $code, $message, $data );
			}
		}

		return self::$instance;
	}

	/**
	 * Adds an error or appends an additional message to an existing error.
	 *
	 * NOTE: Overrides WP_Error::add() to allow support of passing `false` as `$data`.
	 *
	 * @since 2.0.14
	 *
	 * @param string|int $code    Error code.
	 * @param string     $message Error message.
	 * @param mixed      $data    Optional. Error data.
	 */
	public function add( $code, $message, $data = null ) {
		$this->errors[ $code ][] = $message;

		if ( ! is_null( $data ) ) {
			$this->add_data( $data, $code );
		}

		/**
		 * Fires when an error is added to a WP_Error object.
		 *
		 * @since 5.6.0
		 *
		 * @param string|int $code     Error code.
		 * @param string     $message  Error message.
		 * @param mixed      $data     Error data. Might be empty.
		 * @param WP_Error   $wp_error The WP_Error object.
		 */
		do_action( 'wp_error_added', $code, $message, $data, $this );
	}

	/**
	 * @since 2.0.13
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function appendTo( $content = '' ) {

		return $content . $this;
	}

	/**
	 * @since 2.0.13
	 *
	 * @return string
	 */
	public function dump() {

		$dump = array();

		foreach ( (array) $this->errors as $code => $messages ) {

			$data = $this->get_error_data( $code );
			$data = is_string( $data ) ? $data : '<code>' . var_export( $data, true ) . '</code>';
			$data = "\t\t<li class=\"ez-toc-debug-message-data\">{$data}</li>" . PHP_EOL;

			array_push(
				$dump,
				PHP_EOL . "\t<ul class=\"ez-toc-debug-message-{$code}\">" . PHP_EOL . "\t\t<li class=\"ez-toc-debug-message\">" . implode( '</li>' . PHP_EOL . '<li>' . PHP_EOL, $messages ) . '</li>' . PHP_EOL . "{$data}\t</ul>" . PHP_EOL
			);
		}

		return '<div class="ez-toc-debug-message">' . implode( '</div>' . PHP_EOL . '<div class="ez-toc-debug-message">', $dump ) . '</div>' . PHP_EOL;
	}

	/**
	 * @since 2.0.13
	 *
	 * @return string
	 */
	public function __toString() {

		if ( false === $this->enabled ) {

			return '';
		}

		if ( false === $this->display ) {

			return '';
		}

		if ( ! $this->has_errors() ) {

			return '';
		}

		$intro = sprintf(
			esc_html__( 'You see the following because','easy-table-of-contents' ) . ' <a href="%1$s"><code>WP_DEBUG</code></a> ' . esc_html__('and','easy-table-of-contents') . ' <a href="%1$s"><code>WP_DEBUG_DISPLAY</code></a> ' . esc_html__('are enabled on this site. Please disabled these to prevent the display of these developers\' debug messages.','easy-table-of-contents'),
			'https://codex.wordpress.org/WP_DEBUG'
		);

		$intro = PHP_EOL . "<p>{$intro}</p>" .PHP_EOL;
		$dump  = $this->dump();

		return PHP_EOL . "<div class='ez-toc-debug-messages'>{$intro}{$dump}</div>" . PHP_EOL;
	}
}

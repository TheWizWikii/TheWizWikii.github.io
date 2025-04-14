<?php


class ET_Core_Logger {

	/**
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * Checksum for every log message output during the current request.
	 *
	 * @var string[]
	 */
	protected static $HISTORY = array();

	/**
	 * Writes a message to the debug log if it hasn't already been written once.
	 *
	 * @since 3.10
	 *
	 * @param mixed $message
	 * @param int   $bt_index
	 * @param boolean $log_ajax Whether or not to log on AJAX calls.
	 */
	protected static function _maybe_write_log( $message, $bt_index = 4, $log_ajax = true ) {
		global $ET_IS_TESTING_DEPRECATIONS;

		if ( ! is_scalar( $message ) ) {
			$message = print_r( $message, true );
		}

		$message = (string) $message;
		$hash    = md5( $message );

		if ( ! $log_ajax && wp_doing_ajax() ) {
			return;
		}

		if ( $ET_IS_TESTING_DEPRECATIONS ) {
			trigger_error( $message );

		} else if ( getenv( 'CI' ) || ! in_array( $hash, self::$HISTORY ) ) {
			self::$HISTORY[] = $hash;

			self::_write_log( $message, $bt_index );
		}
	}

	/**
	 * Writes a message to the WP Debug and PHP Error logs.
	 *
	 * @param string $message
	 * @param int    $bt_index
	 */
	private static function _write_log( $message, $bt_index = 4 ) {
		$message   = trim( $message );
		$backtrace = debug_backtrace( 1 );
		$class     = '';
		$function  = '';

		if ( ! isset( $backtrace[ $bt_index ] ) ) {
			while ( $bt_index > 0 && ! isset( $backtrace[ $bt_index ] ) ) {
				$bt_index--;
			}

			// We need two stacks to get all the data we need so let's go down one more
			$bt_index--;
		}

		$stack = $backtrace[ $bt_index ];
		$file  = self::$_->array_get( $stack, 'file', '<unknown file>' );
		$line  = self::$_->array_get( $stack, 'line', '<unknown line>' );

		// Name of the function and class (if applicable) are in the previous stack (stacks are in reverse order)
		$stack    = $backtrace[ $bt_index + 1 ];
		$class    = self::$_->array_get( $stack, 'class', '' );
		$function = self::$_->array_get( $stack, 'function', '<unknown function>' );

		if ( $class ) {
			$class .= '::';
		}

		if ( '<unknown file>' !== $file ) {
			$file  = _et_core_normalize_path( $file );
			$parts = explode( '/', $file );
			$parts = array_slice( $parts, -2 );
			$file  = ".../{$parts[0]}/{$parts[1]}";
		}

		$message = " {$file}:{$line}  {$class}{$function}():\n{$message}\n";

		error_log( $message );
	}

	/**
	 * Writes message to the logs if {@link WP_DEBUG} is `true`, otherwise does nothing.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $message
	 * @param int   $bt_index
	 * @param boolean $log_ajax Whether or not to log on AJAX calls.
	 */
	public static function debug( $message, $bt_index = 4, $log_ajax = true ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			self::_maybe_write_log( $message, $bt_index, $log_ajax );
		}
	}

	public static function disable_php_notices() {
		$error_reporting = error_reporting();
		$notices_enabled = $error_reporting & E_NOTICE;

		if ( $notices_enabled ) {
			error_reporting( $error_reporting & ~E_NOTICE );
		}
	}

	/**
	 * Writes an error message to the logs regardless of whether or not debug mode is enabled.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $message
	 * @param int   $bt_index
	 */
	public static function error( $message, $bt_index = 4 ) {
		self::_maybe_write_log( $message, $bt_index );
	}

	public static function enable_php_notices() {
		$error_reporting = error_reporting();
		$notices_enabled = $error_reporting & E_NOTICE;

		if ( ! $notices_enabled ) {
			error_reporting( $error_reporting | E_NOTICE );
		}
	}

	public static function initialize() {
		self::$_ = ET_Core_Data_Utils::instance();
	}

	public static function php_notices_enabled() {
		$error_reporting = error_reporting();
		return $error_reporting & E_NOTICE;
	}
}


ET_Core_Logger::initialize();

<?php
/**
 * Extension API: DiviExtensions class.
 *
 * @package Builder
 * @subpackage API
 */

/**
 * Composite class to manage all Divi Extensions.
 */
class DiviExtensions {

	/**
	 * Utility class instance.
	 *
	 * @since 3.1
	 *
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * The first extension to enable debug mode for itself. Only one Divi Extension can be in
	 * debug mode at a time.
	 *
	 * @var DiviExtension
	 */
	protected static $_debugging_extension;

	/**
	 * List of all instances of the Divi Extension.
	 *
	 * @since 3.1
	 *
	 * @var DiviExtension[] {
	 *     All current Divi Extension instances
	 *
	 *     @type DiviExtension $name Instance
	 * }
	 */
	private static $_extensions;

	/**
	 * Register a Divi Extension instance.
	 *
	 * @since 3.1
	 *
	 * @param DiviExtension $instance Instance.
	 */
	public static function add( $instance ) {
		if ( ! isset( self::$_extensions[ $instance->name ] ) ) {
			self::$_extensions[ $instance->name ] = $instance;
		} else {
			et_error( "A Divi Extension named {$instance->name} already exists!" );
		}
	}

	/**
	 * Get one or all Divi Extension instances.
	 *
	 * @since 3.1
	 *
	 * @param string $name The extension name. Default: 'all'.
	 *
	 * @return DiviExtension|DiviExtension[]|null
	 */
	public static function get( $name = 'all' ) {
		if ( 'all' === $name ) {
			return self::$_extensions;
		}

		return self::$_->array_get( self::$_extensions, $name, null );
	}

	/**
	 * Initialize the base `DiviExtension` class.
	 */
	public static function initialize() {
		self::$_ = ET_Core_Data_Utils::instance();

		require_once ET_BUILDER_DIR . 'api/DiviExtension.php';

		/**
		 * Fires when the {@see DiviExtension} base class is available.
		 *
		 * @since 3.1
		 */
		do_action( 'divi_extensions_init' );
	}

	/**
	 * Whether or not a Divi Extension is in debug mode.
	 *
	 * @since 3.1
	 *
	 * @return bool
	 */
	public static function is_debugging_extension() {
		return ! is_null( self::$_debugging_extension );
	}

	/**
	 * Register's an extension instance for debug mode if one hasn't already been registered.
	 *
	 * @since 3.1
	 *
	 * @param DiviExtension $instance Instance.
	 *
	 * @return bool Whether or not request was successful
	 */
	public static function register_debug_mode( $instance ) {
		if ( ! self::$_debugging_extension ) {
			self::$_debugging_extension = $instance;

			return true;
		}

		return false;
	}
}

DiviExtensions::initialize();

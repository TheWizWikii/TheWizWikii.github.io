<?php
/**
 * Divi extension base class.
 *
 * @package Builder
 * @subpackage API
 * @since 4.6.2
 */

/**
 * Core class used to implement the Divi Extension.
 */
class DiviExtension {

	/**
	 * Utility class instance.
	 *
	 * @since 3.1
	 *
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * Dependencies for the extension's JavaScript bundles.
	 *
	 * @since 3.1
	 *
	 * @var array {
	 *     JavaScript Bundle Dependencies
	 *
	 *     @type string[] $builder  Dependencies for the builder bundle
	 *     @type string[] $frontend Dependencies for the frontend bundle
	 * }
	 */
	protected $_bundle_dependencies = array();

	/**
	 * Builder bundle data
	 *
	 * @since 3.1
	 *
	 * @var array
	 */
	protected $_builder_js_data = array();

	/**
	 * Frontend bundle data
	 *
	 * @since 3.1
	 *
	 * @var array
	 */
	protected $_frontend_js_data = array();

	/**
	 * Whether or not the extension's debug mode is enabled. This should always be enabled
	 * during development and never be enabled in production.
	 *
	 * @since 3.1
	 *
	 * @var bool
	 */
	protected $_debug;

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 3.1
	 *
	 * @var string
	 */
	public $gettext_domain;

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 3.1
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Absolute path to the extension's directory.
	 *
	 * @since 3.1
	 *
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * The extension's directory URL.
	 *
	 * @since 3.1
	 *
	 * @var string
	 */
	public $plugin_dir_url;

	/**
	 * The extension's version.
	 *
	 * @since 3.1
	 *
	 * @var string
	 */
	public $version;

	/**
	 * DiviExtension constructor.
	 *
	 * @since 3.1
	 *
	 * @param string $name This Divi Extension's WP Plugin name/slug.
	 * @param array  $args Argument flexibility for child classes.
	 */
	public function __construct( $name = '', $args = array() ) {
		if ( ! self::$_ ) {
			self::$_ = ET_Core_Data_Utils::instance();
		}

		$this->name = $name;
		if ( $this->name ) {
			$this->_initialize();
		}
	}

	/**
	 * Enqueues minified, production javascript bundles.
	 *
	 * @since 3.1
	 */
	protected function _enqueue_bundles() {
		// Frontend Bundle.
		$bundle_url = "{$this->plugin_dir_url}scripts/frontend-bundle.min.js";

		wp_enqueue_script( "{$this->name}-frontend-bundle", $bundle_url, $this->_bundle_dependencies['frontend'], $this->version, true );

		if ( et_core_is_fb_enabled() ) {
			// Builder Bundle.
			$bundle_url = "{$this->plugin_dir_url}scripts/builder-bundle.min.js";

			wp_enqueue_script( "{$this->name}-builder-bundle", $bundle_url, $this->_bundle_dependencies['builder'], $this->version, true );
		}
	}

	/**
	 * Enqueues non-minified, hot reloaded javascript bundles.
	 *
	 * @since 3.1
	 */
	protected function _enqueue_debug_bundles() {
		// Frontend Bundle.
		$site_url       = wp_parse_url( get_site_url() );
		$hot_bundle_url = "{$site_url['scheme']}://{$site_url['host']}:3000/static/js/frontend-bundle.js";

		wp_enqueue_script( "{$this->name}-frontend-bundle", $hot_bundle_url, $this->_bundle_dependencies['frontend'], $this->version, true );

		if ( et_core_is_fb_enabled() ) {
			// Builder Bundle.
			$hot_bundle_url = "{$site_url['scheme']}://{$site_url['host']}:3000/static/js/builder-bundle.js";

			wp_enqueue_script( "{$this->name}-builder-bundle", $hot_bundle_url, $this->_bundle_dependencies['builder'], $this->version, true );
		}
	}

	/**
	 * Enqueues minified (production) or non-minified (hot reloaded) backend styles.
	 *
	 * @since 4.4.9
	 */
	protected function _enqueue_backend_styles() {
		if ( $this->_debug ) {
			$site_url           = wp_parse_url( get_site_url() );
			$backend_styles_url = "{$site_url['scheme']}://{$site_url['host']}:3000/styles/backend-style.css";
		} else {
			$extension_dir_path  = plugin_dir_path( $this->plugin_dir );
			$backend_styles_path = "{$extension_dir_path}styles/backend-style.min.css";
			$backend_styles_url  = "{$this->plugin_dir_url}styles/backend-style.min.css";

			// Ensure backend style CSS file exists on production.
			if ( ! file_exists( $backend_styles_path ) ) {
				return;
			}
		}

		// Backend Styles - VB.
		wp_enqueue_style( "{$this->name}-backend-styles", $backend_styles_url, array(), $this->version );
	}

	/**
	 * Sets initial value of {@see self::$_bundle_dependencies}.
	 *
	 * @since 3.1
	 */
	protected function _set_bundle_dependencies() {
		/**
		 * Builder script handle name
		 *
		 * @since 3.??
		 *
		 * @param string
		 */

		$this->_bundle_dependencies = array(
			'builder'  => array( 'react-dom', "{$this->name}-frontend-bundle" ),
			'frontend' => array( 'jquery', et_get_combined_script_handle() ),
		);
	}

	/**
	 * Sets {@see self::$_debug} based on the extension's global DEBUG constant.
	 *
	 * @since 3.1
	 */
	protected function _set_debug_mode() {
		$name_parts = explode( '_', get_class( $this ) );
		$prefix     = strtoupper( $name_parts[0] );
		$debug      = $prefix . '_DEBUG';

		$this->_debug = defined( $debug ) && constant( $debug );

		if ( $this->_debug && ! DiviExtensions::register_debug_mode( $this ) ) {
			$this->_debug = false;

			et_error( "You're Doing It Wrong! Only one Divi Extension can be in debug mode at a time." );
		}
	}

	/**
	 * Loads custom modules when the builder is ready.
	 *
	 * @since 3.1
	 * @deprecated ?? - Use {@see 'hook_et_builder_ready'} instead.
	 */
	public function hook_et_builder_modules_loaded() {
		$this->hook_et_builder_ready();
	}

	/**
	 * Loads custom modules when the builder is ready.
	 * {@see 'et_builder_ready'}
	 *
	 * @since 4.10.0
	 */
	public function hook_et_builder_ready() {
		if ( file_exists( trailingslashit( $this->plugin_dir ) . 'loader.php' ) ) {
			require_once trailingslashit( $this->plugin_dir ) . 'loader.php';
		}
	}

	/**
	 * Performs initialization tasks.
	 *
	 * @since 3.1
	 */
	protected function _initialize() {
		DiviExtensions::add( $this );

		$this->_set_debug_mode();
		$this->_set_bundle_dependencies();

		// Setup translations.
		if ( ! empty( $this->gettext_domain ) ) {
			load_plugin_textdomain( $this->gettext_domain, false, basename( $this->plugin_dir ) . '/languages' );
		}

		// Register callbacks.
		register_activation_hook( trailingslashit( $this->plugin_dir ) . $this->name . '.php', array( $this, 'wp_hook_activate' ) );
		register_deactivation_hook( trailingslashit( $this->plugin_dir ) . $this->name . '.php', array( $this, 'wp_hook_deactivate' ) );

		add_action( 'et_builder_ready', array( $this, 'hook_et_builder_ready' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_hook_enqueue_scripts' ) );
	}

	/**
	 * Performs tasks when the plugin is activated.
	 * {@see 'activate_$PLUGINNAME'}
	 *
	 * @since 3.1
	 */
	public function wp_hook_activate() {
		// Force the legacy backend builder to reload its template cache.
		// This ensures that custom modules are available for use right away.
		et_pb_force_regenerate_templates();
	}

	/**
	 * Performs tasks when the plugin is deactivated.
	 * {@see 'deactivate_$PLUGINNAME'}
	 *
	 * @since 3.1
	 */
	public function wp_hook_deactivate() {}

	/**
	 * Enqueues the extension's scripts and styles.
	 * {@see 'wp_enqueue_scripts'}
	 *
	 * @since 3.1
	 * @since 4.4.9 Added backend styles for handling custom builder styles.
	 */
	public function wp_hook_enqueue_scripts() {
		if ( $this->_debug ) {
			$this->_enqueue_debug_bundles();
		} else {
			$styles     = et_is_builder_plugin_active() ? 'style-dbp' : 'style';
			$styles_url = "{$this->plugin_dir_url}styles/{$styles}.min.css";

			wp_enqueue_style( "{$this->name}-styles", $styles_url, array(), $this->version );

			$this->_enqueue_bundles();
		}

		if ( et_core_is_fb_enabled() && ! et_builder_bfb_enabled() ) {
			$this->_enqueue_backend_styles();
		}

		// Normalize the extension name to get actual script name. For example from 'divi-custom-modules' to `DiviCustomModules`.
		$extension_name = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $this->name ) ) );

		// Enqueue frontend bundle's data.
		if ( ! empty( $this->_frontend_js_data ) ) {
			wp_localize_script( "{$this->name}-frontend-bundle", "{$extension_name}FrontendData", $this->_frontend_js_data );
		}

		// Enqueue builder bundle's data.
		if ( et_core_is_fb_enabled() && ! empty( $this->_builder_js_data ) ) {
			wp_localize_script( "{$this->name}-builder-bundle", "{$extension_name}BuilderData", $this->_builder_js_data );
		}
	}

	/**
	 * Enqueues the extension's scripts and styles for admin area.
	 *
	 * @since 4.4.9
	 */
	public function admin_hook_enqueue_scripts() {
		if ( et_builder_bfb_enabled() || et_builder_is_tb_admin_screen() ) {
			$this->_enqueue_backend_styles();
		}
	}
}


new DiviExtension();

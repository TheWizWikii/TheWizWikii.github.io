<?php
/**
 * Ajax Cache.
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class to cache commonly used AJAX requests.
 */
class ET_Builder_Ajax_Cache {

	/**
	 * Instance of this class.
	 *
	 * @var ET_Builder_Ajax_Cache
	 */
	private static $_instance;

	/**
	 * Transient name.
	 *
	 * @var string
	 */
	protected $_transient = 'et_builder_ajax_cache';

	/**
	 * Flag to determine whether to save cache or not on `shutdown` hook.
	 *
	 * @var bool
	 */
	protected $_dirty = false;

	/**
	 * List of all ajax cache.
	 *
	 * @var Array
	 */
	protected $_cache;

	/**
	 * ET_Builder_Ajax_Cache constructor.
	 */
	public function __construct() {
		add_action( 'et_builder_ajax_cache_clear', array( $this, 'clear' ), 10, 1 );
		add_action( 'shutdown', array( $this, 'save' ) );
		add_filter( 'et_builder_dynamic_asset_deps', array( $this, 'add_cache_dep' ), 10, 2 );
	}

	/**
	 * Returns whether cache file exists or not.
	 *
	 * @since 4.0.10
	 *
	 * @return bool
	 */
	public function file_exists() {
		$file = $this->get_file_name();
		return $file && et_()->WPFS()->is_readable( $file );
	}

	/**
	 * Returns whether cache is empty or not.
	 *
	 * @since 4.0.10
	 *
	 * @return bool
	 */
	public function is_empty() {
		$this->load();
		return empty( $this->_cache );
	}

	/**
	 * Enqueue ajax cache as definitions dependency.
	 *
	 * @since 4.0.10
	 *
	 * @param array  $deps Dependencies array.
	 * @param string $key Script handle.
	 *
	 * @return array
	 */
	public function add_cache_dep( $deps, $key ) {
		// Skip all static assets but definitions.
		if ( 'et-dynamic-asset-definitions' !== $key ) {
			return $deps;
		}

		if ( ! $this->file_exists() && ! $this->write_file() ) {
			// Bail out if cache is empty and cannot write the file.
			return $deps;
		}

		// Enqueue ajax cache as definitions dependency.
		$handle = 'et-ajax-cache';
		$deps[] = $handle;
		wp_register_script( $handle, $this->get_url(), array(), ET_BUILDER_VERSION, false );

		return $deps;
	}

	/**
	 * Load cache.
	 *
	 * @since 4.0.10
	 *
	 * @return void
	 */
	public function load() {
		if ( is_array( $this->_cache ) ) {
			// Cache was already loaded.
			return;
		}

		$this->_cache = get_transient( $this->_transient );

		if ( ! is_array( $this->_cache ) ) {
			$this->_cache = array();
			$this->delete_file();
		}
	}

	/**
	 * Save cache.
	 *
	 * @since 4.0.10
	 *
	 * @return void
	 */
	public function save() {
		// Ensure cache is loaded.
		$this->load();

		if ( $this->_dirty ) {
			set_transient( $this->_transient, $this->_cache );
			$this->delete_file();
		}
	}

	/**
	 * Write cache file.
	 *
	 * @since 4.0.10
	 *
	 * @return bool
	 */
	public function write_file() {
		if ( $this->is_empty() ) {
			return false;
		}

		$file  = $this->get_file_name();
		$cache = '';
		foreach ( $this->_cache as $key => $value ) {
			$cache = sprintf( '"%s":%s,', $key, $value );
		}
		$cache = sprintf( '{"ajaxCache":{%s}}', rtrim( $cache, ',' ) );
		$cache = sprintf( 'window.ETBuilderBackend=jQuery.extend(true,%s,window.ETBuilderBackend)', $cache );

		et_()->WPFS()->put_contents( $file, $cache );
		return $this->file_exists();
	}

	/**
	 * Delete cache file.
	 *
	 * @since 4.0.10
	 *
	 * @return void
	 */
	public function delete_file() {
		if ( $this->file_exists() ) {
			et_()->WPFS()->delete( $this->get_file_name() );
		}
	}

	/**
	 * Set cache key.
	 *
	 * @since 4.0.10
	 *
	 * @param string $key Cache key.
	 * @param string $content Cache value.
	 *
	 * @return void
	 */
	public function set( $key, $content ) {
		$this->load();
		$this->_cache[ $key ] = wp_json_encode( $content );
		$this->_dirty         = true;
	}

	/**
	 * Unset cache key.
	 *
	 * @since 4.0.10
	 *
	 * @param string $key Cache key.
	 *
	 * @return void
	 */
	public function unset_( $key ) {
		$this->load();
		if ( isset( $this->_cache[ $key ] ) ) {
			unset( $this->_cache[ $key ] );
			$this->_dirty = true;
		}
	}

	/**
	 * Clear cache.
	 *
	 * @return void
	 */
	public function clear() {
		delete_transient( $this->_transient );
	}

	/**
	 * Get cache file name.
	 *
	 * @since 4.0.10
	 *
	 * @return string.
	 */
	public function get_file_name() {
		// Per language Cache due to some data being localized.
		$lang   = is_admin() || et_fb_is_enabled() ? get_user_locale() : get_locale();
		$lang   = trim( sanitize_file_name( $lang ), '.' );
		$prefix = 'ajax';
		$cache  = et_()->path( et_core_cache_dir()->path, $lang );
		$files  = glob( "{$cache}/{$prefix}-*.js" );
		$exists = is_array( $files ) && $files;

		if ( $exists ) {
			return $files[0];
		}

		wp_mkdir_p( $cache );

		// Create uniq filename.
		$uniq = str_replace( '.', '', (string) microtime( true ) );
		$file = "{$cache}/{$prefix}-{$uniq}.js";

		return et_()->WPFS()->is_writable( dirname( $file ) ) ? $file : false;
	}

	/**
	 * Get cache url.
	 *
	 * @since 4.0.10
	 *
	 * @return string
	 */
	public function get_url() {
		$file = $this->get_file_name();
		$lang = basename( dirname( $file ) );
		$name = basename( $file );
		return et_()->path( et_core_cache_dir()->url, $lang, $name );
	}

	/**
	 * Get the class instance.
	 *
	 * @since 4.0.10
	 *
	 * @return ET_Builder_Ajax_Cache
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		/**
		 * Filters the cache class instance that for caches AJAX requests.
		 *
		 * @param ET_Builder_Ajax_Cache Cache Instance
		 *
		 * @see GlobalHistoryMigrationTest
		 *
		 * @since 4.14.0
		 */
		return apply_filters( 'et_builder_ajax_cache_instance', self::$_instance );
	}

}

ET_Builder_Ajax_Cache::instance();

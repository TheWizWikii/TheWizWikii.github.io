<?php

class ET_Core_Cache_Directory {

	/**
	 * @since 4.0.8
	 * @var self
	 */
	protected static $_instance;

	/**
	 * Whether or not we can write to the cache directory.
	 *
	 * @since 4.0.8
	 * @var bool
	 */
	public $can_write;

	/**
	 * Absolute path to cache directory
	 *
	 * @since 4.0.8
	 * @var string
	 */
	public $path = '';

	/**
	 * URL for {@see self::$path}
	 *
	 * @since 4.0.8
	 * @var string
	 */
	public $url = '';

	/**
	 * @since 4.0.8
	 * @var WP_Filesystem_Base
	 */
	public $wpfs;

	/**
	 * ET_Core_Cache_Directory constructor.
	 *
	 * @since 4.0.8
	 */
	public function __construct() {
		if ( self::$_instance ) {
			et_wrong( 'Use "ET_Core_Cache_Directory::instance()" instead of "new ET_Core_Cache_Directory".' );

			return;
		}

		self::$_instance = $this;

		$this->_initialize();
	}

	/**
	 * Determines the cache directory path and url based on where we can write files
	 * and whether or not the user has defined a custom path and url.
	 *
	 * @since 4.0.8
	 */
	protected function _initialize() {
		$this->_initialize_wpfs();

		if ( $this->_maybe_use_custom_path() ) {
			return;
		}

		$uploads_dir_info = (object) wp_get_upload_dir();
		$path             = et_()->path( WP_CONTENT_DIR, 'et-cache' );
		$url              = content_url( 'et-cache' );

		$can_write  = $this->wpfs->is_writable( $path ) && ! is_file( $path );
		$can_create = ! $can_write && $this->wpfs->is_writable( WP_CONTENT_DIR );

		if ( ! $can_write && ! $can_create && $this->wpfs->is_writable( $uploads_dir_info->basedir ) ) {
			// We can create our cache directory in the uploads directory
			$can_create = true;
			$path       = et_()->path( $uploads_dir_info->basedir, 'et-cache' );
			$url        = et_()->path( $uploads_dir_info->baseurl, 'et-cache' );
		}

		$this->can_write = $can_write || $can_create;
		$this->path      = et_()->normalize_path( $path );
		$this->url       = $url;

		$this->_maybe_adjust_path_for_multisite( $uploads_dir_info );

		/**
		 * Absolute path to directory where we can store cache files.
		 *
		 * @since 4.0.8
		 * @var string
		 */
		define( 'ET_CORE_CACHE_DIR', $this->path );

		/**
		 * URL to {@see ET_CORE_CACHE_DIR}.
		 *
		 * @since 4.0.8
		 * @var string
		 */
		define( 'ET_CORE_CACHE_DIR_URL', $this->url );

		$this->can_write && et_()->ensure_directory_exists( $this->path );
	}

	/**
	 * Ensures that the WP Filesystem API has been initialized.
	 *
	 * @since??
	 *
	 * @return WP_Filesystem_Base
	 */
	protected function _initialize_wpfs() {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		/**
		 * Filters the WP_Filesystem args.
		 *
		 * @since 4.18.1
		 *
		 * @param  $wpfs_args  Arguments to use when initializing WP_Filesystem
		 *
		 * @return |array
		 */
		$wpfs_args = apply_filters( 'et_core_cache_wpfs_args', array() );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- may fail due to the permissions denied error
		if ( defined( 'ET_CORE_CACHE_DIR' ) && @WP_Filesystem( $wpfs_args, ET_CORE_CACHE_DIR, true ) ) {
			// We can write to a user-specified directory
			return $this->wpfs = $GLOBALS['wp_filesystem'];
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- may fail due to the permissions denied error
		if ( @WP_Filesystem( $wpfs_args, false, true ) ) {
			// We can write to WP_CONTENT_DIR
			return $this->wpfs = $GLOBALS['wp_filesystem'];
		}

		$uploads_dir = (object) wp_get_upload_dir();

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- may fail due to the permissions denied error
		if ( @WP_Filesystem( $wpfs_args, $uploads_dir->basedir, true ) ) {
			// We can write to the uploads directory
			return $this->wpfs = $GLOBALS['wp_filesystem'];
		}

		// We aren't able to write to the filesystem so let's just make sure $this->wpfs
		// is an instance of the filesystem base class so that calling it won't cause errors.
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';

		// Write notice to log when WP_DEBUG is enabled.
		$nl  = PHP_EOL;
		$msg = 'Unable to write to filesystem. Please ensure that PHP has write access to one of ';
		$msg .= "the following directories:{$nl}{$nl}\t- WP_CONTENT_DIR{$nl}\t- wp_upload_dir(){$nl}\t- ET_CORE_CACHE_DIR.";

		et_debug( $msg );

		return $this->wpfs = new WP_Filesystem_Base;
	}

	/**
	 * Adjusts the path for multisite if necessary.
	 *
	 * @since 4.0.8
	 *
	 * @param stdClass $uploads_dir_info (object) wp_get_upload_dir()
	 */
	protected function _maybe_adjust_path_for_multisite( $uploads_dir_info ) {
		if ( et_()->starts_with( $this->path, $uploads_dir_info->basedir ) || ! is_multisite() ) {
			return;
		}

		$site       = get_site();
		$network_id = $site->site_id;
		$site_id    = $site->blog_id;

		$this->path = et_()->path( $this->path, $network_id, $site_id );
		$this->url  = et_()->path( $this->url, $network_id, $site_id );
	}

	/**
	 * Whether or not the user has defined a custom path for the cache directory.
	 *
	 * @since 4.0.8
	 *
	 * @return bool
	 */
	protected function _maybe_use_custom_path() {
		if ( ! defined( 'ET_CORE_CACHE_DIR' ) ) {
			return false;
		}

		$this->path = ET_CORE_CACHE_DIR;

		if ( ! $this->can_write = $this->wpfs->is_writable( $this->path ) ) {
			et_wrong( 'ET_CORE_CACHE_DIR is defined but not writable.', true );
		}

		if ( defined( 'ET_CORE_CACHE_DIR_URL' ) ) {
			$this->url = ET_CORE_CACHE_DIR_URL;
		} else {
			et_wrong( 'When ET_CORE_CACHE_DIR is defined, ET_CORE_CACHE_DIR_URL must also be defined.', true );
		}

		return true;
	}

	/**
	 * Returns the class instance.
	 *
	 * @since 4.0.8
	 *
	 * @return ET_Core_Cache_Directory
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}
}

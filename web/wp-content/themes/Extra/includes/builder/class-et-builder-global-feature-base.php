<?php
/**
 * Feature base class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Base Feature feature.
 *
 * @since 4.10.0
 */
class ET_Builder_Global_Feature_Base {

	const CACHE_META_KEY = '_et_builder_global_feature_cache';

	/**
	 * Primed status.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_primed = false;

	/**
	 * Cache array.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache = [];

	/**
	 * Cache status.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache_dirty = false;

	/**
	 * Construct instance.
	 */
	public function __construct() {
		if ( self::enabled() ) {
			$this->cache_prime();

			if ( ! has_action( 'shutdown', [ $this, 'cache_save' ] ) ) {
				add_action( 'shutdown', [ $this, 'cache_save' ] );
			}
		}
	}

	/**
	 * Purge the features cache.
	 *
	 * @since 4.10.0
	 */
	public static function purge_cache() {
		delete_option( static::CACHE_META_KEY );
	}

	/**
	 * Get the features cache.
	 *
	 * @since 4.10.0
	 *
	 * @return mixed
	 */
	public static function load_cache() {
		return get_option( static::CACHE_META_KEY );
	}

	/**
	 * Tell whether we should use cache or not.
	 *
	 * @since 4.10.0
	 *
	 * @return bool
	 */
	public static function enabled() {
		/**
		 * Whether Global Feature Cache should be enabled or not.
		 *
		 * @since ?
		 *
		 * @param bool $enabled.
		 */
		return apply_filters( 'et_builder_global_feature_cache_enabled', et_builder_is_frontend() );
	}

	/**
	 * Prime the cache.
	 *
	 * @since 4.10.0
	 */
	public function cache_prime() {
		if ( empty( $this->_primed ) ) {

			$meta = self::load_cache();

			if ( isset( $meta[1] ) ) {
				list( $stored_index, $cache ) = $meta;
				$current_index                = (string) self::_get_cache_index();
				$this->_cache                 = ( $current_index === $stored_index ) ? $cache : [];
			}

			$this->_primed = true;
		}
	}

	/**
	 * Save the cache.
	 *
	 * @since 4.10.0
	 */
	public function cache_save() {
		// Only if cache is "dirty" and builder is used.
		if ( $this->_cache_dirty ) {
			$cache = array( self::_get_cache_index(), $this->_cache );
			update_option( static::CACHE_META_KEY, $cache );
		}
	}

	/**
	 * Get Cache Version Index Items.
	 *
	 * @since 4.10.0
	 * @access protected
	 * @return array Cache version items.
	 */
	protected static function _get_cache_index_items() {
		global $wp_version;

		/**
		 * Filters global feature cache index items.
		 *
		 * @since 4.10.0
		 *
		 * @param array  Assoc array of cache index items.
		 * @param string The cache meta key that the cache index items belong to.
		 */
		return apply_filters(
			'et_global_feature_cache_index_items',
			array(
				'gph'  => ET_Builder_Global_Presets_History::instance()->get_global_history_index(),
				'divi' => et_get_theme_version(),
				'wp'   => $wp_version,
			),
			static::CACHE_META_KEY
		);
	}

	/**
	 * Get Cache Version Index.
	 *
	 * @since 4.10.0
	 * @access protected
	 * @return string .Cache version index.
	 */
	public static function _get_cache_index() {
		return wp_json_encode( self::_get_cache_index_items() );
	}

	/**
	 * Get cache
	 *
	 * @since 4.10.0
	 * @param string $key Cache key.
	 * @param string $group Cache Group.
	 */
	public function cache_get( $key, $group = 'default' ) {
		$exists = isset( $this->_cache[ $group ] ) && isset( $this->_cache[ $group ][ $key ] );
		return $exists ? $this->_cache[ $group ][ $key ] : null;
	}

	/**
	 * Set cache
	 *
	 * @since 4.10.0
	 * @param string $key Cache key.
	 * @param mixed  $value To be cached.
	 * @param string $group Cache group.
	 */
	public function cache_set( $key, $value, $group = 'default' ) {
		$this->_cache[ $group ][ $key ] = $value;
		$this->_cache_dirty             = true;
	}

	/**
	 * Check for cached value.
	 *
	 * First check cache if present, if not, determine
	 * from calling the callback.
	 *
	 * @param string   $key Name of item.
	 * @param function $cb  Callback function to perform logic.
	 * @param string   $group Cache group.
	 *
	 * @return bool/mixed Result.
	 */
	public function get( $key, $cb, $group = 'default' ) {
		// short circuit.
		$result = $this->cache_get( $key, $group );

		if ( is_null( $result ) ) {
			// Set cache for next time.
			$result = $cb();
			$this->cache_set( $key, $result, $group );
		}

		return $result;
	}
}

<?php
/**
 * Feature base class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Post Based Feature Base.
 *
 * @since 4.10.0
 */
class ET_Builder_Post_Feature_Base {


	// Only save cache if time (milliseconds) to generate it is above this threshold.
	const CACHE_SAVE_THRESHOLD = 15;
	const CACHE_META_KEY       = '_et_builder_module_feature_cache';

	/**
	 * Post ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_post_id = 0;

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
	 * Total time needed to populate the cache.
	 *
	 * @access protected
	 * @var float
	 */
	protected $_cache_set_time = 0;

	/**
	 * Whether the feature manager is enabled.
	 *
	 * @access protected
	 * @var null|bool
	 */
	protected static $_enabled = null;

	/**
	 * Whether this page load is loading the cache or using existing cache.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_cache_loading = false;

	/**
	 * `ET_Builder_Post_Feature_Base` instance.
	 *
	 * @var ET_Builder_Post_Feature_Base
	 */
	private static $_instance;

	/**
	 * Construct instance.
	 */
	public function __construct() {
		if ( self::enabled() ) {
			global $shortname;

			$this->_post_id = ET_Builder_Element::get_current_post_id();

			if ( 'extra' === $shortname ) {
				if ( ( et_is_extra_layout_used_as_home() || et_is_extra_layout_used_as_front() ) && ! is_null( et_get_extra_home_layout_id() ) ) {
					$this->_post_id = et_get_extra_home_layout_id();
				} elseif ( ( is_category() || is_tag() ) && ! is_null( et_get_extra_tax_layout_id() ) ) {
					$this->_post_id = et_get_extra_tax_layout_id();
				}
			}

			$this->cache_prime();

			if ( ! has_action( 'shutdown', [ $this, 'cache_save' ] ) ) {
				add_action( 'shutdown', [ $this, 'cache_save' ] );
			}
		}
	}

	/**
	 * Initialize ET_Builder_Post_Feature_Base class.
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new static();
		}

		return self::$_instance;

	}

	/**
	 * Purge the features cache for a given post.
	 *
	 * @since 4.10.0
	 *
	 * @param int $post_id The post ID to purge cache from.
	 */
	public static function purge_cache( $post_id = '' ) {
		if ( $post_id ) {
			delete_post_meta( $post_id, static::CACHE_META_KEY );
		} else {
			delete_post_meta_by_key( static::CACHE_META_KEY );
		}
	}

	/**
	 * Get the features cache for a given post.
	 *
	 * @since 4.10.0
	 *
	 * @param int $post_id The post ID to get cache from.
	 * @return mixed
	 */
	public static function load_cache( $post_id ) {
		return get_post_meta( $post_id, static::CACHE_META_KEY, true );
	}

	/**
	 * Tell whether we should use cache or not.
	 *
	 * @since 4.10.0
	 *
	 * @return bool
	 */
	public static function enabled() {
		if ( null === self::$_enabled ) {

			$et_dynamic_module_framework = et_builder_dynamic_module_framework();

			$enabled = et_builder_is_frontend() && 'on' === $et_dynamic_module_framework;

			/**
			 * Whether Post Feature Cache should be enabled or not.
			 *
			 * @since ?
			 *
			 * @param bool $enabled.
			 */
			self::$_enabled = apply_filters( 'et_builder_post_feature_cache_enabled', $enabled );
		}

		return self::$_enabled;
	}

	/**
	 * Prime the cache.
	 *
	 * @since 4.10.0
	 */
	public function cache_prime() {
		if ( empty( $this->_primed ) ) {

			$meta          = self::load_cache( $this->_post_id );
			$current_index = (string) self::_get_cache_index();
			$stored_index  = null;

			if ( isset( $meta[1] ) ) {
				list( $stored_index, $cache ) = $meta;
				$this->_cache                 = ( $current_index === $stored_index ) ? $cache : [];
			}

			do_action( 'et_builder_post_feature_cache_primed', $this->_cache, $this->_post_id, $current_index, $stored_index );

			// determine if were loading cache,
			// or using previous cache values.
			$this->_cache_loading = empty( $this->_cache );

			$this->_primed = true;
		}
	}

	/**
	 * Save the cache.
	 *
	 * @since 4.10.0
	 */
	public function cache_save() {
		if ( ! self::enabled() ) {
			return;
		}

		$cache_index        = self::_get_cache_index();
		$is_above_threshold = $this->_cache_set_time >= self::CACHE_SAVE_THRESHOLD;

		do_action( 'et_builder_post_feature_cache_save', $this->_cache, $this->_post_id, $this->_cache_set_time, $is_above_threshold, $cache_index );

		// Only save cache if time to generate it is above a certain threshold.
		if ( $is_above_threshold ) {
			$cache = array( $cache_index, $this->_cache );
			update_post_meta( $this->_post_id, static::CACHE_META_KEY, $cache );
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

		$dynamic_assets = ET_Dynamic_Assets::init();
		$tb_ids = $dynamic_assets->get_theme_builder_template_ids();

		$tb_data = [];
		foreach ( $tb_ids as $tb_id ) {
			$tb_post = get_post( $tb_id );
			$tb_data[ $tb_id ] = $tb_post->post_modified_gmt;
		}

		// Ignore WP Editor template if current page use TB.
		$wpe_data = [];
		if ( empty( $tb_data ) ) {
			$wpe_ids = $dynamic_assets->get_wp_editor_template_ids();
			foreach ( $wpe_ids as $wpe_id ) {
				$wpe_post            = get_post( $wpe_id );
				$wpe_data[ $wpe_id ] = $wpe_post->post_modified_gmt;
			}
		}

		return array(
			'gph'  => ET_Builder_Global_Presets_History::instance()->get_global_history_index(),
			'divi' => et_get_theme_version(),
			'wp'   => $wp_version,
			'tb'   => $tb_data,
			'wpe'  => $wpe_data,
		);
	}

	/**
	 * Get Cache Version Index.
	 *
	 * @since 4.10.0
	 * @access protected
	 * @return string .Cache version index.
	 */
	protected static function _get_cache_index() {
		return wp_json_encode( self::_get_cache_index_items() );
	}

	/**
	 * Get cache
	 *
	 * @since 4.10.0
	 * @param string $key Cache key.
	 * @param string $group Cache group.
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
	 * @param float  $elapsed How much time it took to generate the value.
	 */
	public function cache_set( $key, $value, $group = 'default', $elapsed = 0 ) {
		// Only save truthy values into cache.
		if ( $value ) {
			$this->_cache[ $group ][ $key ] = $value;
		}
		$this->_cache_set_time         += $elapsed * 1000;
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
		if ( ! self::enabled() ) {
			return $cb();
		}

		$result = $this->cache_get( $key, $group );

		if ( is_null( $result ) ) {
			if ( $this->_cache_loading ) {
				// Set cache for next time.
				$before  = microtime( true );
				$result  = $cb();
				$elapsed = microtime( true ) - $before;

				$this->cache_set( $key, $result, $group, $elapsed );
			} else {
				// No entry found in a previsouly loaded cache,
				// means the answer was falsey last time $cb() was checked,
				// as falsey values arent stored in cache.
				$result = false;
			}
		}

		return $result;
	}
}

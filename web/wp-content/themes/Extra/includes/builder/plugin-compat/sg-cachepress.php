<?php
/**
 * Plugin compatibility for Siteground Optimizer.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin compatibility for SiteGround Optimizer
 *
 * @since 4.11.0
 *
 * @link https://wordpress.org/plugins/sg-cachepress/
 */
class ET_Builder_Plugin_Compat_SiteGround_Optimizer extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Excluded stylesheets.
	 *
	 * @var null
	 */
	private $_excluded = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'sg-cachepress/sg-cachepress.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @return void
	 */
	public function init_hooks() {
		if ( ! is_plugin_active( $this->plugin_id ) ) {
			return;
		}

		// Do nothing if it's not on frontend.
		if ( ! et_builder_is_frontend() ) {
			return;
		}

		// Do nothing if Dynamic CSS and Critical CSS are disabled.
		if ( ! ( et_use_dynamic_css() || et_builder_is_critical_enabled() ) ) {
			return;
		}

		add_filter( 'et_core_page_resource_tag', [ $this, 'get_page_resource_handles' ], 10, 3 );
		add_filter( 'sgo_css_combine_exclude', [ $this, 'exclude_inline_styles_from_siteground_cache' ], 11 );
	}

	/**
	 * Get PageResource handles.
	 *
	 * @param string $tag HTML tag.
	 * @param string $handle Resource handle.
	 * @param string $src Resource src.
	 *
	 * @since 4.14.3
	 *
	 * @return string
	 */
	public function get_page_resource_handles( $tag, $handle, $src ) {
		if ( empty( $src ) ) {
			return;
		}

		// Some styles we enqueue too late for wp_enqueue_style so the markup is printed directly.
		// However, SG Optimizer can only exclude registered styles, hence we do it now.
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			$handle .= '-cachepress';
			wp_register_style( $handle, $src, [], ET_BUILDER_VERSION );
		}

		$this->_excluded[] = $handle;

		return $tag;
	}

	/**
	 * Exclude styles from being combined in SiteGround cache.
	 *
	 * @param array $excluded Excluded styles from being combined.
	 */
	public function exclude_inline_styles_from_siteground_cache( $excluded ) {
		global $wp_styles, $shortname;

		$prefix = 'divi-builder';

		if ( 'divi' === $shortname ) {
			$prefix = 'divi';
		} elseif ( 'extra' === $shortname ) {
			$prefix = 'extra';
		}

		$registered = array_keys( $wp_styles->registered );

		foreach ( $registered as $handle ) {
			// Exclude all our styles.
			if ( false !== strpos( $handle, $prefix ) ) {
				$excluded[] = $handle;
			}
		}

		return array_merge(
			$this->_excluded,
			$excluded
		);
	}
}

new ET_Builder_Plugin_Compat_SiteGround_Optimizer();

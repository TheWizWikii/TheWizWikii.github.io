<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Smush
 *
 * @since 3.17.1
 *
 * @link https://wordpress.org/plugins/wp-smushit/
 */
class ET_Builder_Plugin_Compat_Smush extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = $this->_get_plugin_id();
		$this->init_hooks();
	}

	/**
	 * Get the currently activated plugin id as the FREE and PRO versions are separate plugins.
	 *
	 * @since 4.0.5
	 *
	 * @return string
	 */
	protected function _get_plugin_id() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$pro  = 'wp-smush-pro/wp-smush.php';
		$free = 'wp-smushit/wp-smush.php';

		return is_plugin_active( $pro ) ? $pro : $free;
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1601
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		$enabled = array(
			// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
			'vb'  => et_()->array_get( $_GET, 'et_fb' ),
			'bfb' => et_()->array_get( $_GET, 'et_bfb' ),
			'tb'  => et_()->array_get( $_GET, 'et_tb' ),
			// phpcs:enable
		);

		add_filter( 'wp_smush_should_skip_parse', array( $this, 'maybe_skip_parse' ), 11 );

		if ( $enabled['vb'] || $enabled['bfb'] || $enabled['tb'] ) {
			// Plugin's `enqueue` function will cause a PHP notice unless
			// early exit is forced using the following custom filter
			add_filter( 'wp_smush_enqueue', '__return_false' );

			$class = $this->get_smush_class();

			if ( ! empty( $class ) ) {
				// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
				$mod   = call_user_func( array( $class, 'get_instance' ) )->core()->mod;
				$props = get_object_vars( $mod );

				if ( isset( $props['lazy'] ) ) {
					// In Smush 3.3+, lazy loading enqueues and inlines several
					// scripts but the instance is public so we can get a
					// reference and remove the enqueuing action.
					remove_action( 'wp_enqueue_scripts', array( $props['lazy'], 'enqueue_assets' ) );
				} else {
					// The lazy loading instance is private in Smush 3.2.* so
					// we dequeue the script it enqueues as those versions
					// only load a single script.
					add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_lazy_load' ) );
				}
			}
		}

		add_filter( 'et_core_page_resource_get_data', array( $this, 'maybe_get_background_images_cdn' ), 10, 3 );
	}

	/**
	 * Disable Smush page parsing in VB.
	 *
	 * @param boolean $skip
	 *
	 * @return boolean
	 */
	public function maybe_skip_parse( $skip ) {
		if ( et_core_is_fb_enabled() ) {
			return true;
		}

		return $skip;
	}

	/**
	 * Get the base Smush class name.
	 *
	 * @since 4.0.3
	 *
	 * @return string
	 */
	public function get_smush_class() {
		$classes = array(
			'WP_Smush',
			// @since 3.3.0
			'Smush\\WP_Smush',
		);

		foreach ( $classes as $test ) {
			if ( class_exists( $test ) ) {
				return $test;
			}
		}

		return '';
	}

	/**
	 * Dequeue Smush lazy load in builder.
	 *
	 * @since 4.0.10
	 */
	public function dequeue_lazy_load() {
		if ( wp_script_is( 'smush-lazy-load', 'enqueued' ) ) {
			wp_dequeue_script( 'smush-lazy-load' );
		}
	}

	/**
	 * Maybe convert background images local URL inside the styles into CDN before it's
	 * saved as static resource.
	 *
	 * @since 4.4.9
	 *
	 * @param array                $data_resource
	 * @param string               $context
	 * @param ET_Core_PageResource $page_resource
	 *
	 * @return string
	 */
	public function maybe_get_background_images_cdn( $data_resource, $context, $page_resource ) {
		// Bail early if the context is not 'file' or the data resource is empty or resource
		// is not unified (single post & builder is being used).
		if ( 'file' !== $context || empty( $data_resource ) || ! strpos( $page_resource->slug, 'unified' ) ) {
			return $data_resource;
		}

		if ( ! class_exists( '\Smush\Core\Settings' ) || ! class_exists( '\Smush\Core\Modules\Helpers\Parser' ) ) {
			return $data_resource;
		}

		$smush_settings    = Smush\Core\Settings::get_instance();
		$cdn               = $smush_settings->get( 'cdn' );
		$background_images = $smush_settings->get( 'background_images' );

		// Both of CDN and Background Images modules should be activated.
		if ( ! $cdn || ! $background_images ) {
			return $data_resource;
		}

		$new_data_resource = $data_resource;
		$smush_parser      = new Smush\Core\Modules\Helpers\Parser();

		$smush_parser->enable( 'cdn' );
		$smush_parser->enable( 'background_images' );

		// Converting background images local URL into CDN.
		foreach ( $data_resource as $priority => $data_part ) {
			$new_data_part = array();

			foreach ( $data_part as $data ) {
				$new_data_part[] = $smush_parser->parse_page( $data );
			}

			$new_data_resource[ $priority ] = $new_data_part;
		}

		return $new_data_resource;
	}
}

new ET_Builder_Plugin_Compat_Smush();

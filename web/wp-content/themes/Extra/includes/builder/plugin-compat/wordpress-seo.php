<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Yoast SEO
 *
 * @since 3.0.76 (builder version)
 * @link https://wordpress.org/plugins/wordpress-seo/
 */
class ET_Builder_Plugin_Compat_WordPress_SEO extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'wordpress-seo/wp-seo.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Latest plugin version: 3.1.1
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Enable Sitemap Cache
		add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_true' );
		add_filter( 'pre_get_posts', array( $this, 'maybe_load_builder_modules_early' ), 0 );
		add_filter( 'wpseo_indexable_excluded_post_types', array( $this, 'exclude_tb_post_types' ), 0 );
	}

	/**
	 * Checks to see if the current request is for a sitemap and if so, loads the builder's
	 * modules so that they are loaded before Yoast generates the sitemap.
	 * {@see 'pre_get_posts' (0) Must run before Yoast's callback which has priority of 1.}
	 *
	 * @param WP_Query $query
	 */
	public function maybe_load_builder_modules_early( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( ! get_query_var( 'xsl' ) && ! get_query_var( 'sitemap' ) ) {
			return;
		}

		remove_action( 'wp', 'et_builder_init_global_settings', 9 );
		remove_action( 'wp', 'et_builder_add_main_elements' );

		add_filter( 'wpseo_sitemap_content_before_parse_html_images', array( $this, 'do_shortcode' ) );
	}

	/**
	 * Exclude TB post types from indexable posts.
	 *
	 * @param array $post_types Post Types.
	 *
	 * @return array;
	 */
	public function exclude_tb_post_types( $post_types ) {
		$tb_post_types = array(
			ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
			ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
			ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
		);

		return array_merge( $post_types, $tb_post_types );
	}

	public function do_shortcode( $content ) {
		// Check if content includes ET shortcode.
		if ( false === strpos( $content, '[et_pb_section' ) ) {
			// None found, bye.
			return $content;
		}

		// Load modules (only once).
		if ( ! did_action( 'et_builder_ready' ) ) {
			et_builder_init_global_settings();
			et_builder_add_main_elements();
		}

		// Render the shortcode.
		return apply_filters( 'the_content', $content );
	}
}

new ET_Builder_Plugin_Compat_WordPress_SEO();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Amazon S3 Offload
 *
 * @since 3.0.49
 *
 * @link https://wordpress.org/plugins/amazon-s3-and-cloudfront/
 */
class ET_Builder_Plugin_Compat_WP_Offload_S3 extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'amazon-s3-and-cloudfront/wordpress-s3.php';

		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 1.1.6
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest theme version
		add_action( 'et_fb_ajax_save_verification_result', array( $this, 'override_fb_ajax_save_verification' ) );

		// Filter attachment IDs for images with an external/CDN URL.
		add_filter( 'et_get_attachment_id_by_url_pre', array( $this, 'et_get_attachment_id_by_url_pre' ), 10, 2 );

		// Filter image urls in the raw post content.
		add_filter( 'et_fb_load_raw_post_content', array( $this, 'filter_urls_on_raw_post_content' ), 10, 2 );
	}

	/**
	 * @param bool $verification
	 *
	 * @return bool
	 */
	function override_fb_ajax_save_verification( $verification ) {
		return true;
	}

	/**
	 * Filter attachment ID in case it has an external/CDN URL.
	 *
	 * @since 4.2.1
	 *
	 * @param bool|int $attachment_id_pre Default value. Default is false.
	 * @param string   $url               URL of the image need to query.
	 *
	 * @return bool|int
	 */
	public function et_get_attachment_id_by_url_pre( $attachment_id_pre, $url ) {
		global $as3cf;

		$as3cf_s3_to_local = new AS3CF_S3_To_Local( $as3cf );
		$attachment_id     = $attachment_id_pre;

		// In WP Offload Media 2.6 the get_attachment_id_from_url() method was renamed to get_item_source_from_url().
		// Now the method also returns bool or array. If it's an array we need to process it.
		if ( method_exists( $as3cf_s3_to_local, 'get_item_source_from_url' ) ) {
			$item_source = $as3cf_s3_to_local->get_item_source_from_url( $url );

			if ( is_array( $item_source ) && array_key_exists( 'id', $item_source ) ) {
				$attachment_id = $item_source['id'];
			}
		}

		// Pre 2.6. get_attachment_id_from_url() will return the attachment ID.
		if ( method_exists( $as3cf_s3_to_local, 'get_attachment_id_from_url' ) ) {
			$attachment_id = $as3cf_s3_to_local->get_attachment_id_from_url( $url );
		}

		return $attachment_id;
	}

	/**
	 * Filter the raw post content to be used to generate the builder data.
	 * The `et_fb_get_builder_shortcode_object()` directly access the raw post_content, so the image URL not properly transformed
	 *
	 * @since 4.7.0
	 *
	 * @param string $post_content The post content.
	 * @param string $post_id      The post ID.
	 * @return string
	 */
	public function filter_urls_on_raw_post_content( $post_content, $post_id ) {
		global $as3cf;

		if ( property_exists( $as3cf, 'filter_local' ) && method_exists( $as3cf->filter_local, 'filter_post' ) ) {
			$post_content = $as3cf->filter_local->filter_post( $post_content );
		}

		return $post_content;
	}
}

new ET_Builder_Plugin_Compat_WP_Offload_S3();

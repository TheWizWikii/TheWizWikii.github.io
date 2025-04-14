<?php
/**
 * ET_Builder_Module_Helper_Media class file.
 *
 * @class   ET_Builder_Module_Helper_Media
 * @package Divi\Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Module_Helper_Media.
 *
 * Module helpers for media (image).
 *
 * @since 4.6.4
 */
class ET_Builder_Module_Helper_Media {
	/**
	 * Return instance of current class.
	 *
	 * @return ET_Builder_Module_Helper_Media
	 */
	public static function instance() {
		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	/**
	 * Get image attachment class.
	 *
	 * - wp-image-{$id}
	 *   Add `wp-image-{$id}` class to let `wp_filter_content_tags()` fill in missing
	 *   height and width attributes on the image. Those attributes are required to add
	 *   loading "lazy" attribute on the image. WP doesn't have specific method to only
	 *   generate this class. It's included in get_image_tag() to generate image tags.
	 *
	 * @since 4.6.4
	 *
	 * @param array   $attrs         All module attributes.
	 * @param string  $source_key    Key of image source.
	 * @param integer $attachment_id Attachment ID. Optional.
	 *
	 * @return string
	 */
	public function get_image_attachment_class( $attrs, $source_key, $attachment_id = 0 ) {
		$attachment_class = '';

		// 1.a. Find attachment ID by URL. Skip if the source key is empty.
		if ( ! empty( $source_key ) ) {
			$attachment_src = et_()->array_get( $attrs, $source_key, '' );
			$attachment_id  = et_get_attachment_id_by_url( $attachment_src );
		}

		// 1.b. Generate attachment ID class.
		if ( $attachment_id > 0 ) {
			$attachment_class = "wp-image-{$attachment_id}";
		}

		return $attachment_class;
	}
}

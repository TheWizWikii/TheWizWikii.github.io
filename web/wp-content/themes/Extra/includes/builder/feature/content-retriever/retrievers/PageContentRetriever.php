<?php
/**
 * Page Content Retriever is used to get Page related Contents from WordPress.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Feature\ContentRetriever;

/**
 * PageContentRetriever Trait.
 */
trait PageContentRetriever {

	/**
	 * Holds a Cache reference for every page content retrieved.
	 *
	 * @var array
	 */
	private $_cache = [];

	/**
	 * Gets the entire page content, including TB Header, TB Body Layout, Post Content and TB Footer.
	 *
	 * Parameter $post must be given as a variable, since it is passed by reference.
	 * If $post is not given, It then uses global $post if available.
	 *
	 * @since 4.11.0
	 * @since 4.14.5 Return empty string on failure instead of null.
	 *
	 * @param  WP_Post|int $post Optional. WP_Post instance or Post ID. Default null.
	 *
	 * @return string Empty string on failure.
	 */
	public function get_entire_page_content( $post = null ) {

		/**
		 * Validation Checks.
		 */
		$is_using_global_post = false;

		if ( empty( $post ) && isset( $GLOBALS['post'] ) ) {
			$post                 = $GLOBALS['post'];
			$is_using_global_post = true;
		}

		if ( $post instanceof \WP_Post ) {
			$wp_post = $post;
		} else {
			$wp_post = \WP_Post::get_instance( $post );
		}

		// Return empty string on failure because the return value is mostly used along with
		// PHP PCRE or String functions that require non-nullable value.
		if ( ! $wp_post ) {
			return '';
		}

		/**
		 * Core mechanics for retrieving content.
		 */
		if ( $this->_cache && isset( $this->_cache[ $wp_post->ID ] ) ) {
			return $this->_cache[ $wp_post->ID ];
		}

		if ( $is_using_global_post ) {
			$layouts = et_theme_builder_get_template_layouts();
		} else {
			$layouts = et_theme_builder_get_template_layouts( \ET_Theme_Builder_Request::from_post( $wp_post->ID ) );
		}

		$entire_page_content = '';
		$enabled_layout_ids  = [
			ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE => null,
			ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE   => null,
			'content'                                => 'content',
			ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE => null,
		];

		foreach ( $layouts as $key => $layout ) {
			$is_layout_enabled          = isset( $layout['enabled'], $layout['override'] ) && true === $layout['enabled'] && true === $layout['override'];
			$enabled_layout_ids[ $key ] = ( array_key_exists( $key, $enabled_layout_ids ) && $is_layout_enabled ) ? $layout['id'] : null;
		}

		$enabled_layout_ids = array_filter( $enabled_layout_ids );

		/**
		 * $enabled_layout_ids will be in the following order, (assuming each are present):
		 * header, body, footer.
		 * We're intentionally adding the post content so that it's appended
		 * right after the body layout, making the final order of $entire_page_content:
		 * header, body, post content, footer.
		 * They need to be in order for Critical CSS. Otherwise we don't know what
		 * content comes first and is above the fold.
		 */
		foreach ( $enabled_layout_ids as $key => $layout_id ) {
			if ( 'content' === $layout_id ) {
				$entire_page_content .= $wp_post->post_content;
			} else {
				$layout               = get_post( $layout_id );
				$entire_page_content .= $layout->post_content;
			}
		}

		$this->_cache = array_replace( $this->_cache, [ $wp_post->ID => $entire_page_content ] );

		return $entire_page_content;
	}

}

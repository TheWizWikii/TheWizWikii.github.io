<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the Rank Math SEO plugin.
 *
 * @since 4.4.2
 *
 * @link https://wordpress.org/plugins/seo-by-rank-math/
 */
class ET_Builder_Plugin_Compat_Rank_Math_SEO extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.4.2
	 */
	public function __construct() {
		$this->plugin_id = 'seo-by-rank-math/rank-math.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.4.2
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'rank_math/sitemap/urlimages', array( $this, 'get_module_images' ), 10, 2 );
	}

	/**
	 * Add Divi builder module's images to Rank Math sitemap.
	 *
	 * @since 4.4.2
	 *
	 * @param  array $images  Existing images.
	 * @param  int   $post_id
	 * @return array
	 */
	public function get_module_images( $images, $post_id ) {
		$post = get_post( absint( $post_id ) );
		if ( ! $post ) {
			return $images;
		}

		// All Divi modules with image. And the image is generated into img tag, not
		// as background or overlay. Mostly, those modules have alt field setting.
		$modules_with_image = array(
			'et_pb_image'            => true,
			'et_pb_fullwidth_image'  => true,
			'et_pb_blurb'            => array(
				'src' => 'image',
			),
			'et_pb_team_member'      => array(
				'src' => 'image_url',
			),
			'et_pb_menu'             => array(
				'src' => 'logo',
			),
			'et_pb_fullwidth_menu'   => array(
				'src' => 'logo',
			),
			'et_pb_slide'            => array(
				'src' => 'image',
			),
			'et_pb_fullwidth_header' => array(
				'logo'   => array(
					'src'   => 'logo_image_url',
					'title' => 'logo_title',
					'alt'   => 'logo_alt_text',
				),
				'header' => array(
					'src'   => 'header_image_url',
					'title' => 'image_title',
					'alt'   => 'image_alt_text',
				),
			),
		);

		foreach ( $modules_with_image as $module_name => $module_attrs ) {
			// Find all modules shortcodes with image from the content.
			$modules = $this->_get_module_shortcode( $post->post_content, $module_name );
			if ( empty( $modules ) ) {
				continue;
			}

			foreach ( $modules as $module ) {
				// Don't add if the image and its attributes empty.
				$new_images = $this->_get_image_attrs( $module, $module_attrs );
				if ( ! empty( $new_images ) ) {
					$images = array_merge( $images, $new_images );
				}
			}
		}

		// Gallery modules is different with the other modules with image because
		// the images are saved as ID. So, we need to fetch the IDs first then loop
		// through the IDs to get source, title text, and alt text.
		$galleries = $this->_get_module_shortcode( $post->post_content, 'et_pb_gallery' );
		if ( ! empty( $galleries ) ) {
			foreach ( $galleries as $gallery ) {
				// Find gallery ids, if it doesn't exist, skip the process.
				$gallery_ids = $this->_get_image_attr( $gallery, 'gallery_ids' );
				$gallery_ids = explode( ',', $gallery_ids );
				if ( empty( $gallery_ids ) ) {
					continue;
				}

				foreach ( $gallery_ids as $gallery_id ) {
					$image_attrs = array();

					// Find image source, if it doesn't exist, skip the process.
					$src_values = wp_get_attachment_image_src( $gallery_id, 'full' );
					$src_value  = et_()->array_get( $src_values, '0' );
					if ( empty( $src_value ) ) {
						continue;
					}
					$image_attrs['src'] = esc_url( $src_value );

					// Find image title text. In our Gallery module, alt text uses the
					// same title text from the attachment. Keep it here for consistency.
					$title_value = get_the_title( $gallery_id );
					if ( ! empty( $title_value ) ) {
						$image_attrs['title'] = esc_attr( $title_value );
						$image_attrs['alt']   = esc_attr( $title_value );
					}

					$images[] = $image_attrs;
				}
			}
		}

		return $images;
	}

	/**
	 * Get module shortcode from post content.
	 *
	 * @since 4.4.2
	 *
	 * @param  string $content
	 * @param  string $module
	 * @return string
	 */
	private function _get_module_shortcode( $content, $module ) {
		preg_match_all( '/\[' . $module . '[^]]*]/', $content, $module_values );
		return et_()->array_get( $module_values, '0' );
	}

	/**
	 * Get image attributes value and collect them as an array. Attributes list:
	 * source URL, title text, and alternative text.
	 *
	 * @since 4.4.2
	 *
	 * @param  string $module
	 * @param  array  $module_attrs
	 * @return array
	 */
	private function _get_image_attrs( $module, $module_attrs ) {
		$images = array();
		$types  = true !== $module_attrs && ! isset( $module_attrs['src'] ) ? $module_attrs : array( $module_attrs );

		foreach ( $types as $type_attrs ) {
			$image_attrs = array();

			// Find image source, if it doesn't exist, skip the process.
			$src_attr  = et_()->array_get( $type_attrs, 'src', 'src' );
			$src_value = $this->_get_image_attr( $module, $src_attr );
			if ( empty( $src_value ) ) {
				continue;
			}
			$image_attrs['src'] = esc_url( $src_value );

			$title_attr  = et_()->array_get( $type_attrs, 'title', 'title_text' );
			$title_value = $this->_get_image_attr( $module, $title_attr );
			if ( ! empty( $title_value ) ) {
				$image_attrs['title'] = esc_attr( $title_value );
			}

			$alt_attr  = et_()->array_get( $type_attrs, 'alt', 'alt' );
			$alt_value = $this->_get_image_attr( $module, $alt_attr );
			if ( ! empty( $alt_value ) ) {
				$image_attrs['alt'] = esc_attr( $alt_value );
			}

			$images[] = $image_attrs;
		}

		return $images;
	}

	/**
	 * Get image attribute value from the module shortcode.
	 *
	 * @since 4.4.2
	 *
	 * @param  string $content
	 * @param  string $attr
	 * @return string
	 */
	private function _get_image_attr( $content, $attr ) {
		preg_match( '/' . $attr . '="([^"]*)"/', $content, $attr_values );
		return et_()->array_get( $attr_values, '1' );
	}
}

new ET_Builder_Plugin_Compat_Rank_Math_SEO();

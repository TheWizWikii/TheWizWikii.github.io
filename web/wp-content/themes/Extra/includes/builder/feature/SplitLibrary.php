<?php
/**
 * Divi Split Library.
 *
 * @package Builder
 */

/**
 * Core class used to Split library item.
 */
class ET_Builder_Split_Library {
	/**
	 * Instance of `ET_Builder_Split_Library`.
	 *
	 * @var ET_Builder_Split_Library
	 */
	private static $_instance;

	/**
	 * Get the class instance.
	 *
	 * @since 4.20.3
	 *
	 * @return ET_Builder_Split_Library
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * ET_Builder_Split_Library constructor.
	 */
	public function __construct() {
		$this->_register_ajax_callbacks();
	}

	/**
	 * Registers the Split Library's AJAX callbacks.
	 *
	 * @since 4.20.3
	 */
	protected function _register_ajax_callbacks() {
		add_action( 'wp_ajax_et_builder_split_library_item', array( $this, 'split_library_item' ) );
	}

	/**
	 * Split library item based on split type
	 */
	public function split_library_item() {
		et_core_security_check( 'edit_posts', 'et_builder_split_library_item', 'et_cloud_nonce' );

		$id         = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : false;
		$prefix     = isset( $_POST['itemName'] ) ? sanitize_text_field( $_POST['itemName'] ) : false;
		$to_cloud   = isset( $_POST['cloud'] ) ? sanitize_text_field( $_POST['cloud'] ) : 'off';
		$split_type = isset( $_POST['updateType'] ) ? sanitize_text_field( $_POST['updateType'] ) : false;
		$origin     = isset( $_POST['actionOrigin'] ) ? sanitize_text_field( $_POST['actionOrigin'] ) : '';

		if ( ! $id || ! $split_type || ! $prefix ) {
			wp_send_json_error();
		}

		if ( ! in_array( $split_type, array( 'split_layout', 'split_section', 'split_row' ), true ) ) {
			wp_send_json_error();
		}

		$cloud_content = isset( $_POST['content'] ) ? $_POST['content'] : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['content'] is an array, it's value sanitization is done at the time of accessing value.

		if ( $cloud_content ) {
			$item_content = wp_unslash( reset( $cloud_content['data'] ) );
		} else {
			$post         = get_post( $id );
			$item_content = $post->post_content;
		}

		switch ( $split_type ) {
			case 'split_layout':
				$pattern     = '/\[et_pb_section .+?]?.+?\[\/et_pb_section]/s';
				$layout_type = 'section';
				break;

			case 'split_section':
				$pattern     = '/\[et_pb_row(_inner)? .+?].+?\[\/et_pb_row(_inner)?]/s';
				$layout_type = 'row';
				break;

			case 'split_row':
				$pattern     = '/\[(et_pb_(?!section|row|column).+?)\s.+?]?\[\/\1]/s';
				$layout_type = 'module';
				break;
		}

		// Get the intented content array based on split type pattern.
		preg_match_all( $pattern, $item_content, $matches );

		$args = array(
			'split_type'           => $split_type,
			'layout_type'          => $layout_type,
			'layout_selected_cats' => isset( $_POST['itemCategories'] ) ? array_map( 'sanitize_text_field', $_POST['itemCategories'] ) : array(),
			'layout_selected_tags' => isset( $_POST['itemTags'] ) ? array_map( 'sanitize_text_field', $_POST['itemTags'] ) : array(),
			'built_for_post_type'  => 'page',
			'layout_new_cat'       => isset( $_POST['newCategoryName'] ) ? sanitize_text_field( $_POST['newCategoryName'] ) : '',
			'layout_new_tag'       => isset( $_POST['newTagName'] ) ? sanitize_text_field( $_POST['newTagName'] ) : '',
			'columns_layout'       => '0',
			'module_type'          => 'et_pb_unknown',
			'layout_scope'         => isset( $_POST['global'] ) && ( 'on' === $_POST['global'] ) ? 'global' : 'not_global',
			'module_width'         => 'regular',
		);

		$layouts   = array();
		$processed = false;

		foreach ( $matches[0] as $key => $content ) {
			$title = $prefix;

			if ( 'split_row' === $split_type && 'save_modal' === $origin ) {
				$module_name = explode( ' ', $content )[0];
				$module_name = str_replace( '[et_pb_', '', $module_name );
				$module_name = ucfirst( str_replace( '_', ' ', $module_name ) );
				$title       = str_replace( '%module_type%', $module_name, $prefix );
			}

			$args['layout_name'] = $title . ' ' . ( ++$key );

			$content = $this->_get_content_with_type( $content, $layout_type );

			if ( 'on' === $to_cloud ) {
				if ( $cloud_content ) {
					/* From cloud to cloud */
					$layouts[] = $this->_get_cloud_to_cloud_formatted_data( $cloud_content, $content, $args );
				} else {
					/* From local to cloud */
					$layouts[] = $this->_get_local_to_cloud_formatted_data( $content, $args );
				}
			} else {
				if ( $cloud_content ) {
					/* From cloud to local */
					$cloud_content['data']['1'] = $content;

					$layouts[] = $this->_get_cloud_to_local_formatted_data( $cloud_content, $content, $args );

					// We only need to insert these data once into the database.
					unset( $cloud_content['presets'] );
					unset( $cloud_content['global_colors'] );
					unset( $cloud_content['images'] );
					unset( $cloud_content['thumbnails'] );
				} else {
					/* From local to Local */
					$args['layout_content']       = $content;
					$args['layout_selected_cats'] = is_array( $args['layout_selected_cats'] ) ? implode( ',', $args['layout_selected_cats'] ) : '';
					$args['layout_selected_tags'] = is_array( $args['layout_selected_tags'] ) ? implode( ',', $args['layout_selected_tags'] ) : '';

					$new_saved = json_decode( et_pb_submit_layout( $args ) );

					// Only need to process once because all the split item's taxonomies are the same.
					if ( ! $processed ) {
						$layouts[] = [
							'newId'         => $new_saved->post_id,
							'categories'    => $args['layout_selected_cats'],
							'tags'          => $args['layout_selected_tags'],
							'updated_terms' => $this->_get_all_updated_terms(),
						];
						$processed = true;
					}
				}
			}
		}

		wp_send_json_success( $layouts );
	}

	/**
	 * Get content with type.
	 *
	 * @since 4.20.3
	 *
	 * @param string $content Content to be processed.
	 * @param string $type    Type of the content.
	 */
	private function _get_content_with_type( $content, $type ) {
		$pattern = '/^(\[\w+\s)/';
		$replace = '$0template_type="' . $type . '" '; // e.g. [et_pb_row template_type="row" ...].

		return preg_replace( $pattern, $replace, $content );
	}

	/**
	 * Get formatted data for cloud item split to cloud.
	 *
	 * @since 4.20.3
	 *
	 * @param array  $cloud_content Cloud Item data.
	 * @param string $content       Shortcode after split cloud item.
	 * @param array  $assoc_data    Related data after split cloud item.
	 */
	private function _get_cloud_to_cloud_formatted_data( $cloud_content, $content, $assoc_data ) {
		$data = $this->_get_common_cloud_formatted_data( $content, $assoc_data );

		$images        = array();
		$presets       = array();
		$global_colors = array();

		if ( ! empty( $cloud_content['images'] ) ) {
			foreach ( $cloud_content['images'] as $url => $img ) {
				// Use strpos because str_contains() is not present in PHP version 7.4 or earlier.
				if ( strpos( $content, $url ) !== false ) {
					$images[ $url ] = $img;
				}
			}
		}

		if ( ! empty( $cloud_content['presets'] ) ) {
			foreach ( $cloud_content['presets'] as $module => $preset ) {
				// Use strpos because str_contains() is not present in PHP version 7.4 or earlier.
				if ( strpos( $content, $module ) !== false ) {
					$presets[ $module ] = $preset;
				}
			}
		}

		if ( ! empty( $cloud_content['global_colors'] ) ) {
			foreach ( $cloud_content['global_colors'] as $key => $global_color ) {
				// Use strpos because str_contains() is not present in PHP version 7.4 or earlier.
				if ( strpos( $content, $global_color[0] ) !== false ) {
					$global_colors[ $key ] = $global_color;
				}
			}
		}

		$data['images']        = $images;
		$data['presets']       = $presets;
		$data['global_colors'] = $global_colors;

		return $data;
	}

	/**
	 * Get formatted data for local item split to cloud.
	 *
	 * @since 4.20.3
	 *
	 * @param string $content    Shortcode after split cloud item.
	 * @param array  $assoc_data Related data after split cloud item.
	 *
	 * @return array
	 */
	private function _get_local_to_cloud_formatted_data( $content, $assoc_data ) {
		return $this->_get_common_cloud_formatted_data( $content, $assoc_data );
	}

	/**
	 * Get formatted data for cloud item split to local.
	 *
	 * @since 4.20.3
	 *
	 * @param array  $cloud_content Cloud Item data.
	 * @param string $content       Shortcode after split cloud item.
	 * @param array  $assoc_data    Related data after split cloud item.
	 *
	 * @return array
	 */
	private function _get_cloud_to_local_formatted_data( $cloud_content, $content, $assoc_data ) {
		return array(
			'itemName'        => $assoc_data['layout_name'],
			'itemCategories'  => $assoc_data['layout_selected_cats'],
			'itemTags'        => $assoc_data['layout_selected_tags'],
			'newCategoryName' => $assoc_data['layout_new_cat'],
			'newTagName'      => $assoc_data['layout_new_tag'],
			'cloud'           => 'off',
			'global'          => $assoc_data['layout_scope'],
			'layoutType'      => $assoc_data['layout_type'],
			'updateType'      => $assoc_data['split_type'],
			'content'         => $cloud_content,
			'shortcode'       => wp_json_encode( $content ),
		);
	}

	/**
	 * Get common formatted data for cloud item.
	 *
	 * @since 4.20.3
	 *
	 * @param string $content    Shortcode after split cloud item.
	 * @param array  $assoc_data Related data after split cloud item.
	 *
	 * @return array
	 */
	private function _get_common_cloud_formatted_data( $content, $assoc_data ) {
		$data = array(
			'post_title'   => $assoc_data['layout_name'],
			'post_content' => $content,
			'terms'        => array(
				array(
					'name'     => $assoc_data['layout_type'],
					'slug'     => $assoc_data['layout_type'],
					'taxonomy' => 'layout_type',
				),
			),
		);

		foreach ( $assoc_data['layout_selected_cats'] as $category ) {
			$data['terms'][] = array(
				'name'     => $category,
				'slug'     => $category,
				'taxonomy' => 'layout_category',
			);
		}

		foreach ( $assoc_data['layout_selected_tags'] as $tag ) {
			$data['terms'][] = array(
				'name'     => $tag,
				'slug'     => $tag,
				'taxonomy' => 'layout_tag',
			);
		}

		return $data;
	}

	/**
	 * Get all the updated terms.
	 *
	 * @since 4.20.3
	 *
	 * @return array
	 */
	private function _get_all_updated_terms() {
		$updated_terms = array();

		foreach ( [ 'layout_category', 'layout_tag' ] as $taxonomy ) {
			$raw_terms_array   = get_terms( $taxonomy, array( 'hide_empty' => false ) );
			$clean_terms_array = array();

			if ( is_array( $raw_terms_array ) && ! empty( $raw_terms_array ) ) {
				foreach ( $raw_terms_array as $term ) {
					$clean_terms_array[] = array(
						'name' => html_entity_decode( $term->name ),
						'id'   => $term->term_id,
						'slug' => $term->slug,
					);
				}
			}

			$updated_terms[ $taxonomy ] = $clean_terms_array;
		}

		return $updated_terms;
	}
}

ET_Builder_Split_Library::instance();

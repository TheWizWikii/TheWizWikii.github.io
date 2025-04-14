<?php
/**
 * Local Library API.
 *
 * @since ??
 *
 * @package Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ET_Theme_Options_Library_Local utility class.
 *
 * Item can be a layout, a template, a theme option, a code snippet, etc.
 *
 * @since ??
 *
 * @return void
 */
class ET_Theme_Options_Library_Local extends ET_Item_Library_Local {
	/**
	 * Gets the class instance.
	 *
	 * @since ??
	 *
	 * @return ET_Item_Library_Local
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->post_type = ET_THEME_OPTIONS_POST_TYPE;

		$this->exceptional_processes = array(
			'duplicate',
			'duplicate_and_delete',
		);
	}

	/**
	 * Gets the library items.
	 *
	 * @param string $item_type Item type.
	 * @return array
	 */
	public function get_library_items( $item_type ) {
		$_                        = ET_Core_Data_Utils::instance();
		$theme_options_items      = ET_Post_Type_Theme_Options::instance();
		$theme_options_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();
		$theme_options_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();

		$item_categories = [];
		$item_tags       = [];
		$items           = [];
		$index           = 0;

		$query_posts = $theme_options_items
			->query()
			->run(
				array(
					'post_status' => array( 'publish', 'trash' ),
					'orderby'     => 'name',
					'fields'      => 'ids',
				)
			);

		$post_ids = is_array( $query_posts ) ? $query_posts : array( $query_posts );

		foreach ( $post_ids as $post_id ) {
			$item = new stdClass();
			$post = get_post( $post_id );

			$item->id    = $post->ID;
			$item->index = $index;
			$item->date  = $post->post_date;

			$title = html_entity_decode( $post->post_title );

			// check if current user can edit library item.
			$can_edit_post = current_user_can( 'edit_post', $item->id );

			if ( $title ) {
				// Remove periods since we use dot notation to retrieve translation.
				$title = str_replace( '.', '', $title );

				$item->name = et_core_intentionally_unescaped( $title, 'react_jsx' );
			}

			$built_for = get_post_meta( $item->id, '_built_for', true );

			$item->slug = $post->post_name;
			$item->url  = esc_url( wp_make_link_relative( get_permalink( $post ) ) );

			$item->short_name   = '';
			$item->builtFor     = $built_for && '' !== $built_for ? $built_for : 'Divi'; // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- This is valid format for the property in the Cloud App.
			$item->description  = '';
			$item->is_favorite  = $theme_options_items->is_favorite( $item->id );
			$item->isTrash      = 'trash' === $post->post_status; // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- This is valid format for the property in the Cloud App.
			$item->isReadOnly   = ! $can_edit_post; // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- This is valid format for the property in the Cloud App.
			$item->categories   = array();
			$item->category_ids = array();
			$item->tags         = array();
			$item->tag_ids      = array();

			$this->process_item_taxonomy(
				$post,
				$item,
				$index,
				$item_categories,
				$theme_options_categories->name,
				'category'
			);

			$this->process_item_taxonomy(
				$post,
				$item,
				$index,
				$item_tags,
				$theme_options_tags->name,
				'tag'
			);

			$items[] = $item;

			$index++;
		}

		return [
			'categories' => $this->get_processed_terms( $theme_options_categories->name ),
			'tags'       => $this->get_processed_terms( $theme_options_tags->name ),
			'items'      => $items,
		];
	}

	/**
	 * Performs item exceptional updates.
	 *
	 * @param array $payload Payload.
	 * @param array $updated_data Updated data.
	 *
	 * @since ??
	 *
	 * @return array
	 */
	private function _perform_item_exceptional_updates( $payload, $updated_data ) {
		if ( empty( $payload['item_id'] ) || empty( $payload['update_details'] ) ) {
			return false;
		}

		$update_details = $payload['update_details'];

		if ( empty( $update_details['updateType'] ) ) {
			return false;
		}

		$item_id               = absint( $payload['item_id'] );
		$update_type           = sanitize_text_field( $update_details['updateType'] );
		$item_name             = isset( $update_details['itemName'] ) ? sanitize_text_field( $update_details['itemName'] ) : '';
		$et_builder_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$et_builder_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();

		switch ( $update_type ) {
			case 'duplicate':
			case 'duplicate_and_delete':
				if ( isset( $update_details['content'] ) ) {
					$content = $update_details['content'];
				} else {
					$content = get_the_content( null, false, $item_id );
				}

				if ( is_array( $content ) ) {
					$content = wp_json_encode( $content );
				}

				$new_item = array(
					'post_title'   => $item_name,
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => $this->post_type,
					'tax_input'    => array(
						$et_builder_categories->name => $updated_data['categories'],
						$et_builder_tags->name       => $updated_data['tags'],
					),
				);

				$updated_data['newItem'] = wp_insert_post( $new_item );
				break;
		}

		$updated_data['updateType'] = $update_type;

		return $updated_data;
	}

	/**
	 * Updates the library item.
	 *
	 * @param array $payload Payload.
	 *
	 * @return array
	 */
	public function perform_item_update( $payload ) {
		$updated_data = $this->_perform_item_common_updates( $payload );

		if ( ! empty( $this->exceptional_processes ) ) {
			$updated_data = $this->_perform_item_exceptional_updates( $payload, $updated_data );
		}

		return $updated_data;
	}
}

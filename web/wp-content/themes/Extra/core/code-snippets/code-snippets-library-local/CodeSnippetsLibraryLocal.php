<?php
/**
 * Local Library API.
 *
 * @since 4.21.0
 *
 * @package Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ET_Code_Snippets_Library_Local utility class.
 *
 * Item can be a layout, a template, a theme option, a code snippet, etc.
 *
 * @since 4.21.0
 *
 * @return void
 */
class ET_Code_Snippets_Library_Local extends ET_Item_Library_Local {
	/**
	 * Gets the class instance.
	 *
	 * @since 4.21.0
	 *
	 * @return ET_Code_Snippets_Library_Local
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
		$this->post_type = ET_CODE_SNIPPET_POST_TYPE;

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
		$_                       = ET_Core_Data_Utils::instance();
		$code_snippet_items      = ET_Builder_Post_Type_Code_Snippet::instance();
		$code_snippet_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();
		$code_snippet_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$code_snippet_types      = ET_Builder_Post_Taxonomy_CodeSnippetType::instance();

		$item_categories = [];
		$item_tags       = [];
		$items           = [];
		$index           = 0;

		$query_posts = $code_snippet_items
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
			$types       = wp_get_post_terms( $item->id, $code_snippet_types->name );

			if ( ! $types ) {
				continue;
			}

			$item->type = $types[0]->slug;

			if ( $item_type !== $item->type ) {
				continue;
			}

			$title = html_entity_decode( $post->post_title );

			// check if current user can edit library item.
			$can_edit_post = current_user_can( 'edit_post', $item->id );

			if ( $title ) {
				// Remove periods since we use dot notation to retrieve translation.
				$title = str_replace( '.', '', $title );

				$item->name = et_core_intentionally_unescaped( $title, 'react_jsx' );
			}

			$item->slug = $post->post_name;
			$item->url  = esc_url( wp_make_link_relative( get_permalink( $post ) ) );

			$item->short_name   = '';
			$item->is_default   = get_post_meta( $item->id, '_et_default', true );
			$item->description  = '';
			$item->is_favorite  = $code_snippet_items->is_favorite( $item->id );
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
				$code_snippet_categories->name,
				'category'
			);

			$this->process_item_taxonomy(
				$post,
				$item,
				$index,
				$item_tags,
				$code_snippet_tags->name,
				'tag'
			);

			$items[] = $item;

			$index++;
		}

		return [
			'categories' => $this->get_processed_terms( $code_snippet_categories->name ),
			'tags'       => $this->get_processed_terms( $code_snippet_tags->name ),
			'items'      => $items,
		];
	}

	/**
	 * Performs item exceptional updates.
	 *
	 * @param array $payload Payload.
	 * @param array $updated_data Updated data.
	 *
	 * @since 4.21.0
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
		$code_snippets_type    = ET_Builder_Post_Taxonomy_CodeSnippetType::instance();

		if ( in_array( $update_type, [ 'duplicate', 'duplicate_and_delete' ], true ) ) {
			if ( isset( $update_details['content'] ) ) {
				$content      = isset( $update_details['content']['data'] ) ? $update_details['content']['data'] : '';
				$snippet_type = isset( $update_details['content']['snippet_type'] ) ? sanitize_text_field( $update_details['content']['snippet_type'] ) : 'et_code_snippet_html_js';
			} else {
				$content      = get_the_content( null, false, $item_id );
				$snippet_type = wp_get_post_terms( $item_id, $code_snippets_type->name, array( 'fields' => 'names' ) );
				$snippet_type = is_wp_error( $snippet_type ) || '' === $snippet_type ? 'et_code_snippet_html_js' : sanitize_text_field( $snippet_type[0] );
			}

			$new_item = array(
				'post_title'   => $item_name,
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => $this->post_type,
				'tax_input'    => array(
					$et_builder_categories->name => $updated_data['categories'],
					$et_builder_tags->name       => $updated_data['tags'],
					$code_snippets_type->name    => $snippet_type,
				),
			);

			$updated_data['newItem'] = wp_insert_post( $new_item );
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

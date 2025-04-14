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
 * ET_Item_Library_Local utility class.
 *
 * Item can be a layout, a template, a theme option, a code snippet, etc.
 *
 * @since 4.21.0
 *
 * @return void
 */
abstract class ET_Item_Library_Local {
	/**
	 * Instance.
	 *
	 * @var ET_Item_Library_Local Class instance.
	 */
	public static $_instance;

	/**
	 * Post Type.
	 *
	 * @var string Post Type.
	 */
	public $post_type;

	/**
	 * Ignore Process.
	 *
	 * @var array Ignore Process.
	 */
	public $exceptional_processes = array();

	/**
	 * Gets the library items.
	 *
	 * @param string $item_type Item Type.
	 *
	 * @return Array An array of items data.
	 */
	abstract public function get_library_items( $item_type );

	/**
	 * Remove, Rename or Add new Category/Tag into local library.
	 *
	 * @param array $payload Array with the update details.
	 *
	 * @return array
	 */
	public function perform_terms_update( $payload ) {
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die();
		}

		$new_terms = array();

		foreach ( $payload as $single_item ) {
			$filter_type = $single_item['filterType'];
			$taxonomy    = 'tags' === $single_item['filterType'] ? 'layout_tag' : 'layout_category';

			switch ( $single_item['updateType'] ) {
				case 'remove':
					$term_id = (int) $single_item['id'];
					wp_delete_term( $term_id, $taxonomy );
					break;
				case 'rename':
					$term_id  = (int) $single_item['id'];
					$new_name = (string) $single_item['newName'];

					if ( '' !== $new_name ) {
						$updated_term_data = wp_update_term( $term_id, $taxonomy, array( 'name' => $new_name ) );

						if ( ! is_wp_error( $updated_term_data ) ) {
							$new_terms[] = array(
								'name'     => $new_name,
								'id'       => $updated_term_data['term_id'],
								'location' => 'local',
							);
						}
					}
					break;
				case 'add':
					$term_name     = (string) $single_item['id'];
					$new_term_data = wp_insert_term( $term_name, $taxonomy );

					if ( ! is_wp_error( $new_term_data ) ) {
						$new_terms[] = array(
							'name'     => $term_name,
							'id'       => $new_term_data['term_id'],
							'location' => 'local',
						);
					}
					break;
			}
		}

		return array(
			'newFilters'        => $new_terms,
			'filterType'        => $filter_type,
			'localLibraryTerms' => [
				'layout_category' => $this->get_processed_terms( 'layout_category' ),
				'layout_tag'      => $this->get_processed_terms( 'layout_tag' ),
			],
		);
	}

	/**
	 * Gets the terms list and processes it into desired format.
	 *
	 * @since 4.18.0
	 *
	 * @param string $term_name Term Name.
	 *
	 * @return array $terms_by_id
	 */
	public function get_processed_terms( $term_name ) {
		$terms       = get_terms( $term_name, array( 'hide_empty' => false ) );
		$terms_by_id = array();

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		foreach ( $terms as $term ) {
			$term_id = $term->term_id;

			$terms_by_id[ $term_id ]['id']    = $term_id;
			$terms_by_id[ $term_id ]['name']  = $term->name;
			$terms_by_id[ $term_id ]['slug']  = $term->slug;
			$terms_by_id[ $term_id ]['count'] = $term->count;
		}

		return $terms_by_id;
	}

	/**
	 * Processes item taxonomies for inclusion in the library UI items data.
	 *
	 * @since 4.18.0
	 *
	 * @param WP_POST $post          Unprocessed item.
	 * @param object  $item          Currently processing item.
	 * @param int     $index         The item's index position.
	 * @param array[] $item_terms    Processed items.
	 * @param string  $taxonomy_name Item name.
	 * @param string  $type          Item type.
	 *
	 * @return void
	 */
	public function process_item_taxonomy( $post, $item, $index, &$item_terms, $taxonomy_name, $type ) {
		$terms = wp_get_post_terms( $post->ID, $taxonomy_name );

		if ( ! $terms ) {
			if ( 'category' === $type ) {
				$item->category_slug = 'uncategorized';
			}

			return;
		}

		foreach ( $terms as $term ) {
			$term_name = et_core_intentionally_unescaped( $term->name, 'react_jsx' );

			if ( ! isset( $item_terms[ $term->term_id ] ) ) {
				$item_terms[ $term->term_id ] = array(
					'id'    => $term->term_id,
					'name'  => $term_name,
					'slug'  => $term->slug,
					'items' => array(),
				);
			}

			$item_terms[ $term->term_id ]['items'][] = $index;

			if ( 'category' === $type ) {
				$item->categories[] = $term_name;
			} else {
				$item->tags[] = $term_name;
			}

			$item->{$type . '_ids'}[] = $term->term_id;

			if ( ! isset( $item->{$type . '_slug'} ) ) {
				$item->{$type . '_slug'} = $term->slug;
			}

			$id = get_post_meta( $post->ID, "_primary_{$taxonomy_name}", true );

			if ( $id ) {
				// $id is a string, $term->term_id is an int.
				if ( $id === $term->term_id ) {
					// This is the primary term (used in the item URL).
					$item->{$type . '_slug'} = $term->slug;
				}
			}
		}
	}

	/**
	 * Update library item. Support following updates:
	 * - Duplicate
	 * - Rename
	 * - Toggle Favorite status
	 * - Delete
	 * - Delete Permanently
	 * - Restore
	 *
	 * @since 4.21.0
	 *
	 * @param array $payload Array with the id and update details.
	 *
	 * @return array Updated item details
	 */
	protected function _perform_item_common_updates( $payload ) {
		if ( empty( $payload['item_id'] ) || empty( $payload['update_details'] ) ) {
			return false;
		}

		$update_details = $payload['update_details'];

		if ( empty( $update_details['updateType'] ) ) {
			return false;
		}

		$et_builder_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$et_builder_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();

		$new_id          = 0;
		$item_id         = absint( $payload['item_id'] );
		$item_update     = array( 'ID' => $item_id );
		$update_type     = sanitize_text_field( $update_details['updateType'] );
		$item_name       = isset( $update_details['itemName'] ) ? sanitize_text_field( $update_details['itemName'] ) : '';
		$favorite_status = isset( $update_details['favoriteStatus'] ) && ( 'on' === $update_details['favoriteStatus'] ) ? 'favorite' : '';
		$categories      = isset( $update_details['itemCategories'] ) ? array_unique( array_map( 'absint', $update_details['itemCategories'] ) ) : array();
		$tags            = isset( $update_details['itemTags'] ) ? array_unique( array_map( 'absint', $update_details['itemTags'] ) ) : array();
		$post_type       = get_post_type( $item_id );

		if ( ! empty( $update_details['newCategoryName'] ) ) {
			$categories = $this->_create_and_get_all_item_terms(
				$update_details['newCategoryName'],
				$categories,
				$et_builder_categories->name
			);
		}

		if ( ! empty( $update_details['newTagName'] ) ) {
			$tags = $this->_create_and_get_all_item_terms(
				$update_details['newTagName'],
				$tags,
				$et_builder_tags->name
			);
		}

		if ( in_array( $update_type, $this->exceptional_processes, true ) ) {
			$update_type = 'default';
		}

		switch ( $update_type ) {
			case 'duplicate':
			case 'duplicate_and_delete':
				break;
			case 'rename':
				if ( ! current_user_can( 'edit_post', $item_id ) || $this->post_type !== $post_type ) {
					return;
				}

				if ( $item_name ) {
					$item_update['post_title'] = $item_name;
					wp_update_post( $item_update );
				}
				break;

			case 'toggle_fav':
				update_post_meta( $item_id, 'favorite_status', $favorite_status );
				break;

			case 'delete':
				if ( ! current_user_can( 'edit_post', $item_id ) || $this->post_type !== $post_type ) {
					return;
				}

				wp_trash_post( $item_id );
				break;

			case 'delete_permanently':
				if ( ! current_user_can( 'edit_post', $item_id ) || $this->post_type !== $post_type ) {
					return;
				}

				wp_delete_post( $item_id, true );
				break;

			case 'restore':
				if ( ! current_user_can( 'edit_post', $item_id ) || $this->post_type !== $post_type ) {
					return;
				}

				$publish_fn = function() {
					return 'publish';
				};

				// wp_untrash_post() restores the post to `draft` by default, we have to set `publish` status via filter.
				add_filter( 'wp_untrash_post_status', $publish_fn );

				wp_untrash_post( $item_id );

				remove_filter( 'wp_untrash_post_status', $publish_fn );
				break;

			case 'edit_cats':
				wp_set_object_terms( $item_id, $categories, $et_builder_categories->name );
				wp_set_object_terms( $item_id, $tags, $et_builder_tags->name );
				break;
		}

		// Continue with additional data.

		$processed_new_categories = array();
		$processed_new_tags       = array();

		$updated_categories = get_terms(
			array(
				'taxonomy'   => $et_builder_categories->name,
				'hide_empty' => false,
			)
		);

		$updated_tags = get_terms(
			array(
				'taxonomy'   => $et_builder_tags->name,
				'hide_empty' => false,
			)
		);

		if ( ! empty( $updated_categories ) ) {
			foreach ( $updated_categories as $single_category ) {
				$processed_new_categories[] = array(
					'id'       => $single_category->term_id,
					'name'     => $single_category->name,
					'count'    => $single_category->count,
					'location' => 'local',
				);
			}
		}

		if ( ! empty( $updated_tags ) ) {
			foreach ( $updated_tags as $single_tag ) {
				$processed_new_tags[] = array(
					'id'       => $single_tag->term_id,
					'name'     => $single_tag->name,
					'count'    => $single_tag->count,
					'location' => 'local',
				);
			}
		}

		return array(
			'updatedItem'  => $item_id,
			'newItem'      => $new_id,
			'updateType'   => $update_type,
			'categories'   => $categories,
			'tags'         => $tags,
			'updatedTerms' => array(
				'categories' => $processed_new_categories,
				'tags'       => $processed_new_tags,
			),
		);
	}

	/**
	 * Get all terms of an item and merge any newly passed IDs with the list.
	 *
	 * @since 4.19.0
	 *
	 * @param string $new_terms_list List of new terms.
	 * @param array  $taxonomies Taxonomies.
	 * @param string $taxonomy_name Taxonomy name.
	 *
	 * @return array
	 */
	private function _create_and_get_all_item_terms( $new_terms_list, $taxonomies, $taxonomy_name ) {
		$new_names_array = explode( ',', $new_terms_list );

		foreach ( $new_names_array as $new_name ) {
			if ( '' !== $new_name ) {
				$new_term = wp_insert_term( $new_name, $taxonomy_name );

				if ( ! is_wp_error( $new_term ) ) {
					$taxonomies[] = $new_term['term_id'];
				} elseif (
						! empty( $new_term->error_data ) &&
						! empty( $new_term->error_data['term_exists'] )
					) {
					$taxonomies[] = $new_term->error_data['term_exists'];
				}
			}
		}

		return $taxonomies;
	}

	/**
	 * Prepare Library Categories or Tags List.
	 *
	 * @param string $taxonomy Name of the taxonomy.
	 *
	 * @return array Clean Categories/Tags array.
	 **/
	public function get_formatted_library_terms( $taxonomy = 'layout_category' ) {
		$raw_terms_array       = apply_filters( 'et_pb_new_layout_cats_array', get_terms( $taxonomy, array( 'hide_empty' => false ) ) );
		$formatted_terms_array = array();

		if ( is_array( $raw_terms_array ) && ! empty( $raw_terms_array ) ) {
			foreach ( $raw_terms_array as $term ) {
				$formatted_terms_array[] = array(
					'name'  => et_core_intentionally_unescaped( html_entity_decode( $term->name ), 'react_jsx' ),
					'id'    => $term->term_id,
					'slug'  => $term->slug,
					'count' => $term->count,
				);
			}
		}

		return $formatted_terms_array;
	}
}

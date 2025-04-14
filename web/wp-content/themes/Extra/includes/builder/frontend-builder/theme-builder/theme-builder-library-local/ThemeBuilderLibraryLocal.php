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
 * ET_Theme_Builder_Library_Local utility class.
 *
 * Item can be a layout, a template, a theme option, a code snippet, etc.
 *
 * @since 4.21.0
 *
 * @return void
 */
class ET_Theme_Builder_Library_Local extends ET_Item_Library_Local {
	/**
	 * Gets the class instance.
	 *
	 * @since 4.21.0
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
		$this->post_type = ET_TB_ITEM_POST_TYPE;

		$this->exceptional_processes = array(
			'delete',
			'delete_permanently',
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
		$_                  = ET_Core_Data_Utils::instance();
		$tb_items           = ET_Builder_Post_Type_TBItem::instance();
		$tb_item_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();
		$tb_item_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$tb_item_types      = ET_Builder_Post_Taxonomy_TBItemType::instance();

		$item_categories = array();
		$item_tags       = array();
		$item_types      = array();
		$items           = array();
		$index           = 0;

		$set_template_flag = '1';

		$query_posts = $tb_items
			->query()
			->not()->with_meta( '_et_set_template', $set_template_flag )
			->run(
				array(
					'post_status' => array( 'publish', 'trash' ),
					'orderby'     => 'name',
					'fields'      => 'ids',
				)
			);

		$post_ids = $_->array_sort_by( is_array( $query_posts ) ? $query_posts : array( $query_posts ), 'post_name' );

		foreach ( $post_ids as $post_id ) {
			$item = new stdClass();
			$post = get_post( $post_id );

			$item->id    = $post->ID;
			$item->index = $index;
			$item->date  = $post->post_date;
			$types       = wp_get_post_terms( $item->id, $tb_item_types->name );

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
				str_replace( '.', '', $title );

				$item->name = et_core_intentionally_unescaped( $title, 'react_jsx' );
			}

			$item->slug = $post->post_name;
			$item->url  = esc_url( wp_make_link_relative( get_permalink( $post ) ) );

			$item->short_name   = '';
			$item->is_default   = get_post_meta( $item->id, '_et_default', true );
			$item->description  = et_theme_builder_library_get_item_description( $item->id, $item->is_default );
			$item->is_favorite  = $tb_items->is_favorite( $item->id );
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
				$tb_item_categories->name,
				'category'
			);

			$this->process_item_taxonomy(
				$post,
				$item,
				$index,
				$item_tags,
				$tb_item_tags->name,
				'tag'
			);

			$item->item_items = et_theme_builder_library_get_item_items( $item_type, $post->ID );

			$items[] = $item;

			$index++;
		}

		return array(
			'categories' => $this->get_processed_terms( $tb_item_categories->name ),
			'tags'       => $this->get_processed_terms( $tb_item_tags->name ),
			'items'      => $items,
		);
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

		$item_id         = absint( $payload['item_id'] );
		$update_type     = sanitize_text_field( $update_details['updateType'] );
		$title           = isset( $update_details['itemName'] ) ? sanitize_text_field( $update_details['itemName'] ) : '';
		$favorite_status = 'on' === sanitize_text_field( $update_details['favoriteStatus'] ) ? 'favorite' : '';
		$item_type       = $update_details['itemType'];
		$type_template   = ET_THEME_BUILDER_ITEM_TEMPLATE === $item_type;
		$type_set        = ET_THEME_BUILDER_ITEM_SET === $item_type;

		$_                     = ET_Core_Data_Utils::instance();
		$tb_item_types         = ET_Builder_Post_Taxonomy_TBItemType::instance();
		$et_builder_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$et_builder_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();

		switch ( $update_type ) {
			case 'delete':
				if ( $type_set ) {
					$templates = get_post_meta( $item_id, '_et_template_id', false );

					foreach ( $templates as $template ) {
						$template_id = is_string( $template ) ? absint( $template ) : 0;

						if ( current_user_can( 'delete_post', $template_id ) && get_post_type( $template_id ) === $this->post_type ) {
							wp_trash_post( $template_id );
						}
					}
				}

				if ( current_user_can( 'delete_post', $item_id ) && get_post_type( $item_id ) === $this->post_type ) {
					wp_trash_post( $item_id );
				}
				break;
			case 'delete_permanently':
				if ( $type_set ) {
					$templates = get_post_meta( $item_id, '_et_template_id', false );

					foreach ( $templates as $template ) {
						$template_id = is_string( $template ) ? absint( $template ) : 0;

						if ( current_user_can( 'delete_post', $item_id ) && get_post_type( $item_id ) === $this->post_type ) {
							wp_delete_post( $item_id, true );
						}
					}
				}

				if ( current_user_can( 'delete_post', $item_id ) && get_post_type( $item_id ) === $this->post_type ) {
					wp_delete_post( $item_id, true );
				}
				break;
			case 'duplicate':
			case 'duplicate_and_delete':
				if ( ! current_user_can( 'edit_others_posts' ) ) {
					return;
				}

				$meta_input         = array( 'favorite_status' => $favorite_status );
				$content_details    = $_->array_get( $update_details, 'content', null );
				$is_item_from_cloud = isset( $content_details );
				$tax_input          = array(
					$et_builder_categories->name => $updated_data['categories'],
					$et_builder_tags->name       => $updated_data['tags'],
					$tb_item_types->name         => $item_type,
				);

				if ( $is_item_from_cloud ) {
					$meta_input['_et_has_default_template'] = (int) filter_var( $content_details['has_default_template'], FILTER_VALIDATE_BOOLEAN );
					$meta_input['_et_has_global_layouts']   = (int) filter_var( $content_details['has_global_layouts'], FILTER_VALIDATE_BOOLEAN );

					if ( $type_set ) {
						$set_id     = et_theme_builder_library_insert_post( $title, '', $tax_input, $meta_input );
						$meta_input = array(); // Reset.
					}

					$templates   = $_->array_get( $content_details, 'templates', array() );
					$portability = et_core_portability_load( 'et_theme_builder' );

					// Import global colors.
					$layouts = $_->array_get( $content_details, 'layouts', [] );
					foreach ( $layouts as $layout ) {
						if ( ! empty( $layout['global_colors'] ) ) {
							$portability->import_global_colors( $layout['global_colors'] );
						}
					}

					// Import presets.
					$presets_json = $_->array_get( $content_details, 'presets', '' );
					if ( ! empty( $presets_json ) ) {
						$presets = json_decode( stripslashes( $presets_json ), true );
						$portability->import_global_presets( $presets );
					}

					foreach ( $templates as $template ) {
						/**
						 * $layouts_reference: content -> templates -> layouts
						 * $layouts_detail:    content -> layouts
						 */
						$layouts_reference = $_->array_get( $template, 'layouts', array() );
						$layouts_detail    = $_->array_get( $content_details, 'layouts', array() );
						$full_layout       = array();

						$meta_input['_et_template_title'] = sanitize_text_field( $_->array_get( $template, 'title', '' ) );

						$use_on       = array_map( 'sanitize_text_field', $_->array_get( $template, 'use_on', array() ) );
						$exclude_from = array_map( 'sanitize_text_field', $_->array_get( $template, 'exclude_from', array() ) );

						$meta_input['_et_autogenerated_title'] = (int) filter_var( $template['autogenerated_title'], FILTER_VALIDATE_BOOLEAN );
						$meta_input['_et_default']             = (int) filter_var( $template['default'], FILTER_VALIDATE_BOOLEAN );
						$meta_input['_et_enabled']             = (int) filter_var( $template['enabled'], FILTER_VALIDATE_BOOLEAN );

						if ( isset( $layouts_reference['header'] ) ) {
							$layout_id  = (int) $layouts_reference['header']['id'];
							$is_enabled = $layouts_reference['header']['enabled'];

							$meta_input['_et_header_layout_enabled'] = (int) filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

							if ( $layout_id ) {
								$is_global = $layouts_detail[ $layout_id ]['theme_builder']['is_global'];

								$full_layout['header']['post_content']  = wp_unslash( $layouts_detail[ $layout_id ]['data'][ $layout_id ] );
								$meta_input['_et_header_layout_global'] = (int) filter_var( $is_global, FILTER_VALIDATE_BOOLEAN );
							}
						} else {
							// if area is empty and is not explicitly disabled save it as enabled.
							$meta_input['_et_header_layout_enabled'] = 1;
						}

						if ( isset( $layouts_reference['body'] ) ) {
							$layout_id  = (int) $layouts_reference['body']['id'];
							$is_enabled = $layouts_reference['body']['enabled'];

							$meta_input['_et_body_layout_enabled'] = (int) filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

							if ( $layout_id ) {
								$is_global = $layouts_detail[ $layout_id ]['theme_builder']['is_global'];

								$full_layout['body']['post_content']  = wp_unslash( $layouts_detail[ $layout_id ]['data'][ $layout_id ] );
								$meta_input['_et_body_layout_global'] = (int) filter_var( $is_global, FILTER_VALIDATE_BOOLEAN );
							}
						} else {
							// if area is empty and is not explicitly disabled save it as enabled.
							$meta_input['_et_body_layout_enabled'] = 1;
						}

						if ( isset( $layouts_reference['footer'] ) ) {
							$layout_id  = (int) $layouts_reference['footer']['id'];
							$is_enabled = $layouts_reference['footer']['enabled'];

							$meta_input['_et_footer_layout_enabled'] = (int) filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

							if ( $layout_id ) {
								$is_global = $layouts_detail[ $layout_id ]['theme_builder']['is_global'];

								$full_layout['footer']['post_content']  = wp_unslash( $layouts_detail[ $layout_id ]['data'][ $layout_id ] );
								$meta_input['_et_footer_layout_global'] = (int) filter_var( $is_global, FILTER_VALIDATE_BOOLEAN );
							}
						} else {
							// if area is empty and is not explicitly disabled save it as enabled.
							$meta_input['_et_footer_layout_enabled'] = 1;
						}

						$title   = $type_set ? $meta_input['_et_template_title'] : $title;
						$content = wp_json_encode( $full_layout );
						$new_id  = et_theme_builder_library_insert_post( $title, wp_slash( $content ), $tax_input, $meta_input );

						foreach ( $use_on as $condition ) {
							add_post_meta( $new_id, '_et_use_on', sanitize_text_field( $condition ) );
						}

						foreach ( $exclude_from as $condition ) {
							add_post_meta( $new_id, '_et_exclude_from', sanitize_text_field( $condition ) );
						}

						if ( $type_set ) {
							add_post_meta( $new_id, '_et_set_template', 1 );
							add_post_meta( $set_id, '_et_template_id', $new_id );

							if ( $meta_input['_et_default'] ) {
								add_post_meta( $set_id, '_et_default_template_id', $new_id );
							}
						}
					}
				} else {
					/**
					 * For local item duplication.
					 */
					if ( $type_template ) {
						$meta_input = array_merge(
							$meta_input,
							et_theme_builder_get_template_settings( $item_id, false )
						);

						$use_on       = $meta_input['_et_use_on'];
						$exclude_from = $meta_input['_et_exclude_from'];

						// Remove from post meta insertion.
						unset( $meta_input['_et_use_on'], $meta_input['_et_exclude_from'] );
					} else {
						$meta_keys = array(
							'_et_has_global_layouts',
							'_et_has_default_template',
						);

						foreach ( $meta_keys as $key ) {
							$meta_input[ $key ] = get_post_meta( $item_id, $key, true );
						}
					}

					$content = get_the_content( null, false, $item_id );
					$new_id  = et_theme_builder_library_insert_post( $title, wp_slash( $content ), $tax_input, $meta_input );

					if ( $type_template ) {
						foreach ( $use_on as $condition ) {
							add_post_meta( $new_id, '_et_use_on', sanitize_text_field( $condition ) );
						}

						foreach ( $exclude_from as $condition ) {
							add_post_meta( $new_id, '_et_exclude_from', sanitize_text_field( $condition ) );
						}
					} else {
						$template_ids = get_post_meta( $item_id, '_et_template_id', false );
						$template_map = [];

						foreach ( $template_ids as $maybe_template_id ) {
							$template_id  = absint( $maybe_template_id );
							$library_item = new ET_Theme_Builder_Local_Library_Item( $template_id );

							$duplicated_template_id       = $library_item->duplicate_item();
							$template_map[ $template_id ] = $duplicated_template_id;
						}

						$maybe_default_template_id = get_post_meta( $item_id, '_et_default_template_id', true );
						$default_template_id       = absint( $maybe_default_template_id );

						if ( 0 !== $default_template_id ) {
							$duplicated_default_template_id = $template_map[ $default_template_id ];
							update_post_meta( $new_id, '_et_default_template_id', $duplicated_default_template_id );
						}

						foreach ( $template_map as $duplicated_template_id ) {
							add_post_meta( $new_id, '_et_template_id', $duplicated_template_id );
						}
					}
				}

				$updated_data['newItem'] = isset( $set_id ) ? $set_id : $new_id;
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

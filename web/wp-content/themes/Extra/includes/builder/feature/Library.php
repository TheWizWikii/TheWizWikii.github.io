<?php
/**
 * Divi Library.
 *
 * @package Builder
 */

/**
 * Core class used to implement Layout Library.
 */
class ET_Builder_Library {

	/**
	 * Instance of `ET_Builder_Library`.
	 *
	 * @var ET_Builder_Library
	 */
	private static $_instance;

	/**
	 * Instance of  `ET_Core_Data_Utils`.
	 *
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * List of i18n strings.
	 *
	 * @var mixed[]
	 */
	protected static $_i18n;

	/**
	 * Yoast primary category meta key.
	 *
	 * @var string
	 */
	protected static $_primary_category_key;

	/**
	 * List of Divi Library's Standard Post Types.
	 *
	 * @var string[]
	 */
	protected static $_standard_post_types = array( 'post', 'page', 'project' );

	/**
	 * Instance of `ET_Builder_Post_Taxonomy_LayoutCategory`.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutCategory
	 */
	public $layout_categories;

	/**
	 * Instance of `ET_Builder_Post_Taxonomy_LayoutPack`.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutPack
	 */
	public $layout_packs;

	/**
	 * Instance of `ET_Builder_Post_Taxonomy_LayoutType`.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutType
	 */
	public $layout_types;

	/**
	 * Instance of `ET_Builder_Post_Type_Layout`.
	 *
	 * @var ET_Builder_Post_Type_Layout
	 */
	public $layouts;

	/**
	 * List of submenu files to heightlight Divi menu when viwwing layout category and tag.
	 *
	 * @var array
	 */
	public static $submenu_files = array( 'edit-tags.php?taxonomy=layout_category', 'edit-tags.php?taxonomy=layout_tag' );

	/**
	 * Instance of `ET_Builder_Post_Taxonomy_LayoutTag`.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutTag
	 */
	public $layout_tags;

	/**
	 * Instance of `ET_Builder_Post_Taxonomy_LayoutWidth`.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutWidth
	 */
	public $layout_width;

	/**
	 * ET_Builder_Library constructor.
	 */
	public function __construct() {
		$this->_instance_check();
		$this->_register_cpt_and_taxonomies();

		self::$_ = ET_Core_Data_Utils::instance();

		$this->_register_hooks();
		$this->_register_ajax_callbacks();

		$root_directory = defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ? ET_BUILDER_PLUGIN_DIR : get_template_directory();

		self::$_i18n = require $root_directory . '/cloud/i18n/library.php';

		self::$_standard_post_types = self::_standard_post_types();
	}

	/**
	 * Compare by slug
	 *
	 * @param object $a First category in comparison.
	 * @param object $b Second category in comparison.
	 *
	 * @return bool
	 */
	public static function compare_by_slug( $a, $b ) {
		return strcmp( $a->slug, $b->slug );
	}

	/**
	 * Gets a translated string from {@see self::$_i18n}.
	 *
	 * @param string $string The untranslated string.
	 * @param string $path   Optional path for nested strings.
	 *
	 * @return string The translated string if found, the original string otherwise.
	 */
	public static function __( $string, $path = '' ) {
		$path .= $path ? ".{$string}" : $string;

		return self::$_->array_get( self::$_i18n, $path, $string );
	}

	/**
	 * Dies if an instance already exists.
	 *
	 * @since 3.0.99
	 */
	protected function _instance_check() {
		if ( self::$_instance ) {
			et_error( 'Multiple instances are not allowed!' );
			wp_die();
		}
	}

	/**
	 * Get the name of a thumbnail image size used in the library UI.
	 *
	 * @since 3.0.99
	 *
	 * @param string $type The thumbnail type. Accepts 'thumbnail', 'screenshot'.
	 *
	 * @return string
	 */
	protected static function _get_image_size_name( $type ) {
		$names = array(
			'thumbnail'       => 'full',
			'thumbnail_small' => 'et-pb-portfolio-image',
			'screenshot'      => 'et-pb-portfolio-image-single',
		);

		$name = $names[ $type ];

		/**
		 * Filters the names of the registered image sizes to use for layout thumbnails. The
		 * dynamic portion of the filter name, '$type', refers to the layout image
		 * type ('thumbnail' or 'screenshot').
		 *
		 * @since 3.0.99
		 *
		 * @param string $name The name of the registered image size that should be used.
		 */
		return apply_filters( "et_builder_layout_{$type}_image_size_name", $name );
	}

	/**
	 * Returns a filtered short name for a layout.
	 *
	 * @since 3.0.99
	 *
	 * @param string  $long_name Layout title.
	 * @param WP_Post $layout Layout post.
	 *
	 * @return string
	 */
	protected function _get_layout_short_name( $long_name, $layout ) {
		/**
		 * Filters the short name for layouts that do not have one.
		 *
		 * @since 3.0.99
		 *
		 * @param string  $long_name
		 * @param WP_Post $layout
		 */
		return apply_filters( 'et_builder_library_layout_short_name', $long_name, $layout );
	}

	/**
	 * Processes layout categories for inclusion in the library UI layouts data.
	 *
	 * @since 3.0.99
	 *
	 * @param WP_POST $post              Unprocessed layout.
	 * @param object  $layout            Currently processing layout.
	 * @param int     $index             The layout's index position.
	 * @param array[] $layout_categories Processed layouts.
	 */
	protected function _process_layout_categories( $post, $layout, $index, &$layout_categories ) {
		$terms = wp_get_post_terms( $post->ID, $this->layout_categories->name );
		if ( ! $terms ) {
			$layout->category_slug = 'uncategorized';
			return;
		}

		foreach ( $terms as $category ) {
			$category_name = self::__( html_entity_decode( $category->name ), '@categories' );
			$category_name = et_core_intentionally_unescaped( $category_name, 'react_jsx' );

			if ( ! isset( $layout_categories[ $category->term_id ] ) ) {
				$layout_categories[ $category->term_id ] = array(
					'id'      => $category->term_id,
					'name'    => $category_name,
					'slug'    => $category->slug,
					'items' => array(),
				);
			}

			$layout_categories[ $category->term_id ]['items'][] = $index;

			$layout->categories[]   = $category_name;
			$layout->category_ids[] = $category->term_id;

			if ( ! isset( $layout->category_slug ) ) {
				$layout->category_slug = $category->slug;
			}

			$id = get_post_meta( $post->ID, self::$_primary_category_key, true );

			if ( $id ) {
				// $id is a string, $category->term_id is an int.
				if ( $id === $category->term_id ) {
					// This is the primary category (used in the layout URL).
					$layout->category_slug = $category->slug;
				}
			}
		}
	}

	/**
	 * Processes layout tags for inclusion in the library UI layouts data.
	 *
	 * @since 3.0.99
	 *
	 * @param WP_POST $post              Unprocessed layout.
	 * @param object  $layout            Currently processing layout.
	 * @param int     $index             The layout's index position.
	 * @param array[] $layout_tags       Processed layouts.
	 */
	protected function _process_layout_tags( $post, $layout, $index, &$layout_tags ) {
		$terms = wp_get_post_terms( $post->ID, $this->layout_tags->name );

		if ( ! $terms ) {
			return;
		}

		foreach ( $terms as $tag ) {
			$tag_name = self::__( html_entity_decode( $tag->name ), '@tags' );
			$tag_name = et_core_intentionally_unescaped( $tag_name, 'react_jsx' );

			if ( ! isset( $layout_tags[ $tag->term_id ] ) ) {
				$layout_tags[ $tag->term_id ] = array(
					'id'    => $tag->term_id,
					'name'  => $tag_name,
					'slug'  => $tag->slug,
					'items' => array(),
				);
			}

			$layout_tags[ $tag->term_id ]['items'][] = $index;

			$layout->tags[]    = $tag_name;
			$layout->tag_ids[] = $tag->term_id;

			if ( ! isset( $layout->tag_slug ) ) {
				$layout->tag_slug = $tag->slug;
			}

			$id = get_post_meta( $post->ID, self::$_primary_category_key, true );

			if ( $id ) {
				// $id is a string, $category->term_id is an int.
				if ( $id === $tag->term_id ) {
					// This is the primary category (used in the layout URL).
					$layout->tag_slug = $tag->slug;
				}
			}
		}
	}

	/**
	 * Processes layout packs for inclusion in the library UI layouts data.
	 *
	 * @since 3.0.99
	 *
	 * @param WP_POST $post         Unprocessed layout.
	 * @param object  $layout       Currently processing layout.
	 * @param int     $index        The layout's index position.
	 * @param array[] $layout_packs Processed layouts.
	 */
	protected function _process_layout_packs( $post, $layout, $index, &$layout_packs ) {
		$terms = wp_get_post_terms( $post->ID, $this->layout_packs->name );
		if ( ! $terms ) {
			return;
		}

		$pack      = array_shift( $terms );
		$pack_name = self::__( html_entity_decode( $pack->name ), '@packs' );
		$pack_name = et_core_intentionally_unescaped( $pack_name, 'react_jsx' );

		if ( ! isset( $layout_packs[ $pack->term_id ] ) ) {
			$layout_packs[ $pack->term_id ] = array(
				'id'      => $pack->term_id,
				'name'    => $pack_name,
				'slug'    => $pack->slug,
				'date'    => $layout->date,
				'items' => array(),
			);
		}

		if ( $layout->is_landing ) {
			$layout_packs[ $pack->term_id ]['thumbnail']     = $layout->thumbnail;
			$layout_packs[ $pack->term_id ]['screenshot']    = $layout->screenshot;
			$layout_packs[ $pack->term_id ]['description']   = et_core_intentionally_unescaped( html_entity_decode( $post->post_excerpt ), 'react_jsx' );
			$layout_packs[ $pack->term_id ]['category_slug'] = $layout->category_slug;
			$layout_packs[ $pack->term_id ]['landing_index'] = $index;
		}

		$layout_packs[ $pack->term_id ]['items'][] = $index;

		$layout_packs[ $pack->term_id ]['categories']   = $layout->categories;
		$layout_packs[ $pack->term_id ]['category_ids'] = $layout->category_ids;

		$layout_packs[ $pack->term_id ]['tags']    = $layout->tags;
		$layout_packs[ $pack->term_id ]['tag_ids'] = $layout->tag_ids;

		$layout->pack    = $pack_name;
		$layout->pack_id = $pack->term_id;
	}

	/**
	 * Registers the Library's AJAX callbacks.
	 *
	 * @since 3.0.99
	 */
	protected function _register_ajax_callbacks() {
		add_action( 'wp_ajax_et_builder_library_get_layouts_data', array( $this, 'wp_ajax_et_builder_library_get_layouts_data' ) );
		add_action( 'wp_ajax_et_builder_library_get_layout', array( $this, 'wp_ajax_et_builder_library_get_layout' ) );
		add_action( 'wp_ajax_et_builder_library_update_terms', array( $this, 'wp_ajax_et_builder_library_update_terms' ) );
		add_action( 'wp_ajax_et_builder_library_save_temp_layout', array( $this, 'wp_ajax_et_builder_library_save_temp_layout' ) );
		add_action( 'wp_ajax_et_builder_library_update_item', array( $this, 'wp_ajax_et_builder_library_update_item' ) );
		add_action( 'wp_ajax_et_builder_library_convert_item', array( $this, 'wp_ajax_et_builder_library_convert_item' ) );
		add_action( 'wp_ajax_et_builder_library_upload_thumbnail', array( $this, 'wp_ajax_et_builder_library_upload_thumbnail' ) );
		add_action( 'wp_ajax_et_builder_library_update_account', array( $this, 'wp_ajax_et_builder_library_update_account' ) );
		add_action( 'wp_ajax_et_builder_library_remove_temp_layout', array( $this, 'wp_ajax_et_builder_library_remove_temp_layout' ) );
		add_action( 'wp_ajax_et_builder_toggle_cloud_status', array( $this, 'wp_ajax_et_builder_toggle_cloud_status' ) );
		add_action( 'wp_ajax_et_builder_library_get_cloud_token', array( $this, 'wp_ajax_et_builder_library_get_cloud_token' ) );
		add_action( 'wp_ajax_et_builder_library_clear_temp_presets', array( $this, 'wp_ajax_et_builder_library_clear_temp_presets' ) );
	}

	/**
	 * Registers the Library's custom post types and taxonomies.
	 *
	 * @since 3.0.99
	 */
	protected function _register_cpt_and_taxonomies() {
		$files = glob( ET_BUILDER_DIR . 'post/*/Layout*.php' );
		if ( ! $files ) {
			return;
		}

		foreach ( $files as $file ) {
			require_once $file;
		}

		$this->layouts           = ET_Builder_Post_Type_Layout::instance();
		$this->layout_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$this->layout_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();
		$this->layout_packs      = ET_Builder_Post_Taxonomy_LayoutPack::instance();
		$this->layout_types      = ET_Builder_Post_Taxonomy_LayoutType::instance();
		$this->layout_width      = ET_Builder_Post_Taxonomy_LayoutWidth::instance();

		ET_Builder_Post_Taxonomy_LayoutScope::instance();

		// We manually call register_all() now to ensure the CPT and taxonomies are registered
		// at exactly the same point during the request that they were in prior releases.
		ET_Builder_Post_Type_Layout::register_all( 'builder' );

		self::$_primary_category_key = "_yoast_wpseo_primary_{$this->layout_categories->name}";
	}

	/**
	 * Registers the Library's non-AJAX callbacks.
	 *
	 * @since 3.0.99
	 */
	public function _register_hooks() {
		add_action( 'admin_init', 'ET_Builder_Library::update_old_layouts' );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_admin_enqueue_scripts' ), 4 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'render_session_expired_modal' ) );

		add_filter( 'et_theme_builder_template_settings_options_term_pages', array( $this, 'tb_remove_unsupported_taxonomies' ), 10, 2 );
		add_filter( 'parent_file', array( $this, 'wp_hook_parent_file' ), 10, 1 );
		add_filter( 'submenu_file', array( $this, 'wp_hook_submenu_file' ), 10, 1 );
		add_filter( 'tag_row_actions', array( $this, 'wp_filter_tag_row_actions' ), 10, 2 );
	}

	/**
	 * Returns sorted layout category and pack IDs for use in library UI layouts data.
	 *
	 * @since 3.0.99
	 *
	 * @param array[] $categories Layout categories.
	 * @param array[] $packs Layout packs.
	 *
	 * @return array[] {
	 *
	 *     @type int[] $categories Layout category ids sorted alphabetically by category name.
	 *     @type int[] $packs      Layout pack ids sorted alphabetically by pack name.
	 * }
	 */
	protected static function _sort_builder_library_data( $categories, $packs, $tags ) {
		$categories = array_values( $categories );
		$packs      = array_values( $packs );
		$tags       = array_values( $tags );
		$sorted     = array();

		foreach ( array( 'categories', 'packs', 'tags' ) as $taxonomy ) {
			$sorted[ $taxonomy ] = array();

			$$taxonomy = self::$_->array_sort_by( $$taxonomy, 'slug' );

			foreach ( $$taxonomy as $term ) {
				$sorted[ $taxonomy ][] = $term['id'];
			}
		}

		return $sorted;
	}

	/**
	 * Get Divi Library's Standard Post Types.
	 *
	 * @return mixed|void
	 */
	public static function _standard_post_types() {
		/**
		 * Filters the Divi Library's Standard Post Types.
		 *
		 * @since 3.0.99
		 *
		 * @param string[] $standard_post_types
		 */
		return apply_filters( 'et_pb_standard_post_types', self::$_standard_post_types );
	}

	/**
	 * Generates layouts data for the builder's library UI.
	 *
	 * @since 3.0.99
	 *
	 * @return array $data
	 */
	public function builder_library_layouts_data( $library_type = 'layout' ) {
		$layout_categories = array();
		$layout_packs      = array();
		$layout_tags       = array();
		$layouts           = array();
		$index             = 0;

		$thumbnail       = self::_get_image_size_name( 'thumbnail' );
		$thumbnail_small = self::_get_image_size_name( 'thumbnail_small' );
		$screenshot      = self::_get_image_size_name( 'screenshot' );

		$extra_layout_post_type = 'layout';

		$posts = $this->layouts
			->query()
			->not()->with_meta( '_et_pb_built_for_post_type', $extra_layout_post_type )
			->run(
				array(
					'post_status' => array( 'publish', 'trash' ),
					'fields'      => 'ids',
				)
			);

		$posts = is_array( $posts ) ? $posts : array( $posts );

		foreach ( $posts as $post_id ) {
			$post   = get_post( $post_id );
			$layout = new stdClass();

			setup_postdata( $post );

			// check if current user can edit library item.
			$can_edit_post = current_user_can( 'edit_post', $post->ID );

			$layout->id    = $post->ID;
			$layout->index = $index;
			$layout->date  = $post->post_date;
			$types         = wp_get_post_terms( $layout->id, $this->layout_types->name );

			if ( ! $types ) {
				continue;
			}

			$layout->type = $types[0]->name;

			if ( $library_type !== $layout->type ) {
				continue;
			}

			$width_values  = wp_get_post_terms( $layout->id, $this->layout_width->name, array( 'fields' => 'names' ) );
			$layout->width = ! empty( $width_values ) ? $width_values[0] : 'regular';

			$layout->row_layout = get_post_meta( $post->ID, '_et_pb_row_layout', true );

			$layout->subtype = get_post_meta( $post->ID, '_et_pb_module_type', true );

			if ( '' !== $layout->subtype ) {
				$module           = ET_Builder_Element::get_module( $layout->subtype );
				$layout->subtitle = ! empty( $module->name ) ? $module->name : $layout->type;
			}

			$title      = html_entity_decode( $post->post_title );
			$short_name = get_post_meta( $post->ID, '_et_builder_library_short_name', true );

			if ( ! $short_name ) {
				$short_name = $this->_get_layout_short_name( $title, $post );

				if ( $short_name !== $title ) {
					update_post_meta( $post->ID, '_et_builder_library_short_name', $short_name );
				}
			}
			$layout->short_name = '';
			$layout->name       = $layout->short_name;

			if ( $title ) {
				// Remove periods since we use dot notation to retrieve translation.
				str_replace( '.', '', $title );

				$layout->name = et_core_intentionally_unescaped( self::__( $title, '@layoutsLong' ), 'react_jsx' );
			}

			if ( $short_name ) {
				// Remove periods since we use dot notation to retrieve translation.
				str_replace( '.', '', $title );

				$layout->short_name = et_core_intentionally_unescaped( self::__( $short_name, '@layoutsShort' ), 'react_jsx' );
			}

			$layout->slug = $post->post_name;
			$layout->url  = esc_url( wp_make_link_relative( get_permalink( $post ) ) );

			$layout->thumbnail       = esc_url( get_the_post_thumbnail_url( $post->ID, $thumbnail ) );
			$layout->thumbnail_small = esc_url( get_the_post_thumbnail_url( $post->ID, $thumbnail_small ) );
			$layout->screenshot      = esc_url( get_the_post_thumbnail_url( $post->ID, $screenshot ) );

			$layout->is_global    = $this->layouts->is_global( $layout->id );
			$layout->is_favorite  = $this->layouts->is_favorite( $layout->id );
			$layout->is_landing   = ! empty( $post->post_excerpt );
			$layout->description  = '';
			$layout->isTrash      = 'trash' === $post->post_status; // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- This is valid format for the property in the Cloud App.
			$layout->isReadOnly   = ! $can_edit_post; // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- This is valid format for the property in the Cloud App.
			$layout->categories   = array();
			$layout->category_ids = array();
			$layout->tags         = array();
			$layout->tag_ids      = array();

			$this->_process_layout_categories( $post, $layout, $index, $layout_categories );
			$this->_process_layout_tags( $post, $layout, $index, $layout_tags );
			$this->_process_layout_packs( $post, $layout, $index, $layout_packs );

			wp_reset_postdata();

			$layouts[] = $layout;

			$index++;
		}

		/**
		 * Filters data for the 'My Saved Layouts' tab.
		 *
		 * @since 3.1
		 *
		 * @param array[] $saved_layouts_data {
		 *     Saved Layouts Data
		 *
		 *     @type array[]  $categories {
		 *         Layout Categories
		 *
		 *         @type $id mixed[] {
		 *             Category
		 *
		 *             @type int    $id      Id.
		 *             @type int[]  $layouts Id's of layouts in category.
		 *             @type string $name    Name.
		 *             @type string $slug    Slug.
		 *          }
		 *          ...
		 *     }
		 *     @type array[]  $packs {
		 *         Layout Packs
		 *
		 *         @type $id mixed[] {
		 *             Pack
		 *
		 *             @type string $category_ids  Category ids.
		 *             @type string $category_slug Primary category slug.
		 *             @type string $date          Published date.
		 *             @type string $description   Description.
		 *             @type int    $id            Id.
		 *             @type int[]  $layouts       Id's of layouts in pack.
		 *             @type string $name          Name.
		 *             @type string $screenshot    Screenshot URL.
		 *             @type string $slug          Slug.
		 *             @type string $thumbnail     Thumbnail URL.
		 *          }
		 *          ...
		 *     }
		 *     @type object[] $layouts {
		 *         Layouts
		 *
		 *         @type object {
		 *             Layout
		 *
		 *             @type int      $id ID
		 *             @type string[] $categories
		 *             @type int[]    $category_ids
		 *             @type string   $category_slug
		 *             @type int      $date
		 *             @type string   $description
		 *             @type int      $index
		 *             @type bool     $is_global
		 *             @type bool     $is_landing
		 *             @type string   $name
		 *             @type string   $screenshot
		 *             @type string   $short_name
		 *             @type string   $slug
		 *             @type string   $thumbnail
		 *             @type string   $thumbnail_small
		 *             @type string   $type
		 *             @type string   $url
		 *         }
		 *         ...
		 *     }
		 *     @type array[]  $sorted {
		 *         Sorted Ids
		 *
		 *         @type int[] $categories
		 *         @type int[] $packs
		 *     }
		 * }
		 */
		$saved_layouts_data = array(
			'categories' => $this->_get_processed_terms( 'layout_category' ),
			'packs'      => $layout_packs,
			'tags'       => $this->_get_processed_terms( 'layout_tag' ),
			'items'      => $layouts,
		);
		$saved_layouts_data = apply_filters( 'et_builder_library_saved_layouts', $saved_layouts_data );

		/**
		 * Filters custom tabs layout data for the library modal. Custom tabs must be registered
		 * via the {@see 'et_builder_library_modal_custom_tabs'} filter.
		 *
		 * @since 3.1
		 *
		 * @param array[] $custom_layouts_data {
		 *     Custom Layouts Data Organized By Modal Tab
		 *
		 *     @type array[] $tab_slug See {@see 'et_builder_library_saved_layouts'} for array structure.
		 *     ...
		 * }
		 * @param array[] $saved_layouts_data {@see 'et_builder_library_saved_layouts'} for array structure.
		 */
		$custom_layouts_data = apply_filters(
			'et_builder_library_custom_layouts',
			array(
				'existing_pages' => $this->_builder_library_modal_custom_tabs_existing_pages(),
			),
			$saved_layouts_data
		);

		return array(
			'layouts_data'        => $saved_layouts_data,
			'custom_layouts_data' => $custom_layouts_data,
		);
	}

	/**
	 * Gets the terms list and processes it into desired format.
	 *
	 * @since 4.17.0
	 *
	 * @param string $term_name Term Name.
	 *
	 * @return array $terms_by_id
	 */
	protected function _get_processed_terms( $term_name ) {
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
	 * Filters data for the 'Your Existing Pages' tab.
	 *
	 * @since 3.4
	 *
	 * @return array[] $saved_layouts_data {
	 *     Existing Pages/Posts Data
	 *
	 *     @type array[]  $categories {
	 *         Post Types Filters
	 *
	 *         @type $id mixed[] {
	 *             Post Type
	 *
	 *             @type int    $id      Id.
	 *             @type int[]  $layouts Id's of layouts in filter.
	 *             @type string $name    Name.
	 *             @type string $slug    Slug.
	 *          }
	 *          ...
	 *     }
	 *     @type array[]  $packs {
	 *         Layout Packs
	 *
	 *         @type $id mixed[] {
	 *             Pack
	 *
	 *             @type string $category_ids  Category ids.
	 *             @type string $category_slug Primary category slug.
	 *             @type string $date          Published date.
	 *             @type string $description   Description.
	 *             @type int    $id            Id.
	 *             @type int[]  $layouts       Id's of layouts in pack.
	 *             @type string $name          Name.
	 *             @type string $screenshot    Screenshot URL.
	 *             @type string $slug          Slug.
	 *             @type string $thumbnail     Thumbnail URL.
	 *          }
	 *          ...
	 *     }
	 *     @type object[] $layouts {
	 *         Pages/Posts Data
	 *
	 *         @type object {
	 *             Page/Post Object
	 *
	 *             @type int      $id ID
	 *             @type string[] $categories
	 *             @type int[]    $category_ids
	 *             @type string   $category_slug
	 *             @type int      $date
	 *             @type string   $description
	 *             @type int      $index
	 *             @type bool     $is_global
	 *             @type bool     $is_landing
	 *             @type string   $name
	 *             @type string   $screenshot
	 *             @type string   $short_name
	 *             @type string   $slug
	 *             @type string   $thumbnail
	 *             @type string   $thumbnail_small
	 *             @type string   $type
	 *             @type string   $url
	 *         }
	 *         ...
	 *     }
	 *     @type array[]  $sorted {
	 *         Sorted Ids
	 *
	 *         @type int[] $categories
	 *         @type int[] $packs
	 *     }
	 * }
	 */
	protected function _builder_library_modal_custom_tabs_existing_pages() {
		et_core_nonce_verified_previously();

		$categories = array();
		$packs      = array();
		$layouts    = array();
		$index      = 0;

		$thumbnail       = self::_get_image_size_name( 'screenshot' );
		$thumbnail_small = self::_get_image_size_name( 'thumbnail_small' );

		/**
		 * Array of post types that should be listed as categories under "Existing Pages".
		 *
		 * @since 4.0
		 *
		 * @param string[] $post_types
		 */
		$post_types = apply_filters( 'et_library_builder_post_types', et_builder_get_builder_post_types() );

		// Remove Extra's category layouts from "Your Existing Pages" layout list.
		if ( in_array( 'layout', $post_types, true ) ) {
			unset( $post_types[ array_search( 'layout', $post_types, true ) ] );
		}

		if ( wp_doing_ajax() ) {
			// VB case.
			$exclude = isset( $_POST['postId'] ) ? (int) $_POST['postId'] : false;
		} else {
			// BB case.
			$exclude = get_the_ID();
		}

		if ( $post_types ) {
			$category_id  = 1;
			$layout_index = 0;

			// Keep track of slugs in case there are duplicates.
			$seen = array();

			// List of post types which should be excluded from the Pages tab.
			$unsupported_post_types = array(
				ET_BUILDER_LAYOUT_POST_TYPE,
				ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
				ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
				ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
				ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
				ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE,
			);

			foreach ( $post_types as $post_type ) {
				if ( in_array( $post_type, $unsupported_post_types, true ) ) {
					continue;
				}

				$post_type_obj = get_post_type_object( $post_type );

				if ( ! $post_type_obj ) {
					continue;
				}

				$category = new StdClass();

				$category->id      = $category_id;
				$category->layouts = array();
				$category->slug    = $post_type;
				$category->name    = $post_type_obj->label;

				$query = new ET_Core_Post_Query( $post_type );

				$posts = $query
					// Do not include unused Theme Builder layouts. For more information
					// see et_theme_builder_trash_draft_and_unused_posts().
					->not()->with_meta( '_et_theme_builder_marked_as_unused' )
					->run();

				$posts = self::$_->array_sort_by( is_array( $posts ) ? $posts : array( $posts ), 'post_name' );

				if ( ! empty( $posts ) ) {
					foreach ( $posts as $post ) {
						// Check if page builder is activated.
						if ( ! et_pb_is_pagebuilder_used( $post->ID ) ) {
							continue;
						}

						// Do not add the current page to the list.
						if ( $post->ID === $exclude ) {
							continue;
						}

						// Check if content has shortcode.
						if ( ! has_shortcode( $post->post_content, 'et_pb_section' ) ) {
							continue;
						}

						// Only include posts that the user is allowed to edit.
						if ( ! current_user_can( 'edit_post', $post->ID ) ) {
							continue;
						}

						$title = html_entity_decode( $post->post_title );

						$slug = $post->post_name;

						if ( ! $slug ) {
							// Generate a slug, if none is available - this is necessary as draft posts
							// that have never been published will not have a slug by default.
							$slug = wp_unique_post_slug( $post->post_title . '-' . $post->ID, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
						}

						if ( empty( $title ) || empty( $slug ) ) {
							continue;
						}

						// Make sure we don't have duplicate slugs since we're using them as key in React.
						// slugs should always be unique but enabling/disabling WPML can break this rule.
						if ( isset( $seen[ $slug ] ) ) {
							continue;
						}

						$type_label = et_theme_builder_is_layout_post_type( $post_type )
							? $post_type_obj->labels->singular_name
							: $post_type;

						$seen[ $slug ]      = true;
						$layout             = new stdClass();
						$layout->index      = $index;
						$layout->id         = $post->ID;
						$layout->date       = $post->post_date;
						$layout->status     = $post->post_status;
						$layout->icon       = 'layout';
						$layout->type       = $type_label;
						$layout->name       = et_core_intentionally_unescaped( $title, 'react_jsx' );
						$layout->short_name = et_core_intentionally_unescaped( $title, 'react_jsx' );
						$layout->slug       = $slug;
						$layout->url        = esc_url( wp_make_link_relative( get_permalink( $post ) ) );

						$layout->thumbnail       = esc_url( get_the_post_thumbnail_url( $post->ID, $thumbnail ) );
						$layout->thumbnail_small = esc_url( get_the_post_thumbnail_url( $post->ID, $thumbnail_small ) );

						$layout->categories   = array();
						$layout->category_ids = array( $category_id );

						$layout->is_global     = false;
						$layout->is_landing    = false;
						$layout->is_favorite   = $this->layouts->is_favorite( $layout->id );
						$layout->description   = '';
						$layout->category_slug = $post_type;
						// $layout_index is the array index, not the $post->ID
						$category->layouts[] = $layout_index;

						$post_status_object = get_post_status_object( $post->post_status );

						$layout->status = isset( $post_status_object->label ) ? $post_status_object->label : $post->post_status;

						$layouts[] = $layout;

						$index++;
					}
				}

				$categories[ $category_id++ ] = $category;
			}
		}

		if ( count( $categories ) > 1 ) {
			// Sort categories (post_type in this case) by slug.
			uasort( $categories, array( 'ET_Builder_Library', 'compare_by_slug' ) );
		}

		return array(
			'categories' => $categories,
			'packs'      => $packs,
			'items'      => $layouts,
			'options'    => array(
				'content' => array(
					'title' => array(
						et_core_intentionally_unescaped( self::__( '%d Pages' ), 'react_jsx' ),
						et_core_intentionally_unescaped( self::__( '%d Page' ), 'react_jsx' ),
					),
				),
				'sidebar' => array(
					'title' => et_core_intentionally_unescaped( self::__( 'Find A Page' ), 'react_jsx' ),
					'filterTitle' => et_core_intentionally_unescaped( self::__( 'Post Types' ), 'react_jsx' ),
				),
				'list'    => array(
					'columns' => array(
						'status' => et_core_intentionally_unescaped( self::__( 'Status' ), 'react_jsx' ),
					),
				),
			),
			'sorted'     => array(
				'categories' => array_keys( $categories ),
				'packs'      => $packs,
			),
		);
	}

	/**
	 * Get custom tabs for the library modal.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return array[] {
	 *     Custom Tabs
	 *
	 *     @type string $tab_slug Tab display name.
	 *     ...
	 * }
	 */
	public static function builder_library_modal_custom_tabs( $post_type ) {
		/**
		 * Filters custom tabs for the library modal.
		 *
		 * @since 3.1
		 *
		 * @param array[] $custom_tabs See {@self::builder_library_modal_custom_tabs()} return value.
		 */
		$custom_tabs = array();

		if ( 'layout' !== $post_type ) {
			$custom_tabs['existing_pages'] = esc_html__( 'Your Existing Pages', 'et_builder' );
		}

		return apply_filters( 'et_builder_library_modal_custom_tabs', $custom_tabs, $post_type );
	}

	/**
	 * Gets the post types that have existing layouts built for them.
	 *
	 * @since 3.1  Supersedes {@see et_pb_get_standard_post_types()}
	 *            Supersedes {@see et_pb_get_used_built_for_post_types()}
	 * @since 2.0
	 *
	 * @param string $type Accepts 'standard' or 'all'. Default 'standard'.
	 *
	 * @return string[] $post_types
	 */
	public static function built_for_post_types( $type = 'standard' ) {
		static $all_built_for_post_types;

		if ( 'standard' === $type ) {
			return self::$_standard_post_types;
		}

		if ( $all_built_for_post_types ) {
			return $all_built_for_post_types;
		}

		global $wpdb;

		$all_built_for_post_types = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT( meta_value ) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value > ''",
				'_et_pb_built_for_post_type'
			)
		);

		return $all_built_for_post_types;
	}

	/**
	 * Get the class instance.
	 *
	 * @since 3.0.99
	 *
	 * @return ET_Builder_Library
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Performs one-time maintenance tasks on library layouts in the database.
	 * {@see 'admin_init'}
	 *
	 * @since 3.1  Relocated from `builder/layouts.php`. New task: create 'Legacy Layouts' category.
	 * @since 2.0
	 */
	public static function update_old_layouts() {
		$layouts = ET_Builder_Post_Type_Layout::instance();

		if ( 'yes' !== get_theme_mod( 'et_updated_layouts_built_for_post_types', 'no' ) ) {
			$posts = $layouts
				->query()
				->not()->with_meta( '_et_pb_built_for_post_type' )
				->run();

			foreach ( (array) $posts as $single_post ) {
				update_post_meta( $single_post->ID, '_et_pb_built_for_post_type', 'page' );
			}

			set_theme_mod( 'et_updated_layouts_built_for_post_types', 'yes' );
		}

		if ( ! et_get_option( 'et_pb_layouts_updated', false ) ) {
			$types = array(
				'section',
				'row',
				'module',
				'fullwidth_section',
				'specialty_section',
				'fullwidth_module',
			);

			$posts = $layouts
				->query()
				->not()->is_type( $types )
				->run();

			foreach ( (array) $posts as $single_post ) {
				if ( ! get_the_terms( $single_post->ID, 'layout_type' ) ) {
					wp_set_object_terms( $single_post->ID, 'layout', 'layout_type', true );
				}
			}

			et_update_option( 'et_pb_layouts_updated', true );
		}

		if ( ! et_get_option( 'library_removed_legacy_layouts', false ) ) {
			$posts = $layouts
				->query()
				->with_meta( '_et_pb_predefined_layout' )
				->run();

			foreach ( $posts as $post ) {
				if ( 'layout' === get_post_meta( $post->ID, '_et_pb_built_for_post_type', true ) ) {
					// Don't touch Extra's Category Builder layouts.
					continue;
				}

				// Sanity check just to be safe.
				if ( get_post_meta( $post->ID, '_et_pb_predefined_layout', true ) ) {
					wp_delete_post( $post->ID, true );
				}
			}

			et_update_option( 'library_removed_legacy_layouts', true );
		}
	}

	/**
	 * AJAX Callback: Gets a layout by ID.
	 *
	 * @since 3.0.99
	 *
	 * @global $_POST['id']     The id of the desired layout.
	 * @global $_POST ['nonce'] Nonce: 'et_builder_library_get_layout'.
	 *
	 * @return string|void $layout JSON encoded. See return value of {@see et_pb_retrieve_templates()}
	 *                             for array structure.
	 */
	public function wp_ajax_et_builder_library_get_layout() {
		et_core_security_check( 'edit_posts', 'et_builder_library_get_layout', 'nonce' );

		$id           = isset( $_POST['id'] ) ? (int) sanitize_text_field( $_POST['id'] ) : 0;
		$content_type = isset( $_POST['contentType'] ) ? (string) sanitize_text_field( $_POST['contentType'] ) : 'processed';
		$library_type = isset( $_POST['libraryType'] ) ? (string) sanitize_text_field( $_POST['libraryType'] ) : 'layout';
		$built_for    = isset( $_POST['postType'] ) ? (string) sanitize_text_field( $_POST['postType'] ) : 'page';

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$result = array();
		$post   = get_post( $id );

		$post_type = isset( $post->post_type ) ? $post->post_type : ET_BUILDER_LAYOUT_POST_TYPE;

		switch ( $post_type ) {
			case ET_BUILDER_LAYOUT_POST_TYPE:
				$layouts = et_pb_retrieve_templates( $library_type, '', 'all', '0', $built_for, 'all', array() );

				foreach ( $layouts as $layout ) {
					if ( $id === $layout['ID'] ) {
						$result = $layout;
						break;
					}
				}

				$result['savedShortcode'] = $result['shortcode'];

				if ( 'processed' === $content_type ) {
					if ( ! isset( $_POST['is_BB'] ) ) {
						$result['savedShortcode'] = et_fb_process_shortcode( $result['savedShortcode'] );
					} else {
						$post_content_processed = do_shortcode( $result['shortcode'] );
						$result['migrations']   = ET_Builder_Module_Settings_Migration::$migrated;
					}

					unset( $result['shortcode'] );
				}
				break;
			default:
				$post_content = $post->post_content;

				if ( 'processed' === $content_type ) {
					if ( ! isset( $_POST['is_BB'] ) ) {
						$post_content = et_fb_process_shortcode( stripslashes( $post_content ) );
					}
				}

				$result['savedShortcode'] = $post_content;
				$result['shortcode']      = $post_content;
				break;
		}

		if ( 'exported' === $content_type ) {
			$result['exported'] = get_exported_content( $result['shortcode'] );
		}

		$response = wp_json_encode(
			array(
				'success' => true,
				'data'    => $result,
			)
		);

		$tmp_dir = function_exists( 'sys_get_temp_dir' ) ? sys_get_temp_dir() : '/tmp';

		$tmp_file = tempnam( $tmp_dir, 'et' );

		et_()->WPFS()->put_contents( $tmp_file, $response );

		// Remove any previous buffered content since we're setting `Content-Length` header
		// based on $response value only.
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		header( 'Content-Length: ' . @filesize( $tmp_file ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- `filesize` may fail due to the permissions denied error.

		@unlink( $tmp_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- `unlink` may fail due to the permissions denied error.

		// Charset has to be explicitly mentioned when it is other than UTF-8.
		header( 'Content-Type: application/json; charset=' . esc_attr( get_option( 'blog_charset' ) ) );

		die( et_core_intentionally_unescaped( $response, 'html' ) );
	}

	/**
	 * AJAX Callback: Add/Remove Library terms for layout_tag and layout_category taxonomies.
	 *
	 * @since 4.17.0
	 *
	 * @global $_POST['payload'] Array with the terms list and update type (add/remove) for each.
	 *
	 * @return string JSON encoded.
	 */
	public function wp_ajax_et_builder_library_update_terms() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_send_json_error();
		}

		et_core_security_check( 'edit_posts', 'et_builder_library_update_terms', 'nonce' );
		$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

		if ( empty( $payload ) ) {
			wp_send_json_error();
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

		wp_send_json_success(
			array(
				'newFilters'        => $new_terms,
				'filterType'        => $filter_type,
				'localLibraryTerms' => [
					'layout_category' => et_fb_prepare_library_terms(),
					'layout_tag'      => et_fb_prepare_library_terms( 'layout_tag' ),
				],
			)
		);
	}

	/**
	 * AJAX Callback: Remove the Library layout after it was moved to the Cloud.
	 *
	 * @since 4.17.0
	 *
	 * @global $_POST['payload'] Array with the layout data to remove.
	 *
	 * @return void|string JSON encoded in case of empty payload
	 */
	public function wp_ajax_et_builder_toggle_cloud_status() {
		et_core_security_check( 'edit_posts', 'et_builder_library_toggle_item_location', 'nonce' );
		$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

		if ( empty( $payload ) ) {
			wp_send_json_error();
		}

		$post_id = absint( $payload['id'] );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error();
		}

		$unsupported_post_types = array(
			ET_BUILDER_LAYOUT_POST_TYPE,
			ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
			ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
			ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
			ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
			ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE,
		);

		if ( ! in_array( get_post_type( $post_id ), $unsupported_post_types, true ) ) {
			wp_send_json_error();
		}

		wp_delete_post( $post_id, true );

		wp_send_json_success(
			array(
				'localLibraryTerms' => [
					'layout_category' => et_fb_prepare_library_terms(),
					'layout_tag'      => et_fb_prepare_library_terms( 'layout_tag' ),
				],
			)
		);
	}

	/**
	 * AJAX Callback: Save the temp layout into database with the 'draft' status
	 * Uses {@see et_pb_create_layout} to submit the library post
	 *
	 * @since 4.17.0
	 *
	 * @global $_POST['payload'] Array with the layout data to create.
	 *
	 * @return void
	 */
	public function wp_ajax_et_builder_library_save_temp_layout() {
		et_core_security_check( 'edit_posts', 'et_fb_save_library_modules_nonce', 'nonce' );

		$payload     = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.
		$is_draft    = 'draft' === $payload['postStatus'];
		$name        = sanitize_text_field( self::$_->array_get( $payload, 'itemName', '' ) );
		$content     = self::$_->array_get( $payload, 'itemContent', '' );
		$prefix      = esc_html__( 'Edit Cloud Item', 'et_builder' );
		$name        = $is_draft ? $prefix . ': ' . $name : $name;
		$layout_type = self::$_->array_get( $payload, 'itemType', 'layout' );

		$tax_input = array(
			'layout_type'  => sanitize_text_field( $layout_type ),
			'module_width' => sanitize_text_field( self::$_->array_get( $payload, 'moduleWidth', 'regular' ) ),
		);

		if ( 'row' === $layout_type ) {
			$meta_input['_et_pb_row_layout'] = $payload['rowLayout'];
		}

		if ( 'module' === $layout_type && isset( $payload['moduleType'] ) ) {
			$meta_input['_et_pb_module_type'] = $payload['moduleType'];
		}

		$meta_input['_et_pb_built_for_post_type'] = 'page';

		$new_layout_id = et_pb_create_layout( $name, $content, $meta_input, $tax_input, '', '', $post_status = $payload['postStatus'] );

		wp_send_json_success(
			array(
				'layoutId' => $new_layout_id,
			)
		);
	}

	/**
	 * AJAX Callback: Removes temp layout from the website
	 *
	 * @since 4.17.0
	 *
	 * @global $_POST['payload'] Array with the layout id to remove.
	 *
	 * @return void
	 */
	public function wp_ajax_et_builder_library_remove_temp_layout() {
		et_core_security_check( 'edit_posts', 'et_fb_remove_library_modules_nonce', 'nonce' );
		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : '';

		$module_presets_manager = ET_Builder_Global_Presets_Settings::instance();
		$module_presets_manager->clear_temp_presets();

		if ( 0 === $post_id ) {
			$library_layouts = ET_Builder_Post_Type_Layout::instance();

			$draft_posts = $library_layouts->query()->run( array( 'post_status' => array( 'draft' ) ) );

			if ( ! empty( $draft_posts ) ) {
				if ( is_array( $draft_posts ) ) {
					// Several posts were returned.
					foreach ( $draft_posts as $post ) {
						if ( ! current_user_can( 'edit_post', $post->ID ) ) {
							continue;
						}

						wp_delete_post( $post->ID, true );
					}
				} else {
					if ( ! current_user_can( 'edit_post', $draft_posts->ID ) ) {
						wp_send_json_error();
					}

					// Single post was returned.
					wp_delete_post( $draft_posts->ID, true );
				}
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				wp_send_json_error();
			}

			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * AJAX Callback: Removes temp presets from the website
	 *
	 * @since 4.17.0
	 *
	 * @return void
	 */
	public function wp_ajax_et_builder_library_clear_temp_presets() {
		et_core_security_check( 'edit_posts', 'et_fb_clear_temp_presets_nonce', 'nonce' );

		$module_presets_manager = ET_Builder_Global_Presets_Settings::instance();
		$module_presets_manager->clear_temp_presets();
	}

	/**
	 * Returns 'publish' string to set the post correct status for restored library items.
	 *
	 * @since 4.17.0
	 *
	 * @return string new post status.
	 */
	public static function et_builder_set_untrash_status() {
		return 'publish';
	}

	/**
	 * AJAX Callback: Upload thumbnail and assign it to specified post.
	 *
	 * @since 4.17.0
	 *
	 * @global $_FILES['imageFile'] File to upload.
	 * @global $_POST['postId'] Post id to set thumbnail for.
	 *
	 * @return void
	 */
	public function wp_ajax_et_builder_library_upload_thumbnail() {
		et_core_security_check( 'edit_posts', 'et_builder_library_update_layout', 'nonce' );

		$post_id       = isset( $_POST['postId'] ) ? (int) $_POST['postId'] : '';
		$image_url_raw = isset( $_POST['imageURL'] ) ? esc_url_raw( $_POST['imageURL'] ) : ''; // phpcs:ignore ET.Sniffs.ValidVariableName.VariableNotSnakeCase -- This is valid format for the property in the Cloud App.

		// Upload and set featured image.
		if ( $image_url_raw && '' !== $image_url_raw ) {
			$upload = media_sideload_image( $image_url_raw, $post_id, $post_id, 'id' );

			$attachment_id  = is_wp_error( $upload ) ? 0 : $upload;
			$image_url      = get_attached_file( $attachment_id );
			$image_metadata = wp_generate_attachment_metadata( $attachment_id, $image_url );

			wp_update_attachment_metadata( $attachment_id, $image_metadata );

			$result = set_post_thumbnail( $post_id, $attachment_id );

			wp_send_json_success();
		}
	}

	/**
	 * AJAX Callback: Update the library item (layout/section/row/module). Following updates supported:
	 * - Duplicate
	 * - Edit Categories/Tags. New categories/tags can be created as well.
	 * - Rename
	 * - Toggle Favorite status
	 * - Delete
	 *
	 * @since 4.17.0
	 *
	 * @global $_POST['payload'] Array with the update details.
	 *
	 * @return string JSON encoded with the updated item details and new terms (which could be created in Duplicate action)
	 */
	public function wp_ajax_et_builder_library_update_item() {
		et_core_security_check( 'edit_posts', 'et_builder_library_update_layout', 'nonce' );
		$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

		if ( empty( $payload ) ) {
			wp_send_json_error();
		}

		$update_details = $payload['update_details'];
		$update_type    = $update_details['updateType'];
		$item_id        = intval( $payload['item_id'] );
		$new_id         = '';
		$categories     = empty( $update_details['itemCategories'] ) ? [] : array_unique( array_map( 'intval', $update_details['itemCategories'] ) );
		$tags           = empty( $update_details['itemTags'] ) ? [] : array_unique( array_map( 'intval', $update_details['itemTags'] ) );
		$new_categories = array();
		$new_tags       = array();

		$item_update = array(
			'ID' => $item_id,
		);

		$is_library_post_type = 'et_pb_layout' === get_post_type( $item_id );

		if ( ! empty( $update_details['newCategoryName'] ) && current_user_can( 'manage_categories' ) ) {
			$new_names_array = explode( ',', $update_details['newCategoryName'] );
			foreach ( $new_names_array as $new_name ) {
				if ( '' !== $new_name ) {
					$new_term = wp_insert_term( $new_name, 'layout_category' );

					if ( ! is_wp_error( $new_term ) ) {
						$categories[] = $new_term['term_id'];

						$new_categories[] = array(
							'name'  => $new_name,
							'id'    => $new_term['term_id'],
							'count' => 1,
						);
					} elseif ( ! empty( $new_term->error_data ) && ! empty( $new_term->error_data['term_exists'] ) ) {
						$categories[] = $new_term->error_data['term_exists'];
					}
				}
			}
		}

		if ( ! empty( $update_details['newTagName'] ) && current_user_can( 'manage_categories' ) ) {
			$new_names_array = explode( ',', $update_details['newTagName'] );

			foreach ( $new_names_array as $new_name ) {
				if ( '' !== $new_name ) {
					$new_term = wp_insert_term( $new_name, 'layout_tag' );

					if ( ! is_wp_error( $new_term ) ) {
						$tags[] = $new_term['term_id'];

						$new_tags[] = array(
							'name'  => $new_name,
							'id'    => $new_term['term_id'],
							'count' => 1,
						);
					} elseif ( ! empty( $new_term->error_data ) && ! empty( $new_term->error_data['term_exists'] ) ) {
						$tags[] = $new_term->error_data['term_exists'];
					}
				}
			}
		}

		switch ( $update_type ) {
			case 'duplicate':
			case 'duplicate_and_delete':
			case 'duplicate_premade_item':
			case 'save_existing_page':
			case 'split_layout':
			case 'split_section':
			case 'split_row':
				$is_item_from_cloud = isset( $update_details['shortcode'] );
				$title              = sanitize_text_field( $update_details['itemName'] );
				$meta_input         = array();
				$item_thumbnail     = false;

				if ( $is_item_from_cloud ) {
					$content         = $update_details['shortcode'];
					$built_for       = 'page';
					$scope           = ! empty( $update_details['global'] ) && 'on' === $update_details['global'] ? 'global' : 'non_global';
					$layout_type     = isset( $update_details['layoutType'] ) ? sanitize_text_field( $update_details['layoutType'] ) : 'layout';
					$module_width    = isset( $update_details['moduleWidth'] ) ? sanitize_text_field( $update_details['moduleWidth'] ) : 'regular';
					$favorite_status = isset( $update_details['favoriteStatus'] ) && 'on' === sanitize_text_field( $update_details['favoriteStatus'] ) ? 'favorite' : '';

					if ( 'row' === $layout_type ) {
						$meta_input['_et_pb_row_layout'] = $update_details['rowLayout'];
					}

					if ( 'module' === $layout_type ) {
						$meta_input['_et_pb_module_type'] = $update_details['moduleType'];
					}

					if ( '' !== $favorite_status ) {
						$meta_input['favorite_status'] = $favorite_status;
					}
				} else {
					$content        = get_the_content( null, false, $item_id );
					$built_for      = get_post_meta( $item_id, '_et_pb_built_for_post_type', true );
					$module_width   = wp_get_post_terms( $item_id, 'module_width', array( 'fields' => 'names' ) );
					$module_width   = is_wp_error( $module_width ) ? 'regular' : sanitize_text_field( $module_width[0] );
					$layout_type    = wp_get_post_terms( $item_id, 'layout_type', array( 'fields' => 'names' ) );
					$layout_type    = is_wp_error( $layout_type ) || '' === $layout_type ? 'layout' : sanitize_text_field( $layout_type[0] );
					$item_thumbnail = get_post_thumbnail_id( $item_id );

					if ( ! empty( $update_details['global'] ) ) {
						$scope = 'on' === $update_details['global'] ? 'global' : 'non_global';
					} else {
						$scope = wp_get_post_terms( $item_id, 'scope', array( 'fields' => 'names' ) );
						$scope = is_wp_error( $scope ) ? 'non_global' : sanitize_text_field( $scope[0] );
					}

					if ( 'row' === $layout_type ) {
						$row_layout = get_post_meta( $item_id, '_et_pb_row_layout', true );

						$meta_input['_et_pb_row_layout'] = $row_layout;
					}

					if ( 'module' === $layout_type ) {
						$module_type = get_post_meta( $item_id, '_et_pb_module_type', true );

						$meta_input['_et_pb_module_type'] = $module_type;
					}
				}

				$meta_input['_et_pb_built_for_post_type'] = $built_for;

				$new_item = array(
					'post_title'   => $title,
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => 'et_pb_layout',
					'tax_input'    => array(
						'layout_category' => $categories,
						'layout_tag'      => $tags,
						'layout_type'     => $layout_type,
						'scope'           => $scope,
						'module_width'    => $module_width,
					),
					'meta_input'   => $meta_input,
				);

				$new_id = wp_insert_post( $new_item );

				if ( $item_thumbnail ) {
					set_post_thumbnail( $new_id, $item_thumbnail );
				}
				break;
			case 'edit_cats':
				if ( ! current_user_can( 'manage_categories' ) ) {
					return;
				}

				wp_set_object_terms( $item_id, $categories, 'layout_category' );
				wp_set_object_terms( $item_id, $tags, 'layout_tag' );
				break;
			case 'rename':
				if ( ! current_user_can( 'edit_post', $item_id ) ) {
					return;
				}

				$item_update['post_title'] = sanitize_text_field( $update_details['itemName'] );
				wp_update_post( $item_update );
				break;
			case 'toggle_fav':
				if ( ! current_user_can( 'edit_post', $item_id ) ) {
					return;
				}

				$favorite_status = 'on' === sanitize_text_field( $update_details['favoriteStatus'] ) ? 'favorite' : '';
				update_post_meta( $item_id, 'favorite_status', $favorite_status );

				break;
			case 'delete':
				if ( current_user_can( 'delete_post', $item_id ) && $is_library_post_type ) {
					wp_trash_post( $item_id );
				}
				break;
			case 'delete_permanently':
				if ( current_user_can( 'delete_post', $item_id ) && $is_library_post_type ) {
					wp_delete_post( $item_id, true );
				}
				break;
			case 'restore':
				if ( ! current_user_can( 'edit_post', $item_id ) || ! $is_library_post_type ) {
					return;
				}

				// wp_untrash_post() restores the post to `draft` by default, we have to set `publish` status via filter.
				add_filter( 'wp_untrash_post_status', array( 'ET_Builder_Library', 'et_builder_set_untrash_status' ) );
				wp_untrash_post( $item_id );
				remove_filter( 'wp_untrash_post_status', array( 'ET_Builder_Library', 'et_builder_set_untrash_status' ) );
				break;
		}

		$processed_new_tags = array();
		$processed_new_cats = array();

		$updated_tags = get_terms(
			array(
				'taxonomy'   => 'layout_tag',
				'hide_empty' => false,
			)
		);

		$updated_categories = get_terms(
			array(
				'taxonomy'   => 'layout_category',
				'hide_empty' => false,
			)
		);

		if ( ! empty( $updated_tags ) ) {
			foreach ( $updated_tags as $single_tag ) {
				$processed_new_tags[] = array(
					'name'     => $single_tag->name,
					'id'       => $single_tag->term_id,
					'count'    => $single_tag->count,
					'location' => 'local',
				);
			}
		}

		if ( ! empty( $updated_categories ) ) {
			foreach ( $updated_categories as $single_category ) {
				$processed_new_cats[] = array(
					'name'     => $single_category->name,
					'id'       => $single_category->term_id,
					'count'    => $single_category->count,
					'location' => 'local',
				);
			}
		}

		wp_send_json_success(
			array(
				'updatedItem'  => $item_id,
				'newItem'      => $new_id,
				'updateType'   => $update_type,
				'categories'   => $categories,
				'tags'         => $tags,
				'updatedTerms' => array(
					'categories' => $processed_new_cats,
					'tags'       => $processed_new_tags,
				),
			)
		);
	}

	/**
	 * AJAX Callback: Convert the library item (row/module). Following updates supported:
	 * - Convert Module To Row
	 * - Convert Module To Section
	 * - Convert Row To Section
	 *
	 * @since 4.23.1
	 *
	 * @global $_POST['payload'] Array with the item details.
	 */
	public function wp_ajax_et_builder_library_convert_item() {
		et_core_security_check( 'edit_posts', 'et_builder_library_convert_layout', 'nonce' );
		$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : []; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done at the time of accessing value.

		if ( empty( $payload ) ) {
			wp_send_json_error();
		}

		$builder_version   = '_builder_version="' . ET_BUILDER_VERSION . '"';
		$section_start     = '[et_pb_section fb_built="1" fullwidth="off" ' . $builder_version . ' _module_preset="default"]';
		$row_start         = '[et_pb_row ' . $builder_version . ' _module_preset="default" theme_builder_area="post_content"]';
		$column_start      = '[et_pb_column ' . $builder_version . ' _module_preset="default" type="4_4" theme_builder_area="post_content"]';
		$column_end        = '[/et_pb_column]';
		$row_end           = '[/et_pb_row]';
		$section_end       = '[/et_pb_section]';
		$fullwidth_wrapper = str_replace( 'fullwidth="off"', 'fullwidth="on" template_type="section"', $section_start );

		switch ( $payload['action'] ) {
			case 'convert_row_to_section':
				$wrapper_start = $section_start;
				$wrapper_end   = $section_end;
				$from_type     = 'row';
				$to_type       = 'section';
				break;

			case 'convert_module_to_row':
				$wrapper_start = $row_start . $column_start;
				$wrapper_end   = $column_end . $row_end;
				$from_type     = 'module';
				$to_type       = 'row';
				break;

			case 'convert_module_to_section':
				$wrapper_start = $section_start . $row_start . $column_start;
				$wrapper_end   = $column_end . $row_end . $section_end;
				$from_type     = 'module';
				$to_type       = 'section';
				break;
		}

		/**
		 * For cloud item.
		 */
		if ( isset( $payload['content'] ) ) {
			if ( 'convert_module_to_section' === $payload['action'] && 'fullwidth' === $payload['item']['width'] ) {	
				// For fullwidth module, there is no row and column.
				$wrapper_start = $fullwidth_wrapper;
				$wrapper_end   = $section_end;
			}

			$post_content = $wrapper_start . wp_unslash( reset( $payload['content']['data'] ) ) . $wrapper_end;

			wp_send_json_success( $post_content );
		}

		/**
		 * For local item.
		 */
		$post_id = isset( $payload['item']['id'] ) ? absint( $payload['item']['id'] ) : 0;
		$item    = get_post( $post_id );

		if ( ! $item ) {
			wp_send_json_error();
		}

		if ( 'convert_module_to_section' === $payload['action'] ) {
			$module_type = get_post_meta( $post_id, '_et_pb_module_type', true );

			if ( false !== strpos( $module_type, 'et_pb_fullwidth' ) ) {
				// For fullwidth module, there is no row and column.
				$wrapper_start = $fullwidth_wrapper;
				$wrapper_end   = $section_end;
			}
		}

		$scope_terms  = get_the_terms( $post_id, 'scope' );
		$scope_terms  = wp_list_pluck( $scope_terms, 'slug' );
		$is_global    = in_array( 'global', $scope_terms, true );
		$post_content = $wrapper_start . $item->post_content . $wrapper_end;

		$new_id = wp_insert_post(
			[
				'ID'           => $is_global ? 0 : $post_id, // If global item, create a new post.
				'post_content' => $post_content,
				'post_date'    => $item->post_date,
				'post_title'   => $item->post_title,
				'post_type'    => $item->post_type,
				'post_status'  => 'publish',
			]
		);

		if ( is_wp_error( $new_id ) ) {
			wp_send_json_error( $new_id->get_error_message() );
		}

		if ( ! $is_global ) {
			wp_remove_object_terms( $post_id, $from_type, 'layout_type' );
		}

		add_post_meta( $new_id, '_et_pb_built_for_post_type', 'page' );
		delete_post_meta( $new_id, '_et_pb_module_type' );
		wp_set_object_terms( $new_id, $to_type, 'layout_type' );

		wp_send_json_success( $post_content );
	}

	/**
	 * AJAX Callback: Gets Cloud access token from DB and send it to client.
	 *
	 * @since 4.17.0
	 *
	 * @return void
	 */
	public function wp_ajax_et_builder_library_get_cloud_token() {
		et_core_security_check( 'edit_posts', 'et_builder_library_get_cloud_token', 'nonce' );

		$access_token = get_transient( 'et_cloud_access_token' );

		wp_send_json_success(
			array(
				'accessToken' => $access_token,
			)
		);
	}

	/**
	 * AJAX Callback: Gets layouts data for the builder's library UI.
	 *
	 * @since 3.0.99
	 *
	 * @global $_POST['nonce'] Nonce: 'et_builder_library_get_layouts_data'.
	 *
	 * @return string|void $layouts_data JSON Encoded.
	 */
	public function wp_ajax_et_builder_library_get_layouts_data() {
		et_core_security_check( 'edit_posts', 'et_builder_library_get_layouts_data', 'nonce' );

		$library_type = isset( $_POST['et_library_type'] ) ? (string) sanitize_text_field( $_POST['et_library_type'] ) : 'layout';

		wp_send_json_success( $this->builder_library_layouts_data( $library_type ) );
	}

	/**
	 * AJAX Callback: Updates ET Account in database.
	 *
	 * @since 3.0.99
	 *
	 * @global $_POST['nonce']    Nonce: 'et_builder_library_update_account'.
	 * @global $_POST['username'] Username
	 * @global $_POST['api_key']  API Key
	 * @global $_POST['status']   Account Status
	 */
	public function wp_ajax_et_builder_library_update_account() {
		et_core_security_check( 'manage_options', 'et_builder_library_update_account', 'nonce' );

		$args = $_POST;

		if ( ! self::$_->all( $args ) ) {
			wp_send_json_error();
		}

		$args            = array_map( 'sanitize_text_field', $args );
		$updates_options = get_site_option( 'et_automatic_updates_options', array() );
		$account         = array(
			'username' => $args['et_username'],
			'api_key'  => $args['et_api_key'],
		);

		update_site_option( 'et_automatic_updates_options', array_merge( $updates_options, $account ) );
		update_site_option( 'et_account_status', $args['status'] );

		wp_send_json_success();
	}

	/**
	 * Filters out library tags and categories from Theme Builder settings.
	 * These taxonomies are not available on Frontend and user shouldn't be able to select it.
	 *
	 * @param bool   $initial_value original value.
	 * @param object $taxonomy taxonomy to check.
	 *
	 * @since 4.17.0
	 *
	 * @return bool
	 */
	public function tb_remove_unsupported_taxonomies( $initial_value, $taxonomy ) {
		if ( in_array( $taxonomy->name, array( 'layout_category', 'layout_tag' ), true ) ) {
			return false;
		}

		return $initial_value;
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 4.17.0
	 */
	public function enqueue_scripts() {
		// Enqueue resource for edit session expire page.
		if ( $this->vb_is_editing_session_expired() ) {
			et_core_register_admin_assets();

			wp_enqueue_style( 'et-core-admin' );
			wp_enqueue_script( 'et-core-admin' );
		}
	}

	/**
	 * Render modal to display a message when editing session expire.
	 *
	 * @since 4.17.0
	 */
	public function render_session_expired_modal() {
		if ( ! $this->vb_is_editing_session_expired() ) {
			return;
		}
		?>
		<div class="et-core-modal-overlay et-builder-session-expired-modal et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">
						<?php esc_html_e( 'Session Expired', 'et_builder' ); ?>
					</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div id="et-builder-session-expired-modal-content">
					<div class="et-core-modal-content">
						<p>
							<?php
								esc_html_e( 'Your Cloud item editing session has expired.', 'et_builder' );
							?>
						</p>
					</div>
					<a class="et-core-modal-action" href="#" data-et-core-modal="close">
						<?php esc_html_e( 'Close', 'et_builder' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Determine whether editing session is expired.
	 *
	 * @since 4.17.0
	 */
	public function vb_is_editing_session_expired() {
		global $wp_query;
		// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		return $wp_query->is_404() && isset( $_GET['cloudItem'] );
	}

	/**
	 * Enqueues library-related styles and scripts in the admin.
	 * {@see 'admin_enqueue_scripts'}
	 *
	 * @param string $page The current admin page.
	 *
	 * @since 3.0.99
	 */
	public function wp_hook_admin_enqueue_scripts( $page ) {
		global $typenow;

		et_core_load_main_fonts();

		wp_enqueue_style( 'et-builder-notification-popup-styles', ET_BUILDER_URI . '/styles/notification_popup_styles.css', array(), ET_BUILDER_PRODUCT_VERSION );

		if ( 'et_pb_layout' === $typenow ) {
			$new_layout_modal = et_pb_generate_new_layout_modal();

			wp_enqueue_style( 'library-styles', ET_BUILDER_URI . '/styles/library_pages.css', array( 'et-core-admin' ), ET_BUILDER_PRODUCT_VERSION );
			$deps = array(
				'jquery',
			);
			wp_enqueue_script( 'library-scripts', ET_BUILDER_URI . '/scripts/library_scripts.js', $deps, ET_BUILDER_PRODUCT_VERSION, false );

			$new_template_options_data = array(
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'et_admin_load_nonce' => wp_create_nonce( 'et_admin_load_nonce' ),
				'modal_output'        => $new_layout_modal,
			);
			wp_localize_script( 'library-scripts', 'et_pb_new_template_options', $new_template_options_data );
		} else {
			wp_enqueue_script( 'et-builder-failure-notice', ET_BUILDER_URI . '/scripts/failure_notice.js', array( 'jquery' ), ET_BUILDER_PRODUCT_VERSION, false );
		}
	}

	/**
	 * Highlight Divi menu in admin when viewing layout tag.
	 *
	 * @param string $parent_file The parent file.
	 * @return string
	 */
	public function wp_hook_parent_file( $parent_file ) {
		global $submenu_file;
		if ( 'edit.php' === $parent_file && in_array( $submenu_file, self::$submenu_files, true ) ) {
			return 'et_divi_options';
		}
		return $parent_file;
	}

	/**
	 * Highlight Divi Library submenu in admin when viewing layout tag.
	 *
	 * @param string $submenu_file The submenu file.
	 * @return string
	 */
	public function wp_hook_submenu_file( $submenu_file ) {
		global $parent_file;
		if ( 'et_divi_options' === $parent_file && in_array( $submenu_file, self::$submenu_files, true ) ) {
			return 'edit.php?post_type=et_pb_layout';
		}
		return $submenu_file;
	}

	/**
	 * Remove the edit action links displayed for each term in the layout_category and layout_tag list table.
	 *
	 * @param string[] $actions An array of action links to be displayed.
	 * @param WP_Term  $tag     Term object.
	 */
	public function wp_filter_tag_row_actions( $actions, $tag ) {
		if ( in_array( $tag->taxonomy, array( 'layout_category', 'layout_tag' ), true ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}
}

ET_Builder_Library::instance();

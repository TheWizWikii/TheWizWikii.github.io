<?php

if ( ! defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ) {
	define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );
}

require_once ET_BUILDER_DIR . 'post/query/Layouts.php';


class ET_Builder_Post_Type_Layout extends ET_Core_Post_Type {

	/**
	 * @inheritDoc
	 */
	protected $_category_tax = 'layout_category';

	/**
	 * @inheritDoc
	 */
	protected $_owner = 'builder';

	/**
	 * @inheritDoc
	 */
	protected $_tag_tax = 'layout_pack';

	/**
	 * @inheritDoc
	 */
	public $name = ET_BUILDER_LAYOUT_POST_TYPE;

	/**
	 * ET_Builder_Post_Type_Layout constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( "add_meta_boxes_{$this->name}", array( $this, 'wp_hook_add_meta_boxes' ) );
		add_action( "manage_{$this->name}_posts_custom_column", array( $this, 'wp_hook_manage_posts_custom_column' ), 10, 2 );

		add_filter( "manage_{$this->name}_posts_columns", array( $this, 'wp_hook_manage_posts_columns' ) );
	}

	/**
	 * @inheritDoc
	 */
	protected function _before_register() {
		/**
		 * Filters {@see register_post_type()} args for the et_pb_layout post type.
		 *
		 * @deprecated Use {@see 'et_core_cpt_et_pb_layout_args'} instead.
		 *
		 * @since 3.1  Deprecated. See {@see 'et_core_cpt_et_pb_layout_args'}.
		 * @since 1.0
		 *
		 * @param $args
		 */
		$this->_args = apply_filters( 'et_pb_layout_args', $this->_args );
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_args() {
		return array(
			'can_export'         => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'public'             => false,
			'publicly_queryable' => self::is_publicly_queryable(),
			'query_var'          => false,
			'show_in_menu'       => false,
			'show_ui'            => true,
			'supports'           => array(
				'editor',
				'excerpt',
				'revisions',
				'thumbnail',
				'title',
			),
			'taxonomies'         => array(
				'layout_category',
				'layout_tag',
				'layout_pack',
				'layout_type',
				'module_width',
				'scope',
				'layout_location',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_labels() {
		return array(
			'add_new'            => esc_html_x( 'Add New', 'Layout', 'et_builder' ),
			'add_new_item'       => esc_html__( 'Add New Layout', 'et_builder' ),
			'all_items'          => esc_html__( 'All Layouts', 'et_builder' ),
			'edit_item'          => esc_html__( 'Edit Layout', 'et_builder' ),
			'name'               => esc_html__( 'Layouts', 'et_builder' ),
			'new_item'           => esc_html__( 'New Layout', 'et_builder' ),
			'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
			'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
			'parent_item_colon'  => '',
			'search_items'       => esc_html__( 'Search Layouts', 'et_builder' ),
			'singular_name'      => et_builder_i18n( 'Layout' ),
			'view_item'          => esc_html__( 'View Layout', 'et_builder' ),
		);
	}

	/**
	 * Get the class instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'cpt'.
	 * @param string $name The name/slug of the post object. Default is {@see self::$name}.
	 *
	 * @return self|null
	 */
	public static function instance( $type = 'cpt', $name = 'et_pb_layout' ) {
		if ( ! $instance = parent::instance( $type, $name ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Whether or not a layout is global.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function is_global( $post_id ) {
		$tax_name = ET_Builder_Post_Taxonomy_LayoutScope::instance()->name;

		if ( $terms = get_the_terms( $post_id, $tax_name ) ) {
			foreach ( $terms as $term ) {
				if ( 'global' === $term->name ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Whether or not a layout is Favorite.
	 *
	 * @param string $post_id Post ID.
	 *
	 * @return bool
	 */
	public function is_favorite( $post_id ) {
		return 'favorite' === get_post_meta( $post_id, 'favorite_status', true );
	}

	/**
	 * Determines if the Library's CPT and taxonomies should be publicly queryable for the current request.
	 *
	 * @sine ??
	 *
	 * @return bool
	 */
	public static function is_publicly_queryable() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$get     = $_GET;
		$actions = array(
			'et_fb_update_builder_assets',
			'et_fb_retrieve_builder_data',
		);
		$is_ajax = isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions );
		$is_VB   = ( '1' === self::$_->array_get( $get, 'et_fb' ) || $is_ajax ) && et_pb_is_allowed( 'use_visual_builder' );
		// phpcs:enable
		$is_wpcli = defined( 'WP_CLI' ) && WP_CLI;

		$has_preview = ! $is_VB && ! is_null( self::$_->array_get( $get, 'et_pb_preview', null ) );
		$is_preview  = $has_preview && et_core_security_check_passed( '', 'et_pb_preview_nonce', '', '_GET' );

		return $is_VB || $is_preview || $is_wpcli;
	}

	/**
	 * @return ET_Builder_Post_Query_Layouts
	 */
	public function query() {
		return new ET_Builder_Post_Query_Layouts( $this->name, $this->_category_tax, $this->_tag_tax );
	}

	/**
	 * Moves the excerpt meta box into the side column for this post type.
	 * {@see 'add_meta_boxes_{$type}'}
	 *
	 * @since 3.0.99
	 */
	public function wp_hook_add_meta_boxes() {
		remove_meta_box( 'postexcerpt', null, 'normal' );
		add_meta_box( 'postexcerpt', __( 'Description' ), 'post_excerpt_meta_box', null, 'side', 'default' );
	}

	/**
	 * Adds custom columns to the Divi Library admin page.
	 * {@see 'manage_{$post_type}_posts_columns'}
	 *
	 * @since 3.1    Relocated from `builder/layouts.php`.
	 * @since 2.5.7
	 */
	public function wp_hook_manage_posts_columns( $columns ) {
		$_new_columns = array();

		foreach ( $columns as $column_key => $column ) {
			$_new_columns[ $column_key ] = $column;

			if ( 'taxonomy-layout_type' === $column_key ) {
				$_new_columns['layout_global'] = esc_html__( 'Global', 'et_builder' );
			}
		}

		return $_new_columns;
	}

	/**
	 * Sets the content of our custom columns for each row on the Divi Library admin page.
	 * {@see 'manage_posts_custom_column'}
	 *
	 * @since 3.1    Relocated from `builder/layouts.php`.
	 * @since 2.5.7
	 */
	public function wp_hook_manage_posts_custom_column( $column_key, $post_id ) {
		switch ( $column_key ) {
			case 'layout_global':
				if ( $this->is_global( $post_id ) ) {
					echo '<span class="et-builder-library__icon--global" aria-label="Global"/>';
				} else {
					echo '<span aria-label="Not Global"/>';
				}

				break;
		}
	}
}

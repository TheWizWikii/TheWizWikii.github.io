<?php
/**
 * Init `et_tb_item` taxonomy.
 *
 * @since 4.18.0
 *
 * @package Builder
 */

if ( ! defined( 'ET_TB_ITEM_POST_TYPE' ) ) {
	define( 'ET_TB_ITEM_POST_TYPE', 'et_tb_item' );
}

require_once ET_THEME_BUILDER_DIR . 'post/query/TBItems.php';

/**
 * Class to handle `et_tb_item` post type.
 *
 * Registers TB Item.
 */
class ET_Builder_Post_Type_TBItem extends ET_Core_Post_Type {
	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $_category_tax = 'layout_category';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $_owner = 'builder';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $_tag_tax = 'layout_tag';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public $name = ET_TB_ITEM_POST_TYPE;

	/**
	 * {@inheritDoc}
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
			'show_ui'            => false,
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
				'et_tb_item_type',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _get_labels() {
		return array(
			'add_new'            => esc_html_x( 'Add New', 'Layout', 'et_builder' ),
			'add_new_item'       => esc_html__( 'Add New Theme Builder Item', 'et_builder' ),
			'all_items'          => esc_html__( 'All Theme Builder Items', 'et_builder' ),
			'edit_item'          => esc_html__( 'Edit Theme Builder Item', 'et_builder' ),
			'name'               => esc_html__( 'Theme Builder Items', 'et_builder' ),
			'new_item'           => esc_html__( 'New Theme Builder Item', 'et_builder' ),
			'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
			'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
			'parent_item_colon'  => '',
			'search_items'       => esc_html__( 'Search Theme Builder Items', 'et_builder' ),
			'singular_name'      => et_builder_i18n( 'Theme Builder Item' ),
			'view_item'          => esc_html__( 'View Theme Builder Item', 'et_builder' ),
		);
	}

	/**
	 * Get the class instance.
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'cpt'.
	 * @param string $name The name/slug of the post object. Default is {@see self::$name}.
	 *
	 * @return self|null
	 */
	public static function instance( $type = 'cpt', $name = ET_TB_ITEM_POST_TYPE ) {
		$instance = parent::instance( $type, $name );
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Returns TRUE when a layout is Favorite.
	 *
	 * @param string $post_id Post ID.
	 *
	 * @return bool
	 */
	public function is_favorite( $post_id ) {
		return 'favorite' === get_post_meta( $post_id, 'favorite_status', true );
	}

	/**
	 * Determines if TB Item Library's CPT and its taxonomies are publicly queryable for the current request.
	 *
	 * @return bool
	 */
	public static function is_publicly_queryable() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Nonce not required.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Nonce not required.
		$get = $_GET;

		// phpcs:ignore ET.Sniffs.ValidVariableName.VariableNotSnakeCase -- TB is an acronym.
		$is_TB = ( 'et_theme_builder' === self::$_->array_get( $get, 'page' ) || wp_doing_ajax() );
		// phpcs:enable

		// phpcs:ignore ET.Sniffs.ValidVariableName.VariableNotSnakeCase -- TB is an acronym.
		return $is_TB;
	}

	/**
	 * Returns an instance of ET_Builder_Post_Query_TBItems.
	 *
	 * The instance can then be used to get results.
	 *
	 * @see ET_Builder_Post_Query_TBItems::run()
	 *
	 * @return ET_Builder_Post_Query_TBItems
	 */
	public function query() {
		return new ET_Builder_Post_Query_TBItems( $this->name, $this->_category_tax, $this->_tag_tax );
	}
}

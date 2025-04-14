<?php
/**
 * Register ET_THEME_OPTIONS_POST_TYPE.
 *
 * @since ??
 *
 * @package Divi
 * @subpackage Cloud
 * @since ??
 */

/**
 * Class to handle `et_theme_options` post type.
 *
 * Registers TO Item.
 */
class ET_Post_Type_Theme_Options extends ET_Core_Post_Type {
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
	public $name = ET_THEME_OPTIONS_POST_TYPE;

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
			'publicly_queryable' => false,
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
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _get_labels() {
		return array(
			'add_new'            => esc_html_x( 'Add New', 'Layout', 'et_builder' ),
			'add_new_item'       => esc_html__( 'Add New Theme Options', 'et_builder' ),
			'all_items'          => esc_html__( 'All Theme Options', 'et_builder' ),
			'edit_item'          => esc_html__( 'Edit Theme Options', 'et_builder' ),
			'name'               => esc_html__( 'Theme Options', 'et_builder' ),
			'new_item'           => esc_html__( 'New Theme Options', 'et_builder' ),
			'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
			'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
			'parent_item_colon'  => '',
			'search_items'       => esc_html__( 'Search Theme Options', 'et_builder' ),
			'singular_name'      => esc_html__( 'Theme Options', 'et_builder' ),
			'view_item'          => esc_html__( 'View Theme Options', 'et_builder' ),
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
	public static function instance( $type = 'cpt', $name = ET_THEME_OPTIONS_POST_TYPE ) {
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
}

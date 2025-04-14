<?php


class ET_Builder_Post_Taxonomy_LayoutPack extends ET_Core_Post_Taxonomy {

	/**
	 * @inheritDoc
	 */
	protected $_owner = 'builder';

	/**
	 * @inheritDoc
	 */
	public $name = 'layout_pack';

	/**
	 * @inheritDoc
	 */
	protected function _get_args() {
		return array(
			'hierarchical'       => false,
			'show_ui'            => false,
			'show_admin_column'  => false,
			'query_var'          => true,
			'show_in_nav_menus'  => false,
			'publicly_queryable' => ET_Builder_Post_Type_Layout::is_publicly_queryable(),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_labels() {
		return array(
			'add_new'                    => esc_html_x( 'Add New', 'Layout Pack', 'et_builder' ),
			'add_new_item'               => esc_html__( 'Add New Layout Pack', 'et_builder' ),
			'all_items'                  => esc_html__( 'All Layout Packs', 'et_builder' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used layout packs', 'et_builder' ),
			'edit_item'                  => esc_html__( 'Edit Layout Pack', 'et_builder' ),
			'name'                       => esc_html__( 'Packs', 'et_builder' ),
			'new_item'                   => esc_html__( 'New Layout Pack', 'et_builder' ),
			'new_item_name'              => esc_html__( 'New Layout Pack Name', 'et_builder' ),
			'no_terms'                   => esc_html__( 'No layout packs', 'et_builder' ),
			'not_found'                  => esc_html__( 'No layout packs found', 'et_builder' ),
			'search_items'               => esc_html__( 'Search Layout Packs', 'et_builder' ),
			'separate_items_with_commas' => esc_html__( 'Separate layout packs with commas', 'et_builder' ),
			'singular_name'              => esc_html__( 'Layout Pack', 'et_builder' ),
			'update_item'                => esc_html__( 'Update Layout Pack', 'et_builder' ),
			'view_item'                  => esc_html__( 'View Layout Pack', 'et_builder' ),
		);
	}

	/**
	 * Get the class instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'taxonomy'.
	 * @param string $name The name/slug of the post object. Default is {@see self::$name}.
	 *
	 * @return self|null
	 */
	public static function instance( $type = 'taxonomy', $name = 'layout_pack' ) {
		if ( ! $instance = parent::instance( $type, $name ) ) {
			$instance = new self();
		}

		return $instance;
	}
}

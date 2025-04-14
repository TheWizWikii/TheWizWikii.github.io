<?php


class ET_Builder_Post_Taxonomy_LayoutCategory extends ET_Core_Post_Taxonomy {

	/**
	 * @inheritDoc
	 */
	protected $_owner = 'builder';

	/**
	 * @inheritDoc
	 */
	public $name = 'layout_category';

	/**
	 * @inheritDoc
	 */
	protected function _get_args() {
		return array(
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'show_in_nav_menus'  => false,
			'publicly_queryable' => ET_Builder_Post_Type_Layout::is_publicly_queryable(),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_labels() {
		return array();
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
	public static function instance( $type = 'taxonomy', $name = 'layout_category' ) {
		if ( ! $instance = parent::instance( $type, $name ) ) {
			$instance = new self();
		}

		return $instance;
	}
}

<?php


class ET_Builder_Post_Taxonomy_LayoutWidth extends ET_Core_Post_Taxonomy {

	/**
	 * @inheritDoc
	 */
	protected $_owner = 'builder';

	/**
	 * @inheritDoc
	 */
	public $name = 'module_width';

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
			'name' => esc_html__( 'Module Width', 'et_builder' ),
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
	public static function instance( $type = 'taxonomy', $name = 'module_width' ) {
		if ( ! $instance = parent::instance( $type, $name ) ) {
			$instance = new self();
		}

		return $instance;
	}
}

<?php
/**
 * Init layout_tag taxonomy.
 *
 * @package Builder
 */

/**
 * Class to init layout_tag taxonomy.
 */
class ET_Builder_Post_Taxonomy_LayoutTag extends ET_Core_Post_Taxonomy {

	/**
	 * Owner name.
	 *
	 * @since 4.17.0
	 * @var   string
	 */
	protected $_owner = 'builder';

	/**
	 * Taxonomy key.
	 *
	 * @since 4.17.0
	 * @var   string
	 */
	public $name = 'layout_tag';

	/**
	 * {@inheritDoc}
	 */
	protected function _get_args() {
		return array(
			'hierarchical'       => false,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => true,
			'show_in_nav_menus'  => false,
			'publicly_queryable' => ET_Builder_Post_Type_Layout::is_publicly_queryable(),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _get_labels() {
		return array(
			'name' => esc_html__( 'Tags', 'et_builder' ),
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
	public static function instance( $type = 'taxonomy', $name = 'layout_tag' ) {
		$instance = parent::instance( $type, $name );

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}

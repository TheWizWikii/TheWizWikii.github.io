<?php
/**
 * Init `et_tb_item` taxonomy.
 *
 * @since 4.18.0
 *
 * @package Builder
 */

/**
 * Class to handle `et_tb_item` taxonomy.
 *
 * Registers TB Item's Type taxonomy.
 */
class ET_Builder_Post_Taxonomy_TBItemType extends ET_Core_Post_Taxonomy {

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
	public $name = 'et_tb_item_type';

	/**
	 * {@inheritDoc}
	 */
	protected function _get_args() {
		return array(
			'hierarchical'       => false,
			'show_ui'            => false,
			'show_admin_column'  => true,
			'query_var'          => true,
			'show_in_nav_menus'  => false,
			'publicly_queryable' => ET_Builder_Post_Type_TBItem::is_publicly_queryable(),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _get_labels() {
		return array(
			'name' => esc_html__( 'Type', 'et_builder' ),
		);
	}

	/**
	 * Get the class instance.
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'taxonomy'.
	 * @param string $name The name/slug of the post object. Default is {@see self::$name}.
	 *
	 * @return ET_Builder_Post_Taxonomy_TBItemType|null
	 */
	public static function instance( $type = 'taxonomy', $name = 'et_tb_item_type' ) {
		$instance = parent::instance( $type, $name );
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}

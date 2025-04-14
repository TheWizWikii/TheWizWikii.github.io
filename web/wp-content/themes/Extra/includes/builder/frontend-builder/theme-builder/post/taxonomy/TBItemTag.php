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
 * Registers TB Item's Tag taxonomy.
 */
class ET_Builder_Post_Taxonomy_TBItemTag extends ET_Core_Post_Taxonomy {

	/**
	 * Taxonomy key.
	 *
	 * @var string
	 */
	public $name = 'et_tb_item_tag';
	/**
	 * Owner name.
	 *
	 * @var string
	 */
	protected $_owner = 'builder';

	/**
	 * Get the class instance.
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'taxonomy'.
	 * @param string $name The name/slug of the post object. Default is {@see self::$name}.
	 *
	 * @return ET_Builder_Post_Taxonomy_TBItemTag|null
	 */
	public static function instance( $type = 'taxonomy', $name = 'et_tb_item_tag' ) {
		$instance = parent::instance( $type, $name );

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

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
			'name' => esc_html__( 'Tags', 'et_builder' ),
		);
	}
}

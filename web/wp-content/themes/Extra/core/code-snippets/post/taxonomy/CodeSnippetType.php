<?php
/**
 * Init ET_CODE_SNIPPET_TAXONOMY_TYPE taxonomy.
 *
 * @since 4.19.0
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.19.0
 */

/**
 * Register ET_CODE_SNIPPET_TAXONOMY_TYPE taxonomy.
 */
class ET_Builder_Post_Taxonomy_CodeSnippetType extends ET_Core_Post_Taxonomy {

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
	public $name = 'et_code_snippet_type';

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
			'publicly_queryable' => true,
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
	 * @return ET_Builder_Post_Taxonomy_CodeSnippet_Type
	 */
	public static function instance( $type = 'taxonomy', $name = 'et_code_snippet_type' ) {
		$instance = parent::instance( $type, $name );
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}

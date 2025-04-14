<?php


abstract class ET_Core_Post_Taxonomy extends ET_Core_Post_Object {

	/**
	 * The `$args` array used when registering this taxonomy.
	 *
	 * @since 3.0.99
	 * @var   array
	 */
	protected $_args;

	/**
	 * The WP Taxonomy object for this instance.
	 *
	 * @since 3.0.99
	 * @var   WP_Taxonomy
	 */
	protected $_wp_object;

	/**
	 * Taxonomy key.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $name;

	/**
	 * The post types to which this taxonomy applies.
	 *
	 * @since 3.0.99
	 * @var   array
	 */
	public $post_types;

	/**
	 * This taxonomy's terms.
	 *
	 * @var WP_Term[]
	 */
	public $terms;

	/**
	 * @inheritDoc
	 */
	public $wp_type = 'taxonomy';

	/**
	 * ET_Core_Post_Taxonomy constructor.
	 */
	public function __construct() {
		parent::__construct();

		$name = $this->name;

		/**
		 * Filters the supported post types for a custom taxonomy. The dynamic portion of the
		 * filter name, $name, refers to the name of the custom taxonomy.
		 *
		 * @since 3.0.99
		 *
		 * @param array
		 */
		$this->post_types = apply_filters( "et_core_taxonomy_{$name}_post_types", $this->post_types );
	}

	/**
	 * Get the terms for this taxonomy.
	 *
	 * @return array|int|WP_Error|WP_Term[]
	 */
	public function get() {
		if ( is_null( $this->terms ) ) {
			$this->terms = get_terms( $this->name, array( 'hide_empty' => false ) );
		}

		return $this->terms;
	}

	/**
	 * Get a derived class instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'taxonomy'.
	 * @param string $name The name/slug of the derived object. Default is an empty string.
	 *
	 * @return self|null
	 */
	public static function instance( $type = 'taxonomy', $name = '' ) {
		return parent::instance( $type, $name );
	}
}

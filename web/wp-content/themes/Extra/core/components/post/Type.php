<?php


abstract class ET_Core_Post_Type extends ET_Core_Post_Object {

	/**
	 * The `$args` array used when registering this post type.
	 *
	 * @since 3.0.99
	 * @var   array
	 */
	protected $_args;

	/**
	 * The name of the primary category-style taxonomy for this post type.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	protected $_category_tax = '';

	/**
	 * The name of the primary tag-style taxonomy for this post type.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	protected $_tag_tax = '';

	/**
	 * The WP Post Type object for this instance.
	 *
	 * @since 3.0.99
	 * @var   WP_Post_Type
	 */
	protected $_wp_object;

	/**
	 * Post type key.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $name;

	/**
	 * @inheritDoc
	 */
	public $wp_type = 'cpt';

	/**
	 * @return ET_Core_Post_Query
	 */
	public function query() {
		return new ET_Core_Post_Query( $this->name, $this->_category_tax, $this->_tag_tax );
	}
}

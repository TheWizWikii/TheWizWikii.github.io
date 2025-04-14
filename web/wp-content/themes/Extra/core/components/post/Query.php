<?php

class ET_Core_Post_Query {

	/**
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * Whether or not to negate the next query arg that is set. Default 'false'.
	 *
	 * @since 3.0.99
	 * @var   bool
	 */
	protected $_negate = false;

	/**
	 * The query result.
	 *
	 * @since 3.0.99
	 * @var   WP_Post|WP_Post[]
	 */
	protected $_query_result;

	/**
	 * The args that will be passed to {@see WP_Query} the next time {@see self::run()} is called.
	 *
	 * @since 3.0.99
	 * @var   array
	 */
	protected $_wp_query_args;

	/**
	 * The name of the primary category-style taxonomy for this post type.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $category_tax;

	/**
	 * The post type (slug) for this instance.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $post_type;

	/**
	 * The name of the primary tag-style taxonomy for this post type.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $tag_tax;

	/**
	 * ET_Core_Post_Query constructor.
	 *
	 * @since 3.0.99
	 *
	 * @param string $post_type    See {@see self::$post_type}
	 * @param string $category_tax See {@see self::$category_tax}
	 * @param string $tag_tax      See {@see self::$tag_tax}
	 */
	public function __construct( $post_type = '', $category_tax = '', $tag_tax = '' ) {
		$this->post_type    = $this->post_type ? $this->post_type : $post_type;
		$this->category_tax = $this->category_tax ? $this->category_tax : $category_tax;
		$this->tag_tax      = $this->tag_tax ? $this->tag_tax : $tag_tax;

		$this->_wp_query_args = array(
			'post_type'      => $this->post_type,
			'posts_per_page' => -1,
		);

		if ( ! self::$_ ) {
			self::$_ = ET_Core_Data_Utils::instance();
		}
	}

	/**
	 * Adds a meta query to the WP Query args for this instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $key    The meta key.
	 * @param string $value  The meta value.
	 * @param bool   $negate Whether or not to negate this meta query.
	 */
	protected function _add_meta_query( $key, $value, $negate ) {
		if ( ! isset( $this->_wp_query_args['meta_query'] ) ) {
			$this->_wp_query_args['meta_query'] = array();
		}

		if ( is_null( $value ) ) {
			$compare = $negate ? 'NOT EXISTS' : 'EXISTS';
		} else if ( is_array( $value ) ) {
			$compare = $negate ? 'NOT IN' : 'IN';
		} else {
			$compare = $negate ? '!=' : '=';
		}

		$query = array(
			'key'     => $key,
			'compare' => $compare,
		);

		if ( ! is_null( $value ) ) {
			$query['value'] = $value;
		}

		if ( '!=' === $compare ) {
			$query = array(
				'relation' => 'OR',
				array(
					'key'     => $key,
					'compare' => 'NOT EXISTS',
				),
				$query,
			);
		}

		$this->_wp_query_args['meta_query'][] = $query;
	}

	/**
	 * Adds a tax query to the WP Query args for this instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @param array  $terms    Taxonomy terms.
	 * @param bool   $negate   Whether or not to negate this tax query.
	 */
	protected function _add_tax_query( $taxonomy, $terms, $negate ) {
		if ( ! isset( $this->_wp_query_args['tax_query'] ) ) {
			$this->_wp_query_args['tax_query'] = array();
		}

		$operator = $negate ? 'NOT IN' : 'IN';
		$field    = is_int( $terms[0] ) ? 'term_id' : 'name';

		$query = array(
			'taxonomy' => $taxonomy,
			'field'    => $field,
			'terms'    => $terms,
			'operator' => $operator,
		);

		if ( $negate ) {
			$query = array(
				'relation' => 'OR',
				array(
					'taxonomy' => $taxonomy,
					'operator' => 'NOT EXISTS',
				),
				$query,
			);
		}

		$this->_wp_query_args['tax_query'][] = $query;
	}

	/**
	 * Resets {@see self::$_negate} to default then returns the previous value.
	 *
	 * @return bool
	 */
	protected function _reset_negate() {
		$negate = $this->_negate;

		$this->_negate = false;

		return $negate;
	}

	/**
	 * Adds a tax query to this instance's WP Query args for it's category taxonomy.
	 *
	 * @since 3.0.99
	 *
	 * @param mixed ...$categories Variable number of category arguments where each arg can be
	 *                             a single category name or ID or an array of names or IDs.
	 *
	 * @return $this
	 */
	public function in_category() {
		$negate = $this->_reset_negate();

		if ( ! $this->category_tax ) {
			et_error( 'A category taxonomy has not been set for this query!' );

			return $this;
		}

		$args = func_get_args();
		$args = self::$_->array_flatten( $args );

		if ( ! $args ) {
			return $this;
		}

		$this->_add_tax_query( $this->category_tax, $args, $negate );

		return $this;
	}

	/**
	 * Negates the next query arg that is set.
	 *
	 * @since 3.0.99
	 *
	 * @return $this
	 */
	public function not() {
		$this->_negate = true;

		return $this;
	}

	/**
	 * Performs a new WP Query using the instance's current query params and then returns the
	 * results. Typically, this method is the last method call in a set of chained calls to other
	 * methods on this class during which various query params are set.
	 *
	 * Examples:
	 *
	 *     $cpt_query
	 *         ->in_category( 'some_cat' )
	 *         ->with_tag( 'some_tag' )
	 *         ->run();
	 *
	 *     $cpt_query
	 *         ->with_tag( 'some_tag' )
	 *         ->not()->in_category( 'some_cat' )
	 *         ->run();
	 *
	 * @since 3.0.99
	 *
	 * @param array $args Optional. Additional arguments for {@see WP_Query}.
	 *
	 * @return WP_Post|WP_Post[] $posts
	 */
	public function run( $args = array() ) {
		if ( ! is_null( $this->_query_result ) ) {
			return $this->_query_result;
		}

		$name = $this->post_type;

		if ( $args ) {
			$this->_wp_query_args = array_merge_recursive( $this->_wp_query_args, $args );
		}

		/**
		 * Filters the WP Query args for a custom post type query. The dynamic portion of
		 * the filter name, $name, refers to the name of the custom post type.
		 *
		 * @since 3.0.99
		 *
		 * @param array $args {@see WP_Query::__construct()}
		 */
		$this->_wp_query_args = apply_filters( "et_core_cpt_{$name}_query_args", $this->_wp_query_args );

		$query = new WP_Query( $this->_wp_query_args );

		$this->_query_result = $query->posts;

		if ( 1 === count( $this->_query_result ) ) {
			$this->_query_result = array_pop( $this->_query_result );
		}

		return $this->_query_result;
	}

	/**
	 * Adds a meta query to this instance's WP Query args.
	 *
	 * @since 3.0.99
	 *
	 * @param string $key   The meta key.
	 * @param mixed  $value Optional. The meta value to compare. When `$value` is not provided,
	 *                      the comparison will be 'EXISTS' or 'NOT EXISTS' (when negated).
	 *                      When `$value` is an array, comparison will be 'IN' or 'NOT IN'.
	 *                      When `$value` is not an array, comparison will be '=' or '!='.
	 *
	 * @return $this
	 */
	public function with_meta( $key, $value = null ) {
		$this->_add_meta_query( $key, $value, $this->_reset_negate() );

		return $this;
	}

	/**
	 * Adds a tax query to this instance's WP Query args for it's primary tag-like taxonomy.
	 *
	 * @since 3.0.99
	 *
	 * @param mixed ...$tags Variable number of tag arguments where each arg can be
	 *                       a single tag name or ID, or an array of tag names or IDs.
	 *
	 * @return $this
	 */
	public function with_tag() {
		$negate = $this->_reset_negate();

		if ( ! $this->tag_tax ) {
			et_error( 'A tag taxonomy has not been set for this query!' );

			return $this;
		}

		$args = func_get_args();
		$args = self::$_->array_flatten( $args );

		if ( ! $args ) {
			return $this;
		}

		$this->_add_tax_query( $this->tag_tax, $args, $negate );

		return $this;
	}
}

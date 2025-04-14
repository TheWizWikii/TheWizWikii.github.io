<?php

class ET_Theme_Builder_Request {
	/**
	 * Type constants.
	 */
	const TYPE_FRONT_PAGE        = 'front_page';
	const TYPE_404               = '404';
	const TYPE_SEARCH            = 'search';
	const TYPE_SINGULAR          = 'singular';
	const TYPE_POST_TYPE_ARCHIVE = 'archive';
	const TYPE_TERM              = 'term';
	const TYPE_AUTHOR            = 'author';
	const TYPE_DATE              = 'date';

	/**
	 * Requested object type.
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Requested object subtype.
	 *
	 * @var string
	 */
	protected $subtype = '';

	/**
	 * Requested object id.
	 *
	 * @var integer
	 */
	protected $id = 0;

	/**
	 * Create a request object based on the current request.
	 *
	 * @since 4.0
	 *
	 * @return ET_Theme_Builder_Request|null
	 */
	public static function from_current() {
		$is_extra_layout_home = 'layout' === get_option( 'show_on_front' ) && is_home();

		if ( $is_extra_layout_home || is_front_page() ) {
			return new self( self::TYPE_FRONT_PAGE, '', get_queried_object_id() );
		}

		if ( is_404() ) {
			return new self( self::TYPE_404, '', 0 );
		}

		if ( is_search() ) {
			return new self( self::TYPE_SEARCH, '', 0 );
		}

		$id             = get_queried_object_id();
		$object         = get_queried_object();
		$page_for_posts = (int) get_option( 'page_for_posts' );
		$is_blog_page   = 0 !== $page_for_posts && is_page( $page_for_posts );

		if ( is_singular() ) {
			return new self( self::TYPE_SINGULAR, get_post_type( $id ), $id );
		}

		if ( $is_blog_page || is_home() ) {
			return new self( self::TYPE_POST_TYPE_ARCHIVE, 'post', $id );
		}

		if ( is_category() || is_tag() || is_tax() ) {
			return new self( self::TYPE_TERM, $object->taxonomy, $id );
		}

		if ( is_post_type_archive() ) {
			return new self( self::TYPE_POST_TYPE_ARCHIVE, $object->name, $id );
		}

		if ( is_author() ) {
			return new self( self::TYPE_AUTHOR, '', $id );
		}

		if ( is_date() ) {
			return new self( self::TYPE_DATE, '', 0 );
		}

		return null;
	}

	/**
	 * Create a request object based on a post id.
	 *
	 * @since 4.0
	 *
	 * @param integer $post_id
	 *
	 * @return ET_Theme_Builder_Request
	 */
	public static function from_post( $post_id ) {
		if ( (int) get_option( 'page_on_front' ) === $post_id ) {
			return new self( self::TYPE_FRONT_PAGE, '', $post_id );
		}

		if ( (int) get_option( 'page_for_posts' ) === $post_id ) {
			return new self( self::TYPE_POST_TYPE_ARCHIVE, 'post', $post_id );
		}

		return new self( self::TYPE_SINGULAR, get_post_type( $post_id ), $post_id );
	}

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 *
	 * @param string  $type    Type.
	 * @param string  $subtype Subtype.
	 * @param integer $id      ID.
	 */
	public function __construct( $type, $subtype, $id ) {
		$this->type    = $type;
		$this->subtype = $subtype;
		$this->id      = $id;
	}

	/**
	 * Get the requested object type.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the requested object subtype.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_subtype() {
		return $this->subtype;
	}

	/**
	 * Get the requested object id.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the top ancestor of a setting based on its id. Takes the setting itself
	 * if it has no ancestors.
	 * Returns an empty array if the setting is not found.
	 *
	 * @since 4.0
	 *
	 * @param array  $flat_settings Flat settings.
	 * @param string $setting_id    Setting ID.
	 *
	 * @return array
	 */
	protected function _get_template_setting_ancestor( $flat_settings, $setting_id ) {
		$id = $setting_id;

		if ( ! isset( $flat_settings[ $id ] ) ) {
			// If the setting is not found, check if a valid parent exists.
			$parent_id = explode( ET_THEME_BUILDER_SETTING_SEPARATOR, $id );
			array_pop( $parent_id );
			$parent_id[] = '';
			$parent_id   = implode( ET_THEME_BUILDER_SETTING_SEPARATOR, $parent_id );
			$id          = $parent_id;
		}

		if ( ! isset( $flat_settings[ $id ] ) ) {
			// The setting is still not found - bail.
			return array();
		}

		return $flat_settings[ $id ];
	}

	/**
	 * Get $a or $b depending on which template setting has a higher priority.
	 * Handles cases such as category settings with equal priority but in a ancestor-child relationship.
	 * Returns an empty string if neither setting is found.
	 *
	 * @since 4.0
	 *
	 * @param array  $flat_settings Flat settings.
	 * @param string $a             First template setting.
	 * @param string $b             Second template setting.
	 *
	 * @return string
	 */
	protected function _get_higher_priority_template_setting( $flat_settings, $a, $b ) {
		$map        = array_flip( array_keys( $flat_settings ) );
		$a_ancestor = $this->_get_template_setting_ancestor( $flat_settings, $a );
		$b_ancestor = $this->_get_template_setting_ancestor( $flat_settings, $b );
		$a_found    = ! empty( $a_ancestor );
		$b_found    = ! empty( $b_ancestor );

		if ( ! $a_found || ! $b_found ) {
			if ( $a_found ) {
				return $a;
			}

			if ( $b_found ) {
				return $b;
			}

			return '';
		}

		if ( $a_ancestor['priority'] !== $b_ancestor['priority'] ) {
			// Priorities are not equal - use a simple comparison.
			return $a_ancestor['priority'] >= $b_ancestor['priority'] ? $a : $b;
		}

		if ( $a_ancestor['id'] !== $b_ancestor['id'] ) {
			// Equal priorities, but the ancestors are not the same - use the order in $flat_settings
			// so we have a deterministic result even if $a and $b are swapped.
			return $map[ $a_ancestor['id'] ] <= $map[ $b_ancestor['id'] ] ? $a : $b;
		}

		// Equal priorities, same ancestor.
		$ancestor  = $a_ancestor;
		$a_pieces  = explode( ET_THEME_BUILDER_SETTING_SEPARATOR, $a );
		$b_pieces  = explode( ET_THEME_BUILDER_SETTING_SEPARATOR, $b );
		$separator = preg_quote( ET_THEME_BUILDER_SETTING_SEPARATOR, '/' );

		// Hierarchical post types are a special case by spec since we have to take hierarchy into account.
		// Test if the ancestor matches "singular:post_type:<post_type>:children:id:".
		$id_pieces  = array( 'singular', 'post_type', '[^' . $separator . ']+', 'children', 'id', '' );
		$term_regex = '/^' . implode( $separator, $id_pieces ) . '$/';

		if ( preg_match( $term_regex, $ancestor['id'] ) && is_post_type_hierarchical( $a_pieces[2] ) ) {
			$a_post_id = (int) $a_pieces[5];
			$b_post_id = (int) $b_pieces[5];

			$a_post_ancestors = get_post_ancestors( $a_post_id );
			$b_post_ancestors = get_post_ancestors( $b_post_id );

			if ( in_array( $a_post_id, $b_post_ancestors, true ) ) {
				// $b is a child of $a so it should take priority.
				return $b;
			}

			if ( in_array( $b_post_id, $a_post_ancestors, true ) ) {
				// $a is a child of $b so it should take priority.
				return $a;
			}

			// neither $a nor $b is an ancestor to the other - continue the comparisons.
		}

		// Term archive listings are a special case by spec since we have to take hierarchy into account.
		// Test if the ancestor matches "archive:taxonomy:<taxonomy>:term:id:".
		$id_pieces  = array( 'archive', 'taxonomy', '[^' . $separator . ']+', 'term', 'id', '' );
		$term_regex = '/^' . implode( $separator, $id_pieces ) . '$/';

		if ( preg_match( $term_regex, $ancestor['id'] ) && is_taxonomy_hierarchical( $a_pieces[2] ) ) {
			$a_term_id = $a_pieces[5];
			$b_term_id = $b_pieces[5];

			if ( term_is_ancestor_of( $a_term_id, $b_term_id, $a_pieces[2] ) ) {
				// $b is a child of $a so it should take priority.
				return $b;
			}

			if ( term_is_ancestor_of( $b_term_id, $a_term_id, $a_pieces[2] ) ) {
				// $a is a child of $b so it should take priority.
				return $a;
			}

			// neither $a nor $b is an ancestor to the other - continue the comparisons.
		}

		// Find the first difference in the settings and compare it.
		// The difference should be representing an id or a slug.
		foreach ( $a_pieces as $index => $a_piece ) {
			$b_piece = $b_pieces[ $index ];

			if ( $b_piece === $a_piece ) {
				continue;
			}

			if ( is_numeric( $a_piece ) ) {
				$prioritized = (float) $a_piece <= (float) $b_piece ? $a : $b;
			} else {
				$prioritized = strcmp( $a, $b ) <= 0 ? $a : $b;
			}

			/**
			 * Filters the higher prioritized setting in a given pair that
			 * has equal built-in priority.
			 *
			 * @since 4.2
			 *
			 * @param string $prioritized_setting
			 * @param string $setting_a
			 * @param string $setting_b
			 * @param ET_Theme_Builder_Request $request
			 */
			return apply_filters( 'et_theme_builder_prioritized_template_setting', $prioritized, $a, $b, $this );
		}

		// We should only reach this point if $a and $b are equal so it doesn't
		// matter which we return.
		return $a;
	}

	/**
	 * Check if this request fulfills a template setting.
	 *
	 * @since 4.0
	 *
	 * @param array  $flat_settings Flat settings.
	 * @param string $setting_id    Setting ID.
	 *
	 * @return boolean
	 */
	protected function _fulfills_template_setting( $flat_settings, $setting_id ) {
		$ancestor  = $this->_get_template_setting_ancestor( $flat_settings, $setting_id );
		$fulfilled = false;

		if ( ! empty( $ancestor ) && isset( $ancestor['validate'] ) && is_callable( $ancestor['validate'] ) ) {
			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			$fulfilled = call_user_func(
				$ancestor['validate'],
				$this->get_type(),
				$this->get_subtype(),
				$this->get_id(),
				explode( ET_THEME_BUILDER_SETTING_SEPARATOR, $setting_id )
			);
		}

		return $fulfilled;
	}

	/**
	 * Reduce callback for self::get_template() to get the highest priority template from all applicable ones.
	 *
	 * @since 4.0
	 *
	 * @param array $carry
	 * @param array $applicable_template
	 *
	 * @return array
	 */
	public function reduce_get_template( $carry, $applicable_template ) {
		global $__et_theme_builder_request_flat_settings;

		if ( empty( $carry ) ) {
			return $applicable_template;
		}

		$higher = $this->_get_higher_priority_template_setting(
			$__et_theme_builder_request_flat_settings,
			$carry['top_setting_id'],
			$applicable_template['top_setting_id']
		);

		return $carry['top_setting_id'] !== $higher ? $applicable_template : $carry;
	}

	/**
	 * Get the highest-priority template that should be applied for this request, if any.
	 *
	 * @since 4.0
	 *
	 * @param array $templates
	 * @param array $flat_settings
	 *
	 * @return array
	 */
	public function get_template( $templates, $flat_settings ) {
		// Use a global variable to pass data to the reduce callback as we support PHP 5.2.
		global $__et_theme_builder_request_flat_settings;

		$applicable_templates = array();

		foreach ( $templates as $template ) {
			if ( ! $template['enabled'] ) {
				continue;
			}

			foreach ( $template['exclude_from'] as $setting_id ) {
				if ( $this->_fulfills_template_setting( $flat_settings, $setting_id ) ) {
					// The setting is explicitly excluded - bail from testing the template any further.
					continue 2;
				}
			}

			$highest_priority = '';

			foreach ( $template['use_on'] as $setting_id ) {
				if ( $this->_fulfills_template_setting( $flat_settings, $setting_id ) ) {
					$highest_priority = $this->_get_higher_priority_template_setting( $flat_settings, $highest_priority, $setting_id );
				}
			}

			if ( '' !== $highest_priority ) {
				$applicable_templates[] = array(
					'template'       => $template,
					'top_setting_id' => $highest_priority,
				);
			}
		}

		$__et_theme_builder_request_flat_settings = $flat_settings;
		$applicable_template                      = array_reduce( $applicable_templates, array( $this, 'reduce_get_template' ), array() );
		$__et_theme_builder_request_flat_settings = array();

		if ( ! empty( $applicable_template ) ) {
			// Found the highest priority applicable template - return it.
			return $applicable_template['template'];
		}

		$default_templates = et_()->array_pick( $templates, array( 'default' => true ) );

		if ( ! empty( $default_templates ) ) {
			$default_template = $default_templates[0];

			if ( $default_template['enabled'] ) {
				// Return the first default template. We don't expect there to be multiple ones but
				// it is technically possible with direct database edits, for example.
				return $default_template;
			}
		}

		// No templates found at all - probably never used the Theme Builder.
		return array();
	}
}

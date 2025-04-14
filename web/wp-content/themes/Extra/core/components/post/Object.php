<?php


abstract class ET_Core_Post_Object {

	/**
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * Current instances of this class organized by type.
	 *
	 * @since 3.0.99
	 * @var   array[] {
	 *
	 *     @type ET_Core_Post_Object[] $type {
	 *
	 *         @type ET_Core_Post_Object $name Instance.
	 *         ...
	 *     }
	 *     ...
	 * }
	 */
	private static $_instances = array();

	/**
	 * The `$args` array used when registering this post object.
	 *
	 * @since 3.0.99
	 * @var   array
	 */
	protected $_args;

	/**
	 * The owner of this instance. Default 'core'. Accepts 'divi', 'builder', 'epanel', 'bloom', 'monarch'.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	protected $_owner = 'core';

	/**
	 * Whether or not the object has been registered.
	 *
	 * @since 3.0.99
	 * @var   bool
	 */
	protected $_registered = false;

	/**
	 * The WP object for this instance.
	 *
	 * @since 3.0.99
	 * @var   WP_Post_Type|WP_Taxonomy
	 */
	protected $_wp_object;

	/**
	 * Post object key.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $name;

	/**
	 * Post object type. Accepts 'cpt', 'taxonomy'.
	 *
	 * @since 3.0.99
	 * @var   string
	 */
	public $wp_type;

	/**
	 * ET_Core_Post_Base constructor.
	 */
	public function __construct() {
		$this->_args           = $this->_get_args();
		$this->_args['labels'] = $this->_get_labels();

		$this->_apply_filters();
		$this->_sanity_check();

		if ( empty( self::$_instances ) ) {
			self::$_instances['cpt']      = array();
			self::$_instances['taxonomy'] = array();

			add_action( 'init', 'ET_Core_Post_Object::register_all' );
		}

		self::$_instances[ $this->wp_type ][ $this->name ] = $this;
	}

	/**
	 * Applies filters to the instance's filterable properties.
	 */
	protected function _apply_filters() {
		$name = $this->name;

		if ( 'cpt' === $this->wp_type ) {
			/**
			 * Filters the `$args` for a custom post type. The dynamic portion of the
			 * filter, `$name`, refers to the name/key of the post type being registered.
			 *
			 * @since 3.0.99
			 *
			 * @param array $args {@see register_post_type()}
			 */
			$this->_args = apply_filters( "et_core_cpt_{$name}_args", $this->_args );

		} else if ( 'taxonomy' === $this->wp_type ) {
			/**
			 * Filters the `$args` for a custom post taxonomy. The dynamic portion of the
			 * filter, `$name`, refers to the name/key of the taxonomy being registered.
			 *
			 * @since 3.0.99
			 *
			 * @param array $args {@see register_taxonomy()}
			 */
			$this->_args = apply_filters( "et_core_taxonomy_{$name}_args", $this->_args );
		}
	}

	/**
	 * This method is called right before registering the object. It is intended to be
	 * overridden by child classes as needed.
	 */
	protected function _before_register() {}

	/**
	 * Returns the args for the instance.
	 * See {@see register_post_type()} or {@see register_taxonomy()}.
	 *
	 * @return array $args
	 */
	abstract protected function _get_args();

	/**
	 * Returns labels for the instance.
	 * See {@see register_post_type()} or {@see register_taxonomy()}.
	 *
	 * @return array $labels
	 */
	abstract protected function _get_labels();

	/**
	 * Checks for required properties and existing instances.
	 */
	protected function _sanity_check() {
		if ( ! $this->_args || ! $this->name || ! $this->wp_type ) {
			et_error( 'Missing required properties!' );
			wp_die();
		} else if ( isset( self::$_instances[ $this->wp_type ][ $this->name ] ) ) {
			et_error( 'Multiple instances are not allowed!' );
			wp_die();
		}
	}

	/**
	 * Get a derived class instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $type See {@see self::$wp_type} for accepted values. Default is 'cpt'.
	 * @param string $name The name/slug of the derived object. Default is an empty string.
	 *
	 * @return self|null
	 */
	public static function instance( $type = 'cpt', $name = '' ) {
		if ( ! self::$_ ) {
			self::$_ = ET_Core_Data_Utils::instance();
		}

		return self::$_->array_get( self::$_instances, "{$type}.{$name}", null );
	}

	/**
	 * Calls either {@see register_post_type} or {@see register_taxonomy} for each instance.
	 *
	 * @since 3.0.99
	 *
	 * @param string $owner Optional. Only register objects owned by a part of the codebase.
	 *                      See {@see self::_owner} for accepted values.
	 */
	public static function register_all( $owner = null ) {
		if ( empty( self::$_instances ) ) {
			return;
		}

		global $wp_taxonomies;

		foreach ( self::$_instances['taxonomy'] as $name => $instance ) {
			$can_register = is_null( $owner ) || $owner === $instance->_owner;

			if ( $instance->_registered || ! $can_register ) {
				continue;
			}

			$instance->_before_register();

			register_taxonomy( $name, $instance->post_types, $instance->_args );

			$instance->_wp_object  = $wp_taxonomies[ $name ];
			$instance->_registered = true;
		}

		foreach ( self::$_instances['cpt'] as $name => $instance ) {
			$can_register = is_null( $owner ) || $owner === $instance->_owner;

			if ( $instance->_registered || ! $can_register ) {
				continue;
			}

			$instance->_before_register();

			$instance->_wp_object  = register_post_type( $name, $instance->_args );
			$instance->_registered = true;
		}
	}
}

<?php

/**
 * Manages a frontend inline CSS or JavaScript resource.
 *
 * If possible, the resource will be served as a static file for better performance. It can be
 * tied to a specific post or it can be 'global'. The resource can be output, static or inline,
 * to one of four locations on the page:
 *
 *   * `head-early`: right AFTER theme styles have been enqueued
 *   * `head`      : right BEFORE the theme and wp's inline custom css
 *   * `head-late` : right AFTER the theme and wp's inline custom css
 *   * `footer`    : in the footer
 *
 * The first time the class is instantiated, a static callback method will be registered for each
 * output location. Inside each callback, we'll iterate over any/all instances that are assigned
 * to the current output location and perform the following steps:
 *
 *   1. If a static file exists for the resource, go to the next step. Otherwise, try to create
 *      a static file for the resource if it has `data`. If it doesn't have `data`, assign it to
 *      the next output location and then move on to the next resource (continue).
 *   2. If a static file exists for the resource, enqueue it (via WP or manually) and then move on
 *      to the next resource (continue). If no static file exists, go to the next step.
 *   3. Output the resource inline.
 *
 * @since   2.0
 *
 * @package ET\Core
 */
class ET_Core_PageResource {
	/**
	 * Lock file.
	 *
	 * @var string[]
	 */
	protected static $_lock_file;

	/**
	 * Onload attribute for stylesheet output.
	 *
	 * @var string[]
	 */
	private static $_onload = '';

	/**
	 * Output locations.
	 *
	 * @var string[]
	 */
	protected static $_output_locations = array(
		'head-early',
		'head',
		'head-late',
		'footer',
	);

	/**
	 * Resource owners.
	 *
	 * @var string[]
	 */
	protected static $_owners = array(
		'divi',
		'builder',
		'epanel',
		'epanel_temp',
		'extra',
		'core',
		'bloom',
		'monarch',
		'custom',
		'all',
	);

	/**
	 * Resource scopes.
	 *
	 * @var string[]
	 */
	protected static $_scopes = array(
		'global',
		'post',
	);

	/**
	 * Temp DIRS.
	 *
	 * @var array
	 */
	protected static $_temp_dirs = array();

	/**
	 * Resource types.
	 *
	 * @var string[]
	 */
	protected static $_types = array(
		'style',
		'script',
	);

	/**
	 * Whether or not we have write access to the filesystem.
	 *
	 * @var bool
	 */
	protected static $_can_write;

	/**
	 * Request ID.
	 *
	 * @var int
	 */
	protected static $_request_id;

	/**
	 * Request time.
	 *
	 * @var string
	 */
	protected static $_request_time;

	/**
	 * All instances of this class.
	 *
	 * @var ET_Core_PageResource[] {
	 *
	 *     @type ET_Core_PageResource $slug
	 * }
	 */
	protected static $_resources;

	/**
	 * All instances of this class organized by output location and sorted by priority.
	 *
	 * @var array[] {
	 *
	 *     @type array[] $location {@see self::$_output_locations} {
	 *
	 *         @type ET_Core_PageResource[] $priority {
	 *
	 *             @type ET_Core_PageResource $slug
	 *         }
	 *     }
	 * }
	 */
	protected static $_resources_by_location;

	/**
	 * All instances of this class organized by scope.
	 *
	 * @var array[] {
	 *
	 *     @type ET_Core_PageResource[] $post|$global {
	 *
	 *         @type ET_Core_PageResource $slug
	 *     }
	 * }
	 */
	protected static $_resources_by_scope;

	/**
	 * @var string
	 */
	public static $WP_CONTENT_DIR;

	/**
	 * @var string
	 */
	public static $current_output_location;

	/**
	 * @var ET_Core_Data_Utils
	 */
	public static $data_utils;

	/**
	 * @var \WP_Filesystem_Base|null
	 */
	public static $wpfs;

	/**
	 * The absolute path to the directory where the static resource will be stored.
	 *
	 * @var string
	 */
	public $base_dir;

	/**
	 * The absolute path to the static resource on the server.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Temp DIR.
	 *
	 * @var array
	 */
	public $temp_dir;

	/**
	 * The absolute URL through which the static resource can be downloaded.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * The data/contents for/of the static resource sorted by priority.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Whether or not this resource has been disabled.
	 *
	 * @var bool
	 */
	public $disabled;

	/**
	 * Whether or not the static resource file has been enqueued.
	 *
	 * @var bool
	 */
	public $enqueued;

	/**
	 * Whether or not this resource is forced inline.
	 *
	 * @var bool
	 */
	public $forced_inline;

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * Whether or not the resource has already been output to the page inline.
	 *
	 * @var bool
	 */
	public $inlined;

	/**
	 * The owner of this instance.
	 *
	 * @var string
	 */
	public $owner;

	/**
	 * The id of the post to which this resource belongs.
	 *
	 * @var string
	 */
	public $post_id;

	/**
	 * The priority of this resource.
	 *
	 * @var int
	 */
	public $priority;

	/**
	 * A unique identifier for this resource.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * The resource type (style|script).
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The output location during which this resource's static file should be generated.
	 *
	 * @var string
	 */
	public $write_file_location;

	/**
	 * The output location where this resource should be output.
	 *
	 * @var string
	 */
	public $location;

	/**
	 * ET_Core_PageResource constructor
	 *
	 * @param string     $owner    The owner of the instance (core|divi|builder|bloom|monarch|custom).
	 * @param string     $slug     A string that uniquely identifies the resource.
	 * @param string|int $post_id  The post id that the resource is associated with or `global`.
	 *                             If `null`, {@link get_the_ID()} will be used.
	 * @param string     $type     The resource type (style|script). Default: `style`.
	 * @param string     $location Where the resource should be output (head|footer). Default: `head`.
	 */
	public function __construct( $owner, $slug, $post_id = null, $priority = 10, $location = 'head-late', $type = 'style' ) {
		$this->owner    = self::_validate_property( 'owner', $owner );
		$this->post_id  = self::_validate_property( 'post_id', $post_id ? $post_id : et_core_page_resource_get_the_ID() );

		$this->type     = self::_validate_property( 'type', $type );
		$this->location = self::_validate_property( 'location', $location );

		$this->write_file_location = $this->location;

		$this->filename = sanitize_file_name( "et-{$this->owner}-{$slug}-{$post_id}" );
		$this->slug     = "{$this->filename}-cached-inline-{$this->type}s";

		$this->data     = array();
		$this->priority = $priority;

		self::startup();

		$this->_initialize_resource();
	}

	/**
	 * Activates the class
	 */
	public static function startup() {
		if ( null !== self::$_resources ) {
			// Class has already been initialized
			return;
		}

		$time = (string) microtime( true );
		$time = str_replace( '.', '', $time );
		$rand = (string) mt_rand();

		self::$_request_time = $time;
		self::$_request_id   = "{$time}-{$rand}";
		self::$_resources    = array();
		self::$data_utils    = new ET_Core_Data_Utils();

		foreach ( self::$_output_locations as $location ) {
			self::$_resources_by_location[ $location ] = array();
		}

		foreach ( self::$_scopes as $scope ) {
			self::$_resources_by_scope[ $scope ] = array();
		}
		// phpcs:enable

		self::$WP_CONTENT_DIR = self::$data_utils->normalize_path( WP_CONTENT_DIR );
		self::$_lock_file     = self::$_request_id . '~';

		self::_register_callbacks();
		self::_setup_wp_filesystem();

		self::$_can_write = et_core_cache_dir()->can_write;
	}

	/**
	 * Cleanup and save
	 */
	public static function shutdown() {
		if ( ! self::$_resources || ! self::$_can_write ) {
			return;
		}

		// Remove any leftover temporary directories that belong to this request
		foreach ( self::$_temp_dirs as $temp_directory ) {
			if ( file_exists( $temp_directory . '/' . self::$_lock_file ) ) {
				@self::$wpfs->delete( $temp_directory, true );
			}
		}

		// Reset $_resources property; Mostly useful for unit test big request which needs to make
		// each test*() method act like it is different page request
		self::$_resources = null;

		if ( et_()->WPFS()->exists( self::$WP_CONTENT_DIR . '/cache/et' ) ) {
			// Remove old cache directory
			et_()->WPFS()->rmdir( self::$WP_CONTENT_DIR . '/cache/et', true );
		}
	}

	protected static function _assign_output_location( $location, $resource ) {
		$priority_existed = isset( self::$_resources_by_location[ $location ][ $resource->priority ] );

		self::$_resources_by_location[ $location ][ $resource->priority ][ $resource->slug ] = $resource;

		if ( ! $priority_existed ) {
			// We've added a new priority to the list, so put them back in sorted order.
			ksort( self::$_resources_by_location[ $location ], SORT_NUMERIC );
		}
	}

	/**
	 * Enqueues static file for provided script resource.
	 *
	 * @param ET_Core_PageResource $resource page resources.
	 */
	protected static function _enqueue_script( $resource ) {
		// Bust PHP's stats cache for the resource file to ensure we get the latest timestamp.
		clearstatcache( true, $resource->path );

		$can_enqueue = 0 === did_action( 'wp_print_scripts' );
		$timestamp   = filemtime( $resource->path );

		if ( $can_enqueue ) {
			wp_enqueue_script( $resource->slug, set_url_scheme( $resource->url ), array(), $timestamp, true );
		} else {
			$timestamp = $timestamp ? $timestamp : ET_CORE_VERSION;

			printf(
				'<script id="%1$s" src="%2$s"></script>', // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
				esc_attr( $resource->slug ),
				esc_url( set_url_scheme( $resource->url . "?ver={$timestamp}" ) )
			);
		}

		$resource->enqueued = true;
	}

	/**
	 * Enqueues static file for provided style resource.
	 *
	 * @param ET_Core_PageResource $resource
	 */
	protected static function _enqueue_style( $resource ) {
		if ( 'footer' === self::$current_output_location ) {
			return;
		}

		// Bust PHP's stats cache for the resource file to ensure we get the latest timestamp.
		clearstatcache( true, $resource->path );

		$can_enqueue = 0 === did_action( 'wp_print_scripts' );
		// reason: We do this on purpose when a style can't be enqueued.
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		$template = '<link rel="stylesheet" id="%1$s" href="%2$s" />';
		// phpcs:enable
		$timestamp = filemtime( $resource->path );

		if ( $can_enqueue ) {
			wp_enqueue_style( $resource->slug, set_url_scheme( $resource->url ), array(), $timestamp );
		} else {
			// reason: this whole file needs to be converted.
			// phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase
			$timestamp = $timestamp ?: ET_CORE_VERSION;
			$slug      = esc_attr( $resource->slug );
			$scheme    = esc_url( set_url_scheme( $resource->url . "?ver={$timestamp}" ) );
			$tag       = sprintf( $template, $slug, $scheme );
			$onload    = et_core_esc_previously( self::$_onload );
			// phpcs:enable

			$tag = apply_filters( 'et_core_page_resource_tag', $tag, $slug, $scheme, $onload );

			print( et_core_esc_previously( $tag ) );
		}

		$resource->enqueued = true;
	}

	/**
	 * Returns the next output location.
	 *
	 * @see self::$_output_locations
	 *
	 * @return string
	 */
	protected static function _get_next_output_location() {
		$current_index = array_search( self::$current_output_location, self::$_output_locations, true );

		if ( false === $current_index || ! is_int( $current_index ) ) {
			ET_Core_Logger::error( '$current_output_location is invalid!' );
		}

		$current_index += 1;

		return self::$_output_locations[ $current_index ];
	}

	/**
	 * Creates static resource files for an output location if needed.
	 *
	 * @param string $location {@link self::$_output_locations}.
	 */
	protected static function _maybe_create_static_resources( $location ) {
		self::$current_output_location = $location;

		// Disable for footer inside builder if page uses Theme Builder Editor to avoid conflict with critical CSS.
		if ( 'footer' === $location && et_fb_is_enabled() && et_fb_is_theme_builder_used_on_page() ) {
			return false;
		}

		$sorted_resources = self::get_resources_by_output_location( $location );

		foreach ( $sorted_resources as $priority => $resources ) {
			foreach ( $resources as $slug => $resource ) {
				if ( $resource->write_file_location !== $location ) {
					// This resource's static file needs to be generated later on.
					self::_assign_output_location( $resource->write_file_location, $resource );
					continue;
				}

				if ( ! self::$_can_write ) {
					// The reason we don't simply check this before looping through resources and
					// bail if it fails is because we need to perform the output location assignment
					// in the previous conditional regardless (otherwise builder styles will break).
					continue;
				}

				if ( $resource->forced_inline || $resource->has_file() ) {
					continue;
				}

				$data = $resource->get_data( 'file' );

				if ( empty( $data ) && 'footer' !== $location ) {
					// This resource doesn't have any data yet so we'll assign it to the next output location.
					$next_location = self::_get_next_output_location();

					$resource->set_output_location( $next_location );

					continue;
				}

				$force_write = apply_filters( 'et_core_page_resource_force_write', false, $resource );

				if ( ! $force_write && empty( $data ) ) {
					continue;
				}

				// Make sure directory exists.
				if ( ! self::$data_utils->ensure_directory_exists( $resource->base_dir ) ) {
					self::$_can_write = false;
					return;
				}

				$can_continue = true;

				// Try to create a temporary directory which we'll use as a pseudo file lock
				if ( @mkdir( $resource->temp_dir, 0755 ) ) { //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Just ignore this since it's an internal use.
					self::$_temp_dirs[] = $resource->temp_dir;

					// Make sure another request doesn't delete our temp directory.
					$lock_file = $resource->temp_dir . '/' . self::$_lock_file;
					self::$wpfs->put_contents( $lock_file, '' );

					// Create the static resource file
					if ( ! self::$wpfs->put_contents( $resource->path, $data, 0644 ) ) {
						// There's no point in continuing
						self::$_can_write = $can_continue = false;
					} else {
						// Remove the temporary directory
						self::$wpfs->delete( $resource->temp_dir, true );

						/**
						 * Fires when the static resource file is created.
						 *
						 * @since 4.10.8
						 *
						 * @param object $resource The resource object.
						 */
						do_action( 'et_core_static_file_created', $resource );
					}
				} elseif ( file_exists( $resource->temp_dir ) ) {
					// The static resource file is currently being created by another request
					continue;
				} else {
					// Failed for some other reason. There's no point in continuing
					self::$_can_write = $can_continue = false;
					return;
				}

				if ( ! $can_continue ) {
					return;
				}

				if ( ! defined( 'DONOTCACHEPAGE' ) ) {
					define( 'DONOTCACHEPAGE', true );
				}
			}
		}
	}

	/**
	 * Enqueues static files for an output location if available.
	 *
	 * @param string $location {@link self::$_output_locations}.
	 */
	protected static function _maybe_enqueue_static_resources( $location ) {
		$sorted_resources = self::get_resources_by_output_location( $location );

		foreach ( $sorted_resources as $priority => $resources ) {
			foreach ( $resources as $slug => $resource ) {
				if ( $resource->disabled ) {
					// Resource is disabled. Remove it from the queue.
					self::_unassign_output_location( $location, $resource );
					continue;
				}

				if ( $resource->forced_inline || ! $resource->url || ! $resource->has_file() ) {
					continue;
				}

				if ( 'style' === $resource->type ) {
					self::_enqueue_style( $resource );
				} elseif ( 'script' === $resource->type ) {
					self::_enqueue_script( $resource );
				}

				if ( $resource->enqueued ) {
					self::_unassign_output_location( $location, $resource );
				}
			}
		}
	}

	/**
	 * Outputs all non-enqueued resources for an output location inline.
	 *
	 * @param string $location {@link self::$_output_locations}.
	 */
	protected static function _maybe_output_inline_resources( $location ) {
		$sorted_resources = self::get_resources_by_output_location( $location );

		foreach ( $sorted_resources as $priority => $resources ) {
			foreach ( $resources as $slug => $resource ) {
				if ( $resource->disabled ) {
					// Resource is disabled. Remove it from the queue.
					self::_unassign_output_location( $location, $resource );
					continue;
				}

				$data = $resource->get_data( 'inline' );

				$same_write_file_location = $resource->write_file_location === $resource->location;

				if ( empty( $data ) && 'footer' !== $location && $same_write_file_location ) {
					// This resource doesn't have any data yet so we'll assign it to the next output location.
					$next_location = self::_get_next_output_location();
					$resource->set_output_location( $next_location );
					continue;
				} elseif ( empty( $data ) ) {
					continue;
				}

				printf(
					'<%1$s id="%2$s">%3$s</%1$s>',
					esc_html( $resource->type ),
					esc_attr( $resource->slug ),
					et_core_esc_previously( wp_strip_all_tags( $data ) )
				);

				if ( $same_write_file_location ) {
					// File wasn't created during this location's callback and it won't be created later
					$resource->inlined = true;
				}
			}
		}
	}

	/**
	 * Registers necessary callbacks.
	 */
	protected static function _register_callbacks() {
		$class = 'ET_Core_PageResource';

		// Output Location: head-early, right after theme styles have been enqueued.
		add_action( 'wp_enqueue_scripts', array( $class, 'head_early_output_cb' ), 11 );

		// Output Location: head, right BEFORE the theme and wp's custom css.
		add_action( 'wp_head', array( $class, 'head_output_cb' ), 99 );

		// Output Location: head-late, right AFTER the theme and wp's custom css.
		add_action( 'wp_head', array( $class, 'head_late_output_cb' ), 103 );

		// Output Location: footer.
		add_action( 'wp_footer', array( $class, 'footer_output_cb' ), 20 );

		// Always delete cached resources for a post upon saving.
		add_action( 'save_post', array( $class, 'save_post_cb' ), 10, 3 );

		// Always delete cached resources for theme customizer upon saving.
		add_action( 'customize_save_after', array( $class, 'customize_save_after_cb' ) );

		/*
		 * Always delete dynamic css when saving widgets.
		 * `widget_update_callback` fires on save for any of the present widgets,
		 * `delete_widget` fires on save for any deleted widget.
		 */
		add_filter( 'widget_update_callback', array( $class, 'widget_update_callback_cb' ) );
		add_filter( 'delete_widget', array( $class, 'widget_update_callback_cb' ) );
	}

	/**
	 * Initializes the WPFilesystem class.
	 */
	protected static function _setup_wp_filesystem() {
		// The wpfs instance will always exists at this point because the cache dir class initializes it beforehand
		self::$wpfs = $GLOBALS['wp_filesystem'];
	}

	/**
	 * Unassign a resource from an output location.
	 *
	 * @param string               $location {@link self::$_output_locations}.
	 * @param ET_Core_PageResource $resource
	 */
	protected static function _unassign_output_location( $location, $resource ) {
		unset( self::$_resources_by_location[ $location ][ $resource->priority ][ $resource->slug ] );
	}

	protected static function _validate_property( $property, $value ) {
		$valid_values = array(
			'location' => self::$_output_locations,
			'owner'    => self::$_owners,
			'type'     => self::$_types,
		);

		switch ( $property ) {
			case 'path':
				$value    = et_()->normalize_path( realpath( $value ) );
				$is_valid = et_()->starts_with( $value, et_core_cache_dir()->path );
				break;
			case 'url':
				$base_url = et_core_cache_dir()->url;
				$is_valid = et_()->starts_with( $value, set_url_scheme( $base_url, 'http' ) );
				$is_valid = $is_valid ? $is_valid : et_()->starts_with( $value, set_url_scheme( $base_url, 'https' ) );
				break;
			case 'post_id':
				$is_valid = 'global' === $value || 'all' === $value || is_numeric( $value );
				break;
			default:
				$is_valid = isset( $valid_values[ $property ] ) && in_array( $value, $valid_values[ $property ] );
				break;
		}

		return $is_valid ? $value : '';
	}

	/**
	 * Whether or not we are able to write to the filesystem.
	 *
	 * @return bool
	 */
	public static function can_write_to_filesystem() {
		return et_core_cache_dir()->can_write;
	}

	/**
	 * Output Location: footer
	 * {@see 'wp_footer' (20) Allow third-party extensions some room to do what they do}
	 */
	public static function footer_output_cb() {
		self::_maybe_create_static_resources( 'footer' );
		self::_maybe_enqueue_static_resources( 'footer' );
		self::_maybe_output_inline_resources( 'footer' );
	}

	/**
	 * Returns the absolute path to our cache directory.
	 *
	 * @since 4.0.8     Removed `$path_type` param b/c cache directory might not be located under wp-content.
	 * @since 3.0.52
	 *
	 * @return string
	 */
	public static function get_cache_directory() {
		return et_core_cache_dir()->path;
	}

	/**
	 * Returns all current resources.
	 *
	 * @return array {@link self::$_resources}
	 */
	public static function get_resources() {
		return self::$_resources;
	}

	/**
	 * Returns the current resources for the provided output location, sorted by priority.
	 *
	 * @param string $location The desired output location {@see self::$_output_locations}.
	 *
	 * @return array[] {
	 *
	 *     @type ET_Core_PageResource[] $priority {
	 *
	 *         @type ET_Core_PageResource $slug Resource.
	 *         ...
	 *     }
	 *     ...
	 * }
	 */
	public static function get_resources_by_output_location( $location ) {
		return self::$_resources_by_location[ $location ];
	}

	/**
	 * Returns the current resources for the provided scope.
	 *
	 * @param string $scope The desired scope (post|global).
	 *
	 * @return ET_Core_PageResource[]
	 */
	public static function get_resources_by_scope( $scope ) {
		return self::$_resources_by_scope[ $scope ];
	}

	/**
	 * Output Location: head-early
	 * {@see 'wp_enqueue_scripts' (11) Should run right after the theme enqueues its styles.}
	 */
	public static function head_early_output_cb() {
		self::_maybe_create_static_resources( 'head-early' );
		self::_maybe_enqueue_static_resources( 'head-early' );
		self::_maybe_output_inline_resources( 'head-early' );
	}

	/**
	 * Output Location: head
	 * {@see 'wp_head' (99) Must run BEFORE the theme and WP's custom css callbacks.}
	 */
	public static function head_output_cb() {
		self::_maybe_create_static_resources( 'head' );
		self::_maybe_enqueue_static_resources( 'head' );
		self::_maybe_output_inline_resources( 'head' );
	}

	/**
	 * Output Location: head-late
	 * {@see 'wp_head' (103) Must run AFTER the theme and WP's custom css callbacks.}
	 */
	public static function head_late_output_cb() {
		self::_maybe_create_static_resources( 'head-late' );
		self::_maybe_enqueue_static_resources( 'head-late' );
		self::_maybe_output_inline_resources( 'head-late' );
	}

	/**
	 * {@see 'widget_update_callback'}
	 *
	 * @param array $instance Widget settings being saved.
	 */
	public static function widget_update_callback_cb( $instance ) {
		self::remove_static_resources( 'all', 'all', false, 'dynamic' );
		return $instance;
	}

	/**
	 * {@see 'customize_save_after'}
	 *
	 * @param WP_Customize_Manager $manager
	 */
	public static function customize_save_after_cb( $manager ) {
		self::remove_static_resources( 'all', 'all' );
	}

	/**
	 * {@see 'save_post'}
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 * @param bool    $update
	 */
	public static function save_post_cb( $post_id, $post, $update ) {
		// In Dynamic CSS, we parse the layout content for generating styles and store it under the `object_id`, so clearing
		// only the layout assets won't update the page style if we made any changes to the layout/global modules etc.
		// Hence, we need to clear all static resources when we update a layout.
		// Also, we should only clear the cache if the layout being saved is a global module/row/section.
		if ( 'et_pb_layout' === $post->post_type ) {
			$taxonomies     = get_taxonomies( [ 'object_type' => [ 'et_pb_layout' ] ] );
			$tax_to_clear   = array( 'scope', 'layout_type' );
			$types_to_clear = array( 'module', 'row', 'section' );

			$scope_terms  = get_the_terms( $post_id, 'scope' );
			$layout_terms = get_the_terms( $post_id, 'layout_type' );

			if ( ! empty( $scope_terms ) && ! empty( $layout_terms ) ) {
				$scope_terms       = wp_list_pluck( $scope_terms, 'slug' );
				$layout_terms      = wp_list_pluck( $layout_terms, 'slug' );
				$is_global         = in_array( 'global', $scope_terms, true );
				$clearable_modules = array_intersect( $types_to_clear, $layout_terms );
				$remove_resource   = $is_global && ! empty( $clearable_modules );

				foreach ( $taxonomies as $taxonomy ) {
					if ( in_array( $taxonomy, $tax_to_clear, true ) && $remove_resource ) {
						$post_id = 'all';
						break;
					}
				}
			}
		}
		self::remove_static_resources( $post_id, 'all' );
	}

	/**
	 * Remove static resources for a post, or optionally all resources, if any exist.
	 *
	 * @param string $post_id id of post.
	 * @param string $owner owner of file.
	 * @param bool   $force remove all resources.
	 * @param string $slug file slug.
	 */
	public static function remove_static_resources( $post_id, $owner = 'core', $force = false, $slug = 'all' ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! wp_doing_cron() && ! et_core_security_check_passed( 'edit_posts' ) ) {
			return;
		}

		if ( ! self::can_write_to_filesystem() ) {
			return;
		}

		if ( ! self::$data_utils ) {
			self::startup();
		}

		self::do_remove_static_resources( $post_id, $owner, $force, $slug );
	}

	/**
	 * Remove static resources action.
	 *
	 * @param string $post_id id of post.
	 * @param string $owner owner of file.
	 * @param bool   $force remove all resources.
	 * @param string $slug file slug.
	 */
	public static function do_remove_static_resources( $post_id, $owner = 'core', $force = false, $slug = 'all' ) {
		$post_id = self::_validate_property( 'post_id', $post_id );
		$owner   = self::_validate_property( 'owner', $owner );
		$slug    = sanitize_key( $slug );

		if ( '' === $owner || '' === $post_id ) {
			return;
		}

		$_post_id = 'all' === $post_id ? '*' : $post_id;
		$_owner   = 'all' === $owner ? '*' : $owner;
		$_slug    = 'all' === $slug ? '*' : $slug;

		$cache_dir = self::get_cache_directory();

		$files = array_merge(
			// Remove any CSS files missing a parent folder.
			(array) glob( "{$cache_dir}/et-{$_owner}-*" ),
			// Remove CSS files for individual posts or all posts if $post_id set to 'all'.
			(array) glob( "{$cache_dir}/{$_post_id}/et-{$_owner}-{$_slug}*" ),
			// Remove CSS files that contain theme builder template CSS.
			// Multiple directories need to be searched through since * doesn't match / in the glob pattern.
			(array) glob( "{$cache_dir}/*/et-{$_owner}-{$_slug}-*tb-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/*/et-{$_owner}-{$_slug}-*tb-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/*/*/et-{$_owner}-{$_slug}-*tb-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/et-{$_owner}-{$_slug}-*tb-for-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/*/et-{$_owner}-{$_slug}-*tb-for-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/*/*/et-{$_owner}-{$_slug}-*tb-for-{$_post_id}*" ),
			// Remove Dynamic CSS files for categories, tags, authors, archives, homepage post feed and search results.
			(array) glob( "{$cache_dir}/taxonomy/*/*/et-{$_owner}-dynamic*" ),
			(array) glob( "{$cache_dir}/author/*/et-{$_owner}-dynamic*" ),
			(array) glob( "{$cache_dir}/archive/et-{$_owner}-dynamic*" ),
			(array) glob( "{$cache_dir}/search/et-{$_owner}-dynamic*" ),
			(array) glob( "{$cache_dir}/notfound/et-{$_owner}-dynamic*" ),
			(array) glob( "{$cache_dir}/home/et-{$_owner}-dynamic*" ),
			// WP Templates and Template Parts.
			(array) glob( "{$cache_dir}/*/et-{$_owner}-{$_slug}-*wpe-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/*/et-{$_owner}-{$_slug}-*wpe-{$_post_id}*" ),
			(array) glob( "{$cache_dir}/*/*/*/et-{$_owner}-{$_slug}-*wpe-{$_post_id}*" )
		);

		self::_remove_files_in_directory( $files, $cache_dir );

		// Remove empty directories.
		self::$data_utils->remove_empty_directories( $cache_dir );

		// Clear cache managed by 3rd-party cache plugins.
		$post_id = ! empty( $post_id ) && absint( $post_id ) > 0 ? $post_id : '';

		et_core_clear_wp_cache( $post_id );

		// Purge the module features cache.
		if ( class_exists( 'ET_Builder_Module_Features' ) ) {
			if ( ! empty( $post_id ) ) {
				ET_Builder_Module_Features::purge_cache( $post_id );
			} else {
				ET_Builder_Module_Features::purge_cache();
			}
		}

		// Purge the google fonts cache.
		if ( empty( $post_id ) && class_exists( 'ET_Builder_Google_Fonts_Feature' ) ) {
			ET_Builder_Google_Fonts_Feature::purge_cache();
		}

		// Purge the dynamic assets cache.
		if ( empty( $post_id ) && class_exists( 'ET_Builder_Dynamic_Assets_Feature' ) ) {
			ET_Builder_Dynamic_Assets_Feature::purge_cache();
		}

		$post_meta_caches = array(
			'et_enqueued_post_fonts',
			'_et_dynamic_cached_shortcodes',
			'_et_dynamic_cached_attributes',
			'_et_builder_module_features_cache',
		);

		// Clear post meta caches.
		foreach ( $post_meta_caches as $post_meta_cache ) {
			if ( ! empty( $post_id ) ) {
				delete_post_meta( $post_id, $post_meta_cache );
			} else {
				delete_post_meta_by_key( $post_meta_cache );
			}
		}

		// Set our DONOTCACHEPAGE file for the next request.
		self::$data_utils->ensure_directory_exists( $cache_dir );
		self::$wpfs->put_contents( $cache_dir . '/DONOTCACHEPAGE', '' );

		if ( $force ) {
			delete_option( 'et_core_page_resource_remove_all' );
		}

		/**
		 * Fires when the static resources are removed.
		 *
		 * @since 4.21.1
		 *
		 * @param mixed $post_id The post ID.
		 */
		do_action( 'et_core_static_resources_removed', $post_id );
	}

	/**
	 * Removes a list of files from the designated directory.
	 *
	 * @param array[] $files     List of patterns to match.
	 * @param string  $cache_dir Cache directory.
	 */
	protected static function _remove_files_in_directory( $files, $cache_dir ) {
		foreach ( $files as $file ) {
			$file = self::$data_utils->normalize_path( $file );

			if ( ! et_()->starts_with( $file, $cache_dir ) ) {
				// File is not located inside cache directory so skip it.
				continue;
			}

			if ( is_file( $file ) ) {
				self::$wpfs->delete( $file );
			}
		}
	}

	public static function wpfs() {
		if ( null !== self::$wpfs ) {
			return self::$wpfs;
		}

		self::startup();

		return self::$wpfs = et_core_cache_dir()->wpfs;
	}

	protected function _initialize_resource() {
		if ( ! self::can_write_to_filesystem() ) {
			$this->base_dir = $this->temp_dir = $this->path = $this->url = ''; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Just ignore this since it's an internal use.

			$this->_register_resource();
			return;
		}

		$file_extension = 'style' === $this->type ? '.min.css' : '.min.js';
		$path           = self::get_cache_directory();
		$url            = et_core_cache_dir()->url;

		$file = et_()->path( $path, $this->post_id, $this->filename . $file_extension );

		if ( file_exists( $file ) ) {
			// Static resource file exists
			$this->path     = self::$data_utils->normalize_path( $file );
			$this->base_dir = dirname( $this->path );
			$this->url      = et_()->path( $url, $this->post_id, basename( $this->path ) );

		} else {
			// Static resource file doesn't exist
			$url  .= "/{$this->post_id}/{$this->filename}{$file_extension}";
			$path .= "/{$this->post_id}/{$this->filename}{$file_extension}";

			$this->base_dir = self::$data_utils->normalize_path( dirname( $path ) );
			$this->temp_dir = $this->base_dir . "/{$this->slug}~";
			$this->path     = $path;
			$this->url      = $url;
		}

		$this->_register_resource();
	}

	protected function _register_resource() {
		$this->enqueued = false;
		$this->inlined  = false;

		$scope = 'global' === $this->post_id ? 'global' : 'post';

		self::$_resources[ $this->slug ] = $this;

		self::$_resources_by_scope[ $scope ][ $this->slug ] = $this;

		self::_assign_output_location( $this->location, $this );
	}

	public function get_data( $context ) {
		$result = '';

		ksort( $this->data, SORT_NUMERIC );

		/**
		 * Filters the resource's data array.
		 *
		 * @since 3.0.52
		 *
		 * @param array[]              $data {
		 *
		 *     @type string[] $priority Resource data.
		 *     ...
		 * }
		 * @param string               $context  Where the data will be used. Accepts 'inline', 'file'.
		 * @param ET_Core_PageResource $resource The resource instance.
		 */
		$resource_data = apply_filters( 'et_core_page_resource_get_data', $this->data, $context, $this );

		foreach ( $resource_data as $priority => $data_part ) {
			foreach ( $data_part as $data ) {
				$result .= $data;
			}
		}

		return $result;
	}

	/**
	 * Whether or not a static resource exists on the filesystem for this instance.
	 *
	 * @return bool
	 */
	public function has_file() {
		if ( ! self::$wpfs || empty( $this->path ) || ! self::can_write_to_filesystem() ) {
			return false;
		}

		return self::$wpfs->exists( $this->path );
	}

	/**
	 * Set the resource's data.
	 *
	 * @param string $data
	 * @param int    $priority
	 */
	public function set_data( $data, $priority = 10 ) {
		if ( 'style' === $this->type ) {
			$data = et_core_data_utils_minify_css( $data );
			// Remove empty media queries
			//           @media   only..and  (feature:value)    { }
			$pattern = '/@media\s+([\w\s]+)?\([\w-]+:[\w\d-]+\)\{\s*\}/';
			$data    = preg_replace( $pattern, '', $data );
		}

		$this->data[ $priority ][] = trim( strip_tags( str_replace( '\n', '', $data ) ) );
	}

	public function set_output_location( $location ) {
		if ( ! self::_validate_property( 'location', $location ) ) {
			return;
		}

		$current_location = $this->location;

		self::_unassign_output_location( $current_location, $this );
		self::_assign_output_location( $location, $this );

		$this->location = $location;
	}

	public function unregister_resource() {
		$scope = 'global' === $this->post_id ? 'global' : 'post';

		unset( self::$_resources[ $this->slug ], self::$_resources_by_scope[ $scope ][ $this->slug ] );

		self::_unassign_output_location( $this->location, $this );
	}
}

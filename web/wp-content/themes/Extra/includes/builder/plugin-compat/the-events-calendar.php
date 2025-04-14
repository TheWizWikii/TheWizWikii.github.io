<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for The Events Calendar plugin.
 *
 * @since 3.10
 *
 * @link https://wordpress.org/plugins/the-events-calendar/
 */
class ET_Builder_Plugin_Compat_The_Events_Calendar extends ET_Builder_Plugin_Compat_Base {
	public $actual_post_query;
	public $spoofed_post_query;

	/**
	 * Constructor.
	 *
	 * @since 3.10
	 */
	public function __construct() {
		$this->plugin_id = 'the-events-calendar/the-events-calendar.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 * Latest plugin version: 4.6.19
	 *
	 * @todo once this issue is fixed in future version, run version_compare() to limit the scope of the hooked fix
	 *
	 * @since 3.10
	 * @since 4.4.6 Bump loop_start hook priority to cover post hijacking issue.
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'wp', array( $this, 'register_spoofed_post_fix' ) );
		add_action( 'loop_start', array( $this, 'maybe_disable_post_spoofing' ), 1001 );
		add_filter( 'wp_insert_post_empty_content', array( $this, 'maybe_allow_save_empty_content' ), 10, 2 );
		add_filter( 'et_builder_enable_jquery_body', array( $this, 'maybe_disable_jquery_body' ) );
		add_action( 'parse_query', array( $this, 'maybe_exclude_post_type' ), 51 );
	}

	/**
	 * Maybe modify the query to exclude The Events Calendar post type when TB post is
	 * being fetched.
	 *
	 * This method mimic conditional statements of Tribe__Events__Query::parse_query to
	 * avoid unexpected issues.
	 *
	 * @since 4.19.0
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function maybe_exclude_post_type( $query ) {
		// Bail early if current page is admin area.
		if ( is_admin() ) {
			return;
		}

		// Bail early if The Events Calendar suppress query filters or query is not home.
		if ( $query->get( 'tribe_suppress_query_filters' ) || ! $query->is_home() ) {
			return;
		}

		// Bail early if current context is the main query and tec_post_type.
		$context = tribe_context();
		if ( $context->is( 'is_main_query' ) && $context->is( 'tec_post_type' ) ) {
			return;
		}

		// Bail early if showEventsInMainLoop disabled or global query is events front page.
		if ( ! tribe_get_option( 'showEventsInMainLoop', false ) || get_query_var( 'tribe_events_front_page' ) ) {
			return;
		}

		// We need to identify whether current query has:
		// - Meta Keys  : _et_library_theme_builder. Only used by TB to get post ID.
		// - Post Types : et_theme_builder, tribe_events.
		$query_post_types = (array) $query->get( 'post_type', array() );
		$query_meta_key   = (array) $query->get( 'meta_key', array() );
		$flip_post_types  = array_flip( $query_post_types );
		if (
			in_array( '_et_library_theme_builder', $query_meta_key, true )
			&& isset( $flip_post_types[ ET_THEME_BUILDER_THEME_BUILDER_POST_TYPE ] )
			&& isset( $flip_post_types[ Tribe__Events__Main::POSTTYPE ] )
		) {
			unset( $flip_post_types[ Tribe__Events__Main::POSTTYPE ] );
			$query->set( 'post_type', array_keys( $flip_post_types ) );
		}
	}

	/**
	 * Disable JQuery Body feature when showing calendar.
	 *
	 * @since 4.10.5
	 *
	 * @param bool $enabled Whether the feature should be enabled or not.
	 *
	 * @return bool
	 */
	public function maybe_disable_jquery_body( $enabled ) {
		return is_post_type_archive( 'tribe_events' ) ? false : $enabled;
	}

	/**
	 * The Events Calendar register Tribe__Events__Templates::maybeSpoofQuery() on wp_head (100) hook
	 * which modifies global $posts. This modified post object breaks anything that came after wp_head
	 * until the spoofed post is fixed. Anything that relies on $post global value on body_class is affected
	 * (ie Divi's hide nav until scroll because it adds classname to <body> to work)
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	function register_spoofed_post_fix() {
		// Bail if global $post doesn't exist for some reason. Just to be safe.
		if ( ! isset( $GLOBALS['post'] ) ) {
			return;
		}

		// Only apply spoofed post fix if builder is used in custom post type page
		if ( ! et_builder_post_is_of_custom_post_type( get_the_ID() ) || ! et_pb_is_pagebuilder_used( get_the_ID() ) ) {
			return;
		}

		// Get actual $post query before Tribe__Events__Templates::maybeSpoofQuery() modifies it
		$this->actual_post_query = $GLOBALS['post'];

		// Return spoofed $post into its actual post then re-return it into spoofed post object
		add_action( 'et_layout_body_class_before', array( $this, 'fix_post_query' ) );
		add_action( 'et_layout_body_class_after', array( $this, 'respoofed_post_query' ) );
	}

	/**
	 * Return spoofed $post into its actual post so anything that relies to $post object works as expected
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	function fix_post_query() {
		// Bail if global $post doesn't exist for some reason. Just to be safe.
		if ( ! isset( $GLOBALS['post'] ) ) {
			return;
		}

		$this->spoofed_post_query = $GLOBALS['post'];

		$GLOBALS['post'] = $this->actual_post_query; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
	}

	/**
	 * Re-return actual $post object into spoofed post so The Event Calendar works as expected
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	function respoofed_post_query() {
		$GLOBALS['post'] = $this->spoofed_post_query; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
	}

	/**
	 * Maybe disable post spoofing when a TB body layout is used.
	 *
	 * @since 4.2.2
	 * @since 4.4.6 Maybe disable post hijacking on Page Template v2.
	 */
	function maybe_disable_post_spoofing() {
		if ( et_theme_builder_overrides_layout( ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ) ) {
			remove_action( 'the_post', array( 'Tribe__Events__Templates', 'spoof_the_post' ) );

			// Ensure to check the class and tribe() method exists. Method tribe() is used
			// to return an instance of the class and resolve the object.
			if ( class_exists( '\Tribe\Events\Views\V2\Template\Page' ) && function_exists( 'tribe' ) ) {
				$page = tribe( \Tribe\Events\Views\V2\Template\Page::class );
				remove_action( 'the_post', array( $page, 'hijack_the_post' ), 25 );
			}
		}
	}

	/**
	 * Allow event with empty title to update post and trigger save_post action when
	 * activating BFB for the first time. So, event post meta can be saved as well.
	 *
	 * @since 4.4.4
	 *
	 * @param bool  $maybe_empty Original status.
	 * @param array $postarr     Array of post data.
	 */
	public function maybe_allow_save_empty_content( $maybe_empty, $postarr ) {
		$post_action        = et_()->array_get( $postarr, 'action' );
		$post_id            = et_()->array_get( $postarr, 'post_ID', 0 );
		$post_status        = et_()->array_get( $postarr, 'post_status' );
		$post_origin_status = et_()->array_get( $postarr, 'original_post_status' );
		$post_type          = et_()->array_get( $postarr, 'post_type' );

		// Ensure to override the status only on very first BFB activation and
		// limited for tribe_events post type only.
		$is_edit_action  = 'editpost' === $post_action;
		$is_builder_used = et_pb_is_pagebuilder_used( $post_id );
		$is_post_draft   = 'draft' === $post_status && 'auto-draft' === $post_origin_status;
		$is_post_event   = 'tribe_events' === $post_type;
		if ( $is_edit_action && $is_builder_used && $is_post_draft && $is_post_event ) {
			return false;
		}

		return $maybe_empty;
	}
}

new ET_Builder_Plugin_Compat_The_Events_Calendar();

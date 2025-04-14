<?php
/**
 * Serves the data to various AJAX requests.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Feature\AjaxData;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ET_Builder_Module_Fields_Factory;

/**
 * AJAX Data class.
 *
 * @since 4.11.0
 */
class ET_Builder_Ajax_Data {

	/**
	 * Holds the class instance.
	 *
	 * @var Class
	 */
	private static $_instance = null;

	/**
	 * Registers the AJAX actions when class is constructed.
	 */
	public function __construct() {
		add_action( 'wp_ajax_et_builder_ajax_get_post_types', array( $this, 'get_post_types' ) );
		add_action( 'wp_ajax_et_builder_ajax_get_authors', array( $this, 'get_authors' ) );
		add_action( 'wp_ajax_et_builder_ajax_get_user_roles', array( $this, 'get_user_roles' ) );
		add_action( 'wp_ajax_et_builder_ajax_get_categories', array( $this, 'get_categories' ) );
		add_action( 'wp_ajax_et_builder_ajax_get_tags', array( $this, 'get_tags' ) );
		add_action( 'wp_ajax_et_builder_ajax_search_products', array( $this, 'search_products' ) );
		add_action( 'wp_ajax_et_builder_ajax_get_display_conditions_status', array( $this, 'get_display_conditions_status' ) );
		add_action( 'wp_ajax_et_builder_ajax_get_post_meta_fields', array( $this, 'get_post_meta_fields' ) );
	}

	/**
	 * Get the instance of the Class
	 *
	 * @return Class Instance
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * AJAX Action for Display Conditions Status.
	 *
	 * @return void
	 */
	public function get_display_conditions_status() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_display_conditions_status', 'nonce', '_POST' );

		/**
		 * Filters "Display Conditions" functionality to determine whether to enable or disable the functionality or not.
		 *
		 * Useful for disabling/enabling "Display Condition" feature site-wide.
		 *
		 * @since 4.13.1
		 *
		 * @param boolean True to enable the functionality, False to disable it.
		 */
		$is_display_conditions_enabled = apply_filters( 'et_is_display_conditions_functionality_enabled', true );

		if ( ! $is_display_conditions_enabled ) {
			wp_send_json_error();
		}

		// $_POST['conditions'] is a JSON so there is no effective way to sanitize it at this level.
		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput  -- Conditions is not stored or displayed therefore XSS safe.
		$conditions = isset( $_POST['conditions'] ) ? $_POST['conditions'] : '';
		$conditions = json_decode( stripslashes( $conditions ), true );

		$status = ET_Builder_Module_Fields_Factory::get( 'DisplayConditions' )->is_displayable( $conditions, true );

		if ( ! $status ) {
			wp_send_json_error();
		}

		wp_send_json_success( $status );
	}

	/**
	 * AJAX Action for Searching within Products.
	 *
	 * @return void
	 */
	public function search_products() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_search_products', 'nonce', '_GET' );

		$current_page     = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
		$current_page     = max( $current_page, 1 );
		$search           = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
		$results_per_page = 20;
		$results          = [
			'results' => [],
			'meta'    => [],
		];

		$query = [
			'post_type'      => 'product',
			'posts_per_page' => $results_per_page,
			'post_status'    => 'publish',
			's'              => $search,
			'orderby'        => 'date',
			'order'          => 'desc',
			'paged'          => $current_page,
		];

		$query = new \WP_Query( $query );

		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $post ) {
				$results['results'][] = [
					'value' => (int) $post->ID,
					'label' => et_core_intentionally_unescaped( wp_strip_all_tags( $post->post_title ), 'react_jsx' ),
				];
			}
		}

		$results['meta']['pagination'] = array(
			'results' => array(
				'per_page' => (int) $results_per_page,
				'total'    => (int) $query->found_posts,
			),
			'pages'   => array(
				'current' => (int) $current_page,
				'total'   => (int) $query->max_num_pages,
			),
		);

		// Only reset if the query is successful to avoid resetting previous query by mistake.
		if ( ! empty( $query->posts ) ) {
			wp_reset_postdata();
		}

		wp_send_json_success( $results );
	}

	/**
	 * AJAX Action for getting a list of all Categories (All Taxonomies Terms) except excluded taxonomies.
	 *
	 * @return void
	 */
	public function get_categories() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_categories', 'nonce', '_GET' );

		$data                = [];
		$search              = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
		$excluded_taxonomies = [ 'post_tag', 'project_tag', 'product_tag', 'nav_menu', 'link_category', 'post_format', 'layout_category', 'layout_pack', 'layout_type', 'scope', 'module_width' ];

		/**
		 * Filters excluded taxonomies for `et_builder_ajax_get_categories` ajax action.
		 *
		 * @since 4.11.0
		 *
		 * @param array $excluded_taxonomies
		 */
		$excluded_taxonomies = apply_filters( 'et_builder_ajax_get_categories_excluded_taxonomies', $excluded_taxonomies );

		$taxonomies = array_diff( get_taxonomies(), $excluded_taxonomies );
		$categories = get_terms(
			[
				'taxonomy'   => $taxonomies,
				'hide_empty' => false,
				'search'     => $search,
			]
		);

		foreach ( $categories as $cat ) {
			$tax_name                 = get_taxonomy( $cat->taxonomy )->label;
			$tax_slug                 = get_taxonomy( $cat->taxonomy )->name;
			$data[ $cat->taxonomy ][] = [
				'name'         => et_core_intentionally_unescaped( wp_strip_all_tags( $cat->name ), 'react_jsx' ),
				'id'           => $cat->term_id,
				'taxonomyName' => et_core_intentionally_unescaped( wp_strip_all_tags( $tax_name ), 'react_jsx' ),
				'taxonomySlug' => $tax_slug,
			];
		}

		$results = [
			'results' => $data,
		];

		if ( is_wp_error( $categories ) ) {
			wp_send_json_error( $categories );
		}

		wp_send_json_success( $results );
	}

	/**
	 * AJAX Action for getting a list of Divi registered Tags.
	 *
	 * @return void
	 */
	public function get_tags() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_tags', 'nonce', '_GET' );

		$data                = [];
		$search              = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
		$included_taxonomies = [ 'post_tag', 'project_tag', 'product_tag' ];

		/**
		 * Filters included taxonomies for `et_builder_ajax_get_tags` ajax action.
		 *
		 * @since 4.11.0
		 *
		 * @param array $included_taxonomies
		 */
		$included_taxonomies = apply_filters( 'et_builder_ajax_get_tags_included_taxonomies', $included_taxonomies );

		$included_taxonomies = array_filter(
			$included_taxonomies,
			function( $taxonomy_slug ) {
				return taxonomy_exists( $taxonomy_slug );
			}
		);

		$tags = get_terms(
			[
				'taxonomy'   => $included_taxonomies,
				'hide_empty' => false,
				'search'     => $search,
			]
		);

		foreach ( $tags as $tag ) {
			$tax_name                 = get_taxonomy( $tag->taxonomy )->label;
			$tax_slug                 = get_taxonomy( $tag->taxonomy )->name;
			$data[ $tag->taxonomy ][] = [
				'name'         => et_core_intentionally_unescaped( wp_strip_all_tags( $tag->name ), 'react_jsx' ),
				'id'           => $tag->term_id,
				'taxonomyName' => et_core_intentionally_unescaped( wp_strip_all_tags( $tax_name ), 'react_jsx' ),
				'taxonomySlug' => $tax_slug,
			];
		}

		$results = [
			'results' => $data,
		];

		if ( is_wp_error( $tags ) ) {
			wp_send_json_error( $tags );
		}

		wp_send_json_success( $results );
	}

	/**
	 * AJAX Action for getting a list of Post Types.
	 *
	 * @return void
	 */
	public function get_post_types() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_post_types', 'nonce', '_GET' );

		$current_page = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
		$current_page = max( $current_page, 1 );
		$post_types   = array_values( get_post_types( [ 'public' => true ], 'objects' ) );

		/**
		 * Filters included post types for `et_builder_ajax_get_post_types` ajax action.
		 *
		 * @since 4.11.0
		 *
		 * @param array $post_types
		 */
		$post_types = apply_filters( 'et_builder_ajax_get_post_types', $post_types );

		$total            = count( $post_types );
		$results_per_page = 20;
		$pages_total      = 1;

		$post_types_data = array_map(
			function( $item ) {
				return [
					'label'          => et_core_intentionally_unescaped( wp_strip_all_tags( $item->labels->name ), 'react_jsx' ),
					'singular_label' => et_core_intentionally_unescaped( wp_strip_all_tags( $item->labels->singular_name ), 'react_jsx' ),
					'value'          => $item->name,
				];
			},
			$post_types
		);

		$results                       = [
			'results' => $post_types_data,
			'meta'    => [],
		];
		$results['meta']['pagination'] = array(
			'results' => array(
				'per_page' => $results_per_page,
				'total'    => $total,
			),
			'pages'   => array(
				'current' => $current_page,
				'total'   => $pages_total,
			),
		);

		wp_send_json_success( $results );
	}

	/**
	 * AJAX Action for getting a list of Authors.
	 *
	 * @return void
	 */
	public function get_authors() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_authors', 'nonce', '_GET' );

		$current_page     = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
		$current_page     = max( $current_page, 1 );
		$results_per_page = 10;
		$users_data       = [];
		$role__in         = [];

		foreach ( wp_roles()->roles as $role_slug => $role ) {
			if ( ! empty( $role['capabilities']['publish_posts'] ) ) {
				$role__in[] = $role_slug;
			}
		}

		/**
		 * Filters included roles for `et_builder_ajax_get_authors` ajax action.
		 *
		 * @since 4.11.0
		 *
		 * @param array $role__in
		 */
		$role__in = apply_filters( 'et_builder_ajax_get_authors_included_roles', $role__in );

		$user_query  = new \WP_User_Query(
			[
				'role__in' => $role__in,
				'fields'   => [ 'ID', 'user_login' ],
				'number'   => $results_per_page,
				'paged'    => 1,
			]
		);
		$found_users = $user_query->get_results();

		if ( ! empty( $found_users ) ) {
			$users_data = array_map(
				function( $item ) {
					return [
						'label' => et_core_intentionally_unescaped( wp_strip_all_tags( $item->user_login ), 'react_jsx' ),
						'value' => $item->ID,
					];
				},
				$found_users
			);
		}

		$total       = $user_query->get_total();
		$pages_total = max( $total / $results_per_page, 1 );

		$results                       = [
			'results' => $users_data,
			'meta'    => [],
		];
		$results['meta']['pagination'] = array(
			'results' => array(
				'per_page' => (int) $results_per_page,
				'total'    => (int) $total,
			),
			'pages'   => array(
				'current' => (int) $current_page,
				'total'   => (int) $pages_total,
			),
		);

		wp_send_json_success( $results );
	}

	/**
	 * AJAX Action for getting a list of User Roles.
	 *
	 * @return void
	 */
	public function get_user_roles() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_user_roles', 'nonce', '_GET' );

		$user_roles = [];

		foreach ( wp_roles()->roles as $key => $value ) {
			$user_roles[] = [
				'label' => et_core_intentionally_unescaped( wp_strip_all_tags( $value['name'] ), 'react_jsx' ),
				'value' => $key,
			];
		}

		/**
		 * Filters included user roles for `et_builder_ajax_get_user_roles` ajax action.
		 *
		 * @since 4.11.0
		 *
		 * @param array $user_roles
		 */
		$user_roles = apply_filters( 'et_builder_ajax_get_user_roles_included_roles', $user_roles );

		$results = [
			'results' => $user_roles,
		];

		wp_send_json_success( $results );
	}

	/**
	 * AJAX Action for getting a list of all meta fields assigned to a post.
	 *
	 * @return void
	 */
	public function get_post_meta_fields() {
		et_core_security_check( 'edit_posts', 'et_builder_ajax_get_post_meta_fields', 'nonce', '_GET' );

		$data        = [];
		$post_id     = isset( $_GET['postId'] ) ? sanitize_text_field( $_GET['postId'] ) : '';
		$meta_fields = get_post_meta( (int) $post_id );

		/**
		 * Filters included meta fields for `et_builder_ajax_get_post_meta_fields` ajax action.
		 *
		 * @since 4.14.3
		 *
		 * @param array $meta_fields
		 */
		$meta_fields = apply_filters( 'et_builder_ajax_get_post_meta_fields', $meta_fields );

		$data = is_array( $meta_fields ) ? $meta_fields : [];

		$results = [
			'results' => $data,
		];

		wp_send_json_success( $results );
	}

}

ET_Builder_Ajax_Data::get_instance();

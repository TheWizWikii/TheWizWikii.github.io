<?php
/**
 * Rest API: Layout Block
 *
 * @package Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for custom REST API endpoint for Divi Layout block.
 */
class ET_Api_Rest_Block_Layout {

	/**
	 * Instance of `ET_Api_Rest_Block_Layout`.
	 *
	 * @var ET_Api_Rest_Block_Layout
	 */
	private static $_instance;

	/**
	 * Constructor.
	 *
	 * ET_Api_Rest_Block_Layout constructor.
	 */
	public function __construct() {
		$this->register();
	}

	/**
	 * Get class instance
	 *
	 * @since 4.1.0
	 *
	 * @return object class instance
	 */
	public static function instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Register callback for Layout block REST API
	 *
	 * @since 4.1.0
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes for Layout block
	 *
	 * @since 4.1.0
	 */
	public function register_routes() {
		register_rest_route(
			'divi/v1',
			'get_layout_content',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_layout_content_callback' ),
				'args'                => array(
					'id'    => array(
						// using intval directly doesn't work, hence the custom callback.
						'sanitize_callback'   => array( $this, 'sanitize_int' ),
						'validation_callback' => 'is_numeric',
					),
					'nonce' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'permission_callback' => array( $this, 'rest_api_layout_block_permission' ),
			)
		);

		register_rest_route(
			'divi/v1',
			'block/layout/builder_edit_data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'process_builder_edit_data' ),
				'args'                => array(
					'action'        => array(
						'sanitize_callback'   => array( $this, 'sanitize_action' ), // update|delete|get.
						'validation_callback' => array( $this, 'validate_action' ),
					),
					'postId'        => array(
						'sanitize_callback'   => array( $this, 'sanitize_int' ),
						'validation_callback' => 'is_numeric',
					),
					'blockId'       => array(
						'sanitize_callback' => 'sanitize_title',
					),
					'layoutContent' => array(
						'sanitize_callback' => 'wp_kses_post',
					),
					'nonce'         => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'permission_callback' => array( $this, 'rest_api_layout_block_permission' ),
			)
		);
	}

	/**
	 * Get layout content based on given post ID
	 *
	 * @since 4.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return string|WP_Error
	 */
	public function get_layout_content_callback( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'id' );
		$nonce   = $request->get_param( 'nonce' );

		// Action nonce check. REST API actually has checked for nonce at cookie sent on every
		// request and performed capability-based check. This check perform action-based nonce
		// check to strengthen the security
		// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/.
		if ( ! wp_verify_nonce( $nonce, 'et_rest_get_layout_content' ) ) {
			return new WP_Error(
				'invalid_nonce',
				esc_html__( 'Invalid nonce', 'et_builder' ),
				array(
					'status' => 400,
				)
			);
		}

		// request has to have id param.
		if ( ! $post_id ) {
			return new WP_Error(
				'no_layout_id',
				esc_html__( 'No layout id found', 'et_builder' ),
				array(
					'status' => 400,
				)
			);
		}

		$post = get_post( $post_id );

		if ( ! isset( $post->post_content ) || ! $post->post_content ) {
			return new WP_Error(
				'no_layout_found',
				esc_html__( 'No valid layout content found.', 'et_builder' ),
				array(
					'status' => 404,
				)
			);
		}

		return $post->post_content;
	}

	/**
	 *  Process /block/layout/builder_edit_data route request
	 *
	 * @param WP_Rest_Request $request Request to prepare items for.
	 *
	 * @return string|WP_Error
	 * @since 4.1.0
	 */
	public function process_builder_edit_data( WP_Rest_Request $request ) {
		$post_id  = $request->get_param( 'postId' );
		$block_id = $request->get_param( 'blockId' );
		$nonce    = $request->get_param( 'nonce' );

		// No post ID.
		if ( empty( $post_id ) ) {
			return new WP_Error(
				'no_post_id',
				esc_html__( 'No post id', 'et_builder' ),
				array(
					'status' => 400,
				)
			);
		}

		// No block ID.
		if ( empty( $block_id ) ) {
			return new WP_Error(
				'no_block_id',
				esc_html__( 'No block id', 'et_builder' ),
				array(
					'status' => 400,
				)
			);
		}

		// Action nonce check. REST API actually has checked for nonce at cookie sent on every
		// request and performed capability-based check. This check perform action-based nonce
		// check to strengthen the security
		// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/.
		if ( ! wp_verify_nonce( $nonce, 'et_rest_process_builder_edit_data' ) ) {
			return new WP_Error(
				'invalid_nonce',
				esc_html__( 'Invalid nonce', 'et_builder' ),
				array(
					'status' => 400,
				)
			);
		}

		$result        = '';
		$post_meta_key = "_et_block_layout_preview_{$block_id}";

		switch ( $request->get_param( 'action' ) ) {
			case 'get':
				$result = get_post_meta( $post_id, $post_meta_key, true );
				break;
			case 'update':
				$layout_content = $request->get_param( 'layoutContent' );

				// No layout content.
				if ( empty( $layout_content ) ) {
					return new WP_Error(
						'no_layout_content',
						esc_html__( 'No layout content', 'et_builder' ),
						array(
							'status' => 400,
						)
					);
				}

				$saved_layout_content = get_post_meta( $post_id, $post_meta_key, true );

				if ( ! empty( $saved_layout_content ) && $saved_layout_content === $layout_content ) {
					// If for some reason layout exist and identical to the one being sent, return
					// true because update_post_meta() returns false if it updates the meta key and
					// the value doesn't change.
					$result = true;
				} else {
					// Otherwise, attempt to save post meta and returns how it goes.
					$result = update_post_meta(
						$post_id,
						$post_meta_key,
						$layout_content
					);
				}

				break;
			case 'delete':
				$result = delete_post_meta( $post_id, $post_meta_key );
				break;
			default:
				return new WP_Error(
					'no_valid_action',
					esc_html__( 'No valid action found', 'et_builder' ),
					array(
						'status' => 400,
					)
				);
		}

		return array(
			'result' => $result,
		);
	}

	/**
	 * Sanitize int value
	 *
	 * @since 4.1.0
	 *
	 * @param int|mixed $value Value.
	 *
	 * @return int
	 */
	public function sanitize_int( $value ) {
		return intval( $value );
	}

	/**
	 *  Sanitize request "action" argument
	 *
	 * @since 4.1.0
	 *
	 * @param string $value Action value.
	 *
	 * @return string
	 */
	public function sanitize_action( $value ) {
		return $this->validate_action( $value ) ? $value : '';
	}

	/**
	 * Validate request "action" argument
	 *
	 * @since 4.1.0
	 *
	 * @param string $value Action value.
	 *
	 * @return bool
	 */
	public function validate_action( $value ) {
		$valid_builder_edit_data_actions = array(
			'get',
			'update',
			'delete',
		);

		return in_array( $value, $valid_builder_edit_data_actions, true );
	}

	/**
	 * Permission callback for get layout permalink REST API endpoint
	 *
	 * @since 4.1.0
	 *
	 * @return bool
	 */
	public function rest_api_layout_block_permission() {
		return current_user_can( 'edit_posts' ) && et_pb_is_allowed( 'use_visual_builder' );
	}
}

// Initialize ET_Api_Rest_Block_Layout.
ET_Api_Rest_Block_Layout::instance();

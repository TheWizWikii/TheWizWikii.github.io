<?php
/**
 * Display Conditions functionalities (tracking post visits etc.) used site wide.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display Conditions functionalities to be used site wide.
 *
 * @since 4.11.0
 */
class ET_Builder_Display_Conditions {

	/**
	 * Hold the class instance.
	 *
	 * @var Class
	 */
	private static $_instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return ET_Builder_Display_Conditions
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init actions and filters needed for Display Condition's functionality
	 */
	public function __construct() {
		add_filter( 'et_module_process_display_conditions', array( $this, 'process_display_conditions' ), 10, 3 );
		add_filter( 'et_is_display_conditions_functionality_enabled', array( $this, 'check_if_wp_version_is_sufficient' ) );
		add_action( 'wp', array( $this, 'post_visit_set_cookie' ) );
		add_action( 'template_redirect', array( $this, 'number_of_views_set_cookie' ) );
		add_action( 'save_post', array( $this, 'save_tracking_post_ids' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'delete_tracking_post_ids' ), 10, 1 );
	}

	/**
	 * Processes "Display Conditions" of a module and decides whether to display a module or not.
	 *
	 * We do need to render the module first and then decide to keep it or not, This is because we want the styles of
	 * the module (shortcode) and any nested modules inside it to get registered so "Dynamic Assets" would include the
	 * styles of all modules used on the page. Ref: https://github.com/elegantthemes/Divi/issues/24965
	 *
	 * @since 4.13.1
	 *
	 * @param string             $output           HTML output of the rendered module.
	 * @param string             $render_method    The render method used to render the module, Typically it's either
	 *                                             'render' or 'render_as_builder_data` @see ET_Builder_Element::_render().
	 *
	 * @param ET_Builder_Element $element_instance The current instance of ET_Builder_Element.
	 *
	 * @return string                              HTML output of the rendered module if conditions are met, Empty otherwise.
	 */
	public function process_display_conditions( $output, $render_method, $element_instance ) {
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
			return $output;
		}

		// Setup variables.
		$is_displayable                        = true;
		$is_display_conditions_set             = isset( $element_instance->props['display_conditions'] ) && ! empty( $element_instance->props['display_conditions'] );
		$is_display_conditions_as_base64_empty = 'W10=' === $element_instance->props['display_conditions'];
		$has_display_conditions                = $is_display_conditions_set && ! $is_display_conditions_as_base64_empty;

		// Check if display_conditions attribute is defined, Decode the data and check if it is displayable.
		if ( $has_display_conditions ) {
			$display_conditions_json = base64_decode( $element_instance->props['display_conditions'] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- The returned data is an array and necessary validation checks are performed.
		}
		if ( $has_display_conditions && false !== $display_conditions_json ) {
			$display_conditions = json_decode( $display_conditions_json, true );
			$is_displayable     = \ET_Builder_Module_Fields_Factory::get( 'DisplayConditions' )->is_displayable( $display_conditions );
		}

		$is_vb_ajax_nonce_valid = isset( $_POST['et_pb_process_computed_property_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['et_pb_process_computed_property_nonce'] ), 'et_pb_process_computed_property_nonce' );

		// Check if we're rendering on frontend, Then decide whether to keep the output or erase it.
		if ( 'render' === $render_method ) {
			if ( wp_doing_ajax() && $is_vb_ajax_nonce_valid && et_pb_is_pagebuilder_used( get_the_ID() ) ) {
				// "Blog Module" in VB will be rendered like a normal frontend request not as builder data, Here we retain the output
				// so it will always be visible in VB ignoring Display Conditions Ref: https://github.com/elegantthemes/Divi/issues/23309, https://github.com/elegantthemes/Divi/issues/25463.
				$output = $output;
			} elseif ( 'et_pb_post_content' === $element_instance->slug && ! $is_displayable && et_core_is_fb_enabled() ) {
				// When VB is loaded and "Post Content" Module is used in TB and it's not displayable, set the correct
				// output so it'd be displayed in VB and TB respectively Ref: https://github.com/elegantthemes/Divi/issues/23479.
				$output = $output;
			} else {
				// All other scenarios will fall here, Normal frontend request, AJAX frontend request, etc.
				$output = ( $is_displayable ) ? $output : '';
			}
		}

		return $output;
	}

	/**
	 * Checks if WordPress version is sufficient for "Display Conditions" feature.
	 *
	 * @since 4.13.1
	 *
	 * @param boolean $is_display_conditions_enabled True if "Display Conditions" functionality is enabled, False if it's disabled.
	 *
	 * @return boolean True if WordPress version is sufficient & "Display Condition" functionality is enabled, False otherwise.
	 */
	public function check_if_wp_version_is_sufficient( $is_display_conditions_enabled ) {
		/**
		 * We intentionally check `$is_display_conditions_enabled` to avoid enabling the functionality if it has been
		 * disabled via `add_filter()` with lower priority sooner.
		 */
		return version_compare( get_bloginfo( 'version' ), '5.3', '>=' ) && $is_display_conditions_enabled ? true : false;
	}

	/**
	 * Saves Post IDs selected in PageVisit/PostVisit Display Conditions into WP Options.
	 *
	 * This data will be used to only track the Posts which are selected by the user
	 * It is to keep the PageVisit/PostVisit related Cookie minimal and under 4KB limitation.
	 *
	 * @since 4.11.0
	 *
	 * @param  int     $post_id Post ID which is being saved.
	 * @param  WP_Post $post    Post object which is being saved.
	 * @param  bool    $update  Whether this is an existing post being updated.
	 *
	 * @return void
	 */
	public function save_tracking_post_ids( $post_id, $post, $update ) {
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
			return;
		}

		/**
		 * Validation and Security Checks.
		 */
		if ( ! $post || ! $post instanceof WP_Post ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return;
		}

		$post_type = get_post_type_object( $post->post_type );
		if ( ! $post_type instanceof WP_Post_Type || ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['et_fb_save_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_save_nonce'] ), 'et_fb_save_nonce' ) ) {
			return;
		}

		/**
		 * Setup Prerequisites.
		 */
		$tracking_post_ids        = [];
		$content                  = get_the_content( null, false, $post );
		$preg_match               = preg_match_all( '/display_conditions="[^"]*"/mi', $content, $matches, PREG_SET_ORDER );  // Return format: `display_conditions="base_64_encoded_data"`.
		$display_conditions_attrs = array_reduce( $matches, 'array_merge', [] );  // Flatten and Store All `display_conditions` attributes found.

		/**
		 * Decode each `display_conditions` attribute, and store post IDs used in PageVisit/PostVisit conditions.
		 */
		foreach ( $display_conditions_attrs as $display_condition_attr ) {
			$display_condition_base64 = substr( $display_condition_attr, strpos( $display_condition_attr, '"' ), -1 );
			$display_conditions       = json_decode( base64_decode( $display_condition_base64 ), true );  // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- The returned data is an array and necessary validation checks are performed.

			if ( ! is_array( $display_conditions ) ) {
				continue;
			}

			foreach ( $display_conditions as $display_condition ) {
				$condition_name     = $display_condition['condition'];
				$condition_settings = $display_condition['conditionSettings'];

				if ( 'pageVisit' !== $condition_name && 'postVisit' !== $condition_name ) {
					continue;
				}

				$pages_raw         = isset( $condition_settings['pages'] ) ? $condition_settings['pages'] : [];
				$pages_ids         = array_map(
					function( $item ) {
						return isset( $item['value'] ) ? (int) $item['value'] : null;
					},
					$pages_raw
				);
				$pages_ids         = array_filter( $pages_ids );
				$tracking_post_ids = array_merge( $pages_ids, $tracking_post_ids );
			}
		}

		$tracking_post_ids = array_unique( $tracking_post_ids );

		if ( $tracking_post_ids ) {
			$result = [ (int) $post_id => $tracking_post_ids ];
		} else {
			$result = null;
		}

		$wp_option = get_option( 'et_display_conditions_tracking_post_ids', null );

		// If option exist, Either update it OR remove from it.
		if ( is_array( $wp_option ) ) {
			if ( $result ) {
				$result = array_replace( $wp_option, $result );
			} else {
				$result = array_filter(
					$wp_option,
					function( $key ) use ( $post_id ) {
						return $key !== $post_id;
					},
					ARRAY_FILTER_USE_KEY
				);
			}
		}

		if ( $wp_option === $result ) {
			return;
		}

		update_option( 'et_display_conditions_tracking_post_ids', $result );
	}

	/**
	 * Deletes Post IDs selected in PageVisit/PostVisit Display Conditions from WP Options.
	 *
	 * This data will be used to only track the Posts which are selected by the user
	 * It is to keep the PageVisit/PostVisit related Cookie minimal and under 4KB limitation.
	 *
	 * @since 4.11.0
	 *
	 * @param  int $post_id Post ID which is being deleted.
	 *
	 * @return void
	 */
	public function delete_tracking_post_ids( $post_id ) {
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
			return;
		}

		$post               = get_post( $post_id );
		$wp_option          = get_option( 'et_display_conditions_tracking_post_ids', null );
		$is_wp_option_exist = is_array( $wp_option ) && ! empty( $wp_option );

		if ( ! $is_wp_option_exist ) {
			return;
		}

		if ( ! $post || ! $post instanceof WP_Post ) {
			return;
		}

		// Get real Post ID if Revision ID is passed, Using `Empty Trash` button will set $post_id to revision id.
		$revision_parent_id = wp_is_post_revision( $post_id );
		if ( $revision_parent_id ) {
			$post_id = $revision_parent_id;
		}

		$post_type = get_post_type_object( $post->post_type );
		if ( ! current_user_can( $post_type->cap->delete_post, $post_id ) ) {
			return;
		}

		$result = array_filter(
			$wp_option,
			function( $key ) use ( $post_id ) {
				return (int) $key !== (int) $post_id;
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( $wp_option === $result ) {
			return;
		}

		update_option( 'et_display_conditions_tracking_post_ids', $result );
	}

	/**
	 * Sets a cookie based on page visits so Page/Post Visit Display Conditions would function as expected.
	 *
	 * @since 4.11.0
	 *
	 * @return void
	 */
	public function post_visit_set_cookie() {
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
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$current_post_id         = get_queried_object_id();
		$new_cookie              = [];
		$has_visited_page_before = false;
		$wp_option               = get_option( 'et_display_conditions_tracking_post_ids', null );
		$is_wp_option_exist      = is_array( $wp_option ) && ! empty( $wp_option );
		$flatten_wp_option       = is_array( $wp_option ) ? array_unique( array_reduce( $wp_option, 'array_merge', [] ) ) : [];
		$is_post_id_in_wp_option = array_search( $current_post_id, $flatten_wp_option, true ) !== false;

		if ( ! $is_wp_option_exist || ! $is_post_id_in_wp_option ) {
			return;
		}

		if ( isset( $_COOKIE['divi_post_visit'] ) ) {
			$new_cookie = json_decode( base64_decode( $_COOKIE['divi_post_visit'] ), true ); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- Cookie is not stored or displayed therefore XSS safe, base64_decode returned data is an array and necessary validation checks are performed.
		}

		if ( $new_cookie && is_array( $new_cookie ) ) {
			$has_visited_page_before = array_search( $current_post_id, array_column( $new_cookie, 'id' ), true );
		}

		if ( false === $has_visited_page_before ) {
			$new_cookie[] = [
				'id' => $current_post_id,
			];
			$new_cookie   = base64_encode( wp_json_encode( $new_cookie ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode  -- base64_encode data is an array.
			setrawcookie( 'divi_post_visit', $new_cookie, time() + 3600 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		}
	}

	/**
	 * Sets a cookie based on how many times a module is displayed so "Number of Views" Condition would function as expected.
	 *
	 * @since 4.11.0
	 *
	 * @return void
	 */
	public function number_of_views_set_cookie() {
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
			return;
		}

		// Do not run on VB itself.
		if ( et_core_is_fb_enabled() ) {
			return;
		}

		/**
		 * This is to ensure that network request such as '/favicon.ico' won't change the cookie
		 * since those requests do trigger these functions to run again without the proper context
		 * resulting updating cookie >=2 times on 1 page load.
		 */
		$is_existing_wp_query = ( is_home() || is_404() || is_archive() || is_search() );
		if ( get_queried_object_id() === 0 && ! $is_existing_wp_query ) {
			return;
		}

		// Setup prerequisite.
		$display_conditions_attrs = [];
		$cookie                   = [];
		$entire_page_content      = \Feature\ContentRetriever\ET_Builder_Content_Retriever::init()->get_entire_page_content( get_queried_object_id() );

		// Find all display conditions used in the page, flat the results, filter to only include NumberOfViews conditions.
		if ( preg_match_all( '/(?<=display_conditions=")[^"]*/mi', $entire_page_content, $matches, PREG_SET_ORDER ) ) {
			$display_conditions_attrs = array_reduce( $matches, 'array_merge', [] ); // Flatten and Store All `display_conditions` attributes found.
			$cookie                   = $this->number_of_views_process_conditions( $display_conditions_attrs );
			if ( false === $cookie ) {
				return;
			}
		}

		/**
		 * Encode cookie content and set cookie only if quired object id can be retrieved.
		 * `serrawcookie` is used to ignore automatic `urlencode` with `setcookie` since it corrupts base64 data.
		 */
		if ( ! empty( $cookie ) ) {
			$cookie = base64_encode( wp_json_encode( $cookie ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode  -- base64_encode data is an array.
			setrawcookie( 'divi_module_views', $cookie, time() + 3600 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN );
		}

	}

	/**
	 * Checks "NumberOFViews" conditions against respective $_COOKIE content and updates/reset the
	 * condition when necessary.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $display_conditions_attrs Array of base64 encoded conditions.
	 *
	 * @return array
	 */
	public function number_of_views_process_conditions( $display_conditions_attrs ) {
		$is_cookie_set    = isset( $_COOKIE['divi_module_views'] );
		$current_datetime = current_datetime();
		$decoded_cookie   = $is_cookie_set ? json_decode( base64_decode( $_COOKIE['divi_module_views'] ), true ) : []; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- Cookie is not stored or displayed therefore XSS safe, The returned data is an array and necessary validation checks are performed.
		$cookie           = is_array( $decoded_cookie ) ? $decoded_cookie : [];

		// Decode NumberOfViews conditions one by one then set or update cookie.
		foreach ( $display_conditions_attrs as $condition_base64 ) {
			$display_conditions = json_decode( base64_decode( $condition_base64 ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- The returned data is an array and necessary validation checks are performed.

			if ( ! is_array( $display_conditions ) ) {
				continue;
			}

			foreach ( $display_conditions as $display_condition ) {
				$condition_id       = $display_condition['id'];
				$condition_name     = $display_condition['condition'];
				$condition_settings = $display_condition['conditionSettings'];

				if ( 'numberOfViews' !== $condition_name ) {
					continue;
				}

				$is_reset_on               = 'on' === $condition_settings['resetAfterDuration'] ? true : false;
				$reset_time                = $condition_settings['displayAgainAfter'] . ' ' . $condition_settings['displayAgainAfterUnit'];
				$is_condition_id_in_cookie = array_search( $condition_id, array_column( $cookie, 'id' ), true ) !== false ? true : false;

				if ( $is_reset_on && $is_cookie_set && isset( $cookie[ $condition_id ] ) ) {
					$first_visit_timestamp = $cookie[ $condition_id ]['first_visit_timestamp'];
					$first_visit_datetime  = $current_datetime->setTimestamp( $first_visit_timestamp );
					$reset_datetime        = $first_visit_datetime->modify( $reset_time );
					if ( $current_datetime > $reset_datetime ) {
						$cookie[ $condition_id ]['visit_count']           = 1;
						$cookie[ $condition_id ]['first_visit_timestamp'] = $current_datetime->getTimestamp();
						continue;
					}
				}

				if ( $is_cookie_set && $is_condition_id_in_cookie ) {
					$cookie[ $condition_id ]['visit_count'] += 1;
				} else {
					$cookie[ $condition_id ] = [
						'id'                    => $condition_id,
						'visit_count'           => 1,
						'first_visit_timestamp' => $current_datetime->getTimestamp(),
					];
				}
			}
		}

		return $cookie;
	}

}

ET_Builder_Display_Conditions::get_instance();

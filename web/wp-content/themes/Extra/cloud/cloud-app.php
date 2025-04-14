<?php

if ( ! defined( 'ET_CLOUD_SERVER_URL' ) ) {
	define( 'ET_CLOUD_SERVER_URL', 'https://cloud.elegantthemes.com' );
}

class ET_Cloud_App {
	/**
	 * @var ET_Cloud_App
	 */
	private static $_instance;

	/**
	 * Get the class instance.
	 *
	 * @since 3.0.99
	 *
	 * @return ET_Builder_Library
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self;
		}

		add_action( 'wp_ajax_et_cloud_update_tokens', array( 'ET_Cloud_App', 'ajaxRefreshTokens' ) );
		add_action( 'wp_ajax_et_cloud_remove_tokens', array( 'ET_Cloud_App', 'removeTokens' ) );

		add_filter( 'et_builder_load_requests', array( 'ET_Cloud_App', 'updateAjaxCallsList' ) );

		return self::$_instance;
	}

	public static function updateAjaxCallsList() {
		return array( 'action' => array( 'et_cloud_update_tokens' ) );
	}

	public static function removeTokens() {
		$nonce = $_POST['et_cloud_token_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'et_cloud_remove_token' ) ) {
			die();
		}

		$user_id      = (string) get_current_user_id();
		$saved_tokens = get_option( 'et_cloud_refresh_token', array() );

		$saved_tokens[ $user_id ] = array();

		// Save empty refresh token for current user.
		update_option( 'et_cloud_refresh_token', $saved_tokens );

		wp_send_json_success();
	}

	public static function ajaxRefreshTokens() {
		$nonce = $_POST['et_cloud_token_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'et_cloud_refresh_token' ) ) {
			die();
		}

		return ET_Cloud_App::refreshTokens();
	}

	public static function refreshTokens() {
		// Clear options cache to make sure we're using the latest version of the token.
		wp_cache_delete( 'et_cloud_refresh_token', 'options' );

		$user_id         = (string) get_current_user_id();
		$saved_tokens    = get_option( 'et_cloud_refresh_token', array() );
		$access_token    = sanitize_text_field( $_POST['et_cloud_access_token'] );
		$save_session    = wp_validate_boolean( sanitize_text_field( $_POST['et_cloud_save_session'] ) );
		$token_part      = sanitize_text_field( $_POST['et_cloud_refresh_token_part'] );
		$user_token_data = isset( $saved_tokens[ $user_id ] ) ? $saved_tokens[ $user_id ] : array();
		$is_refresh      = ! $access_token || '' === $access_token;
		$url             = ET_CLOUD_SERVER_URL . '/wp/wp-json/cloud/v1/activate';

		$refresh_token = '';

		if ( $is_refresh && is_array( $user_token_data ) && !empty( $user_token_data ) ) {
			$refresh_token = $user_token_data['is_full_token'] ? $user_token_data['refresh_token'] : $user_token_data['refresh_token'] . $token_part;

			$save_session = $is_refresh ? $user_token_data['is_full_token'] : $save_session;
		}

		if ( $is_refresh ) {
			if ( ! $save_session && ( ! $token_part || '' === $token_part ) ) {
				wp_send_json_error( array(
					'error'     => '401',
					'errorType' => 'silent',
				) );

				return;
			}

			$is_updating_token = 'updating' === get_transient( 'et_cloud_access_token_update_status' );

			// Previous request is not finished yet. Try again after 2 seconds.
			// Otherwise this request will fail with 401 error.
			if ( $is_updating_token ) {
				sleep(2);
				ET_Cloud_App::refreshTokens();
			}

			// Set updating token flag with 5 seconds expiration.
			set_transient( 'et_cloud_access_token_update_status', 'updating', 5 );
		}

		if ( ( ! $is_refresh && '' === $access_token ) || ( $is_refresh && '' === $refresh_token ) ) {
			wp_send_json_error( array(
				'error' => '401',
			) );

			return;
		}

		if ( $is_refresh ) {
			$token_array        = explode('.', $refresh_token);
			// Token is a json string of base64 encoded array. Decode it to access the data in token.
			$refresh_token_data = json_decode(base64_decode($token_array[1]));

			if ( !empty( $refresh_token_data ) && is_object( $refresh_token_data ) && isset( $refresh_token_data->aud ) ) {
				$user_cloud_endpoint = $refresh_token_data->aud[1];
			} else {
				wp_send_json_error( array(
					'error' => '401',
				) );

				return;
			}

			$url = sprintf('%1$s/wp-json/auth/v1/token', $user_cloud_endpoint);
		}

		if ( ! $is_refresh ) {
			update_option( 'et_server_domain_token', $access_token );
		}

		$request_body = array();

		$auth_token = $is_refresh ? $refresh_token : $access_token;

		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $auth_token,
				'X-ET-ORIGIN'   => site_url(),
			),
			'body' => $request_body,
		) );

		if ( is_wp_error( $response ) ) {
			// Delete updating token flag.
			delete_transient( 'et_cloud_access_token_update_status' );

			wp_send_json_error( array(
				'error' => 'Cloud Request Failed. Please Try Again Later',
			) );

			return;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$decoded_body  = json_decode( $response_body, TRUE );

		// Valid response should contain 2 tokens.
		if ( !isset( $decoded_body['refresh_token'] ) || !isset( $decoded_body['access_token'] ) || 200 !== $response_code ) {
			// Authorization error. Need to reset all tokens and ask user to login again.
			if ( 401 === $response_code ) {
				$saved_tokens[ $user_id ] = array();

				// Save empty refresh token for current user.
				update_option( 'et_cloud_refresh_token', $saved_tokens, false );

				// Delete updating token flag.
				delete_transient( 'et_cloud_access_token_update_status' );

				wp_send_json_error( array(
					'error' => '401',
				) );

				return;
			}

			wp_send_json_error( array(
				'error' => wp_remote_retrieve_response_message( $response ),
			) );

			return;
		}

		$refresh_token = $decoded_body['refresh_token'];
		$access_token  = $decoded_body['access_token'];
		$token_to_save = $refresh_token;
		$token_part    = '';

		// We shouldn't save the full token, so user cannot use this token in other browser.
		if ( ! $save_session ) {
			$token_length  = (int) strlen( $refresh_token );
			$token_parts   = str_split( $refresh_token, ceil( $token_length / 2 ) );
			$token_to_save = $token_parts[0];
			$token_part    = $token_parts[1];
		}

		$saved_tokens[ $user_id ] = array(
			'refresh_token' => sanitize_text_field( $token_to_save ),
			'is_full_token' => $save_session,
		);

		// Save refresh token for current user.
		update_option( 'et_cloud_refresh_token', $saved_tokens, false );

		// Delete updating token flag.
		delete_transient( 'et_cloud_access_token_update_status' );

		// Save Access Token for 30 seconds so it can be quickly retrieved by the VB.
		set_transient( 'et_cloud_access_token', $access_token, 30);

		wp_send_json_success( array(
			'accessToken'      => $decoded_body['access_token'],
			'refreshTokenPart' => $token_part,
			'domainToken'      => get_option( 'et_server_domain_token', '' ),
			'sharedFolders'    => self::normalize_shared_cloud_array( $decoded_body['clouds'] ),
		) );
	}

	/**
	 * Normalize shared cloud array from the server response.
	 *
	 * @param array $shared_cloud_array Raw shared clouds array.
	 *
	 * @return array
	 */
	public static function normalize_shared_cloud_array( $shared_cloud_array ) {
		if ( empty( $shared_cloud_array ) ) {
			return null;
		}

		$normalized_array = array();

		foreach ( $shared_cloud_array as $cloud_id => $shared_cloud ) {
			$use_permission    = isset( $shared_cloud['permissions']['use_items'] ) ? $shared_cloud['permissions']['use_items'] : false;
			$add_permission    = isset( $shared_cloud['permissions']['add_items'] ) ? $shared_cloud['permissions']['add_items'] : false;
			$edit_permission   = isset( $shared_cloud['permissions']['edit_items'] ) ? $shared_cloud['permissions']['edit_items'] : false;
			$delete_permission = isset( $shared_cloud['permissions']['delete_items'] ) ? $shared_cloud['permissions']['delete_items'] : false;

			// No permission to use this cloud.
			if ( ! $use_permission && ! $add_permission && ! $edit_permission && ! $delete_permission ) {
				continue;
			}

			$normalized_array[] = array(
				'id'          => $cloud_id,
				'name'        => $shared_cloud['owner'],
				'count'       => $shared_cloud['item_counts'],
				'endpoint'    => $shared_cloud['endpoint'],
				'permissions' => array(
					'use'    => $shared_cloud['permissions']['use_items'],
					'add'    => $shared_cloud['permissions']['add_items'],
					'edit'   => $shared_cloud['permissions']['edit_items'],
					'delete' => $shared_cloud['permissions']['delete_items'],
				),
			);
		}

		return $normalized_array;
	}

	public static function hasRefreshToken() {
		$user_id       = (string) get_current_user_id();
		$saved_tokens  = get_option( 'et_cloud_refresh_token', array() );
		$refresh_token = isset( $saved_tokens[ $user_id ] ) ? $saved_tokens[ $user_id ] : array();

		return $refresh_token && ! empty( $refresh_token );
	}

	public static function get_cloud_helpers() {
		if ( !defined( 'ET_CLOUD_PLUGIN_DIR' ) ) {
			define( 'ET_CLOUD_PLUGIN_DIR', get_template_directory() . '/cloud' );
		}

		$home_url  = wp_parse_url( get_site_url() );
		$etAccount = et_core_get_et_account();

		return [
			'i18n' => require ET_CLOUD_PLUGIN_DIR . '/i18n/library.php',
			'nonces' => [
				'et_cloud_download_item'                           => wp_create_nonce( 'et_cloud_download_item' ),
				'et_cloud_refresh_token'                           => wp_create_nonce( 'et_cloud_refresh_token' ),
				'et_cloud_remove_token'                            => wp_create_nonce( 'et_cloud_remove_token' ),
				'et_builder_split_library_item'          => wp_create_nonce( 'et_builder_split_library_item' ),
				'et_builder_ajax_save_domain_token'                => wp_create_nonce( 'et_builder_ajax_save_domain_token' ),
				'et_builder_marketplace_api_get_layouts'           => wp_create_nonce( 'et_builder_marketplace_api_get_layouts' ),
				'et_builder_marketplace_api_get_layout_categories' => wp_create_nonce( 'et_builder_marketplace_api_get_layout_categories' ),
			],
			'ajaxurl'              => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
			'home_url'             => isset( $home_url['path'] ) ? untrailingslashit( $home_url['path'] ) : '/',
			'website_url'          => $home_url['host'],
			'predefined_items_url' => ET_CLOUD_SERVER_URL . '/wp/wp-json/cloud/v1',
			'etAccount'            => [
				'username' => $etAccount['et_username'],
				'apiKey'   => $etAccount['et_api_key'],
			],
			'domainToken'         => get_option( 'et_server_domain_token', '' ),
			'initialCloudStatus'  => self::hasRefreshToken() ? 'on' : 'off',
			'localCategoriesEdit' => current_user_can( 'manage_categories' ) ? 'allowed' : 'notAllowed',
		];
	}

	/**
	 * Load the Cloud App scripts.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public static function load_js( $enqueue_prod_scripts = true, $skip_react_loading = false ) {
		if ( !defined( 'ET_CLOUD_PLUGIN_URI' ) ) {
			define( 'ET_CLOUD_PLUGIN_URI', get_template_directory_uri() . '/cloud' );
		}

		if ( !defined( 'ET_CLOUD_PLUGIN_DIR' ) ) {
			define( 'ET_CLOUD_PLUGIN_DIR', get_template_directory() . '/cloud' );
		}

		$CORE_VERSION = defined( 'ET_CORE_VERSION' ) ? ET_CORE_VERSION : '';
		$ET_DEBUG     = defined( 'ET_DEBUG' ) && ET_DEBUG;
		$DEBUG        = $ET_DEBUG;

		$home_url       = wp_parse_url( get_site_url() );
		$build_dir_uri  = ET_CLOUD_PLUGIN_URI . '/build';
		$common_scripts = ET_COMMON_URL . '/scripts';
		$cache_buster   = $DEBUG ? mt_rand() / mt_getrandmax() : $CORE_VERSION;
		$asset_path     = ET_CLOUD_PLUGIN_DIR . '/build/et-cloud-app.bundle.js';

		if ( file_exists( $asset_path ) ) {
			wp_enqueue_style( 'et-cloud-styles', "{$build_dir_uri}/et-cloud-app.bundle.modals.css", [], (string) $cache_buster );
		}

		wp_enqueue_script( 'es6-promise', "{$common_scripts}/es6-promise.auto.min.js", [], '4.2.2', true );

		$BUNDLE_DEPS = [
			'jquery',
			'react',
			'react-dom',
			'es6-promise',
		];

		if ( $DEBUG || $enqueue_prod_scripts || file_exists( $asset_path ) ) {
			$BUNDLE_URI = ! file_exists( $asset_path ) ? "{$home_url['scheme']}://{$home_url['host']}:31495/et-cloud-app.bundle.js" : "{$build_dir_uri}/et-cloud-app.bundle.js";

			// Skip the React loading if we already have React ( Gutenberg editor for example ) to avoid conflicts.
			if ( ! $skip_react_loading ) {
				if ( function_exists( 'et_fb_enqueue_react' ) ) {
					et_fb_enqueue_react();
				}
			}

			wp_enqueue_script( 'et-cloud-app', $BUNDLE_URI, $BUNDLE_DEPS, (string) $cache_buster, true );
			wp_localize_script( 'et-cloud-app', 'et_cloud_data', ET_Cloud_App::get_cloud_helpers());
		}
	}
}

ET_Cloud_App::instance();

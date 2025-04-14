<?php

if ( ! class_exists( 'ET_Core_API_ElegantThemes' ) ):
/**
 * Handles communication with the main ET API.
 *
 * @since 3.10
 *
 * @private
 *
 * @package ET\Core\API
 */
class ET_Core_API_ElegantThemes {
	/**
	 * Base API URL.
	 *
	 * @var string
	 */
	protected $api_url = 'https://www.elegantthemes.com/api/';

	/**
	 * API username.
	 *
	 * @var string
	 */
	protected $username = '';

	/**
	 * API key.
	 *
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * ET_Core_API_Client constructor.
	 *
	 * @since 3.10
	 *
	 * @param string $username
	 * @param string $api_key
	 * @param string $api
	 */
	public function __construct( $username, $api_key ) {
		$this->username = sanitize_text_field( $username );
		$this->api_key = sanitize_text_field( $api_key );
	}

	/**
	 * Decorate a payload array with common data.
	 *
	 * @since 3.10
	 *
	 * @param array $payload
	 *
	 * @return array
	 */
	protected function _get_decorated_payload( $payload ) {
		if ( ! isset( $payload['username'] ) ) {
			$payload['username'] = $this->username;
		}

		if ( ! isset( $payload['api_key'] ) ) {
			$payload['api_key'] = $this->api_key;
		}

		return $payload;
	}

	/**
	 * Decorate request options array with common options.
	 *
	 * @since 3.10
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	protected function _get_decorated_request_options( $options = array() ) {
		global $wp_version;

		$options = array_merge( array(
			'timeout'    => 10,
			'user-agent' => 'WordPress/' . $wp_version . '; Elegant Themes/' . ET_CORE_VERSION . '; ' . home_url( '/' ),
		), $options );

		return $options;
	}

	/**
	 * Parse a response from the API.
	 *
	 * @since 3.10
	 *
	 * @param array|WP_Error $response
	 *
	 * @return object|WP_Error
	 */
	protected function _parse_response( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_body = trim( wp_remote_retrieve_body( $response ) );

		if ( '' === $response_body ) {
			return new WP_Error( 'et_unknown', esc_html__( 'An unknown error has occurred. Please try again later.', 'et-core' ) );
		}

		$credentials_errors = array(
			'Username is not set.',
			'Subscription is not active',
		);

		if ( in_array( $response_body, $credentials_errors )  ) {
			return new WP_Error( 'et_invalid_api_credentials', esc_html__( 'Invalid Username and/or API Key provided.', 'et-core' ) );
		}

		return maybe_unserialize( $response_body );
	}

	/**
	 * Get the full API endpoint.
	 *
	 * @since 3.10
	 *
	 * @param string $endpoint "api" or "api_downloads"
	 *
	 * @return string
	 */
	protected function _get_endpoint( $endpoint = 'api' ) {
		$allowed_endpoints = array( 'api', 'api_downloads' );
		$suffix = '.php';

		if ( ! in_array( $endpoint, $allowed_endpoints ) ) {
			$endpoint = $allowed_endpoints[0];
		}

		return esc_url_raw( $this->api_url . $endpoint . $suffix );
	}

	/**
	 * Submit a GET request to the API.
	 *
	 * @since 3.10
	 *
	 * @param array $payload
	 * @param string $endpoint
	 *
	 * @return object|WP_Error
	 */
	protected function _get( $payload, $endpoint = 'api' ) {
		$payload = $this->_get_decorated_payload( $payload );
		$options = $this->_get_decorated_request_options();
		$url = esc_url_raw( add_query_arg( $payload, $this->_get_endpoint( $endpoint ) ) );

		$response = wp_remote_get( $url, $options );

		return $this->_parse_response( $response );
	}

	/**
	 * Submit a POST request to the API.
	 *
	 * @since 3.10
	 *
	 * @param array $payload
	 * @param string $endpoint
	 *
	 * @return object|WP_Error
	 */
	protected function _post( $payload, $endpoint = 'api' ) {
		$payload = $this->_get_decorated_payload( $payload );
		$options = $this->_get_decorated_request_options( array(
			'body' => $payload,
		) );

		$response = wp_remote_post( $this->_get_endpoint( $endpoint ), $options );

		return $this->_parse_response( $response );
	}

	/**
	 * Check if a product is available.
	 *
	 * @since 3.10
	 *
	 * @param string $product_name
	 * @param string $version
	 *
	 * @return bool|WP_Error
	 */
	public function is_product_available( $product_name, $version ) {
		$response = $this->_get( array(
			'api_update' => 1,
			'action'     => 'check_version_status',
			'product'    => $product_name,
			'version'    => $version,
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! is_array( $response ) ) {
			return new WP_Error( 'et_unknown', esc_html__( 'An unexpected response was received from the version server. Please try again later.', 'et-core' ) );
		}

		switch ( $response['status'] ) {
			case 'not_available':
				return new WP_Error( 'et_version_rollback_not_available', sprintf(
					esc_html__( 'The previously used version of %1$s does not support version rollback.', 'et-core' ),
					esc_html( $product_name )
				) );

			case 'blocklisted':
				return new WP_Error( 'et_version_rollback_blocklisted', et_get_safe_localization( sprintf(
					__( 'For privacy and security reasons, you cannot rollback to <strong>Version %1$s</strong>.', 'et-core' ),
					esc_html( $version )
				) ) );

			case 'available':
				return true;
		}

		return new WP_Error( 'et_unknown', esc_html__( 'An unknown error has occurred. Please try again later.', 'et-core' ) );
	}

	/**
	 * Get a product download url for a specific version, if available.
	 *
	 * @since 3.10
	 *
	 * @param string $product_name
	 * @param string $version
	 *
	 * @return string|WP_Error
	 */
	public function get_download_url( $product_name, $version ) {
		$payload = $this->_get_decorated_payload( array(
			'api_update' => 1,
			'theme'      => $product_name,
			'version'    => $version,
		) );
		return esc_url_raw( add_query_arg( $payload, $this->_get_endpoint( 'api_downloads' ) ) );
	}
}
endif;

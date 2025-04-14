<?php

/**
 * Wrapper for Aweber's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Aweber extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $ACCESS_TOKEN_URL = 'https://auth.aweber.com/1.0/oauth/access_token';

	/**
	 * @inheritDoc
	 */
	public $AUTHORIZATION_URL = 'https://auth.aweber.com/1.0/oauth/authorize';

	/**
	 * @inheritDoc
	 */
	public $REQUEST_TOKEN_URL = 'https://auth.aweber.com/1.0/oauth/request_token';

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.aweber.com/1.0';

	/**
	 * @var string
	 */
	public $accounts_url;

	/**
	 * @inheritDoc
	 */
	public $name = 'Aweber';

	/**
	 * @inheritDoc
	 */
	public $name_field_only = true;

	/**
	 * @inheritDoc
	 */
	public $slug = 'aweber';

	/**
	 * @inheritDoc
	 */
	public $oauth_version = '1.0a';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = true;

	/**
	 * ET_Core_API_Email_Aweber constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner, $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );
		$this->accounts_url = "{$this->BASE_URL}/accounts";
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		$this->prepare_request( $list['custom_fields_collection_link'] );

		return parent::_fetch_custom_fields( $list_id, $list );
	}

	protected function _get_lists_collection_url() {
		$this->prepare_request( $this->accounts_url );
		$this->make_remote_request();
		$url = '';

		if ( ! $this->response->ERROR && ! empty( $this->response->DATA['entries'][0]['lists_collection_link'] ) ) {
			$url = $this->response->DATA['entries'][0]['lists_collection_link'];
		}

		return $url;
	}

	protected static function _parse_ID( $ID ) {
		$values = explode( '|', $ID );

		return ( count( $values ) === 6 ) ? $values : null;
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$fields = $args['custom_fields'];

		unset( $args['custom_fields'] );

		foreach ( $fields as $field_id => $value ) {
			if ( is_array( $value ) && $value ) {
				// This is a multiple choice field (eg. checkbox, radio, select)
				$value = array_values( $value );

				if ( count( $value ) > 1 ) {
					$value = implode( ',', $value );
				} else {
					$value = array_pop( $value );
				}
			}

			$list_id    = $args['list_id'];
			$field_name = self::$_->array_get( $this->data, "lists.{$list_id}.custom_fields.{$field_id}.name" );

			self::$_->array_set( $args, "custom_fields.{$field_name}", $value );
		}

		return $args;
	}

	/**
	 * Uses the app's authorization code to get an access token
	 */
	public function authenticate() {
		if ( empty( $this->data['api_key'] ) ) {
			return false;
		}

		$key_parts = self::_parse_ID( $this->data['api_key'] );

		if ( null === $key_parts ) {
			return false;
		}

		list( $consumer_key, $consumer_secret, $request_token, $request_secret, $verifier ) = $key_parts;

		if ( ! $verifier ) {
			return false;
		}

		$this->data['consumer_key']    = $consumer_key;
		$this->data['consumer_secret'] = $consumer_secret;
		$this->data['access_key']      = $request_token;
		$this->data['access_secret']   = $request_secret;
		$this->oauth_verifier          = $verifier;

		// AWeber returns oauth access key in url query format :face_with_rolling_eyes:
		$this->http->expects_json = false;

		return parent::authenticate();
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'Authorization Code', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_error_message() {
		$error_message = parent::get_error_message();

		return preg_replace('/https.*/m', '', $error_message );
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'         => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'total_subscribers',
			),
			'subscriber'   => array(
				'name'          => 'name',
				'email'         => 'email',
				'ad_tracking'   => 'ad_tracking',
				'custom_fields' => 'custom_fields',
			),
			'error'        => array(
				'error_message' => 'error.message',
			),
			'custom_field' => array(
				'field_id' => 'id',
				'name'     => 'name',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		$needs_to_authenticate = ! $this->is_authenticated() || ! $this->_initialize_oauth_helper();

		if ( $needs_to_authenticate && ! $this->authenticate() ) {
			if ( empty( $this->response->DATA ) ) {
				return '';
			}

			$this->response->DATA = json_decode( $this->response->DATA, true );
			return $this->get_error_message();
		}

		$this->http->expects_json = true;
		$this->LISTS_URL          = $this->_get_lists_collection_url();

		if ( empty( $this->LISTS_URL ) ) {
			return '';
		}

		$this->response_data_key = 'entries';

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$lists_url  = $this->_get_lists_collection_url();
		$url        = "{$lists_url}/{$args['list_id']}/subscribers";
		$ip_address = 'true' === self::$_->array_get( $args, 'ip_address', 'true' );

		$params = $this->_process_custom_fields( $args );
		$params = $this->transform_data_to_provider_format( $params, 'subscriber' );
		$params = array_merge( $params, array(
			'ws.op'      => 'create',
			'ip_address' => $ip_address ? et_core_get_ip_address() : '0.0.0.0',
			'misc_notes' => $this->SUBSCRIBED_VIA,
		) );

		// There is a bug in AWeber some characters not encoded properly on AWeber side when sending data in x-www-form-urlencoded format so use json instead
		$this->prepare_request( $url, 'POST', false, $params, true );
		$this->request->HEADERS['Content-Type'] = 'application/json';

		return parent::subscribe( $params, $url );
	}
}

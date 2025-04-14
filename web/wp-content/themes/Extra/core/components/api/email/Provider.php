<?php

/**
 * High-level wrapper for interacting with the external API's offered by 3rd-party mailing list providers.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API
 */
abstract class ET_Core_API_Email_Provider extends ET_Core_API_Service  {

	/**
	 * The URL from which custom fields for a list on this account can be retrieved.
	 *
	 * @var string
	 */
	public $FIELDS_URL;

	/**
	 * The URL from which groups/tags for a list on this account can be retrieved.
	 *
	 * @var string
	 */
	public $GROUPS_URL;

	/**
	 * The number of records to return from API (per request).
	 *
	 * @var int
	 */
	public $COUNT;

	/**
	 * The URL from which subscriber lists for this account can be retrieved.
	 *
	 * @var string
	 */
	public $LISTS_URL;

	/**
	 * The URL to which new subscribers can be posted.
	 *
	 * @var string
	 */
	public $SUBSCRIBE_URL;

	/**
	 * The URL from which subscribers for this account can be retrieved.
	 *
	 * @var string
	 */
	public $SUBSCRIBERS_URL;

	/**
	 * "Subscribed via..." translated string.
	 *
	 * @var string
	 */
	public $SUBSCRIBED_VIA;

	/**
	 * Type of support for custom fields offered by provider.
	 *
	 * @since 3.17.2
	 *
	 * @var bool|string Accepts `dynamic`, `predefined`, `false`. Default `predefined`.
	 */
	public $custom_fields = 'predefined';

	/**
	 * Type of support for custom fields offered by provider.
	 *
	 * @since 3.17.2
	 *
	 * @var string Accepts `list`, `account`.
	 */
	public $custom_fields_scope = 'list';

	/**
	 * Whether or not only a single name field is supported instead of first/last name fields.
	 *
	 * @var string
	 */
	public $name_field_only = false;

	/**
	 * ET_Core_API_Email_Provider constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		$this->service_type = 'email';

		parent::__construct( $owner, $account_name, $api_key );

		if ( 'builder' === $this->owner ) {
			$owner = 'Divi Builder';
		} else {
			$owner = ucfirst( $this->owner );
		}

		$this->SUBSCRIBED_VIA = sprintf( '%1$s %2$s.', esc_html__( 'Subscribed via', 'et_core' ), $owner );

		/**
		 * Filters the max number of results returned from email API provider per request.
		 *
		 * @since 3.17.2
		 *
		 * @param int $max_results_count
		 */
		$this->COUNT = apply_filters( 'et_core_api_email_max_results_count', 250 );
	}

	/**
	 * Get custom fields for a subscriber list.
	 *
	 * @param int|string $list_id
	 * @param array      $list
	 *
	 * @return array
	 */
	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		if ( 'dynamic' === $this->custom_fields ) {
			return array();
		}

		if ( null === $this->request || $this->request->COMPLETE ) {
			$this->prepare_request( $this->FIELDS_URL );
		}

		$this->make_remote_request();

		$result = array();

		if ( false !== $this->response_data_key && empty( $this->response_data_key ) ) {
			// Let child class handle parsing the response data themselves.
			return $result;
		}

		if ( $this->response->ERROR ) {
			et_debug( $this->get_error_message() );
			return $result;
		}

		if ( false === $this->response_data_key ) {
			// The data returned by the service is not nested.
			$data = $this->response->DATA;
		} else {
			// The data returned by the service is nested under a single key.
			$data = $this->response->DATA[ $this->response_data_key ];
		}

		foreach ( $data as &$custom_field ) {
			$custom_field = $this->transform_data_to_our_format( $custom_field, 'custom_field' );
		}

		$fields      = array();
		$field_types = self::$_->array_get( $this->data_keys, 'custom_field_type' );

		foreach ( $data as $field ) {
			$field_id = $field['field_id'];
			$type     = self::$_->array_get( $field, 'type', 'any' );

			if ( $field_types && ! isset( $field_types[ $type ] ) ) {
				// Unsupported field type. Make it 'text' instead.
				$type = 'text';
			}

			if ( isset( $field['hidden'] ) && is_string( $field['hidden'] ) ) {
				$field['hidden'] = 'false' === $field['hidden'] ? false : true;
			}

			$field['type'] = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'any' );

			$fields[ $field_id ] = $field;
		}

		return $fields;
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_data() {
		$options = parent::_get_data();

		// return empty array in case of empty name
		if ( '' === $this->account_name || ! is_string( $this->account_name ) ) {
			return array();
		}

		$provider = sanitize_text_field( $this->slug );
		$account  = sanitize_text_field( $this->account_name );

		if ( ! isset( $options['accounts'][ $provider ][ $account ] ) ) {
			$options['accounts'][ $provider ][ $account ] = array();
			update_option( "et_core_api_email_options", $options );
		}

		return $options['accounts'][ $provider ][ $account ];
	}

	protected function _process_custom_fields( $args ) {
		return $args;
	}

	/**
	 * Processes subscriber lists data from the provider's API and returns only the data we're interested in.
	 *
	 * @since 1.1.0
	 *
	 * @param array $lists Subscriber lists data to process.
	 *
	 * @return array
	 */
	protected function _process_subscriber_lists( $lists ) {
		$id_key = $this->data_keys['list']['list_id'];
		$result = array();

		foreach ( (array) $lists as $list ) {
			if ( ! is_array( $list ) ) {
				$list = (array) $list;
			}

			if ( ! isset( $list[ $id_key ] ) ) {
				continue;
			}

			$id            = $list[ $id_key ];
			$result[ $id ] = $this->transform_data_to_our_format( $list, 'list' );

			if ( ! array_key_exists( 'subscribers_count', $result[ $id ] ) ) {
				$result[ $id ]['subscribers_count'] = 0;
			}

			$get_custom_fields = $this->custom_fields && 'list' === $this->custom_fields_scope;

			if ( $get_custom_fields && $custom_fields = $this->_fetch_custom_fields( $id, $list ) ) {
				$result[ $id ]['custom_fields'] = $custom_fields;
			}
		}

		return $result;
	}

	/**
	 * Returns whether or not an account exists in the database.
	 *
	 * @param string $provider
	 * @param string $account_name
	 *
	 * @return bool
	 */
	public static function account_exists( $provider, $account_name ) {
		$all_accounts = self::get_accounts();

		return isset( $all_accounts[ $provider ][ $account_name ] );
	}

	/**
	 * @inheritDoc
	 */
	public function delete() {
		self::remove_account( $this->slug, $this->account_name );

		$this->account_name = '';

		$this->_get_data();
	}

	/**
	 * Retrieves the email accounts data from the database.
	 *
	 * @return array
	 */
	public static function get_accounts() {
		$options = (array) get_option( 'et_core_api_email_options' );

		return isset( $options['accounts'] ) ? $options['accounts'] : array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		return $keymap;
	}

	/**
	 * Retrieves the subscriber lists for the account assigned to the current instance.
	 *
	 * @return string 'success' if successful, an error message otherwise.
	 */
	public function fetch_subscriber_lists() {
		if ( null === $this->request || $this->request->COMPLETE ) {
			$this->prepare_request( $this->LISTS_URL );
		}

		$this->make_remote_request();
		$result = 'success';

		if ( false !== $this->response_data_key && empty( $this->response_data_key ) ) {
			// Let child class handle parsing the response data themselves.
			return '';
		}

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		if ( false === $this->response_data_key ) {
			// The data returned by the service is not nested.
			$data = $this->response->DATA;
		} else {
			// The data returned by the service is nested under a single key.
			$data = $this->response->DATA[ $this->response_data_key ];
		}

		if ( ! empty( $data ) ) {
			$this->data['lists']         = $this->_process_subscriber_lists( $data );
			$this->data['is_authorized'] = true;

			$list = is_array( $data ) ? array_shift( $data ) : array();

			if ( $this->custom_fields && 'account' === $this->custom_fields_scope ) {
				$this->data['custom_fields'] = $this->_fetch_custom_fields( '', $list );
			}

			$this->save_data();
		}

		return $result;
	}

	/**
	 * Remove an account
	 *
	 * @param $provider
	 * @param $account_name
	 */
	public static function remove_account( $provider, $account_name ) {
		$options = (array) get_option( 'et_core_api_email_options' );

		unset( $options['accounts'][ $provider ][ $account_name ] );

		update_option( 'et_core_api_email_options', $options );
	}

	/**
	 * @inheritDoc
	 */
	public function save_data() {
		self::update_account( $this->slug, $this->account_name, $this->data );
	}

	/**
	 * @inheritDoc
	 */
	public function set_account_name( $name ) {
		$this->account_name = $name;
		$this->data = $this->_get_data();
	}

	/**
	 * Makes an HTTP POST request to add a subscriber to a list.
	 *
	 * @param string[] $args Data for the POST request.
	 * @param string   $url  The URL for the POST request. Optional when called on child classes.
	 *
	 * @return string 'success' if successful, an error message otherwise.
	 */
	public function subscribe( $args, $url = '' ) {
		if ( null === $this->request || $this->request->COMPLETE ) {
			if ( ! in_array( 'ip_address', $args ) || 'true' === $args['ip_address'] ) {
				$args['ip_address'] = et_core_get_ip_address();
			} else if ( 'false' === $args['ip_address'] ) {
				$args['ip_address'] = '0.0.0.0';
			}

			$args = $this->transform_data_to_provider_format( $args, 'subscriber' );

			if ( $this->custom_fields ) {
				$args = $this->_process_custom_fields( $args );
			}

			$this->prepare_request( $url, 'POST', false, $args );
		} else if ( $this->request->JSON_BODY && ! is_string( $this->request->BODY ) && ! $this->uses_oauth ) {
			$this->request->BODY = json_encode( $this->request->BODY );
		} else if ( is_array( $this->request->BODY ) ) {
			$this->request->BODY = array_merge( $this->request->BODY, $args );
		} else if ( ! $this->request->JSON_BODY ) {
			$this->request->BODY = $args;
		}

		$this->make_remote_request();

		return $this->response->ERROR ? $this->get_error_message() : 'success';
	}

	/**
	 * Updates the data for a provider account.
	 *
	 * @param string $provider The provider's slug.
	 * @param string $account  The account name.
	 * @param array  $data     The new data for the account.
	 */
	public static function update_account( $provider, $account, $data ) {
		$options       = (array) get_option( 'et_core_api_email_options' );
		$existing_data = array();

		if ( empty( $account ) || empty( $provider ) ) {
			return;
		}

		$provider = sanitize_text_field( $provider );
		$account  = sanitize_text_field( $account );

		if ( isset( $options['accounts'][ $provider ][ $account ] ) ) {
			$existing_data = $options['accounts'][ $provider ][ $account ];
		}

		$options['accounts'][ $provider ][ $account ] = array_merge( $existing_data, $data );

		update_option( 'et_core_api_email_options', $options );
	}
}

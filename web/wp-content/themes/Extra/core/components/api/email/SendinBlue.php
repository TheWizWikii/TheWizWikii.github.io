<?php

/**
 * Wrapper for SendinBlue's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_SendinBlue extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.sendinblue.com/v3'; // @phpcs:ignore ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- Keep the variable name.

	/**
	 * @inheritDoc
	 */
	public $FIELDS_URL = 'https://api.sendinblue.com/v3/contacts/attributes'; // @phpcs:ignore ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- Keep the variable name.

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.sendinblue.com/v3/contacts/lists/'; // @phpcs:ignore ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- Keep the variable name.

	/**
	 * The URL to which new subscribers can be posted.
	 *
	 * @var string
	 */
	public $SUBSCRIBE_URL = 'https://api.sendinblue.com/v3/contacts'; // @phpcs:ignore ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- Keep the variable name.

	/**
	 * The URL to get the subscriber information.
	 * Only used by legacy mode (v2) to check whether we should create or update subscription.
	 *
	 * @var string
	 */
	public $USERS_URL = 'https://api.sendinblue.com/v2.0/user';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'SendinBlue';

	/**
	 * @inheritDoc
	 */
	public $slug = 'sendinblue';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}

	protected function _maybe_set_custom_headers() {
		if ( empty( $this->custom_headers ) && isset( $this->data['api_key'] ) ) {
			$this->custom_headers = array( 'api-key' => $this->data['api_key'] );
		}
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$fields      = $args['custom_fields'];
		$fileds_info = $args['fileds_info'];

		unset( $args['custom_fields'] );

		foreach ( $fields as $field_id => $value ) {
			if ( ! isset( $fileds_info[ $field_id ] ) ) {
				continue;
			}

			if ( is_array( $value ) && $value ) {
				// This is a multiple choice field (eg. checkbox, radio, select)
				$value = array_values( $value );

				if ( count( $value ) > 1 ) {
					$value = implode( ',', $value );
				} else {
					$type = self::$_->array_get( $fileds_info, "{$field_id}.native_type" );

					// User checked the checkbox, when native type is Boolean.
					$value = 'boolean' === $type ? true : array_pop( $value );
				}
			}

			self::$_->array_set( $args, "attributes.{$field_id}", $value );
		}

		return $args;
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'         => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => $this->_should_use_legacy_api() ? 'total_subscribers' : 'totalSubscribers',
			),
			'subscriber'   => array(
				'email'         => 'email',
				'name'          => 'attributes.FIRSTNAME',
				'last_name'     => 'attributes.LASTNAME',
				'list_id'       => $this->_should_use_legacy_api() ? '@listid' : '@listIds',
				'custom_fields' => 'custom_fields',
				'updateEnabled' => 'updateEnabled',
			),
			'custom_field' => array(
				'native_type' => 'type',
				'field_id'    => 'name',
				'name'        => 'name',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	public function get_subscriber( $email ) {
		$this->prepare_request( "{$this->USERS_URL}/{$email}", 'GET' );
		$this->make_remote_request();

		if ( $this->response->ERROR || ! isset( $this->response->DATA['listid'] ) ) {
			return false;
		}

		if ( isset( $this->response->DATA['code'] ) && 'success' !== $this->response->DATA['code'] ) {
			return false;
		}

		return $this->response->DATA;
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		if ( empty( $this->custom_headers ) ) {
			$this->_maybe_set_custom_headers();
		}

		$use_legacy_api = $this->_should_use_legacy_api();
		if ( $use_legacy_api ) {
			$this->LISTS_URL         = 'https://api.sendinblue.com/v2.0/list'; // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Keep the variable name.
			$this->response_data_key = 'data';
			$params                  = array(
				'page'       => 1,
				'page_limit' => 2,
			);
		} else {
			$this->response_data_key = 'lists';
			$params                  = array();
		}

		/**
		 * The maximum number of subscriber lists to request from Sendinblue's API.
		 *
		 * @since 4.11.4
		 *
		 * @param int $max_lists
		 */
		$max_lists = (int) apply_filters( 'et_core_api_email_sendinblue_max_lists', 50 );
		$url       = "{$this->LISTS_URL}?limit={$max_lists}&offset=0&sort=desc";

		$this->prepare_request( $url, 'GET', false, $params );

		$this->request->data_format = 'body';

		parent::fetch_subscriber_lists();

		if ( $this->response->ERROR ) {
			return $this->response->ERROR_MESSAGE;
		}

		if ( isset( $this->response->DATA['code'] ) && 'success' !== $this->response->DATA['code'] ) {
			return $this->response->DATA['message'];
		}

		$result                      = 'success';
		$this->data['is_authorized'] = 'true';
		$list_data                   = $use_legacy_api ? $this->response->DATA['data']['lists'] : ( isset( $this->response->DATA['lists'] ) ? $this->response->DATA['lists'] : [] );

		if ( ! empty( $list_data ) ) {
			$this->data['lists'] = $this->_process_subscriber_lists( $list_data );
			$this->save_data();
		}

		return $result;
	}

	/**
	 * Get custom fields for a subscriber list.
	 * Need to override the method in the child class to dynamically use the API endpoint
	 * and response_data_key based on the API version being used.
	 *
	 * @param int|string $list_id The list ID.
	 * @param array      $list    The lists array.
	 *
	 * @return array
	 */
	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		if ( $this->_should_use_legacy_api() ) {
			$this->FIELDS_URL        = 'https://api.sendinblue.com/v2.0/attribute/normal'; // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Keep the variable name.
			$this->response_data_key = 'data';
		} else {
			$this->response_data_key = 'attributes';
		}

		return parent::_fetch_custom_fields( $list_id, $list );
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$args['list_id'] = array( absint( $args['list_id'] ) ); // in V3 the list id has to be integer.
		if ( $this->_should_use_legacy_api() ) {
			$this->SUBSCRIBE_URL = 'https://api.sendinblue.com/v2.0/user/createdituser'; // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Keep the variable name.
			$existing_user       = $this->get_subscriber( $args['email'] );
			if ( false !== $existing_user ) {
				$args['list_id'] = array_unique( array_merge( $args['list_id'], $existing_user['listid'] ) );
			}
		} else {
			$args['updateEnabled'] = true; // Update existing contact if exists.
			// Process data and encode to json, the new API (v3) uses json encoded body params.
			if ( ! in_array( 'ip_address', $args, true ) || 'true' === $args['ip_address'] ) {
				$args['ip_address'] = et_core_get_ip_address();
			} elseif ( 'false' === $args['ip_address'] ) {
				$args['ip_address'] = '0.0.0.0';
			}

			$args = $this->transform_data_to_provider_format( $args, 'subscriber' );
			if ( $this->custom_fields ) {
				$args['fileds_info'] = $this->_fetch_custom_fields();

				$args = $this->_process_custom_fields( $args );
			}
			$this->prepare_request( $this->SUBSCRIBE_URL, 'POST', false, $args, true ); // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Keep the variable name.
		}

		return parent::subscribe( $args, $this->SUBSCRIBE_URL );
	}

	/**
	 * Check if the api-key being used is legacy (v2).
	 *
	 * @return boolean
	 */
	protected function _should_use_legacy_api() {
		$api_key = isset( $this->data['api_key'] ) ? $this->data['api_key'] : '';
		return ! empty( $api_key ) && 'xkeysib-' !== substr( $api_key, 0, 8 );
	}
}

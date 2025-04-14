<?php

/**
 * Wrapper for Campaign Monitor's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_CampaignMonitor extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.createsend.com/api/v3.1';

	/**
	 * @inheritDoc
	 */
	public $http_auth = array(
		'username' => 'api_key',
		'password' => '-',
	);

	/**
	 * @inheritDoc
	 */
	public $name = 'CampaignMonitor';

	/**
	 * @inheritDoc
	 */
	public $name_field_only = true;

	/**
	 * @inheritDoc
	 */
	public $slug = 'campaign_monitor';

	/**
	 * @inheritDoc
	 * @internal If true, oauth endpoints properties must also be defined.
	 */
	public $uses_oauth = false;

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->http_auth['password'] = $owner;
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		$this->prepare_request( "{$this->BASE_URL}/lists/{$list_id}/customfields.json" );
		$this->make_remote_request();

		$result = array();

		if ( $this->response->ERROR ) {
			et_debug( $this->get_error_message() );

			return $result;
		}

		foreach ( $this->response->DATA as &$custom_field ) {
			$custom_field = $this->transform_data_to_our_format( $custom_field, 'custom_field' );
			$custom_field['field_id'] = ltrim( rtrim( $custom_field['field_id'], ']' ), '[' );
		}

		$fields = array();

		foreach ( $this->response->DATA as $field ) {
			$field_id = $field['field_id'];
			$type     = self::$_->array_get( $field, 'type', 'any' );

			$field['type'] = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'any' );

			$fields[ $field_id ] = $field;
		}

		return $fields;
	}

	protected function _get_clients() {
		$url = "{$this->BASE_URL}/clients.json";

		$this->prepare_request( $url );
		$this->make_remote_request();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		return (array) $this->response->DATA;
	}

	protected function _get_subscriber_counts() {
		$subscriber_lists = $this->_process_subscriber_lists( $this->response->DATA );
		$with_counts      = array();

		foreach ( $subscriber_lists as $subscriber_list ) {
			$list_id                 = $subscriber_list['list_id'];
			$with_counts[ $list_id ] = $subscriber_list;
			$url                     = "{$this->BASE_URL}/lists/{$list_id}/stats.json";

			$this->prepare_request( $url );
			$this->make_remote_request();

			if ( $this->response->ERROR  ) {
				continue;
			}

			if ( isset( $this->response->DATA['TotalActiveSubscribers'] ) ) {
				$with_counts[ $list_id ]['subscribers_count'] = $this->response->DATA['TotalActiveSubscribers'];
			} else {
				$with_counts[ $list_id ]['subscribers_count'] = 0;
			}

			usleep( 500000 ); // 0.5 seconds
		}

		return $with_counts;
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$fields_unprocessed = $args['custom_fields'];

		unset( $args['custom_fields'] );

		$fields = array();

		foreach ( $fields_unprocessed as $field_id => $value ) {
			if ( is_array( $value ) && $value ) {
				// This is a multiple choice field (eg. checkbox)
				foreach ( $value as $selected_option ) {
					$fields[] = array(
						'Key'   => $field_id,
						'Value' => $selected_option,
					);
				}
			} else {
				$fields[] = array(
					'Key'   => $field_id,
					'Value' => $value,
				);
			}
		}

		$args['CustomFields'] = $fields;

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
			'list'              => array(
				'list_id'           => 'ListID',
				'name'              => 'Name',
				'subscribers_count' => 'TotalActiveSubscribers',
			),
			'subscriber'        => array(
				'name'          => 'Name',
				'email'         => 'EmailAddress',
				'custom_fields' => 'custom_fields',
			),
			'error'             => array(
				'error_message' => 'Message',
			),
			'custom_field'      => array(
				'field_id' => 'Key',
				'name'     => 'FieldName',
				'type'     => 'DataType',
				'options'  => 'FieldOptions',
			),
			'custom_field_type' => array(
				// Us => Them
				'input'           => 'Text',
				'select'          => 'MultiSelectOne',
				'checkbox'        => 'MultiSelectMany',
				// Them => Us
				'Text'            => 'input',
				'Number'          => 'input',
				'Date'            => 'input',
				'MultiSelectOne'  => 'select',
				'MultiSelectMany' => 'checkbox',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$clients = $this->_get_clients();
		$lists   = array();

		if ( ! is_array( $clients ) ) {
			// Request failed with an error, return the error message.
			return $clients;
		}

		foreach ( $clients as $client_info ) {
			if ( empty( $client_info['ClientID'] ) ) {
				continue;
			}

			$url = "{$this->BASE_URL}/clients/{$client_info['ClientID']}/lists.json";

			$this->prepare_request( $url );

			parent::fetch_subscriber_lists();

			if ( $this->response->ERROR ) {
				return $this->get_error_message();
			}

			if ( isset( $this->response->DATA ) ) {
				$with_counts                 = $this->_get_subscriber_counts();
				$lists                       = $lists + $with_counts;
				$this->data['is_authorized'] = true;

				$this->save_data();
			}
		}

		if ( empty( $this->data['lists'] ) || ! empty( $lists ) ) {
			$this->data['lists'] = $lists;
			$this->save_data();
		}

		return $this->is_authenticated() ? 'success' : $this->FAILURE_MESSAGE;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$url    = "{$this->BASE_URL}/subscribers/{$args['list_id']}.json";
		$params = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$params = $this->_process_custom_fields( $params );

		$params['CustomFields'][] = array( 'Key' => 'Note', 'Value' => $this->SUBSCRIBED_VIA );

		$this->prepare_request( $url, 'POST', false, $params, true );

		return parent::subscribe( $params, $url );
	}
}

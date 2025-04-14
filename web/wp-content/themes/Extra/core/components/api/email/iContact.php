<?php

/**
 * Wrapper for iContact's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_iContact extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://app.icontact.com/icp/a';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'iContact';

	/**
	 * @inheritDoc
	 */
	public $slug = 'icontact';

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		$this->prepare_request( $this->FIELDS_URL );
		$this->make_remote_request();

		$result = array();

		if ( $this->response->ERROR ) {
			et_debug( $this->get_error_message() );

			return $result;
		}

		$data = $this->response->DATA['customfields'];

		foreach ( $data as &$custom_field ) {
			$custom_field = $this->transform_data_to_our_format( $custom_field, 'custom_field' );
		}

		$fields = array();

		foreach ( $data as $field ) {
			if ( 'text' !== $field['type'] ) {
				continue;
			}

			$field_id = $field['field_id'];
			$type     = self::$_->array_get( $field, 'type', 'any' );

			$field['type'] = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'any' );

			$fields[ $field_id ] = $field;
		}

		return $fields;
	}

	protected function _set_custom_headers() {
		if ( ! empty( $this->custom_headers ) ) {
			return;
		}

		$this->custom_headers = array(
			'Accept'       => 'application/json',
			'API-Version'  => '2.2',
			'API-AppId'    => sanitize_text_field( $this->data['client_id'] ),
			'API-Username' => sanitize_text_field( $this->data['username'] ),
			'API-Password' => sanitize_text_field( $this->data['password'] ),
		);
	}

	protected function _get_account_id() {
		if ( ! empty( $this->data['account_id'] ) ) {
			return $this->data['account_id'];
		}

		$this->prepare_request( 'https://app.icontact.com/icp/a' );
		$this->make_remote_request();

		if ( isset( $this->response->DATA['accounts'][0]['accountId'] ) ) {
			$this->data['account_id'] = $this->response->DATA['accounts'][0]['accountId'];
		}

		return $this->data['account_id'];
	}

	protected function _get_subscriber( $args, $exclude = null ) {
		$default_excludes = array( 'name', 'last_name' );

		if ( is_array( $exclude ) ) {
			$exclude = array_merge( $default_excludes, $exclude );
		} else if ( $exclude ) {
			$default_excludes[] = $exclude;
			$exclude            = $default_excludes;
		}

		$args = $this->transform_data_to_provider_format( $args, 'subscriber', $exclude );
		$url  = add_query_arg( $args, $this->SUBSCRIBERS_URL );

		$this->prepare_request( $url );
		$this->make_remote_request();

		if ( $this->response->ERROR || ! $this->response->DATA['contacts'] ) {
			return false;
		}

		return $this->response->DATA['contacts'][0];
	}

	protected function _get_folder_id() {
		if ( ! empty( $this->data['folder_id'] ) ) {
			return $this->data['folder_id'];
		}

		$this->prepare_request( "{$this->BASE_URL}/{$this->data['account_id']}/c" );
		$this->make_remote_request();

		if ( isset( $this->response->DATA['clientfolders'][0]['clientFolderId'] ) ) {
			$this->data['folder_id'] = $this->response->DATA['clientfolders'][0]['clientFolderId'];
		}

		return $this->data['folder_id'];
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

			self::$_->array_set( $args, $field_id, $value );
		}

		return $args;
	}

	protected function _set_urls() {
		$this->_set_custom_headers();

		$account_id = $this->_get_account_id();
		$folder_id  = $this->_get_folder_id();

		$this->BASE_URL        = "{$this->BASE_URL}/{$account_id}/c/{$folder_id}";
		$this->FIELDS_URL      = "{$this->BASE_URL}/customfields";
		$this->LISTS_URL       = "{$this->BASE_URL}/lists";
		$this->SUBSCRIBE_URL   = "{$this->BASE_URL}/subscriptions";
		$this->SUBSCRIBERS_URL = "{$this->BASE_URL}/contacts";
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'client_id' => array(
				'label' => esc_html__( 'App ID', 'et_core' ),
			),
			'username'  => array(
				'label' => esc_html__( 'Username', 'et_core' ),
			),
			'password'  => array(
				'label' => esc_html__( 'App Password', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'              => array(
				'list_id'           => 'listId',
				'name'              => 'name',
				'subscribers_count' => 'total',
			),
			'subscriber'        => array(
				'name'          => 'firstName',
				'last_name'     => 'lastName',
				'email'         => 'email',
				'list_id'       => 'listId',
				'custom_fields' => 'custom_fields',
			),
			'subscriptions'     => array(
				'list_id'    => 'listId',
				'message_id' => 'confirmationMessageId',
			),
			'custom_field'      => array(
				'field_id' => 'privateName',
				'name'     => 'publicName',
				'type'     => 'fieldType',
				'hidden'   => '!displayToUser',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['client_id'] ) || empty( $this->data['username'] ) || empty( $this->data['password'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_set_urls();

		$this->response_data_key = 'lists';

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( empty( $this->data['client_id'] ) || empty( $this->data['username'] ) || empty( $this->data['password'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_set_urls();

		if ( $this->_get_subscriber( $args ) ) {
			// Subscriber exists and is already subscribed to the list.
			return 'success';
		}

		if ( ! $contact = $this->_get_subscriber( $args, 'list_id' ) ) {
			// Create new contact
			$body = $this->transform_data_to_provider_format( $args, 'subscriber' );
			$body = $this->_process_custom_fields( $body );

			$this->prepare_request( $this->SUBSCRIBERS_URL, 'POST', false, array( $body ), true );
			$this->make_remote_request();

			if ( $this->response->ERROR ) {
				return $this->get_error_message();
			}

			$contact = $this->response->DATA['contacts'][0];
		}

		// Subscribe contact to list
		$body              = $this->transform_data_to_provider_format( $args, 'subscriptions' );
		$body['contactId'] = $contact['contactId'];
		$body['status']    = isset( $args['message_id'] ) ? 'pending' : 'normal';

		$this->prepare_request( $this->SUBSCRIBE_URL, 'POST', false, array( $body ), true );

		return parent::subscribe( $args, $this->SUBSCRIBE_URL );
	}
}

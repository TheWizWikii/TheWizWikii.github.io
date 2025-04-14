<?php

/**
 * Wrapper for Feedblitz's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Feedblitz extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://www.feedblitz.com';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://www.feedblitz.com/f.api/syndications';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://www.feedblitz.com/f';

	/**
	 * @inheritDoc
	 */
	public $custom_fields = 'dynamic';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'Feedblitz';

	/**
	 * @inheritDoc
	 */
	public $slug = 'feedblitz';

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

			self::$_->array_set( $args, $field_id, rawurlencode( $value ) );
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
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'subscribersummary.subscribers',
			),
			'subscriber' => array(
				'list_id'       => 'listid',
				'email'         => 'email',
				'name'          => 'FirstName',
				'last_name'     => 'LastName',
				'custom_fields' => 'custom_fields',
			),
			'error'      => array(
				'error_message' => 'rsp.err.@attributes.msg',
			)
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

		$this->http->expects_json = false;
		$this->response_data_key  = false;
		$this->LISTS_URL          = add_query_arg( 'key', $this->data['api_key'], $this->LISTS_URL );

		parent::fetch_subscriber_lists();

		$response = $this->data_utils->process_xmlrpc_response( $this->response->DATA, true );
		$response = $this->data_utils->xml_to_array( $response );

		if ( $this->response->ERROR || ! empty( $response['rsp']['err']['@attributes']['msg'] ) ) {
			return $this->get_error_message();
		}

		$this->data['lists']         = $this->_process_subscriber_lists( $response['syndications']['syndication'] );
		$this->data['is_authorized'] = true;

		$this->save_data();

		return 'success';
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$query_args = array(
			'email'         => rawurlencode( $args['email'] ),
			'name'          => empty( $args['name'] ) ? '' : rawurlencode( $args['name'] ),
			'last_name'     => empty( $args['last_name'] ) ? '' : rawurlencode( $args['last_name'] ),
			'custom_fields' => $args['custom_fields'],
			'list_id'       => $args['list_id'],
		);

		$query        = $this->transform_data_to_provider_format( $query_args, 'subscriber' );
		$query        = $this->_process_custom_fields( $query );
		$query['key'] = rawurlencode( $this->data['api_key'] );
		$url          = add_query_arg( $query, "{$this->SUBSCRIBE_URL}?SimpleApiSubscribe" );

		$this->prepare_request( $url, 'GET', false, null, false, false );
		$this->make_remote_request();

		$response = $this->data_utils->process_xmlrpc_response( $this->response->DATA, true );
		$response = $this->data_utils->xml_to_array( $response );

		if ( $this->response->ERROR || ! empty( $response['rsp']['err']['@attributes']['msg'] ) ) {
			return $this->get_error_message();
		}

		if ( ! empty( $response['rsp']['success']['@attributes']['msg'] ) ) {
			return $response['rsp']['success']['@attributes']['msg'];
		}

		return 'success';
	}
}

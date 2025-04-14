<?php

/**
 * Wrapper for MadMimi's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MadMimi extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.madmimi.com';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.madmimi.com/audience_lists/lists.json';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://api.madmimi.com/audience_lists/@list_id@/add';

	/**
	 * @inheritDoc
	 */
	public $custom_fields = 'dynamic';

	/**
	 * @inheritDoc
	 */
	public $name = 'MadMimi';

	/**
	 * @inheritDoc
	 */
	public $slug = 'madmimi';

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_urls();
	}

	protected function _maybe_set_urls( $list_id = '' ) {
		if ( ! empty( $this->data['api_key'] ) && ! empty( $this->data['username'] ) ) {
			$args = array(
				'username' => rawurlencode( $this->data['username'] ),
				'api_key'  => $this->data['api_key'],
			);

			$this->LISTS_URL     = add_query_arg( $args, $this->LISTS_URL );
			$this->SUBSCRIBE_URL = add_query_arg( $args, $this->SUBSCRIBE_URL );

			if ( $list_id ) {
				$this->SUBSCRIBE_URL = str_replace( '@list_id@', rawurlencode( $list_id ), $this->SUBSCRIBE_URL );
			}
		}
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

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'username' => array(
				'label' => esc_html__( 'Username', 'et_core' ),
			),
			'api_key'  => array(
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
				'subscribers_count' => 'list_size',
			),
			'subscriber' => array(
				'name'          => 'first_name',
				'last_name'     => 'last_name',
				'email'         => 'email',
				'custom_fields' => 'custom_fields',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['username'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_urls();

		$this->response_data_key = false;

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['username'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_urls( $args['list_id'] );

		$ip_address = 'true' === self::$_->array_get( $args, 'ip_address', 'true' ) ? et_core_get_ip_address() : '0.0.0.0';

		$args                   = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$args                   = $this->_process_custom_fields( $args );
		$args['ip_address']     = $ip_address;
		$args['subscribed_via'] = $this->SUBSCRIBED_VIA;

		$this->SUBSCRIBE_URL = add_query_arg( $args, $this->SUBSCRIBE_URL );

		$this->prepare_request( $this->SUBSCRIBE_URL, 'POST', false );

		return parent::subscribe( $args, $url );
	}
}

<?php

/**
 * Wrapper for GetResponse's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_GetResponse extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.getresponse.com/v3';

	/**
	 * @inheritDoc
	 */
	public $FIELDS_URL = 'https://api.getresponse.com/v3/custom-fields';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.getresponse.com/v3/campaigns';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://api.getresponse.com/v3/contacts';

	/**
	 * @inheritDoc
	 */
	public $name = 'GetResponse';

	/**
	 * @inheritDoc
	 */
	public $name_field_only = true;

	/**
	 * @inheritDoc
	 */
	public $slug = 'getresponse';

	/**
	 * @inheritDoc
	 * @internal If true, oauth endpoints properties must also be defined.
	 */
	public $uses_oauth = false;

	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}

	protected function _maybe_set_custom_headers() {
		if ( empty( $this->custom_headers ) && isset( $this->data['api_key'] ) ) {
			$this->custom_headers = array( 'X-Auth-Token' => "api-key {$this->data['api_key']}" );
		}
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$fields_unprocessed = $args['custom_fields'];
		$fields             = array();

		unset( $args['custom_fields'] );

		foreach ( $fields_unprocessed as $field_id => $value ) {
			if ( is_array( $value ) && $value ) {
				// This is a multiple choice field (eg. checkbox, radio, select)
				$value = array_values( $value );
			} else {
				$value = array( $value );
			}

			$fields[] = array(
				'customFieldId' => $field_id,
				'value'         => $value,
			);
		}

		$args['customFieldValues'] = $fields;

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
				'name'              => 'name',
				'list_id'           => 'campaignId',
				'subscribers_count' => 'totalSubscribers',
			),
			'subscriber'        => array(
				'name'          => 'name',
				'email'         => 'email',
				'list_id'       => 'campaign.campaignId',
				'ip_address'    => 'ipAddress',
				'custom_fields' => 'custom_fields',
			),
			'error'             => array(
				'error_message' => 'message',
			),
			'custom_field'      => array(
				'field_id' => 'customFieldId',
				'name'     => 'name',
				'type'     => 'fieldType',
				'options'  => 'values',
				'hidden'   => 'hidden',
			),
			'custom_field_type' => array(
				// Us <=> Them
				'textarea'      => 'textarea',
				'radio'         => 'radio',
				'checkbox'      => 'checkbox',
				// Us => Them
				'input'         => 'text',
				'select'        => 'single_select',
				// Them => Us
				'text'          => 'input',
				'single_select' => 'select',
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

		$this->_maybe_set_custom_headers();

		$this->response_data_key = false;

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$ip_address = 'true' === self::$_->array_get( $args, 'ip_address', 'true' ) ? et_core_get_ip_address() : '0.0.0.0';

		$args['ip_address'] = $ip_address;
		$args               = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$args               = $this->_process_custom_fields( $args );
		$args['note']       = $this->SUBSCRIBED_VIA;
		$args['dayOfCycle'] = 0;

		if ( empty( $args['name'] ) ) {
			unset( $args['name'] );
		}

		$this->prepare_request( $this->SUBSCRIBE_URL, 'POST', false, $args );

		return parent::subscribe( $args, $this->SUBSCRIBE_URL );
	}
}

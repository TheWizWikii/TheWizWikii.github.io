<?php

/**
 * Wrapper for MailerLite's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailerLite extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.mailerlite.com/api/v2';

	/**
	 * @inheritDoc
	 */
	public $FIELDS_URL = 'https://api.mailerlite.com/api/v2/fields';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.mailerlite.com/api/v2/groups';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'MailerLite';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailerlite';

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
			$this->custom_headers = array( 'X-MailerLite-ApiKey' => "{$this->data['api_key']}" );
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

			self::$_->array_set( $args, "fields.{$field_id}", $value );
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
				'subscribers_count' => 'active',
			),
			'subscriber'   => array(
				'name'          => 'fields.name',
				'last_name'     => 'fields.last_name',
				'email'         => 'email',
				'custom_fields' => 'custom_fields',
				'resubscribe'   => 'resubscribe',
			),
			'error'        => array(
				'error_message' => 'error.message',
			),
			'custom_field' => array(
				'field_id' => 'key',
				'name'     => 'title',
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
		$args['resubscribe'] = 1;
		$url = "{$this->LISTS_URL}/{$args['list_id']}/subscribers";

		return parent::subscribe( $args, $url );
	}
}

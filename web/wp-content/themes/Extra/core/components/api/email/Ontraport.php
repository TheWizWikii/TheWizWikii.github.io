<?php

/**
 * Wrapper for Ontraport's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Ontraport extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $FIELDS_URL = 'https://api.ontraport.com/1/Contacts/meta';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.ontraport.com/1/objects?objectID=5';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://api.ontraport.com/1';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'Ontraport';

	/**
	 * @inheritDoc
	 */
	public $slug = 'ontraport';

	/**
	 * @inheritDoc
	 * @internal If true, oauth endpoints properties must also be defined.
	 */
	public $uses_oauth = false;

	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		static $fields = null;

		if ( is_null( $fields ) ) {
			$this->response_data_key = null;

			parent::_fetch_custom_fields( $list_id, $list );

			if ( $this->response->ERROR ) {
				et_debug( $this->get_error_message() );

				return array();
			}

			$fields             = array();
			$fields_unprocessed = self::$_->array_get( $this->response->DATA, 'data.[0].fields', $fields );

			foreach ( $fields_unprocessed as $field_id => $field ) {
				if ( in_array( $field_id, array( 'firstname', 'lastname', 'email' ) ) ) {
					continue;
				}

				$type = $field['type'];

				$field['field_id'] = $field_id;
				$field['type']     = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'text' );

				$fields[ $field_id ] = $this->transform_data_to_our_format( $field, 'custom_field' );
			}
		}

		return $fields;
	}

	protected function _get_subscriber_list_type( $list_id ) {
		$sequence_key = 'seq:' . $list_id;
		$campaign_key = 'camp:' . $list_id;

		if ( isset( $this->data['lists'][ $campaign_key ] ) ) {
			return 'Campaign';
		}

		if ( isset( $this->data['lists'][ $sequence_key ] ) ) {
			return 'Sequence';
		}

		return 'Campaign';
	}

	protected function _maybe_set_custom_headers() {
		if ( empty( $this->custom_headers ) && isset( $this->data['api_key'] )  && isset( $this->data['client_id'] ) ) {
			$this->custom_headers = array(
				'Api-Appid' => sanitize_text_field( $this->data['client_id'] ),
				'Api-Key'   => sanitize_text_field( $this->data['api_key'] ),
			);
		}
	}

	protected function _prefix_subscriber_lists( $name_prefix, $id_prefix ) {
		$lists = array();

		foreach ( $this->data['lists'] as $list_id => $list ) {
			$key = $id_prefix . $list_id;

			if ( ! $list['name'] ) {
				$list['name'] = $list_id;
			}

			$lists[ $key ]            = $list;
			$lists[ $key ]['name']    = $name_prefix . $list['name'];
			$lists[ $key ]['list_id'] = $key;
		}

		$this->data['lists'] = $lists;

		$this->save_data();
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
				$value = array_keys( $value );

				if ( 'checkbox' === $this->data['custom_fields'][ $field_id ]['type'] ) {
					// Determine if checkbox is a single checkbox or a list. 
					// In case of single checkbox pass `1` as a value
					if ( ! empty( $this->data['custom_fields'][ $field_id ]['options'] ) ) {
						$value = implode( '*/*', $value );
						$value = "*/*{$value}*/*";
					} else {
						$value = '1';
					}
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
			'api_key'   => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
			'client_id' => array(
				'label' => esc_html__( 'APP ID', 'et_core' ),
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
				'list_id'           => 'drip_id',
				'subscribers_count' => 'subscriber_count',
			),
			'subscriber'        => array(
				'name'          => 'firstname',
				'last_name'     => 'lastname',
				'email'         => 'email',
				'custom_fields' => 'custom_fields',
			),
			'custom_field'      => array(
				'field_id' => 'field_id',
				'type'     => 'type',
				'name'     => 'alias',
				'options'  => 'options',
			),
			'custom_field_type' => array(
				// Us => Them
				'input'    => 'text',
				'textarea' => 'textlong',
				'checkbox' => 'list',
				'select'   => 'drop',
				// Them => Us
				'text'     => 'input',
				'textlong' => 'textarea',
				'list'     => 'checkbox',
				'check'    => 'checkbox',
				'drop'     => 'select',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['client_id'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_custom_headers();

		$this->response_data_key = 'data';

		parent::fetch_subscriber_lists();

		$this->_prefix_subscriber_lists( 'Sequence: ', 'seq:' );

		$sequences = $this->data['lists'];
		$url       = 'https://api.ontraport.com/1/CampaignBuilderItems';
		$url       = add_query_arg( 'listFields', 'id,name,subs', $url );

		$this->data_keys['list']['list_id']           = 'id';
		$this->data_keys['list']['subscribers_count'] = 'subs';

		$this->prepare_request( $url );

		$this->response_data_key = 'data';

		$result = parent::fetch_subscriber_lists();

		$this->_prefix_subscriber_lists( 'Campaign: ', 'camp:' );

		$this->data['lists'] = array_merge( $this->data['lists'], $sequences );

		$this->save_data();

		return $result;
	}

	public function get_subscriber( $email ) {
		$args = array(
			'objectID' => '0',
			'email' => rawurlencode( $email ),
		);

		$url = add_query_arg( $args, $this->SUBSCRIBE_URL . '/object/getByEmail' );

		$this->prepare_request( $url );
		$this->make_remote_request();

		return self::$_->array_get( $this->response->DATA, 'data.id', false );
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['client_id'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$list_id          = $args['list_id'];
		$args             = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$args             = $this->_process_custom_fields( $args );
		$args['objectID'] = 0;
		$url              = $this->SUBSCRIBE_URL . '/Contacts/saveorupdate';

		// Create or update contact
		$this->prepare_request( $url, 'POST', false, $args );
		$this->make_remote_request();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		$list_id_parts = explode( ':', $list_id );
		$list_id       = array_pop( $list_id_parts );
		$data          = $this->response->DATA['data'];

		// Subscribe contact to list
		$url  = $this->SUBSCRIBE_URL . '/objects/subscribe';
		$args = array(
			'ids'      => self::$_->array_get( $data, 'id', $data['attrs']['id'] ),
			'add_list' => $list_id,
			'sub_type' => $this->_get_subscriber_list_type( $list_id ),
			'objectID' => 0,
		);

		$this->prepare_request( $url, 'PUT', false, $args );

		return parent::subscribe( $args, $url );
	}
}

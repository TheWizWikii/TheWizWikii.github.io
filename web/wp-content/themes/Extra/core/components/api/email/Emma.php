<?php

/**
 * Wrapper for Emma's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Emma extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.e2ma.net';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $http_auth = array(
		'username' => 'api_key',
		'password' => 'private_api_key',
	);

	/**
	 * @inheritDoc
	 */
	public $name = 'Emma';

	/**
	 * @inheritDoc
	 */
	public $slug = 'emma';

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		$this->response_data_key = false;

		$this->prepare_request( $this->_get_custom_fields_url() );

		return parent::_fetch_custom_fields( $list_id, $list );
	}

	protected function _get_custom_fields_url() {
		return "https://api.e2ma.net/{$this->data['user_id']}/fields?group_types=all";
	}

	protected function _get_lists_url() {
		return "https://api.e2ma.net/{$this->data['user_id']}/groups?group_types=all";
	}

	protected function _get_subscribe_url() {
		return "https://api.e2ma.net/{$this->data['user_id']}/members/signup";
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
			'api_key'         => array(
				'label' => esc_html__( 'Public API Key', 'et_core' ),
			),
			'private_api_key' => array(
				'label' => esc_html__( 'Private API Key', 'et_core' ),
			),
			'user_id'         => array(
				'label' => esc_html__( 'Account ID', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'              => array(
				'name'              => 'group_name',
				'list_id'           => 'member_group_id',
				'subscribers_count' => 'active_count',
			),
			'subscriber'        => array(
				'email'         => 'email',
				'name'          => 'fields.first_name',
				'last_name'     => 'fields.last_name',
				'list_id'       => '@group_ids',
				'custom_fields' => 'custom_fields',
			),
			'custom_field'      => array(
				'field_id' => 'shortcut_name',
				'name'     => 'display_name',
				'options'  => 'options',
				'type'     => 'widget_type',
			),
			'custom_field_type' => array(
				// Us <=> Them
				'radio'          => 'radio',
				// Us => Them
				'select'         => 'select one',
				'text'           => 'long',
				'checkbox'       => 'check_multiple',
				// Them => Us
				'text'           => 'input',
				'long'           => 'text',
				'check_multiple' => 'checkbox',
				'select one'     => 'select',
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

		$this->LISTS_URL         = $this->_get_lists_url();
		$this->response_data_key = false;

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$url                     = $this->_get_subscribe_url();
		$this->response_data_key = 'member_id';

		$args = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$args = $this->_process_custom_fields( $args );

		$this->prepare_request( $url, 'POST', false, $args, true );

		return parent::subscribe( $args, $url );
	}
}

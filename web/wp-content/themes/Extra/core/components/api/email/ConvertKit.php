<?php

/**
 * Wrapper for ConvertKit's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_ConvertKit extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.convertkit.com/v3';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'ConvertKit';

	/**
	 * @inheritDoc
	 */
	public $name_field_only = true;

	/**
	 * @inheritDoc
	 */
	public $slug = 'convertkit';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	/**
	 * ET_Core_API_Email_ConvertKit constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner, $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		// ConvertKit doesn't have "lists". They have "forms" so we use "forms" as if they were "lists".
		$this->LISTS_URL = "{$this->BASE_URL}/forms";
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		$this->response_data_key = 'custom_fields';

		$this->prepare_request( $this->_generate_url_for_request( "{$this->BASE_URL}/custom_fields" ) );

		return parent::_fetch_custom_fields( $list_id, $list );
	}

	/**
	 * Generates the URL for adding subscribers.
	 *
	 * @param $list_id
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	protected function _get_subscribe_url( $list_id ) {
		return "{$this->LISTS_URL}/{$list_id}/subscribe";
	}

	protected function _get_subscriber_counts( $forms ) {
		$result = array();

		foreach ( (array) $forms as $form_info ) {
			$url = $this->_generate_url_for_request( "{$this->LISTS_URL}/{$form_info['id']}/subscriptions", true );

			$this->prepare_request( $url );
			$this->make_remote_request();

			if ( $this->response->ERROR || ! isset( $this->response->DATA['total_subscriptions'] ) ) {
				continue;
			}

			$form_info['total_subscriptions'] = $this->response->DATA['total_subscriptions'];

			$result[] = $form_info;
		}

		return $result;
	}

	/**
	 * Adds default args for all API requests to given url.
	 *
	 * @since 1.1.0
	 *
	 * @param string $url
	 * @param bool   $with_secret
	 *
	 * @return string
	 */
	protected function _generate_url_for_request( $url, $with_secret = false ) {
		$key = $with_secret ? $this->data['api_secret'] : $this->data['api_key'];
		$key_type = $with_secret ? 'api_secret' : 'api_key';

		return esc_url_raw( add_query_arg( $key_type, $key, $url ) );
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
			'api_secret' => array(
				'label' => esc_html__( 'API Secret', 'et_core' ),
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
				'subscribers_count' => 'total_subscriptions',
			),
			'subscriber'   => array(
				'email'         => 'email',
				'name'          => 'first_name',
				'custom_fields' => 'custom_fields',
			),
			'error'        => array(
				'error_message' => 'message',
			),
			'custom_field' => array(
				'field_id' => 'key',
				'name'     => 'label',
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

		$url    = $this->_generate_url_for_request( $this->LISTS_URL );
		$result = 'success';

		$this->response_data_key = '';

		$this->prepare_request( $url );
		parent::fetch_subscriber_lists();

		if ( ! $this->response->ERROR && ! empty( $this->response->DATA['forms'] ) ) {
			/**
			 * We need to store `$forms` to avoid using mutated `$this->response->DATA['forms']` down the line.
			 *
			 * `$this->response->DATA['forms']` will be mutated to `NULL` in this code block since it is a shared state
			 * and is used by other functions all over. This fixes the issue.
			 *
			 * @see: https://github.com/elegantthemes/Divi/issues/25296
			 */
			$forms                       = $this->response->DATA['forms'];
			$with_subscriber_counts      = $this->_get_subscriber_counts( $forms );
			$this->data['lists']         = $this->_process_subscriber_lists( $with_subscriber_counts );
			$this->data['is_authorized'] = 'true';
			$this->data['custom_fields'] = $this->_fetch_custom_fields( '', array_shift( $forms ) );
			$this->save_data();
		} elseif ( $this->response->ERROR ) {
			$result = $this->get_error_message();
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$url = $this->_generate_url_for_request( $this->_get_subscribe_url( $args['list_id'] ) );
		$params = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$params = $this->_process_custom_fields( $params );
		$params['fields']['notes'] = $this->SUBSCRIBED_VIA;

		$this->prepare_request( $url, 'POST', false, $params );

		return parent::subscribe( $params, $url );
	}
}

<?php

/**
 * Wrapper for ActiveCampaign's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_ActiveCampaign extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $name = 'ActiveCampaign';

	/**
	 * @inheritDoc
	 */
	public $slug = 'activecampaign';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		// These general / default fields are static (it can not be edited or deleted in the ActiveCampaign dashboard) and not returned by the rest API,
		// so just add it here as the default custom fields for ActiveCampaign.
		$fields = array(
			'%phone%'              => array(
				'field_id' => '%phone%',
				'hidden'   => false,
				'name'     => 'Phone',
				'type'     => 'input',
			),
			'%customer_acct_name%' => array(
				'field_id' => '%customer_acct_name%',
				'hidden'   => false,
				'name'     => 'Account',
				'type'     => 'input',
			),
		);

		foreach ( $list['fields'] as $field ) {
			$field    = $this->transform_data_to_our_format( $field, 'custom_field' );
			$field_id = $field['field_id'];
			$type     = $field['type'];

			$field['type'] = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'text' );

			if ( isset( $field['options'] ) ) {
				$options = array();

				foreach ( $field['options'] as $option ) {
					$option = $this->transform_data_to_our_format( $option, 'custom_field_option' );
					$id     = $option['id'];

					$options[ $id ] = $option['name'];
				}

				$field['options'] = $options;
			}

			$fields[ $field_id ] = $field;
		}

		return $fields;
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

				if ( 'checkbox' === $this->data['custom_fields'][ $field_id ]['type'] ) {
					$value = implode( '||', $value );
					$value = "||{$value}||";
				} else {
					$value = array_pop( $value );
				}
			}

			// Check if the custom field is an ActiveCampaign general field,
			// it should be posted as dedicated param instead of `field[{$field_id},0]` param.
			if ( preg_match( '/%(.*)%/', $field_id, $matches ) ) {
				self::$_->array_set( $args, $matches[1], $value );
			} else {
				self::$_->array_set( $args, "field[{$field_id},0]", $value );
			}
		}

		return $args;
	}

	/**
	 * Returns the requests URL for the account assigned to this class instance.
	 *
	 * @return string
	 */
	protected function _get_requests_url() {
		$base_url = untrailingslashit( $this->data['api_url'] );

		return "{$base_url}/admin/api.php";
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
			'api_url' => array(
				'label'               => esc_html__( 'API URL', 'et_core' ),
				'apply_password_mask' => false,
			),
			'form_id' => array(
				'label'               => esc_html__( 'Form ID', 'et_core' ),
				'not_required'        => true,
				'apply_password_mask' => false,
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'                => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'subscriber_count',
			),
			'subscriber'          => array(
				'email'         => 'email',
				'last_name'     => 'last_name',
				'name'          => 'first_name',
				'custom_fields' => 'custom_fields',
			),
			'error'               => array(
				'error_message' => 'result_message',
			),
			'custom_field'        => array(
				'field_id' => 'id',
				'name'     => 'title',
				'type'     => 'type',
				'hidden'   => '!visible',
				'options'  => 'options',
			),
			'custom_field_option' => array(
				'id'   => 'value',
				'name' => 'name',
			),
			'custom_field_type'   => array(
				// Us <=> Them
				'checkbox' => 'checkbox',
				'radio'    => 'radio',
				'textarea' => 'textarea',
				'hidden'   => 'hidden',
				// Us => Them
				'select'   => 'dropdown',
				'input'    => 'text',
				// Them => Us
				'dropdown' => 'select',
				'text'     => 'input',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['api_url'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$query_args = array(
			'api_key'       => $this->data['api_key'],
			'api_action'    => 'list_list',
			'api_output'    => 'json',
			'ids'           => 'all',
			'full'          => '1',
			'global_fields' => '1',
		);

		$request_url = add_query_arg( $query_args, $this->_get_requests_url() );
		$request_url = esc_url_raw( $request_url, array( 'https' ) );

		$this->prepare_request( $request_url );
		$this->request->HEADERS['Content-Type'] = 'application/x-www-form-urlencoded';

		parent::fetch_subscriber_lists();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		$lists = array();

		foreach ( $this->response->DATA as $key => $list_data ) {
			if ( ! is_numeric( $key ) ) {
				continue;
			}

			if ( ! empty( $list_data ) ) {
				$lists[] = $list_data;
			}
		}

		$this->data['lists']         = $this->_process_subscriber_lists( $lists );
		$this->data['custom_fields'] = $this->_fetch_custom_fields( '', array_shift( $this->response->DATA ) );
		$this->data['is_authorized'] = 'true';

		$this->save_data();

		et_debug($this->data);

		return 'success';
	}

	/**
	 * Get ActiveCampaign subscriber info by email.
	 *
	 * @param string $email
	 *
	 * @return array
	 */
	public function get_subscriber( $email ) {
		$query_args = array(
			'api_key'    => $this->data['api_key'],
			'api_action' => 'subscriber_view_email',
			'api_output' => 'json',
			'email'      => $email,
		);

		// Build request URL. This action only accept GET method.
		$request_url = add_query_arg( $query_args, $this->_get_requests_url() );
		$request_url = esc_url_raw( $request_url, array( 'https' ) );

		// Prepare and send the request.
		$this->prepare_request( $request_url );
		$this->request->HEADERS['Content-Type'] = 'application/x-www-form-urlencoded';
		$this->make_remote_request();

		// Ensure no error happen and it's included in one of the lists.
		$list_id     = self::$_->array_get( $this->response->DATA, 'listid', false );
		$result_code = self::$_->array_get( $this->response->DATA, 'result_code', false );
		if ( $this->response->ERROR || ! $list_id || ! $result_code ) {
			return false;
		}

		return $this->response->DATA;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url= '' ) {
		// Ensure to skip subscribe action if current email already subscribed.
		$subscriber_data  = $this->get_subscriber( $args['email'] );
		$subscriber_lists = self::$_->array_get( $subscriber_data, 'lists', array() );
		$subscriber_list  = self::$_->array_get( $subscriber_lists, $args['list_id'], false );
		if ( $subscriber_list ) {
			return 'success';
		}

		$list_id_key    = 'p[' . $args['list_id'] . ']';
		$status_key     = 'status[' . $args['list_id'] . ']';
		$responders_key = 'instantresponders[' . $args['list_id'] . ']';
		$list_id        = $args['list_id'];

		$args = $this->transform_data_to_provider_format( $args, 'subscriber', array( 'list_id' ) );
		$args = $this->_process_custom_fields( $args );

		$query_args = array(
			'api_key'    => $this->data['api_key'],
			'api_action' => 'contact_sync',
			'api_output' => 'json',
		);

		$url = esc_url_raw( add_query_arg( $query_args, $this->_get_requests_url() ) );

		$args[ $list_id_key ]    = $list_id;
		$args[ $status_key ]     = 1;
		$args[ $responders_key ] = 1;

		if ( ! empty( $this->data['form_id'] ) ) {
			$args['form'] = (int) $this->data['form_id'];
		}

		$this->prepare_request( $url, 'POST', false, $args );
		$this->request->HEADERS['Content-Type'] = 'application/x-www-form-urlencoded';

		return parent::subscribe( $args, $url );
	}
}

<?php

/**
 * Wrapper for MailChimp's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailChimp extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = '';

	/**
	 * Use this variable to hold the pattern and update $BASE_URL dynamically when needed
	 */
	public $BASE_URL_PATTERN = 'https://@datacenter@.api.mailchimp.com/3.0';

	/**
	 * @inheritDoc
	 */
	public $http_auth = array(
		'username' => '-',
		'password' => 'api_key',
	);

	/**
	 * @inheritDoc
	 */
	public $name = 'MailChimp';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailchimp';

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		if ( ! empty( $this->data['api_key'] ) ) {
			$this->_set_base_url();
		}

		$this->http_auth['username'] = $owner;
	}

	protected function _add_note_to_subscriber( $email, $url ) {
		$email = md5( $email );

		$this->prepare_request( "{$url}/$email/notes", 'POST' );

		$this->request->BODY = json_encode( array( 'note' => $this->SUBSCRIBED_VIA ) );

		$this->make_remote_request();
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		$this->response_data_key = 'merge_fields';

		$this->prepare_request( "{$this->BASE_URL}/lists/{$list_id}/merge-fields?count={$this->COUNT}" );

		$fields = parent::_fetch_custom_fields( $list_id, $list );

		foreach ( $fields as $id => $field ) {
			if ( in_array( $id, array( 1, 2 ) ) ) {
				unset( $fields[ $id ] );
			}
		}

		// MailChimp is weird in that they treat checkbox fields as an entirely different concept in their API (Groups)
		// We'll grab the groups and treat them as checkbox fields in our UI.
		$groups = $this->_fetch_subscriber_list_groups( $list_id );

		return $fields + $groups;
	}

	protected function _fetch_subscriber_list_group_options( $list_id, $group_id ) {
		$this->prepare_request( "{$this->BASE_URL}/lists/{$list_id}/interest-categories/{$group_id}/interests?count={$this->COUNT}" );

		$this->make_remote_request();

		if ( $this->response->ERROR ) {
			et_debug( $this->get_error_message() );

			return array();
		}

		$data    = $this->response->DATA['interests'];
		$options = array();

		foreach ( $data as $option ) {
			$option = $this->transform_data_to_our_format( $option, 'group_option' );
			$id     = $option['id'];

			$options[ $id ] = $option['name'];
		}

		return $options;
	}

	protected function _fetch_subscriber_list_groups( $list_id ) {
		$this->response_data_key = 'categories';

		$this->prepare_request( "{$this->BASE_URL}/lists/{$list_id}/interest-categories?count={$this->COUNT}" );
		$this->make_remote_request();

		$groups = array();

		if ( false !== $this->response_data_key && empty( $this->response_data_key ) ) {
			// Let child class handle parsing the response data themselves.
			return $groups;
		}

		if ( $this->response->ERROR ) {
			et_debug( $this->get_error_message() );

			return $groups;
		}

		$data = $this->response->DATA[ $this->response_data_key ];

		foreach ( $data as $group ) {
			$group    = $this->transform_data_to_our_format( $group, 'group' );
			$field_id = $group['field_id'];
			$type     = $group['type'];

			if ( 'hidden' === $type ) {
				// MailChimp only allows groups of type: 'checkbox' to be hidden.
				$group['type']   = 'checkbox';
				$group['hidden'] = true;
			}

			$group['is_group'] = true;
			$group['options']  = $this->_fetch_subscriber_list_group_options( $list_id, $field_id );
			$group['type']     = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'text' );

			$groups[ $field_id ] = $group;
		}

		return $groups;
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$fields = $args['custom_fields'];
		$list_id = self::$_->array_get( $args, 'list_id', '' );

		unset( $args['custom_fields'] );
		unset( $args['list_id'] );

		$custom_fields_data = self::$_->array_get( $this->data, "lists.{$list_id}.custom_fields", array() );

		foreach ( $fields as $field_id => $value ) {
			$is_group = self::$_->array_get( $custom_fields_data, "{$field_id}.is_group", false );

			if ( is_array( $value ) && $value ) {
				foreach ( $value as $id => $field_value ) {
					if ( $is_group ) {
						// If it is a group custom field, set as `interests` and don't process the `merge_fields`
						self::$_->array_set( $args, "interests.{$id}", true );
						$field_id = false;
					} else {
						$value = $field_value;
					}
				}
			}

			if ( false === $field_id ) {
				continue;
			}

			// In previous version of Mailchimp implementation we only supported default field tag, but it can be customized and our code fails.
			// Added `field_tag` attribute which is actual field tag. Fallback to default field tag if `field_tag` doesn't exist for backward compatibility.
			$custom_field_tag = self::$_->array_get( $custom_fields_data, "{$field_id}.field_tag", "MMERGE{$field_id}" );

			// Need to strips existing slash chars.
			self::$_->array_set( $args, "merge_fields.{$custom_field_tag}", stripslashes( $value ) );
		}

		return $args;
	}

	protected function _set_base_url() {
		$api_key_pieces = explode( '-', $this->data['api_key'] );
		$datacenter     = empty( $api_key_pieces[1] ) ? '' : $api_key_pieces[1];
		$this->BASE_URL = str_replace( '@datacenter@', $datacenter, $this->BASE_URL_PATTERN );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_set_base_url();

		/**
		 * The maximum number of subscriber lists to request from MailChimp's API.
		 *
		 * @since 2.0.0
		 *
		 * @param int $max_lists
		 */
		$max_lists = (int) apply_filters( 'et_core_api_email_mailchimp_max_lists', 250 );

		$url = "{$this->BASE_URL}/lists?count={$max_lists}&fields=lists.name,lists.id,lists.stats,lists.double_optin";

		$this->prepare_request( $url );

		$this->response_data_key = 'lists';

		$result = parent::fetch_subscriber_lists();

		return $result;
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
				'list_id'           => 'id',
				'name'              => 'name',
				'double_optin'      => 'double_optin',
				'subscribers_count' => 'stats.member_count',
			),
			'subscriber'        => array(
				'email'         => 'email_address',
				'name'          => 'merge_fields.FNAME',
				'last_name'     => 'merge_fields.LNAME',
				'custom_fields' => 'custom_fields',
			),
			'error'             => array(
				'error_message' => 'detail',
			),
			'custom_field'      => array(
				'field_id'  => 'merge_id',
				'field_tag' => 'tag',
				'name'      => 'name',
				'type'      => 'type',
				'hidden'    => '!public',
				'options'   => 'options.choices',
			),
			'custom_field_type' => array(
				// Us <=> Them
				'radio'      => 'radio',
				// Us => Them
				'input'      => 'text',
				'select'     => 'dropdown',
				'checkbox'   => 'checkboxes',
				// Them => Us
				'text'       => 'input',
				'dropdown'   => 'select',
				'checkboxes' => 'checkbox',
			),
			'group'             => array(
				'field_id' => 'id',
				'name'     => 'title',
				'type'     => 'type',
			),
			'group_option'      => array(
				'id'   => 'id',
				'name' => 'name',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	public function get_subscriber( $list_id, $email ) {
		$hash = md5( strtolower( $email ) );
		$this->prepare_request( implode( '/', array( $this->BASE_URL, 'lists', $list_id, 'members', $hash ) ) );
		$this->make_remote_request();

		return $this->response->STATUS_CODE !== 200 ? null : $this->response->DATA;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$list_id   = $args['list_id'];
		$args      = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$url       = "{$this->BASE_URL}/lists/{$list_id}/members";
		$email     = $args['email_address'];
		$err       = esc_html__( 'An error occurred, please try later.', 'et_core' );
		$dbl_optin = self::$_->array_get( $this->data, "lists.{$list_id}.double_optin", true );

		$ip_address = 'true' === self::$_->array_get( $args, 'ip_address', 'true' ) ? et_core_get_ip_address() : '0.0.0.0';

		$args['ip_signup'] = $ip_address;
		$args['status']    = $dbl_optin ? 'pending' : 'subscribed';
		$args['list_id']   = $list_id;

		$args = $this->_process_custom_fields( $args );

		$this->prepare_request( $url, 'POST', false, $args, true );
		$result = parent::subscribe( $args, $url );

		if ( false !== stripos( $result, 'already a list member' ) ) {
			$result = $err;

			if ( $user = $this->get_subscriber( $list_id, $email ) ) {
				if ( 'subscribed' === $user['status'] ) {
					$result = 'success';
				} else {
					$this->prepare_request( implode( '/', array( $url, $user['id'] ) ), 'PUT', false, $args, true );

					$result = parent::subscribe( $args, $url );
				}
			}
		}

		if ( 'success' === $result ) {
			$this->_add_note_to_subscriber( $email, $url );
		} else if ( false !== stripos( $result, 'has signed up to a lot of lists ' ) ) {
			// return message which can be translated. Generic Mailchimp messages are not translatable.
			$result = esc_html__( 'You have signed up to a lot of lists very recently, please try again later', 'et_core' );
		} else {
			$result = $err;
		}

		return $result;
	}
}

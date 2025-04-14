<?php

/**
 * Wrapper for MailPoet's API.
 *
 * @since   3.0.76
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailPoet3 extends ET_Core_API_Email_Provider {

	public static $PLUGIN_REQUIRED;

	/**
	 * @inheritDoc
	 */
	public $name = 'MailPoet';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailpoet';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		if ( null === self::$PLUGIN_REQUIRED ) {
			self::$PLUGIN_REQUIRED = esc_html__( 'MailPoet plugin is either not installed or not activated.', 'et_core' );
		}
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		static $processed = null;

		if ( is_null( $processed ) ) {
			$fields    = \MailPoet\API\API::MP( 'v1' )->getSubscriberFields();
			$processed = array();

			foreach ( $fields as $field ) {
				$field_id   = $field['id'];
				$field_name = $field['name'];

				if ( in_array( $field_id, array( 'email', 'first_name', 'last_name' ) ) ) {
					continue;
				}

				$processed[ $field_id ] = array(
					'field_id' => $field_id,
					'name'     => $field_name,
					'type'     => 'any',
				);
			}
		}

		return $processed;
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$custom_fields = $args['custom_fields'];

		$required_fields = $this->_get_required_custom_fields();

		// Filter missing required custom field.
		$missing_fields = array_diff( $required_fields, array_keys( $custom_fields ) );

		// Fill-up missing required custom fields with dummy content and merge with available ones.
		// This is required because of how MailPoet API validates the custom fields currently
		// and there's no filters available to alter this behavior right now.
		$fields = array_merge( $custom_fields, array_fill_keys( $missing_fields, 'n/a' ) );

		unset( $args['custom_fields'] );

		foreach ( $fields as $field_id => $value ) {
			if ( is_array( $value ) && $value ) {
				// This is a multiple choice field (eg. checkbox, radio, select)
				$value = array_keys( $value );

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
	 * Get required custom fields.
	 *
	 * @return $required_fields
	 */
	protected function _get_required_custom_fields() {
		$fields          = \MailPoet\API\API::MP( 'v1' )->getSubscriberFields();
		$required_fields = [];

		foreach ( $fields as $field ) {
			// Custom field ids have `cf_` prefix. Also we need only the required ones.
			if ( false !== strpos( $field['id'], 'cf_' ) && $field['params']['required'] ) {
				$required_fields[] = $field['id'];
			}
		}

		return $required_fields;
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'       => array(
				'list_id' => 'id',
				'name'    => 'name',
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
		if ( ! class_exists( '\MailPoet\API\API' ) ) {
			return self::$PLUGIN_REQUIRED;
		}

		$data = \MailPoet\API\API::MP( 'v1' )->getLists();

		if ( ! empty( $data ) ) {
			$this->data['lists'] = $this->_process_subscriber_lists( $data );

			$list                        = is_array( $data ) ? array_shift( $data ) : array();
			$this->data['custom_fields'] = $this->_fetch_custom_fields( '', $list );
		}

		$this->data['is_authorized'] = true;

		$this->save_data();

		return array( 'success' => $this->data );
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( ! class_exists( '\MailPoet\API\API' ) ) {
			ET_Core_Logger::error( self::$PLUGIN_REQUIRED );

			return esc_html__( 'An error occurred. Please try again later.', 'et_core' );
		}

		$subscriber      = array();
		$args            = et_core_sanitized_previously( $args );
		$subscriber_data = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$subscriber_data = $this->_process_custom_fields( $subscriber_data );
		$subscriber_data = self::$_->array_flatten( $subscriber_data );
		$result          = 'success';
		$lists           = array( $args['list_id'] );

		unset( $subscriber_data['custom_fields'] );

		/**
		 * Check if the subscriber with this email already exists.
		 */
		$subscriber = \MailPoet\Models\Subscriber::findOne( $subscriber_data['email'] );

		if ( $subscriber ) {
			$subscriber = $subscriber->withCustomFields()->withSubscriptions()->asArray();
		}
		/**
		 * If subscriber is not found, add as a new subscriber. Otherwise, add existing subscriber to the lists.
		 */
		if ( empty( $subscriber ) ) {
			try {
				\MailPoet\API\API::MP( 'v1' )->addSubscriber( $subscriber_data, $lists );
			} catch ( Exception $exception ) {
				$result = $exception->getMessage();
			}
		} else {
			try {
				\MailPoet\API\API::MP( 'v1' )->subscribeToLists( $subscriber['id'], $lists );
			} catch ( Exception $exception ) {
				$result = $exception->getMessage();
			}
		}

		return $result;
	}
}

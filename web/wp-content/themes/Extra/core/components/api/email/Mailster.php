<?php

/**
 * Wrapper for integration with Mailster plugin.
 *
 * @license
 * Copyright © 2017 Elegant Themes, Inc.
 * Copyright © 2017 Xaver Birsak
 *
 * @since   1.1.0
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Mailster extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $name = 'Mailster';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailster';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	/**
	 * Creates a referrer string that includes the name of the opt-in used to subscribe.
	 *
	 * @param array $args The args array that was passed to {@link self::subscribe()}
	 *
	 * @return string
	 */
	protected function _get_referrer( $args ) {
		$optin_name = '';
		$owner      = ucfirst( $this->owner );

		if ( 'bloom' === $this->owner && isset( $args['optin_id'] ) ) {
			$optin_form = ET_Bloom::get_this()->dashboard_options[ $args['optin_id'] ];
			$optin_name = $optin_form['optin_name'];
		}

		return sprintf( '%1$s %2$s "%3$s" on %4$s',
			esc_html( $owner ),
			esc_html__( 'Opt-in', 'et_core' ),
			esc_html( $optin_name ),
			wp_get_referer()
		);
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		static $fields = null;

		if ( is_null( $fields ) ) {
			$customfields = mailster()->get_custom_fields();

			if ( empty( $customfields ) ) {
				return $fields;
			}
			
			$field_types = self::$_->array_get( $this->data_keys, 'custom_field_type' );
			foreach ( $customfields as $field_id => $field ) {
				$field             = $this->transform_data_to_our_format( $field, 'custom_field' );
				$type              = self::$_->array_get( $field, 'type', 'any' );
				$field['field_id'] = $field_id;

				if ( $field_types && ! isset( $field_types[ $type ] ) ) {
					// Unsupported field type. Make it 'text' instead.
					$type = 'textfield';
				}
				$field['type'] = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'input' );

				$fields[ $field_id ] = $field;
			}
		}

		return $fields;
	}

	protected function _process_custom_fields( $args ) {
		if ( ! isset( $args['custom_fields'] ) ) {
			return $args;
		}

		$fields = $args['custom_fields'];

		unset( $args['custom_fields'] );
		$registered_custom_fields = self::$_->array_get( $this->data, 'custom_fields', array() );
		foreach ( $fields as $field_id => $value ) {
			$field_type = isset( $registered_custom_fields[ $field_id ] ) && isset( $registered_custom_fields[ $field_id ]['type'] ) ? $registered_custom_fields[ $field_id ]['type'] : false;
			// Mailster doesn't support multiple checkboxes and if it appears here that means the checkbox is checked
			if ( 'checkbox' === $field_type ) {
				$value = 1;
			}

			if ( is_array( $value ) && $value ) {
				// This is a multiple choice field (eg. radio, select)
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
		return array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'       => array(
				'list_id'           => 'ID',
				'name'              => 'name',
				'subscribers_count' => 'subscribers',
			),
			'subscriber' => array(
				'dbl_optin'     => 'status',
				'email'         => 'email',
				'last_name'     => 'lastname',
				'name'          => 'firstname',
				'custom_fields' => 'custom_fields',
			),
			'custom_field'      => array(
				'name'     => 'name',
				'type'     => 'type',
				'options'  => 'values',
				'hidden'   => 'hidden',
			),
			'custom_field_type' => array(
				// Us => Them
				'textarea'   => 'textarea',
				'radio'      => 'radio',
				'checkbox'   => 'checkbox',
				'input'      => 'textfield',
				'select'     => 'dropdown',
				// Them => Us
				'textfield'  => 'input',
				'dropdown'   => 'select',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	public function fetch_subscriber_lists() {
		if ( ! function_exists( 'mailster' ) ) {
			return esc_html__( 'Mailster Newsletter Plugin is not enabled!', 'et_core' );
		}

		$lists         = mailster( 'lists' )->get( null, null, true );
		$error_message = esc_html__( 'No lists were found. Please create a Mailster list first!', 'et_core' );

		if ( $lists ) {
			$error_message               = 'success';
			$this->data['lists']         = $this->_process_subscriber_lists( $lists );
			$this->data['is_authorized'] = true;
			$this->data['custom_fields'] = $this->_fetch_custom_fields();
			$this->save_data();
		}

		return $error_message;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$error = esc_html__( 'An error occurred. Please try again later.', 'et_core' );

		if ( ! function_exists( 'mailster' ) ) {
			return $error;
		}

		$params       = $this->transform_data_to_provider_format( $args, 'subscriber', array( 'dbl_optin' ) );
		$params       = $this->_process_custom_fields( $params );
		$extra_params = array(
			'status'   => 'disable' === $args['dbl_optin'] ? 1 : 0,
			'referrer' => $this->_get_referrer( $args ),
		);

		$params        = array_merge( $params, $extra_params );
		$subscriber_id = mailster( 'subscribers' )->merge( $params );

		if ( is_wp_error( $subscriber_id ) ) {
			$result = htmlspecialchars_decode( $subscriber_id->get_error_message() );
		} else if ( mailster( 'subscribers' )->assign_lists( $subscriber_id, $args['list_id'], false ) ) {
			$result = 'success';
		} else {
			$result = $error;
		}

		return $result;
	}
}

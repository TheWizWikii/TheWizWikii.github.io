<?php

/**
 * Wrapper for SalesForce's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_SalesForce extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $ACCESS_TOKEN_URL = 'https://login.salesforce.com/services/oauth2/token';

	/**
	 * @inheritDoc
	 */
	public $AUTHORIZATION_URL = 'https://login.salesforce.com/services/oauth2/authorize';

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = '';

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'SalesForce';

	/**
	 * @inheritDoc
	 */
	public $slug = 'salesforce';

	/**
	 * @inheritDoc
	 */
	public $oauth_version = '2.0';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = true;

	/**
	 * ET_Core_API_SalesForce constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner, $account_name = '' ) {
		parent::__construct( $owner, $account_name );

		if ( 'builder' === $owner ) {
			$this->REDIRECT_URL = add_query_arg( 'et-core-api-email-auth', 1, home_url( '', 'https' ) ); // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- No need to change prop name.
		} else {
			$this->REDIRECT_URL = admin_url( 'admin.php?page=et_bloom_options', 'https' ); // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- No need to change prop name.
		}

		$this->_set_base_url();
	}

	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {
		static $fields = null;

		$this->response_data_key = 'fields';

		$this->prepare_request( "{$this->BASE_URL}/services/data/v39.0/sobjects/Lead/describe" );

		if ( is_null( $fields ) ) {
			$fields = parent::_fetch_custom_fields( $list_id, $list );

			foreach ( $fields as $index => $field ) {
				if ( ! isset( $field['custom'] ) || ! $field['custom'] ) {
					unset( $fields[ $index ] );
				}
			}
		}

		return $fields;
	}

	/**
	 * @return string
	 */
	protected function _fetch_subscriber_lists() {
		$query = urlencode( 'SELECT Id, Name, NumberOfLeads from Campaign LIMIT 100' );
		$url   = "{$this->BASE_URL}/services/data/v39.0/query?q={$query}";

		$this->response_data_key = 'records';

		$this->prepare_request( $url );

		return parent::fetch_subscriber_lists();
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
					$value = implode( ';', $value );
				} else {
					$value = array_pop( $value );
				}
			}

			self::$_->array_set( $args, "custom_fields.{$field_id}", $value );
		}

		return $args;
	}

	public function _set_base_url() {
		// If we already have the `instance_url`, use it as the base API url.
		if ( isset( $this->data['instance_url'] ) && ! empty( $this->data['instance_url'] ) ) {
			$this->BASE_URL = untrailingslashit( $this->data['instance_url'] ); // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- No need to change prop name.
		} else {
			$this->BASE_URL = empty( $this->data['login_url'] ) ? '' : untrailingslashit( $this->data['login_url'] ); // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- No need to change prop name.
		}
	}

	public function authenticate() {
		$this->data['consumer_secret'] = $this->data['client_secret'];
		$this->data['consumer_key']    = $this->data['api_key'];

		return parent::authenticate();
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			// SalesForce supports OAuth for SSL websites so generate different fields in this case
			'login_url'       => array(
				'label'    => esc_html__( 'Instance URL', 'et_core' ),
				'required' => 'https',
				'show_if'  => array( 'function.protocol' => 'https' ),
			),
			'api_key'         => array(
				'label'    => esc_html__( 'Consumer Key', 'et_core' ),
				'required' => 'https',
				'show_if'  => array( 'function.protocol' => 'https' ),
			),
			'client_secret'   => array(
				'label'    => esc_html__( 'Consumer Secret', 'et_core' ),
				'required' => 'https',
				'show_if'  => array( 'function.protocol' => 'https' ),
			),
			// This has to be the last field because is the only one shown in both cases and
			// CANCEL / SUBMIT buttons will be attached to it.
			'organization_id' => array(
				'label'        => esc_html__( 'Organization ID', 'et_core' ),
				'required' => 'http',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		$this->_set_base_url();
		// SalesForce supports 2 types of authentication: Simple and OAuth2
		if ( isset( $this->data['api_key'], $this->data['client_secret'] ) && ! empty( $this->data['api_key'] ) && ! empty( $this->data['client_secret'] ) ) {

			// Fetch lists if user already authenticated.
			if ( $this->is_authenticated() ) {
				return $this->_fetch_subscriber_lists();
			}

			$authenticated = $this->authenticate();
			// If the authenticating process returns an array with redirect url to complete OAuth authorization.
			if ( is_array( $authenticated ) ) {
				return $authenticated;
			}

			if ( true === $authenticated ) {
				// Need to reinitialize the OAuthHelper with the new data, to set the authorization header in the next request.
				$urls               = array(
					'access_token_url'  => $this->ACCESS_TOKEN_URL, // @phpcs:ignore -- No need to change the class property
					'request_token_url' => $this->REQUEST_TOKEN_URL, // @phpcs:ignore -- No need to change the class property
					'authorization_url' => $this->AUTHORIZATION_URL, // @phpcs:ignore -- No need to change the class property
					'redirect_url'      => $this->REDIRECT_URL, // @phpcs:ignore -- No need to change the class property
				);
				$this->OAuth_Helper = new ET_Core_API_OAuthHelper( $this->data, $urls, $this->owner ); // @phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- No need to change the prop name.
				return $this->_fetch_subscriber_lists();
			}

			return false;
		} elseif ( isset( $this->data['organization_id'] ) && '' !== $this->data['organization_id'] ) {
			// Simple
			$this->data['is_authorized'] = 'true';
			$this->data['lists']         = array( array( 'list_id' => 0, 'name' => 'WebToLead', 'subscribers_count' => 0 ) );

			$this->save_data();

			// return 'success' immediately in case of simple authentication. Lists cannot be retrieved with this type.
			return 'success';
		} else {
			return esc_html__( 'Organization ID cannot be empty', 'et_core' );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'              => array(
				'list_id'           => 'Id',
				'name'              => 'Name',
				'subscribers_count' => 'NumberOfLeads',
			),
			'subscriber'        => array(
				'name'          => 'FirstName',
				'last_name'     => 'LastName',
				'email'         => 'Email',
				'custom_fields' => 'custom_fields',
			),
			'custom_field'      => array(
				'field_id' => 'name',
				'name'     => 'label',
				'type'     => 'type',
				'options'  => 'valueSet',
			),
			'custom_field_type' => array(
				// Us => Them
				'input'               => 'Text',
				'textarea'            => 'TextArea',
				'checkbox'            => 'MultiselectPicklist',
				'select'              => 'Picklist',
				// Them => Us
				'Text'                => 'input',
				'TextArea'            => 'textarea',
				'MultiselectPicklist' => 'checkbox',
				'Picklist'            => 'select',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	public function get_subscriber( $email ) {
		$query = urlencode( "SELECT Id from Lead where Email='{$email}' LIMIT 100" );
		$url   = "{$this->BASE_URL}/services/data/v39.0/query?q={$query}";

		$this->response_data_key = 'records';

		$this->prepare_request( $url );
		$this->make_remote_request();

		$response = $this->response;

		if ( $response->ERROR || empty( $response->DATA['records'] ) ) {
			return false;
		}

		return isset( $response->DATA['records'][0]['Id'] ) ? $response->DATA['records'][0]['Id'] : false;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( empty( $this->data['access_secret'] ) ) {
			// Try to use simple web form
			return $this->subscribe_salesforce_web( $args );
		}

		$error_message = esc_html__( 'An error occurred. Please try again.', 'et_core' );
		$subscriber_id = $this->get_subscriber( $args['email'] );

		if ( ! $subscriber_id ) {
			$url                = "{$this->BASE_URL}/services/data/v39.0/sobjects/Lead";
			$content            = $this->transform_data_to_provider_format( $args, 'subscriber' );
			$content            = $this->_process_custom_fields( $content );
			$content['Company'] = 'Bloom';
			if ( isset( $content['custom_fields'] ) && is_array( $content['custom_fields'] ) ) {
				$content = array_merge( $content, $content['custom_fields'] );
				unset( $content['custom_fields'] );
			}

			// The LastName is required by Salesforce, whereas it is possible for Optin Form to not have the last name field.
			if ( ! isset( $content['LastName'] ) || empty( $content['LastName'] ) ) {
				$content['LastName'] = '[not provided]';
			}

			$this->prepare_request( $url, 'POST', false, json_encode( $content ), true );

			$this->response_data_key = false;

			$result = parent::subscribe( $content, $url );

			if ( 'success' !== $result || empty( $this->response->DATA['id'] ) ) {
				return $error_message;
			}

			$subscriber_id = $this->response->DATA['id'];
		}

		$url     = "{$this->BASE_URL}/services/data/v39.0/sobjects/CampaignMember";
		$content = array(
			'LeadId'     => $subscriber_id,
			'CampaignId' => $args['list_id'],
		);

		$this->prepare_request( $url, 'POST', false, json_encode( $content ), true );

		$result = parent::subscribe( $content, $url );

		if ( 'success' !== $result && ! empty( $this->response->DATA['errors'] ) ) {
			return $this->response->DATA['errors'][0];
		} else if ( 'success' !== $result ) {
			return $error_message;
		}

		return 'success';
	}

	/**
	 * Post web-to-lead request to SalesForce
	 *
	 * @return string
	 */
	public function subscribe_salesforce_web( $args ) {
		if ( ! isset( $this->data['organization_id'] ) || '' === $this->data['organization_id'] ) {
			return esc_html__( 'Unknown Organization ID', 'et_core' );
		}

		// Define SalesForce web-to-lead endpoint
		$url  = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
		$args = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$args = $this->_process_custom_fields( $args );

		// Prepare arguments for web-to-lead POST
		$form_args = array(
			'body' => array(
				'oid'    => sanitize_text_field( $this->data['organization_id'] ),
				'retURL' => esc_url( home_url( '/' ) ),
				'email'  => sanitize_email( $args['Email'] ),
			),
		);

		if ( '' !== $args['FirstName'] ) {
			$form_args['body']['first_name'] = sanitize_text_field( $args['FirstName'] );
		}

		if ( '' !== $args['LastName'] ) {
			$form_args['body']['last_name'] = sanitize_text_field( $args['LastName'] );
		}

		if ( isset( $args['custom_fields'] ) && is_array( $args['custom_fields'] ) ) {
			$form_args = array_merge( $form_args, $args['custom_fields'] );
		}

		// Post to SalesForce web-to-lead endpoint
		$request = wp_remote_post( $url, $form_args );

		if ( ! is_wp_error( $request ) && 200 === wp_remote_retrieve_response_code( $request ) ) {
			return 'success';
		}

		return esc_html__( 'An error occurred. Please try again.', 'et_core' );
	}
}

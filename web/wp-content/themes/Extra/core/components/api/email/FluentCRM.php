<?php
/**
 * ET_Core_API_Email_FluentCRM class file.
 *
 * @class   ET_Core_API_Email_FluentCRM
 * @package ET\Core\API\Email
 */

// phpcs:disable Squiz.Commenting.VariableComment.MissingVar -- All the class level variables here inherit parent inline documentation. Please check ET_Core_API_Email_Provider or ET_Core_API_Service class.
// phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag -- Almost all the methods here inherit parent inline documentation. Please check ET_Core_API_Email_Provider or ET_Core_API_Service class.

/**
 * Wrapper for FluentCRM's API.
 *
 * @since 4.9.1
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_FluentCRM extends ET_Core_API_Email_Provider {

	/**
	 * Warning message text if the required plugin doesn't exist.
	 *
	 * @var boolean
	 */
	public static $PLUGIN_REQUIRED; // phpcs:ignore ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- Widely used on all email provider classes.

	/**
	 * {@inheritdoc}
	 */
	public $name = 'FluentCRM';

	/**
	 * {@inheritdoc}
	 */
	public $slug = 'fluentcrm';

	/**
	 * {@inheritdoc}
	 */
	public $custom_fields = 'predefined';

	/**
	 * {@inheritdoc}
	 */
	public $custom_fields_scope = 'account';

	/**
	 * ET_Core_API_Email_FluentCRM constructor.
	 */
	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		if ( null === self::$PLUGIN_REQUIRED ) { // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Widely used on all email provider classes.
			self::$PLUGIN_REQUIRED = esc_html__( 'FluentCRM plugin is either not installed or not activated.', 'et_core' ); // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Widely used on all email provider classes.
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data_keymap( $keymap = array() ) {
		$keymap = array(
			'list'                => array(
				'list_id' => 'id',
				'name'    => 'title',
			),
			'subscriber'          => array(
				'email'         => 'email',
				'last_name'     => 'last_name',
				'name'          => 'first_name',
				'custom_fields' => 'custom_fields',
			),
			'custom_field'        => array(
				'field_id' => 'slug',
				'name'     => 'label',
				'type'     => 'type',
				'hidden'   => 'hidden',
				'options'  => 'options',
			),
			'custom_field_option' => array(
				'id'   => 'value',
				'name' => 'value',
			),
			'custom_field_type'   => array(
				// Us <=> Them.
				'radio'        => 'radio',
				'checkbox'     => 'checkbox',
				// Us => Them.
				'input'        => 'text',
				'select'       => 'select-one',
				// Them => Us.
				'text'         => 'input',
				'number'       => 'input',
				'date'         => 'input',
				'date_time'    => 'input',
				'select-one'   => 'select',
				'select-multi' => 'checkbox',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * {@inheritdoc}
	 *
	 * FluentCRM is self hosted and all the fields can be managed on the plugin itself.
	 */
	protected function _fetch_custom_fields( $list_id = '', $list = array() ) {

		static $processed = null;

		if ( is_null( $processed ) ) {
			// A. Default custom fields.
			$processed = array(
				'address_line_1' => array(
					'field_id' => 'address_line_1',
					'name'     => __( 'Address Line 1', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'address_line_2' => array(
					'field_id' => 'address_line_2',
					'name'     => __( 'Address Line 2', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'postal_code'    => array(
					'field_id' => 'postal_code',
					'name'     => __( 'Postal Code', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'city'           => array(
					'field_id' => 'city',
					'name'     => __( 'City', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'state'          => array(
					'field_id' => 'state',
					'name'     => __( 'State', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'country'        => array(
					'field_id' => 'country',
					'name'     => __( 'Country', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'phone'          => array(
					'field_id' => 'phone',
					'name'     => __( 'Phone', 'et_builder' ),
					'type'     => 'input',
					'hidden'   => false,
				),
				'status'         => array(
					'field_id'      => 'status',
					'name'          => __( 'Enable Double Optin', 'et_builder' ),
					'type'          => 'input',
					'required_mark' => 'off',
					'default'       => 'pending',
					'hidden'        => true,
				),
			);

			// B. Contact custom fields.
			$contact_custom_fields = fluentcrm_get_option( 'contact_custom_fields', array() );

			if ( ! empty( $contact_custom_fields ) ) {
				foreach ( $contact_custom_fields as $field ) {
					// 1. Transform field data to fit our format.
					$field    = $this->transform_data_to_our_format( $field, 'custom_field' );
					$field_id = $field['field_id'];
					$type     = $field['type'];

					// 2. Transform field type to fit our supported field type.
					$field['type'] = self::$_->array_get( $this->data_keys, "custom_field_type.{$type}", 'any' );

					if ( 'select-multi' === $type ) {
						$field['type_origin'] = $type;
					}

					// 3. Transform `options` data to fit our format (`id` => `name`).
					if ( ! empty( $field['options'] ) ) {
						$options = array();

						foreach ( $field['options'] as $option ) {
							$option = array( 'value' => $option );
							$option = $this->transform_data_to_our_format( $option, 'custom_field_option' );

							$options[ $option['id'] ] = $option['name'];
						}

						$field['options'] = $options;
					}

					$processed[ $field_id ] = $field;
				}
			}
		}

		return $processed;
	}

	/**
	 * {@inheritdoc}
	 *
	 * FluentCRM is self hosted and all the fields can be managed on the plugin itself.
	 */
	public function get_account_fields() {
		return array();
	}

	/**
	 * {@inheritdoc}
	 *
	 * FluentCRM is self hosted and all the lists can be managed on the plugin itself.
	 */
	public function fetch_subscriber_lists() {
		if ( ! defined( 'FLUENTCRM' ) ) {
			return self::$PLUGIN_REQUIRED; // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Widely used on all email provider classes.
		}

		$list_tags = array();

		// Lists.
		$lists = FluentCrmApi( 'lists' )->all();

		foreach ( $lists as $list ) {
			$list_tags[ 'list_' . $list->id ] = array(
				'list_id'           => 'list_' . $list->id,
				'name'              => 'List: ' . $list->title,
				'subscribers_count' => 0,
			);
		}

		// Tags.
		$tags = FluentCrmApi( 'tags' )->all();

		foreach ( $tags as $tag ) {
			$list_tags[ 'tags_' . $tag->id ] = array(
				'list_id'           => 'tags_' . $tag->id,
				'name'              => 'Tag: ' . $tag->title,
				'subscribers_count' => 0,
			);
		}

		// Combine both of lists and tags because there is no other way to display both
		// with different fields.
		$this->data['lists']         = $list_tags;
		$this->data['custom_fields'] = $this->_fetch_custom_fields( '', array() );
		$this->data['is_authorized'] = true;

		$this->save_data();

		return 'success';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _process_custom_fields( $args ) {
		if ( ! empty( $args['custom_fields'] ) ) {
			$custom_fields = $args['custom_fields'];

			foreach ( $custom_fields as $field_id => $field_value ) {
				if ( $field_value ) {
					$field_type        = self::$_->array_get( $this->data['custom_fields'][ $field_id ], 'type' );
					$field_type_origin = self::$_->array_get( $this->data['custom_fields'][ $field_id ], 'type_origin' );

					// Transform `checkbox` value into string separated by comma i.e. 'val1,val2'.
					// However, if the field type origin is `select-multi`, we have to use array
					// values instead.
					if ( 'checkbox' === $field_type ) {
						$field_value = 'select-multi' === $field_type_origin ? array_values( $field_value ) : implode( ',', $field_value );
					}

					$args[ $field_id ] = $field_value;
				}
			}

			if ( isset( $custom_fields['status'] ) ) {
				$args['status'] = 'pending';
			}

			unset( $args['custom_fields'] );
		}

		return $args;
	}

	/**
	 * {@inheritdoc}
	 */
	public function subscribe( $args, $url = '' ) {
		if ( ! defined( 'FLUENTCRM' ) ) {
			ET_Core_Logger::error( self::$PLUGIN_REQUIRED ); // phpcs:ignore ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- Widely used on all email provider classes.
			return esc_html__( 'An error occurred. Please try again later.', 'et_core' );
		}

		$contact = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$contact = $this->_process_custom_fields( $contact );

		// IP Address.
		$ip_address            = 'true' === self::$_->array_get( $args, 'ip_address', 'true' ) ? et_core_get_ip_address() : '';
		$contact['ip_address'] = $ip_address;

		// Name.
		if ( empty( $contact['last_name'] ) ) {
			$contact['full_name'] = $contact['first_name'];
			unset( $contact['first_name'] );
			unset( $contact['last_name'] );
		}

		// List or Tag.
		$list_or_tag_id = $args['list_id'];

		if ( false === strpos( $list_or_tag_id, 'tags_' ) ) {
			$contact['lists'] = array( intval( str_replace( 'list_', '', $list_or_tag_id ) ) );
		} else {
			$contact['tags'] = array( intval( str_replace( 'tags_', '', $list_or_tag_id ) ) );
		}

		$contact = array_filter( $contact );

		// Email.
		if ( empty( $contact['email'] ) || ! is_email( $contact['email'] ) ) {
			return esc_html__( 'Email Validation has been failed.', 'et_core' );
		}

		FluentCrmApi( 'contacts' )->createOrUpdate( $contact );

		return 'success';
	}
}

<?php

abstract class ET_Builder_Module_Type_WithSpamProtection extends ET_Builder_Module {

	/**
	 * Spam protection providers class instance
	 *
	 * @since 4.0.7
	 *
	 * @var ET_Core_API_Spam_Providers
	 */
	protected static $_spam_providers;

	/**
	 * Enabled spam protection providers
	 *
	 * @since 4.0.7
	 *
	 * @var string[]
	 */
	public static $enabled_spam_providers;

	/**
	 * Shortcode attributes array checksum.
	 *
	 * @since 4.4.9
	 *
	 * @var
	 */
	protected $_checksum;

	/**
	 * Get module settings fields for spam protection providers
	 *
	 * @since 4.0.7
	 *
	 * @param $provider_slug
	 *
	 * @return array
	 */
	public static function _get_spam_account_fields( $provider_slug ) {
		$fields  = self::spam_providers()->account_fields( $provider_slug );
		$is_BB   = et_builder_is_loading_data( 'bb' );
		$show_if = $is_BB ? 'manage|add_new_account' : 'add_new_account';

		$account_name_key = $provider_slug . '_account_name';
		$account_key      = $provider_slug . '_list';
		$description_text = esc_html__( 'Anti-Spam Provider Account Setup Documentation', 'et_builder' );

		if ( $fields ) {
			$field_ids     = array_keys( $fields );
			$last_field_id = "{$provider_slug}_" . array_pop( $field_ids );
		} else {
			$last_field_id = $account_name_key;
		}

		$buttons = array(
			'option_class' => 'et-pb-option-group--last-field',
			'after'        => array(
				array(
					'type'  => 'button',
					'class' => 'et_pb_email_cancel',
					'text'  => et_builder_i18n( 'Cancel' ),
				),
				array(
					'type'  => 'button',
					'class' => 'et_pb_email_submit',
					'text'  => esc_html__( 'Submit', 'et_builder' ),
				),
			),
		);

		$account_fields[ $account_name_key ] = array(
			'name'            => 'account_name',
			'label'           => esc_html__( 'Account Name', 'et_builder' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'A name to associate with the account when displayed in the account select field.', 'et_builder' ),
			'show_if'         => array(
				$account_key => $show_if,
			),
			'class'           => "et_pb_email_{$provider_slug}_account_name",
			'toggle_slug'     => 'spam',
			'default'         => 'Default',
		);

		foreach ( $fields as $field_id => $field_info ) {
			$field_id = "{$provider_slug}_{$field_id}";

			$show_if_conditions = array(
				$account_key => $show_if,
			);

			if ( isset( $field_info['show_if'] ) ) {
				$show_if_conditions = array_merge( $show_if_conditions, $field_info['show_if'] );
			}

			$account_fields[ $field_id ] = array(
				'name'            => $field_id,
				'label'           => et_core_esc_previously( $field_info['label'] ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => sprintf( '<a target="_blank" href="https://www.elegantthemes.com/documentation/divi/spam-protection-providers#%1$s">%2$s</a>', $provider_slug, $description_text ),
				'show_if'         => $show_if_conditions,
				'class'           => 'et_pb_email_' . $field_id,
				'toggle_slug'     => 'spam',
			);
		}

		$account_fields[ $last_field_id ] = array_merge( $account_fields[ $last_field_id ], $buttons );

		return $account_fields;
	}

	/**
	 * Returns the definitions of the options for the select_with_option_groups field used for selecting provider accounts.
	 *
	 * @since 4.0.7
	 *
	 * @return array[] $accounts {
	 *     Spam Accounts Options Fields
	 *
	 *     @type string[] {
	 *       Spam Account Option Field
	 *
	 *         @type string $account_slug Account display name
	 *     }
	 *     ...
	 * }
	 */
	public static function _get_spam_accounts() {
		$all_accounts = self::spam_providers()->accounts();
		$result       = array();

		foreach ( $all_accounts as $provider_slug => $accounts ) {
			if ( ! array_key_exists( $provider_slug, self::$enabled_spam_providers ) ) {
				continue;
			}

			$result[ $provider_slug ] = array(
				'0' => array( 'none' => esc_html__( 'Select an account', 'et_builder' ) ),
			);

			foreach ( $accounts as $account_name => $account_details ) {
				if ( ! isset( $result[ $provider_slug ][ $account_name ] ) ) {
					$result[ $provider_slug ][ $account_name ] = array();
				}

				$index = count( $result[ $provider_slug ][ $account_name ] );
				$result[ $provider_slug ][ $account_name ][ "{$account_name}-{$index}" ] = esc_html( $account_name );
			}

			$result[ $provider_slug ]['manage'] = array(
				'add_new_account' => '',
				'remove_account'  => '',
			);
		}

		return $result;
	}

	/**
	 * Returns the field definitions for all spam provider accounts.
	 *
	 * @since 4.0.7
	 *
	 * @return array
	 */
	public static function _get_spam_provider_fields() {
		$fields = array(
			'use_spam_service' => array(
				'label'           => esc_html__( 'Use A Spam Protection Service', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'toggle_slug'     => 'spam',
				'description'     => esc_html__( 'Whether or not to use a 3rd-party spam protection service like Google reCAPTCHA.', 'et_builder' ),
				'default'         => 'off',
			),
			'spam_provider'    => array(
				'label'           => esc_html__( 'Service Provider', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => self::$enabled_spam_providers,
				'description'     => esc_html__( 'Choose a service provider.', 'et_builder' ),
				'toggle_slug'     => 'spam',
				'default'         => 'recaptcha',
				'show_if'         => array(
					'use_spam_service' => 'on',
				),
			),
		);

		$accounts = self::_get_spam_accounts();

		foreach ( self::$enabled_spam_providers as $provider_slug => $provider_name ) {
			$max_accounts = null;
			$no_accounts  = array(
				'0'      => array( 'none' => esc_html__( 'Select an account', 'et_builder' ) ),
				'manage' => array(
					'add_new_account' => '',
					'remove_account'  => '',
					'fetch_lists'     => '',
				),
			);

			if ( 'ReCaptcha' === $provider_name ) {
				$provider_name = 'reCAPTCHA v3';
				$max_accounts  = 1;
			}

			$fields[ $provider_slug . '_list' ] = array(
				'label'           => sprintf( esc_html__( '%s Account', 'et_builder' ), $provider_name ),
				'type'            => 'select_with_option_groups',
				'option_category' => 'basic_option',
				'options'         => isset( $accounts[ $provider_slug ] ) ? $accounts[ $provider_slug ] : $no_accounts,
				'description'     => esc_html__( 'Choose an account or click "Add" to add a new account.', 'et_builder' ),
				'show_if'         => array(
					'spam_provider'    => $provider_slug,
					'use_spam_service' => 'on',
				),
				'default'         => '0|none',
				'max_accounts'    => $max_accounts,
				'toggle_slug'     => 'spam',
				'after'           => array(
					array(
						'type'  => 'button',
						'class' => 'et_pb_email_add_account',
						'text'  => esc_html__( 'Add', 'et_builder' ),
					),
					array(
						'type'       => 'button',
						'class'      => 'et_pb_email_remove_account',
						'text'       => esc_html__( 'Remove', 'et_builder' ),
						'attributes' => array(
							'data-confirm_text' => esc_attr__( 'Confirm', 'et_builder' ),
							'data-cancel_text'  => esc_attr__( 'Cancel', 'et_builder' ),
						),
					),
				),
				'attributes'      => array(
					'data-confirm_remove_text'     => esc_attr__( 'The following account will be removed:', 'et_builder' ),
					'data-adding_new_account_text' => esc_attr__( 'Use the fields below to add a new account.', 'et_builder' ),
				),
			);

			$account_fields = is_admin() || et_builder_should_load_all_data() ? self::_get_spam_account_fields( $provider_slug ) : array();
			$fields         = array_merge( $fields, $account_fields );
		}

		$fields['recaptcha_min_score'] = array(
			'label'           => esc_html__( 'Minimum Score', 'et_builder' ),
			'type'            => 'range',
			'option_category' => 'configuration',
			'validate_unit'   => false,
			'range_settings'  => array(
				'min'  => '0',
				'max'  => '1',
				'step' => '0.1',
			),
			'toggle_slug'     => 'spam',
			'description'     => esc_html__( 'reCAPTCHA v3 returns a score between 0 and 1 where 1 is very likely a good interaction, and 0 is very likely a bot. With this setting you can choose the minimum score that should be considered a good interaction.', 'et_builder' ),
			'default'         => '0.5',
			'show_if'         => array(
				'spam_provider'    => 'recaptcha',
				'use_spam_service' => 'on',
			),
		);

		return $fields;
	}

	/**
	 * Gets spam providers class instance.
	 *
	 * @since 4.0.7
	 *
	 * @return ET_Core_API_Spam_Providers
	 */
	public static function spam_providers() {
		if ( null === self::$_spam_providers ) {
			self::$_spam_providers = ET_Core_API_Spam_Providers::instance();
		}

		return self::$_spam_providers;
	}

	/**
	 * Whether or not the form submitted in the current request is spam.
	 *
	 * @since 4.0.7
	 *
	 * @return bool
	 */
	public function is_spam_submission() {
		if ( empty( $_POST['token'] ) ) {
			return true;
		}

		$provider = $this->prop( 'spam_provider' );
		$account  = $this->prop( "{$provider}_list" );

		if ( et_()->all( array( $provider, $account ) ) ) {
			$service = self::spam_providers()->get( $provider, $account );
		} else {
			$service = et_core_api_spam_find_provider_account();
		}

		if ( ! $service || ! $service->is_enabled() ) {
			return false;
		}

		$result = $service->verify_form_submission();

		if ( is_string( $result ) || ! $result['success'] ) {
			et_error( $result );

			return true;
		}

		$min_score_default = et_()->array_get( $this->get_default_props(), 'recaptcha_min_score' );

		return $result['score'] < (float) $this->prop( 'recaptcha_min_score', $min_score_default );
	}

	public function init() {
		if ( self::$_spam_providers ) {
			return;
		}

		$spam_providers = self::spam_providers()->names_by_slug();

		/**
		 * Filters the list of enabled anti-spam providers.
		 *
		 * @since 4.0.7
		 *
		 * @param string[] $spam_providers
		 */
		self::$enabled_spam_providers = apply_filters( 'et_builder_module_contact_form_enabled_spam_providers', $spam_providers );

		ksort( self::$enabled_spam_providers );
	}

	/**
	 * Renders the module output.
	 *
	 * @param  array  $attrs       List of attributes.
	 * @param  string $content     Content being processed.
	 * @param  string $render_slug Slug of module that is used for rendering output.
	 */
	public function render( $attrs, $content, $render_slug ) {

		$this->_checksum  = md5( serialize( $attrs ) );
		$use_spam_service = get_option( $this->slug . '_' . $this->_checksum );

		if ( 'on' === $this->prop( 'use_spam_service' ) ) {
			$this->add_classname( 'et_pb_recaptcha_enabled' );

			if ( 'on' !== $use_spam_service ) {
				update_option( $this->slug . '_' . $this->_checksum, 'on' );
			}
		} elseif ( 'off' !== $use_spam_service ) {
			update_option( $this->slug . '_' . $this->_checksum, 'off' );
		}
	}
}

<?php

/**
 * High-level wrapper for interacting with the external API's offered by 3rd-party anti-spam providers.
 *
 * @since 4.0.7
 *
 * @package ET\Core\API\Spam
 */
abstract class ET_Core_API_Spam_Provider extends ET_Core_API_Service  {

	/**
	 * @since 4.0.7
	 *
	 * @inheritDoc
	 */
	public $service_type = 'spam';

	/**
	 * @since 4.0.7
	 *
	 * @inheritDoc
	 */
	protected function _get_data() {
		$options = parent::_get_data();

		// return empty array in case of empty name
		if ( '' === $this->account_name || ! is_string( $this->account_name ) ) {
			return array();
		}

		$provider = sanitize_text_field( $this->slug );
		$account  = sanitize_text_field( $this->account_name );

		if ( ! isset( $options['accounts'][ $provider ][ $account ] ) ) {
			$options['accounts'][ $provider ][ $account ] = array();
			update_option( 'et_core_api_spam_options', $options );
		}

		return $options['accounts'][ $provider ][ $account ];
	}

	/**
	 * Returns whether or not an account exists in the database.
	 *
	 * @since 4.0.7
	 *
	 * @param string $provider
	 * @param string $account_name
	 *
	 * @return bool
	 */
	public static function account_exists( $provider, $account_name ) {
		$all_accounts = self::get_accounts();

		return isset( $all_accounts[ $provider ][ $account_name ] );
	}

	/**
	 * @since 4.0.7
	 *
	 * @inheritDoc
	 */
	public function delete() {
		self::remove_account( $this->slug, $this->account_name );

		$this->account_name = '';

		$this->_get_data();
	}

	/**
	 * Retrieves the email accounts data from the database.
	 *
	 * @since 4.0.7
	 *
	 * @return array
	 */
	public static function get_accounts() {
		$options = (array) get_option( 'et_core_api_spam_options' );

		return isset( $options['accounts'] ) ? $options['accounts'] : array();
	}

	/**
	 * @since 4.0.7
	 *
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		return $keymap;
	}

	abstract public function is_enabled();

	/**
	 * Remove an account
	 *
	 * @since 4.0.7
	 *
	 * @param string $provider
	 * @param string $account_name
	 */
	public static function remove_account( $provider, $account_name ) {
		$options = (array) get_option( 'et_core_api_spam_options' );

		unset( $options['accounts'][ $provider ][ $account_name ] );

		update_option( 'et_core_api_spam_options', $options );
	}

	/**
	 * @since 4.0.7
	 *
	 * @inheritDoc
	 */
	public function save_data() {
		self::update_account( $this->slug, $this->account_name, $this->data );
	}

	/**
	 * @since 4.0.7
	 *
	 * @inheritDoc
	 */
	public function set_account_name( $name ) {
		$this->account_name = $name;
		$this->data = $this->_get_data();
	}

	/**
	 * Remove keys with brackets.
	 *
	 * Fixing my errors is no walk in the park; it's more like a hike up Mt.
	 *
	 * @param array $data Options array.
	 * @return void
	 */
	public static function remove_keys_with_brackets( &$data ) {
		foreach ( $data as $key => &$value ) {
			if ( is_array( $value ) ) {
				self::remove_keys_with_brackets( $value );
			}

			if ( strpos( $key, '[' ) === 0 && strpos( $key, ']' ) === strlen( $key ) - 1 ) {
				unset( $data[ $key ] );
			}
		}
	}

	/**
	 * Updates the data for a provider account.
	 *
	 * @since 4.0.7
	 *
	 * @param string $provider The provider's slug.
	 * @param string $account  The account name.
	 * @param array  $data     The new data for the account.
	 */
	public static function update_account( $provider, $account, $data ) {
		if ( empty( $account ) || empty( $provider ) ) {
			return;
		}

		$options  = (array) get_option( 'et_core_api_spam_options' );
		$provider = sanitize_text_field( $provider );
		$account  = sanitize_text_field( $account );

		self::remove_keys_with_brackets( $options );

		self::$_->array_update( $options, "accounts.$provider.$account", $data );

		update_option( 'et_core_api_spam_options', $options );
	}

	abstract public function verify_form_submission();
}

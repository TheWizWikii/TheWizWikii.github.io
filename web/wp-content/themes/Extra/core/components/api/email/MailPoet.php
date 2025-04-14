<?php

/**
 * Wrapper for MailPoet's API.
 *
 * @since   3.0.76
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailPoet extends ET_Core_API_Email_Provider {

	/**
	 * @var ET_Core_API_Email_Provider
	 */
	private $_MP;

	public static $PLUGIN_REQUIRED;

	/**
	 * @inheritDoc
	 */
	public $custom_fields_scope = 'account';

	/**
	 * @inheritDoc
	 */
	public $name = 'MailPoet';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailpoet';

	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		if ( null === self::$PLUGIN_REQUIRED ) {
			self::$PLUGIN_REQUIRED = esc_html__( 'MailPoet plugin is either not installed or not activated.', 'et_core' );
		}

		$has_php53 = version_compare( PHP_VERSION, '5.3', '>=' );

		if ( $has_php53 && class_exists( '\MailPoet\API\API' ) ) {
			require_once( ET_CORE_PATH . 'components/api/email/_MailPoet3.php' );
			$this->_init_provider_class( '3', $owner, $account_name, $api_key );

		} else if ( class_exists( 'WYSIJA' ) ) {
			require_once( ET_CORE_PATH . 'components/api/email/_MailPoet2.php' );
			$this->_init_provider_class( '2', $owner, $account_name, $api_key );
		}
	}

	/**
	 * Initiate provider class based on the version number.
	 *
	 * @param  string $version      Version number.
	 * @param  string $owner        Owner.
	 * @param  string $account_name Account name.
	 * @param  string $api_key      API key.
	 */
	protected function _init_provider_class( $version, $owner, $account_name, $api_key ) {
		if ( '3' === $version ) {
			$this->_MP = new ET_Core_API_Email_MailPoet3( $owner, $account_name, $api_key );
		} else {
			$this->_MP           = new ET_Core_API_Email_MailPoet2( $owner, $account_name, $api_key );
			$this->custom_fields = false;
		}
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
		if ( $this->_MP ) {
			return $this->_MP->get_data_keymap( $keymap );
		}

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		$lists_data = $this->_MP ? $this->_MP->fetch_subscriber_lists() : self::$PLUGIN_REQUIRED;

		// Update data in Main MailPoet class, so correct lists data can be accessed
		if ( isset( $lists_data['success'] ) ) {
			$this->data = $lists_data['success'];

			$this->save_data();

			return 'success';
		}

		return $lists_data;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		return $this->_MP ? $this->_MP->subscribe( $args, $url ) : self::$PLUGIN_REQUIRED;
	}
}

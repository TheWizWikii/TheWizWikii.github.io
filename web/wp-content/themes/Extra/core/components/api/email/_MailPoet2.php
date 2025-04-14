<?php

/**
 * Wrapper for MailPoet's API.
 *
 * @since   3.0.76
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailPoet2 extends ET_Core_API_Email_Provider {

	public static $PLUGIN_REQUIRED;

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
				'name'      => 'first_name',
				'last_name' => 'last_name',
				'email'     => 'email',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( ! class_exists( 'WYSIJA' ) ) {
			return self::$PLUGIN_REQUIRED;
		}

		$lists           = array();
		$list_model      = WYSIJA::get( 'list', 'model' );
		$all_lists_array = $list_model->get( array( 'name', 'list_id' ), array( 'is_enabled' => '1' ) );

		foreach ( $all_lists_array as $list_details ) {
			$lists[ $list_details['list_id'] ]['name'] = sanitize_text_field( $list_details['name'] );

			$user_model            = WYSIJA::get( 'user_list', 'model' );
			$all_subscribers_array = $user_model->get( array( 'user_id' ), array( 'list_id' => $list_details['list_id'] ) );

			$subscribers_count                                      = count( $all_subscribers_array );
			$lists[ $list_details['list_id'] ]['subscribers_count'] = sanitize_text_field( $subscribers_count );
		}

		$this->data['is_authorized'] = true;

		if ( ! empty( $lists ) ) {
			$this->data['lists'] = $lists;
		}

		$this->save_data();

		return array( 'success' => $this->data );
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( ! class_exists( 'WYSIJA' ) ) {
			ET_Core_Logger::error( self::$PLUGIN_REQUIRED );
			return esc_html__( 'An error occurred. Please try again later.', 'et_core' );
		}

		global $wpdb;
		$wpdb->wysija_user_table       = $wpdb->prefix . 'wysija_user';
		$wpdb->wysija_user_lists_table = $wpdb->prefix . 'wysija_user_list';

		// get the ID of subscriber if they're in the list already
		$subscriber_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT user_id FROM {$wpdb->wysija_user_table} WHERE email = %s",
			array(
				et_core_sanitized_previously( $args['email'] ),
			)
		) );
		$already_subscribed = 0;

		// if current email is subscribed, then check whether it subscribed to the current list
		if ( ! empty( $subscriber_id ) ) {
			$already_subscribed = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->wysija_user_lists_table} WHERE user_id = %s AND list_id = %s",
				array(
					$subscriber_id,
					et_core_sanitized_previously( $args['list_id'] ),
				)
			) );
		}

		unset( $wpdb->wysija_user_table );
		unset( $wpdb->wysija_user_list_table );

		// if email is not subscribed to current list, then subscribe.
		if ( 0 === $already_subscribed ) {
			$new_user = array(
				'user'      => array(
					'email'     => et_core_sanitized_previously( $args['email'] ),
					'firstname' => et_core_sanitized_previously( $args['name'] ),
					'lastname'  => et_core_sanitized_previously( $args['last_name'] ),
				),
				'user_list' => array(
					'list_ids' => array( et_core_sanitized_previously( $args['list_id'] ) ),
				),
			);

			$mailpoet_class = WYSIJA::get( 'user', 'helper' );
			$error_message  = $mailpoet_class->addSubscriber( $new_user );
			$error_message  = is_int( $error_message ) ? 'success' : $error_message;
		} else {
			$error_message = esc_html__( 'Already Subscribed', 'bloom' );
		}

		return $error_message;
	}
}

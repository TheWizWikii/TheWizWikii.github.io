<?php

/**
 * Wrapper for ProviderName's API.
 *
 * @since 4.0.7
 *
 * @package ET\Core\API\Misc\ReCaptcha
 */
class ET_Core_API_Spam_ReCaptcha extends ET_Core_API_Spam_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	 * @inheritDoc
	 */
	public $max_accounts = 1;

	/**
	 * @inheritDoc
	 */
	public $name = 'ReCaptcha';

	/**
	 * @inheritDoc
	 */
	public $slug = 'recaptcha';

	public function __construct( $owner = 'ET_Core', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_add_actions_and_filters();
	}

	protected function _add_actions_and_filters() {
		if ( ! is_admin() && ! et_core_is_fb_enabled() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'action_wp_enqueue_scripts' ) );
		}
	}

	public function action_wp_enqueue_scripts() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		/**
		 * reCAPTCHA v3 actions may only contain alphanumeric characters and slashes/underscore.
		 * https://developers.google.com/recaptcha/docs/v3#actions
		 *
		 * Replace all non-alphanumeric characters with underscore.
		 * Ex: '?page_id=254980' => '_page_id_254980'
		 */
		$action = preg_replace( '/[^A-Za-z0-9]/', '_', basename( get_the_permalink() ) );
		$deps   = array( 'jquery', 'es6-promise', 'et-recaptcha-v3' );

		wp_register_script( 'et-recaptcha-v3', "https://www.google.com/recaptcha/api.js?render={$this->data['site_key']}", array(), ET_CORE_VERSION, true );
		wp_register_script( 'es6-promise', ET_CORE_URL . 'admin/js/es6-promise.auto.min.js', array(), ET_CORE_VERSION, true );

		wp_enqueue_script( 'et-core-api-spam-recaptcha', ET_CORE_URL . 'admin/js/recaptcha.js', $deps, ET_CORE_VERSION, true );
		wp_localize_script( 'et-core-api-spam-recaptcha', 'et_core_api_spam_recaptcha', array(
			'site_key'    => empty( $this->data['site_key'] ) ? '' : $this->data['site_key'],
			'page_action' => array( 'action' => $action ),
		) );
	}

	public function is_enabled() {
		$has_recaptcha_module = true;

		if ( class_exists( 'ET_Dynamic_Assets' ) ) {
			$et_dynamic_module_framework  = et_builder_dynamic_module_framework();
			$is_dynamic_framework_enabled = et_builder_is_frontend() && 'on' === $et_dynamic_module_framework;
			$is_dynamic_css_enabled       = et_builder_is_frontend() && et_use_dynamic_css();

			if ( $is_dynamic_framework_enabled && $is_dynamic_css_enabled ) {
				$et_dynamic_assets    = ET_Dynamic_Assets::init();
				$saved_shortcodes     = $et_dynamic_assets->get_saved_page_shortcodes();
				$recaptcha_modules    = array( 'et_pb_contact_form', 'et_pb_signup' );
				$has_recaptcha_module = ! empty( array_intersect( $saved_shortcodes, $recaptcha_modules ) );
			}
		}

		$has_key = isset( $this->data['site_key'], $this->data['secret_key'] )
			&& et_()->all( array( $this->data['site_key'], $this->data['secret_key'] ) );

		return $has_key && $has_recaptcha_module;
	}

	/**
	 * Verify a form submission.
	 *
	 * @since 4.0.7
	 *
	 * @global $_POST['token']
	 *
	 * @return mixed[]|string $result {
	 *     Interaction Result
	 *
	 *     @type bool     $success      Whether or not the request was valid for this site.
	 *     @type int      $score        Score for the request (0 < score < 1).
	 *     @type string   $action       Action name for this request (important to verify).
	 *     @type string   $challenge_ts Timestamp of the challenge load (ISO format yyyy-MM-ddTHH:mm:ssZZ).
	 *     @type string   $hostname     Hostname of the site where the challenge was solved.
	 *     @type string[] $error-codes  Optional
	 * }
	 */
	public function verify_form_submission() {
		$args = array(
			'secret'   => $this->data['secret_key'],
			'response' => et_()->array_get_sanitized( $_POST, 'token' ),
			'remoteip' => et_core_get_ip_address(),
		);

		$this->prepare_request( $this->BASE_URL, 'POST', false, $args );
		$this->make_remote_request();

		return $this->response->ERROR ? $this->response->ERROR_MESSAGE : $this->response->DATA;
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'site_key'   => array(
				'label' => esc_html__( 'Site Key (v3)', 'et_core' ),
			),
			'secret_key' => array(
				'label' => esc_html__( 'Secret Key (v3)', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array() ) {
		return array(
			'ip_address' => 'remoteip',
			'response'   => 'response',
			'secret_key' => 'secret',
		);
	}
}

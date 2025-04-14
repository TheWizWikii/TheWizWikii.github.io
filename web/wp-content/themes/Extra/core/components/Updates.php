<?php

if ( ! class_exists( 'ET_Core_Updates' ) ):
/**
 * Handles the updates workflow.
 *
 * @private
 *
 * @package ET\Core\Updates
 */
final class ET_Core_Updates {
	protected $core_url;
	protected $options;
	protected $account_status;
	protected $product_version;
	protected $all_et_products_domains;
	protected $upgrading_et_product;
	protected $up_to_date_products_data;

	// class version
	protected $version;

	private static $_this;

	function __construct( $core_url, $product_version ) {
		global $wp_version;

		// Don't allow more than one instance of the class
		if ( isset( self::$_this ) ) {
			wp_die( sprintf( esc_html__( '%s: You cannot create a second instance of this class.', 'et-core' ),
				esc_html( get_class( $this ) )
			) );
		}

		self::$_this = $this;

		$this->core_url = $core_url;
		$this->version  = '1.2';

		$this->up_to_date_products_data = array();

		$this->product_version = $product_version;

		$this->get_options();

		$this->upgrading_et_product = false;

		$this->update_product_domains();

		$this->maybe_force_update_requests();

		add_filter( 'wp_prepare_themes_for_js', array( $this, 'replace_theme_update_notification' ) );
		add_filter( 'upgrader_package_options', array( $this, 'check_upgrading_product' ) );

		// The 4th paramenter, $hook_extra was added in WordPress 5.5.0.
		if ( version_compare( $wp_version, '5.5.0', '>=' ) ) {
			add_filter( 'upgrader_pre_download', array( $this, 'update_error_message' ), 20, 4 );
		} else {
			add_filter( 'upgrader_pre_download', array( $this, 'update_error_message' ), 20, 3 );
		}

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugins_updates' ) );
		add_filter( 'plugins_api', array( $this, 'maybe_modify_plugins_changelog' ), 20, 3 );

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_themes_updates' ) );

		add_filter( 'self_admin_url', array( $this, 'change_plugin_changelog_url' ), 10, 2 );
		add_filter( 'admin_url', array( $this, 'change_plugin_changelog_url' ), 10, 2 );
		add_filter( 'network_admin_url', array( $this, 'change_plugin_changelog_url' ), 10, 2 );

		add_action( 'admin_notices', array( $this, 'maybe_show_account_notice' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_styles' ) );

		add_action( 'plugins_loaded', array( $this, 'remove_updater_plugin_actions' ), 30 );

		add_action( 'after_setup_theme', array( $this, 'remove_theme_update_actions' ), 11 );

		add_action( 'admin_init', array( $this, 'remove_plugin_update_actions' ) );

		add_action( 'update_site_option_et_automatic_updates_options', array( $this, 'force_update_requests' ) );
		add_action( 'update_option_et_automatic_updates_options', array( $this, 'force_update_requests' ) );

		add_action( 'deleted_site_transient', array( $this, 'maybe_reset_et_products_update_transient' ) );
	}

	function check_upgrading_product( $options ) {
		if ( ! isset( $options['hook_extra'] ) ) {
			return $options;
		}

		$hook_name = isset( $options['hook_extra']['plugin'] ) ? 'plugin' : 'theme';

		// set the upgrading_et_product flag if one of ET plugins or themes is about to upgrade
		if ( isset( $options['hook_extra'][ $hook_name ] ) && in_array( $options['hook_extra'][ $hook_name ], $this->all_et_products_domains[ $hook_name ] ) ) {
			$this->upgrading_et_product = true;
		}

		return $options;
	}

	function maybe_append_custom_notification( $plugin_data, $response ) {
		if ( empty( $response ) ) {
			$package_available = false;
		} else {
			// for themes response is array for plugins - object, so check the format of data to get the correct results
			$package_available = is_array( $response ) ? ! empty( $response['package'] ) : ! empty( $response->package );
		}

		if ( $package_available ) {
			return;
		}

		$message = empty( $custom_message = $this->get_custom_update_notification_message( $plugin_data['url'] ) )
			? et_get_safe_localization( __( 'For all Elegant Themes products, please <a href="https://www.elegantthemes.com/documentation/divi/update-divi/" target="_blank">authenticate your subscription</a> via the Updates tab in your theme & plugin settings to enable product updates. Make sure that your Username and API Key have been entered correctly.', 'et-core' ) )
			: $custom_message;
		echo "</p><p>{$message}";
	}

	/**
	 * Check if we need to force update options removal in case a customer clicked on "Check Again" button
	 * in the notification area.
	 */
	function maybe_force_update_requests() {
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( empty( $_GET['et_action'] ) || 'update_account_details' !== $_GET['et_action'] ) {
			return;
		}

		if ( empty( $_GET['et_update_account_details_nonce'] ) || ! wp_verify_nonce( $_GET['et_update_account_details_nonce'], 'et_update_account_details' )
		) {
			return;
		}

		$this->force_update_requests();
	}

	function get_custom_update_notification_message( $update_message ) {
		$is_valid_api_key_status = empty( $account_api_key_status = get_site_option( 'et_account_api_key_status' ) ) || 'invalid' !== $account_api_key_status;

		if ( $is_valid_api_key_status && false !== strpos( $update_message, '/wp-json/api/v1/changelog/product_id/' ) ) {
			return et_get_safe_localization( __( '<em>The license for this Divi Marketplace product has expired. Please <a target="_blank" href="https://www.elegantthemes.com/members-area/marketplace/">renew the license</a> to continue receiving product updates and support.</em>', 'et-core' ) );
		}

		if ( false !== strpos( $update_message, 'Automatic update is unavailable for this theme' ) ) {
			return 'expired' === $this->account_status
				? et_get_safe_localization( __( '<em>Your Elegant Themes subscription has expired. You must <a href="https://www.elegantthemes.com/members-area/renew/" target="_blank">renew your account</a> to regain access to product updates and support.</em>', 'et-core' ) )
				: et_get_safe_localization( __( '<em>Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key into the Updates Tab in your theme and plugin settings. To locate your API Key, <a href="https://www.elegantthemes.com/members-area/api/" target="_blank">log in</a> to your Elegant Themes account and navigate to the <strong>Account > API Key</strong> page. <a href="https://www.elegantthemes.com/documentation/divi/update-divi/" target="_blank">Learn more here</a></em>. If you still get this message, please make sure that your Username and API Key have been entered correctly', 'et-core' ) );
		}

		return '';
	}

	function replace_theme_update_notification( $themes_array ) {
		if ( empty( $themes_array ) ) {
			return $themes_array;
		}

		if ( empty( $this->all_et_products_domains['theme'] ) ) {
			return $themes_array;
		}

		foreach ( $themes_array as $id => $theme_data ) {
			// replace default error message with custom message for ET themes.
			if ( ! in_array( $id, $this->all_et_products_domains['theme'] )
				|| false === strpos( $theme_data['update'], 'Automatic update is unavailable for this theme' ) ) {
				continue;
			}

			if ( ! empty( $custom_message = $this->get_custom_update_notification_message( $theme_data['update'] ) ) ) {
				$themes_array[ $id ]['update'] = sprintf(
					'<p>%1$s<br/> %2$s</p>',
					$theme_data['update'],
					$custom_message
				);
			}
		}

		return $themes_array;
	}

	function update_error_message( $reply, $package, $upgrader, $hook_extra = array() ) {
		if ( ! $this->upgrading_et_product ) {
			return $reply;
		}

		// reset the upgrading_et_product flag
		$this->upgrading_et_product = false;

		if ( ! empty( $package ) ) {
			return $reply;
		}

		$hook_name = ! empty( $hook_extra['theme'] ) ? 'theme' : 'plugin';
		$site_transient = 'theme' === $hook_name ? get_site_transient( 'et_update_themes' ) : get_site_transient( 'et_update_all_plugins' );

		$changelog_url = '';
		if ( isset( $site_transient->response ) && ! empty( $site_transient->response[ $hook_extra[ $hook_name ] ] ) ) {
			$changelog_url = 'theme' === $hook_name
				? $site_transient->response[ $hook_extra[ $hook_name ] ]['url']
				: $site_transient->response[ $hook_extra[ $hook_name ] ]->url;
		}

		if ( false !== strpos( $changelog_url, '/wp-json/api/v1/changelog/product_id/' ) ) {
			$error_message = $this->get_custom_update_notification_message( $changelog_url );
		} else {
			$error_message = 'expired' === $this->account_status
				? et_get_safe_localization( __( '<em>Your Elegant Themes subscription has expired. You must <a href="https://www.elegantthemes.com/members-area/renew/" target="_blank">renew your account</a> to regain access to product updates and support.</em>', 'et-core' ) )
				: et_get_safe_localization( __( '<em>Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key into the Updates Tab in your theme and plugin settings. To locate your API Key, <a href="https://www.elegantthemes.com/members-area/api/" target="_blank">log in</a> to your Elegant Themes account and navigate to the <strong>Account > API Key</strong> page. <a href="https://www.elegantthemes.com/documentation/divi/update-divi/" target="_blank">Learn more here</a></em>. If you still get this message, please make sure that your Username and API Key have been entered correctly', 'et-core' ) );
		}

		// output custom error message for ET Products if package is empty

		return new WP_Error( 'no_package', $error_message );
	}

	/**
	 * Get all Elegant Themes products, returned from the API request
	 */
	function get_et_api_products() {
		$products = array(
			'theme'  => array(),
			'plugin' => array(),
		);

		$update_transients = array(
			'et_update_themes',
			'et_update_all_plugins',
		);

		foreach ( $update_transients as $update_transient_name ) {
			$type = 'et_update_themes' === $update_transient_name ? 'theme' : 'plugin';

			if (
				false !== ( $update_transient = get_site_transient( $update_transient_name ) )
				&& ! empty( $update_transient->response )
				&& is_array( $update_transient->response )
			) {
				$et_product_stylesheet_names = array_keys( $update_transient->response );

				foreach ( $et_product_stylesheet_names as $et_product_stylesheet_name ) {
					$products[ $type ][] = $et_product_stylesheet_name;
				}
			}
		}

		return $products;
	}

	function get_all_et_products() {
		$checked_et_products = $this->get_et_api_products();

		return $checked_et_products;
	}

	function remove_theme_update_actions() {
		remove_filter( 'pre_set_site_transient_update_themes', 'et_check_themes_updates' );
		remove_filter( 'site_transient_update_themes', 'et_add_themes_to_update_notification' );
	}

	function remove_plugin_update_actions() {
		remove_filter( 'pre_set_site_transient_update_plugins', 'et_shortcodes_plugin_check_updates' );
		remove_filter( 'site_transient_update_plugins', 'et_shortcodes_plugin_add_to_update_notification' );
	}

	/**
	 * Removes Updater plugin actions and filters,
	 * so it doesn't make additional requests to API
	 *
	 * @return void
	 */
	function remove_updater_plugin_actions() {
		if ( ! class_exists( 'ET_Automatic_Updates' ) ) {
			return;
		}

		$updates_class = ET_Automatic_Updates::get_this();

		remove_filter( 'after_setup_theme', array( $updates_class, 'remove_default_updates' ), 11 );

		remove_filter( 'init', array( $updates_class, 'remove_default_plugins_updates' ), 20 );

		remove_action( 'admin_notices', array( $updates_class, 'maybe_display_expired_message' ) );
	}

	/**
	 * Returns an instance of the object
	 *
	 * @return object
	 */
	static function get_this() {
		return self::$_this;
	}

	/**
	 * Adds automatic updates data only if Username and API key options are set
	 *
	 * @param array $send_to_api Data sent to server
	 * @return array Modified data set if Username and API key are set, original data if not
	 */
	function maybe_add_automatic_updates_data( $send_to_api ) {
		if ( $this->options && isset( $this->options['username'] ) && isset( $this->options['api_key'] ) ) {
			$send_to_api['automatic_updates'] = 'on';

			$send_to_api['username'] = urlencode( sanitize_text_field( $this->options['username'] ) );
			$send_to_api['api_key']  = sanitize_text_field( $this->options['api_key'] );

			$send_to_api = apply_filters( 'et_add_automatic_updates_data', $send_to_api );
		}

		return $send_to_api;
	}

	/**
	 * Gets plugin options
	 *
	 * @return void
	 */
	function get_options() {
		if ( ! $this->options = get_site_option( 'et_automatic_updates_options' ) ) {
			$this->options = get_option( 'et_automatic_updates_options' );
		}

		if ( ! $this->account_status = get_site_option( 'et_account_status' ) ) {
			$this->account_status = get_option( 'et_account_status' );
		}
	}

	function load_scripts_styles( $hook ) {
		if ( 'plugin-install.php' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'et_core_updates', $this->core_url . 'admin/css/updates.css', array(), $this->product_version );
	}

	function add_up_to_date_products_data( $update_data, $settings = array() ) {
		$settings = $this->process_request_settings( $settings );

		$products_category = $settings['is_plugin_response'] ? 'plugins' : 'themes';

		if ( ! empty( $this->up_to_date_products_data[ $products_category ] ) ) {
			$update_data->no_update = $this->up_to_date_products_data[ $products_category ];
		}

		return $update_data;
	}

	function merge_et_products_response( $update_transient, $et_update_products_data ) {
		if (
			empty( $et_update_products_data )
			|| (
				empty( $et_update_products_data->response )
				&& empty( $et_update_products_data->no_update )
			)
		) {
			return $update_transient;
		}

		$merge_data_fields = array(
			'response',
			'no_update',
		);

		foreach ( $merge_data_fields as $data_field_name ) {
			if ( empty( $et_update_products_data->$data_field_name ) ) {
				continue;
			}

			$default_response_data = ! empty( $update_transient->$data_field_name ) ? $update_transient->$data_field_name : array();

			$update_transient->$data_field_name = array_merge( $default_response_data, $et_update_products_data->$data_field_name );
		}

		return $update_transient;
	}

	function check_plugins_updates( $update_transient ) {
		global $wp_version;

		if ( ! isset( $update_transient->response ) ) {
			return $update_transient;
		}

		$plugins = [];

		$et_update_plugins = get_site_transient( 'et_update_all_plugins' );

		// update_plugins transient gets set two times, so we ensure we make a request once
		if (
			isset( $et_update_plugins->last_checked )
			&& isset( $update_transient->last_checked )
			&& $et_update_plugins->last_checked > ( $update_transient->last_checked - 60 )
		) {
			return $this->merge_et_products_response( $update_transient, $et_update_plugins );
		}

		$_plugins = get_plugins();

		if ( empty( $_plugins ) ) {
			return $update_transient;
		}

		foreach ( $_plugins as $file => $plugin ) {
			$update_uri = isset( $plugin['UpdateURI'] ) ? $plugin['UpdateURI'] : '';
			$is_et_uri  = false !== strpos( $update_uri, 'elegantthemes.com' );

			// Continue to the next iteration if the Update URI
			// is not empty and not using Elegant Themes's domain.
			if ( ! empty( $update_uri ) && ! $is_et_uri ) {
				continue;
			}

			$plugins[ $file ] = $plugin['Version'];
		}

		do_action( 'et_core_updates_before_request' );

		$send_to_api = array(
			'action'            => 'check_all_plugins_updates',
			'installed_plugins' => $plugins,
			'class_version'     => $this->version,
		);

		// Add automatic updates data if Username and API key are set correctly
		$send_to_api = $this->maybe_add_automatic_updates_data( $send_to_api );

		// If we don't have update values cached in the transient, we need to bypass rate limiting.
		if ( $et_update_plugins ) {
			$rate_limit = 'true';
		} else {
			$rate_limit = 'false';
		}

		$options = array(
			'timeout'    => ( ( defined('DOING_CRON') && DOING_CRON ) ? 10 : 3),
			'body'       => $send_to_api,
			'headers'    => array(
				'rate_limit' => $rate_limit,
			),
			'user-agent' => 'WordPress/' . $wp_version . '; Plugin Updates/' . ET_CORE_VERSION . '; ' . home_url( '/' ),
		);

		$last_update = new stdClass();

		$plugins_request = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );

		$plugins_response = [];
		$et_update_plugins_updated = false;

		if ( ! is_wp_error( $plugins_request ) && wp_remote_retrieve_response_code( $plugins_request ) === 200 ){
			$plugins_response = maybe_unserialize( wp_remote_retrieve_body( $plugins_request ) );

			if ( ! empty( $plugins_response ) && is_array( $plugins_response ) ) {
				$et_update_plugins_updated = true;

				$request_settings = array( 'is_plugin_response' => true );

				$plugins_response = $this->process_additional_response_settings( $plugins_response, $request_settings );

				$last_update->checked  = $plugins;
				$last_update->response = $plugins_response;

				$last_update = $this->add_up_to_date_products_data( $last_update, $request_settings );

				$last_update->last_checked = time();

				$update_transient = $this->merge_et_products_response( $update_transient, $plugins_response );

				set_site_transient( 'et_update_all_plugins', $last_update );

				$this->update_product_domains();
			}
		}

		// if this is a rate limited or failed request, fallback to the last et_update_plugins value
		// rather than overwrite it with an empty response
		if ( ! $et_update_plugins_updated ) {
			$update_transient = $this->merge_et_products_response( $update_transient, $et_update_plugins );
		}

		return $update_transient;
	}

	public function maybe_modify_plugins_changelog( $false, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $false;
		}

		if ( isset( $args->slug ) ) {
			$et_update_lb_plugin = get_site_transient( 'et_update_all_plugins' );

			$plugin_basename = sprintf( '%1$s/%1$s.php', sanitize_text_field( $args->slug ) );

			if ( isset( $et_update_lb_plugin->response[ $plugin_basename ] ) ) {
				$plugin_info = $et_update_lb_plugin->response[ $plugin_basename ];

				if ( isset( $plugin_info->et_sections_used ) && 'on' === $plugin_info->et_sections_used ) {
					return $plugin_info;
				}
			}
		}

		return $false;
	}

	function process_account_settings( $response ) {
		if ( empty( $response['et_account_data'] ) ) {
			return $response;
		}

		$additional_settings_fields = array(
			'et_username_status',
			'et_api_key_status',
			'et_expired_subscription',
		);

		$et_account_data = $response['et_account_data'];

		$additional_settings = array();

		$is_theme_response = is_array( $et_account_data );

		foreach ( $additional_settings_fields as $additional_settings_field ) {
			$field = '';

			$field_exists = $is_theme_response ? array_key_exists( $additional_settings_field, $et_account_data ) : ! empty( $et_account_data->$additional_settings_field );

			if ( $field_exists ) {
				$field = $is_theme_response ? $et_account_data[ $additional_settings_field ] : $et_account_data->$additional_settings_field;
			}

			$additional_settings[ $additional_settings_field ] = $field;
		}

		if (
			! empty( $additional_settings[ 'et_username_status' ] )
			&& in_array( $additional_settings[ 'et_username_status' ], array( 'active', 'expired', 'not_found' ) )
		) {
			$this->account_status = sanitize_text_field( $additional_settings['et_username_status'] );
		} else {
			// Set the account status to expired if the response array has 'et_expired_subscription' key
			$this->account_status = ! empty( $additional_settings[ 'et_expired_subscription' ] ) ? 'expired' : 'active';
		}

		update_site_option( 'et_account_status', $this->account_status );

		if ( ! empty( $additional_settings[ 'et_api_key_status' ] ) ) {
			update_site_option( 'et_account_api_key_status', sanitize_text_field( $additional_settings['et_api_key_status'] ) );
		} else {
			delete_site_option( 'et_account_api_key_status' );
		}

		unset( $response['et_account_data'] );

		return $response;
	}

	function process_up_to_date_products_settings( $response, $settings ) {
		if ( empty( $response['et_up_to_date_products'] ) ) {
			return $response;
		}

		$products_category = $settings['is_plugin_response'] ? 'plugins' : 'themes';

		$this->up_to_date_products_data[ $products_category ] = $response['et_up_to_date_products'];

		unset( $response['et_up_to_date_products'] );

		return $response;
	}

	function process_request_settings( $settings = array() ) {
		$defaults = array(
			'is_plugin_response' => false,
		);

		return array_merge( $defaults, $settings );
	}

	function process_additional_response_settings( $response, $settings = array() ) {
		if ( empty( $response ) ) {
			return $response;
		}

		$settings = $this->process_request_settings( $settings );

		$response = $this->process_account_settings( $response );

		$response = $this->process_up_to_date_products_settings( $response, $settings );

		return $response;
	}

	/**
	 * Sends a request to server, gets current themes versions
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function check_themes_updates( $update_transient ){
		global $wp_version;

		$et_update_themes = get_site_transient( 'et_update_themes' );

		// update_themes transient gets set two times, so we ensure we make a request once
		if (
			isset( $et_update_themes->last_checked )
			&& isset( $update_transient->last_checked )
			&& $et_update_themes->last_checked > ( $update_transient->last_checked - 60 )
		) {
			return $this->merge_et_products_response( $update_transient, $et_update_themes );
		}

		if ( ! isset( $update_transient->checked ) ) {
			return $update_transient;
		}

		$themes = $update_transient->checked;

		do_action( 'et_core_updates_before_request' );

		$send_to_api = array(
			'action'           => 'check_theme_updates',
			'installed_themes' => $themes,
			'class_version'    => $this->version,
		);

		// Add automatic updates data if Username and API key are set correctly
		$send_to_api = $this->maybe_add_automatic_updates_data( $send_to_api );

		// If we don't have update values cached in the transient, we need to bypass rate limiting.
		if ( $et_update_themes ) {
			$rate_limit = 'true';
		} else {
			$rate_limit = 'false';
		}

		$options = array(
			'timeout'    => ( ( defined('DOING_CRON') && DOING_CRON ) ? 10 : 3 ),
			'body'       => $send_to_api,
			'headers'    => array(
				'rate_limit' => $rate_limit,
			),
			'user-agent' => 'WordPress/' . $wp_version . '; Theme Updates/' . ET_CORE_VERSION . '; ' . home_url( '/' ),
		);

		$last_update = new stdClass();

		$theme_request = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );
		$theme_response = [];
		$et_update_themes_updated = false;

		if ( ! is_wp_error( $theme_request ) && wp_remote_retrieve_response_code( $theme_request ) === 200 ){
			$theme_response = maybe_unserialize( wp_remote_retrieve_body( $theme_request ) );

			if ( ! empty( $theme_response ) && is_array( $theme_response ) ) {
				$et_update_themes_updated = true;

				$theme_response = $this->process_additional_response_settings( $theme_response );

				$last_update->checked  = $themes;
				$last_update->response = $theme_response;

				$last_update = $this->add_up_to_date_products_data( $last_update );

				$last_update->last_checked = time();

				$update_transient = $this->merge_et_products_response( $update_transient, $last_update );

				set_site_transient( 'et_update_themes', $last_update );

				$this->update_product_domains();
			}
		}

		// if this is a rate limited or failed request, fallback to the last et_update_themes value
		// rather than overwrite it with an empty response
		if ( ! $et_update_themes_updated ) {
			$update_transient = $this->merge_et_products_response( $update_transient, $et_update_themes );
		}

		return $update_transient;
	}

	function maybe_show_account_notice() {
		if ( empty( $this->options['username'] ) || empty( $this->options['api_key'] ) ) {
			return;
		}

		$output = '';

		$messages = array();

		$account_api_key_status = get_site_option( 'et_account_api_key_status' );

		$is_expired_account = 'expired' === $this->account_status;

		$is_invalid_account = 'not_found' === $this->account_status;

		if (
			! $is_expired_account
			&& ! $is_invalid_account
			&& empty( $account_api_key_status )
		) {
			return;
		}

		if ( $is_expired_account ) {
			$messages[] = et_get_safe_localization( __( 'Your Elegant Themes subscription has expired. You must <a href="https://www.elegantthemes.com/members-area/" target="_blank">renew your account</a> to regain access to product updates and support. To ensure compatibility and security, it is important to always keep your themes and plugins updated.', 'et-core' ) );
		} else if ( $is_invalid_account ) {
			$messages[] = et_get_safe_localization( __( 'The Elegant Themes username you entered is invalid. Please enter a valid username to receive product updates. If you forgot your username you can <a href="https://www.elegantthemes.com/members-area/retrieve-username/" target="_blank">request it here</a>.', 'et-core' ) );
		}

		if ( ! empty( $account_api_key_status ) ) {
			switch ( $account_api_key_status ) {
				case 'deactivated':
					$status = 'not active';

					break;
				default:
					$status = 'invalid';

					break;
			}

			$messages[] = et_get_safe_localization( __(
				sprintf( 'The Elegant Themes API key you entered is %1$s. Please make sure that your API has been entered correctly and that it is <a href="https://www.elegantthemes.com/members-area/api/" target="_blank">enabled</a> in your account.', $status ),
				'et-core'
			) );
		}

		foreach ( $messages as $message ) {
			$output .= sprintf( '<p>%1$s</p>', $message );
		}

		if ( empty( $output ) ) {
			return;
		}

		$dashboard_url = add_query_arg( 'et_action', 'update_account_details', admin_url( 'update-core.php' ) );

		printf(
			'<div class="notice notice-warning">
				%1$s
				<p><a href="%2$s">%3$s</a></p>
			</div>',
			$output,
			esc_url( wp_nonce_url( $dashboard_url, 'et_update_account_details', 'et_update_account_details_nonce' ) ),
			esc_html__( 'Check Again', 'et-core' )
		);
	}

	function change_plugin_changelog_url( $url, $path ) {
		if ( 0 !== strpos( $path, 'plugin-install.php?tab=plugin-information&plugin=' ) ) {
			return $url;
		}

		$matches                 = array();
		$update_transient        = get_site_transient( 'et_update_all_plugins' );
		$et_updated_plugins_data = get_transient( 'et_updated_plugins_data' );
		$has_last_checked        = ! empty( $update_transient->last_checked ) && ! empty( $et_updated_plugins_data->last_checked );

		if ( ! is_object( $update_transient ) ) {
			return $url;
		}

		/*
		 * Attempt to use a cached list of updated plugins.
		 * Re-save the list, whenever the update transient last checked time changes.
		 */
		if ( false === $et_updated_plugins_data || ( $has_last_checked && $update_transient->last_checked !== $et_updated_plugins_data->last_checked ) ) {
			$et_updated_plugins_data = new stdClass();

			if ( ! empty( $update_transient->last_checked ) ) {
				$et_updated_plugins_data->last_checked = $update_transient->last_checked;
			}

			foreach ( $update_transient->response as $response_plugin_settings ) {
				$slug = sanitize_text_field( $response_plugin_settings->slug );

				$et_updated_plugins_data->changelogs[ $slug ] = esc_url_raw( $response_plugin_settings->url . '?TB_iframe=true&width=1024&height=800' );
			}

			set_transient( 'et_updated_plugins_data', $et_updated_plugins_data );
		}

		if ( ! empty( $update_transient->no_update ) ) {
			foreach ( $update_transient->no_update as $no_update_plugin_settings ) {
				$slug = sanitize_text_field( $no_update_plugin_settings->slug );

				$et_updated_plugins_data->changelogs[ $slug ] = esc_url_raw( $no_update_plugin_settings->url . '?TB_iframe=true&width=1024&height=800' );
			}
		}

		preg_match( '/plugin=([^&]*)/', $path, $matches );

		$current_plugin_slug = $matches[1];

		// Check if we're dealing with a product that has a custom changelog URL
		if ( ! empty( $et_updated_plugins_data->changelogs[ $current_plugin_slug ] ) ) {
			$url = esc_url_raw( $et_updated_plugins_data->changelogs[ $current_plugin_slug ] );
		}

		return $url;
	}

	function force_update_requests() {
		$update_transients = array(
			'update_themes',
			'update_plugins',
			'et_update_themes',
			'et_update_all_plugins',
		);

		foreach ( $update_transients as $update_transient ) {
			if ( get_site_transient( $update_transient ) ) {
				delete_site_transient( $update_transient );
			}
		}
	}

	function update_product_domains() {
		$this->all_et_products_domains = $this->get_all_et_products();

		$append_notification_action_name = 'maybe_append_custom_notification';

		// update notifications for ET products if needed
		foreach ( array( 'theme', 'plugin' ) as $product_type ) {
			if ( empty( $this->all_et_products_domains[ $product_type] ) ) {
				continue;
			}

			foreach ( $this->all_et_products_domains[ $product_type ] as $product_key ) {
				$action_name = sanitize_text_field( sprintf(
					'in_%1$s_update_message-%2$s',
					$product_type,
					$product_key
				) );

				if ( has_action( $action_name, array( $this, $append_notification_action_name ) ) ) {
					continue;
				}

				add_action( $action_name, array( $this, $append_notification_action_name ), 10, 2 );
			}
		}
	}

	/**
	 * Delete Elegant Themes update products transient, whenever default WordPress update transient gets removed
	 */
	function maybe_reset_et_products_update_transient( $transient_name ) {
		// Transient names for update transients we're interested in.
		$update_transients_names = array(
			'update_themes'  => 'et_update_themes',
			'update_plugins' => 'et_update_all_plugins',
		);

		// Check if the transient name is one of the update transients we're interested in.
		if ( empty( $update_transients_names[ $transient_name ] ) ) {
			return;
		}

		// Check the last_checked time in the transient, and only delete it if it's older than 24 hours.
		$et_update_transient = get_site_transient( $update_transients_names[ $transient_name ] );

		if (
			! empty( $et_update_transient->last_checked )
			&& $et_update_transient->last_checked > ( time() - DAY_IN_SECONDS )
		) {
			return;
		}

		// Delete the ET update transient, because it's older than 24 hours.
		delete_site_transient( $update_transients_names[ $transient_name ] );
	}
}
endif;

if ( ! function_exists( 'et_core_enable_automatic_updates' ) ) :
function et_core_enable_automatic_updates( $deprecated, $version ) {
	if ( ! is_admin() && ! wp_doing_cron() ) {
		return;
	}

	if ( isset( $GLOBALS['et_core_updates'] ) ) {
		return;
	}

	if ( defined( 'ET_CORE_URL' ) ) {
		$url = ET_CORE_URL;
	} else {
		$url = trailingslashit( $deprecated ) . 'core/';
	}

	$GLOBALS['et_core_updates'] = new ET_Core_Updates( $url, $version );

}
endif;

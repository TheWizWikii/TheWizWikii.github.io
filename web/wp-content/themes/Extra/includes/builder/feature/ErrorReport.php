<?php
/**
 * Handle error report
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// get_plugins() is only available on dashboard; Manually require it needed.
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Class to send an error report.
 */
class ET_Builder_Error_Report {
	/**
	 * Instance of `ET_Core_Data_Utils`.
	 *
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * Instance of `ET_Builder_Error_Report`.
	 *
	 * @var ET_Builder_Error_Report
	 */
	private static $_instance;

	/**
	 * ET_Builder_Error_Report constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_et_fb_error_report', array( 'ET_Builder_Error_Report', 'endpoint' ) );
	}

	/**
	 * Get json_decode data and stripslashes if needed.
	 *
	 * @since 3.24
	 *
	 * @param string $data Data to be decoded.
	 *
	 * @return mixed
	 */
	public static function json_decode_maybe_stripslashes( $data ) {
		$decoded = json_decode( $data, true );
		if ( null === $decoded ) {
			$decoded = json_decode( stripslashes( $data ), true );
		}
		return $decoded;
	}

	/**
	 * Get the class instance.
	 *
	 * @since 3.21.4
	 *
	 * @return ET_Builder_Error_Report
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		self::$_ = ET_Core_Data_Utils::instance();

		return self::$_instance;
	}

	/**
	 * Get information sent for error reporting
	 *
	 * @since 3.21.4
	 *
	 * @return array
	 */
	public static function get_debug_info() {
		$info = array(
			'user'         => array(
				'role',
			),
			'errors'       => array(
				'error_message',
				'error_message_stack',
				'error_stack',
				'component_info',
				'notes',
			),
			'page'         => array(
				'post_type',
				'builder_settings',
				'builder_history',
				'preferences',
			),
			'installation' => array(
				'product_name',
				'product_version',
				'builder_version',
				'wp_version',
				'installed_plugins',
				'active_plugins',
				'must_use_plugins',
			),
		);

		// If the site uses divi builder plugin, provide the theme information.
		if ( et_is_builder_plugin_active() ) {
			array_unshift(
				$info['installation'],
				'theme_name'
			);
		}

		// If the site uses child theme, provide the child theme information.
		if ( is_child_theme() ) {
			array_unshift(
				$info['installation'],
				'is_child_theme',
				'child_theme_name',
				'child_theme_version'
			);
		}

		return $info;
	}

	/**
	 * Get current product name
	 *
	 * @since 3.21.4
	 *
	 * @return string|bool
	 */
	protected function _get_product() {
		if ( et_is_builder_plugin_active() ) {
			return 'divi-builder';
		}

		if ( function_exists( 'et_divi_fonts_url' ) ) {
			return 'Divi';
		}

		if ( function_exists( 'et_extra_fonts_url' ) ) {
			return 'Extra';
		}

		return false;
	}

	/**
	 * Get debug item value
	 *
	 * @since 3.21.4
	 *
	 * @param string $info_name debug info item name.
	 * @param object $post      alias for $_POST.
	 *
	 * @return string|array|object
	 */
	protected function _get_debug_value( $info_name, $post ) {
		switch ( $info_name ) {
			case 'role':
				$current_user = wp_get_current_user();
				$value        = esc_html( implode( ', ', $current_user->roles ) );
				break;

			case 'error_message':
			case 'error_message_stack':
			case 'error_stack':
			case 'notes':
			case 'post_type':
				// this will be saved into a text report, no need to convert entities.
				$value = self::$_->array_get( $post, $info_name, '' );
				break;

			case 'latest_content':
			case 'loaded_content':
				$value = et_fb_process_to_shortcode( self::$_->array_get( $post, $info_name, array() ) );
				break;

			case 'builder_settings':
			case 'builder_history':
			case 'component_info':
				$value = wp_json_encode( self::$_->array_get( $post, $info_name, array() ) );
				break;

			case 'preferences':
				$value = array();
				foreach ( et_fb_app_preferences() as $name => $preference ) {
					$value[ $name ] = $preference['value'];
				}
				$value = wp_json_encode( $value );
				break;

			case 'product_name':
				$value = $this->_get_product();
				break;

			case 'product_version':
				$value = et_is_builder_plugin_active() ?
					self::$_->array_get( get_plugin_data( WP_PLUGIN_DIR . '/divi-builder/divi-builder.php' ), 'Version', '' ) :
					et_get_theme_version();

				$value = esc_html( $value );
				break;

			case 'builder_version':
				$value = ET_BUILDER_PRODUCT_VERSION;
				break;

			case 'wp_version':
				$value = esc_html( get_bloginfo( 'version' ) );
				break;

			case 'installed_plugins':
				$all_plugins = get_plugins();
				$value       = wp_json_encode( array_keys( $all_plugins ), true );
				break;

			case 'active_plugins':
				$all_plugins          = get_plugins();
				$active_plugins_saved = get_option( 'active_plugins' );
				$active_plugins_keys  = is_array( $active_plugins_saved ) ? $active_plugins_saved : array();
				$active_plugins       = array_intersect_key( $all_plugins, array_flip( $active_plugins_keys ) );
				$value                = wp_json_encode( $active_plugins, true );
				break;

			case 'must_use_plugins':
				$value = wp_json_encode( get_mu_plugins(), true );
				break;

			case 'theme_name':
			case 'child_theme_name':
				$value = esc_html( wp_get_theme()->get( 'Name' ) );
				break;

			case 'theme_version':
			case 'child_theme_version':
				$value = esc_html( wp_get_theme()->get( 'Version' ) );
				break;

			case 'is_child_theme':
				$value = is_child_theme() ? 'yes' : 'no';
				break;

			default:
				$value = '';
				break;
		}

		return $value;
	}

	/**
	 * Get error report content
	 *
	 * @since 3.21.4
	 *
	 * @param string $data Report data.
	 *
	 * @return string
	 */
	protected function _get_report_content( $data ) {
		$report_content = '';

		$debug_info = self::get_debug_info();

		$report_content = array();

		foreach ( $debug_info as $items_title => $debug_items ) {
			$item_key    = 'group_title-' . $items_title;
			$items_title = ucwords( $items_title );

			$report_content[ $item_key ] = $items_title;

			foreach ( $debug_items as $debug_item ) {
				$item_value = et_core_esc_previously( $this->_get_debug_value( $debug_item, $data, 'array' ) );

				$report_content[ $debug_item ] = $item_value;
			}
		}

		return $report_content;
	}

	/**
	 * Get attachment data as string to be passed into endpoint
	 *
	 * @since 3.21.4
	 *
	 * @param string $data Report data.
	 * @param string $field Debug info item name.
	 *
	 * @return string
	 */
	protected function _get_exported_layout_content( $data, $field ) {
		// phpcs:disable WordPress.Security.NonceVerification -- Nonce has been verified in the {@see self::endpoint()}.
		// Set faux $_POST value that is required by portability.
		$_POST['post']    = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
		$_POST['content'] = self::$_instance->_get_debug_value( $field, $data );

		// Remove page value if it is equal to `false`, avoiding paginated images not accidentally triggered.
		if ( isset( $_POST['page'] ) && false === $_POST['page'] ) {
			unset( $_POST['page'] );
		}

		$portability = et_core_portability_load( 'et_builder' );
		// Export the content.
		$result = $portability->export( true );
		// Delete temp files or else the same content will be used for all exports.
		$portability->delete_temp_files( 'et_core_export' );
		return $result;
		// phpcs:enable
	}

	/**
	 * Endpoint for sending error report request
	 *
	 * @since 3.21.4
	 */
	public static function endpoint() {
		// Check for valid permission. Only administrator role can send error report.
		if ( ! et_core_security_check_passed( 'manage_options', 'et_fb_send_error_report' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have valid permission to send error report', 'et_builder' ),
				)
			);
			wp_die();
		}

		// Check valid post id.
		$post_id = self::$_->array_get( $_POST, 'post_id', false );

		if ( ! $post_id ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'No valid post id found', 'et_builder' ),
				)
			);
			wp_die();
		}

		// Check report data.
		$data = self::$_->array_get( $_POST, 'data', false );

		if ( ! $data ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'No valid report data found', 'et_builder' ),
				)
			);
			wp_die();
		}

		// Check for Elegant Themes username & API Key.
		$updates_options = get_site_option( 'et_automatic_updates_options', array() );
		$et_username     = self::$_->array_get( $updates_options, 'username', '' );
		$et_api_key      = self::$_->array_get( $updates_options, 'api_key', '' );

		if ( '' === $et_username || '' === $et_api_key ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'No Elegant Themes username or API key found', 'et_builder' ),
				)
			);
			wp_die();
		}

		// Check for account status.
		$et_account_status = get_site_option( 'et_account_status', 'not_active' );

		if ( 'active' !== $et_account_status ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Your Elegant Themes account is inactive', 'et_builder' ),
				)
			);
			wp_die();
		}

		$data        = self::json_decode_maybe_stripslashes( $data );
		$et_endpoint = apply_filters( 'et_builder_report_endpoint', 'https://www.elegantthemes.com/api/reportV2.php' );

		// Crafting reports and send to end endpoint.
		$request_settings = array(
			'timeout' => 30,
			'body'    => array(
				'username'     => $et_username,
				'api_key'      => $et_api_key,
				'error_report' => self::$_instance->_get_report_content( $data ),
				'site_url'     => site_url(),
				'attachments'  => array(
					'latest' => self::$_instance->_get_exported_layout_content( $data, 'latest_content' ),
					'loaded' => self::$_instance->_get_exported_layout_content( $data, 'loaded_content' ),
				),
			),
		);

		$request               = wp_remote_post( $et_endpoint, $request_settings );
		$request_response_code = wp_remote_retrieve_response_code( $request );
		$request_body          = wp_remote_retrieve_body( $request );

		if ( 200 === $request_response_code ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( json_decode( $request_body ) );
		}
		wp_die();
	}
}

ET_Builder_Error_Report::instance();

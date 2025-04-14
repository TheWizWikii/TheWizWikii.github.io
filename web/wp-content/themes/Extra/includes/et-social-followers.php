<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/ext/oauth.php';

class ET_Social_Followers {

	public $name;

	public $slug;

	public $authorization_required = false;

	public $fields = array();

	public $follower_verbiage = '';

	public $followers_verbiage = '';

	private $_transient_prefix = 'et_sf_';

	private static $_networks = array();

	function __construct() {
		$this->init();

		self::$_networks[$this->slug] = $this;

		//register ajax action and action to process the authorization if needed for current network
		if ( $this->authorization_required ) {
			add_action( 'wp_ajax_et_social_authorize_network_' . $this->slug, array( $this, 'authorize_network' ) );
			add_action( 'admin_notices', array( $this, 'api_maybe_get_access_token' ) );
		}
	}

	public function get_fields() {}

	public function fetch_count() {}

	public function authorize_network() {
		if ( ! wp_verify_nonce( $_POST['et_extra_nonce'], 'authorize_nonce' ) ) {
			die();
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$fields_data = isset( $_POST['et_extra_fields_data'] ) ? $_POST['et_extra_fields_data'] : '';

		if ( '' === $fields_data ) {
			die();
		}

		$fields_data_json = str_replace( '\\', '',  $fields_data );
		$fields_data_array = json_decode( $fields_data_json, true );
		$processed_data_array = array();

		// prepare array with fields data in convenient format
		if ( ! empty( $fields_data_array ) ) {
			foreach ( $fields_data_array as $index => $field_data ) {
				$processed_data_array[ $field_data['field_id'] ] = $field_data['field_val'];
			}
		}

		$authorization_url = $this->get_authorization_url( $processed_data_array );

		die( json_encode( array( 'authorization_url' => $authorization_url ) ) );
	}

	public function api_maybe_get_access_token() {
		$screen = get_current_screen();

		if ( "widgets" !== $screen->id ) {
			return;
		}

		// Check if a network returned authorization code
		if ( ! isset( $_GET['state'] ) || ! isset( $_GET['code'] ) ) {
			return;
		}

		$state = sanitize_text_field( $_GET['state'] );
		$code  = sanitize_text_field( $_GET['code'] );

		$underscore_position = strpos( $state, '_' );

		// Valid nonce should have an underscore ( e.g vimeo_58787324 )
		if ( false === $underscore_position ) {
			return;
		}

		$nonce = substr( $state, $underscore_position + 1 );

		// Check if a nonce is valid
		if ( ! wp_verify_nonce( $nonce, 'et_extra_authorize_app_' . $this->slug ) ) {
			return;
		}

		$this->get_access_token( $code );
	}

	public function get_authorization_url( $processed_data_array = array() ) {}

	public function get_access_token( $code = '' ) {}

	public function number_format( $count ) {
		if ( is_numeric( $count ) ) {
			return number_format( $count );
		} else {
			return $count;
		}
	}

	public function get_followers_text( $count ) {
		return esc_html( sprintf( _n( '%s follower', '%s followers', $count, 'extra' ), $this->number_format( $count ) ) );
	}

	public function populate_settings( $settings_values ) {
		$this->fields = $this->get_fields();
		foreach ( $this->fields as $field_name => $field ) {
			$this->fields[ $field_name ]['value'] = $settings_values[ $field_name ];
		}
	}

	public function get_settings() {
		return $this->fields;
	}

	public function get_count() {
		$transient_name = $this->_transient_name();

		if ( false === ( $this->count = get_transient( $transient_name ) ) ) {
			$this->count = $this->fetch_count();
			$transient_expiration = $this->count ? et_get_option( 'social_followers_transient_expiration', ( HOUR_IN_SECONDS * 3 ) ) : HOUR_IN_SECONDS;
			set_transient( $transient_name, $this->count, $transient_expiration );
		}

		return $this->count;
	}

	// retrieve the api data if network was previously authorized in Monarch or Extra theme

	public function get_default_api_data( $field_id ) {
		$monarch_options = get_option( 'et_monarch_options' ) ? get_option( 'et_monarch_options' ) : array();
		$social_follow_api = et_get_option( 'social_follow_api', array() );
		$option_name = $this->slug . '_' . $field_id;

		// get the saved URL and username from Monarch.
		// Note: field names in Monarch are different than in this widget, username = URL, client_id = username
		if ( in_array( $field_id, array( 'username', 'client_id' ) ) ) {
			$current_network_index = false;

			if ( empty( $monarch_options['follow_networks_networks_sorting']['class'] ) ) {
				return '';
			}
			// get the index number of network in Monarch options if exists
			foreach ( $monarch_options['follow_networks_networks_sorting']['class'] as $index => $network_name ) {
				if ( $network_name === $this->slug ) {
					$current_network_index = $index;
				}
			}

			if ( false === $current_network_index ) {
				return '';
			}

			return isset( $monarch_options['follow_networks_networks_sorting'][ $field_id ][ $current_network_index ] ) ? $monarch_options['follow_networks_networks_sorting'][ $field_id ][ $current_network_index ] : '';
		}

		// return saved value from Extra options if option was saved in Extra
		if ( ! empty( $social_follow_api[ $option_name ] ) ) {
			return $social_follow_api[ $option_name ];
		}

		// import and return the value from Monarch plugin if exists
		$monarch_option_prefix = 'facebook' === $this->slug ? 'general_main_' : 'follow_networks_';
		$monarch_option_name = $monarch_option_prefix . $option_name;
		if ( isset( $monarch_options[ $monarch_option_name ] ) ) {
			// import access token if exists in monarch and doesn't exist in Extra theme yet
			if ( isset( $this->authorization_required ) && $this->authorization_required && empty( $social_follow_api[ $this->slug . '_access_token' ] ) && ! empty( $monarch_options['access_tokens'][ $this->slug ] ) ) {
				$social_follow_api[ $option_name ] = $monarch_options[ $monarch_option_name ];
				$social_follow_api[ $this->slug . '_access_token' ] = $monarch_options['access_tokens'][ $this->slug ];
				et_update_option( 'social_follow_api', $social_follow_api );
			}

			return $monarch_options[ $monarch_option_name ];
		}

		return '';
	}

	private function _transient_name() {
		$settings = json_encode( $this->get_settings() );
		$settings_hash = sha1( $settings );
		$transient_name = $this->_transient_prefix . $this->slug . '-' . $settings_hash;
		$transient_name = substr( $transient_name, 0, 45 );
		return $transient_name;
	}

	static function get_networks() {
		return self::$_networks;
	}

}

class ET_Facebook_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Facebook', 'extra' );
		$this->slug = 'facebook';
		$this->authorization_required = true;
	}

	function get_fields() {
		$fields = array(
			'name'   => array(
				'label'       => esc_html__( 'Page Name', 'extra' ),
				'type'        => 'text',
				'default'     => $this->get_default_api_data( 'client_id' ),
				'description' => esc_html__( 'The name of your facebook page, not the full URL. If your facebook url is: "http://www.facebook.com/elegantthemes", then your page name is: "elegantthemes"', 'extra' ),
			),
			'id'     => array(
				'label'               => esc_html__( 'App ID', 'extra' ),
				'type'                => 'password',
				'default'             => $this->get_default_api_data( 'id' ),
				'authorization_field' => true,
				'description'         => esc_html__( 'Enter the App ID', 'extra' ),
			),
			'secret' => array(
				'label'               => esc_html__( 'App Secret', 'extra' ),
				'type'                => 'password',
				'default'             => $this->get_default_api_data( 'secret' ),
				'authorization_field' => true,
				'description'         => esc_html__( 'Enter the App Secret', 'extra' ),
			),
		);
		return $fields;
	}

	public function get_followers_text( $count ) {
		return esc_html( sprintf( _n( '%s like', '%s likes', $count, 'extra' ), $this->number_format( $count ) ) );
	}

	function get_authorization_url( $processed_data_array = array() ) {
		$authorization_url = sprintf(
			'https://www.facebook.com/dialog/oauth?response_type=code&scope=public_profile&state=%1$s&client_id=%2$s&redirect_uri=%3$s',
			"facebook_" . wp_create_nonce( "et_extra_authorize_app_facebook" ),
			sanitize_text_field( $processed_data_array['id'] ),
			rawurlencode( esc_url( admin_url( 'widgets.php' ) ) )
		);

		et_update_option( 'facebook_id', $processed_data_array['id'] );
		et_update_option( 'facebook_secret', $processed_data_array['secret'] );

		return $authorization_url;
	}

	function get_access_token( $code = '' ) {
		$access_token_url = 'https://graph.facebook.com/v2.4/oauth/access_token';
		$redirect_url = admin_url( 'widgets.php' );
		$social_follow_api = et_get_option( 'social_follow_api', array() );

		// Exchange an authotization code for an access token
		$request = wp_remote_post( $access_token_url, array(
			'method'  => 'POST',
			'timeout' => 30,
			'body'    => array(
				'client_id'     => sanitize_text_field( isset( $social_follow_api['facebook_id'] ) ? $social_follow_api['facebook_id'] : '' ),
				'client_secret' => sanitize_text_field( isset( $social_follow_api['facebook_secret'] ) ? $social_follow_api['facebook_secret'] : '' ),
				'grant_type'    => 'authorization_code',
				'code'          => sanitize_text_field( $code ),
				'redirect_uri'  => esc_url( $redirect_url ),
			),
		) );

		if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) == 200 ) {
			$response = json_decode( wp_remote_retrieve_body( $request ) );

			// If we received a valid access token, update the access_token option
			if ( isset( $response->access_token ) ) {
				$social_follow_api['facebook_access_token'] = sanitize_text_field( $response->access_token );
				et_update_option( 'social_follow_api', $social_follow_api );
			}
		}
	}

	function fetch_count() {
		$settings = $this->get_settings();
		$social_follow_api = et_get_option( 'social_follow_api', array() );
		$token = isset( $social_follow_api['facebook_access_token'] ) ? $social_follow_api['facebook_access_token'] : '';
		$page_name = $settings['name']['value'];

		if ( '' === $token || '' === $page_name ) {
			return false;
		}

		$url = sprintf( 'https://graph.facebook.com/v2.4/?id=%1$s&access_token=%2$s&fields=likes', $page_name, $token );

		$response_data = wp_remote_get( esc_url_raw( $url ) );

		if ( is_wp_error( $response_data ) || wp_remote_retrieve_response_code( $response_data ) != 200 ) {
			return false;
		}

		$data = wp_remote_retrieve_body( $response_data );
		$data = json_decode( $data );

		return isset( $data->likes ) ? $data->likes : false;
	}

}
new ET_Facebook_Social_Followers;

class ET_Google_Plus_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Google+', 'extra' );
		$this->slug = 'googleplus';
	}

	function get_fields() {
		$fields = array(
			'userId'  => array(
				'label'       => esc_html__( 'Google+ User Id', 'extra' ),
				'type'        => 'text',
				'default'     => $this->get_default_api_data( 'client_id' ),
				'description' => esc_html__( 'You can find the User Id at the end of your Google+ URL. For example if the URL was: http://plus.google.com/+elegentthemes, the User Id would be: +elegenthemes', 'extra' ),
			),
			'api_key' => array(
				'label'               => esc_html__( 'API Key', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'api_key' ),
				'description'         => sprintf( et_get_safe_localization( __( 'Google API key. You can find all the info on how to obtain one here: %s', 'extra' ) ), '<a target="_blank" href="https://developers.google.com/+/api/oauth#apikey">https://developers.google.com/+/api/oauth#apikey</a>' ),
			),
		);
		return $fields;
	}

	public function get_followers_text( $count ) {
		return esc_html( sprintf( _n( '%s +1', '%s +1s', $count, 'extra' ), $this->number_format( $count ) ) );
	}

	function fetch_count() {
		$settings = $this->get_settings();

		$url = 'https://www.googleapis.com/plus/v1/people/'. $settings['userId']['value'] .'?fields=plusOneCount&key=' .$settings['api_key']['value'];

		$response_data = wp_remote_get( esc_url_raw( $url ) );

		$response_json = json_decode( $response_data['body'] );

		return isset( $response_json->plusOneCount ) ? $response_json->plusOneCount : false;
	}

}
new ET_Google_Plus_Social_Followers;

class ET_Youtube_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Youtube', 'extra' );
		$this->slug = 'youtube';
	}

	function get_fields() {
		$fields = array(
			'username' => array(
				'label'   => esc_html__( 'Youtube Username', 'extra' ),
				'type'    => 'text',
				'default' => $this->get_default_api_data( 'client_id' ),
			),
			'api_key'  => array(
				'label'               => esc_html__( 'API Key', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'api_key' ),
				'description'         => sprintf( et_get_safe_localization( __( 'Google API key. You can find all the info on how to obtain one here: %s', 'extra' ) ), '<a target="_blank" href="https://developers.google.com/+/api/oauth#apikey">https://developers.google.com/+/api/oauth#apikey</a>' ),
			),
		);
		return $fields;
	}

	public function get_followers_text( $count ) {
		return esc_html( sprintf( _n( '%s subscriber', "%s subscribers", $count, 'extra' ), $this->number_format( $count ) ) );
	}

	function fetch_count() {
		$settings = $this->get_settings();

		$url = 'https://www.googleapis.com/youtube/v3/channels/?forUsername='. $settings['username']['value'] .'&part=statistics&key=' .$settings['api_key']['value'];

		$response_data = wp_remote_get( esc_url_raw( $url ) );

		$response_json = json_decode( $response_data['body'] );

		return isset( $response_json->items[0] ) ? $response_json->items[0]->statistics->subscriberCount : false;
	}

}
new ET_Youtube_Social_Followers;

class ET_Vimeo_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Vimeo', 'extra' );
		$this->slug = 'vimeo';
	}

	function get_fields() {
		$fields = array(
			'channel_name' => array(
				'label' => esc_html__( 'Channel Name', 'extra' ),
				'type'  => 'text',
			),
		);
		return $fields;
	}

	public function get_followers_text( $count ) {
		return esc_html( sprintf( _n( '%s subscriber', "%s subscribers", $count, 'extra' ), $this->number_format( $count ) ) );
	}

	function fetch_count() {
		$settings = $this->get_settings();

		$url = 'http://vimeo.com/api/v2/channel/' . $settings['channel_name']['value'] . '/info.json';

		$response_data = wp_remote_get( esc_url_raw( $url ) );

		$response_json = json_decode( $response_data['body'] );

		return isset( $response_json->total_subscribers ) ? $response_json->total_subscribers : false;
	}

}
new ET_Vimeo_Social_Followers;


class ET_Tumblr_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Tumblr', 'extra' );
		$this->slug = 'tumblr';
	}

	function get_fields() {
		$fields = array(
			'consumer_key'        => array(
				'label'               => esc_html__( 'Consumer Key', 'extra' ),
				'type'                => 'password',
				'default'             => $this->get_default_api_data( 'consumer_key' ),
				'authorization_field' => true,
				'description'         => sprintf( et_get_safe_localization( __( ' You can find all the info on how to obtain all the required credentials here: %s', 'extra' ) ), '<a href="http://www.tumblr.com/oauth/apps" target="_blank">http://www.tumblr.com/oauth/apps</a>' ),
			),
			'consumer_secret'     => array(
				'label'               => esc_html__( 'Consumer Secret', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'consumer_secret' ),
			),
			'access_token'        => array(
				'label'               => esc_html__( 'Access Token', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'access_token' ),
			),
			'access_token_secret' => array(
				'label'               => esc_html__( 'Access Token Secret', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'access_token_secret' ),
			),
		);
		return $fields;
	}

	public function get_followers_text( $count ) {
		return esc_html( sprintf( _n( '%s like', "%s likes", $count, 'extra' ), $this->number_format( $count ) ) );
	}

	function fetch_count() {
		require_once dirname( __FILE__ ) . '/ext/tumblr_oauth.php';

		$settings = $this->get_settings();

		$tumblrConnection = new TumblrOAuth(
			$settings['consumer_key']['value'],
			$settings['consumer_secret']['value'],
			$settings['access_token']['value'],
			$settings['access_token_secret']['value']
		);

		$tumblrData = $tumblrConnection->get( 'user/info' );

		return isset( $tumblrData->response ) ? $tumblrData->response->user->likes : false;
	}

}
new ET_Tumblr_Social_Followers;

class ET_Twitter_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Twitter', 'extra' );
		$this->slug = 'twitter';
	}

	function get_fields() {
		$fields = array(
			'api_key'      => array(
				'label'               => esc_html__( 'Consumer Key', 'extra' ),
				'type'                => 'password',
				'default'             => $this->get_default_api_data( 'api_key' ),
				'authorization_field' => true,
				'description'         => sprintf( et_get_safe_localization( __( 'You can find all the info on how to obtain all the needed API credentials here: %s', 'extra' ) ), '<a href="https://dev.twitter.com/oauth/overview/application-owner-access-tokens" target="_blank">https://dev.twitter.com/oauth/overview/application-owner-access-tokens</a>' ),
			),
			'api_secret'   => array(
				'label'               => esc_html__( 'Consumer Secret', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'api_secret' ),
			),
			'token'        => array(
				'label'               => esc_html__( 'Access Token', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'token' ),
			),
			'token_secret' => array(
				'label'               => esc_html__( 'Access Token Secret', 'extra' ),
				'type'                => 'password',
				'authorization_field' => true,
				'default'             => $this->get_default_api_data( 'token_secret' ),
			),
		);
		return $fields;
	}

	function fetch_count() {
		require_once dirname( __FILE__ ) . '/ext/twitter_oauth.php';

		$settings = $this->get_settings();

		$twitterConnection = new TwitterOAuth(
			$settings['api_key']['value'],
			$settings['api_secret']['value'],
			$settings['token']['value'],
			$settings['token_secret']['value']
		);

		$twitterData = $twitterConnection->get( 'statuses/user_timeline' );

		return !isset( $twitterData->errors ) ? $twitterData[0]->user->followers_count : false;
	}

}
new ET_Twitter_Social_Followers;

class ET_Pinterest_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Pinterest', 'extra' );
		$this->slug = 'pinterest';
	}

	function get_fields() {
		$fields = array(
			'name' => array(
				'label'   => esc_html__( 'User Name', 'extra' ),
				'type'    => 'text',
				'default' => $this->get_default_api_data( 'client_id' ),
			),
		);
		return $fields;
	}

	function fetch_count() {
		$settings = $this->get_settings();

		$url = 'http://www.pinterest.com/' . $settings['name']['value'];
		$tags = get_meta_tags( $url );

		return isset( $tags['pinterestapp:followers'] ) ? $tags['pinterestapp:followers'] : false;
	}

}
new ET_Pinterest_Social_Followers;

class ET_Instagram_Social_Followers extends ET_Social_Followers {

	function init() {
		$this->name = esc_html__( 'Instagram', 'extra' );
		$this->slug = 'instagram';
		$this->authorization_required = true;
	}

	function get_fields() {
		$fields = array(
			'id'     => array(
				'label'               => esc_html__( 'App ID', 'extra' ),
				'type'                => 'password',
				'default'             => $this->get_default_api_data( 'id' ),
				'authorization_field' => true,
				'description'         => esc_html__( 'Enter the App ID', 'extra' ),
			),
			'secret' => array(
				'label'               => esc_html__( 'App Secret', 'extra' ),
				'type'                => 'password',
				'default'             => $this->get_default_api_data( 'secret' ),
				'authorization_field' => true,
				'description'         => esc_html__( 'Enter the App Secret', 'extra' ),
			),
		);
		return $fields;
	}

	function get_authorization_url( $processed_data_array = array() ) {
		$authorization_url = sprintf(
			'https://api.instagram.com/oauth/authorize/?response_type=code&scope=basic&state=%1$s&client_id=%2$s&redirect_uri=%3$s',
			"instagram_" . wp_create_nonce( "et_extra_authorize_app_instagram" ),
			sanitize_text_field( $processed_data_array['id'] ),
			rawurlencode( esc_url( admin_url( 'widgets.php' ) ) )
		);

		et_update_option( 'instagram_id', $processed_data_array['id'] );
		et_update_option( 'instagram_secret', $processed_data_array['secret'] );

		return $authorization_url;
	}

	function get_access_token( $code = '' ) {
		$access_token_url = 'https://api.instagram.com/oauth/access_token';
		$redirect_url = admin_url( 'widgets.php' );
		$social_follow_api = et_get_option( 'social_follow_api', array() );

		// Exchange an authotization code for an access token
		$request = wp_remote_post( $access_token_url, array(
			'method'  => 'POST',
			'timeout' => 30,
			'body'    => array(
				'client_id'     => sanitize_text_field( isset( $social_follow_api['instagram_id'] ) ? $social_follow_api['instagram_id'] : '' ),
				'client_secret' => sanitize_text_field( isset( $social_follow_api['instagram_secret'] ) ? $social_follow_api['instagram_secret'] : '' ),
				'grant_type'    => 'authorization_code',
				'code'          => sanitize_text_field( $code ),
				'redirect_uri'  => esc_url( $redirect_url ),
			),
		) );

		if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) == 200 ) {
			$response = json_decode( wp_remote_retrieve_body( $request ) );

			// If we received a valid access token, update the access_token option
			if ( isset( $response->access_token ) ) {
				$social_follow_api['instagram_access_token'] = sanitize_text_field( $response->access_token );
				et_update_option( 'social_follow_api', $social_follow_api );
			}
		}
	}

	function fetch_count() {
		$settings = $this->get_settings();
		$social_follow_api = et_get_option( 'social_follow_api', array() );
		$token = isset( $social_follow_api['instagram_access_token'] ) ? $social_follow_api['instagram_access_token'] : '';

		$url = sprintf( 'https://api.instagram.com/v1/users/self/?access_token=%1$s', $token );

		$response_data = wp_remote_get( esc_url_raw( $url ) );

		if ( is_wp_error( $response_data ) || wp_remote_retrieve_response_code( $response_data ) != 200 ) {
			return false;
		}

		$data = wp_remote_retrieve_body( $response_data );
		$data = json_decode( $data );

		return isset( $data->data ) ? $data->data->counts->followed_by : false;
	}

}
new ET_Instagram_Social_Followers;

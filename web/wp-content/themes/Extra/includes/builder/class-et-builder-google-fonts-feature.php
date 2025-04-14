<?php
/**
 * Google_Fonts feature class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Handles Google_Fonts feature.
 *
 * @since 4.10.0
 */
class ET_Builder_Google_Fonts_Feature extends ET_Builder_Global_Feature_Base {

	/**
	 * `ET_Builder_Google_Fonts_Feature` instance.
	 *
	 * @var ET_Builder_Google_Fonts_Feature
	 */
	private static $_instance;

	const CACHE_META_KEY = '_et_builder_gf_feature_cache';

	/**
	 * Returns instance of the class.
	 *
	 * @since 4.10.0
	 */
	public function __construct() {
		// need to set this filter up front, bc as soon as the parent __construct
		// is called then it will prime the cache so it needs
		// this cache index filter to be setup right away.
		add_filter( 'et_global_feature_cache_index_items', [ 'ET_Builder_Google_Fonts_Feature', 'cache_index_items' ], 10, 2 );

		parent::__construct();

		if ( self::enabled() ) {
			self::setup_transient();
		}
	}

	/**
	 * Initialize ET_Builder_Google_Fonts_Feature class.
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Setup transient to purge cache once a day.
	 *
	 * @since 4.10.0
	 */
	public static function setup_transient() {
		if ( ! get_transient( self::CACHE_META_KEY ) ) {
			self::purge_cache();

			set_transient( self::CACHE_META_KEY, '1', DAY_IN_SECONDS );
		}
	}

	/**
	 * Add to cache index items.
	 *
	 * @since 4.10.0
	 *
	 * @param array  $items Assoc array of cache index items.
	 * @param string $key   The cache meta key that the cache index items belong to.
	 *
	 * @return array  $items Assoc array of cache index items.
	 */
	public static function cache_index_items( $items, $key ) {
		global $shortname;

		if ( self::CACHE_META_KEY === $key ) {
			$items['enable_all_character_sets'] = (string) et_get_option( "{$shortname}_gf_enable_all_character_sets", 'false' );
		}

		return $items;
	}

	/**
	 * Determines if an option is enabled.
	 *
	 * @since 4.10.0
	 *
	 * @param string $sub_option Google Fonts Sub Option.
	 *
	 * @return bool Whether the sub option is enabled.
	 */
	public function is_option_enabled( $sub_option = '' ) {
		global $shortname;

		if ( ! self::enabled() ) {
			return false;
		}

		if ( et_is_builder_plugin_active() ) {
			$options      = get_option( 'et_pb_builder_options', array() );
			$option_state = isset( $options[ 'performance_main_' . $sub_option ] ) ? $options[ 'performance_main_' . $sub_option ] : 'on';
		} else {
			$option_state = et_get_option( $shortname . '_' . $sub_option, 'on' );
		}
		
		/**
		 * Filters the option state before it's returned.
		 * Allows other plugins to enable or disable a Google Font feature from code.
		 *
		 * @since 4.19.1
		 *
		 * @param bool   $is_enabled Whether the sub option is enabled.
		 * @param string $sub_option Google Fonts sub option.
		 */
		return apply_filters( 'et_builder_google_fonts_is_enabled', 'on' === $option_state, $sub_option );
	}
	
	/**
	 * Builds the full URL to the Google Font css file to load the fonts
	 * provided by the parameter.
	 *
	 * @since 4.19.1
	 *
	 * @param array $google_fonts_url_args An array containing query-parameters.
	 *
	 * @return string The full URL to the Google Fonts resource.
	 */
	public function get_google_fonts_url( $google_fonts_url_args ) {
		$url = add_query_arg( $google_fonts_url_args, 'https://fonts.googleapis.com/css' );
		$url = esc_url_raw( $url );
		
		/**
		 * Filters the URL of the requested Google Font CSS file.
		 * Allows swapping Google Fonts with other font sources.
		 *
		 * @since 4.19.1
		 *
		 * @param string $url The URL to the Google Fonts CSS file to fetch.
		 */
		return apply_filters( 'et_builder_google_fonts_get_url', $url );		
	}

	/**
	 * Get User Agents.
	 *
	 * @since 4.10.0
	 *
	 * @return array[string] List of user agents.
	 */
	protected function _get_user_agents() {
		$limit_support = $this->is_option_enabled( 'limit_google_fonts_support_for_legacy_browsers' );

		$uas = [];

		if ( ! $limit_support ) {
			/**
			 * IE9 Compat Modes
			 */
			$uas['eot'] = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)';

			/**
			 * Legacy iOS
			 */
			$uas['svg'] = 'Mozilla/4.0 (iPad; CPU OS 4_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/4.1 Mobile/9A405 Safari/7534.48.3';
		}

		/**
		 * Safari, Android, iOS
		 */
		$uas['ttf'] = 'Mozilla/5.0 (Unknown; Linux x86_64) AppleWebKit/538.1 (KHTML, like Gecko) Safari/538.1 Daum/4.1';

		/**
		 * Modern Browsers.
		 * Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+
		 */
		$uas['woff'] = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0';

		/**
		 * Super Modern Browsers.
		 * hrome 26+, Opera 23+, Firefox 39+
		 */
		$uas['woff2'] = 'Mozilla/5.0 (Windows NT 6.3; rv:39.0) Gecko/20100101 Firefox/39.0';

		/**
		 * Filters which user agents to use to get google fonts.
		 *
		 * @since 4.10.0
		 */
		return apply_filters( 'et_builder_google_fonts_user_agents', $uas );
	}

	/**
	 * Fetch CSS file contents from google fonts URL.
	 *
	 * @since 4.10.0
	 *
	 * @param string $url The Google Fonts URL to fetch the contents for.
	 *
	 * @return string $url CSS file contents.
	 */
	public function fetch( $url ) {
		$all_contents = '/* Original: ' . esc_url( $url ) . ' */';

		foreach ( $this->_get_user_agents() as $ua ) {
			$response = wp_remote_get(
				esc_url_raw( $url ),
				[
					'user-agent' => $ua,
				]
			);

			// Return if there is no response or if there is an error.
			if ( ! is_array( $response ) || is_wp_error( $response ) ) {
				return false;
			}

			$contents = wp_remote_retrieve_body( $response );

			$contents = '/* User Agent: ' . $ua . ' */' . "\n" . $contents;

			$all_contents .= "\n\n" . $contents;
		}

		/**
		 * Filter the unminified CSS file contents after loading it from Googles servers.
		 *
		 * @since 4.19.1
		 *
		 * @param string $all_contents The concatenated CSS files content that was fetched from Google.
		 * @param string $url          The Google Fonts URL that was fetched.
		 */
		$all_contents = apply_filters( 'et_builder_google_fonts_fetch_content', $all_contents, $url );

		$all_contents = self::minify( $all_contents );
		
		return $all_contents;
	}

	/**
	 * Minify CSS string.
	 *
	 * @since 4.10.0
	 *
	 * @param string $data Multiline CSS data.
	 *
	 * @return string Minifed CSS data.
	 */
	public static function minify( $data ) {
		$data = trim( $data );
		$data = preg_replace( '/\n/smi', '', $data );
		$data = preg_replace( '/\s\s/smi', '', $data );

		return $data;
	}
}

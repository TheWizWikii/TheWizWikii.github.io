<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * All In One SEO Pack Compatibility
 */
if ( is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ) || is_plugin_active( 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php' ) ) {
	class Extra_Plugin_AIOSEP_Compat {
		public static $instance;
		protected $aioseop_options;
		protected $show_on_front;
		protected $use_static_home_info;

		/**
		 * Gets the instance of the plugin
		 */
		public static function instance(){
			if ( null === self::$instance ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		function __construct() {
			$this->aioseop_options = get_option( 'aioseop_options' );
			$this->show_on_front   = get_option( 'show_on_front' );
			$this->use_static_home_info = $this->get_plugin_option( 'aiosp_use_static_home_info' );

			$filter_prefix = $this->get_plugin_filter_prefix();

			add_filter( "{$filter_prefix}_title", array( $this, 'title_compat' ) );
			add_filter( "{$filter_prefix}_description", array( $this, 'description_compat' ) );
			add_filter( "{$filter_prefix}_keywords", array( $this, 'keywords_compat' ) );
		}

		/**
		 * Get plugin version.
		 *
		 * @since ??
		 *
		 * @return string
		 */
		public function get_plugin_version() {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/all-in-one-seo-pack/all_in_one_seo_pack.php', false );

			if ( ! isset( $plugin_data['Version'] ) ) {
				return false;
			}

			return $plugin_data['Version'];
		}

		/**
		 * Get plugin filter prefix.
		 *
		 * @since ??
		 *
		 * @return string
		 */
		public function get_plugin_filter_prefix() {
			$filter_prefix = 'aioseo';

			if ( version_compare( $this->get_plugin_version(), '4.0.0', '<' ) ) {
				$filter_prefix = 'aioseop';
			}

			return $filter_prefix;
		}

		/**
		 * Get AIOSEP option based on its key
		 *
		 * @param  string option key
		 * @param  mixed  default value
		 * @return mixed
		 */
		function get_plugin_option( $key, $default = false ) {
			if ( isset( $this->aioseop_options[ $key ] ) ) {
				return $this->aioseop_options[ $key ];
			}

			return $default;
		}

		/**
		 * Assess whether current page is a page that need to be overriden by compatibility file. Known troubling condition:
		 * - Homepage where AIESOP homepage setting is used + Extra layout is activated to be shown on the home page
		 *
		 * @return bool
		 */
		function use_home_compat() {
			return is_home() && '1' !== $this->use_static_home_info && 'layout' === $this->show_on_front;
		}

		/**
		 * Modify title tag, filtered by known condition
		 *
		 * @param  string existing title tag value
		 * @return string modified title tag value
		 */
		function title_compat( $title ) {
			if ( $this->use_home_compat() && $this->get_plugin_option( 'aiosp_home_title' ) ) {
				$title = esc_attr( $this->get_plugin_option( 'aiosp_home_title' ) );
			}

			return $title;
		}

		/**
		 * Modify description meta tag, filtered by known condition
		 *
		 * @param  string existing description meta tag value
		 * @return string modified description meta tag value
		 */
		function description_compat( $description ) {
			if ( $this->use_home_compat() && $this->get_plugin_option( 'aiosp_home_description' ) ) {
				$description = esc_attr( $this->get_plugin_option( 'aiosp_home_description' ) );
			}

			return $description;
		}

		/**
		 * Modify keywords meta tag, filtered by known condition
		 *
		 * @param  string existing keywords meta tag value
		 * @return string modified keywords meta tag value
		 */
		function keywords_compat( $keywords ) {
			if ( $this->use_home_compat() && $this->get_plugin_option( 'aiosp_home_keywords' ) ) {
				$keywords = esc_attr( $this->get_plugin_option( 'aiosp_home_keywords' ) );
			}

			return $keywords;
		}
	}

	/**
	 * Initialize All In One SEO PACK compatibility class
	 */
	Extra_Plugin_AIOSEP_Compat::instance();
}


/**
 * Only load if YOAST SEO plugin is activated
 */
if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ) {
	/**
	 * Force YOAST SEO to use title and meta description options on Dashboard > Titles & Metas > Homepage
	 * when Category Builder is used for homepage.
	 * @return string
	 */
	function extra_yoast_seo_show_titledesc_options( $value, $option ) {
		global $pagenow;

		if ( is_admin() && 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'wpseo_titles' === $_GET['page'] && 'layout' === $value ) {
			$value = 'posts';
		}

		return $value;
	}
	add_filter( 'option_show_on_front', 'extra_yoast_seo_show_titledesc_options', 10, 2 );

	/**
	 * Get values from WPSEO_Options based on given key if current page is
	 * homepage and category builder is used on homepage
	 * @return string
	 */
	function extra_yoast_seo_homepage_adjustment( $option_key, $default ) {
		if ( class_exists( 'WPSEO_Options' ) && is_home() && 'layout' === get_option( 'show_on_front' ) ) {
			$all_options = WPSEO_Options::get_all();
		}

		$value = isset( $all_options[$option_key] ) ? $all_options[$option_key] : $default;

		return wpseo_replace_vars( $value, array() );
	}

	/**
	 * Adjusting homepage's title tag
	 * @return string
	 */
	function extra_yoast_seo_homepage_title_adjustment( $title ) {
		return extra_yoast_seo_homepage_adjustment( 'title-home-wpseo', $title );
	}
	add_filter( 'wpseo_title', 'extra_yoast_seo_homepage_title_adjustment' );

	/**
	 * Adjusting homepage's meta description
	 * @return string
	 */
	function extra_yoast_seo_homepage_meta_desc_adjustment( $desc ) {
		return extra_yoast_seo_homepage_adjustment( 'metadesc-home-wpseo', $desc );
	}
	add_filter( 'wpseo_metadesc', 'extra_yoast_seo_homepage_meta_desc_adjustment' );
}
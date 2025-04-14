<?php
/**
 * Shortcode Manager Class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Handles module shortcodes.
 *
 * @since 4.10.0
 */
class ET_Builder_Module_Shortcode_Manager {

	/**
	 * Modules container.
	 *
	 * @access public
	 * @var array
	 */
	public static $modules_map = [];

	/**
	 * WooCommerce modules container.
	 *
	 * @access public
	 * @var array
	 */
	public static $woo_modules_map = [];

	/**
	 * Structural Modules container.
	 *
	 * @access public
	 * @var array
	 */
	public static $structural_modules_map = [];

	/**
	 * Initialize shortcode manager class.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function init() {
		$this->register_modules();
		$this->register_fullwidth_modules();
		$this->register_structural_modules();
		$this->register_woo_modules();
		$this->register_shortcode();
	}

	/**
	 * Get modules map.
	 *
	 * @since 4.14.5
	 *
	 * @param string $type Modules map type.
	 *
	 * @return array Modules map.
	 */
	public static function get_modules_map( $type = false ) {
		if ( 'woo_modules' === $type ) {
			return self::$woo_modules_map;
		}

		if ( 'structural_modules' === $type ) {
			return self::$structural_modules_map;
		}

		return self::$modules_map;
	}

	/**
	 * Start registering shortcodes.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_shortcode() {
		// is_saving_cache or other scenarios where we need to load everything.
		if ( et_builder_should_load_all_module_data() ) {
			$this->register_all_shortcodes();
		} else {
			$this->register_lazy_shortcodes();
		}
	}

	/**
	 * Register normal modules.
	 *
	 * Modules dependent to each other will have
	 * to have a dependency parameter on them.
	 * Eg : et_pb_accordion_item needs et_pb_toggle so we
	 * have to pass add that on the `deps` key.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_modules() {
		$modules = [
			'et_pb_accordion'                   => [
				'classname' => 'ET_Builder_Module_Accordion',
			],
			'et_pb_accordion_item'              => [
				'classname' => 'ET_Builder_Module_Accordion_Item',
				'deps'      => array( 'et_pb_toggle' ),
			],
			'et_pb_audio'                       => [
				'classname' => 'ET_Builder_Module_Audio',
			],
			'et_pb_counters'                    => [
				'classname' => 'ET_Builder_Module_Bar_Counters',
			],
			'et_pb_counter'                     => [
				'classname' => 'ET_Builder_Module_Bar_Counters_Item',
			],
			'et_pb_blog'                        => [
				'classname' => 'ET_Builder_Module_Blog',
			],
			'et_pb_blurb'                       => [
				'classname' => 'ET_Builder_Module_Blurb',
			],
			'et_pb_button'                      => [
				'classname' => 'ET_Builder_Module_Button',
			],
			'et_pb_circle_counter'              => [
				'classname' => 'ET_Builder_Module_Circle_Counter',
			],
			'et_pb_code'                        => [
				'classname' => 'ET_Builder_Module_Code',
			],
			'et_pb_comments'                    => [
				'classname' => 'ET_Builder_Module_Comments',
			],
			'et_pb_contact_form'                => [
				'classname' => 'ET_Builder_Module_Contact_Form',
			],
			'et_pb_contact_field'               => [
				'classname' => 'ET_Builder_Module_Contact_Form_Item',
			],
			'et_pb_countdown_timer'             => [
				'classname' => 'ET_Builder_Module_Countdown_Timer',
			],
			'et_pb_cta'                         => [
				'classname' => 'ET_Builder_Module_Cta',
			],
			'et_pb_divider'                     => [
				'classname' => 'ET_Builder_Module_Divider',
			],
			'et_pb_filterable_portfolio'        => [
				'classname' => 'ET_Builder_Module_Filterable_Portfolio',
			],
			'et_pb_gallery'                     => [
				'classname' => 'ET_Builder_Module_Gallery',
			],
			'et_pb_image'                       => [
				'classname' => 'ET_Builder_Module_Image',
			],
			'et_pb_login'                       => [
				'classname' => 'ET_Builder_Module_Login',
			],
			'et_pb_map'                         => [
				'classname' => 'ET_Builder_Module_Map',
			],
			'et_pb_map_pin'                     => [
				'classname' => 'ET_Builder_Module_Map_Item',
			],
			'et_pb_menu'                        => [
				'classname' => 'ET_Builder_Module_Menu',
			],
			'et_pb_number_counter'              => [
				'classname' => 'ET_Builder_Module_Number_Counter',
			],
			'et_pb_portfolio'                   => [
				'classname' => 'ET_Builder_Module_Portfolio',
			],
			'et_pb_post_content'                => [
				'classname' => 'ET_Builder_Module_PostContent',
			],
			'et_pb_post_slider'                 => [
				'classname' => 'ET_Builder_Module_Post_Slider',
			],
			'et_pb_post_title'                  => [
				'classname' => 'ET_Builder_Module_Post_Title',
			],
			'et_pb_post_nav'                    => [
				'classname' => 'ET_Builder_Module_Posts_Navigation',
			],
			'et_pb_pricing_tables'              => [
				'classname' => 'ET_Builder_Module_Pricing_Tables',
			],
			'et_pb_pricing_table'               => [
				'classname' => 'ET_Builder_Module_Pricing_Tables_Item',
			],
			'et_pb_search'                      => [
				'classname' => 'ET_Builder_Module_Search',
			],
			'et_pb_sidebar'                     => [
				'classname' => 'ET_Builder_Module_Sidebar',
			],
			'et_pb_signup'                      => [
				'classname' => 'ET_Builder_Module_Signup',
			],
			'et_pb_signup_custom_field'         => [
				'classname'    => 'ET_Builder_Module_Signup_Item',
				'preload_deps' => array( 'et_pb_contact_field' ),
			],
			'et_pb_slider'                      => [
				'classname' => 'ET_Builder_Module_Slider',
			],
			'et_pb_slide'                       => [
				'classname' => 'ET_Builder_Module_Slider_Item',
			],
			'et_pb_social_media_follow'         => [
				'classname' => 'ET_Builder_Module_Social_Media_Follow',
			],
			'et_pb_social_media_follow_network' => [
				'classname' => 'ET_Builder_Module_Social_Media_Follow_Item',
			],
			'et_pb_tabs'                        => [
				'classname' => 'ET_Builder_Module_Tabs',
			],
			'et_pb_tab'                         => [
				'classname' => 'ET_Builder_Module_Tabs_Item',
			],
			'et_pb_team_member'                 => [
				'classname' => 'ET_Builder_Module_Team_Member',
			],
			'et_pb_testimonial'                 => [
				'classname' => 'ET_Builder_Module_Testimonial',
			],
			'et_pb_text'                        => [
				'classname' => 'ET_Builder_Module_Text',
			],
			'et_pb_toggle'                      => [
				'classname' => 'ET_Builder_Module_Toggle',
			],
			'et_pb_video'                       => [
				'classname' => 'ET_Builder_Module_Video',
			],
			'et_pb_video_slider'                => [
				'classname' => 'ET_Builder_Module_Video_Slider',
			],
			'et_pb_video_slider_item'           => [
				'classname' => 'ET_Builder_Module_Video_Slider_Item',
			],
			'et_pb_icon'                        => [
				'classname' => 'ET_Builder_Module_Icon',
			],
			'et_pb_heading'                     => [
				'classname' => 'ET_Builder_Module_Heading',
			],
		];

		/**
		 * Filters built-in Divi Builder module class names.
		 *
		 * 3rd-party plugins can use this filter to override Divi Builder modules.
		 *
		 * NOTE: Overriding built-in modules is not ideal and should only be used as a temporary solution.
		 * The recommended approach for achieving this is using the official API:
		 * https://www.elegantthemes.com/documentation/developers/divi-module/how-to-create-a-divi-builder-module/
		 *
		 * @since 4.11.0
		 *
		 * @param array $additional_modules Additional modules.
		 */
		$additional_modules = apply_filters( 'et_module_classes', [] );

		self::$modules_map = array_merge( self::$modules_map, $modules, $additional_modules );
	}

	/**
	 * Register fullwidth modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_fullwidth_modules() {
		$modules = [
			'et_pb_fullwidth_code'         => [
				'classname' => 'ET_Builder_Module_Fullwidth_Code',
			],
			'et_pb_fullwidth_header'       => [
				'classname' => 'ET_Builder_Module_Fullwidth_Header',
			],
			'et_pb_fullwidth_image'        => [
				'classname' => 'ET_Builder_Module_Fullwidth_Image',
			],
			'et_pb_fullwidth_map'          => [
				'classname' => 'ET_Builder_Module_Fullwidth_Map',
			],
			'et_pb_fullwidth_menu'         => [
				'classname' => 'ET_Builder_Module_Fullwidth_Menu',
			],
			'et_pb_fullwidth_portfolio'    => [
				'classname' => 'ET_Builder_Module_Fullwidth_Portfolio',
			],
			'et_pb_fullwidth_post_content' => [
				'classname' => 'ET_Builder_Module_Fullwidth_PostContent',
			],
			'et_pb_fullwidth_post_slider'  => [
				'classname' => 'ET_Builder_Module_Fullwidth_Post_Slider',
			],
			'et_pb_fullwidth_post_title'   => [
				'classname' => 'ET_Builder_Module_Fullwidth_Post_Title',
			],
			'et_pb_fullwidth_slider'       => [
				'classname' => 'ET_Builder_Module_Fullwidth_Slider',
			],
		];

		/**
		 * Filters built-in Divi Builder module class names.
		 *
		 * 3rd-party plugins can use this filter to override Divi Builder modules.
		 *
		 * NOTE: Overriding built-in modules is not ideal and should only be used as a temporary solution.
		 * The recommended approach for achieving this is using the official API:
		 * https://www.elegantthemes.com/documentation/developers/divi-module/how-to-create-a-divi-builder-module/
		 *
		 * @since 4.11.0
		 *
		 * @param array $additional_modules Additional modules.
		 */
		$additional_modules = apply_filters( 'et_fullwidth_module_classes', [] );

		self::$modules_map = array_merge( self::$modules_map, $modules, $additional_modules );
	}

	/**
	 * Register structural modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_structural_modules() {
		$modules = [
			'et_pb_section'   => [
				'classname' => 'ET_Builder_Section',
			],
			'et_pb_row'       => [
				'classname' => 'ET_Builder_Row',
			],
			'et_pb_row_inner' => [
				'classname' => 'ET_Builder_Row_Inner',
			],
			'et_pb_column'    => [
				'classname' => 'ET_Builder_Column',
			],
		];

		/**
		 * Filters built-in Divi Builder module class names.
		 *
		 * 3rd-party plugins can use this filter to override Divi Builder modules.
		 *
		 * NOTE: Overriding built-in modules is not ideal and should only be used as a temporary solution.
		 * The recommended approach for achieving this is using the official API:
		 * https://www.elegantthemes.com/documentation/developers/divi-module/how-to-create-a-divi-builder-module/
		 *
		 * @since 4.11.0
		 *
		 * @param array $additional_modules Additional modules.
		 */
		$additional_modules = apply_filters( 'et_structural_module_classes', [] );

		self::$structural_modules_map = array_merge( $modules, $additional_modules );
	}

	/**
	 * Register woocommerce modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_woo_modules() {
		// Only add wooModules if woo is active.
		if ( ! et_is_woocommerce_plugin_active() ) {
			return;
		}

		$woo_modules = [
			'et_pb_wc_add_to_cart'              => [
				'classname' => 'ET_Builder_Module_Woocommerce_Add_To_Cart',
			],
			'et_pb_wc_additional_info'          => [
				'classname' => 'ET_Builder_Module_Woocommerce_Additional_Info',
			],
			'et_pb_wc_breadcrumb'               => [
				'classname' => 'ET_Builder_Module_Woocommerce_Breadcrumb',
			],
			'et_pb_wc_cart_notice'              => [
				'classname' => 'ET_Builder_Module_Woocommerce_Cart_Notice',
			],
			'et_pb_wc_description'              => [
				'classname' => 'ET_Builder_Module_Woocommerce_Description',
			],
			'et_pb_wc_gallery'                  => [
				'classname' => 'ET_Builder_Module_Woocommerce_Gallery',
			],
			'et_pb_wc_images'                   => [
				'classname' => 'ET_Builder_Module_Woocommerce_Images',
			],
			'et_pb_wc_meta'                     => [
				'classname' => 'ET_Builder_Module_Woocommerce_Meta',
			],
			'et_pb_wc_price'                    => [
				'classname' => 'ET_Builder_Module_Woocommerce_Price',
			],
			'et_pb_wc_rating'                   => [
				'classname' => 'ET_Builder_Module_Woocommerce_Rating',
			],
			'et_pb_wc_related_products'         => [
				'classname' => 'ET_Builder_Module_Woocommerce_Related_Products',
			],
			'et_pb_wc_reviews'                  => [
				'classname' => 'ET_Builder_Module_Woocommerce_Reviews',
			],
			'et_pb_wc_stock'                    => [
				'classname' => 'ET_Builder_Module_Woocommerce_Stock',
			],
			'et_pb_wc_tabs'                     => [
				'classname' => 'ET_Builder_Module_Woocommerce_Tabs',
			],
			'et_pb_wc_title'                    => [
				'classname' => 'ET_Builder_Module_Woocommerce_Title',
			],
			'et_pb_wc_upsells'                  => [
				'classname' => 'ET_Builder_Module_Woocommerce_Upsells',
			],
			'et_pb_wc_cart_products'            => [
				'classname' => 'ET_Builder_Module_Woocommerce_Cart_Products',
			],
			'et_pb_wc_cross_sells'              => [
				'classname' => 'ET_Builder_Module_Woocommerce_Cross_Sells',
			],
			'et_pb_wc_cart_totals'              => [
				'classname' => 'ET_Builder_Module_Woocommerce_Cart_Totals',
			],
			'et_pb_wc_checkout_billing'         => [
				'classname' => 'ET_Builder_Module_Woocommerce_Checkout_Billing',
			],
			'et_pb_wc_checkout_shipping'        => [
				'classname' => 'ET_Builder_Module_Woocommerce_Checkout_Shipping',
			],
			'et_pb_wc_checkout_order_details'   => [
				'classname' => 'ET_Builder_Module_Woocommerce_Checkout_Order_Details',
			],
			'et_pb_wc_checkout_payment_info'    => [
				'classname' => 'ET_Builder_Module_Woocommerce_Checkout_Payment_Info',
			],
			'et_pb_wc_checkout_additional_info' => [
				'classname' => 'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
			],
			'et_pb_shop'                        => [
				'classname' => 'ET_Builder_Module_Shop',
			],
		];

		/**
		 * Filters built-in Divi Builder module class names.
		 *
		 * 3rd-party plugins can use this filter to override Divi Builder modules.
		 *
		 * NOTE: Overriding built-in modules is not ideal and should only be used as a temporary solution.
		 * The recommended approach for achieving this is using the official API:
		 * https://www.elegantthemes.com/documentation/developers/divi-module/how-to-create-a-divi-builder-module/
		 *
		 * @since 4.11.0
		 *
		 * @param array $additional_modules Additional modules.
		 */
		$additional_modules = apply_filters( 'et_woo_module_classes', [] );

		self::$woo_modules_map = $woo_modules;
		self::$modules_map     = array_merge( self::$modules_map, $woo_modules, $additional_modules );
	}

	/**
	 * Register shortcode.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_all_shortcodes() {
		$et_builder_module_files = glob( ET_BUILDER_DIR . 'module/*.php' );
		$et_builder_module_types = glob( ET_BUILDER_DIR . 'module/type/*.php' );

		if ( ! $et_builder_module_files ) {
			return;
		}

		/**
		 * Fires before the builder's module classes are loaded.
		 *
		 * @since 3.0.77
		 */
		do_action( 'et_builder_modules_load' );

		foreach ( $et_builder_module_types as $module_type ) {
			require_once $module_type;
		}

		foreach ( $et_builder_module_files as $module_file ) {
			// skip this all caps version, if it exists.
			// See https://github.com/elegantthemes/Divi/issues/24780.
			if ( 'CTA.php' === basename( $module_file ) ) {
				continue;
			}

			require_once $module_file;
		}

		if ( et_is_woocommerce_plugin_active() ) {
			$et_builder_woocommerce_module_files = glob( ET_BUILDER_DIR . 'module/woocommerce/*.php' );
			foreach ( $et_builder_woocommerce_module_files as $module_type ) {
				require_once $module_type;
			}
		}

		/**
		 * Fires after the builder's module classes are loaded.
		 *
		 * NOTE: this hook only fires on :
		 * - Visual Builder pages
		 * - Front end cache prime initial request
		 *
		 * IT DOES NOT fire on ALL front end requests
		 *
		 * @since 3.0.77
		 * @deprecated ?? Introduced shortcode manager.
		 *                Use {@see et_builder_module_loading}/{@see et_builder_module_loaded}/{@see et_builder_ready} instead.
		 */
		do_action( 'et_builder_modules_loaded' );
	}

	/**
	 * Lazy load shortcodes.
	 *
	 * @since 4.10.0
	 * @access public
	 * @return void
	 */
	public function register_lazy_shortcodes() {
		// A fake handler has to be registered for every shortcode, otherways
		// code will exit early and the pre_do_shortcode_tag hook won't be executed.
		foreach ( self::$modules_map as $shortcode_slug => $module_data ) {
			add_shortcode( $shortcode_slug, '__return_empty_string' );
		}

		// Load modules as needed.
		add_filter( 'pre_do_shortcode_tag', [ $this, 'load_modules' ], 99, 2 );

		// Ensure all our module slugs are always considered, even when not loaded (yet).
		add_filter( 'et_builder_get_module_slugs_by_post_type', [ $this, 'add_module_slugs' ] );

		add_filter( 'et_builder_get_woocommerce_modules', [ $this, 'add_woo_slugs' ] );

		// Ensure all our structural module slugs are always considered, even when not loaded (yet).
		add_filter( 'et_builder_get_structural_module_slugs', [ $this, 'add_structural_module_slugs' ] );

		/**
		 * Fires after the builder's module classes are loaded.
		 *
		 * This hook is fired here for legacy reasons only.
		 * Do not depend on this hook in the future.
		 *
		 * @since 3.0.77
		 * @deprecated ?? Introduced shortcode manager.
		 *                Use {@see et_builder_module_loading}/{@see et_builder_module_loaded}/{@see et_builder_ready} instead.
		 */
		do_action( 'et_builder_modules_loaded' );

		/**
		 * Fires after the builder's module shortcodes are lazy registered.
		 *
		 * @since 4.10.0
		 */
		do_action( 'et_builder_module_lazy_shortcodes_registered' );
	}

	/**
	 * Add slugs for all our woo modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param array $loaded Loaded woo modules slugs.
	 * @return array
	 */
	public function add_woo_slugs( $loaded ) {
		static $module_slugs;

		// Only compute this once.
		if ( empty( $module_slugs ) ) {
			$module_slugs = array_keys( self::$woo_modules_map );
		}

		return array_unique( array_merge( $loaded, $module_slugs ) );
	}

	/**
	 * Add slugs for all our modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param array $loaded Loaded modules slugs.
	 * @return array
	 */
	public function add_module_slugs( $loaded ) {
		static $module_slugs;

		// Only compute this once.
		if ( empty( $module_slugs ) ) {
			$module_slugs = array_keys( self::$modules_map );
		}

		return array_unique( array_merge( $loaded, $module_slugs ) );
	}

	/**
	 * Add slugs for all our structural modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param array $loaded Loaded modules slugs.
	 * @return array
	 */
	public function add_structural_module_slugs( $loaded ) {
		static $structural_module_slugs;

		// Only compute this once.
		if ( empty( $structural_module_slugs ) ) {
			$structural_module_slugs = array_keys( self::$structural_modules_map );
		}

		return array_unique( array_merge( $loaded, $structural_module_slugs ) );
	}

	/**
	 * Load modules.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param mixed  $override Whether to override do_shortcode return value or not.
	 * @param string $tag Shortcode tag.
	 * @return mixed
	 */
	public function load_modules( $override, $tag ) {
		$this->maybe_load_module_from_slug( $tag );
		return $override;
	}

	/**
	 * Instantiate module from a shortcode slug.
	 *
	 * @since 4.10.0
	 * @access public
	 * @param string $tag Shortcode tag.
	 * @return void
	 */
	public function maybe_load_module_from_slug( $tag ) {

		if ( empty( self::$modules_map[ $tag ] ) ) {
			// None of our business.
			return;
		}

		$module =& self::$modules_map[ $tag ];

		if ( empty( $module['instance'] ) ) {
			/**
			 * Fires before module class is instantiated.
			 *
			 * @param string $tag    Shortcode tag for module.
			 * @param array  $module Module loading configuration details.
			 *
			 * @since 4.10.0
			 */
			do_action( 'et_builder_module_loading', $tag, $module );

			/**
			 * Fires before module class is instantiated.
			 *
			 * The dynamic portion of the hook, `$tag`, refers to the shortcode tag.
			 *
			 * @param array $module Module loading configuration details.
			 *
			 * @since 4.10.0
			 */
			do_action( "et_builder_module_loading_{$tag}", $module );

			// Load dependency before the class if needed.
			if ( ! empty( $module['preload_deps'] ) ) {
				foreach ( $module['preload_deps'] as $slug ) {
					$this->maybe_load_module_from_slug( $slug );
				}
			}

			$module['instance'] = new $module['classname']();

			if ( ! empty( $module['deps'] ) ) {
				foreach ( $module['deps'] as $slug ) {
					$this->maybe_load_module_from_slug( $slug );
				}
			}

			/**
			 * Fires after module class is instantiated.
			 *
			 * @param string $tag    Shortcode tag for module.
			 * @param array  $module Module loading configuration details.
			 *
			 * @since 4.10.0
			 */
			do_action( 'et_builder_module_loaded', $tag, $module );

			/**
			 * Fires after module class is instantiated.
			 *
			 * The dynamic portion of the hook, `$tag`, refers to the shortcode tag.
			 *
			 * @param array $module Module loading configuration details.
			 *
			 * @since 4.10.0
			 */
			do_action( "et_builder_module_loaded_{$tag}", $module );
		}
	}
}

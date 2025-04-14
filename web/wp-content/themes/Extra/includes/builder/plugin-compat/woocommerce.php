<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WooCommerce
 *
 * @since 3.0.65 (builder version)
 * @link https://wordpress.org/plugins/woocommerce/
 */
class ET_Builder_Plugin_Compat_WooCommerce extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'woocommerce/woocommerce.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Latest plugin version: 3.1.1
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found or needed functions do not exist
		if (
			! $this->get_plugin_version() ||
			! function_exists( 'is_cart' ) ||
			! function_exists( 'is_account_page' )
		) {
			return;
		}

		// Up to: latest theme version
		add_filter( 'et_grab_image_setting', array( $this, 'disable_et_grab_image_setting' ), 1 );

		// Hook before calling comments_template function in module.
		add_action( 'et_fb_before_comments_template', array( $this, 'remove_filter_comments_number_by_woo' ) );
		add_action( 'et_builder_before_comments_number', array( $this, 'remove_filter_comments_number_by_woo' ) );

		// Hook afer calling comments_template function in module.
		add_action( 'et_fb_after_comments_template', array( $this, 'restore_filter_comments_number_by_woo' ) );
		add_action( 'et_builder_after_comments_number', array( $this, 'restore_filter_comments_number_by_woo' ) );

		// Prevent malformed html in demo store notice from breaking the VB.
		add_filter( 'woocommerce_demo_store', 'et_core_fix_unclosed_html_tags' );

		// Dynamic Content
		add_filter( 'et_builder_dynamic_content_display_hidden_meta_keys', array( $this, 'filter_dynamic_content_display_hidden_meta_keys' ), 10, 2 );
		add_filter( 'et_builder_dynamic_content_custom_field_label', array( $this, 'filter_dynamic_content_custom_field_label' ), 10, 2 );
		add_filter( 'et_builder_dynamic_content_meta_value', array( $this, 'maybe_filter_dynamic_content_meta_value' ), 10, 3 );

		if ( is_object( WC() ) && is_object( WC()->structured_data ) ) {
			$enabled = array(
				// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
				'vb'  => et_()->array_get( $_GET, 'et_fb' ),
				'bfb' => et_()->array_get( $_GET, 'et_bfb' ),
				// phpcs:enable
			);

			if ( ( $enabled['vb'] || $enabled['bfb'] ) ) {
				// Hook generates JSON-LD which is used by some search engines but it's not needed in VB/BFB
				// and it also breaks inline generation of static definitions.
				remove_action( 'woocommerce_single_product_summary', array( WC()->structured_data, 'generate_product_data' ), 60 );
			}
		}

		// Theme Builder.
		add_filter( 'et_theme_builder_template_settings_options', array( $this, 'maybe_filter_theme_builder_template_settings_options' ) );
		add_action( 'et_theme_builder_after_layout_opening_wrappers', array( $this, 'maybe_trigger_woo_hooks_in_theme_builder_body' ) );
	}

	/**
	 * When an order is cancelled, WooCommerce cart shortcode changes the order status to prevent
	 * the 'Your order was cancelled.' notice from being shown multiple times.
	 * Since grab_image renders shortcodes twice, it must be disabled in the cart page or else the notice
	 * will not be shown at all.
	 * My Account Page and Checkout Page is also affected by the same issue.
	 *
	 * @return bool
	 */
	function disable_et_grab_image_setting( $settings ) {
		return ( is_cart() || is_checkout() || is_account_page() ) ? false : $settings;
	}

	/**
	 * Remove comments_number filter added by Woo that caused missing comment
	 * count in Comment module
	 *
	 * @return void
	 */
	public function remove_filter_comments_number_by_woo() {
		if ( ! current_theme_supports( 'woocommerce' ) || ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) < 0 ) ) {
			remove_filter( 'comments_number', '__return_empty_string' );
		}
	}

	/**
	 * Restore comments_number that removed by remove_filter_comments_number_by_woo
	 *
	 * @return void
	 */
	public function restore_filter_comments_number_by_woo() {
		if ( ! current_theme_supports( 'woocommerce' ) || ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) < 0 ) ) {
			add_filter( 'comments_number', '__return_empty_string' );
		}
	}

	/**
	 * Allowlist hidden WooCommerce meta keys for dynamic content.
	 *
	 * @since 3.17.2
	 *
	 * @param string[] $meta_keys
	 * @param integer  $post_id
	 *
	 * @return string[]
	 */
	public function filter_dynamic_content_display_hidden_meta_keys( $meta_keys, $post_id ) {
		return array_merge(
			$meta_keys,
			array(
				'_stock_status',
				'_regular_price',
				'_sale_price',
			)
		);
	}

	/**
	 * Rename label of known displayed hidden post meta fields in dynamic content.
	 *
	 * @since 3.17.2
	 *
	 * @param string $label
	 * @param string $key
	 *
	 * @return string
	 */
	public function filter_dynamic_content_custom_field_label( $label, $key ) {
		$custom_labels = array(
			'total_sales'    => esc_html__( 'Product Total Sales', 'et_builder' ),
			'_stock_status'  => esc_html__( 'Product Stock Status', 'et_builder' ),
			'_regular_price' => esc_html__( 'Product Regular Price', 'et_builder' ),
			'_sale_price'    => esc_html__( 'Product Sale Price', 'et_builder' ),
		);

		if ( isset( $custom_labels[ $key ] ) ) {
			return $custom_labels[ $key ];
		}

		return $label;
	}

	/**
	 * Format WooCommerce meta values accordingly.
	 *
	 * @since 3.17.2
	 *
	 * @param string  $meta_value
	 * @param string  $meta_key
	 * @param integer $post_id
	 *
	 * @return string
	 */
	public function maybe_filter_dynamic_content_meta_value( $meta_value, $meta_key, $post_id ) {
		switch ( $meta_key ) {
			case '_stock_status':
				// Check for function existance just in case
				if ( function_exists( 'wc_get_product_stock_status_options' ) ) {
					$stock_statuses = wc_get_product_stock_status_options();

					// Format meta value into human readable format
					if ( ! empty( $stock_statuses[ $meta_value ] ) ) {
						$meta_value = esc_html( $stock_statuses[ $meta_value ] );
					}
				}

				break;
		}

		return $meta_value;
	}

	/**
	 * Add Theme Builder template settings options.
	 *
	 * @since 4.0
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function maybe_filter_theme_builder_template_settings_options( $options ) {
		$woocommerce_options = array(
			'woocommerce' => array(
				'label'    => esc_html__( 'WooCommerce Pages', 'et_builder' ),
				'settings' => array(
					array(
						'id'       => 'woocommerce:shop',
						'label'    => esc_html__( 'Shop', 'et_builder' ),
						'title'    => trim( str_replace( home_url(), '', get_post_type_archive_link( 'product' ) ), '/' ),
						'priority' => 120,
						'validate' => array( $this, 'theme_builder_validate_woocommerce_shop' ),
					),
					array(
						'id'       => 'woocommerce:cart',
						'label'    => esc_html__( 'Cart', 'et_builder' ),
						'title'    => get_post_field( 'post_name', wc_get_page_id( 'cart' ) ),
						'priority' => 120,
						'validate' => array( $this, 'theme_builder_validate_woocommerce_cart' ),
					),
					array(
						'id'       => 'woocommerce:checkout',
						'label'    => esc_html__( 'Checkout', 'et_builder' ),
						'title'    => get_post_field( 'post_name', wc_get_page_id( 'checkout' ) ),
						'priority' => 120,
						'validate' => array( $this, 'theme_builder_validate_woocommerce_checkout' ),
					),
					array(
						'id'       => 'woocommerce:my_account',
						'label'    => esc_html__( 'My Account', 'et_builder' ),
						'title'    => get_post_field( 'post_name', wc_get_page_id( 'myaccount' ) ),
						'priority' => 130,
						'validate' => array( $this, 'theme_builder_validate_woocommerce_my_account' ),
					),
				),
			),
		);

		$archive_index = array_search( 'archive', array_keys( $options ) );

		if ( false === $archive_index ) {
			return array_merge(
				$options,
				$woocommerce_options
			);
		}

		return array_merge(
			array_slice( $options, 0, $archive_index + 1, true ),
			$woocommerce_options,
			array_slice( $options, $archive_index + 1, null, true )
		);
	}

	/**
	 * Theme Builder: Validate woocommerce:shop.
	 *
	 * @since 4.0
	 *
	 * @param string   $type
	 * @param string   $subtype
	 * @param integer  $id
	 * @param string[] $setting
	 *
	 * @return bool
	 */
	public function theme_builder_validate_woocommerce_shop( $type, $subtype, $id, $setting ) {
		return (
			( ET_Theme_Builder_Request::TYPE_POST_TYPE_ARCHIVE === $type && $subtype === 'product' )
			||
			( ET_Theme_Builder_Request::TYPE_SINGULAR === $type && $id === wc_get_page_id( 'shop' ) )
		);
	}

	/**
	 * Theme Builder: Validate woocommerce:cart.
	 *
	 * @since 4.0
	 *
	 * @param string   $type
	 * @param string   $subtype
	 * @param integer  $id
	 * @param string[] $setting
	 *
	 * @return bool
	 */
	public function theme_builder_validate_woocommerce_cart( $type, $subtype, $id, $setting ) {
		return ET_Theme_Builder_Request::TYPE_SINGULAR === $type && $id === wc_get_page_id( 'cart' );
	}

	/**
	 * Theme Builder: Validate woocommerce:checkout.
	 *
	 * @since 4.0
	 *
	 * @param string   $type
	 * @param string   $subtype
	 * @param integer  $id
	 * @param string[] $setting
	 *
	 * @return bool
	 */
	public function theme_builder_validate_woocommerce_checkout( $type, $subtype, $id, $setting ) {
		return ET_Theme_Builder_Request::TYPE_SINGULAR === $type && $id === wc_get_page_id( 'checkout' );
	}

	/**
	 * Theme Builder: Validate woocommerce:my_account.
	 *
	 * @since 4.0
	 *
	 * @param string   $type
	 * @param string   $subtype
	 * @param integer  $id
	 * @param string[] $setting
	 *
	 * @return bool
	 */
	public function theme_builder_validate_woocommerce_my_account( $type, $subtype, $id, $setting ) {
		return ET_Theme_Builder_Request::TYPE_SINGULAR === $type && $id === wc_get_page_id( 'myaccount' );
	}

	/**
	 * Trigger Woo hooks before a Theme Builder body layout is rendered
	 * so stuff like structured data is output.
	 *
	 * @since 4.0.10
	 *
	 * @param string $layout_type
	 */
	public function maybe_trigger_woo_hooks_in_theme_builder_body( $layout_type ) {
		global $product;

		if ( ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE !== $layout_type || ! is_singular( 'product' ) ) {
			return;
		}

		if ( $product && ! is_a( $product, 'WC_Product' ) ) {
			// Required for Woo to setup its $product global.
			the_post();
		}

		// Make sure builder and non-builder products do not render
		// anything as this will be taken care of by the
		// Post Content module in TB, if used.
		et_builder_wc_disable_default_layout();
		remove_action(
			'woocommerce_after_single_product_summary',
			'et_builder_wc_product_render_layout',
			5
		);

		// Trigger the usual Woo hooks so functionality like structured data works.
		do_action( 'woocommerce_before_single_product' );

		if ( ! post_password_required() ) {
			do_action( 'woocommerce_before_single_product_summary' );
			do_action( 'woocommerce_single_product_summary' );
			do_action( 'woocommerce_after_single_product_summary' );
			do_action( 'woocommerce_after_single_product' );
		}
	}
}
new ET_Builder_Plugin_Compat_WooCommerce();

<?php
/**
 * All WooCommerce modules specific functions.php stuff goes here
 *
 * @package Divi
 * @subpackage Builder
 * @since 3.29
 */

/**
 * Define required constants.
 */
if ( ! defined( 'ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY' ) ) {
	// Post meta key to retrieve/save Long description metabox content.
	define( 'ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY', '_et_pb_old_content' );
}

if ( ! defined( 'ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY' ) ) {
	// Post meta key to retrieve/save Long description metabox content.
	define( 'ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY', '_et_pb_product_page_layout' );
}

if ( ! defined( 'ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY' ) ) {
	// Post meta key to track Product page content status changes.
	define( 'ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY', '_et_pb_woo_page_content_status' );
}

/**
 * Handles Shipping calculator Update button click.
 *
 * `wc-form-handler` handles shipping calculator update ONLY when WooCommerce shortcode is used.
 * Hence, Cart Total's shipping calculator update is handled this way.
 *
 * @since 4.14.3
 */
function et_builder_handle_shipping_calculator_update_btn_click() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by WooCommerce plugin.
	if ( ! isset( $_POST['woocommerce-shipping-calculator-nonce'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by WooCommerce plugin.
	if ( ! isset( $_POST['_wp_http_referer'] ) ) {
		return;
	}

	$nonce_verified = false;

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- Nonce verification is handled by WordPress.
	if ( wp_verify_nonce( $_POST['woocommerce-shipping-calculator-nonce'], 'woocommerce-shipping-calculator' ) ) { // WPCS: input var ok.
		// We can safely move forward.
		$nonce_verified = true;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by WooCommerce plugin.
	$referrer         = esc_url_raw( $_POST['_wp_http_referer'] );
	$referrer_page_id = url_to_postid( $referrer );
	$cart_page_id     = wc_get_page_id( 'cart' );

	// Bail when nonce failed, and $referrer_page_id isn't equal to $cart_page_id.
	if ( ! $nonce_verified && $cart_page_id !== $referrer_page_id ) {
		return;
	}

	if ( ( ! class_exists( 'WC_Shortcodes' ) ) ||
		( ! method_exists( 'WC_Shortcodes', 'cart' ) ) ) {
		return;
	}

	WC_Shortcodes::cart();
}

/**
 * Identify whether Woo v2 should replace content on Cart & Checkout pages.
 *
 * @param string $shortcode Post content. Builder converts empty string to shortcode string.
 *
 * @since 4.14.0
 *
 * @return bool
 */
function et_builder_wc_should_replace_content( $shortcode ) {
	$default_shortcodes     = array( 'et_pb_section', 'et_pb_row', 'et_pb_column', 'et_pb_text', 'woocommerce_cart', 'woocommerce_checkout' );
	$should_replace_content = true;

	// Get all shortcodes on the page.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $shortcode, $matches );

	$matched_shortcodes = $matches[1];

	foreach ( $matched_shortcodes as $shortcode ) {
		// If a shortcode exists that is not a default shortcode, don't replace content. The user has already built a custom page.
		if ( ! in_array( $shortcode, $default_shortcodes, true ) ) {
			$should_replace_content = false;
			break;
		}
	}

	return $should_replace_content;
}

/**
 * Stop redirecting to Cart page when enabling builder on Checkout page.
 *
 * @since 4.14.0
 *
 * @link https://github.com/elegantthemes/Divi/issues/23873
 *
 * @param bool $flag Flag.
 *
 * @return bool
 */
function et_builder_stop_cart_redirect_while_enabling_builder( $flag ) {
	/*
	 * Don't need to check if the current page is Checkout page since this filter
	 * `woocommerce_checkout_redirect_empty_cart` only fires if the
	 * current page is a Checkout page.
	 */

	$post_id = get_the_ID();

	if ( is_array( $_GET ) && isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] ) {
		$is_builder_activation_request = true;
	} else {
		// Verify if the request is a valid Builder activation request.
		$is_builder_activation_request = et_core_security_check(
			'',
			"et_fb_activation_nonce_{$post_id}",
			'et_fb_activation_nonce',
			'_REQUEST',
			false
		);
	}

	return $is_builder_activation_request ? false : $flag;
}

/**
 * Message to be displayed in Checkout Payment Info module in VB mode.
 *
 * So styling the Notice becomes easier.
 *
 * @since 4.14.0
 *
 * @return string
 */
function et_builder_wc_no_available_payment_methods_message() {
	// Fallback.
	$message = esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.' );

	if ( ! function_exists( 'WC' ) ) {
		return $message;
	}

	if ( ! isset( WC()->customer ) && ! method_exists( WC()->customer, 'get_billing_country' ) ) {
		return $message;
	}

	$message = WC()->customer->get_billing_country()
		? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'et_builder' )
		: esc_html__( 'Please fill in your details above to see available payment methods.', 'et_builder' );

	return apply_filters(
		'woocommerce_no_available_payment_methods_message',
		$message
	);
}

/**
 * Output the cart shipping calculator.
 *
 * @param string $button_text Text for the shipping calculation toggle.
 */
function et_builder_woocommerce_shipping_calculator( $button_text = '' ) {
	wp_enqueue_script( 'wc-country-select' );
	wc_get_template(
		'cart/shipping-calculator.php',
		array(
			'button_text' => $button_text,
		)
	);
}

/**
 * Gets the Checkout modules notice to be displayed on non-checkout pages.
 *
 * @since 4.14.0
 *
 * @used-by et_fb_get_static_backend_helpers()
 *
 * @return string
 */
function et_builder_wc_get_non_checkout_page_notice() {
	return esc_html__( 'This module will not function properly on the front end of your website because this is not the assigned Checkout page.', 'et_builder' );
}

/**
 * Gets the Checkout notice to be displayed on Checkout Payment Info module.
 *
 * @since 4.14.0
 *
 * @param string $woocommerce_ship_to_destination Default `shipping`.
 *
 * @used-by et_fb_get_static_backend_helpers()
 *
 * @return string
 */
function et_builder_wc_get_checkout_notice( $woocommerce_ship_to_destination = 'shipping' ) {
	$settings_modal_notice = '';

	if ( 'billing_only' === $woocommerce_ship_to_destination ) {
		$settings_modal_notice = wp_kses(
			__( '<strong>Woo Billing Address Module</strong> must be added to this page to allow users to submit orders.', 'et_builder' ),
			array( 'strong' => array() )
		);
	} else {
		$settings_modal_notice = wp_kses(
			__( '<strong>Woo Billing Address Module</strong> and <strong>Woo Shipping Address Module</strong> must be added to this page to allow users to submit orders.', 'et_builder' ),
			array( 'strong' => array() )
		);
	}

	return $settings_modal_notice;
}

/**
 * Stop WooCommerce from redirecting Checkout page to Cart when the cart is empty.
 *
 * Divi Builder stops redirection only for logged-in admins.
 *
 * @since 4.14.0
 */
function et_builder_wc_template_redirect() {
	$checkout_page_id = wc_get_page_id( 'checkout' );

	$post = get_post( $checkout_page_id );
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	$is_checkout_page = $checkout_page_id === $post->ID;

	if ( ! $is_checkout_page ) {
		return;
	}

	if ( ! et_core_is_fb_enabled() ) {
		return;
	}

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$has_wc_shortcode = has_shortcode( $post->post_content, 'et_pb_section' );

	if ( ! $has_wc_shortcode ) {
		return;
	}

	add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
}

/**
 * Sets the meta to indicate that the Divi content has been modified.
 *
 * This avoids setting the default WooCommerce Modules layout more than once.
 *
 * @link https://github.com/elegantthemes/Divi/issues/16420
 *
 * @since 4.14.0
 *
 * @param int $post_id Post ID.
 */
function et_builder_wc_set_page_content_status( $post_id ) {
	if ( 0 === absint( $post_id ) ) {
		return;
	}

	/**
	 * The ID page of the Checkout page set in WooCommerce Settings page.
	 *
	 * WooCommerce — Settings — Advanced — Checkout page
	 */
	$checkout_page_id = wc_get_page_id( 'checkout' );

	/**
	 * The ID page of the Cart page set in WooCommerce Settings page.
	 *
	 * WooCommerce — Settings — Advanced — Cart page
	 */
	$cart_page_id = wc_get_page_id( 'cart' );

	$is_cart     = $post_id === $cart_page_id;
	$is_checkout = $post_id === $checkout_page_id;
	$is_product  = 'product' === get_post_type( $post_id );

	// Take action only on Product, Cart and Checkout pages. Bail early otherwise.
	if ( ! ( $is_product || $is_cart || $is_checkout ) ) {
		return;
	}

	$modified_status            = 'modified';
	$is_content_status_modified = get_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY, true ) === $modified_status;

	if ( $is_content_status_modified ) {
		return;
	}

	update_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY, $modified_status );
}

/**
 * Gets the prefilled Cart Page content built using Divi Woo Modules.
 *
 * @since 4.14.0
 *
 * @return string
 */
function et_builder_wc_get_prefilled_cart_page_content() {
	$page_title = '[et_pb_post_title meta="off" featured_image="off"][/et_pb_post_title]';

	// Gets Parent theme's info in case child theme is used.
	if ( 'Extra' === et_core_get_theme_info( 'Name' ) ) {
		$page_title = '';
	}

	return '
	[et_pb_section]
		[et_pb_row]
			[et_pb_column type="4_4"]
				' . $page_title . '
				[et_pb_wc_cart_notice page_type="cart"][/et_pb_wc_cart_notice]
				[et_pb_wc_cart_products][/et_pb_wc_cart_products]
			[/et_pb_column]
		[/et_pb_row]
		[et_pb_row column_structure="1_2,1_2"]
			[et_pb_column type="1_2"]
				[et_pb_wc_cross_sells][/et_pb_wc_cross_sells]
			[/et_pb_column]
			[et_pb_column type="1_2"]
				[et_pb_wc_cart_totals][/et_pb_wc_cart_totals]
			[/et_pb_column]
		[/et_pb_row]
	[/et_pb_section]
	';
}

/**
 * Gets the prefilled Checkout Page content built using Divi Woo Modules.
 *
 * @since 4.14.0
 * @return string
 */
function et_builder_wc_get_prefilled_checkout_page_content() {
	$page_title = '[et_pb_post_title meta="off" featured_image="off"][/et_pb_post_title]';

	// Use `et_core_get_theme_info` to get Parent theme's info even when a child theme is used.
	if ( 'Extra' === et_core_get_theme_info( 'Name' ) ) {
		$page_title = '';
	}

	return '
	[et_pb_section]
		[et_pb_row custom_padding="||0%||false|false"]
			[et_pb_column type="4_4"]
				' . $page_title . '
				[et_pb_wc_cart_notice page_type="checkout"][/et_pb_wc_cart_notice]
			[/et_pb_column]
		[/et_pb_row]
		[et_pb_row column_structure="1_2,1_2"]
			[et_pb_column type="1_2"]
				[et_pb_wc_checkout_billing ][/et_pb_wc_checkout_billing]
			[/et_pb_column]
			[et_pb_column type="1_2"]
				[et_pb_wc_checkout_shipping][/et_pb_wc_checkout_shipping]
				[et_pb_wc_checkout_additional_info][/et_pb_wc_checkout_additional_info]
			[/et_pb_column]
		[/et_pb_row]
		[et_pb_row]
			[et_pb_column type="4_4"]
				[et_pb_wc_checkout_order_details][/et_pb_wc_checkout_order_details]
				[et_pb_wc_checkout_payment_info][/et_pb_wc_checkout_payment_info]
			[/et_pb_column]
		[/et_pb_row]
	[/et_pb_section]
	';
}

/**
 * Sets the pre-filled Divi Woo Pages layout content.
 *
 * The following are the three types of WooCommerce pages that have pre-filled content.
 *
 * 1. WooCommerce Product page
 * 2. WooCommerce Cart page
 * 3. WooCommerce Checkout page
 *
 * @param string $maybe_shortcode_content May be shortcode content.
 * @param int    $post_id Post ID.
 *
 * @return string
 */
function et_builder_wc_set_prefilled_page_content( $maybe_shortcode_content, $post_id ) {
	$post = get_post( absint( $post_id ) );
	if ( ! $post ) {
		return $maybe_shortcode_content;
	}

	/**
	 * The ID page of the Checkout page set in WooCommerce Settings page.
	 *
	 * WooCommerce — Settings — Advanced — Checkout page
	 */
	$checkout_page_id = wc_get_page_id( 'checkout' );

	/**
	 * The ID page of the Cart page set in WooCommerce Settings page.
	 *
	 * WooCommerce — Settings — Advanced — Cart page
	 */
	$cart_page_id = wc_get_page_id( 'cart' );

	$is_cart     = $post_id === $cart_page_id;
	$is_checkout = $post_id === $checkout_page_id;
	$is_product  = ( $post instanceof WP_Post ) && 'product' === $post->post_type;

	// Bail early when none of the conditions are met.
	if ( ! ( $is_product || $is_checkout || $is_cart ) ) {
		return $maybe_shortcode_content;
	}

	// Bail early if the Page already has initial content set.
	$is_content_status_modified = 'modified' === get_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY, true );

	if ( $is_content_status_modified ) {
		return $maybe_shortcode_content;
	}

	$should_replace_content = true;
	if ( $is_cart || $is_checkout ) {
		$should_replace_content = et_builder_wc_should_replace_content( $maybe_shortcode_content );
	}

	if ( $is_cart && $should_replace_content ) {
		return et_builder_wc_get_prefilled_cart_page_content();
	} elseif ( $is_checkout && $should_replace_content ) {
		return et_builder_wc_get_prefilled_checkout_page_content();
	} elseif ( $is_product ) {
		$args                = array();
		$product_page_layout = et_builder_wc_get_product_layout( $post_id );

		/*
		 * When FALSE, this means the Product doesn't use Builder at all;
		 * Or the Product has been using the Builder before WooCommerce Modules QF launched.
		 */
		if ( ! $product_page_layout ) {
			$product_page_layout = et_get_option(
				'et_pb_woocommerce_page_layout',
				'et_build_from_scratch'
			);
		}

		// Load default content.
		if ( 'et_default_layout' === $product_page_layout ) {
			return $maybe_shortcode_content;
		}

		$has_et_builder_shortcode          = has_shortcode( $maybe_shortcode_content, 'et_pb_section' );
		$is_layout_type_build_from_scratch = 'et_build_from_scratch' === $product_page_layout;

		if ( $has_et_builder_shortcode && $is_layout_type_build_from_scratch ) {
			$args['existing_shortcode'] = $maybe_shortcode_content;
		}

		return et_builder_wc_get_prefilled_product_page_content( $args );
	}

	return $maybe_shortcode_content;
}

/**
 * Returning <img> string for default image placeholder
 *
 * @since 4.14.0 Added $mode param.
 * @since 4.0.10
 *
 * @param string $mode Default ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA. Either Landscape or
 *                     Portrait image mode.
 *
 * @return string
 */
function et_builder_wc_placeholder_img( $mode = 'portrait' ) {
	$allowed_list = array(
		'portrait'  => ET_BUILDER_PLACEHOLDER_PORTRAIT_VARIATION_IMAGE_DATA,
		'landscape' => ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA,
	);

	if ( ! in_array( $mode, array_keys( $allowed_list ), true ) ) {
		$mode = 'portrait';
	}

	return sprintf(
		'<img src="%1$s" alt="2$s" />',
		et_core_esc_attr( 'placeholder', $allowed_list[ $mode ] ),
		esc_attr__( 'Product image', 'et_builder' )
	);
}

/**
 * Gets the Product Content options.
 *
 * This array is used in Divi Page Settings metabox and in Divi Theme Options ⟶ Builder ⟶ Post Type integration.
 *
 * @since 3.29
 *
 * @param string $translation_context Translation Context to indicate if translation origins from Divi Theme or
 *                                    from the Builder. Optional. Default 'et_builder'.
 *
 * @return array
 */
function et_builder_wc_get_page_layouts( $translation_context = 'et_builder' ) {
	switch ( $translation_context ) {
		case 'Divi':
			$product_page_layouts = array(
				'et_build_from_scratch' => esc_html__( 'Build From Scratch', 'Divi' ),
				'et_default_layout'     => esc_html__( 'Default', 'Divi' ),
			);
			break;
		default:
			$product_page_layouts = array(
				'et_build_from_scratch' => esc_html__( 'Build From Scratch', 'et_builder' ),
				'et_default_layout'     => et_builder_i18n( 'Default' ),
			);
			break;
	}

	return $product_page_layouts;
}

/**
 * Adds WooCommerce Module settings to the Builder settings.
 *
 * Adding in the Builder Settings tab will ensure that the field is available in Extra Theme and
 * Divi Builder Plugin.
 *
 * @since 4.0.3 Hide Product Content layout settings Divi Builder Plugin options.
 * @since 3.29
 *
 * @param array $builder_settings_fields Builder settings fields.
 *
 * @return array
 */
function et_builder_wc_add_settings( $builder_settings_fields ) {
	// Bail early to hide WooCommerce Settings tab under the Builder tab.
	// If $fields['tab_slug'] is not equal to the tab slug (i.e. woocommerce_page_layout) then WooCommerce settings tab won't be displayed.
	// {@see ET_Builder_Settings::_get_builder_settings_in_epanel_format}.
	if ( ! et_is_woocommerce_plugin_active() ) {
		return $builder_settings_fields;
	}

	$fields = array(
		'et_pb_woocommerce_product_layout' => array(
			'type'            => 'select',
			'id'              => 'et_pb_woocommerce_product_layout',
			'index'           => - 1,
			'label'           => esc_html__( 'Product Layout', 'et_builder' ),
			'description'     => esc_html__( 'Here you can choose Product Page Layout for WooCommerce.', 'et_builder' ),
			'options'         => array(
				'et_right_sidebar'   => esc_html__( 'Right Sidebar', 'et_builder' ),
				'et_left_sidebar'    => esc_html__( 'Left Sidebar', 'et_builder' ),
				'et_no_sidebar'      => esc_html__( 'No Sidebar', 'et_builder' ),
				'et_full_width_page' => esc_html__( 'Fullwidth', 'et_builder' ),
			),
			'default'         => 'et_right_sidebar',
			'validation_type' => 'simple_text',
			'et_save_values'  => true,
			'tab_slug'        => 'post_type_integration',
			'toggle_slug'     => 'performance',
		),
		'et_pb_woocommerce_page_layout'    => array(
			'type'            => 'select',
			'id'              => 'et_pb_woocommerce_product_page_layout',
			'index'           => -1,
			'label'           => esc_html__( 'Product Content', 'et_builder' ),
			'description'     => esc_html__( '"Build From Scratch" loads a pre-built WooCommerce page layout, with which you build on when the Divi Builder is enabled. "Default" option lets you use default WooCommerce page layout.', 'et_builder' ),
			'options'         => et_builder_wc_get_page_layouts(),
			'default'         => 'et_build_from_scratch',
			'validation_type' => 'simple_text',
			'et_save_values'  => true,
			'tab_slug'        => 'post_type_integration',
			'toggle_slug'     => 'performance',
		),
	);

	// Hide setting in DBP : https://github.com/elegantthemes/Divi/issues/17378.
	if ( et_is_builder_plugin_active() ) {
		unset( $fields['et_pb_woocommerce_product_layout'] );
	}

	return array_merge( $builder_settings_fields, $fields );
}

/**
 * Gets the pre-built layout for WooCommerce product pages.
 *
 * @since 3.29
 *
 * @param array $args {
 *  Additional args.
 *
 * @type string $existing_shortcode Existing builder shortcode.
 * }
 *
 * @return string
 */
function et_builder_wc_get_prefilled_product_page_content( $args = array() ) {
	/**
	 * Filters the Top section Background in the default WooCommerce Modules layout.
	 *
	 * @param string $color Default empty.
	 */
	$et_builder_wc_initial_top_section_bg = apply_filters( 'et_builder_wc_initial_top_section_bg', '' );

	$content = '
	[et_pb_section custom_padding="0px||||false|false" background_color="' . esc_attr( $et_builder_wc_initial_top_section_bg ) . '"]
			[et_pb_row width="100%" custom_padding="0px||0px||false|false"]
				[et_pb_column type="4_4"]
					[et_pb_wc_breadcrumb][/et_pb_wc_breadcrumb]
					[et_pb_wc_cart_notice][/et_pb_wc_cart_notice]
				[/et_pb_column]
			[/et_pb_row]
			[et_pb_row custom_padding="0px||||false|false" width="100%"]
				[et_pb_column type="1_2"]
					[et_pb_wc_images][/et_pb_wc_images]
				[/et_pb_column]
				[et_pb_column type="1_2"]
					[et_pb_wc_title][/et_pb_wc_title]
					[et_pb_wc_rating][/et_pb_wc_rating]
					[et_pb_wc_price][/et_pb_wc_price]
					[et_pb_wc_description][/et_pb_wc_description]
					[et_pb_wc_add_to_cart form_field_text_align="center"][/et_pb_wc_add_to_cart]
					[et_pb_wc_meta][/et_pb_wc_meta]
				[/et_pb_column]
			[/et_pb_row]
			[et_pb_row width="100%"]
				[et_pb_column type="4_4"]
					[et_pb_wc_tabs]
					[/et_pb_wc_tabs]
					[et_pb_wc_upsells columns_number="3"][/et_pb_wc_upsells]
					[et_pb_wc_related_products columns_number="3"][/et_pb_wc_related_products]
				[/et_pb_column]
			[/et_pb_row]
		[/et_pb_section]';

	if ( ! empty( $args['existing_shortcode'] ) ) {
		return $content . $args['existing_shortcode'];
	}

	return $content;
}

/**
 * Gets the Product layout for a given Post ID.
 *
 * @since 3.29
 *
 * @param int $post_id Post Id.
 *
 * @return string The return value will be one of the values from
 *                {@see et_builder_wc_get_page_layouts()} when the Post ID is valid.
 *                Empty string otherwise.
 */
function et_builder_wc_get_product_layout( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return false;
	}

	return get_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY, true );
}

/**
 * Sets the pre-built layout for WooCommerce product pages.
 *
 * @param string $maybe_shortcode_content Post content.
 * @param int    $post_id Post id.
 *
 * @return string
 */
function et_builder_wc_set_initial_content( $maybe_shortcode_content, $post_id ) {
	$post = get_post( absint( $post_id ) );
	$args = array();

	if ( ! ( $post instanceof WP_Post ) || 'product' !== $post->post_type ) {
		return $maybe_shortcode_content;
	}

	// $post_id is a valid Product ID by now.
	$product_page_layout = et_builder_wc_get_product_layout( $post_id );

	/*
	 * When FALSE, this means the Product doesn't use Builder at all;
	 * Or the Product has been using the Builder before WooCommerce Modules QF launched.
	 */
	if ( ! $product_page_layout ) {
		$product_page_layout = et_get_option(
			'et_pb_woocommerce_page_layout',
			'et_build_from_scratch'
		);
	}

	$is_product_content_modified = 'modified' === get_post_meta(
		$post_id,
		ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY,
		true
	);

	// Content was already saved or default content should be loaded.
	if ( $is_product_content_modified || 'et_default_layout' === $product_page_layout ) {
		return $maybe_shortcode_content;
	}

	if ( has_shortcode( $maybe_shortcode_content, 'et_pb_section' ) && 'et_build_from_scratch' === $product_page_layout && ! empty( $maybe_shortcode_content ) ) {
		$args['existing_shortcode'] = $maybe_shortcode_content;
	}

	return et_builder_wc_get_prefilled_product_page_content( $args );
}

/**
 * Saves the WooCommerce long description metabox content.
 *
 * The content is stored as post meta w/ the key `_et_pb_old_content`.
 *
 * @param int     $post_id Post id.
 * @param WP_Post $post Post Object.
 * @param array   $request The $_POST Request variables.
 *
 * @since 3.29
 */
function et_builder_wc_long_description_metabox_save( $post_id, $post, $request ) {
	if ( ! isset( $request['et_bfb_long_description_nonce'] ) ) {
		return;
	}

	if ( current_user_can( 'edit_posts', $post_id ) && et_core_security_check( 'edit_posts', 'et_bfb_long_description_nonce', '_et_bfb_long_description_nonce', '_POST', false )
	) {
		return;
	}

	if ( 'product' !== $post->post_type ) {
		return;
	}

	if ( ! isset( $request['et_builder_wc_product_long_description'] ) ) {
		return;
	}

	$long_desc_content = $request['et_builder_wc_product_long_description'];
	$is_updated        = update_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY, wp_kses_post( $long_desc_content ) );
}

/**
 * Output Callback for Product long description metabox.
 *
 * @since 3.29
 *
 * @param WP_Post $post Post.
 */
function et_builder_wc_long_description_metabox_render( $post ) {
	$settings = array(
		'textarea_name' => 'et_builder_wc_product_long_description',
		'quicktags'     => array( 'buttons' => 'em,strong,link' ),
		'tinymce'       => array(
			'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
			'theme_advanced_buttons2' => '',
		),
		'editor_css'    => '<style>#wp-et_builder_wc_product_long_description-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
	);

	// Since we use $post_id in more than one place, use a variable.
	$post_id = $post->ID;

	// Long description metabox content. Default Empty.
	$long_desc_content = get_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY, true );
	$long_desc_content = ! empty( $long_desc_content ) ? $long_desc_content : '';

	/**
	 * Filters the wp_editor settings used in the Long description metabox.
	 *
	 * @param array $settings WP Editor settings.
	 *
	 * @since 3.29
	 */
	$settings = apply_filters( 'et_builder_wc_product_long_description_editor_settings', $settings );

	wp_nonce_field( '_et_bfb_long_description_nonce', 'et_bfb_long_description_nonce' );

	wp_editor(
		$long_desc_content,
		'et_builder_wc_product_long_description',
		$settings
	);
}

/**
 * Adds the Long description metabox to Product post type.
 *
 * @since 3.29
 *
 * @param WP_Post $post WP Post.
 */
function et_builder_wc_long_description_metabox_register( $post ) {
	if ( 'on' !== get_post_meta( $post->ID, '_et_pb_use_builder', true ) ) {
		return;
	}

	add_meta_box(
		'et_builder_wc_product_long_description_metabox',
		__( 'Product long description', 'et_builder' ),
		'et_builder_wc_long_description_metabox_render',
		'product',
		'normal'
	);
}

/**
 * Determine if WooCommerce's $product global need to be overwritten or not.
 * IMPORTANT: make sure to reset it later
 *
 * @since 3.29
 *
 * @param string $product_id Post id.
 *
 * @return bool
 */
function et_builder_wc_need_overwrite_global( $product_id = 'current' ) {
	$is_current_product_page = 'current' === $product_id;

	// There are three situation which requires global value overwrite: initial builder
	// ajax request, computed callback jax request (all ajax request has faulty global variable),
	// and if `product` attribute is not current page's product id (ie Woo Tabs being used
	// on non `product` CPT).
	$need_overwrite_global = ! $is_current_product_page
		|| et_fb_is_builder_ajax()
		|| et_fb_is_computed_callback_ajax();

	return $need_overwrite_global;
}

/**
 * Gets the Product ID.
 *
 * @since 4.14.0
 *
 * @param array $args Module props.
 *
 * @return int $product_id
 */
function et_builder_wc_get_product_id( $args ) {
	$maybe_product_id        = et_()->array_get( $args, 'product', 'latest' );
	$is_latest_product       = 'latest' === $maybe_product_id;
	$is_current_product_page = 'current' === $maybe_product_id;

	if ( $is_latest_product ) {
		// Dynamic filter's product_id need to be translated into correct id.
		$product_id = ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( $maybe_product_id );
	} elseif ( $is_current_product_page && wp_doing_ajax() && class_exists( 'ET_Builder_Element' ) ) {
		/*
		 * $product global doesn't exist in ajax request; thus get the fallback post id
		 * this is likely happen in computed callback ajax request.
		 */
		$product_id = ET_Builder_Element::get_current_post_id();
	} else {
		// Besides two situation above, $product_id is current $args['product'].
		if ( false !== get_post_status( $maybe_product_id ) ) {
			$product_id = $maybe_product_id;
		} else {
			// Fallback to Latest product if saved product ID doesn't exist.
			$product_id = ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( 'latest' );
		}
	}

	return $product_id;
}

/**
 * Helper to render module template for module's front end and computed callback output
 *
 * @since 3.29
 *
 * @param string $function_name Rendering method name.
 * @param array  $args Method arguments.
 * @param array  $overwrite List of global variables to overwrites e.g $product, $post and $wp_query.
 *
 * @return string
 */
function et_builder_wc_render_module_template( $function_name, $args = array(), $overwrite = array( 'product' ) ) {
	// Shouldn't be fired in Backend to not break the BB loading.
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	// Check if passed function name is allowlisted or not.
	$allowlisted_functions = array(
		'the_title',
		'woocommerce_breadcrumb',
		'woocommerce_template_single_price',
		'woocommerce_template_single_add_to_cart',
		'woocommerce_product_additional_information_tab',
		'woocommerce_template_single_meta',
		'woocommerce_template_single_rating',
		'woocommerce_show_product_images',
		'wc_get_stock_html',
		'wc_print_notices',
		'wc_print_notice',
		'woocommerce_output_related_products',
		'woocommerce_upsell_display',
		'woocommerce_checkout_login_form',
		'wc_cart_empty_template',
		'woocommerce_output_all_notices',
	);

	if ( ! in_array( $function_name, $allowlisted_functions, true ) ) {
		return '';
	}

	// phpcs:disable WordPress.WP.GlobalVariablesOverride -- Overwrite global variables when rendering templates which are restored before this function exist.
	global $product, $post, $wp_query;

	$defaults = array(
		'product' => 'current',
	);

	$args               = wp_parse_args( $args, $defaults );
	$overwrite_global   = et_builder_wc_need_overwrite_global( $args['product'] );
	$overwrite_product  = in_array( 'product', $overwrite, true );
	$overwrite_post     = in_array( 'post', $overwrite, true );
	$overwrite_wp_query = in_array( 'wp_query', $overwrite, true );
	$is_tb              = et_builder_tb_enabled();
	$is_use_placeholder = $is_tb || is_et_pb_preview();

	if ( $is_use_placeholder ) {
		// global object needs to be set before output rendering. This needs to be performed on each
		// module template rendering instead of once for all module template rendering because some
		// module's template rendering uses `wp_reset_postdata()` which resets global query.
		et_theme_builder_wc_set_global_objects();
	} elseif ( $overwrite_global ) {
		$product_id = et_builder_wc_get_product_id( $args );

		if ( 'product' !== get_post_type( $product_id ) ) {
			// We are in a Theme Builder layout and the current post is not a product - use the latest one instead.
			$products = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'no_found_rows'  => true,
				)
			);

			if ( ! $products->have_posts() ) {
				return '';
			}

			$product_id = $products->posts[0]->ID;
		}

		// Overwrite product.
		if ( $overwrite_product ) {
			$original_product = $product;
			$product          = wc_get_product( $product_id );
		}

		// Overwrite post.
		if ( $overwrite_post ) {
			$original_post = $post;
			$post          = get_post( $product_id );
		}

		// Overwrite wp_query.
		if ( $overwrite_wp_query ) {
			$original_wp_query = $wp_query;
			$wp_query          = new WP_Query( array( 'p' => $product_id ) );
		}
	}

	ob_start();

	switch ( $function_name ) {
		case 'woocommerce_breadcrumb':
			$breadcrumb_separator = et_()->array_get( $args, 'breadcrumb_separator', '' );
			$breadcrumb_separator = str_replace( '&#8221;', '', $breadcrumb_separator );

			woocommerce_breadcrumb(
				array(
					'delimiter' => ' ' . $breadcrumb_separator . ' ',
					'home'      => et_()->array_get( $args, 'breadcrumb_home_text', '' ),
				)
			);
			break;
		case 'woocommerce_show_product_images':
			if ( is_a( $product, 'WC_Product' ) ) {
				// WC Images module needs to modify global variable's property. Thus it is performed
				// here instead at module's class since the $product global might be modified.
				$gallery_ids     = $product->get_gallery_image_ids();
				$image_id        = $product->get_image_id();
				$show_image      = 'on' === $args['show_product_image'];
				$show_gallery    = 'on' === $args['show_product_gallery'];
				$show_sale_badge = 'on' === $args['show_sale_badge'];

				// If featured image is disabled, replace it with first gallery image's id (if gallery
				// is enabled) or replaced it with empty string (if gallery is disabled as well).
				if ( ! $show_image ) {
					if ( $show_gallery && isset( $gallery_ids[0] ) ) {
						$product->set_image_id( $gallery_ids[0] );

						// Remove first image from the gallery because it'll be added as thumbnail and will be duplicated.
						unset( $gallery_ids[0] );
						$product->set_gallery_image_ids( $gallery_ids );
					} else {
						$product->set_image_id( '' );
					}
				}

				// Replaced gallery image ids with empty array.
				if ( ! $show_gallery ) {
					$product->set_gallery_image_ids( array() );
				}

				if ( $show_sale_badge && function_exists( 'woocommerce_show_product_sale_flash' ) ) {
					woocommerce_show_product_sale_flash();
				}

				// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- Using for consistency.
				call_user_func( $function_name );

				// Reset product's actual featured image id.
				if ( ! $show_image ) {
					$product->set_image_id( $image_id );
				}

				// Reset product's actual gallery image id.
				if ( ! $show_gallery ) {
					$product->set_gallery_image_ids( $gallery_ids );
				}
			}
			break;
		case 'wc_get_stock_html':
			if ( is_a( $product, 'WC_Product' ) ) {
				echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- `wc_get_stock_html` include woocommerce's `single-product/stock.php` template.
			}
			break;
		case 'wc_print_notice':
			$message = et_()->array_get( $args, 'wc_cart_message', '' );

			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			call_user_func( $function_name, $message );
			break;
		case 'wc_print_notices':
			if ( isset( WC()->session ) ) {
				// Save existing notices to restore them as many times as we need.
				$et_wc_cached_notices = WC()->session->get( 'wc_notices', array() );

				// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- Using for consistency.
				call_user_func( $function_name );

				// Restore notices which were removed after wc_print_notices() executed to render multiple modules on page.
				if ( ! empty( $et_wc_cached_notices ) && empty( WC()->session->get( 'wc_notices', array() ) ) ) {
					WC()->session->set( 'wc_notices', $et_wc_cached_notices );
				}
			}
			break;
		case 'woocommerce_checkout_login_form':
			if ( function_exists( 'woocommerce_checkout_login_form' ) ) {
				woocommerce_checkout_login_form();
			}
			if ( function_exists( 'woocommerce_checkout_coupon_form' ) ) {
				woocommerce_checkout_coupon_form();
			}

			$is_builder = et_()->array_get( $args, 'is_builder', false );
			if ( $is_builder ) {
				ET_Builder_Module_Woocommerce_Cart_Notice::output_coupon_error_message();
			}
			break;
		case 'woocommerce_upsell_display':
			$order = isset( $args['order'] ) ? $args['order'] : '';
			// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			call_user_func( $function_name, '', '', '', $order );
			break;
		case 'wc_cart_empty_template':
			wc_get_template( 'cart/cart-empty.php' );
			break;
		case 'woocommerce_output_all_notices':
			if ( isset( WC()->session ) ) {
				// Save existing notices to restore them as many times as we need.
				$et_wc_cached_notices = WC()->session->get( 'wc_notices', array() );

				if ( function_exists( $function_name ) ) {
					// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- Using for consistency.
					call_user_func( $function_name );
				}

				// Restore notices which were removed after wc_print_notices() executed to render multiple modules on page.
				if ( ! empty( $et_wc_cached_notices ) && empty( WC()->session->get( 'wc_notices', array() ) ) ) {
					WC()->session->set( 'wc_notices', $et_wc_cached_notices );
				}
			}
			break;
		case 'woocommerce_template_single_price':
		case 'woocommerce_template_single_meta':
			if ( is_a( $product, 'WC_Product' ) ) {
				/*
				 * Variable functions.
				 * @see https://www.php.net/manual/en/functions.variable-functions.php
				 */
				$function_name();
			}
			break;
		default:
			// Only whitelisted functions shall be allowed until this point of execution.
			if ( is_a( $product, 'WC_Product' ) ) {
				// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- Only whitelisted functions reach here.
				call_user_func( $function_name );
			}
	}

	$output = ob_get_clean();

	// Reset original product variable to global $product.
	if ( $is_use_placeholder ) {
		et_theme_builder_wc_reset_global_objects();
	} elseif ( $overwrite_global ) {
		// Reset $product global.
		if ( $overwrite_product ) {
			$product = $original_product;
		}

		// Reset post.
		if ( $overwrite_post ) {
			$post = $original_post;
		}

		// Reset wp_query.
		if ( $overwrite_wp_query ) {
			$wp_query = $original_wp_query;
		}
		// phpcs:enable WordPress.WP.GlobalVariablesOverride -- Enable global variable override check.
	}

	return $output;
}

/**
 * Renders the content.
 *
 * Rendering the content will enable Divi Builder to take over the entire
 * post content area.
 *
 * @since 3.29
 */
function et_builder_wc_product_render_layout() {
	do_action( 'et_builder_wc_product_before_render_layout' );

	the_content();

	do_action( 'et_builder_wc_product_after_render_layout' );
}

/**
 * Force WooCommerce to load default template over theme's custom template when builder's
 * et_builder_from_scratch is used to prevent unexpected custom layout which makes builder
 * experience inconsistent
 *
 * @since 3.29
 *
 * @param string $template Path to template file.
 * @param string $slug Template slug.
 * @param string $name Template name.
 *
 * @return string
 */
function et_builder_wc_override_template_part( $template, $slug, $name ) {
	// Only force load default `content-single-product.php` template.
	$is_content_single_product = 'content' === $slug && 'single-product' === $name;

	return $is_content_single_product ? WC()->plugin_path() . "/templates/{$slug}-{$name}.php" : $template;
}

/**
 * Disable all default WooCommerce single layout hooks.
 *
 * @since 4.0.10
 */
function et_builder_wc_disable_default_layout() {
	// To remove a hook, the $function_to_remove and $priority arguments must match
	// with which the hook was added.
	remove_action(
		'woocommerce_before_main_content',
		'woocommerce_breadcrumb',
		20
	);

	remove_action(
		'woocommerce_before_single_product_summary',
		'woocommerce_show_product_sale_flash',
		10
	);
	remove_action(
		'woocommerce_before_single_product_summary',
		'woocommerce_show_product_images',
		20
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_title',
		5
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_rating',
		10
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_price',
		10
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_excerpt',
		20
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_add_to_cart',
		30
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_meta',
		40
	);
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_sharing',
		50
	);
	remove_action(
		'woocommerce_after_single_product_summary',
		'woocommerce_output_product_data_tabs',
		10
	);
	remove_action(
		'woocommerce_after_single_product_summary',
		'woocommerce_upsell_display',
		15
	);
	remove_action(
		'woocommerce_after_single_product_summary',
		'woocommerce_output_related_products',
		20
	);
}

/**
 * Relocate all registered callbacks from `woocommerce_single_product_summary` hook to
 * any suitable Woo modules.
 *
 * @since 4.14.5
 * @since 4.15.0 Move relocation process into outside callbacks loop to avoid duplication.
 */
function et_builder_wc_relocate_single_product_summary() {
	global $post, $wp_filter;

	if ( ! $post ) {
		return;
	}

	$tb_body_layout    = ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE;
	$tb_body_override  = et_theme_builder_overrides_layout( $tb_body_layout );
	$tb_layouts        = et_theme_builder_get_template_layouts();
	$tb_body_layout_id = $tb_body_override ? $tb_layouts[$tb_body_layout]['id'] : false;
	$tb_body_content   = $tb_body_layout_id ? get_post_field( 'post_content', $tb_body_layout_id ) : '';
	$has_wc_module     = et_builder_has_woocommerce_module( $post->post_content );
	$has_wc_module_tb  = et_builder_has_woocommerce_module( $tb_body_content );
	$hook              = et_()->array_get( $wp_filter, 'woocommerce_single_product_summary', null );

	// Bail early if there is no `woocommerce_single_product_summary` hook callbacks or
	// if there is no WooCommerce module in the content of current page and TB body layout.
	if (
		( ! $has_wc_module && ! $has_wc_module_tb )
		|| empty( $hook->callbacks ) 
	) {
		return;
	}

	$is_copy_needed = false;
	$is_move_needed = false;
	$post_id        = ! empty( $post->ID ) ? $post->ID : false;

	// Product related pages.
	$is_product          = function_exists( 'is_product' ) && is_product();
	$is_shop             = function_exists( 'is_shop' ) && is_shop();
	$is_product_category = function_exists( 'is_product_category' ) && is_product_category();
	$is_product_tag      = function_exists( 'is_product_tag' ) && is_product_tag();

	// Copy single product summary hooks when current page is:
	// - Product related pages: single, shop, category, & tag.
	// - Theme Builder or Page Builder.
	// - Before & after components AJAX request.
	// - Has TB layouts contain WC modules.
	if (
		$is_product
		|| $is_shop
		|| $is_product_category
		|| $is_product_tag
		|| et_builder_tb_enabled()
		|| et_core_is_fb_enabled()
		|| et_fb_is_before_after_components_callback_ajax()
		|| et_builder_wc_is_non_product_post_type()
	) {
		$is_copy_needed = true;
	}

	// Move single product summary hooks when current page is single product with:
	// - Builder is used.
	// - TB Body layout overrides the content.
	if ( $is_product ) {
		if ( et_pb_is_pagebuilder_used( $post_id ) || $tb_body_override ) {
			$is_move_needed = true;
		}
	}

	/**
	 * Filters whether to copy single product summary hooks output or not.
	 *
	 * 3rd-party plugins can use this filter to force enable or disable this action.
	 *
	 * @since 4.14.5
	 *
	 * @param boolean $is_copy_needed Whether to copy single product summary or not.
	 */
	$is_copy_needed = apply_filters( 'et_builder_wc_relocate_single_product_summary_is_copy_needed', $is_copy_needed );

	/**
	 * Filters whether to move (remove the original) single product summary or not.
	 *
	 * 3rd-party plugins can use this filter to force enable or disable this action.
	 *
	 * @since 4.14.5
	 *
	 * @param boolean $is_move_needed Whether to move single product summary or not.
	 */
	$is_move_needed = apply_filters( 'et_builder_wc_relocate_single_product_summary_is_move_needed', $is_move_needed );

	// Bail early if copy action is not needed.
	if ( ! $is_copy_needed ) {
		return;
	}

	$modules_with_relocation = array();

	/**
	 * Filters the list of ignored `woocommerce_single_product_summary` hook callbacks.
	 *
	 * 3rd-party plugins can use this filter to keep their callbacks so they won't be
	 * relocated from `woocommerce_single_product_summary` hook. The value is string of
	 * `function_name` or `class::method` combination. By default, it contanis all single
	 * product summary actions from WooCommerce plugin.
	 *
	 * @since 4.14.5
	 *
	 * @param array $ignored_callbacks List of ignored callbacks.
	 */
	$ignored_callbacks = apply_filters(
		'et_builder_wc_relocate_single_product_summary_ignored_callbacks',
		array(
			'WC_Structured_Data::generate_product_data',
			'woocommerce_template_single_title',
			'woocommerce_template_single_rating',
			'woocommerce_template_single_price',
			'woocommerce_template_single_excerpt',
			'woocommerce_template_single_add_to_cart',
			'woocommerce_template_single_meta',
			'woocommerce_template_single_sharing',
		)
	);

	// Pair of WooCommerce layout priority numbers and Woo module slugs.
	$modules_priority = array(
		'5'  => 'et_pb_wc_title',
		'10' => 'et_pb_wc_price', // `et_pb_wc_rating` also has the same priority.
		'20' => 'et_pb_wc_description', // It's `excerpt` on WooCommerce default layout.
		'30' => 'et_pb_wc_add_to_cart',
		'40' => 'et_pb_wc_meta',
	);

	foreach ( $hook->callbacks as $callback_priority => $callbacks ) {
		foreach ( $callbacks as $callback_args ) {
			// 1. Generate 'callback name' (string).
			// Get the callback name stored on the `function` argument.
			$callback_function = et_()->array_get( $callback_args, 'function' );
			$callback_name     = $callback_function;

			// Bail early if the callback is not callable to avoid any unexpected issue.
			if ( ! is_callable( $callback_function ) ) {
				continue;
			}

			// If the `function` is an array, it's probably a class based function. We should
			// convert it into string based callback name for validating purpose.
			if ( is_array( $callback_function ) ) {
				$callback_name   = '';
				$callback_object = et_()->array_get( $callback_function, 0 );
				$callback_method = et_()->array_get( $callback_function, 1 );

				// Ensure the index `0` is an object and the index `1` is string. We're going to
				// use the class::method combination as callback name.
				if ( is_object( $callback_object ) && is_string( $callback_method ) ) {
					$callback_class = get_class( $callback_object );
					$callback_name  = "{$callback_class}::{$callback_method}";
				}
			}

			// Bail early if callback name is not string or empty to avoid unexpected issues.
			if ( ! is_string( $callback_name ) || empty( $callback_name ) ) {
				continue;
			}

			// Bail early if current callback is listed on ignored callbacks list.
			if ( in_array( $callback_name, $ignored_callbacks, true ) ) {
				continue;
			}

			// 2. Generate 'module priority' to get suitable 'module slug'.
			// Find the module priority number by round down the priority to the nearest 10.
			// It's needed to get suitable Woo module. For example, a callback with priority
			// 41 means we have to put it on module with priority 40 which is `et_pb_wc_meta`.
			$rounded_callback_priority = intval( floor( $callback_priority / 10 ) * 10 );
			$module_priority           = $rounded_callback_priority;

			// Additional rules for module priority:
			// - 0  : Make it 5 as default to target `et_pb_wc_title` because there is no
			// module with priority less than 5.
			// - 50 : Make it 40 as default to target `et_pb_wc_meta` because there is no
			// module with priority more than 40.
			if ( 0 === $rounded_callback_priority ) {
				$module_priority = 5;
			} elseif ( $rounded_callback_priority >= 50 ) {
				$module_priority = 40;
			}

			$module_slug = et_()->array_get( $modules_priority, $module_priority );

			/**
			 * Filters target module for the current callback.
			 *
			 * 3rd-party plugins can use this filter to target different module slug.
			 *
			 * @since 4.14.5
			 *
			 * @param string $module_slug     Module slug.
			 * @param string $callback_name   Callback name.
			 * @param string $module_priority Module priority.
			 */
			$module_slug = apply_filters( 'et_builder_wc_relocate_single_product_summary_module_slug', $module_slug, $callback_name, $module_priority );

			// Bail early if module slug is empty.
			if ( empty( $module_slug ) ) {
				continue;
			}

			// 3. Determine 'output location'.
			// Move the callback to the suitable Woo module. Since we can't call the action
			// inside the module render, we have to buffer the output and prepend/append it
			// to the module output or preview. By default, the default location is 'after'
			// the module output or preview. But, for priority less than 5, we have to put it
			// before the `et_pb_wc_title` because there is no module on that location.
			$output_location = $callback_priority < 5 ? 'before' : 'after';

			/**
			 * Filters output location for the current module and callback.
			 *
			 * 3rd-party plugins can use this filter to change the output location.
			 *
			 * @since 4.14.5
			 *
			 * @param string $output_location   Output location.
			 * @param string $callback_name     Callback name.
			 * @param string $module_slug       Module slug.
			 * @param string $callback_priority Callback priority.
			 */
			$output_location = apply_filters( 'et_builder_wc_relocate_single_product_summary_output_location', $output_location, $callback_name, $module_slug, $callback_priority );

			// Bail early if the output location is not 'before' or 'after'.
			if ( ! in_array( $output_location, array( 'before', 'after' ), true ) ) {
				continue;
			}

			// 4. Determine 'module output priority'.
			// Get the "{$module_slug}_{$hook_suffix_name}}" filter priority number by sum up
			// default hook priority number (10) and the remainder. This part is important,
			// so we can prepend and append the layout output more accurate. For example:
			// Callback A with priority 42 should be added after callback B with priority 41
			// on `et_pb_wc_meta` module. So, "et_pb_wc_meta_{$hook_suffix_name}_output" hook
			// priority for callback A will be 12, meanwhile callback B will be 11.
			$remainder_priority = $rounded_callback_priority > 0 ? $callback_priority % 10 : $callback_priority - 5;
			$output_priority    = 10 + $remainder_priority;

			/**
			 * Filters module output priority number for the current module and callback.
			 *
			 * 3rd-party plugins can use this filter to rearrange the output priority.
			 *
			 * @since 4.14.5
			 *
			 * @param string $output_priority   Module output priority number.
			 * @param string $callback_name     Callback name.
			 * @param string $module_slug       Module slug.
			 * @param string $callback_priority Callback priority.
			 */
			$output_priority = apply_filters( 'et_builder_wc_relocate_single_product_summary_output_priority', $output_priority, $callback_name, $module_slug, $callback_priority );

			// Remove the callback from `woocommerce_single_product_summary` when it's needed.
			if ( $is_move_needed ) {
				remove_action( 'woocommerce_single_product_summary', $callback_function, $callback_priority );
			}

			// And, copy and paste it to suitable location & module.
			add_action( "et_builder_wc_single_product_summary_{$output_location}_{$module_slug}", $callback_function, $output_priority );

			$modules_with_relocation[] = $module_slug;
		}
	}

	// Finally, move it to suitable Woo modules.
	if ( ! empty( $modules_with_relocation ) ) {
		foreach ( $modules_with_relocation as $module_slug ) {
			// Builder - Before and/or after components.
			add_filter( "{$module_slug}_fb_before_after_components", 'et_builder_wc_single_product_summary_before_after_components', 10, 3 );

			// FE - Shortcode output.
			add_filter( "{$module_slug}_shortcode_output", 'et_builder_wc_single_product_summary_module_output', 10, 3 );
		}
	}
}

/**
 * Prepend and/or append callback output to the suitable module output on FE.
 *
 * @since 4.14.5
 *
 * @param string             $module_output   Module output.
 * @param string             $module_slug     Module slug.
 * @param ET_Builder_Element $module_instance Module instance.
 *
 * @return string Processed module output.
 */
function et_builder_wc_single_product_summary_module_output( $module_output, $module_slug, $module_instance ) {
	// Bail early if module output is not string.
	if ( ! is_string( $module_output ) ) {
		return $module_output;
	}

	global $post, $product;

	$original_post    = $post;
	$original_product = $product;
	$target_id        = '';
	$is_overwritten   = false;

	if ( ! empty( $module_instance->props ) ) {
		// Get target ID if any.
		$target_id = et_()->array_get( $module_instance->props, 'product' );
		$target_id = class_exists( 'ET_Builder_Element' ) ? ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( $target_id ) : $target_id;
	}

	// Determine whether global product and post objects need to be overwritten or not.
	if ( 'current' !== $target_id ) {
		$target_product = wc_get_product( $target_id );

		if ( $target_product instanceof WC_Product ) {
			$is_overwritten = false;
			$product        = $target_product;
			$post           = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Overriding global post is safe as original $post has been restored at the end.
		}
	}

	// Get before & after outputs only if product is WC_Product instance.
	if ( $product instanceof WC_Product ) {
		$before_output = et_builder_wc_single_product_summary_before_module( $module_slug );
		$after_output  = et_builder_wc_single_product_summary_after_module( $module_slug );
		$module_output = $before_output . $module_output . $after_output;
	}

	// Reset product and/or post object.
	if ( $is_overwritten ) {
		$product = $original_product;
		$post    = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Restore global post.
	}

	return $module_output;
}

/**
 * Set callback output as before and/or after components on builder.
 *
 * @since 4.14.5
 *
 * @param array  $module_components Default module before & after components.
 * @param string $module_slug       Module slug.
 * @param array  $module_data       Module data.
 *
 * @return array Processed module before & after components.
 */
function et_builder_wc_single_product_summary_before_after_components( $module_components, $module_slug, $module_data ) {
	// Bail early if module components variable is not an array.
	if ( ! is_array( $module_components ) ) {
		return $module_components;
	}

	global $post, $product;

	$original_post    = $post;
	$original_product = $product;
	$target_id        = '';
	$overwritten_by   = '';
	$is_tb_enabled    = et_builder_tb_enabled();
	$is_fb_enabled    = et_core_is_fb_enabled() || is_et_pb_preview();

	if ( ! empty( $module_data ) ) {
		// Get target ID if any.
		$target_id = et_()->array_get( $module_data, array( 'module_attrs', 'product' ) );
		$target_id = class_exists( 'ET_Builder_Element' ) ? ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( $target_id ) : $target_id;
	}

	// Determine whether global product and post objects need to be overwritten or not.
	// - Dummy product:  TB and FB initial load.
	// - Target product: Components request from builder.
	if ( $is_tb_enabled || $is_fb_enabled ) {
		et_theme_builder_wc_set_global_objects( array( 'is_tb' => true ) );
		$overwritten_by = 'dummy_product';
	} elseif ( 'current' !== $target_id && et_fb_is_before_after_components_callback_ajax() ) {
		$target_product = wc_get_product( $target_id );

		if ( $target_product instanceof WC_Product ) {
			$overwritten_by = 'target_product';
			$product        = $target_product;
			$post           = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Overriding global post is safe as original $post has been restored at the end.
		}
	}

	// Get before & after components only if product is WC_Product instance.
	if ( $product instanceof WC_Product ) {
		$default_before_component = et_()->array_get( $module_components, '__before_component', '' );
		$default_after_component  = et_()->array_get( $module_components, '__after_component', '' );
		$current_before_component = et_builder_wc_single_product_summary_before_module( $module_slug );
		$current_after_component  = et_builder_wc_single_product_summary_after_module( $module_slug );

		$module_components['has_components']     = true;
		$module_components['__before_component'] = $default_before_component . $current_before_component;
		$module_components['__after_component']  = $default_after_component . $current_after_component;
	}

	// Reset product and/or post object.
	if ( 'dummy_product' === $overwritten_by ) {
		et_theme_builder_wc_reset_global_objects( array( 'is_tb' => true ) );
	} elseif ( 'target_product' === $overwritten_by ) {
		$product = $original_product;
		$post    = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Restore global post.
	}

	return $module_components;
}

/**
 * Render single product summary before Woo module output.
 *
 * @since 4.14.5
 *
 * @param string $module_slug Module slug.
 *
 * @return string Rendered output.
 */
function et_builder_wc_single_product_summary_before_module( $module_slug ) {
	ob_start();

	/**
	 * Fires additional output for single product summary before module output.
	 *
	 * @since 4.14.5
	 */
	do_action( "et_builder_wc_single_product_summary_before_{$module_slug}" );

	return ob_get_clean();
}

/**
 * Render single product summary after Woo module output.
 *
 * @since 4.14.5
 *
 * @param string $module_slug Module slug.
 *
 * @return string Rendered output.
 */
function et_builder_wc_single_product_summary_after_module( $module_slug ) {
	ob_start();

	/**
	 * Fires additional output for single product summary after module output.
	 *
	 * @since 4.14.5
	 */
	do_action( "et_builder_wc_single_product_summary_after_{$module_slug}" );

	return ob_get_clean();
}

/**
 * Overrides the default WooCommerce layout.
 *
 * @see woocommerce/includes/wc-template-functions.php
 *
 * @since 3.29
 */
function et_builder_wc_override_default_layout() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}

	// global $post won't be available with `after_setup_theme` hook and hence `wp` hook is used.
	global $post;

	if ( ! et_pb_is_pagebuilder_used( $post->ID ) ) {
		return;
	}

	$product_page_layout         = et_builder_wc_get_product_layout( $post->ID );
	$is_product_content_modified = 'modified' === get_post_meta( $post->ID, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY, true );
	$is_preview_loading          = is_preview();

	// BFB was enabled but page content wasn't saved yet. Load default layout on FE.
	if ( 'et_build_from_scratch' === $product_page_layout && ! $is_product_content_modified && ! $is_preview_loading ) {
		return;
	}

	/*
	 * The `has_shortcode()` check does not work here. Hence solving the need using `strpos()`.
	 *
	 * The WHY behind the check is explained in the following issue.
	 * @see https://github.com/elegantthemes/Divi/issues/16155
	 */
	if ( ! $product_page_layout && ! et_core_is_fb_enabled() || ( $product_page_layout && 'et_build_from_scratch' !== $product_page_layout )
	) {
		return;
	}

	// Force use WooCommerce's default template if current theme is not Divi or Extra (handling
	// possible custom template on DBP / Child Theme).
	if ( ! in_array( wp_get_theme()->get( 'Name' ), array( 'Divi', 'Extra' ), true ) ) {
		add_filter( 'wc_get_template_part', 'et_builder_wc_override_template_part', 10, 3 );
	}

	et_builder_wc_disable_default_layout();

	do_action( 'et_builder_wc_product_before_render_layout_registration' );

	// Add render content on product page.
	add_action( 'woocommerce_after_single_product_summary', 'et_builder_wc_product_render_layout', 5 );
}

/**
 * Skips setting default content on Product post type during Builder activation.
 *
 * Otherwise, the description would be shown in both Product Tabs and at the end of the
 * default WooCommerce layout set at
 *
 * @see et_builder_wc_get_prefilled_product_page_content()
 *
 * @since 3.29
 *
 * @param bool    $flag Whether to skips the content activation.
 * @param WP_Post $post Post.
 *
 * @return bool
 */
function et_builder_wc_skip_initial_content( $flag, $post ) {
	if ( ! ( $post instanceof WP_Post ) ) {
		return $flag;
	}

	if ( 'product' !== $post->post_type ) {
		return $flag;
	}

	return true;
}

/**
 * Determine whether given content has WooCommerce module inside it or not
 *
 * @since 4.0 Added ET_Builder_Element class exists check.
 * @since 3.29
 *
 * @param string $content Content.
 *
 * @return bool
 */
function et_builder_has_woocommerce_module( $content = '' ) {
	if ( ! class_exists( 'ET_Builder_Element' ) ) {
		return false;
	}

	$has_woocommerce_module = false;
	$woocommerce_modules    = ET_Builder_Element::get_woocommerce_modules();

	foreach ( $woocommerce_modules as $module ) {
		if ( has_shortcode( $content, $module ) ) {
			$has_woocommerce_module = true;

			// Stop the loop once any shortcode is found.
			break;
		}
	}

	return apply_filters( 'et_builder_has_woocommerce_module', $has_woocommerce_module );
}

/**
 * Check if current global $post uses builder / layout block, not `product` CPT, and contains
 * WooCommerce module inside it. This check is needed because WooCommerce by default only adds
 * scripts and style to `product` CPT while WooCommerce Modules can be used at any CPT
 *
 * @since 3.29
 * @since 4.1.0 check if layout block is used instead of builder
 *
 * @since bool
 */
function et_builder_wc_is_non_product_post_type() {
	if ( wp_doing_ajax() ) {
		return false;
	}

	global $post;

	if ( $post && 'product' === $post->post_type ) {
		return false;
	}

	$types   = et_theme_builder_get_layout_post_types();
	$layouts = et_theme_builder_get_template_layouts();

	foreach ( $types as $type ) {
		if ( ! isset( $layouts[ $type ] ) ) {
			continue;
		}

		if ( $layouts[ $type ]['override'] && et_builder_has_woocommerce_module( get_post_field( 'post_content', $layouts[ $type ]['id'] ) ) ) {
			return true;
		}
	}

	// If no post found, bail early.
	if ( ! $post ) {
		return false;
	}

	$is_builder_used      = et_pb_is_pagebuilder_used( $post->ID );
	$is_layout_block_used = has_block( 'divi/layout', $post->post_content );

	// If no builder or layout block used, bail early.
	if ( ! $is_builder_used && ! $is_layout_block_used ) {
		return false;
	}

	$has_wc_module = et_builder_has_woocommerce_module( $post->post_content );

	if ( ( $is_builder_used || $is_layout_block_used ) && $has_wc_module ) {
		return true;
	}

	return false;
}


/**
 * Load WooCommerce related scripts. This function basically redo what
 * `WC_Frontend_Scripts::load_scripts()` does without the `product` CPT limitation.
 *
 * Once more WooCommerce Modules are added (checkout, account, etc), revisit this method and
 * compare it against `WC_Frontend_Scripts::load_scripts()`. Some of the script queues are
 * removed here because there is currently no WooCommerce module equivalent of them.
 *
 * @since 3.29
 * @since 4.3.3 Loads WC scripts on Shop, Product Category & Product Tags archives.
 * @since 4.9.11 Avoid invalid argument supplied for foreach() warning.
 */
function et_builder_wc_load_scripts() {
	global $post;

	$is_shop     = function_exists( 'is_shop' ) && is_shop();
	$is_checkout = function_exists( 'is_checkout' ) && is_checkout();

	// is_product_taxonomy() is not returning TRUE for Category & Tags.
	// Hence we check Category & Tag archives individually.
	$is_product_category = function_exists( 'is_product_category' ) && is_product_category();
	$is_product_tag      = function_exists( 'is_product_tag' ) && is_product_tag();

	// If current page is not non-`product` CPT which using builder, stop early.
	if ( ( ! et_builder_wc_is_non_product_post_type()
		|| ! class_exists( 'WC_Frontend_Scripts' ) )
		&& function_exists( 'et_fb_enabled' )
		&& ! et_core_is_fb_enabled()
		&& ! $is_shop
		&& ! $is_product_category
		&& ! $is_product_tag
		&& ! $is_checkout
	) {
		return;
	}

	// Simply enqueue the scripts; All of them have been registered.
	if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
	}

	if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
		wp_enqueue_script( 'zoom' );
	}
	if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
		wp_enqueue_script( 'flexslider' );
	}
	if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
		wp_enqueue_script( 'photoswipe-ui-default' );
		wp_enqueue_style( 'photoswipe-default-skin' );

		add_action( 'wp_footer', 'woocommerce_photoswipe' );
	}
	wp_enqueue_script( 'wc-single-product' );

	if ( 'geolocation_ajax' === get_option( 'woocommerce_default_customer_address' ) ) {
		$ua = strtolower( wc_get_user_agent() ); // Exclude common bots from geolocation by user agent.

		if ( ! strstr( $ua, 'bot' ) && ! strstr( $ua, 'spider' ) && ! strstr( $ua, 'crawl' ) ) {
			wp_enqueue_script( 'wc-geolocation' );
		}
	}

	wp_enqueue_script( 'woocommerce' );
	wp_enqueue_script( 'wc-cart-fragments' );
	wp_enqueue_script( 'wc-checkout' );
	wp_enqueue_script( 'select2' );
	wp_enqueue_script( 'selectWoo' );
	wp_enqueue_style( 'select2' );

	// Enqueue style.
	$wc_styles = WC_Frontend_Scripts::get_styles();

	/*
	 * Since $wc_styles is passed in to `woocommerce_enqueue_styles` filter,
	 * ensure that the value is array.
	 *
	 * @see https://github.com/elegantthemes/divi-builder/issues/1268
	 */
	if ( ! is_array( $wc_styles ) ) {
		return;
	}

	foreach ( $wc_styles as $style_handle => $wc_style ) {
		if ( ! isset( $wc_style['has_rtl'] ) ) {
			$wc_style['has_rtl'] = false;
		}

		wp_enqueue_style( $style_handle, $wc_style['src'], $wc_style['deps'], $wc_style['version'], $wc_style['media'], $wc_style['has_rtl'] );
	}
}
/**
 * Add WooCommerce body class name on non `product` CPT builder page
 *
 * @param array $classes CSS class names.
 *
 * @return array
 * @since 3.29
 */
function et_builder_wc_add_body_class( $classes ) {
	if ( et_builder_wc_is_non_product_post_type() || is_et_pb_preview() ) {
		$classes[] = 'woocommerce';
		$classes[] = 'woocommerce-page';
	}

	return $classes;
}

/**
 * Add product class name on inner content wrapper page on non `product` CPT builder page with woocommerce modules
 * And on Product posts
 *
 * @param array $classes Product class names.
 *
 * @return array
 * @since 3.29
 */
function et_builder_wc_add_inner_content_class( $classes ) {
	// The class is required on any post with woocommerce modules and on product pages.
	if ( et_builder_wc_is_non_product_post_type() || is_product() || is_et_pb_preview() ) {
		$classes[] = 'product';
	}

	return $classes;
}

/**
 * Add WooCommerce class names on Divi Shop Page (not WooCommerce Shop).
 *
 * @since 4.0.7
 *
 * @param array $classes Array of Classes.
 *
 * @return array
 */
function et_builder_wc_add_outer_content_class( $classes ) {
	$body_classes = get_body_class();

	// Add Class only to WooCommerce Shop page if built using Divi (i.e. Divi Shop page).
	if ( ! ( function_exists( 'is_shop' ) && is_shop() ) ) {
		return $classes;
	}

	// Add Class only when the WooCommerce Shop page is built using Divi.
	if ( ! et_builder_wc_is_non_product_post_type() ) {
		return $classes;
	}

	// Precautionary check: $body_classes should always be an array.
	if ( ! is_array( $body_classes ) ) {
		return $classes;
	}

	// Add Class only when the <body> tag does not contain them.
	$woocommerce_classes = array( 'woocommerce', 'woocommerce-page' );
	$common_classes      = array_intersect(
		$body_classes,
		array(
			'woocommerce',
			'woocommerce-page',
		)
	);
	if ( is_array( $common_classes ) && count( $woocommerce_classes ) === count( $common_classes ) ) {
		return $classes;
	}

	// Precautionary check: $classes should always be an array.
	if ( ! is_array( $classes ) ) {
		return $classes;
	}

	$classes[] = 'woocommerce';
	$classes[] = 'woocommerce-page';

	return $classes;
}

/**
 * Sets the Product page layout post meta on two occurrences.
 *
 * They are 1) On WP Admin Publish/Update post 2) On VB Save.
 *
 * @since 4.14.0 Remove ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY meta key on non-product post types.
 *           Also move `since` section above `param` section.
 * @since 3.29
 *
 * @param int $post_id Post ID.
 */
function et_builder_set_product_page_layout_meta( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	/*
	 * The Product page layout post meta adds no meaning to the Post when the Builder is not used.
	 * Hence the meta key/value is removed, when the Builder is turned off.
	 */
	if ( ! et_pb_is_pagebuilder_used( $post_id ) ) {
		delete_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY );
		return;
	}

	// The meta key is to be used only on Product post types.
	// Hence remove the meta if exists on other post types.
	$is_non_product_post_type = 'product' !== $post->post_type;
	if ( $is_non_product_post_type ) {
		// Returns FALSE when no meta key is found.
		delete_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY );

		return;
	}

	// Do not update Product page layout post meta when it contains a value.
	$product_page_layout = get_post_meta(
		$post_id,
		ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY,
		true
	);
	if ( $product_page_layout ) {
		return;
	}

	$product_page_layout = et_get_option(
		'et_pb_woocommerce_page_layout',
		'et_build_from_scratch'
	);

	update_post_meta(
		$post_id,
		ET_BUILDER_WC_PRODUCT_PAGE_LAYOUT_META_KEY,
		sanitize_text_field( $product_page_layout )
	);
}

/**
 * Sets the Product content status as modified during VB save.
 *
 * This avoids setting the default WooCommerce Modules layout more than once.
 *
 * @link https://github.com/elegantthemes/Divi/issues/16420
 *
 * @param int $post_id Post ID.
 */
function et_builder_set_product_content_status( $post_id ) {
	if ( 0 === absint( $post_id ) ) {
		return;
	}

	if ( 'product' !== get_post_type( $post_id ) || 'modified' === get_post_meta(
		$post_id,
		ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY,
		true
	) ) {
		return;
	}

	update_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY, 'modified' );
}

/**
 * Gets Woocommerce Tabs for the given Product ID.
 *
 * @since 4.4.2
 */
function et_builder_get_woocommerce_tabs() {
	// Nonce verification.
	et_core_security_check( 'edit_posts', 'et_builder_get_woocommerce_tabs', 'nonce' );

	$_          = et_();
	$product_id = $_->array_get( $_POST, 'product', 0 );

	if ( null === $product_id || ! et_is_woocommerce_plugin_active() ) {
		wp_send_json_error();
	}

	// Allow Latest Product ID which is a string 'latest'.
	// `This Product` tabs are defined in et_fb_current_page_params().
	if ( ! in_array( $product_id, array( 'current', 'latest' ), true ) && 0 === absint( $product_id ) ) {
		wp_send_json_error();
	}

	$tabs = ET_Builder_Module_Woocommerce_Tabs::get_tabs( array( 'product' => $product_id ) );

	wp_send_json_success( $tabs );
}

/**
 * Returns alternative hook to make Woo Extra Product Options display fields in FE when TB is
 * enabled.
 *
 * - The Woo Extra Product Options addon does not display the extra fields on the FE.
 * - This is because the original hook i.e. `woocommerce_before_single_product` in the plugin
 * is not triggered when TB is enabled.
 * - Hence return a suitable hook that is fired for all types of Products i.e. Simple, Variable,
 * etc.
 *
 * @param string $hook Hook name.
 *
 * @return string WooCommerce Hook that is being fired on TB enabled Product pages.
 * @see WEPOF_Product_Options_Frontend::define_public_hooks()
 *
 * @since 4.0.9
 */
function et_builder_trigger_extra_product_options( $hook ) {
	return 'woocommerce_before_add_to_cart_form';
}

/**
 * Strip Builder shortcodes to avoid nested parsing.
 *
 * @see   https://github.com/elegantthemes/Divi/issues/18682
 *
 * @param string $content Post content.
 *
 * @since 4.3.3
 *
 * @return string
 */
function et_builder_avoid_nested_shortcode_parsing( $content ) {
	if ( is_et_pb_preview() ) {
		return $content;
	}

	// Strip shortcodes only on non-builder pages that contain Builder shortcodes.
	if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		return $content;
	}

	// WooCommerce layout loads when builder is not enabled.
	// So strip builder shortcodes from Post content.
	if ( function_exists( 'is_product' ) && is_product() ) {
		return et_strip_shortcodes( $content );
	}

	// Strip builder shortcodes from non-product pages.
	// Only Tabs shortcode is checked since that causes nested rendering.
	if ( has_shortcode( $content, 'et_pb_wc_tabs' ) ) {
		return et_strip_shortcodes( $content );
	}

	return $content;
}

/**
 * Parses Product description to
 *
 * - converts any [embed][/embed] shortcode to its respective HTML.
 * - strips `et_` shortcodes to avoid nested rendering in Woo Tabs module.
 * - adds <p> tag to keep the paragraph sanity.
 * - runs other shortcodes if any using do_shortcode.
 *
 * @since 4.4.1
 *
 * @param string $description Product description i.e. Post content.
 *
 * @return string
 */
function et_builder_wc_parse_description( $description ) {
	if ( ! is_string( $description ) ) {
		return $description;
	}

	global $wp_embed;

	$parsed_description = et_strip_shortcodes( $description );
	$parsed_description = $wp_embed->run_shortcode( $parsed_description );
	$parsed_description = do_shortcode( $parsed_description );
	$parsed_description = wpautop( $parsed_description );

	return $parsed_description;
}

/**
 * Deletes ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY when Builder is OFF.
 *
 * The deletion allows switching between Divi Builder and the GB builder smoothly.
 *
 * @link https://github.com/elegantthemes/Divi/issues/22477
 *
 * @since 4.14.0
 *
 * @param WP_Post $post Post Object.
 */
function et_builder_wc_delete_post_meta( $post ) {
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	if ( et_pb_is_pagebuilder_used( $post->ID ) ) {
		return;
	}

	delete_post_meta( $post->ID, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY );
}

/**
 * Adds the Preview class to the wrapper.
 *
 * @param string $maybe_class_string Classnames string.
 * @return string
 */
function et_builder_wc_add_preview_wrap_class( $maybe_class_string ) {
	if ( ! is_string( $maybe_class_string ) ) {
		return $maybe_class_string;
	}

	$classes   = explode( ' ', $maybe_class_string );
	$classes[] = 'product';

	return implode( ' ', $classes );
}

/**
 * Entry point for the woocommerce-modules.php file.
 *
 * @since 3.29
 */
function et_builder_wc_init() {
	// global $post won't be available with `after_setup_theme` hook and hence `wp` hook is used.
	add_action( 'wp', 'et_builder_wc_override_default_layout' );

	// Add WooCommerce class names on non-`product` CPT which uses builder.
	add_filter( 'body_class', 'et_builder_wc_add_body_class' );
	add_filter( 'et_builder_inner_content_class', 'et_builder_wc_add_inner_content_class' );
	add_filter( 'et_pb_preview_wrap_class', 'et_builder_wc_add_preview_wrap_class' );
	add_filter( 'et_builder_outer_content_class', 'et_builder_wc_add_outer_content_class' );

	// Load WooCommerce related scripts.
	add_action( 'wp_enqueue_scripts', 'et_builder_wc_load_scripts', 15 );

	add_filter(
		'et_builder_skip_content_activation',
		'et_builder_wc_skip_initial_content',
		10,
		2
	);

	// Show Product Content dropdown settings under
	// Divi Theme Options ⟶ Builder ⟶ Post TYpe Integration.
	add_filter( 'et_builder_settings_definitions', 'et_builder_wc_add_settings' );

	/**
	 * Adds the metabox only to Product post type.
	 *
	 * This is achieved using the post type hook - add_meta_boxes_{post_type}.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
	 *
	 * @since 3.29
	 */
	add_action( 'add_meta_boxes_product', 'et_builder_wc_long_description_metabox_register' );

	// Saves the long description metabox data.
	// Since `et_pb_metabox_settings_save_details()` already uses `save_post` hook
	// to save `_et_pb_old_content` post meta,
	// we use this additional hook `et_pb_old_content_updated`.
	add_action( 'et_pb_old_content_updated', 'et_builder_wc_long_description_metabox_save', 10, 3 );

	/*
	 * 01. Sets the initial Content when `Use Divi Builder` button is clicked
	 * in the Admin dashboard.
	 * 02. Sets the initial Content when `Enable Visual Builder` is clicked.
	 */
	add_filter(
		'et_fb_load_raw_post_content',
		'et_builder_wc_set_prefilled_page_content',
		10,
		2
	);

	add_action( 'et_save_post', 'et_builder_set_product_page_layout_meta' );

	/*
	 * Set the Product modified status as modified upon save to make sure default layout is not
	 * loaded more than one time.
	 *
	 * @see https://github.com/elegantthemes/Divi/issues/16420
	 */
	add_action( 'et_update_post', 'et_builder_wc_set_page_content_status' );

	/*
	 * Handle get Woocommerce tabs AJAX call initiated by Tabs checkbox in settings modal.
	 */
	add_action( 'wp_ajax_et_builder_get_woocommerce_tabs', 'et_builder_get_woocommerce_tabs' );

	/*
	 * Fix Woo Extra Product Options addon compatibility.
	 * @see https://github.com/elegantthemes/Divi/issues/17909
	 */
	add_filter( 'thwepof_hook_name_before_single_product', 'et_builder_trigger_extra_product_options' );

	/*
	 * Fix nested parsing on non-builder product pages w/ shortcode content.
	 * @see https://github.com/elegantthemes/Divi/issues/18682
	 */
	add_filter( 'the_content', 'et_builder_avoid_nested_shortcode_parsing' );

	add_filter( 'et_builder_wc_description', 'et_builder_wc_parse_description' );

	add_filter( 'template_redirect', 'et_builder_wc_template_redirect', 9 );

	/*
	 * Delete `_et_pb_woo_page_content_status` post meta when Divi Builder is off
	 * when using GB editor.
	 *
	 * The latest value of `_et_pb_use_builder` post meta is only available in
	 * `rest_after_insert_page` and NOT in `rest_insert_page` hook.
	 *
	 * This action is documented in
	 * wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php
	 */
	add_action( 'rest_after_insert_page', 'et_builder_wc_delete_post_meta' );

	add_filter( 'woocommerce_checkout_redirect_empty_cart', 'et_builder_stop_cart_redirect_while_enabling_builder' );

	/*
	 * `wp_loaded` is used intentionally because
	 * `get_cart()` should not be called before wp_loaded hook.
	 */
	add_action(
		'wp_loaded',
		'et_builder_handle_shipping_calculator_update_btn_click'
	);

	/*
	 * In the case of dynamic module framework's shortcode manager
	 * we need to fire this hook on its own,
	 */
	if ( ! et_builder_should_load_all_module_data() ) {
		add_action(
			'et_builder_module_lazy_shortcodes_registered',
			[
				'ET_Builder_Module_Woocommerce_Cart_Notice',
				'disable_default_notice',
			]
		);

		add_action(
			'et_builder_module_lazy_shortcodes_registered',
			[
				'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
				'maybe_invoke_woocommerce_hooks',
			]
		);
	}

	// Relocate WC single product summary hooks to any suitable modules.
	add_action( 'et_builder_ready', 'et_builder_wc_relocate_single_product_summary' );
}

et_builder_wc_init();

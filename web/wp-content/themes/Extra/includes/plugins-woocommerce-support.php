<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function extra_setup_woocommerce(){
	if ( !class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_filter( 'woocommerce_product_tabs', 'extra_woocommerce_product_tabs', 98 );

	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	add_action( 'init', 'extra_woocommerce_remove_loop_button' );

	add_filter( 'woocommerce_show_page_title', '__return_false' );

	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	add_action( 'woocommerce_before_main_content', 'extra_woocommerce_output_content_wrapper', 10 );

	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_after_main_content', 'extra_woocommerce_output_content_wrapper_end', 10 );

	add_action( 'woocommerce_before_shop_loop_item_title', 'extra_woocommerce_before_shop_loop_item_title_add_et_overlay', 11 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

	add_action( 'woocommerce_share', 'extra_woocommerce_share' );

	add_filter( 'woocommerce_output_related_products_args', 'extra_woocommerce_output_related_products_args' );

	add_filter( 'woocommerce_get_image_size_shop_catalog', 'extra_woocommerce_get_image_size_shop_catalog' );
	add_filter( 'woocommerce_get_image_size_shop_single', 'extra_woocommerce_get_image_size_shop_single' );
	add_filter( 'woocommerce_get_image_size_shop_thumbnail', 'extra_woocommerce_get_image_size_shop_thumbnail' );

	add_filter( 'extra_dynamic_selectors', 'extra_dynamic_selectors_woocommerce_accent_color' );
}

add_action( 'after_setup_theme', 'extra_setup_woocommerce', 11 );

function extra_woocommerce_output_related_products_args( $args ) {
	if ( extra_get_sidebar_class() ) {
		$columns = 3;
	} else {
		$columns = 4;
	}

	$args['columns'] = $args['posts_per_page'] = $columns;

	return $args;
}

function extra_woocommerce_share() {
	printf(
		'<div class="product-share-links">
			<div class="centered clearfix">
				<div class="social-icons ed-social-share-icons clearfix">
				%s
				</div>
			</div>
		</div>',
		extra_post_share_links( false )
	);
}

function extra_woocommerce_product_tabs( $tabs ) {
	global $product, $post;

	if ( $post->post_content ) {
		$tabs['description']['priority'] = 5;
	}

	if ( comments_open() ) {
		$tabs['reviews']['priority'] = 10;
	}

	if ( $product && ( $product->has_attributes() || ( $product->has_dimensions() || $product->has_weight() ) ) ) {
		$tabs['additional_information']['title'] = esc_html__( 'Additional Info', 'extra' );
		$tabs['additional_information']['priority'] = 7;
	}

	return $tabs;
}

function extra_woocommerce_remove_loop_button(){
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}

function extra_woocommerce_before_shop_loop_item_title_add_et_overlay() {
	global $product, $woocommerce;

	if ( ! empty( $woocommerce->version ) && version_compare( $woocommerce->version, '3.0.0', '>=' ) ) {
		$rating_html = wc_get_rating_html( $product->get_average_rating() );
	} else {
		$rating_html = $product->get_rating_html();
	}

	echo '<span class="et_pb_extra_overlay">';

	if ( $rating_html ) {
		echo $rating_html;
	}

	echo '</span>';
	echo '</a><a href="' . get_the_permalink() . '">';
}

function extra_woocommerce_output_content_wrapper() {
	echo '
		<div id="main-content">
			<div class="container">
				<div id="content-area" class="clearfix">
					<div class="woocommerce-page-top">
					    <div class="et_pb_row">';

	if ( !is_singular( 'product' ) ) {
		echo '<h1 class="page-title">' . woocommerce_page_title( false ). '</h1>';
	}

	// `woocommerce_before_main_content` is triggered at
	// `woocommerce/templates/single-product.php` template.
	// This means, global $product should be set and ready to use.
	global $product;
	if ( is_a( $product, 'WC_Product' )
	     && ! et_pb_is_pagebuilder_used( $product->get_id() ) ) {
		woocommerce_breadcrumb();
	}

	echo '				</div>
					</div>
					<div class="et_pb_extra_column_main">';
}

function extra_woocommerce_output_content_wrapper_end() {
	echo '
					</div> <!--.et_pb_extra_column_main -->';

	woocommerce_get_sidebar();

	echo '
				</div> <!-- #content-area -->
			</div> <!-- .container -->
		</div> <!-- #main-content -->';
}

function extra_woocommerce_get_image_size_shop_catalog() {
	return array(
		'width'  => '440',
		'height' => '440',
		'crop'   => 1,
	);
}

function extra_woocommerce_get_image_size_shop_single() {
	return array(
		'width'  => '627',
		'height' => '9999',
		'crop'   => 0,
	);
}

function extra_woocommerce_get_image_size_shop_thumbnail() {
	return array(
		'width'  => '192',
		'height' => '192',
		'crop'   => 1,
	);
}

if ( ! function_exists( 'woocommerce_output_product_data_tabs' ) ) {
	function woocommerce_output_product_data_tabs() {
		wp_enqueue_script( 'jquery-ui-accordion' );

		$tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if ( empty( $tabs ) ) {
			return;
		}

		global $post;

		$data_desc_active = !$post->post_excerpt ? '1' : '';
		?>
		<div class="extra-woocommerce-details-accordion" data-desc-tab-active="<?php echo esc_attr( $data_desc_active ); ?>">
			<?php foreach ($tabs as $key => $tab) { ?>
				<div class="group" id="group-<?php echo $key ?>">
					<div class="header">
						<h3 class="title"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></h3>
					</div>
					<div class="content">
						<?php call_user_func( $tab['callback'], $key, $tab ) ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}

function extra_dynamic_selectors_woocommerce_accent_color( $selectors ) {
	$selectors['accent_color_color'] = array_merge( $selectors['accent_color_color'], array(
		'.woocommerce .summary .price .amount, .woocommerce-page .summary .price .amount',
		'.et_pb_widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .total .amount',
		'.woocommerce .star-rating span:before',
		'.woocommerce ul.products li.product a .price ins .amount',
		'.woocommerce-page ul.products li.product a .price ins .amount',
		'.woocommerce ul.products li.product a .price .amount',
		'.woocommerce-page ul.products li.product a .price .amount',
		'.woocommerce ul.products li.product a .amount, .woocommerce-page ul.products li.product a .amount',
		'widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .product_list_widget li a.remove',
		'.woocommerce ul.products li.product a .price ins',
		'.woocommerce-page ul.products li.product a .price ins',
		'.et_pb_widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .price_label .to',
		'.et_pb_widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .price_label .from',
	));

	$selectors['accent_color_background_color'] = array_merge( $selectors['accent_color_background_color'], array(
		'.woocommerce button.button.alt',
		'.woocommerce .et_pb_widget .buttons .button',
		'.woocommerce .et_pb_widget .buttons .button:hover',
		'.woocommerce .et_pb_widget .buttons .button:after',
		'.woocommerce input[type="submit"]',
		'.woocommerce #respond #submit',
		'.woocommerce .button.alt',
		'.et_pb_widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .product_list_widget li a.remove:hover',
		'.et_pb_widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider .ui-slider-range',
	));

	$selectors['accent_color_border_color'] = array_merge( $selectors['accent_color_border_color'], array(
		'.woocommerce div.product .woocommerce-tabs ul.tabs li.active a',
	));

	$selectors['footer_widget_links'] = array_merge( $selectors['footer_widget_links'], array(
		'#footer .et_pb_widget.woocommerce .product_list_widget li a',
	));

	return $selectors;
}

function extra_woocommerce_before_shop_loop_item() {
		echo '<div class="product-wrapper">';
}

add_action( 'woocommerce_before_shop_loop_item', 'extra_woocommerce_before_shop_loop_item' );

function extra_woocommerce_after_shop_loop_item() {
		echo '</div>';
}

add_action( 'woocommerce_after_shop_loop_item', 'extra_woocommerce_after_shop_loop_item' );

function extra_woocommerce_buttons_selectors( $selectors ) {
	if ( class_exists( 'woocommerce' ) ) {
		$woocommerce_button_selectors = apply_filters( 'extra_woocommerce_buttons_selectors', array(
			'.woocommerce .button',
			'.woocommerce-page .button',
			'.woocommerce input.button',
			'.woocommerce-page input.button',
			'.woocommerce input[type="submit"]',
			'.woocommerce-page input[type="submit"]',
			'.woocommerce .cart input.button',
			'.woocommerce-page .cart input.button',
			'.woocommerce a.button',
			'.woocommerce-page a.button',
			'.woocommerce .woocommerce-message .button',
			'.woocommerce-page .woocommerce-message .button',
			'.woocommerce a.checkout-button',
			'.woocommerce-page a.checkout-button',
			'.woocommerce .wc-proceed-to-checkout a.checkout-button',
			'.woocommerce-page .wc-proceed-to-checkout a.checkout-button',
			'.woocommerce a.btn.alt',
			'.woocommerce-page a.btn.alt',
			'.woocommerce #payment #place_order',
			'.woocommerce-page #payment #place_order',
			'.woocommerce div.product form.cart .button',
			'.woocommerce div.product form.cart .button.disabled',
			'.woocommerce div.product form.cart .button.disabled:hover',
			'.woocommerce-page div.product form.cart .button',
			'.woocommerce-page div.product form.cart .button.disabled',
			'.woocommerce-page div.product form.cart .button.disabled:hover',
			'.woocommerce div.product #respond .form-submit input#submit',
			'.woocommerce-page div.product #respond .form-submit input#submit',
			'.woocommerce .read-more-button',
			'.woocommerce-page .read-more-button',
			'.woocommerce input.read-more-button',
			'.woocommerce-page input.read-more-button',
			'.woocommerce .cart input.read-more-button',
			'.woocommerce-page .cart input.read-more-button',
			'.woocommerce a.read-more-button',
			'.woocommerce-page a.read-more-button',
			'.woocommerce .woocommerce-message .read-more-button',
			'.woocommerce-page .woocommerce-message .read-more-button',
			'.woocommerce div.product form.cart .read-more-button',
			'.woocommerce-page div.product form.cart .read-more-button',
			'.woocommerce .post-nav .nav-links .button',
			'.woocommerce-page .post-nav .nav-links .button',
			'.woocommerce input.post-nav .nav-links .button',
			'.woocommerce-page input.post-nav .nav-links .button',
			'.woocommerce .cart input.post-nav .nav-links .button',
			'.woocommerce-page .cart input.post-nav .nav-links .button',
			'.woocommerce a.post-nav .nav-links .button',
			'.woocommerce-page a.post-nav .nav-links .button',
			'.woocommerce .woocommerce-message .post-nav .nav-links .button',
			'.woocommerce-page .woocommerce-message .post-nav .nav-links .button',
			'.woocommerce div.product form.cart .post-nav .nav-links .button',
			'.woocommerce-page div.product form.cart .post-nav .nav-links .button',
			'.widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons .button',
			'.widget.woocommerce.widget_layered_nav ul li a',
			'.widget.woocommerce.widget_layered_nav_filters ul li a',
			'.widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .button',
			'.widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons .read-more-button',
			'.widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .read-more-button',
			'.widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons .post-nav .nav-links .button',
			'.widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .post-nav .nav-links .button',
			'#footer .widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons .button',
			'#footer .widget.woocommerce.widget_layered_nav ul li a',
			'#footer .widget.woocommerce.widget_layered_nav_filters ul li a',
			'#footer .widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .button',
			'#footer .widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons .read-more-button',
			'#footer .widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .read-more-button',
			'#footer .widget.woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons .post-nav .nav-links .button',
			'#footer .widget.woocommerce.widget_price_filter .price_slider_wrapper .price_slider_amount .post-nav .nav-links .button',
		) );

		$selectors['buttons'] = array_merge( $selectors['buttons'], $woocommerce_button_selectors );
	}

	return $selectors;
}

add_filter( 'extra_dynamic_selectors', 'extra_woocommerce_buttons_selectors' );

/**
 * Renders the Content.
 *
 * This allows Builder to take over the entire Post Content area,
 * as opposed to Description tab.
 *
 * @since ??
 */
function extra_render_the_content() {
	the_content();
}

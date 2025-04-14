<?php
/**
 * Get placeholders for WooCommerce module in Theme Builder
 *
 * @since 4.0.1
 * @since 4.0.10 Product placeholders is initialized as TB placeholder product's default props
 *
 * @return array
 */
function et_theme_builder_wc_placeholders() {
	return array(
		'title'             => esc_html__( 'Product name', 'et_builder' ),
		'slug'              => 'product-name',
		'short_description' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris bibendum eget dui sed vehicula. Suspendisse potenti. Nam dignissim at elit non lobortis.', 'et_builder' ),
		'description'       => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris bibendum eget dui sed vehicula. Suspendisse potenti. Nam dignissim at elit non lobortis. Cras sagittis dui diam, a finibus nibh euismod vestibulum. Integer sed blandit felis. Maecenas commodo ante in mi ultricies euismod. Morbi condimentum interdum luctus. Mauris iaculis interdum risus in volutpat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Praesent cursus odio eget cursus pharetra. Aliquam lacinia lectus a nibh ullamcorper maximus. Quisque at sapien pulvinar, dictum elit a, bibendum massa. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris non pellentesque urna.', 'et_builder' ),
		'status'            => 'publish',
		'comment_status'    => 'open',
	);
}

/**
 * Force set product's class to ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder in TB's woocommerceComponent
 * rendering. This product classname is specifically filled and will returned TB placeholder data
 * without retrieving actual value from database
 *
 * @since 4.0.10
 *
 * @return string
 */
function et_theme_builder_wc_product_class() {
	return 'ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder';
}

/**
 * Get review placeholder for WooCommerce module in Theme Builder. This can't be included at
 * `et_theme_builder_wc_placeholders()` due to dependability on global $post value and
 * `et_theme_builder_wc_placeholders()`'s returned value being cached on static variable
 *
 * @since 4.0.1
 *
 * @return object
 */
function et_theme_builder_wc_review_placeholder() {
	global $post;

	$review                       = new stdClass();
	$review->comment_ID           = 1;
	$review->comment_author       = 'John Doe';
	$review->comment_author_email = 'john@doe.com';
	$review->comment_date         = '2019-10-15 16:13:13';
	$review->comment_date_gmt     = '2019-10-15 16:13:13';
	$review->comment_content      = 'Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cum sociis natoque penatibus et magnis dis parturient montes; nascetur ridiculus mus.';
	$review->comment_post_ID      = $post->ID;
	$review->user_id              = null;

	return new WP_Comment( $review );
}

/**
 * Set global objects needed to manipulate `ETBuilderBackend.currentPage.woocommerceComponents` on
 * theme builder into displaying WooCommerce module placeholder (even though TB's CPT is not
 * WooCommerce's product CPT)
 *
 * @since 4.0.1
 *
 * @param array $conditional_tags evaluate conditional tags when current request is AJAX request
 */
function et_theme_builder_wc_set_global_objects( $conditional_tags = array() ) {
	$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
	$is_use_placeholder = $is_tb || is_et_pb_preview();

	// Check if current request is theme builder (direct page / AJAX request)
	if ( ! et_builder_tb_enabled() && ! $is_use_placeholder ) {
		return;
	}

	// Global variable that affects WC module rendering
	global $product, $post, $tb_original_product, $tb_original_post, $tb_wc_post, $tb_wc_product;

	// Making sure correct comment template is loaded on WC tabs' review tab
	add_filter( 'comments_template', array( 'ET_Builder_Module_Woocommerce_Tabs', 'comments_template_loader' ), 20 );

	// Force display related posts; technically sets all products as related
	add_filter( 'woocommerce_product_related_posts_force_display', '__return_true' );

	// Make sure review's form is opened
	add_filter( 'comments_open', '__return_true' );

	// Save original $post for reset later
	$tb_original_post = $post;

	// Save original $product for reset later
	$tb_original_product = $product;

	// If modified global existed, use it for efficiency
	if ( ! is_null( $tb_wc_post ) && ! is_null( $tb_wc_product ) ) {
		$post    = $tb_wc_post;
		$product = $tb_wc_product;

		return;
	}

	// Get placeholders
	$placeholders = et_theme_builder_wc_placeholders();

	if ( $is_use_placeholder ) {
		$placeholder_src = wc_placeholder_img_src( 'full' );
		$placeholder_id  = attachment_url_to_postid( $placeholder_src );

		if ( absint( $placeholder_id ) > 0 ) {
			$placeholders['gallery_image_ids'] = array( $placeholder_id );
		}
	} else {
		$placeholders['gallery_image_ids'] = array();
	}

	// $post might be null if current request is computed callback (ie. WC gallery)
	if ( is_null( $post ) ) {
		$post = new stdClass();
	}

	// Overwrite $post global
	$post->post_title     = $placeholders['title'];
	$post->post_slug      = $placeholders['slug'];
	$post->post_excerpt   = $placeholders['short_description'];
	$post->post_content   = $placeholders['description'];
	$post->post_status    = $placeholders['status'];
	$post->comment_status = $placeholders['comment_status'];

	// Overwrite global $product
	$product = new ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder();

	// Set current post ID as product's ID. `ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder`
	// handles all placeholder related value but product ID need to be manually set to match current
	// post's ID. This is especially needed when add-ons is used and accessing get_id() method.
	if ( isset( $post->ID ) ) {
		$product->set_id( $post->ID );
	}

	// Save modified global for later use
	$tb_wc_post    = $post;
	$tb_wc_product = $product;
}

/**
 * Reset global objects needed to manipulate `ETBuilderBackend.currentPage.woocommerceComponents`
 *
 * @since 4.0.1
 * @since 4.14.5 Add conditional tags parameter to evaluate AJAX request.
 *
 * @param array $conditional_tags Evaluate conditional tags when current request is AJAX request.
 */
function et_theme_builder_wc_reset_global_objects( $conditional_tags = array() ) {
	$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
	$is_use_placeholder = $is_tb || is_et_pb_preview();

	// Check if current request is theme builder (direct page / AJAX request).
	if ( ! et_builder_tb_enabled() && ! $is_use_placeholder ) {
		return;
	}

	global $product, $post, $tb_original_product, $tb_original_post;

	remove_filter( 'comments_template', array( 'ET_Builder_Module_Woocommerce_Tabs', 'comments_template_loader' ), 20 );
	remove_filter( 'woocommerce_product_related_posts_force_display', '__return_true' );
	remove_filter( 'comments_open', '__return_true' );

	$post    = $tb_original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Need override the post with the theme builder post.
	$product = $tb_original_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Need override the product with the theme builder product.
}

/**
 * Modify reviews output on WooCommerce's review and tabs' review module in TB
 *
 * @since 4.0.1
 *
 * @param array $comments
 *
 * @return array
 */
function et_theme_builder_wc_set_review_objects( $comments ) {
	// Return early if it isn't theme builder
	if ( ! et_builder_tb_enabled() ) {
		return $comments;
	}

	$placeholder = et_theme_builder_wc_review_placeholder();

	// Add two placeholder reviews
	$comments = array(
		$placeholder,
		$placeholder,
	);

	// When comment metadata is modified via `get_comment_metadata` filter, the $comment param
	// passed into template functions is int instead of WP_Comment object which triggers
	// `get_comment()` which triggers error because there's no real review/comment saved in database
	// to fix it, modify cache to short-circuit and prevent full `get_comment()`  execution
	wp_cache_set( $placeholder->comment_ID, $placeholder, 'comment' );

	return $comments;
}

// Modify review output on WooCommerce Tabs module
add_filter( 'comments_array', 'et_theme_builder_wc_set_review_objects' );

// Modify review output on WooCommerce Review module
add_filter( 'the_comments', 'et_theme_builder_wc_set_review_objects' );

/**
 * Modify review rating output on WooCommerce review and tabs review module in TB
 *
 * @since 4.0.1
 *
 * @param mixed  $value
 * @param int    $object_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 */
function et_theme_builder_wc_set_review_metadata( $value, $object_id, $meta_key, $single ) {
	$is_tb = et_builder_tb_enabled();

	// Modify rating metadata
	if ( $is_tb && 'rating' === $meta_key ) {
		global $product;
		return $product->get_average_rating();
	}

	// Modify verified metadata
	if ( $is_tb && 'verified' === $meta_key ) {
		return false;
	}

	return $value;
}

add_filter( 'get_comment_metadata', 'et_theme_builder_wc_set_review_metadata', 10, 4 );

/**
 * Filter `get_the_terms()` output for Theme Builder layout usage. `get_the_term()` is used for
 * product tags and categories in WC meta module and relies on current post's ID to output product's
 * tags and categories. In TB settings, post ID is irrelevant as the current layout can be used in
 * various pages. Thus, simply get the first tags and cats then output it for visual preview purpose
 *
 * @since 4.0.10
 *
 * @param WP_Term[]|WP_Error $terms    Array of attached terms, or WP_Error on failure.
 * @param int                $post_id  Post ID.
 * @param string             $taxonomy Name of the taxonomy.
 *
 * @return
 */
function et_theme_builder_wc_terms( $terms, $post_id, $taxonomy ) {
	// Only modify product_cat and product_tag taxonomies; This function is only called in TB's
	// woocommerceComponent output for current product setting
	if ( in_array( $taxonomy, array( 'product_cat', 'product_tag' ) ) && empty( $terms ) ) {
		$tags = get_categories( array( 'taxonomy' => $taxonomy ) );

		if ( isset( $tags[0] ) ) {
			$terms = array( $tags[0] );
		}
	}

	return $terms;
}

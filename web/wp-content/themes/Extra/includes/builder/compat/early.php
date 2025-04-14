<?php
// Compatibility code that needs to be run early and for each request.

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( function_exists( 'ud_get_stateless_media' ) ) {
	// WP Stateless Plugin.
	function et_compat_stateless_skip_cache_busting( $result, $filename ) {
		return $filename;
	}

	add_filter( 'stateless_skip_cache_busting', 'et_compat_stateless_skip_cache_busting', 10, 2 );
}

/**
 * Disable JQuery Body Feature.
 *
 * @since 4.10.3
 *
 * @return void
 */
function et_builder_disable_jquery_body() {
	add_filter( 'et_builder_enable_jquery_body', '__return_false' );
}

if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
	// Disable JQuery Body when SG CachePress JS Combine option is enabled
	// because the two features aren't compatible.
	if ( '1' === get_option( 'siteground_optimizer_combine_javascript' ) ) {
		et_builder_disable_jquery_body();
	}
}

if ( defined( 'WP_ROCKET_SLUG' ) ) {
	// Disable JQuery Body when WP Rocket Defer JS option is enabled
	// because the two features aren't compatible.
	if ( 1 === et_()->array_get( get_option( WP_ROCKET_SLUG ), 'defer_all_js' ) ) {
		et_builder_disable_jquery_body();
	}
}

if ( defined( 'LSCWP_V' ) ) {
	$options = [
		'litespeed.conf.optm-js_comb_ext_inl',
		'litespeed.conf.optm-js_defer',
	];

	// Disable JQuery Body when some LiteSpeed Cache JS options are enabled
	// because the features aren't compatible.
	foreach ( $options as $option ) {
		if ( ! empty( get_option( $option ) ) ) {
			et_builder_disable_jquery_body();
			break;
		}
	}
}

if ( defined( 'AUTOPTIMIZE_PLUGIN_VERSION' ) ) {
	$options = [
		'autoptimize_js_include_inline',
		'autoptimize_js_defer_inline',
		'autoptimize_js_forcehead',
	];

	// Disable JQuery Body when some Autoptimize JS options are enabled
	// because the features aren't compatible.
	foreach ( $options as $option ) {
		if ( ! empty( get_option( $option ) ) ) {
			et_builder_disable_jquery_body();
			break;
		}
	}
}

if ( defined( 'OP3_VERSION' ) ) {
	// Disable JQuery Body when some OptimizePress is active
	// because the two aren't compatible.
	et_builder_disable_jquery_body();
}

/**
 * Sets the loading attr threshold based on Post meta.
 *
 * @param int $omit_threshold The number of media elements where the `loading`
 *                            attribute will not be added. Default 1.
 *
 * @return int
 */
function et_builder_set_loading_attr_threshold_by_atf_content( $omit_threshold ) {
	global $post;

	if ( empty( $post ) ) {
		return $omit_threshold;
	}

	$post_id = $post->ID;

	$post_threshold = get_post_meta(
		$post_id,
		'_et_builder_dynamic_assets_loading_attr_threshold',
		true
	);

	$post_threshold = absint( $post_threshold );

	return $post_threshold > 1 ? $post_threshold : $omit_threshold;

}

/**
 * Execute the following on `wp` hook.
 *
 * The loading attribute threshold is set on `wp` hook. This is because framework.php is run on `init` which determines the threshold value.
 * Once the value is determined (happens only on first load), it is the saved on to post meta.
 * The saved post meta is retrieved on every load until the page is changed or cache cleared.
 * The value is then fed to WordPress using the `wp_omit_loading_attr_threshold` filter.
 *
 * @return void
 */
function et_builder_on_wp() {
	add_filter(
		'wp_omit_loading_attr_threshold',
		'et_builder_set_loading_attr_threshold_by_atf_content'
	);
}

add_action( 'wp', 'et_builder_on_wp' );

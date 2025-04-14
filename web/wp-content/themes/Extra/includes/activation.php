<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Automatically assign social network values in popular social network so
 * top header displays social media icon by default
 * @return void
 */
function et_assign_social_network_url_upon_activation() {
	$popular_social_networks = apply_filters( 'social_network_url_upon_activation', array(
		'facebook'  => 'https://facebook.com',
		'twitter'   => 'https://twitter.com',
		'instagram' => 'https://instagram.com',
	) );

	if ( ! empty( $popular_social_networks ) ) {
		foreach ( $popular_social_networks as $id => $url ) {
			$key       = sprintf( '%s_url', $id );
			$saved_url = et_get_option( $key, false );

			// Do not overwrite existing value
			if ( !empty( $saved_url ) ) {
				continue;
			}

			et_update_option( $key, esc_url( $url ) );
		}
	}
}

add_action( 'after_switch_theme', 'et_assign_social_network_url_upon_activation' );

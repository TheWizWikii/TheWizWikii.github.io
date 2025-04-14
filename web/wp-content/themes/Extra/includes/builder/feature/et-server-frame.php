<?php
/**
 * Handle ET Server interaction.
 *
 * @package Builder
 */

/**
 * Save the domain token received from ET Server on successful login.
 */
function et_builder_ajax_save_domain_token() {
	et_core_security_check( 'manage_options', 'et_builder_ajax_save_domain_token', 'nonce' );

	if ( isset( $_POST['domain_token'] ) && is_string( $_POST['domain_token'] ) ) {
		$domain_token = sanitize_text_field( $_POST['domain_token'] );
		update_option( 'et_server_domain_token', $domain_token );
	}
}

add_action( 'wp_ajax_et_builder_ajax_save_domain_token', 'et_builder_ajax_save_domain_token' );

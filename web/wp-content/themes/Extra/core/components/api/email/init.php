<?php

if ( ! function_exists( 'et_core_api_email_init' ) ):
function et_core_api_email_init() {
	if ( defined( 'ET_CORE_UPDATED' ) ) {
		et_core_api_email_fetch_all_lists();
	}
}
endif;


if ( ! function_exists( 'et_core_api_email_fetch_all_lists' ) ):
/**
 * Fetch the latest email lists for all provider accounts and update the database accordingly.
 *
 * @since 3.4
 */
function et_core_api_email_fetch_all_lists() {
	$providers    = ET_Core_API_Email_Providers::instance();
	$all_accounts = $providers->accounts();

	foreach ( $all_accounts as $provider_slug => $accounts ) {
		$provider = $providers->get( $provider_slug, '' );

		foreach ( $accounts as $account ) {
			$provider->set_account_name( $account );
			$provider->fetch_subscriber_lists();
		}
	}
}
endif;


if ( ! function_exists( 'et_core_api_email_fetch_lists' ) ):
/**
 * Fetch the latest email lists for a provider account and update the database accordingly.
 *
 * @param string $name_or_slug The provider name or slug.
 * @param string $account      The account name.
 * @param string $api_key      Optional. The api key (if fetch succeeds, the key will be saved).
 *
 * @return string 'success' if successful, an error message otherwise.
 */
function et_core_api_email_fetch_lists( $name_or_slug, $account, $api_key = '' ) {
	if ( ! empty( $api_key ) ) {
		// The account provided either doesn't exist yet or has a new api key.
		et_core_security_check( 'manage_options' );
	}

	if ( empty( $name_or_slug ) || empty( $account ) ) {
		return __( 'ERROR: Invalid arguments.', 'et_core' );
	}

	$providers = ET_Core_API_Email_Providers::instance();
	$provider  = $providers->get( $name_or_slug, $account, 'builder' );

	if ( ! $provider ) {
		return '';
	}

	if ( is_array( $api_key ) ) {
		foreach ( $api_key as $field_name => $value ) {
			$provider->data[ $field_name ] = sanitize_text_field( $value );
		}
	} else if ( '' !== $api_key ) {
		$provider->data['api_key'] = sanitize_text_field( $api_key );
	}

	return $provider->fetch_subscriber_lists();
}
endif;


if ( ! function_exists( 'et_core_api_email_providers' ) ):
/**
 * @deprecated {@see ET_Core_API_Email_Providers::instance()}
 *
 * @return ET_Core_API_Email_Providers
 */
function et_core_api_email_providers() {
	return ET_Core_API_Email_Providers::instance();
}
endif;


if ( ! function_exists( 'et_core_api_email_remove_account' ) ):
/**
 * Delete an existing provider account.
 *
 * @param string $name_or_slug The provider name or slug.
 * @param string $account      The account name.
 */
function et_core_api_email_remove_account( $name_or_slug, $account ) {
	et_core_security_check( 'manage_options' );

	if ( empty( $name_or_slug ) || empty( $account ) ) {
		return;
	}

	// If the account being removed is a legacy account (pre-dates core api), remove the old data.
	switch( $account ) {
		case 'Divi Builder Aweber':
			et_delete_option( 'divi_aweber_consumer_key' );
			et_delete_option( 'divi_aweber_consumer_secret' );
			et_delete_option( 'divi_aweber_access_key' );
			et_delete_option( 'divi_aweber_access_secret' );
			break;
		case 'Divi Builder Plugin Aweber':
			$opts  = (array) get_option( 'et_pb_builder_options' );
			unset( $opts['aweber_consumer_key'], $opts['aweber_consumer_secret'], $opts['aweber_access_key'], $opts['aweber_access_secret'] );
			update_option( 'et_pb_builder_options', $opts );
			break;
		case 'Divi Builder MailChimp':
			et_delete_option( 'divi_mailchimp_api_key' );
			break;
		case 'Divi Builder Plugin MailChimp':
			$options  = (array) get_option( 'et_pb_builder_options' );
			unset( $options['newsletter_main_mailchimp_key'] );
			update_option( 'et_pb_builder_options', $options );
			break;
	}

	$providers = ET_Core_API_Email_Providers::instance();
	$provider  = $providers->get( $name_or_slug, $account );

	if ( $provider ) {
		$provider->delete();
	}
}
endif;

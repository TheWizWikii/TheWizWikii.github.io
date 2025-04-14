<?php

if ( ! function_exists( 'et_core_api_spam_init' ) ):
function et_core_api_spam_init() {
	if ( is_admin() ) {
		return;
	}

	if ( ! et_core_api_spam_find_provider_account() ) {
		// Always instantiate ReCaptcha class
		ET_Core_API_Spam_Providers::instance()->get( 'recaptcha', '' );
	}
}
endif;


if ( ! function_exists('et_builder_spam_add_account' ) ):
function et_core_api_spam_add_account( $name_or_slug, $account, $api_key ) {
	et_core_security_check();

	if ( empty( $name_or_slug ) || empty( $account ) ) {
		return __( 'ERROR: Invalid arguments.', 'et_core' );
	}

	$providers = ET_Core_API_Spam_Providers::instance();
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

	$provider->save_data();

	return 'success';
}
endif;


if ( ! function_exists( 'et_core_api_spam_find_provider_account' ) ):
function et_core_api_spam_find_provider_account() {
	$spam_providers = ET_Core_API_Spam_Providers::instance();

	if ( $accounts = $spam_providers->accounts() ) {
		$enabled_account = '';
		$provider        = '';

		foreach ( $accounts as $provider_slug => $provider_accounts ) {
			if ( empty( $provider_accounts ) ) {
				continue;
			}

			foreach ( $provider_accounts as $account_name => $account ) {
				if ( isset( $account['site_key'], $account['secret_key'] ) ) {
					$provider        = $provider_slug;
					$enabled_account = $account_name;
					break;
				}
			}
		}

		if ( $provider && $enabled_account ) {
			return $spam_providers->get( $provider, $enabled_account );
		}
	}

	return false;
}
endif;


if ( ! function_exists( 'et_core_api_spam_remove_account' ) ):
/**
 * Delete an existing provider account.
 *
 * @since 4.0.7
 *
 * @param string $name_or_slug The provider name or slug.
 * @param string $account      The account name.
 */
function et_core_api_spam_remove_account( $name_or_slug, $account ) {
	et_core_security_check();

	if ( empty( $name_or_slug ) || empty( $account ) ) {
		return;
	}

	$providers = ET_Core_API_Spam_Providers::instance();

	if ( $provider = $providers->get( $name_or_slug, $account ) ) {
		$provider->delete();
	}
}
endif;

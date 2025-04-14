<?php
/**
 * Common admin.
 *
 * @package \ET\Common
 */

/**
 * Get code snippets application nonces.
 */
function et_code_snippets_get_nonces() {
	return [
		'saveDomainToken'                => wp_create_nonce( 'et_builder_ajax_save_domain_token' ),
		'et_theme_builder_api_get_terms' => wp_create_nonce( 'et_theme_builder_api_get_terms' ),
		'et_library_save_item'           => wp_create_nonce( 'et_library_save_item' ),
	];
}

/**
 * Gets the available languages.
 *
 * @return array Available languages.
 */
function et_common_get_available_languages() {
	$translations        = get_site_transient( 'available_translations' );
	$available_languages = [];

	if ( ! $translations ) {
		/** Load WordPress Translation Install API */
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';

		$translations = wp_get_available_translations();
	}

	foreach ( $translations as $translation => $translation_data ) {
		if ( ! isset( $translation_data['english_name'] ) ) {
			continue;
		}

		$english_name = $translation_data['english_name'];

		$available_languages[ $english_name ] = $english_name;
	}

	return $available_languages;
}

/**
 * Localize common app js data.
 */
function et_common_global_js_vars() {
	if ( ! is_admin() && ! et_core_is_fb_enabled() && ! et_builder_is_et_onboarding_page() ) {
		return;
	}

	if ( is_admin() ) {
		global $shortname;

		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state and is therefore not susceptible to CSRF.
		$is_templates_page       = isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'];
		$current_screen          = get_current_screen();
		$toplevel_page           = 'toplevel_page_et_' . $shortname . '_options';
		$is_options_page         = $toplevel_page === $current_screen->id;
		$is_layouts_library_page = isset( $current_screen->id ) && 'edit-et_pb_layout' === $current_screen->id;
		$is_divi_library         = isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];

		if ( ! $is_templates_page && ! $is_options_page && ! $is_layouts_library_page && ! $is_divi_library && ! et_builder_bfb_enabled() && ! et_builder_is_et_onboarding_page() ) {
			return;
		}
	}

	$home_url = wp_parse_url( get_site_url() );

	$data = [
		'config' => [
			'nonces'              => et_code_snippets_get_nonces(),
			'api'                 => admin_url( 'admin-ajax.php' ),
			'site_domain'         => $home_url['host'],
			'domainToken'         => get_option( 'et_server_domain_token', '' ),
			'layoutCategories'    => et_theme_builder_get_terms( 'layout_category' ),
			'layoutTags'          => et_theme_builder_get_terms( 'layout_tag' ),
			'localCategoriesEdit' => current_user_can( 'manage_categories' ) ? 'allowed' : 'notAllowed',
			'availableLanguages'  => et_common_get_available_languages(),
			'post_types'          => [
				'et_code_snippet'  => ET_CODE_SNIPPET_POST_TYPE,
				'et_theme_options' => is_admin() ? ET_THEME_OPTIONS_POST_TYPE : '',
			],
			'images_uri'          => ET_COMMON_URL . 'images/',
		],
		'i18n'   => [
			'library' => require ET_COMMON_DIR . 'i18n/library.php',
			'ai'      => require ET_COMMON_DIR . 'i18n/ai.php',
			'common'  => require ET_COMMON_DIR . 'i18n/common.php',
		],
	];

	echo '<script>var et_common_data = ' . wp_json_encode( $data ) . '</script>';
}

add_action( 'wp_head', 'et_common_global_js_vars' );
add_action( 'admin_head', 'et_common_global_js_vars' );

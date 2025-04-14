<?php
/**
 * Load portability used in the Theme Builder admin page.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_load_portability() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		return;
	}

	// Get all the roles that can edit theme options and other posts.
	$tb_applicability_roles = et_core_get_roles_by_capabilities( [ 'edit_theme_options', 'edit_others_posts' ] );

	et_core_load_component( 'portability' );
	et_core_portability_register(
		'et_theme_builder',
		array(
			'name'          => esc_html__( 'Divi Theme Builder', 'et_builder' ),
			'type'          => 'theme_builder',
			'view'          => 'et_theme_builder' === et_()->array_get( $_GET, 'page' ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No need to use nonce.
			'applicability' => $tb_applicability_roles,
		)
	);
}
add_action( 'admin_init', 'et_theme_builder_load_portability' );

/**
 * Register the Theme Builder admin page.
 *
 * @since 4.0
 *
 * @param string $parent
 *
 * @return void
 */
function et_theme_builder_add_admin_page( $parent ) {
	if (
		! et_pb_is_allowed( 'theme_builder' )
		|| ! current_user_can( 'edit_theme_options' )
		|| ! current_user_can( 'edit_others_posts' )
	) {
		return;
	}

	// We register the page with the 'edit_others_posts' capability since it's the lowest
	// requirement to use VB and we already checked for the theme_builder ET cap.
	add_submenu_page(
		$parent,
		esc_html__( 'Theme Builder', 'et_builder' ),
		esc_html__( 'Theme Builder', 'et_builder' ),
		'edit_others_posts',
		'et_theme_builder',
		'et_theme_builder_admin_page'
	);
}

/**
 * Enqueue Theme Builder assets.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_enqueue_scripts() {
	if ( ! et_builder_is_tb_admin_screen() && ! et_builder_is_et_onboarding_page() ) {
		return;
	}

	$role_capabilities = et_pb_get_role_settings();
	$user_role         = et_pb_get_current_user_role();

	et_builder_enqueue_open_sans();

	et_fb_enqueue_bundle( 'et-theme-builder', 'theme-builder.css', array( 'et-core-admin' ) );

	et_builder_enqueue_assets_head();
	et_builder_enqueue_assets_main();

	global $wp_version;

	$ver  = ET_BUILDER_VERSION;
	$root = ET_BUILDER_URI;

	if ( version_compare( substr( $wp_version, 0, 3 ), '4.5', '<' ) ) {
		$dep = array( 'jquery-ui-compat' );
		wp_register_script( 'jquery-ui-compat', "{$root}/scripts/ext/jquery-ui-1.10.4.custom.min.js", array( 'jquery' ), $ver, true );
	} else {
		$dep = array( 'jquery-ui-datepicker' );
	}

	wp_register_script( 'jquery-ui-datepicker-addon', "{$root}/scripts/ext/jquery-ui-timepicker-addon.js", $dep, $ver, true );
	wp_register_script( 'react-tiny-mce', "{$root}/frontend-builder/assets/vendors/tinymce.min.js" );

	$asset_ver = ET_BUILDER_VERSION;

	$frame_helpers_id   = 'et-frame-helpers';
	$frame_helpers_path = ET_BUILDER_DIR . '/frontend-builder/build/frame-helpers.js';
	$frame_helpers_url  = ET_BUILDER_URI . '/frontend-builder/build/frame-helpers.js';

	if ( ! file_exists( $frame_helpers_path ) ) {
		// Load "hot" from webpack-dev-server.
		$site_url          = wp_parse_url( get_site_url() );
		$frame_helpers_url = "{$site_url['scheme']}://{$site_url['host']}:31495/frame-helpers.js";
	}

	wp_register_script( $frame_helpers_id, $frame_helpers_url, array(), $asset_ver );

	$asset_id     = 'et-theme-builder';
	$asset_path   = ET_BUILDER_DIR . '/frontend-builder/build/theme-builder.js';
	$asset_uri    = ET_BUILDER_URI . '/frontend-builder/build/theme-builder.js';
	$dependencies = array(
		'jquery',
		'jquery-ui-sortable',
		'jquery-ui-datepicker-addon',
		'react',
		'react-dom',
		'react-tiny-mce',
		'et-core-admin',
		'wp-hooks',
		'et-frame-helpers',
	);

	if ( ! wp_script_is( 'wp-hooks', 'registered' ) ) {
		// Use bundled wp-hooks script when WP < 5.0
		wp_enqueue_script( 'wp-hooks', ET_BUILDER_URI . '/frontend-builder/assets/backports/hooks.js', array(), $asset_ver, false );
	}

	et_fb_enqueue_react();

	if ( ! file_exists( $asset_path ) ) {
		// Load "hot" from webpack-dev-server.
		$site_url  = wp_parse_url( get_site_url() );
		$asset_uri = "{$site_url['scheme']}://{$site_url['host']}:31495/theme-builder.js";
	}

	wp_enqueue_script( $asset_id, $asset_uri, $dependencies, $asset_ver, true );

	// Strip 'validate' key from settings as it is used server-side only.
	$default_settings = et_theme_builder_get_template_settings_options();
	foreach ( $default_settings as $group_key => $group ) {
		foreach ( $group['settings'] as $setting_key => $setting ) {
			unset( $default_settings[ $group_key ]['settings'][ $setting_key ]['validate'] );
		}
	}

	// Library item editor.
	$theme_builder_id   = 0;
	$library_item_title = '';
	$is_item_editor     = et_theme_builder_library_is_item_editor();

	if ( $is_item_editor ) {
		$item_id     = et_theme_builder_get_item_id();
		$item_editor = ET_Theme_Builder_Local_Library_Item_Editor::instance( $item_id );
		if ( null !== $item_editor->item->item_post ) {
			$theme_builder_id   = $item_editor->get_interim_theme_builder_id();
			$library_item_title = $item_editor->get_library_item_editor_item_title();
		}
	}

	$preloaded_settings = et_theme_builder_get_template_settings_options_for_preloading( $theme_builder_id );
	foreach ( $preloaded_settings as $setting_key => $setting ) {
		unset( $preloaded_settings[ $setting_key ]['validate'] );
	}

	$preferences = et_fb_app_preferences();
	$animation   = et_()->array_get( $preferences, 'builder_animation.value', 'true' );
	$animation   = true === $animation || 'true' === $animation;
	$i18n        = require ET_BUILDER_DIR . 'frontend-builder/i18n.php';

	wp_localize_script(
		'et-theme-builder',
		'et_theme_builder_bundle',
		array(
			'config' => array(
				'distPath'              => ET_BUILDER_URI . '/frontend-builder/build/',
				'api'                   => admin_url( 'admin-ajax.php' ),
				'apiErrors'             => ET_Theme_Builder_Api_Errors::getMap(),
				'themeBuilderURL'       => admin_url( 'admin.php?page=et_theme_builder' ),
				'diviLibraryCustomTabs' => apply_filters( 'et_builder_library_modal_custom_tabs', array(), 'theme-builder' ),
				// phpcs:disable WordPress.Arrays.MultipleStatementAlignment -- It fails to correctly identify the required spaces before double arrow in the nonces list`.
				'nonces'                => array(
					'et_builder_library_get_layouts_data'             => wp_create_nonce( 'et_builder_library_get_layouts_data' ),
					'et_theme_builder_library_get_items_data'         => wp_create_nonce( 'et_theme_builder_library_get_items_data' ),
					'et_theme_builder_library_update_terms'           => wp_create_nonce( 'et_theme_builder_library_update_terms' ),
					'et_theme_builder_library_get_item'               => wp_create_nonce( 'et_theme_builder_library_get_item' ),
					'et_theme_builder_api_duplicate_layout'           => wp_create_nonce( 'et_theme_builder_api_duplicate_layout' ),
					'et_theme_builder_api_create_layout'              => wp_create_nonce( 'et_theme_builder_api_create_layout' ),
					'et_theme_builder_api_get_layout_url'             => wp_create_nonce( 'et_theme_builder_api_get_layout_url' ),
					'et_theme_builder_api_save'                       => wp_create_nonce( 'et_theme_builder_api_save' ),
					'et_theme_builder_api_drop_autosave'              => wp_create_nonce( 'et_theme_builder_api_drop_autosave' ),
					'et_theme_builder_api_get_template_settings'      => wp_create_nonce( 'et_theme_builder_api_get_template_settings' ),
					'et_theme_builder_api_reset'                      => wp_create_nonce( 'et_theme_builder_api_reset' ),
					'et_theme_builder_api_export_theme_builder'       => wp_create_nonce( 'et_theme_builder_api_export_theme_builder' ),
					'et_theme_builder_api_import_theme_builder'       => wp_create_nonce( 'et_theme_builder_api_import_theme_builder' ),
					'et_builder_library_update_account'               => wp_create_nonce( 'et_builder_library_update_account' ),
					'et_theme_builder_library_update_item'            => wp_create_nonce( 'et_theme_builder_library_update_item' ),
					'et_theme_builder_library_save_temp_layout'       => wp_create_nonce( 'et_theme_builder_library_save_temp_layout' ),
					'et_theme_builder_library_remove_temp_layout'     => wp_create_nonce( 'et_theme_builder_library_remove_temp_layout' ),
					'et_theme_builder_library_toggle_cloud_status'    => wp_create_nonce( 'et_theme_builder_library_toggle_cloud_status' ),
					'et_theme_builder_api_save_template_to_library'   => wp_create_nonce( 'et_theme_builder_api_save_template_to_library' ),
					'et_theme_builder_api_save_preset_to_library'     => wp_create_nonce( 'et_theme_builder_api_save_preset_to_library' ),
					'et_theme_builder_api_get_terms'                  => wp_create_nonce( 'et_theme_builder_api_get_terms' ),
					'et_theme_builder_api_use_library_item'           => wp_create_nonce( 'et_theme_builder_api_use_library_item' ),
					'et_theme_builder_trash_theme_builder'            => wp_create_nonce( 'et_theme_builder_trash_theme_builder' ),
					'et_theme_builder_library_item_edit'              => wp_create_nonce( 'et_theme_builder_library_item_edit' ),
					'et_theme_builder_api_get_library_item'           => wp_create_nonce( 'et_theme_builder_api_get_library_item' ),
					'et_pb_preview_nonce'                             => wp_create_nonce( 'et_pb_preview_nonce' ),
					'et_theme_builder_library_get_set_items'          => wp_create_nonce( 'et_theme_builder_library_get_set_items' ),
					'et_theme_builder_get_preset_default_template_id' => wp_create_nonce( 'et_theme_builder_get_preset_default_template_id' ),
					'saveDomainToken'                                 => wp_create_nonce( 'et_builder_ajax_save_domain_token' ),
					'et_theme_builder_library_clear_temp_data'        => wp_create_nonce( 'et_theme_builder_library_clear_temp_data' ),
					'et_theme_builder_library_get_cloud_token'        => wp_create_nonce( 'et_theme_builder_library_get_cloud_token' ),
				),
				// phpcs:enable
				'site_url'              => get_site_url(),
				'rtl'                   => is_rtl(),
				'animation'             => $animation,
				'templateSettings'      => array(
					'default'   => $default_settings,
					'preloaded' => $preloaded_settings,
				),
				'etAccount'             => et_core_get_et_account(),
				'capabilities'          => isset( $role_capabilities[ $user_role ] ) ? $role_capabilities[ $user_role ] : array(),
				'templates'             => array(
					'hasDraft' => ! $theme_builder_id && 0 !== et_theme_builder_get_theme_builder_post_id( false, false ),
					'live'     => et_theme_builder_get_theme_builder_templates( true, $theme_builder_id ),
					'draft'    => et_theme_builder_get_theme_builder_templates( false, $theme_builder_id ),
				),
				'localLibrary'          => array(
					'templateCategories' => et_theme_builder_get_terms( 'layout_category' ),
					'templateTags'       => et_theme_builder_get_terms( 'layout_tag' ),
					'themeBuilderId'     => $theme_builder_id,
					'libraryItemTitle'   => ! empty( $library_item_title ) ? $library_item_title : '',
				),
				'site_domain'           => isset( $home_url['host'] ) ? untrailingslashit( $home_url['host'] ) : '/',
				'domainToken'           => get_option( 'et_server_domain_token', '' ),
				'verticalMenu'          => array(
					'showTooltip' => false,
				),
			),
			'i18n'   => array(
				'generic'      => $i18n['generic'],
				'portability'  => $i18n['portability'],
				'library'      => $i18n['library'],
				'themeBuilder' => $i18n['themeBuilder'],
			),
		)
	);

	// Load Library and Cloud.
	et_builder_load_library();

	ET_Cloud_App::load_js();

	if ( et_pb_is_allowed( 'divi_ai' ) ) {
		ET_AI_App::load_js();
	}
}
add_action( 'admin_enqueue_scripts', 'et_theme_builder_enqueue_scripts' );

/**
 * Render the Theme Builder admin page.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_admin_page() {
	echo '<div id="et-theme-builder"></div>';
}

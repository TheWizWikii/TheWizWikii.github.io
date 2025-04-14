<?php

if ( ! function_exists( 'et_allow_ampersand' ) ) :
/**
 * Convert &amp; into &
 * Escaped ampersand by wp_kses() which is used by et_get_safe_localization()
 * can be a troublesome in some cases, ie.: when string is sent in an email.
 *
 * @param string $string original string
 *
 * @return string modified string
 */
function et_allow_ampersand( $string ) {
	return str_replace('&amp;', '&', $string);
}
endif;


if ( ! function_exists( 'et_core_autoloader' ) ):
/**
 * Callback for {@link spl_autoload_register()}.
 *
 * @param $class_name
 */
function et_core_autoloader( $class_name ) {
	if ( 0 !== strpos( $class_name, 'ET_Core' ) ) {
		return;
	}

	static $components    = null;
	static $groups_loaded = array();

	if ( null === $components ) {
		$components = et_core_get_components_metadata();
	}

	if ( ! isset( $components[ $class_name ] ) ) {
		return;
	}

	$file   = ET_CORE_PATH . $components[ $class_name ]['file'];
	$groups = $components[ $class_name ]['groups'];
	$slug   = $components[ $class_name ]['slug'];

	if ( ! file_exists( $file ) ) {
		return;
	}

	// Load component class
	require_once $file;

	/**
	 * Fires when a Core Component is loaded.
	 *
	 * The dynamic portion of the hook name, $slug, refers to the slug of the Core Component that was loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( "et_core_component_{$slug}_loaded" );

	if ( empty( $groups ) ) {
		return;
	}

	foreach( $groups as $group_name ) {
		if ( in_array( $group_name, $groups_loaded ) ) {
			continue;
		}

		$groups_loaded[] = $group_name;
		$slug            = $components['groups'][ $group_name ]['slug'];
		$init_file       = $components['groups'][ $group_name ]['init'];
		$init_file       = empty( $init_file ) ? null : ET_CORE_PATH . $init_file;

		et_core_initialize_component_group( $slug, $init_file );
	}
}
endif;

if ( ! function_exists( 'et_core_clear_transients' ) ):
function et_core_clear_transients() {
	delete_transient( 'et_core_path' );
	delete_transient( 'et_core_version' );
	delete_transient( 'et_core_needs_old_theme_patch' );
}
add_action( 'upgrader_process_complete', 'et_core_clear_transients', 10, 0 );
add_action( 'switch_theme', 'et_core_clear_transients' );
add_action( 'update_option_active_plugins', 'et_core_clear_transients', 10, 0 );
add_action( 'update_site_option_active_plugins', 'et_core_clear_transients', 10, 0 );
endif;


if ( ! function_exists( 'et_core_cron_schedules_cb' ) ):
function et_core_cron_schedules_cb( $schedules ) {
	if ( isset( $schedules['monthly'] ) ) {
		return $schedules;
	}

	$schedules['monthly'] = array(
		'interval' => MONTH_IN_SECONDS,
		'display'  => __( 'Once Monthly' )
	);

	return $schedules;
}
add_action( 'cron_schedules', 'et_core_cron_schedules_cb' );
endif;


if ( ! function_exists( 'et_core_die' ) ):
function et_core_die( $message = '' ) {
	if ( wp_doing_ajax() ) {
		$message = '' !== $message ? $message : esc_html__( 'Configuration Error', 'et_core' );
		wp_send_json_error( array( 'error' => $message ) );
	}

	wp_die();
}
endif;


if ( ! function_exists( 'et_core_get_components_metadata' ) ):
function et_core_get_components_metadata() {
	static $metadata = null;

	if ( null === $metadata ) {
		require_once '_metadata.php';
		$metadata = json_decode( $metadata, true );
	}

	return $metadata;
}
endif;


if ( ! function_exists( 'et_core_get_component_names' ) ):
/**
 * Returns the names of all available components, optionally filtered by type and/or group.
 *
 * @param string $include The type of components to include (official|third-party|all). Default is 'official'.
 * @param string $group   Only include components in $group. Optional.
 *
 * @return array
 */
function et_core_get_component_names( $include = 'official', $group = '' ) {
	static $official_components = null;

	if ( null === $official_components ) {
		$official_components = et_core_get_components_metadata();
	}

	if ( 'official' === $include ) {
		return empty( $group ) ? $official_components['names'] : $official_components['groups'][ $group ]['members'];
	}

	$third_party_components = et_core_get_third_party_components();

	if ( 'third-party' === $include ) {
		return array_keys( $third_party_components );
	}

	return array_merge(
		array_keys( $third_party_components ),
		empty( $group ) ? $official_components['names'] : $official_components['groups'][ $group ]['members']
	);
}
endif;


if ( ! function_exists( 'et_core_get_ip_address' ) ):
/**
 * Returns the IP address of the client that initiated the current HTTP request.
 *
 * @return string
 */
function et_core_get_ip_address() {
	static $ip;

	if ( null !== $ip ) {
		return $ip;
	}

	// Array of headers that could contain a valid IP address.
	$headers = array(
		'HTTP_TRUE_CLIENT_IP',
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_SUCURI_CLIENTIP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'HTTP_CLIENT_IP',
		'REMOTE_ADDR',
	);

	$ip = '';

	foreach ( $headers as $header ) {
		// Skip if the header is not set.
		if ( empty( $_SERVER[ $header ] ) ) {
			continue;
		}

		$header = $_SERVER[ $header ];

		if ( et_()->includes( $header, ',' ) ) {
			$header = explode( ',', $header );
			$header = $header[0];
		}

		// Break if valid IP address is found.
		if ( filter_var( $header, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE ) ) {
			$ip = sanitize_text_field( $header );

			break;
		}
	}

	return $ip;
}
endif;

if ( ! function_exists( 'et_core_use_google_fonts' ) ) :
function et_core_use_google_fonts() {
	$utils              = ET_Core_Data_Utils::instance();
	$google_api_options = get_option( 'et_google_api_settings' );

	return 'on' === $utils->array_get( $google_api_options, 'use_google_fonts', 'on' );
}
endif;

if ( ! function_exists( 'et_core_get_main_fonts' ) ) :
function et_core_get_main_fonts() {
	global $wp_version;

	if ( version_compare( $wp_version, '4.6', '<' ) || ( ! is_admin() && ! et_core_use_google_fonts() ) ) {
		return '';
	}

	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'Divi' );

	if ( 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '%7C', $font_families ),
			'subset' => 'latin,latin-ext',
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}
endif;


if ( ! function_exists( 'et_core_get_theme_info' ) ) :
	/**
	 * Gets Theme Info.
	 *
	 * Gets Parent theme's info even when child theme is used.
	 *
	 * @param string $key One of WP_Theme class public properties.
	 *
	 * @returns string
	 */
	function et_core_get_theme_info( $key ) {
		static $theme_info = null;

		if ( ! $theme_info ) {
			$theme_info = wp_get_theme();

			if ( defined( 'STYLESHEETPATH' ) && is_child_theme() ) {
				$theme_info = wp_get_theme( $theme_info->parent_theme );
			}
		}

		return $theme_info->display( $key );
	}
endif;


if ( ! function_exists( 'et_core_get_third_party_components' ) ):
function et_core_get_third_party_components( $group = '' ) {
	static $third_party_components = null;

	if ( null !== $third_party_components ) {
		return $third_party_components;
	}

	/**
	 * 3rd-party components can be registered by adding the class instance to this array using it's name as the key.
	 *
	 * @since 1.1.0
	 *
	 * @param array $third_party {
	 *     An array mapping third party component names to a class instance reference.
	 *
	 *     @type ET_Core_3rdPartyComponent $name The component class instance.
	 *     ...
	 * }
	 * @param string $group If not empty, only components classified under this group should be included.
	 */
	return $third_party_components = apply_filters( 'et_core_get_third_party_components', array(), $group );
}
endif;


if ( ! function_exists( 'et_core_get_memory_limit' ) ):
/**
 * Returns the current php memory limit in megabytes as an int.
 *
 * @return int
 */
function et_core_get_memory_limit() {
	// Do NOT convert value to the integer, because wp_convert_hr_to_bytes() expects raw value from php_ini like 128M, 256M, 512M, etc
	$limit = @ini_get( 'memory_limit' );
	$mb_in_bytes = 1024*1024;
	$bytes = max( wp_convert_hr_to_bytes( $limit ), $mb_in_bytes );

	return ceil( $bytes / $mb_in_bytes );
}
endif;


if ( ! function_exists( 'et_core_initialize_component_group' ) ):
function et_core_initialize_component_group( $slug, $init_file = null ) {
	$slug = strtolower( $slug );

	if ( null !== $init_file && file_exists( $init_file ) ) {
		// Load and run component group's init function
		require_once $init_file;

		$init = "et_core_{$slug}_init";

		$init();
	}

	/**
	 * Fires when a Core Component Group is loaded.
	 *
	 * The dynamic portion of the hook name, `$group`, refers to the name of the Core Component Group that was loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( "et_core_{$slug}_loaded" );
}
endif;


if ( ! function_exists( 'et_core_is_builder_used_on_current_request' ) ) :
function et_core_is_builder_used_on_current_request() {
	static $builder_used = null;

	if ( null !== $builder_used ) {
		return $builder_used;
	}

	global $wp_query;

	if ( ! $wp_query ) {
		ET_Core_Logger::error( 'Called too early! $wp_query is not available.' );
		return false;
	}

	$builder_used = false;

	if ( ! empty( $wp_query->posts ) ) {
		foreach ( $wp_query->posts as $post ) {
			if ( 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) ) {
				$builder_used = true;
				break;
			}
		}
	} else if ( ! empty( $wp_query->post ) ) {
		if ( 'on' === get_post_meta( $wp_query->post->ID, '_et_pb_use_builder', true ) ) {
			$builder_used = true;
		}
	}

	return $builder_used = apply_filters( 'et_core_is_builder_used_on_current_request', $builder_used );
}
endif;


if ( ! function_exists( 'et_core_is_fb_enabled' ) ):
function et_core_is_fb_enabled() {
	if ( function_exists( 'et_fb_is_enabled' ) ) {
		return et_fb_is_enabled();
	}

	return isset( $_GET['et_fb'] ) && current_user_can( 'edit-posts' );
}
endif;

if ( ! function_exists( 'et_core_is_saving_builder_modules_cache' ) ):
function et_core_is_saving_builder_modules_cache() {
	// This filter is set when Modules Cache is being saved.
	return apply_filters( 'et_builder_modules_is_saving_cache', false );
}
endif;


/**
 * Is Gutenberg active?
 *
 * @since 3.19.2 Renamed from {@see et_is_gutenberg_active()} and moved to core.
 * @since 3.18
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'et_core_is_gutenberg_active' ) ):
function et_core_is_gutenberg_active() {
	global $wp_version;

	static $has_wp5_plus = null;

	if ( is_null( $has_wp5_plus ) ) {
		$has_wp5_plus = version_compare( $wp_version, '5.0-alpha1', '>=' );
	}

	return $has_wp5_plus || function_exists( 'is_gutenberg_page' );
}
endif;


/**
 * Is Gutenberg active and enabled for the current post
 * WP 5.0 WARNING - don't use before global post has been set
 *
 * @since 3.19.2 Renamed from {@see et_is_gutenberg_enabled()} and moved to core.
 * @since 3.18
 *
 * @return bool  True - if the plugin is active and enabled.
 */
if ( ! function_exists( 'et_core_is_gutenberg_enabled' ) ):
function et_core_is_gutenberg_enabled() {
	if ( function_exists( 'is_gutenberg_page' ) ) {
		return et_core_is_gutenberg_active() && is_gutenberg_page() && has_filter( 'replace_editor', 'gutenberg_init' );
	}

	return et_core_is_gutenberg_active() && function_exists( 'use_block_editor_for_post' ) && use_block_editor_for_post( null );
}
endif;


if ( ! function_exists( 'et_core_load_main_fonts' ) ) :
function et_core_load_main_fonts() {
	$fonts_url = et_core_get_main_fonts();
	if ( empty( $fonts_url ) ) {
		return;
	}

	wp_enqueue_style( 'et-core-main-fonts', esc_url_raw( $fonts_url ), array(), null );
}
endif;


if ( ! function_exists( 'et_core_load_main_styles' ) ) :
function et_core_load_main_styles( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}

	wp_enqueue_style( 'et-core-admin' );
}
endif;


if ( ! function_exists( 'et_core_maybe_set_updated' ) ):
function et_core_maybe_set_updated() {
	// TODO: Move et_{*}_option() functions to core.
	$last_core_version = get_option( 'et_core_version', '' );

	if ( ET_CORE_VERSION === $last_core_version ) {
		return;
	}

	update_option( 'et_core_version', ET_CORE_VERSION );

	define( 'ET_CORE_UPDATED', true );
}
endif;


if ( ! function_exists( 'et_core_maybe_patch_old_theme' ) ):
function et_core_maybe_patch_old_theme() {
	if ( ! ET_Core_Logger::php_notices_enabled() ) {
		return;
	}

	if ( get_transient( 'et_core_needs_old_theme_patch' ) ) {
		add_action( 'after_setup_theme', 'ET_Core_Logger::disable_php_notices', 9 );
		add_action( 'after_setup_theme', 'ET_Core_Logger::enable_php_notices', 11 );
		return;
	}

	$themes         = array( 'Divi' => '3.0.41', 'Extra' => '2.0.40' );
	$current_theme  = et_core_get_theme_info( 'Name' );

	if ( ! in_array( $current_theme, array_keys( $themes ) ) ) {
		return;
	}

	$theme_version = et_core_get_theme_info( 'Version' );

	if ( version_compare( $theme_version, $themes[ $current_theme ], '<' ) ) {
		add_action( 'after_setup_theme', 'ET_Core_Logger::disable_php_notices', 9 );
		add_action( 'after_setup_theme', 'ET_Core_Logger::enable_php_notices', 11 );
		set_transient( 'et_core_needs_old_theme_patch', true, DAY_IN_SECONDS );
	}
}
endif;


if ( ! function_exists( 'et_core_patch_core_3061' ) ):
function et_core_patch_core_3061() {
	if ( '3.0.61' !== ET_CORE_VERSION ) {
		return;
	}

	if ( ! ET_Core_PageResource::can_write_to_filesystem() ) {
		return; // Should we display a notice in the dashboard?
	}

	$old_file = ET_CORE_PATH . 'init.php';
	$new_file = dirname( __FILE__ ) . '/init.php';

	ET_Core_PageResource::startup();

	if ( ! ET_Core_PageResource::$wpfs ) {
		return;
	}

	ET_Core_PageResource::$wpfs->copy( $new_file, $old_file, true, 0644 );
	et_core_clear_transients();
}
endif;


if ( ! function_exists( 'et_core_register_admin_assets' ) ) :
	/**
	 * Register Core admin assets.
	 *
	 * @since ?.? Script 'et-core-admin' now loads in footer.
	 * @since 1.0.0
	 *
	 * @private
	 */
	function et_core_register_admin_assets() {
		wp_register_style( 'et-core-admin', ET_CORE_URL . 'admin/css/core.css', array(), ET_CORE_VERSION );
		wp_register_script(
			'et-core-admin',
			ET_CORE_URL . 'admin/js/core.js',
			array(
				'jquery',
				'jquery-ui-tabs',
				'jquery-form',
			),
			ET_CORE_VERSION,
			true
		);
		wp_localize_script(
			'et-core-admin',
			'etCore',
			array(
				'ajaxurl' => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
				'wp_version' => get_bloginfo( 'version' ),
				'text'    => array(
					'modalTempContentCheck' => esc_html__( 'Got it, thanks!', 'et_core' ),
				),
			)
		);

		// enqueue common scripts as well.
		et_core_register_common_assets();
	}
endif;
add_action( 'admin_enqueue_scripts', 'et_core_register_admin_assets' );

if ( ! function_exists( 'et_core_register_common_assets' ) ) :
/**
 * Register and Enqueue Common Core assets.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_register_common_assets() {
	// common.js needs to be located at footer after waypoint, fitvid, & magnific js to avoid broken javascript on Facebook in-app browser
	wp_register_script( 'et-core-common', ET_CORE_URL . 'admin/js/common.js', array( 'jquery' ), ET_CORE_VERSION, true );
	wp_enqueue_script( 'et-core-common' );
}
endif;

// common.js needs to be loaded after waypoint, fitvid, & magnific js to avoid broken javascript on Facebook in-app browser, hence the 15 priority
add_action( 'wp_enqueue_scripts', 'et_core_register_common_assets', 15 );

if ( ! function_exists( 'et_core_noconflict_styles_gform' ) ) :
/**
 * Register Core styles with Gravity Forms so that they're enqueued when running on no-conflict mode
 *
 * @since 3.21.2
 *
 * @param $styles
 *
 * @return array
 */
function et_core_noconflict_styles_gform( $styles ) {
	$styles[] = 'et-core-admin';

	return $styles;
}
endif;
add_filter( 'gform_noconflict_styles', 'et_core_noconflict_scripts_gform' );

if ( ! function_exists( 'et_core_noconflict_scripts_gform' ) ) :
/**
 * Register Core scripts with Gravity Forms so that they're enqueued when running on no-conflict mode
 *
 * @since 3.21.2
 *
 * @param $scripts
 *
 * @return array
 */
function et_core_noconflict_scripts_gform( $scripts ) {
	$scripts[] = 'et-core-admin';
	$scripts[] = 'et-core-common';

	return $scripts;
}
endif;
add_filter( 'gform_noconflict_scripts', 'et_core_noconflict_scripts_gform' );

if ( ! function_exists( 'et_core_security_check' ) ):
/**
 * Check if current user can perform an action and/or verify a nonce value. die() if not authorized.
 *
 * @examples:
 *   - Check if user can 'manage_options': `et_core_security_check();`
 *   - Verify a nonce value: `et_core_security_check( '', 'nonce_name' );`
 *   - Check if user can 'something' and verify a nonce value: `self::do_security_check( 'something', 'nonce_name' );`
 *
 * @param string $user_can       The name of the capability to check with `current_user_can()`.
 * @param string $nonce_action   The name of the nonce action to check (excluding '_nonce').
 * @param string $nonce_key      The key to use to lookup nonce value in `$nonce_location`. Default
 *                               is the value of `$nonce_action` with '_nonce' appended to it.
 * @param string $nonce_location Where the nonce is stored (_POST|_GET|_REQUEST). Default: _POST.
 * @param bool   $die            Whether or not to `die()` on failure. Default is `true`.
 *
 * @return bool|null Whether or not the checked passed if `$die` is `false`.
 */
function et_core_security_check( $user_can = 'manage_options', $nonce_action = '', $nonce_key = '', $nonce_location = '_POST', $die = true ) {
	$user_can     = (string) $user_can;
	$nonce_action = (string) $nonce_action;
	$nonce_key    = (string) $nonce_key;

	if ( empty( $nonce_key ) && false === strpos( $nonce_action, '_nonce' ) ) {
		$nonce_key = $nonce_action . '_nonce';
	} else if ( empty( $nonce_key ) ) {
		$nonce_key = $nonce_action;
	}

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	switch( $nonce_location ) {
		case '_POST':
			$nonce_location = $_POST;
			break;
		case '_GET':
			$nonce_location = $_GET;
			break;
		case '_REQUEST':
			$nonce_location = $_REQUEST;
			break;
		default:
			return $die ? et_core_die() : false;
	}
	// phpcs:enable

	$passed = true;

	if ( is_numeric( $user_can ) ) {
		// Numeric values are deprecated in current_user_can(). We do not accept them here.
		$passed = false;

	} else if ( '' !== $nonce_action && empty( $nonce_location[ $nonce_key ] ) ) {
		// A nonce value is required when a nonce action is provided.
		$passed = false;

	} else if ( '' === $user_can && '' === $nonce_action ) {
		// At least one of a capability OR a nonce action is required.
		$passed = false;

	} else if ( '' !== $user_can && ! current_user_can( $user_can ) ) {
		// Capability check failed.
		$passed = false;

	} else if ( '' !== $nonce_action && ! wp_verify_nonce( $nonce_location[ $nonce_key ], $nonce_action ) ) {
		// Nonce verification failed.
		$passed = false;
	}

	if ( $die && ! $passed ) {
		et_core_die();
	}

	return $passed;
}
endif;


if ( ! function_exists( 'et_core_security_check_passed' ) ):
/**
 * Wrapper for {@see et_core_security_check()} that disables `die()` on failure.
 *
 * @see et_core_security_check() for parameter documentation.
 *
 * @return bool Whether or not the security check passed.
 */
function et_core_security_check_passed( $user_can = 'manage_options', $nonce_action = '', $nonce_key = '', $nonce_location = '_POST' ) {
	return et_core_security_check( $user_can, $nonce_action, $nonce_key, $nonce_location, false );
}
endif;


if ( ! function_exists( 'et_core_setup' ) ) :
/**
 * Setup Core.
 *
 * @since 1.0.0
 * @since 3.0.60 The `$url` param is deprecated.
 *
 * @param string $deprecated Deprecated parameter.
 */
function et_core_setup( $deprecated = '' ) {
	if ( defined( 'ET_CORE_PATH' ) ) {
		return;
	}

	$core_path = _et_core_normalize_path( trailingslashit( dirname( __FILE__ ) ) );
	$theme_dir = _et_core_normalize_path( trailingslashit( realpath( get_template_directory() ) ) );

	if ( 0 === strpos( $core_path, $theme_dir ) ) {
		$url  = get_template_directory_uri() . '/core/';
		$type = 'theme';
	} else {
		$url  = plugin_dir_url( __FILE__ );
		$type = 'plugin';
	}

	define( 'ET_CORE_PATH', $core_path );
	define( 'ET_CORE_URL', $url );
	define( 'ET_CORE_TEXTDOMAIN', 'et-core' );
	define( 'ET_CORE_TYPE', $type );

	load_theme_textdomain( 'et-core', ET_CORE_PATH . 'languages/' );
	et_core_maybe_set_updated();
	et_new_core_setup();

	register_shutdown_function( 'ET_Core_PageResource::shutdown' );

	if ( is_admin() || ! empty( $_GET['et_fb'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		add_action( 'admin_enqueue_scripts', 'et_core_load_main_styles' );
	}

	et_core_maybe_patch_old_theme();
}
endif;


if ( ! function_exists( 'et_force_edge_compatibility_mode' ) ) :
function et_force_edge_compatibility_mode() {
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
}
endif;
add_action( 'et_head_meta', 'et_force_edge_compatibility_mode' );


if ( ! function_exists( 'et_get_allowed_localization_html_elements' ) ) :
function et_get_allowed_localization_html_elements() {
	$allowlisted_attributes = array(
		'id'    => array(),
		'class' => array(),
		'style' => array(),
	);

	$allowlisted_attributes = apply_filters( 'et_allowed_localization_html_attributes', $allowlisted_attributes );

	$elements = array(
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'rel'    => array(),
		),
		'b'      => array(),
		'br'     => array(),
		'em'     => array(),
		'p'      => array(),
		'span'   => array(),
		'div'    => array(),
		'strong' => array(),
		'code'   => array(),
	);

	$elements = apply_filters( 'et_allowed_localization_html_elements', $elements );

	foreach ( $elements as $tag => $attributes ) {
		$elements[ $tag ] = array_merge( $attributes, $allowlisted_attributes );
	}

	return $elements;
}
endif;


if ( ! function_exists( 'et_get_safe_localization' ) ) :
function et_get_safe_localization( $string ) {
	return apply_filters( 'et_get_safe_localization', wp_kses( $string, et_get_allowed_localization_html_elements() ) );
}
endif;

if ( ! function_exists( 'et_get_theme_version' ) ) :
function et_get_theme_version() {
	$theme_info = wp_get_theme();

	if ( is_child_theme() ) {
		$theme_info = wp_get_theme( $theme_info->parent_theme );
	}

	$theme_version = $theme_info->display( 'Version' );

	return $theme_version;
}
endif;

if ( ! function_exists( 'et_get_child_theme_version' ) ) :
	/**
	 * Get the current version of the active child theme.
	 *
	 * @since 4.10.0
	 */
	function et_get_child_theme_version() {
		$theme_info    = wp_get_theme();
		$theme_info    = wp_get_theme( $theme_info->child_theme );
		$theme_version = $theme_info->display( 'Version' );

		return $theme_version;
	}
endif;

if ( ! function_exists( 'et_requeue_child_theme_styles' ) ) :
	/**
	 * Dequeue child theme css files and re-enqueue them below the theme stylesheet
	 * and dynamic css files to preserve priority.
	 *
	 * @since 4.10.0
	 */
	function et_requeue_child_theme_styles() {
		if ( is_child_theme() ) {
			global $shortname;

			$theme_version          = et_get_child_theme_version();
			$template_directory_uri = preg_quote( get_stylesheet_directory_uri(), '/' );
			$styles                 = wp_styles();
			$inline_style_suffix    = et_core_is_inline_stylesheet_enabled() && et_use_dynamic_css() ? '-inline' : '';
			$style_dep              = array( $shortname . '-style-parent' . $inline_style_suffix );

			if ( empty( $styles->registered ) ) {
				return;
			}

			foreach ( $styles->registered as $handle => $style ) {
				if ( preg_match( '/' . $template_directory_uri . '.*/', $style->src ) ) {
					$style_version = isset( $style->ver ) ? $style->ver : $theme_version;
					et_core_replace_enqueued_style( $style->src, '', $style_version, '', $style_dep, false );
				}
			}
		}
	}
endif;

if ( ! function_exists( 'et_new_core_setup') ):
function et_new_core_setup() {
	$has_php_52x = -1 === version_compare( PHP_VERSION, '5.3' );

	require_once ET_CORE_PATH . 'components/Updates.php';
	require_once ET_CORE_PATH . 'components/init.php';
	require_once ET_CORE_PATH . 'php_functions.php';
	require_once ET_CORE_PATH . 'wp_functions.php';

	if ( $has_php_52x ) {
		spl_autoload_register( 'et_core_autoloader', true );
	} else {
		spl_autoload_register( 'et_core_autoloader', true, true );
	}

	// Initialize top-level components "group"
	$hook = did_action( 'plugins_loaded' ) ?  'after_setup_theme' : 'plugins_loaded';
	add_action( $hook, 'et_core_init', 9999999 );
}
endif;


if ( ! function_exists( 'et_core_add_crossorigin_attribute' ) ):
function et_core_add_crossorigin_attribute( $tag, $handle, $src ) {
	if ( ! $handle || ! in_array( $handle, array( 'react', 'react-dom' ) ) ) {
		return $tag;
	}

	return sprintf( '<script src="%1$s" crossorigin></script>', esc_attr( $src ) ); // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
}
endif;


if ( ! function_exists( 'et_core_get_version_from_filesystem' ) ):
/**
 * Get the core version from the filesystem.
 * This is necessary in cases such as Version Rollback where you cannot use
 * a constant from memory as it is outdated or you wish to get the version
 * not from the active (latest) core but from a different one.
 *
 * @param string $core_directory
 *
 * @return string
 */
function et_core_get_version_from_filesystem( $core_directory ) {
	$version_file = $core_directory . DIRECTORY_SEPARATOR . '_et_core_version.php';

	if ( ! file_exists( $version_file ) ) {
		return '';
	}

	include $version_file;

	return $ET_CORE_VERSION;
}
endif;

if ( ! function_exists( 'et_core_replace_enqueued_style' ) ):
	/**
	 * Replace a style's src if it is enqueued.
	 *
	 * @since 3.10
	 *
	 * @param string  $old_src    Current src of css file.
	 * @param string  $new_src    New css file src to replace old src.
	 * @param string  $new_ver    New version for .css file.
	 * @param string  $new_handle New handle for .css file.
	 * @param string  $new_deps   New deps for .css file.
	 * @param boolean $regex      Use regex to match and replace the style src.
	 *
	 * @return void
	 */
	function et_core_replace_enqueued_style( $old_src, $new_src, $new_ver, $new_handle, $new_deps, $regex = false ) {
		$styles = wp_styles();

		if ( empty( $styles->registered ) ) {
			return;
		}

		foreach ( $styles->registered as $handle => $style ) {
			$match = $regex ? preg_match( $old_src, $style->src ) : $old_src === $style->src;

			if ( ! $match ) {
				continue;
			}

			$old_ver               = isset( $style->ver ) ? $style->ver : false;
			$old_handle            = $handle;
			$old_deps              = isset( $style->deps ) ? $style->deps : array();
			$style_handle          = $new_handle ? $new_handle : $old_handle;
			$style_src             = $regex ? preg_replace( $old_src, $new_src, $style->src ) : $new_src;
			$style_src             = $new_src ? $style_src : $old_src;
			$style_deps            = $new_deps ? $new_deps : $old_deps;
			$style_ver             = $new_ver ? $new_ver : $old_ver;
			$style_media           = isset( $style->args ) ? $style->args : 'all';
			$inline_styles         = $styles->get_data( $handle, 'after' );
			$style_handle_filtered = apply_filters( 'et_core_enqueued_style_handle', $style_handle );

			// Deregister first, so the handle can be re-enqueued.
			wp_dequeue_style( $old_handle );
			wp_deregister_style( $old_handle );

			// Enqueue the same handle with the new src.
			wp_enqueue_style( $style_handle_filtered, $style_src, $style_deps, $style_ver, $style_media );

			if ( ! empty( $inline_styles ) ) {
				wp_add_inline_style( $style_handle_filtered, implode( "\n", $inline_styles ) );
			}
		}
	}
endif;

if ( ! function_exists( 'et_core_is_inline_stylesheet_enabled' ) ) :
	/**
	 * Check to see if Inline Stylesheet is enabled.
	 *
	 * @return bool
	 * @since 4.10.2
	 */
	function et_core_is_inline_stylesheet_enabled() {
		global $shortname;

		if ( defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ) {
			$options           = get_option( 'et_pb_builder_options', array() );
			$inline_stylesheet = isset( $options['performance_main_inline_stylesheet'] ) ? $options['performance_main_inline_stylesheet'] : 'on';
		} else {
			// Get option value. If Extra, defaults to off.
			$inline_stylesheet = et_get_option( $shortname . '_inline_stylesheet', 'extra' === $shortname ? 'off' : 'on' );
		}

		$enable_inline_stylesheet = 'on' === $inline_stylesheet ? true : false;

		return $enable_inline_stylesheet;
	}
endif;

if ( ! function_exists( 'et_core_is_safe_mode_active' ) ):
/**
 * Check whether the Support Center's Safe Mode is active
 *
 * @param false|string $product The ET theme or plugin checking for Safe Mode status.
 *
 * @since ?.?
 *
 * @see ET_Core_SupportCenter::toggle_safe_mode
 *
 * @return bool
 */
function et_core_is_safe_mode_active($product=false) {
	// If we're checking against a particular product, return false if the product-specific usermeta doesn't match
	if ( $product ) {
		$product = esc_attr( $product );
		if ( $product === get_user_meta( get_current_user_id(), '_et_support_center_safe_mode_product', true ) ) {
			return true;
		}
		return false;
	};

	if ( 'on' === get_user_meta( get_current_user_id(), '_et_support_center_safe_mode', true ) ) {
		return true;
	};
	return false;
}
endif;

if ( ! function_exists( 'et_core_load_component' ) ) :
/**
 * =============================
 * ----->>> DEPRECATED! <<<-----
 * =============================
 * Load Core components.
 *
 * This function loads Core components. Components are only loaded once, even if they are called many times.
 * Admin components/functions are automatically wrapped in an is_admin() check.
 *
 * @deprecated Component classes are now loaded automatically upon first use. Portability was the only component
 *             ever loaded by this function, so it now only handles that single use-case (for backwards compatibility).
 *
 * @param string|array $components Name of the Core component(s) to include as and indexed array.
 *
 * @return bool Always return true.
 */
function et_core_load_component( $components ) {
	static $portability_loaded = false;

	if ( $portability_loaded || empty( $components ) ) {
		return true;
	}

	$is_jetpack = isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Jetpack' );

	if ( ! $is_jetpack && ! is_admin() && empty( $_GET['et_fb'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		return true;
	}

	if ( ! class_exists( 'ET_Core_Portability', false ) ) {
		include_once ET_CORE_PATH . 'components/Cache.php';
		include_once ET_CORE_PATH . 'components/Portability.php';
	}

	return $portability_loaded = true;
}
endif;


/**
 * Is WooCommerce plugin active?
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'et_is_woocommerce_plugin_active' ) ):
function et_is_woocommerce_plugin_active() {
	return class_exists( 'WooCommerce' );
}
endif;

/**
 * Check if WPML plugin is active.
 *
 * @since 4.2
 *
 * @return bool
 */
function et_core_is_wpml_plugin_active() {
	return class_exists( 'SitePress' );
}

if ( ! function_exists( 'et_is_product_taxonomy' ) ):
/**
 * Wraps {@see is_product_taxonomy()} to check for its existence before calling.
 *
 * @since 4.0
 *
 * @return bool
 */
function et_is_product_taxonomy() {
	return function_exists( 'is_product_taxonomy' ) && is_product_taxonomy();
}
endif;


if ( ! function_exists( 'et_core_add_allowed_protocols' ) ) :
/**
 * Extend the allowlist of allowed URL protocols
 *
 * @param array $protocols List of URL protocols allowed by WordPress.
 *
 * @since 3.27.2
 *
 * @return array Our extended list of URL protocols.
 */
function et_core_add_allowed_protocols( $protocols = array() ) {
	$additional = array(
		'skype', // Add Skype messaging protocol
		'sms', // Add SMS text messaging protocol
	);
	$protocols  = array_unique( array_merge( $protocols, $additional ) );

	return $protocols;
}
add_filter( 'kses_allowed_protocols', 'et_core_add_allowed_protocols' );
endif;


if ( ! function_exists( 'et_is_responsive_images_enabled' ) ):
/**
 * Get the responsive images setting whether is enabled or not
 *
 * @since 3.27.1
 *
 * @return bool
 */
function et_is_responsive_images_enabled() {
	global $shortname;
	static $enable_responsive_images;

	// Fetch the option once
	if ( null === $enable_responsive_images ) {
		$enable_responsive_images = et_get_option( "{$shortname}_enable_responsive_images", 'on' );
	}

	return 'on' === $enable_responsive_images;
}
endif;

if ( ! function_exists( 'et_screen_sizes' ) ) :
/**
 * Get screen sizes list.
 *
 * @since 3.27.1
 *
 * @return array
 */
function et_screen_sizes() {
	return array(
		'desktop' => 1280,
		'tablet'  => 980,
		'phone'   => 480,
	);
}
endif;

if ( ! function_exists( 'et_image_get_responsive_size' ) ) :
/**
 * Get images responsive sizes.
 *
 * @since 3.27.1
 *
 * @param int    $orig_width Original image's width.
 * @param int    $orig_height Original image's height.
 * @param string $breakpoint Screen breakpont. See et_screen_sizes().
 *
 * @return array|boolean Image responsive width & height. False on failure.
 */
function et_image_get_responsive_size( $orig_width, $orig_height, $breakpoint ) {
	$et_screen_sizes = et_screen_sizes();

	if ( ! isset( $et_screen_sizes[ $breakpoint ] ) ) {
		return false;
	}

	$new_width = $et_screen_sizes[ $breakpoint ];

	if ( $new_width >= $orig_width ) {
		return false;
	}

	$ratio = ( $orig_width * 1.0 ) / $orig_height;

	$new_height = round( ( $new_width / $ratio ) );

	return array(
		'width'  => $new_width,
		'height' => $new_height,
	);
}
endif;

if ( ! function_exists( 'et_image_add_srcset_and_sizes' ) ) :
/**
 * Add ‘srcset’ and ‘sizes’ attributes to an existing ‘img’ element.
 *
 * @param string  $image Image HTML markup.
 * @param boolean $echo  Is print the output?
 *
 * @return string
 */
function et_image_add_srcset_and_sizes( $image, $echo = false ) {
	static $srcset_and_sizes_cached = array();

	// Check if option is enabled.
	if ( ! et_is_responsive_images_enabled() ) {
		if ( $echo ) {
			echo et_core_intentionally_unescaped( $image, 'html' );
		}

		return $image;
	}

	$src = et_get_src_from_img_tag( $image );

	$cache_key = $src ? $src : 'empty-src';

	if ( isset( $srcset_and_sizes_cached[ $cache_key ] ) ) {
		$image = $srcset_and_sizes_cached[ $cache_key ];
	} else {
		// Only process if src attribute is not empty.
		if ( $src ) {
			$attachment_id = et_get_attachment_id_by_url( $src );
			$image_meta    = false;

			if ( $attachment_id ) {
				$image_meta = wp_get_attachment_metadata( $attachment_id );
			}

			if ( $image_meta ) {
				$image = wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id );
			}
		}

		$srcset_and_sizes_cached[ $cache_key ] = $image;
	}

	if ( $echo ) {
		echo et_core_intentionally_unescaped( $image, 'html' );
	}

	return $image;
}
endif;

if ( ! function_exists( 'et_get_attachment_id_by_url_sql' ) ) :
	/**
	 * Generate SQL query syntax to compute attachment ID by URL.
	 *
	 * @since 4.4.2
	 *
	 * @param string $url The URL being looked up.
	 *
	 * @return string SQL query syntax.
	 */
	function et_get_attachment_id_by_url_sql( $normalized_url ) {
		global $wpdb;

		// Strip the HTTP/S protocol.
		$cleaned_url = preg_replace( '/^https?:/i', '', $normalized_url );

		// Remove any thumbnail size suffix from the filename and use that as a fallback.
		$fallback_url = preg_replace( '/-(\d+)x(\d+)\.(jpg|jpeg|gif|png|svg|webp)$/', '.$3', $cleaned_url );

		if ( $cleaned_url === $fallback_url ) {
			$attachments_query = $wpdb->prepare(
				"SELECT id
				FROM $wpdb->posts
				WHERE `post_type` = %s
					AND `guid` IN ( %s, %s )",
				'attachment',
				esc_url_raw( "https:{$cleaned_url}" ),
				esc_url_raw( "http:{$cleaned_url}" )
			);
		} else {
			// Scenario: Trying to find the attachment for a file called x-150x150.jpg.
			// 1. Since WordPress adds the -150x150 suffix for thumbnail sizes we cannot be
			// sure if this is an attachment or an attachment's generated thumbnail.
			// 2. Since both x.jpg and x-150x150.jpg can be uploaded as separate attachments
			// we must decide which is a better match.
			// 3. The above is why we order by guid length and use the first result.
			$attachments_query = $wpdb->prepare(
				"SELECT id
				FROM $wpdb->posts
				WHERE `post_type` = %s
					AND `guid` IN ( %s, %s, %s, %s )
				ORDER BY CHAR_LENGTH( `guid` ) DESC",
				'attachment',
				esc_url_raw( "https:{$cleaned_url}" ),
				esc_url_raw( "https:{$fallback_url}" ),
				esc_url_raw( "http:{$cleaned_url}" ),
				esc_url_raw( "http:{$fallback_url}" )
			);
		}

		return $attachments_query;
	}
endif;

if ( ! function_exists( 'et_get_attachment_id_by_url' ) ) :
/**
 * Tries to get attachment ID by URL.
 *
 * @since 3.27.1
 *
 * @param string $url The URL being looked up.
 *
 * @return int The attachment ID found, or 0 on failure.
 */
function et_get_attachment_id_by_url( $url ) {
	global $wpdb;

	/**
	 * Filters the attachment ID.
	 *
	 * @since 4.2.1
	 *
	 * @param bool    $attachment_id_pre Default value. Default is false.
	 * @param string  $url               URL of the image need to query.
	 *
	 * @return bool|int
	 */
	$attachment_id_pre = apply_filters( 'et_get_attachment_id_by_url_pre', false, $url );

	if ( false !== $attachment_id_pre ) {
		return $attachment_id_pre;
	}

	/**
	 * Filters the attachment GUID.
	 *
	 * This filter intended to get the actual attachment guid URL in case the URL has been filtered before.
	 * For example the URL has been modified to use CDN URL.
	 *
	 * @since 4.2.1
	 *
	 * @param string  $url URL of the image need to query.
	 *
	 * @return string
	 */
	$url = apply_filters( 'et_get_attachment_id_by_url_guid', $url );

	// Normalize image URL.
	$normalized_url = et_attachment_normalize_url( $url );

	// Bail early if the url is invalid.
	if ( ! $normalized_url ) {
		return 0;
	}

	// Load cached data for attachment_id_by_url.
	$cache = ET_Core_Cache_File::get( 'attachment_id_by_url' );

	if ( isset( $cache[ $normalized_url ] ) ) {
		if ( et_core_is_uploads_dir_url( $normalized_url ) ) {
			return $cache[ $normalized_url ];
		}

		unset( $cache[ $normalized_url ] );
		ET_Core_Cache_File::set( 'attachment_id_by_url', $cache );
	}

	$attachments_sql_query = et_get_attachment_id_by_url_sql( $normalized_url );
	$attachment_id         = (int) $wpdb->get_var( $attachments_sql_query );

	// There is this new feature in WordPress 5.3 that allows users to upload big image file
	// (threshold being either width or height of 2560px) and the core will scale it down.
	// This causing the GUID URL info stored is no more relevant since the WordPress core system
	// will append "-scaled." string into the image URL when serving it in the frontend.
	// Hence we run another query as fallback in case the attachment ID is not found and
	// there is "-scaled." string appear in the image URL
	// @see https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/
	// @see https://wordpress.org/support/topic/media-images-renamed-to-xyz-scaled-jpg/
	if ( ! $attachment_id && false !== strpos( $normalized_url, '-scaled.' ) ) {
		$normalized_url_not_scaled = str_replace( '-scaled.', '.', $normalized_url );
		$attachments_sql_query     = et_get_attachment_id_by_url_sql( $normalized_url_not_scaled );
		$attachment_id             = (int) $wpdb->get_var( $attachments_sql_query );
	}

	// There is a case the GUID image URL stored differently with the URL
	// served in the frontend for a featured image, so the query will always fail.
	// Hence we add another fallback query to the _wp_attached_file value in
	// the postmeta table to match with the image relative path.
	if ( ! $attachment_id ) {
		$uploads         = wp_get_upload_dir();
		$uploads_baseurl = trailingslashit( $uploads['baseurl'] );

		if ( 0 === strpos( $normalized_url, $uploads_baseurl ) ) {
			$file_path = str_replace( $uploads_baseurl, '', $normalized_url );
			$file_path_no_resize = preg_replace( '/-(\d+)x(\d+)\.(jpg|jpeg|gif|png|svg|webp)$/', '.$3', $file_path );

			if ( $file_path === $file_path_no_resize ) {
				$attachments_sql_query = $wpdb->prepare(
					"SELECT post_id
					FROM $wpdb->postmeta
					WHERE `meta_key` = %s
						AND `meta_value` = %s",
					'_wp_attached_file',
					$file_path
				);
			} else {
				// Scenario: Trying to find the attachment for a file called x-150x150.jpg.
				// 1. Since WordPress adds the -150x150 suffix for thumbnail sizes we cannot be
				// sure if this is an attachment or an attachment's generated thumbnail.
				// 2. Since both x.jpg and x-150x150.jpg can be uploaded as separate attachments
				// we must decide which is a better match.
				// 3. The above is why we order by meta_value length and use the first result.
				$attachments_sql_query = $wpdb->prepare(
					"SELECT post_id
					FROM $wpdb->postmeta
					WHERE `meta_key` = %s
						AND `meta_value` IN ( %s, %s )
					ORDER BY CHAR_LENGTH( `meta_value` ) DESC",
					'_wp_attached_file',
					$file_path,
					$file_path_no_resize
				);
			}

			$attachment_id = (int) $wpdb->get_var( $attachments_sql_query );
		}
	}

	// Cache data only if attachment ID is found.
	if ( $attachment_id && et_core_is_uploads_dir_url( $normalized_url ) ) {
		$cache[ $normalized_url ] = $attachment_id;
		ET_Core_Cache_File::set( 'attachment_id_by_url', $cache );
	}

	return $attachment_id;
}
endif;

if ( ! function_exists( 'et_get_attachment_size_by_url' ) ) :
/**
 * Tries to get attachment size by URL.
 *
 * @since 3.27.1
 *
 * @param string $url The URL being looked up.
 * @param string $default_size Default size name on failure.
 *
 * @return array|string Detected image size width and height or 'full' on failure.
 */
function et_get_attachment_size_by_url( $url, $default_size = 'full' ) {
	// Normalize image URL.
	$normalized_url = et_attachment_normalize_url( $url );

	// Bail early if URL is invalid.
	if ( ! $normalized_url ) {
		return $default_size;
	}

	$cache = ET_Core_Cache_File::get( 'attachment_size_by_url' );

	if ( isset( $cache[ $normalized_url ] ) ) {
		if ( et_core_is_uploads_dir_url( $normalized_url ) ) {
			return $cache[ $normalized_url ];
		}

		unset( $cache[ $normalized_url ] );
		ET_Core_Cache_File::set( 'attachment_size_by_url', $cache );
	}

	$attachment_id = et_get_attachment_id_by_url( $url );

	if ( ! $attachment_id ) {
		return $default_size;
	}

	$metadata = wp_get_attachment_metadata( $attachment_id );

	if ( ! is_array( $metadata ) ) {
		return $default_size;
	}

	$size = $default_size;

	if ( isset( $metadata['file'] ) && strpos( $url, $metadata['file'] ) === ( strlen( $url ) - strlen( $metadata['file'] ) ) ) {
		$size = array( $metadata['width'], $metadata['height'] );
	} elseif ( preg_match( '/-(\d+)x(\d+)\.(jpg|jpeg|gif|png|svg|webp)$/', $url, $match ) ) {
		// Get the image width and height.
		// Example: https://regex101.com/r/7JwGz7/1.
		$size = array( $match[1], $match[2] );
	}

	// Cache data only if size is found.
	if ( $size !== $default_size && et_core_is_uploads_dir_url( $normalized_url ) ) {
		$cache[ $normalized_url ] = $size;
		ET_Core_Cache_File::set( 'attachment_size_by_url', $cache );
	}

	return $size;
}
endif;

if ( ! function_exists( 'et_get_image_srcset_sizes' ) ) :
/**
 * Get image srcset & sizes attributes.
 *
 * @since 3.29.3
 *
 * @param string $url Image source attribute value.
 *
 * @return (array|bool) Associative array of srcset & sizes attributes. False on failure.
 */
function et_get_image_srcset_sizes( $url ) {
	// Normalize image URL.
	$normalized_url = et_attachment_normalize_url( $url );

	// Bail early if URL is invalid.
	if ( ! $normalized_url ) {
		return array();
	}

	$cache = ET_Core_Cache_File::get( 'image_srcset_sizes' );

	if ( isset( $cache[ $normalized_url ] ) ) {
		if ( et_core_is_uploads_dir_url( $normalized_url ) ) {
			return $cache[ $normalized_url ];
		}

		unset( $cache[ $normalized_url ] );
		ET_Core_Cache_File::set( 'image_srcset_sizes', $cache );
	}

	$attachment_id = et_get_attachment_id_by_url( $url );
	if ( ! $attachment_id ) {
		return array();
	}

	$image_size = et_get_attachment_size_by_url( $url );
	if ( ! $image_size ) {
		return array();
	}

	$srcset = wp_get_attachment_image_srcset( $attachment_id, $image_size );
	$sizes  = wp_get_attachment_image_sizes( $attachment_id, $image_size );

	if ( ! $srcset || ! $sizes ) {
		return array();
	}

	$data = array(
		'srcset' => $srcset,
		'sizes'  => $sizes,
	);

	if ( et_core_is_uploads_dir_url( $normalized_url ) ) {
		$cache[ $normalized_url ] = $data;
		ET_Core_Cache_File::set( 'image_srcset_sizes', $cache );
	}

	return $data;
}
endif;

if ( ! function_exists( 'et_attachment_normalize_url' ) ) :
/**
 * Tries to normalize attachment URL
 *
 * @since 3.27.1
 *
 * @param string $url The URL being looked up.
 *
 * @return string|bool Normalized image URL or false on failure.
 */
function et_attachment_normalize_url( $url ) {
	// Remove URL query and string after
	list( $url ) = explode( '?', $url );

	// Fixes the issue with x symbol between width and height values in the filename.
	$url = str_replace( '%26%23215%3B', 'x', rawurlencode( $url ) );

	// Decode the URL.
	$url = rawurldecode( $url );

	// Set as full path URL.
	if ( 0 !== strpos( $url, 'http' ) ) {
		$wp_upload_dir = wp_upload_dir( null, false );
		$upload_dir    = str_replace( site_url( '/' ), '', $wp_upload_dir['baseurl'] );
		$url_trimmed   = ltrim( $url, '/' );

		if ( 0 === strpos( $url_trimmed, $upload_dir ) || 0 === strpos( $url_trimmed, 'wp-content' ) ) {
			$url = site_url( $url_trimmed );
		} else {
			$url = $wp_upload_dir['baseurl'] . '/' . $url_trimmed;
		}
	}

	// Validate URL format and file extension.
	// Example: https://regex101.com/r/dXcpto/1.
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) || ! preg_match( '/^(.+)\.(jpg|jpeg|gif|png|svg|webp)$/', $url ) ) {
		return false;
	}

	return esc_url( $url );
}
endif;

if ( ! function_exists( 'et_core_is_uploads_dir_url' ) ) :
/**
 * Check if a URL starts with the base upload directory URL.
 *
 * @since 4.2
 *
 * @param string $url The URL being looked up.
 *
 * @return bool
 */
function et_core_is_uploads_dir_url( $url ) {
	$upload_dir = wp_upload_dir( null, false );

	return et_()->starts_with( $url, $upload_dir['baseurl'] );
}
endif;

if ( ! function_exists( 'et_get_src_from_img_tag' ) ) :
/**
 * Get src attribute value from image tag
 *
 * @since 3.27.1
 *
 * @param string $image The HTML image tag to look up.
 *
 * @return string|bool Src attribute value. False on failure.
 */
function et_get_src_from_img_tag( $image ) {
	// Parse src attributes using regex.
	// Example: https://regex101.com/r/kY6Gdd/1.
	if ( preg_match( '/^<img.+src=[\'"](?P<src>.+?)[\'"].*>/', $image, $match ) ) {
		if ( isset( $match['src'] ) ) {
			return $match['src'];
		}
	}

	// Parse src attributes using DOMDocument when regex is failed.
	if ( class_exists( 'DOMDocument' ) && class_exists( 'DOMXPath' ) ) {
		$doc = new DOMDocument();
		$doc->loadHTML( $image );

		$xpath = new DOMXPath( $doc );
		return $xpath->evaluate( 'string(//img/@src)' );
	}

	return false;
}
endif;

if ( ! function_exists( 'et_core_enqueue_js_admin' ) ) :
function et_core_enqueue_js_admin() {
	global $themename;

	$epanel_jsfolder = ET_CORE_URL . 'admin/js';

	et_core_load_main_fonts();

	wp_register_script( 'epanel_colorpicker', $epanel_jsfolder . '/colorpicker.js', array(), et_get_theme_version() );
	wp_register_script( 'epanel_eye', $epanel_jsfolder . '/eye.js', array(), et_get_theme_version() );
	wp_register_script( 'epanel_checkbox', $epanel_jsfolder . '/checkbox.js', array(), et_get_theme_version() );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );

	$wp_color_picker_alpha_uri = defined( 'ET_BUILDER_URI' ) ? ET_BUILDER_URI . '/scripts/ext/wp-color-picker-alpha.min.js' : $epanel_jsfolder . '/wp-color-picker-alpha.min.js';

	wp_enqueue_script( 'wp-color-picker-alpha', $wp_color_picker_alpha_uri, array(
		'jquery',
		'wp-color-picker',
	), et_get_theme_version(), true );

	if ( ! wp_script_is( 'epanel_functions_init', 'enqueued' ) ) {
		wp_enqueue_script( 'epanel_functions_init', $epanel_jsfolder . '/functions-init.js', array(
			'jquery',
			'jquery-ui-tabs',
			'jquery-form',
			'epanel_colorpicker',
			'epanel_eye',
			'epanel_checkbox',
			'wp-color-picker-alpha',
		), et_get_theme_version() );
		wp_localize_script( 'epanel_functions_init', 'ePanelishSettings', array(
			'clearpath'       => get_template_directory_uri() . '/epanel/images/empty.png',
			'epanelish_nonce' => wp_create_nonce( 'epanelish_nonce' ),
			'help_label'      => esc_html__( 'Help', $themename ),
			'et_core_nonces'  => et_core_get_nonces(),
		) );
	}

	// Use WP 4.9 CodeMirror Editor for some fields
	if ( function_exists( 'wp_enqueue_code_editor' ) ) {
		wp_enqueue_code_editor(
			array(
				'type' => 'text/css',
			)
		);
		// Required for Javascript mode
		wp_enqueue_script( 'jshint' );
		wp_enqueue_script( 'htmlhint' );
	}
}
endif;

/**
 * Get ET account information.
 *
 * @since 4.0
 *
 * @return array
 */
function et_core_get_et_account() {
	$utils           = ET_Core_Data_Utils::instance();
	$updates_options = get_site_option( 'et_automatic_updates_options', array() );

	// Improve performance by NOT using $utils->array_get().
	$username = isset( $updates_options['username'] ) ? $updates_options['username'] : '';
	$api_key  = isset( $updates_options['api_key'] ) ? $updates_options['api_key'] : '';

	return array(
		'et_username' => $username,
		'et_api_key'  => $api_key,
		'status'      => get_site_option( 'et_account_status', 'not_active' ),
	);
}

/**
 * Get all meta saved by the builder for a given post.
 *
 * @since 4.0.10
 *
 * @param integer $post_id
 *
 * @return array
 */
function et_core_get_post_builder_meta( $post_id ) {
	$raw_meta = get_post_meta( $post_id );
	$meta     = array();

	foreach ( $raw_meta as $key => $values ) {
		if ( strpos( $key, '_et_pb_' ) !== 0 && strpos( $key, '_et_builder_' ) !== 0 ) {
			continue;
		}

		if ( strpos( $key, '_et_pb_ab_' ) === 0 ) {
			// Do not copy A/B meta as it is post-specific.
			continue;
		}

		foreach ( $values as $value ) {
			$meta[] = array(
				'key'   => $key,
				'value' => $value,
			);
		}
	}

	return $meta;
}

if ( ! function_exists( 'et_core_parse_google_fonts_json' ) ) :
	/**
	 * Parse google fonts json to array.
	 *
	 * @since 4.0.10
	 *
	 * @param string $json Google fonts json file content.
	 *
	 * @return array Associative array list of google fonts.
	 */
	function et_core_parse_google_fonts_json( $fonts_json ) {
		if ( ! $fonts_json || ! is_string( $fonts_json ) ) {
			return array();
		}

		$fonts_json_decoded = json_decode( $fonts_json, true );

		if ( ! $fonts_json_decoded || empty( $fonts_json_decoded['items'] ) ) {
			return array();
		}

		$fonts = array();

		foreach ( $fonts_json_decoded['items'] as $font_item ) {
			if ( ! isset( $font_item['family'], $font_item['variants'], $font_item['subsets'], $font_item['category'] ) ) {
				continue;
			}

			$fonts[ sanitize_text_field( $font_item['family'] ) ] = array(
				'styles'        => sanitize_text_field( implode( ',', $font_item['variants'] ) ),
				'character_set' => sanitize_text_field( implode( ',', $font_item['subsets'] ) ),
				'type'          => sanitize_text_field( $font_item['category'] ),
			);
		}

		ksort( $fonts );

		return $fonts;
	}
endif;

if ( ! function_exists( 'et_core_get_saved_google_fonts' ) ) :
	/**
	 * Get saved google fonts list.
	 *
	 * @since 4.0.10
	 *
	 * @return array Associative array list of google fonts.
	 */
	function et_core_get_saved_google_fonts() {
		static $saved_google_fonts;

		if ( ! is_null( $saved_google_fonts ) ) {
			return $saved_google_fonts;
		}

		$json_file = ET_CORE_PATH . 'json-data/google-fonts.json';

		if ( ! et_()->WPFS()->is_readable( $json_file ) ) {
			return array();
		}

		$saved_google_fonts = et_core_parse_google_fonts_json( et_()->WPFS()->get_contents( $json_file ) );

		return $saved_google_fonts;
	}
endif;

if ( ! function_exists( 'et_core_get_websafe_fonts' ) ) :
	/**
	 * Get websafe fonts list.
	 *
	 * @since 4.0.10
	 *
	 * @return array Associative array list of websafe fonts.
	 */
	function et_core_get_websafe_fonts() {
		$websafe_fonts = array(
			'Georgia' => array(
				'styles'        => '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set' => 'cyrillic,greek,latin',
				'type'          => 'serif',
			),
			'Times New Roman' => array(
				'styles'        => '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set' => 'arabic,cyrillic,greek,hebrew,latin',
				'type'          => 'serif',
			),
			'Arial' => array(
				'styles'        => '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set' => 'arabic,cyrillic,greek,hebrew,latin',
				'type'          => 'sans-serif',
			),
			'Trebuchet' => array(
				'styles'         => '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set'  => 'cyrillic,latin',
				'type'           => 'sans-serif',
				'add_ms_version' => true,
			),
			'Verdana' => array(
				'styles'        => '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set' => 'cyrillic,latin',
				'type'          => 'sans-serif',
			),
		);

		foreach ( array_keys( $websafe_fonts ) as $font_name ) {
			$websafe_fonts[ $font_name ]['standard'] = true;
		}

		ksort( $websafe_fonts );

		return apply_filters( 'et_websafe_fonts', $websafe_fonts );
	}
endif;

if ( ! function_exists( 'et_maybe_update_hosting_card_status' ) ) :
	/**
	 * Divi Hosting Card :: Update dismiss status via ET API
	 *
	 * @since 4.4.7
	 */
	function et_maybe_update_hosting_card_status() {
		$et_account        = et_core_get_et_account();
		$et_username       = et_()->array_get( $et_account, 'et_username', '' );
		$et_api_key        = et_()->array_get( $et_account, 'et_api_key', '' );

		// Exit if ET Username and/or ET API Key is not found
		if ( '' === $et_username || '' === $et_api_key ) {
			// Remove any WP Cron for Updating Hosting Card Status
			wp_unschedule_hook( 'et_maybe_update_hosting_card_status_cron' );

			return;
		}

		global $wp_version;

		// Prepare settings for API request
		$options = array(
			'timeout'    => 10,
			'body'       => array(
				'action'   => 'disable_hosting_card',
				'username' => $et_username,
				'api_key'  => $et_api_key,
			),
			'user-agent' => 'WordPress/' . $wp_version . '; Hosting Card/' . ET_CORE_VERSION . '; ' . home_url( '/' ),
		);

		$request               = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );
		$request_response_code = wp_remote_retrieve_response_code( $request );
		$response_body         = wp_remote_retrieve_body( $request );
		$response              = (array) json_decode( $response_body );

		// API request has been updated successfully and the User has already disabled the card, or,
		// when API request was successful and returns error message
		if ( 'disabled' === et_()->array_get( $response, 'status' ) || '' !== et_()->array_get( $response, 'error', '' ) ) {
			// Remove any WP Cron for Updating Hosting Card Status
			wp_unschedule_hook( 'et_maybe_update_hosting_card_status_cron' );

			return;
		}

		// Fail-safe :: Schedule WP Cron to try again
		// Once something were wrong in API request, or, response has error code
		if ( is_wp_error( $request ) || 200 !== $request_response_code ) {

			// First API request has failed, which were done already in above, second request
			// (via cron) will be made in a minute, then third (via cron) and future (via cron)
			// call will be per hour. Once API request is successful, cron will be removed
			$timestamp = time() + 1 * MINUTE_IN_SECONDS;

			if ( ! wp_next_scheduled( 'et_maybe_update_hosting_card_status_cron' ) ) {
				wp_schedule_event( $timestamp, 'hourly', 'et_maybe_update_hosting_card_status_cron' );
			}
		}
	}
endif;

// Action for WP Cron: Disable Hosting Card status via ET API
add_action( 'et_maybe_update_hosting_card_status_cron', 'et_maybe_update_hosting_card_status' );

if ( ! function_exists( 'et_disable_emojis' ) ) :
	/**
	 * Disable WordPress Emojis
	 * Copyright Ryan Hellyer https://geek.hellyer.kiwi/
	 * License: GPL2
	 *
	 * @since 4.10.0
	 *
	 * Removes WordPress emoji scripts and styles.
	 */
	function et_disable_emojis() {
		global $shortname;

		if ( 'on' === et_get_option( $shortname . '_disable_emojis', 'on' ) ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'tiny_mce_plugins', 'et_disable_emojis_tinymce' );
			add_filter( 'wp_resource_hints', 'et_disable_emojis_dns_prefetch', 10, 2 );
		}
	}
endif;

if ( ! function_exists( 'et_disable_emojis_tinymce' ) ) :
	/**
	 * Disables tinymce emojis.
	 * Copyright Ryan Hellyer https://geek.hellyer.kiwi/
	 * License: GPL2
	 *
	 * @since 4.10.0
	 *
	 * @param array $plugins of plugins.
	 * @return array plugins.
	 */
	function et_disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		}

		return array();
	}
endif;

if ( ! function_exists( 'et_disable_emojis_dns_prefetch' ) ) :
	/**
	 * Disables dns prefech meta tags.
	 * Copyright Ryan Hellyer https://geek.hellyer.kiwi/
	 * License: GPL2
	 *
	 * @since 4.10.0
	 *
	 * @param array  $urls URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for.
	 * @return array plugins.
	 */
	function et_disable_emojis_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
			foreach ( $urls as $key => $url ) {
				if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
					unset( $urls[ $key ] );
				}
			}
		}

		return $urls;
	}
endif;

if ( ! function_exists( 'et_dequeue_block_css' ) ) :
	/**
	 * If the option is enabled and the page is built with the Divi Builder,
	 * dequeue the gutenberg block css file from the head.
	 *
	 * @since 4.10.0
	 */
	function et_dequeue_block_css() {
		global $shortname;

		$post_id                 = get_the_id();
		$is_page_builder_used    = function_exists( 'et_pb_is_pagebuilder_used' ) ? et_pb_is_pagebuilder_used( $post_id ) : false;
		$defer_block_css_enabled = ( 'on' === et_get_option( $shortname . '_defer_block_css', 'on' ) );
		$is_wp_template_used     = ! empty( et_builder_get_wp_editor_templates() );

		if ( $is_page_builder_used && $defer_block_css_enabled && ! $is_wp_template_used ) {
			wp_dequeue_style( 'wp-block-library' );
		}
	}
endif;

if ( ! function_exists( 'et_enqueue_block_css' ) ) :
	/**
	 * If the option is enabled and the page is built with the Divi Builder,
	 * enqueue the gutenberg block css file in the body.
	 *
	 * @since 4.10.0
	 */
	function et_enqueue_block_css() {
		global $shortname;

		$post_id                 = get_the_id();
		$is_page_builder_used    = et_pb_is_pagebuilder_used( $post_id );
		$defer_block_css_enabled = ( 'on' === et_get_option( $shortname . '_defer_block_css', 'on' ) );

		if ( $is_page_builder_used && $defer_block_css_enabled ) {
			// Defer the stylesheet.
			add_filter( 'style_loader_tag', 'et_defer_gb_css', 10, 2 );
			// Re-enqueue the deferred stylesheet.
			wp_enqueue_style( 'wp-block-library' );
		}
	}
endif;

if ( ! function_exists( 'et_defer_gb_css' ) ) :
	/**
	 * Load GB stylesheet asynchronously by swapping the media attribute on load.
	 *
	 * @since 4.10.0
	 *
	 * @param string $html HTML to replace.
	 * @param string $handle Stylesheet handle.
	 * @return string $html replacement html.
	 */
	function et_defer_gb_css( $html, $handle ) {
		if ( 'wp-block-library' === $handle ) {
			return str_replace( "media='all'", "media='none' onload=\"media='all'\"", $html );
		}

		return $html;
	}
endif;

/**
 * Enqueue Code snippets library scripts on theme options page.
 *
 * @since 4.19.0
 *
 * @param string $hook_suffix Page hook suffix.
 * @return void
 */
function et_code_snippets_admin_enqueue_scripts( $hook_suffix ) {
	global $shortname;

	// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state and is therefore not susceptible to CSRF.
	$is_templates_page = isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'];
	$is_options_page   = 'toplevel_page_et_' . $shortname . '_options' === $hook_suffix;

	$current_screen          = get_current_screen();
	$is_layouts_library_page = isset( $current_screen->id ) && 'edit-et_pb_layout' === $current_screen->id;

	if ( ! $is_templates_page && ! $is_options_page && ! $is_layouts_library_page && ! et_builder_bfb_enabled() ) {
		return;
	}

	if ( ! class_exists( 'ET_Code_Snippets_App' ) ) {
		require_once ET_CORE_PATH . 'code-snippets/code-snippets-app.php';
	}

	if ( $is_layouts_library_page ) {
		// Avoids et_cloud_data not defined error.
		ET_Cloud_App::load_js();
	}
	ET_Code_Snippets_App::load_js();
}

add_action( 'admin_enqueue_scripts', 'et_code_snippets_admin_enqueue_scripts' );

/**
 * Enqueue Code snippets library scripts in VB.
 *
 * @since 4.19.0
 *
 * @return void
 */
function et_code_snippets_vb_enqueue_scripts() {
	if ( ! et_core_is_fb_enabled() ) {
		return;
	}

	if ( ! class_exists( 'ET_Code_Snippets_App' ) ) {
		require_once ET_CORE_PATH . 'code-snippets/code-snippets-app.php';
	}

	ET_Code_Snippets_App::load_js();
}
add_action( 'wp_enqueue_scripts', 'et_code_snippets_vb_enqueue_scripts' );

/**
 * Enqueue AI scripts on BFB page.
 *
 * @since 4.22.0
 *
 * @return void
 */
function et_ai_admin_enqueue_scripts() {
	if ( ! et_builder_bfb_enabled() ) {
		return;
	}

	if ( ! class_exists( 'ET_AI_App' ) ) {
		$path = defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ? ET_BUILDER_PLUGIN_DIR : get_template_directory();
		require_once $path . '/ai-app/ai-app.php';
	}

	if ( et_pb_is_allowed( 'divi_ai' ) ) {
		ET_AI_App::load_js();
	}
}

add_action( 'admin_enqueue_scripts', 'et_ai_admin_enqueue_scripts' );

/**
 * Load Cloud Snippets App on `Export To Divi Cloud` btn click.
 *
 * @since 4.21.1
 *
 * @return void
 */
function et_save_to_cloud_modal() {
	$current_screen    = get_current_screen();
	$current_screen_id = $current_screen ? $current_screen->id : '';

	if ( 'edit-et_pb_layout' !== $current_screen_id ) {
		return;
	}
	?>
		<div id="et-cloud-app--layouts"></div>
	<?php
}

add_action( 'admin_footer', 'et_save_to_cloud_modal' );

/**
 * Get roles with specific capabilities.
 *
 * This function iterates through WordPress roles, identifying those with the specific
 * capabilities. The resulting array of applicable roles is then filtered using the
 * 'et_core_get_roles_by_capabilities' hook.
 *
 * Notes: The relation between the capabilities is "AND". So, all the capabilites should
 * be available and enabled to mark current role.
 *
 * @since 4.25.2
 *
 * @param array $capabilities Specific capabilities that we want to check.
 *
 * @return array List of roles based on the specific capabilities.
 */
function et_core_get_roles_by_capabilities( $capabilities ) {
	global $wp_roles;

	$roles = [];

	foreach ( $wp_roles->roles as $role => $details ) {
		// By default, we assume that current role has all the capabilities.
		$has_capabilities = true;

		// Iterate through the capabilities to check the availability and activation.
		foreach ( $capabilities as $capability ) {
			// But once we find out one of the capabilities doesn't exist for current role,
			// break the loop and mark it as `false` to fasten the checking process.
			$has_capability = isset( $details['capabilities'][ $capability ] ) ? $details['capabilities'][ $capability ] : false;

			if ( ! $has_capability ) {
				$has_capabilities = false;
				break;
			}
		}

		// If capability check done and current role has all the capabilities, assign it.
		if ( $has_capabilities ) {
			$roles[] = $role;
		}
	}

	/**
	 * Filters the list of roles based on the specific capabilities.
	 *
	 * @since 4.25.2
	 *
	 * @param array  $roles        List of roles based on the specific capabilities.
	 * @param string $capabilities Specific capabilities that we want to check.
	 */
	return apply_filters( 'et_core_get_roles_by_capabilities', $roles, $capabilities );
}

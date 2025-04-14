<?php
/**
 * Load Elegant Themes Core.
 *
 * @package \ET\Core
 */


if ( defined( 'ET_CORE' ) ) {
	// Core has already been loaded.
	return;
}

define( 'ET_CORE', true );


if ( ! function_exists( '_et_core_find_latest' ) ) :
/**
 * Find the latest version of Core currently available.
 *
 * @since 3.0.60
 *
 * @return string $core_path Absolute path to the latest version of core.
 */
function _et_core_find_latest( $return = 'path' ) {
	static $latest_core_path    = null;
	static $latest_core_version = null;

	if ( 'path' === $return && null !== $latest_core_path ) {
		return $latest_core_path;
	}

	if ( 'version' === $return && null !== $latest_core_version ) {
		return $latest_core_version;
	}

	$this_core_path = _et_core_normalize_path( dirname( __FILE__ ) );
	$content_dir    = _et_core_normalize_path( WP_CONTENT_DIR );

	include $this_core_path . '/_et_core_version.php';

	$latest_core_path    = $this_core_path;
	$latest_core_version = $ET_CORE_VERSION;

	unset( $ET_CORE_VERSION );

	$version_files = array_merge(
		(array) glob( "{$content_dir}/themes/*/core/_et_core_version.php" ),
		(array) glob( "{$content_dir}/plugins/*/core/_et_core_version.php" )
	);

	foreach ( $version_files as $version_file ) {
		$version_file = _et_core_normalize_path( $version_file );

		if ( ! is_file( $version_file ) || 0 === strpos( $version_file, $this_core_path ) ) {
			continue;
		}

		include_once $version_file;

		if ( ! isset( $ET_CORE_VERSION ) ) {
			continue;
		}

		$is_greater_than = version_compare( $ET_CORE_VERSION, $latest_core_version, '>' );

		if ( $is_greater_than && _et_core_path_belongs_to_active_product( $version_file ) ) {
			$latest_core_path    = _et_core_normalize_path( dirname( $version_file ) );
			$latest_core_version = $ET_CORE_VERSION;
		}

		unset( $ET_CORE_VERSION );
	}

	if ( 'version' === $return ) {
		return $latest_core_version;
	}

	return $latest_core_path;
}
endif;


if ( ! function_exists( '_et_core_path_belongs_to_active_product' ) ):
/**
 * @private
 * @internal
 */
function _et_core_path_belongs_to_active_product( $path ) {
	global $wp_customize;

	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	$theme_dir = _et_core_normalize_path( get_template_directory() );

	// When previewing a theme the `get_template_directory()` doesn't return the directory of the previewed theme
	// since this function will be called earlier (before `WP_Customize_Manager` manipulates the active theme)
	// when loaded from plugins (e.g bloom)
	if ( is_a( $wp_customize, 'WP_Customize_Manager' ) && ! $wp_customize->is_theme_active() ) {
		$template                   = $wp_customize->get_template();
		$theme_root                 = get_theme_root( $template );
		$preview_template_directory =  apply_filters( 'template_directory', "$theme_root/$template", $template, $theme_root );
		$theme_dir                  = _et_core_normalize_path( $preview_template_directory );
	}

	if ( 0 === strpos( $path, $theme_dir ) ) {
		return true;
	}

	if ( false !== strpos( $path, '/divi-builder/' ) ) {
		return is_plugin_active( 'divi-builder/divi-builder.php' );
	}

	if ( false !== strpos( $path, '/bloom/' ) ) {
		return is_plugin_active( 'bloom/bloom.php' );
	}

	if ( false !== strpos( $path, '/monarch/' ) ) {
		return is_plugin_active( 'monarch/monarch.php' );
	}

	return false;
}
endif;


if ( ! function_exists( '_et_core_load_latest' ) ):
function _et_core_load_latest() {
	if ( defined( 'ET_CORE_VERSION' ) ) {
		return;
	}

	$core_path      = get_transient( 'et_core_path' );
	$version_file   = $core_path ? file_exists( $core_path . '/_et_core_version.php' ) : false;
	$have_core_path = $core_path && $version_file && ! defined( 'ET_DEBUG' );

	if ( $have_core_path && _et_core_path_belongs_to_active_product( $core_path ) ) {
		$core_version      = get_transient( 'et_core_version' );
		$core_path_changed = false;
	} else {
		$core_path         = _et_core_find_latest();
		$core_version      = _et_core_find_latest( 'version' );
		$core_path_changed = true;
	}

	/**
	 * Overrides ET_CORE_PATH right before its loaded.
	 *
	 * @since 3.0.68
	 *
	 * @param bool|string $core_path_override The absolute path to the core that should be loaded.
	 */
	$core_path_override = apply_filters( 'et_core_path_override', false );

	if ( $core_path_override ) {
		$core_path = $core_path_override;
	} else if ( $core_path_changed ) {
		set_transient( 'et_core_path', $core_path, DAY_IN_SECONDS );
		set_transient( 'et_core_version', $core_version, DAY_IN_SECONDS );
	}

	define( 'ET_CORE_VERSION', $core_version );

	require_once $core_path . '/functions.php';
}
endif;


if ( ! function_exists( '_et_core_normalize_path' ) ):
/**
 * @private
 * @internal
 */
function _et_core_normalize_path( $path ) {
	return $path ? str_replace( '\\', '/', $path ) : '';
}
endif;


if ( ! function_exists( 'register_portability_for_code_snippets' ) ) :
	/**
	 * Register portability which is needed to import/export saved Snippet via Divi Library.
	 *
	 * @since 4.19.0
	 */
	function register_portability_for_code_snippets() {
		// No permission, can't load library UI in the first place.
		if ( et_pb_is_allowed( 'divi_library' ) ) {
			// Register portability.
			et_core_portability_register(
				'et_code_snippets',
				array(
					'name' => esc_html__( 'Divi Code Snippets', 'et_builder' ),
				)
			);
		}
	}

	add_action( 'admin_init', 'register_portability_for_code_snippets' );
endif;


_et_core_load_latest();

<?php

if ( ! defined( 'ET_COMMON_DIR' ) ) {
	define( 'ET_COMMON_DIR', get_template_directory() . '/common/' );
}

if ( ! function_exists( 'et_common_setup' ) ) :
	/**
	 * Setup Common const.
	 *
	 * @since ??
	 */
	function et_common_setup() {
		if ( defined( 'ET_COMMON_URL' ) ) {
			return;
		}

		$common_path = _et_core_normalize_path( trailingslashit( dirname( __FILE__ ) ) );
		$theme_dir   = _et_core_normalize_path( trailingslashit( realpath( get_template_directory() ) ) );

		if ( 0 === strpos( $common_path, $theme_dir ) ) {
			$url = get_template_directory_uri() . '/common/';
		} else {
			$url = plugin_dir_url( __FILE__ );
		}

		define( 'ET_COMMON_URL', $url );

		require_once ET_COMMON_DIR . 'admin.php';
		require_once ET_COMMON_DIR . 'library.php';
	}
endif;


if ( ! function_exists( 'et_fb_enqueue_react' ) ):
	/**
	 * Load React. Use react from cdn server in debug mode or local version in production.
	 *
	 * @since ??
	 *
	 */
	function et_fb_enqueue_react() {
		if ( ! et_common_should_enqueue_react() ) {
			return;
		}

		$DEBUG          = defined( 'ET_DEBUG' ) && ET_DEBUG;
		$common_scripts = ET_COMMON_URL . 'scripts';
		$react_version  = '16.14.0';

		wp_dequeue_script( 'react' );
		wp_dequeue_script( 'react-dom' );
		wp_deregister_script( 'react' );
		wp_deregister_script( 'react-dom' );

		if ( $DEBUG || DiviExtensions::is_debugging_extension() ) {
			wp_enqueue_script( 'react', "https://cdn.jsdelivr.net/npm/react@{$react_version}/umd/react.development.js", array(), $react_version, true );
			wp_enqueue_script( 'react-dom', "https://cdn.jsdelivr.net/npm/react-dom@{$react_version}/umd/react-dom.development.js", array( 'react' ), $react_version, true );
			add_filter( 'script_loader_tag', 'et_core_add_crossorigin_attribute', 10, 3 );
		} else {
			wp_enqueue_script( 'react', "{$common_scripts}/react.production.min.js", array(), $react_version, true );
			wp_enqueue_script( 'react-dom', "{$common_scripts}/react-dom.production.min.js", array( 'react' ), $react_version, true );
		}
	}
endif;

if ( ! function_exists( 'et_common_should_enqueue_react' ) ) :
	/**
	 * Determine whether React should be enqueued or not.
	 *
	 * @since 4.20.1
	 *
	 * @return bool
	 */
	function et_common_should_enqueue_react() {
		$page            = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		$post_type       = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		$is_fb           = et_core_is_fb_enabled();
		$is_tb           = et_pb_is_allowed( 'theme_builder' ) && 'et_theme_builder' === $page;
		$is_epanel       = et_pb_is_allowed( 'theme_options' ) && 'et_divi_options' === $page;
		$is_divi_library = et_pb_is_allowed( 'divi_library' ) && 'et_pb_layout' === $post_type;

		$should_enqueue = $is_fb || $is_tb || $is_epanel || $is_divi_library;

		/**
		 * Filter whether React should be enqueued or not.
		 *
		 * @since 4.20.1
		 *
		 * @param string $should_enqueue Enqueue status.
		 */
		return apply_filters( 'et_common_should_enqueue_react', $should_enqueue );
	}
endif;

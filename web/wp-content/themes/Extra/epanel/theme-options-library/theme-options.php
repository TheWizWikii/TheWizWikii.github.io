<?php
/**
 * Theme options quick feature entry file.
 *
 * Divi Cloud Theme Options Library.
 *
 * @package Divi
 * @subpackage Epanel
 * @since ??
 */

if ( ! defined( 'ET_THEME_OPTIONS_DIR' ) ) {
	define( 'ET_THEME_OPTIONS_DIR', get_template_directory() . '/epanel/theme-options-library/' );
}

require_once trailingslashit( ET_THEME_OPTIONS_DIR ) . 'constants.php';
require_once trailingslashit( ET_THEME_OPTIONS_DIR ) . 'api.php';

if ( ! function_exists( 'et_init_theme_options_library' ) ) :
	/**
	 * Init Theme Options Library.
	 *
	 * @return void
	 */
	function et_init_theme_options_library() {
		require_once trailingslashit( ET_THEME_OPTIONS_DIR ) . 'ThemeOptionsLibrary.php';
	}
endif;

add_action( 'init', 'et_init_theme_options_library' );

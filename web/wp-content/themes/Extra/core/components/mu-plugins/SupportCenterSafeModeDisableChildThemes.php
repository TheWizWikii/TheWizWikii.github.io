<?php
/**
 * Plugin Name: Elegant Themes Support Center :: Safe Mode Child Theme Disabler
 * Plugin URI: http://www.elegantthemes.com
 * Description: When the ET Support Center's Safe Mode is activated, this Must-Use Plugin will temporarily disable any active child themes for that user.
 * Author: Elegant Themes
 * Author URI: http://www.elegantthemes.com
 * License: GPLv2 or later
 *
 * @package ET\Core\SupportCenter\SafeModeDisableChildThemes
 * @author Elegant Themes <http://www.elegantthemes.com>
 * @license GNU General Public License v2 <http://www.gnu.org/licenses/gpl-2.0.html>
 */

// Quick exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable Child Theme (if Safe Mode is active)
 *
 * The `is_child_theme()` function returns TRUE if a child theme is active. Parent theme info can be gathered from
 * the child theme's settings, so in the case of an active child theme we can capture the parent theme's info and
 * temporarily push the parent theme as active (similar to how WP lets the user preview a theme before activation).
 *
 * @since 3.20
 * @since 3.23 Moved from `ET_Core_SupportCenter::maybe_disable_child_theme()` for an improved Safe Mode experience.
 *
 * @param $current_theme
 *
 * @return false|string
 */
function et_safe_mode_maybe_disable_child_theme( $current_theme ) {
	// Verbose logging: only log if `wp-config.php` has defined `ET_DEBUG='support_center'`
	$DEBUG_ET_SUPPORT_CENTER = defined( 'ET_DEBUG' ) && 'support_center' === ET_DEBUG;

	if ( ! isset( $_COOKIE['et-support-center-safe-mode'] ) ) {
		if ( $DEBUG_ET_SUPPORT_CENTER ) {
			error_log( 'ET Support Center :: Safe Mode: No cookie found' );
		}

		return $current_theme;
	}

	$verify = get_option( 'et-support-center-safe-mode-verify' );

	if ( ! $verify ) {
		if ( $DEBUG_ET_SUPPORT_CENTER ) {
			error_log( 'ET Support Center :: Safe Mode: No option found to verify cookie' );
		}

		return $current_theme;
	}

	if ( $_COOKIE['et-support-center-safe-mode'] !== $verify ) {
		if ( $DEBUG_ET_SUPPORT_CENTER ) {
			error_log( 'ET Support Center :: Safe Mode: Cookie/Option mismatch' );
		}

		return $current_theme;
	}

	$template = get_option( 'template' );

	if ( $template !== $current_theme ) {
		return $template;
	}

	return $current_theme;
}

add_filter( 'template', 'et_safe_mode_maybe_disable_child_theme' );
add_filter( 'stylesheet', 'et_safe_mode_maybe_disable_child_theme' );

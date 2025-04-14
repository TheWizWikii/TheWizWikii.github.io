<?php
/**
 * Plugin Name: ET Support Center :: Must-Use Plugins Autoloader
 * Plugin URI: http://www.elegantthemes.com
 * Description: This plugin enables the Elegant Themes Support Center to provide more consistent functionality when Safe Mode is active.
 * Author: Elegant Themes
 * Author URI: http://www.elegantthemes.com
 * License: GPLv2 or later
 *
 * @package ET\Core\SupportCenter\SafeModeDisablePlugins
 * @author Elegant Themes <http://www.elegantthemes.com>
 * @license GNU General Public License v2 <http://www.gnu.org/licenses/gpl-2.0.html>
 */

// The general idea here is loosely based on <https://codex.wordpress.org/Must_Use_Plugins#Autoloader_Example>.

// Quick exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We only want to load these MU Plugins if Support Center is installed
$support_center_installed = get_option( 'et_support_center_installed' );

if ( $support_center_installed ) {
	// Compile a list of plugins in the `mu-plugins/et-safe-mode` directory
	// (see `$pathname_to` in `ET_Core_SupportCenter::maybe_add_mu_autoloader()`)
	if ( $mu_plugins = glob( dirname( __FILE__ ) . '/et-safe-mode/*.php' ) ) {
		// Verbose logging: only log if `wp-config.php` has defined `ET_DEBUG='support_center'`
		$DEBUG_ET_SUPPORT_CENTER = defined( 'ET_DEBUG' ) && 'support_center' === ET_DEBUG;

		// Loop through the list of plugins and require each in turn
		foreach ( $mu_plugins as $plugin ) {
			if ( file_exists( $plugin ) ) {
				if ( $DEBUG_ET_SUPPORT_CENTER ) {
					error_log( 'ET Support Center: loading mu-plugin: ' . $plugin );
				}
				require_once( $plugin );
			}
		}
	}
}

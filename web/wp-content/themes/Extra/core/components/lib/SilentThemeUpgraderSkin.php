<?php
require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

if ( ! class_exists( 'ET_Core_LIB_SilentThemeUpgraderSkin' ) ):
	/**
	 * Theme Upgrader skin which does not output feedback.
	 *
	 * @since 3.10
	 *
	 * @private
	 */
	class ET_Core_LIB_SilentThemeUpgraderSkin extends WP_Upgrader_Skin {
		/**
		 * Override feedback method.
		 *
		 * @since 3.10
		 *
		 * @private
		 *
		 * @param string $string Feedback string.
		 * @param mixed  ...$args Optional text replacements.
		 */
		public function feedback( $string, ...$args ) {
			return; // Suppress all feedback.
		}
	}
endif;

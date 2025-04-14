<?php // phpcs:ignore WordPress.Files.FileName -- We don't follow WP filename format.
/**
 * ET_Builder_Plugin_Compat_Relevanssi class file.
 *
 * @class   ET_Builder_Plugin_Compat_Relevanssi
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the Relevanssi plugin.
 *
 * @since 4.7.0
 *
 * @link https://wordpress.org/plugins/relevanssi/
 */
class ET_Builder_Plugin_Compat_Relevanssi extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->plugin_id = $this->_get_plugin_id();
		$this->init_hooks();
	}

	/**
	 * Get the currently activated plugin id as the FREE and PRO versions are separate plugins.
	 *
	 * @since 4.9.4
	 *
	 * @return string
	 */
	protected function _get_plugin_id() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$pro  = 'relevanssi-premium/relevanssi.php';
		$free = 'relevanssi/relevanssi.php';

		return is_plugin_active( $pro ) ? $pro : $free;
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.7.0
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_blog_query', array( $this, 'maybe_modify_blog_query' ) );
	}

	/**
	 * Maybe modify blog query to intercept the posts result.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_Query $query Main blog query.
	 *
	 * @return WP_Query Modified blog query.
	 */
	public function maybe_modify_blog_query( $query ) {
		// Modify blog query when the current page is search result page.
		if ( is_search() && function_exists( 'relevanssi_do_query' ) && $query->query_vars['s'] ) {
			relevanssi_do_query( $query );
		}

		return $query;
	}
}

new ET_Builder_Plugin_Compat_Relevanssi();

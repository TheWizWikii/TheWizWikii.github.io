<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WordPress Toolbar Publish Button
 */
class ET_Builder_Plugin_Compat_Toolber_Publish_Button extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_id = 'toolbar-publish-button/toolbar-publish-button.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_fb_ignore_adminbar_click_ids', array( $this, 'et_fb_ignore_adminbar_click_ids' ) );
	}

	/**
	 * Add this plugin `publish` button to ignored Admin Bar click ids.
	 *
	 * @param array $ids Ignored Admin Bar click ids.
	 *
	 * @access public.
	 * @return array
	 */
	public function et_fb_ignore_adminbar_click_ids( $ids ) {
		$ids[] = 'top-toolbar-submit';
		return $ids;
	}
}

new ET_Builder_Plugin_Compat_Toolber_Publish_Button();

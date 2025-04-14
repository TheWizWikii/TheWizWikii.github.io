<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for dk-pdf
 *
 * @since 3.0.96
 * @link https://wordpress.org/plugins/dk-pdf/
 */
class ET_Builder_Plugin_Compat_DK_Pdf extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'dk-pdf/dk-pdf.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Note: once this issue is fixed in future version, run version_compare() to limit the scope
	 * of the hooked fix
	 * Latest plugin version: 1.9.3
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Up to: latest theme version

		// If current page has pdf query string, it means that DK PDF re-route the request to
		// display the pdf version of the page
		if ( isset( $_GET['pdf'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			add_filter(
				'et_pb_load_main_elements_priority',
				array( $this, 'fix_load_main_elements_priority' )
			);
		}

		// Add styling to fix DK PDF button when page builder is used
		add_action( 'wp_enqueue_scripts', array( $this, 'fix_dkpdf_button_styling' ), 20 );
	}

	/**
	 * Modify priority of et_builder_add_main_elements() hook so it'll be triggered before dk-pdf
	 *
	 * @return int
	 */
	function fix_load_main_elements_priority( $priority ) {
		return 7;
	}

	/**
	 * Modify DK PDF button if the page uses builder
	 *
	 * @return void
	 */
	function fix_dkpdf_button_styling() {
		global $post;

		if ( isset( $post->ID ) && et_pb_is_pagebuilder_used( $post->ID ) ) {
			$content_width = et_get_option( 'content_width', 1080 );

			wp_add_inline_style(
				'dkpdf-frontend',
				'.dkpdf-button-container { float: none; width: 80%; max-width: ' . $content_width . 'px; margin: 0 auto; }'
			);
		}
	}
}
new ET_Builder_Plugin_Compat_DK_Pdf();

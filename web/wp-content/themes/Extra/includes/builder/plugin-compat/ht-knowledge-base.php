<?php // phpcs:disable Squiz.Commenting.FileComment.Missing -- Not used in other compat classes.
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the Heroic Knowledge Base plugin.
 *
 * @link https://herothemes.com/plugins/heroic-wordpress-knowledge-base/
 */
class ET_Builder_Plugin_Compat_HT_Knowledge_Base extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->plugin_id = 'ht-knowledge-base/ht-knowledge-base.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'template_include', array( $this, 'tb_category_page_compatibility' ) );
	}

	/**
	 * Compatibility with Heroic Knowledge Base :: Theme Builder doesn't work on category pages
	 * https://github.com/elegantthemes/Divi/issues/22184
	 *
	 * @param string $template The template file.
	 *
	 * @return string $template The filtered template file.
	 */
	public function tb_category_page_compatibility( $template ) {
		global $wp_query, $ht_knowledge_base_init;

		// Dummy post default.
		$dummy_post_default = array();

		$queried_object = isset( $wp_query->queried_object ) ? $wp_query->queried_object : null;

		if ( ! empty( $queried_object->taxonomy ) && 'ht_kb_category' === $queried_object->taxonomy ) {
			$dummy_post_default['is_tax'] = true;

			// Reset post.
			$ht_knowledge_base_init->ht_kb_theme_compat_reset_post( $dummy_post_default );
		}

		return $template;
	}
}

new ET_Builder_Plugin_Compat_HT_Knowledge_Base();

<?php
/**
 * Theme Options library.
 *
 * Registers post types to be used in the "Theme Options" library.
 *
 * @package Divi
 * @subpackage Cloud
 * @since ??
 */

/**
 * Core class used to implement "Theme Options" library.
 *
 * Register post types & taxonomies to be used in "Theme Options" library.
 */
class ET_Builder_Theme_Options_Library {

	/**
	 * Instance of `ET_Builder_Theme_Options_Library`.
	 *
	 * @var ET_Builder_Theme_Options_Library
	 */
	private static $_instance;

	/**
	 * Instance of`ET_Core_Data_Utils`.
	 *
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * List of i18n strings.
	 *
	 * @var mixed[]
	 */
	protected static $_i18n;

	/**
	 * ET_Builder_Post_Taxonomy_LayoutCategory instance.
	 *
	 * Shall be used for querying `et_theme_options` taxonomy.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutCategory
	 */
	public $theme_options_categories;

	/**
	 * ET_Builder_Post_Taxonomy_LayoutTag instance.
	 *
	 * Shall be used for querying `et_theme_options` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutTag
	 */
	public $theme_options_tags;

	/**
	 * ET_Builder_Post_Type_TBItem instance.
	 *
	 * Shall be used for querying `et_tb_item` posts .
	 *
	 * @var ET_Builder_Post_Type_TBItem
	 */
	public $theme_options;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->_instance_check();
		$this->_register_cpt_and_taxonomies();
	}

	/**
	 * Dies if an instance already exists.
	 */
	protected function _instance_check() {
		if ( self::$_instance ) {
			et_error( 'Multiple instances are not allowed!' );
			wp_die();
		}
	}

	/**
	 * Registers the Theme Options Library's custom post type and its taxonomies.
	 */
	protected function _register_cpt_and_taxonomies() {
		$files = [
			ET_THEME_OPTIONS_DIR . 'post/type/ThemeOptions.php',
		];

		if ( ! $files ) {
			return;
		}

		foreach ( $files as $file ) {
			require_once $file;
		}

		$this->theme_options            = ET_Post_Type_Theme_Options::instance();
		$this->theme_options_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$this->theme_options_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();

		// We manually call register_all() now to ensure the CPT and taxonomies are registered
		// at exactly the same point during the request that they were in prior releases.
		ET_Builder_Post_Type_Layout::register_all( 'builder' );
	}

	/**
	 * Returns the ET_Builder_Theme_Options_Library instance.
	 *
	 * @return ET_Builder_Theme_Options_Library
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

}

ET_Builder_Theme_Options_Library::instance();

<?php
/**
 * Code Snippets library.
 *
 * Registers post types to be used in the "Code Snippets" library.
 *
 * @link https://github.com/elegantthemes/Divi/issues/26232
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.19.0
 */

/**
 * Core class used to implement "Code Snippets" library.
 *
 * Register post types & taxonomies to be used in "Code Snippets" library.
 */
class ET_Builder_Code_Snippets_Library {

	/**
	 * Instance of `ET_Builder_Code_Snippets_Library`.
	 *
	 * @var ET_Builder_Code_Snippets_Library
	 */
	private static $_instance;

	/**
	 * Instance of  `ET_Core_Data_Utils`.
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
	 * Shall be used for querying `et_code_snippet` taxonomy.
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutCategory
	 */
	public $code_snippet_categories;

	/**
	 * ET_Builder_Post_Taxonomy_LayoutTag instance.
	 *
	 * Shall be used for querying `et_code_snippet` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutTag
	 */
	public $code_snippet_tags;

	/**
	 * ET_Builder_Post_Taxonomy_CodeSnippetType instance.
	 *
	 * Shall be used for querying `et_code_snippet` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_CodeSnippetType
	 */
	public $code_snippet_types;

	/**
	 * ET_Builder_Post_Type_TBItem instance.
	 *
	 * Shall be used for querying `et_tb_item` posts .
	 *
	 * @var ET_Builder_Post_Type_TBItem
	 */
	public $code_snippets;

	/**
	 * ET_Builder_Post_Taxonomy_LayoutCategory instance.
	 *
	 * Shall be used for querying `et_tb_layout_category` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutCategory
	 */
	public $code_snippets_categories;

	/**
	 * ET_Builder_Post_Taxonomy_LayoutTag instance.
	 *
	 * Shall be used for querying `et_tb_layout_tag` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_LayoutTag
	 */
	public $code_snippets_tags;

	/**
	 * ET_Builder_Post_Taxonomy_CodeSnippetType instance.
	 *
	 * Shall be used for querying `et_tb_layout_type` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_CodeSnippetType
	 */
	public $code_snippets_type;

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
	 * Registers the Theme Builder Library's custom post type and its taxonomies.
	 */
	protected function _register_cpt_and_taxonomies() {
		$files = [
			ET_CODE_SNIPPETS_DIR . 'post/type/CodeSnippet.php',
			ET_CODE_SNIPPETS_DIR . 'post/taxonomy/CodeSnippetType.php',
		];

		if ( ! $files ) {
			return;
		}

		foreach ( $files as $file ) {
			require_once $file;
		}

		$this->code_snippets            = ET_Builder_Post_Type_Code_Snippet::instance();
		$this->code_snippets_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$this->code_snippets_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();
		$this->code_snippets_type       = ET_Builder_Post_Taxonomy_CodeSnippetType::instance();

		// We manually call register_all() now to ensure the CPT and taxonomies are registered
		// at exactly the same point during the request that they were in prior releases.
		ET_Builder_Post_Type_TBItem::register_all( 'builder' );
	}

	/**
	 * Returns the ET_Builder_TBItem_Library instance.
	 *
	 * @return ET_Builder_TBItem_Library
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

}

ET_Builder_Code_Snippets_Library::instance();

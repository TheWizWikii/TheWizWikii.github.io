<?php
/**
 * Divi Theme Builder Item Library.
 *
 * @since 4.18.0
 *
 * @package Builder
 */

/**
 * Core class used to implement TB Item library.
 *
 * Register TB Item post type and its taxonomies.
 */
class ET_Builder_TBItem_Library {

	/**
	 * Instance of `ET_Builder_TBItem_Library`.
	 *
	 * @var ET_Builder_TBItem_Library
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
	 * ET_Builder_Post_Taxonomy_TBItemCategory instance.
	 *
	 * Shall be used for querying `et_tb_item` taxonomy.
	 *
	 * @var ET_Builder_Post_Taxonomy_TBItemCategory
	 */
	public $item_categories;

	/**
	 * ET_Builder_Post_Taxonomy_TBItemTag instance.
	 *
	 * Shall be used for querying `et_tb_item` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_TBItemTag
	 */
	public $item_tags;

	/**
	 * ET_Builder_Post_Taxonomy_TBItemType instance.
	 *
	 * Shall be used for querying `et_tb_item` taxonomy .
	 *
	 * @var ET_Builder_Post_Taxonomy_TBItemType
	 */
	public $item_types;

	/**
	 * ET_Builder_Post_Type_TBItem instance.
	 *
	 * Shall be used for querying `et_tb_item` posts .
	 *
	 * @var ET_Builder_Post_Type_TBItem
	 */
	public $items;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->_instance_check();
		$this->_register_cpt_and_taxonomies();

		self::$_ = ET_Core_Data_Utils::instance();

		$root_directory = defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ? ET_BUILDER_PLUGIN_DIR : get_template_directory();

		self::$_i18n = require $root_directory . '/cloud/i18n/library.php';
	}

	/**
	 * Gets a translated string from {@see self::$_i18n}.
	 *
	 * @param string $string The untranslated string.
	 * @param string $path   Optional path for nested strings.
	 *
	 * @return string The translated string if found, the original string otherwise.
	 */
	public static function __( $string, $path = '' ) {
		$path .= $path ? ".{$string}" : $string;

		return self::$_->array_get( self::$_i18n, $path, $string );
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
			ET_THEME_BUILDER_DIR . 'post/type/TBItem.php',
			ET_THEME_BUILDER_DIR . 'post/taxonomy/TBItemType.php',
			ET_THEME_BUILDER_DIR . 'post/query/TBItems.php',
			ET_BUILDER_DIR . 'post/type/Layout.php',
			ET_BUILDER_DIR . 'post/taxonomy/LayoutCategory.php',
			ET_BUILDER_DIR . 'post/taxonomy/LayoutTag.php',
			ET_BUILDER_DIR . 'post/query/Layouts.php',
		];

		if ( ! $files ) {
			return;
		}

		foreach ( $files as $file ) {
			require_once $file;
		}

		$this->items           = ET_Builder_Post_Type_TBItem::instance();
		$this->item_categories = ET_Builder_Post_Taxonomy_LayoutCategory::instance();
		$this->item_tags       = ET_Builder_Post_Taxonomy_LayoutTag::instance();
		$this->item_types      = ET_Builder_Post_Taxonomy_TBItemType::instance();

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

ET_Builder_TBItem_Library::instance();

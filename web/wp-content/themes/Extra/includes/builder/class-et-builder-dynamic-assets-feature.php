<?php
/**
 * Dynamic_Assets feature class.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.0
 */

/**
 * Handles Dynamic_Assets feature.
 *
 * @since 4.10.0
 */
class ET_Builder_Dynamic_Assets_Feature extends ET_Builder_Global_Feature_Base {

	/**
	 * Hold the class instance.
	 *
	 * @var null
	 */
	private static $_instance = null;

	const CACHE_META_KEY = '_et_builder_da_feature_cache';

	/**
	 * Initialize ET_Builder_Dynamic_Assets_Feature class.
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

<?php
/**
 * Content Retriever is home for functions which return non-ajax WordPress Content.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Feature\ContentRetriever;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load traits, No autoloader yet :sad_pepe:
 */
require_once __DIR__ . '/retrievers/PageContentRetriever.php';

/**
 * Class ET_Builder_Content_Retriever
 */
class ET_Builder_Content_Retriever {

	/**
	 * Import traits dependencies.
	 * Keep the code clean and the logic separated.
	 */
	use PageContentRetriever;

	/**
	 * Holds the class instance.
	 *
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Initialize ET_Builder_Content_Retriever class.
	 */
	public static function init() {
		if ( null === self::$_instance ) {
			self::$_instance = new ET_Builder_Content_Retriever();
		}

		return self::$_instance;
	}

}

ET_Builder_Content_Retriever::init();

<?php
/**
 * Code snippets quick feature entry file.
 *
 * Divi Cloud Code Snippets
 *
 * @link https://github.com/elegantthemes/Divi/issues/26232
 *
 * @package Divi
 * @subpackage Core
 * @since 4.19.0
 */

if ( ! defined( 'ET_CODE_SNIPPETS_DIR' ) ) {
	define( 'ET_CODE_SNIPPETS_DIR', ET_CORE_PATH . 'code-snippets/' );
}

require_once trailingslashit( ET_CODE_SNIPPETS_DIR ) . 'constants.php';
require_once trailingslashit( ET_CODE_SNIPPETS_DIR ) . 'code-snippets-library.php';
require_once trailingslashit( ET_CODE_SNIPPETS_DIR ) . 'api.php';

if ( ! function_exists( 'et_init_code_snippets_library' ) ) :
	/**
	 * Init Code Snippets Library.
	 *
	 * Class `ET_Builder_Post_Taxonomy_LayoutCategory` must be initalized
	 * before `ET_Builder_Code_Snippets_Library` because of the internal dependency.
	 *
	 * Since `ET_Builder_Post_Taxonomy_LayoutCategory is initialized using
	 * `add_action( 'init', 'et_setup_builder', 0 );`,
	 *
	 * We initialize `ET_Builder_Code_Snippets_Library` using
	 * `add_action( 'init', 'et_init_code_snippets_library', 10 );`
	 *
	 * @return void
	 */
	function et_init_code_snippets_library() {
		require_once trailingslashit( ET_CODE_SNIPPETS_DIR ) . 'CodeSnippetsLibrary.php';
	}
endif;

add_action( 'init', 'et_init_code_snippets_library' );

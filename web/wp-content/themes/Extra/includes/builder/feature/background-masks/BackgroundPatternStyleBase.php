<?php
/**
 * Abstract Class for Pattern Style.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * ET_Builder_Background_Pattern_Style_Base.
 */
abstract class ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	abstract public function settings();
}

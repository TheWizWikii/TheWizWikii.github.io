<?php
/**
 * Abstract Class for Mask Style.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * ET_Builder_Background_Mask_Style_Base.
 *
 * @since 4.15.0
 */
abstract class ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	abstract public function settings();
}

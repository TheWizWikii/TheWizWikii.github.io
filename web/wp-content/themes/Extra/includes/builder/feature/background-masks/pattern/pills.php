<?php
/**
 * Background Pattern Style - Pills.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Pills
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Pills extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Pills', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M27,180a13,13,0,0,1-26,0V60a13,13,0,0,1,26,0ZM55,0V60a13,13,0,0,1-26,0V0H0V240H29V180a13,13,0,0,1,26,0v60h1V0Z"/>',
				'default-inverted' => '<path d="M14,193A13,13,0,0,1,1,180V60a13,13,0,0,1,26,0V180A13,13,0,0,1,14,193ZM55,60V0H29V60a13,13,0,0,0,26,0Zm0,180V180a13,13,0,0,0-26,0v60Z"/>',
				'rotated'          => '<path d="M180,29a13,13,0,0,1,0,26H60a13,13,0,0,1,0-26ZM0,1H60a13,13,0,0,1,0,26H0V56H240V27H180a13,13,0,0,1,0-26h60V0H0Z"/>',
				'rotated-inverted' => '<path d="M193,42a13,13,0,0,1-13,13H60a13,13,0,0,1,0-26H180A13,13,0,0,1,193,42ZM60,1H0V27H60A13,13,0,0,0,60,1ZM240,1H180a13,13,0,0,0,0,26h60Z"/>',
				'thumbnail'        => '<path d="M79,0V15s-.63,3.25-2.33,3.25A2.64,2.64,0,0,1,74,15V0H66V15a3,3,0,1,1-6,0V0H53V15a3,3,0,1,1-6,0V0H40V15a3,3,0,1,1-6,0V0H26V15a3.14,3.14,0,0,1-3,3.25A3.14,3.14,0,0,1,20,15V0H13V15a3.14,3.14,0,0,1-3,3.25A3.14,3.14,0,0,1,7,15V0H0V60H7V45a3.14,3.14,0,0,1,3-3.25A3.14,3.14,0,0,1,13,45V60h7V45a3.14,3.14,0,0,1,3-3.25A3.14,3.14,0,0,1,26,45V60h8V45a3,3,0,1,1,6,0V60h7V45a3,3,0,1,1,6,0V60h7V45a3,3,0,1,1,6,0V60h8V45a2.64,2.64,0,0,1,2.67-3.25C78.37,41.75,79,45,79,45V60h1V0ZM6,44.54a3.08,3.08,0,0,1-3,3.15,3.08,3.08,0,0,1-3-3.15V15.46a3.08,3.08,0,0,1,3-3.15,3.08,3.08,0,0,1,3,3.15Zm14,0a3,3,0,1,1-6,0V15.46a3,3,0,1,1,6,0Zm13,0a3,3,0,1,1-6,0V15.46a3,3,0,1,1,6,0Zm13,0a3,3,0,1,1-6,0V15.46a3,3,0,1,1,6,0Zm14,0a3,3,0,1,1-6,0V15.46a3,3,0,1,1,6,0Zm13,0a3,3,0,1,1-6,0V15.46a3,3,0,1,1,6,0Z"/>',
			),
			'width'      => '56px',
			'height'     => '240px',
		);
	}
}

return new ET_Builder_Pattern_Pills();

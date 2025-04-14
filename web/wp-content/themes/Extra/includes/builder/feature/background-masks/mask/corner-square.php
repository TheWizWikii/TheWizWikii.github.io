<?php
/**
 * Background Mask Style - Corner Square.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Corner_Square
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Corner_Square extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Corner Square', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<path d="M1423.43,1145.79a147.64,147.64,0,0,1-190.28-85.94L832.83,0H0V1440H1920V958.23Z"/>',
					'portrait'  => '<path d="M1423.43,1145.79a147.64,147.64,0,0,1-190.28-85.94L832.83,0H0V2560H1920V958.23Z"/>',
					'square'    => '<path d="M1423.43,1145.79a147.64,147.64,0,0,1-190.28-85.94L832.83,0H0V1920H1920V958.23Z"/>',
				),
				'default-inverted' => array(
					'landscape' => '<path d="M1423.43,1145.79,1920,958.23V0H832.83l400.32,1059.85A147.64,147.64,0,0,0,1423.43,1145.79Z"/>',
					'portrait'  => '<path d="M1423.43,1145.79,1920,958.23V0H832.83l400.32,1059.85A147.64,147.64,0,0,0,1423.43,1145.79Z"/>',
					'square'    => '<path d="M1423.43,1145.79,1920,958.23V0H832.83l400.32,1059.85A147.64,147.64,0,0,0,1423.43,1145.79Z"/>',
				),
				'rotated'          => array(
					'landscape' => '<path d="M1145.79,496.57a147.64,147.64,0,0,1-85.94,190.28L0,1087.17V1440H1920V0H958.23Z"/>',
					'portrait'  => '<path d="M1145.79,496.57a147.64,147.64,0,0,1-85.94,190.28L0,1087.17V2560H1920V0H958.23Z"/>',
					'square'    => '<path d="M1145.79,496.57a147.64,147.64,0,0,1-85.94,190.28L0,1087.17V1920H1920V0H958.23Z"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<path d="M1145.79,496.57a147.64,147.64,0,0,1-85.94,190.28L0,1087.17V0H958.23Z"/>',
					'portrait'  => '<path d="M1145.79,496.57a147.64,147.64,0,0,1-85.94,190.28L0,1087.17V0H958.23Z"/>',
					'square'    => '<path d="M1145.79,496.57a147.64,147.64,0,0,1-85.94,190.28L0,1087.17V0H958.23Z"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Corner_Square();

<?php
/**
 * Background Mask Style - Wave.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Wave
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Wave extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Wave', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<path d="M0,1440H1920V0H0ZM1734.14,50H1830a40,40,0,0,1,40,40V1350a40,40,0,0,1-40,40H405.31C1070.07,1390,1069.38,50,1734.14,50Z"/>',
					'portrait'  => '<path d="M0,0V2560H1920V0ZM1870,2470a40,40,0,0,1-40,40H434.48C1086,2510,1085.32,50,1736.85,50H1830a40,40,0,0,1,40,40Z"/>',
					'square'    => '<path d="M0,1920H1920V0H0ZM1737.77,50H1830a40,40,0,0,1,40,40V1830a40,40,0,0,1-40,40H444.47C1091.46,1870,1090.78,50,1737.77,50Z"/>',
				),
				'default-inverted' => array(
					'landscape' => '<path d="M405.31,1390c664.76,0,664.07-1340,1328.83-1340H1830a40,40,0,0,1,40,40V1350a40,40,0,0,1-40,40Z"/>',
					'portrait'  => '<path d="M434.48,2510C1086,2510,1085.32,50,1736.85,50H1830a40,40,0,0,1,40,40V2470a40,40,0,0,1-40,40Z"/>',
					'square'    => '<path d="M444.47,1870c647,0,646.31-1820,1293.3-1820H1830a40,40,0,0,1,40,40V1830a40,40,0,0,1-40,40Z"/>',
				),
				'rotated'          => array(
					'landscape' => '<path d="M1920,1440V0H0V1440ZM50,182.23V90A40,40,0,0,1,90,50H1830a40,40,0,0,1,40,40V1187C1870,540,50,829.22,50,182.23Z"/>',
					'portrait'  => '<path d="M1920,2560V0H0V2560ZM50,182.23V90A40,40,0,0,1,90,50H1830a40,40,0,0,1,40,40V1475.53C1870,828.54,50,829.22,50,182.23Z"/>',
					'square'    => '<path d="M1920,1920V0H0V1920ZM50,182.23V90A40,40,0,0,1,90,50H1830a40,40,0,0,1,40,40V1475.53C1870,828.54,50,829.22,50,182.23Z"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<path d="M50,182.23V90A40,40,0,0,1,90,50H1830a40,40,0,0,1,40,40V1187C1870,540,50,829.22,50,182.23Z"/>',
					'portrait'  => '<path d="M50,182.23V90A40,40,0,0,1,90,50H1830a40,40,0,0,1,40,40V1475.53C1870,828.54,50,829.22,50,182.23Z"/>',
					'square'    => '<path d="M50,182.23V90A40,40,0,0,1,90,50H1830a40,40,0,0,1,40,40V1475.53C1870,828.54,50,829.22,50,182.23Z"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Wave();

<?php
/**
 * Background Mask Style - Ellipse.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Ellipse
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Ellipse extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Ellipse', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<path d="M0,1440H1920V0H0ZM870.4,382.19c386.66-102.56,740.21-34.45,789.7,152.12s-223.85,421-610.5,523.5-740.21,34.45-789.7-152.12S483.75,484.74,870.4,382.19Z"/>',
					'portrait'  => '<path d="M0,0V2560H1920V0ZM1049.6,1617.81c-386.66,102.56-740.21,34.45-789.7-152.12s223.85-420.95,610.5-523.5,740.21-34.45,789.7,152.12S1436.25,1515.26,1049.6,1617.81Z"/>',
					'square'    => '<path d="M0,1920H1920V0H0ZM870.4,622.19c386.66-102.56,740.21-34.45,789.7,152.12s-223.85,421-610.5,523.5-740.21,34.45-789.7-152.12S483.75,724.74,870.4,622.19Z"/>',
				),
				'default-inverted' => array(
					'landscape' => '<ellipse cx="960" cy="720" rx="724.3" ry="349.49" transform="matrix(0.97, -0.26, 0.26, 0.97, -152.5, 270.17)"/>',
					'portrait'  => '<ellipse cx="960" cy="1280" rx="724.3" ry="349.49" transform="translate(-296.06 288.89) rotate(-14.85)"/>',
					'square'    => '<ellipse cx="960" cy="960" rx="724.3" ry="349.49" transform="translate(-214.03 278.19) rotate(-14.85)"/>',
				),
				'rotated'          => array(
					'landscape' => '<path d="M1920,1440V0H0V1440ZM689.75,791.68c-82-309.32-27.55-592.17,121.7-631.76s336.76,179.08,418.8,488.4,27.55,592.17-121.7,631.76S771.79,1101,689.75,791.68Z"/>',
					'portrait'  => '<path d="M0,2560H1920V0H0ZM1297.81,1190.4c102.56,386.66,34.45,740.21-152.12,789.7s-420.95-223.85-523.5-610.5S587.74,629.39,774.31,579.9,1195.26,803.75,1297.81,1190.4Z"/>',
					'square'    => '<path d="M1920,1920V0H0V1920ZM622.19,1049.6C519.63,662.94,587.74,309.39,774.31,259.9s421,223.85,523.5,610.5,34.45,740.21-152.12,789.7S724.74,1436.25,622.19,1049.6Z"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<ellipse cx="960" cy="720" rx="279.6" ry="579.44" transform="translate(-152.5 270.17) rotate(-14.85)"/>',
					'portrait'  => '<ellipse cx="960" cy="1280" rx="349.49" ry="724.3" transform="translate(-296.06 288.89) rotate(-14.85)"/>',
					'square'    => '<ellipse cx="960" cy="960" rx="349.49" ry="724.3" transform="matrix(0.97, -0.26, 0.26, 0.97, -214.03, 278.19)"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Ellipse();

<?php
/**
 * Background Mask Style - Diagonal Lines.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Diagonal_Bars
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Diagonal_Bars extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Diagonal Lines', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<polygon points="0 0 0 1440 1431.18 1440 955.79 0 0 0"/>
									<polygon points="1291.92 0 1767.32 1440 1816.13 1440 1340.73 0 1291.92 0"/>
									<polygon points="1725.68 0 1676.87 0 1920 736.45 1920 588.61 1725.68 0"/>',
					'portrait'  => '<polygon points="0 0 0 2560 1801.04 2560 955.79 0 0 0"/>
									<polygon points="1920 1754.63 1340.73 0 1291.92 0 1920 1902.48 1920 1754.63"/>
									<polygon points="1920 588.61 1725.68 0 1676.87 0 1920 736.45 1920 588.61"/>',
					'square'    => '<polygon points="1725.68 0 1676.87 0 1920 736.45 1920 588.61 1725.68 0"/>
									<polygon points="1291.92 0 1920 1902.48 1920 1754.63 1340.73 0 1291.92 0"/>
									<polygon points="0 0 0 1920 1589.65 1920 955.79 0 0 0"/>',
				),
				'default-inverted' => array(
					'landscape' => '<polygon points="955.79 0 1431.18 1440 1767.32 1440 1291.92 0 955.79 0"/>
									<polygon points="1340.73 0 1816.13 1440 1920 1440 1920 736.45 1676.87 0 1340.73 0"/>
									<polygon points="1920 0 1725.68 0 1920 588.61 1920 0"/>',
					'portrait'  => '<polygon points="955.79 0 1801 2560 1920 2560 1920 1902.48 1291.92 0 955.79 0"/>
									<polygon points="1340.73 0 1920 1754.63 1920 736.45 1676.87 0 1340.73 0"/>
									<polygon points="1920 0 1725.68 0 1920 588.61 1920 0"/>',
					'square'    => '<polygon points="955.79 0 1589.65 1920 1920 1920 1920 1902.48 1291.92 0 955.79 0"/>
									<polygon points="1340.73 0 1920 1754.63 1920 736.45 1676.87 0 1340.73 0"/>
									<polygon points="1920 0 1725.68 0 1920 588.61 1920 0"/>',
				),
				'rotated'          => array(
					'landscape' => '<path d="M588.61,0H736.45L0,243.13V194.32ZM0,628.08,1902.48,0H1754.63L0,579.27ZM0,1440H1920V330.35L0,964.21Z"/>',
					'portrait'  => '<path d="M588.61,0H736.45L0,243.13V194.32ZM0,628.08,1902.48,0H1754.63L0,579.27ZM0,2560H1920V330.35L0,964.21Z"/>',
					'square'    => '<path d="M588.61,0H736.45L0,243.13V194.32ZM0,628.08,1902.48,0H1754.63L0,579.27ZM0,1920H1920V330.35L0,964.21Z"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<path d="M0,628.08,1902.48,0H1920V330.35L0,964.21Zm0-48.81L1754.63,0H736.45L0,243.13ZM0,0V194.32L588.61,0Z"/>',
					'portrait'  => '<path d="M0,628.08,1902.48,0H1920V330.35L0,964.21Zm0-48.81L1754.63,0H736.45L0,243.13ZM0,0V194.32L588.61,0Z"/>',
					'square'    => '<path d="M0,628.08,1902.48,0H1920V330.35L0,964.21Zm0-48.81L1754.63,0H736.45L0,243.13ZM0,0V194.32L588.61,0Z"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Diagonal_Bars();

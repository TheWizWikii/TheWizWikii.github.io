<?php
/**
 * Background Mask Style - Triangles.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Triangles
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Triangles extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Triangles', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<polygon points="1920 98.49 1568 776.05 1164.83 0 0 0 0 1440 1331.44 1440 1920 307.07 1920 98.49"/>',
					'portrait'  => '<polygon points="1920 540.23 1586.65 1181.89 972.65 0 0 0 0 2560 997.76 2560 1920 784.79 1920 540.23"/>',
					'square'    => '<polygon points="1920 230.67 1484.67 1068.63 929.51 0 0 0 0 1920 1150.9 1920 1920 439.56 1920 230.67"/>',
				),
				'default-inverted' => array(
					'landscape' => '<path d="M1568,776.06,1164.83,0H1920V98.49Zm352-469L1331.44,1440H1920Z"/>',
					'portrait'  => '<path d="M1586.65,1181.9,972.65,0H1920V540.23ZM1920,784.79,997.77,2560H1920Z"/>',
					'square'    => '<path d="M1484.67,1068.63,929.51,0H1920V230.67ZM1920,439.56,1150.9,1920H1920Z"/>',
				),
				'rotated'          => array(
					'landscape' => '<polygon points="230.67 0 1068.63 435.33 0 990.49 0 1440 1920 1440 1920 769.1 439.56 0 230.67 0"/>',
					'portrait'  => '<polygon points="230.67 0 1068.63 435.33 0 990.49 0 2560 1920 2560 1920 769.1 439.56 0 230.67 0"/>',
					'square'    => '<polygon points="230.67 0 1068.63 435.33 0 990.49 0 1920 1920 1920 1920 769.1 439.56 0 230.67 0"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<path d="M1068.63,435.33,0,990.49V0H230.67ZM439.56,0,1920,769.1V0Z"/>',
					'portrait'  => '<path d="M1068.63,435.33,0,990.49V0H230.67ZM439.56,0,1920,769.1V0Z"/>',
					'square'    => '<path d="M1068.63,435.33,0,990.49V0H230.67ZM439.56,0,1920,769.1V0Z"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Triangles();

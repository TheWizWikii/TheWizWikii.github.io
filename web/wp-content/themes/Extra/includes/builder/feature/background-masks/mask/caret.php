<?php
/**
 * Background Mask Style - Caret.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Caret
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Caret extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Caret', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<polygon points="1241.5 0 0 0 0 1440 1241.82 1440 1016 719.5 1241.5 0"/>',
					'portrait'  => '<polygon points="1327.2 0 0 0 0 2560 1327.76 2560 926.3 1279.11 1327.2 0"/>',
					'square'    => '<polygon points="1241.4 0 0 0 0 1920 1241.82 1920 940.73 959.33 1241.4 0"/>',
				),
				'default-inverted' => array(
					'landscape' => '<polygon points="1920 0 1241.5 0 1016 719.5 1241.82 1440 1920 1440 1920 0"/>',
					'portrait'  => '<polygon points="1327.2 0 1920 0 1920 2560 1327.76 2560 926.3 1279.11 1327.2 0"/>',
					'square'    => '<polygon points="1241.4 0 1920 0 1920 1920 1241.82 1920 940.73 959.33 1241.4 0"/>',
				),
				'rotated'          => array(
					'landscape' => '<polygon points="0 428.6 0 1440 1920 1440 1920 428.18 959.33 729.27 0 428.6"/>',
					'portrait'  => '<polygon points="0 991.6 0 2560 1920 2560 1920 991.18 959.33 1292.27 0 991.6"/>',
					'square'    => '<polygon points="0 669.6 0 1920 1920 1920 1920 669.18 959.33 970.27 0 669.6"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<polygon points="0 428.6 0 0 1920 0 1920 428.18 959.33 729.27 0 428.6"/>',
					'portrait'  => '<polygon points="0 991.6 0 0 1920 0 1920 991.18 959.33 1292.27 0 991.6"/>',
					'square'    => '<polygon points="0 669.6 0 0 1920 0 1920 669.18 959.33 970.27 0 669.6"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Caret();

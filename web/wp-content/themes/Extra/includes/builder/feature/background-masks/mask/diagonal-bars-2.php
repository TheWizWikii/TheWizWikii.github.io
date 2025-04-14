<?php
/**
 * Background Mask Style - Diagonal Bars 2.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_Diagonal_Bars_2
 *
 * @since 4.15.0
 */
class ET_Builder_Mask_Diagonal_Bars_2 extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Diagonal Bars 2', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '<polygon points="390 803.5 390 389.7 779.86 0 558 0 390.5 167.5 390.5 0 0 0 0 1440 1920 1440 1920 0 1829 0 389.5 1439.5 389.5 1025.7 1415.61 0 1193.5 0 390 803.5"/>',
					'portrait'  => '<polygon points="390 1284.5 390 870.7 1261.05 0 1038.5 0 390 648.5 390 234.7 624.8 0 0 0 0 2560 1920 2560 1920 1026 389.5 2556.5 389.5 2142.7 1920 612.81 1920 390.5 390 1920.5 390 1506.7 1897.3 0 1674.5 0 390 1284.5"/>',
					'square'    => '<polygon points="1920 1920 1920 390 390 1920 1920 1920"/>
									<polygon points="390 1284.5 390 870.7 1261.05 0 1039 0 390.5 648.5 390.5 234.7 625.3 0 0 0 0 1920 389.5 1920 389.5 1506.7 1896.8 0 1674.5 0 390 1284.5"/>',
				),
				'default-inverted' => array(
					'landscape' => '<polygon points="389.5 1439.5 1829 0 1415.61 0 389.5 1025.7 389.5 1439.5"/>
									<polygon points="390 803.5 1193.5 0 779.86 0 390 389.7 390 803.5"/>
									<polygon points="558 0 390.5 0 390.5 167.5 558 0"/>',
					'portrait'  => '<polygon points="389.5 2556.5 1920 1026 1920 612.81 389.5 2142.7 389.5 2556.5"/>
									<polygon points="390 1920.5 1920 390.5 1920 0 1897.3 0 390 1506.7 390 1920.5"/>
									<polygon points="390 1284.5 1674.5 0 1261.05 0 390 870.7 390 1284.5"/>
									<polygon points="390 648.5 1038.5 0 624.8 0 390 234.7 390 648.5"/>',
					'square'    => '<polygon points="389.5 1920 390 1920 1920 390 1920 0 1896.8 0 389.5 1506.7 389.5 1920"/>
									<polygon points="390 1284.5 1674.5 0 1261.05 0 390 870.7 390 1284.5"/>
									<polygon points="390.5 648.5 1039 0 625.3 0 390.5 234.7 390.5 648.5"/>',
				),
				'rotated'          => array(
					'landscape' => '<path d="M234.5,0H456.62L1506.7,1050.5H1920V1440H0V814.7l234.7,234.8H648.5L0,401V179L870.7,1050h413.8ZM1920,0H870L1920,1050Z"/>',
					'portrait'  => '<path d="M1920.5,1530.5,390,0H1920Zm2.5,638H1509.2L0,659V881L1287,2168H873.2L0,1294.7V2560H1920l.1-616.1L0,23.2V245.5Z"/>',
					'square'    => '<path d="M1920,1530,390,0H1920Zm-635.5,0H870.7L0,659V881l648.5,648.5H234.7L0,1294.7V1920H1920V1530.5H1506.7L0,23.2V245.5Z"/>',
				),
				'rotated-inverted' => array(
					'landscape' => '<path d="M234.7,1049.5,0,814.7V401l648.5,648.5ZM456.62,0,1506.7,1050.5H1920v-.5L870,0ZM1284.5,1050,234.5,0H0V179L870.7,1050Z"/>',
					'portrait'  => '<path d="M1920,2165.5l.06.06v2.94H1920Zm-633,2.5L0,881v413.7L873.2,2168Zm633-638L390,0H0V23.2L1920,1943.8Zm0,638.5v-3L0,245.5V659L1509.2,2168.5Z"/>',
					'square'    => '<path d="M234.7,1529.5,0,1294.7V881l648.5,648.5Zm1049.8.5L0,245.5V659L870.7,1530ZM0,0V23.2L1506.7,1530.5H1920v-.5L390,0Z"/>',
				),
			),
		);
	}
}

return new ET_Builder_Mask_Diagonal_Bars_2();

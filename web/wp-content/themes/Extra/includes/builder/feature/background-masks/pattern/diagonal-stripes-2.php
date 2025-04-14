<?php
/**
 * Background Pattern Style - Diagonal Stripes 2.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Diagonal_Srtipes_2
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Diagonal_Srtipes_2 extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Diagonal Stripes 2', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M1.41,0,0,1.41V0ZM11,0H9.59L0,9.59V11H1.41L11,1.41Zm0,9.59L9.59,11H11Z"/>',
				'default-inverted' => '<path d="M9.59,0,0,9.59V1.41L1.41,0ZM11,1.41,1.41,11H9.59L11,9.59Z"/>',
				'rotated'          => '<path d="M0,9.59,1.41,11H0ZM0,0V1.41L9.59,11H11V9.59L1.41,0ZM9.59,0,11,1.41V0Z"/>',
				'rotated-inverted' => '<path d="M0,1.41,9.59,11H1.41L0,9.59ZM1.41,0,11,9.59V1.41L9.59,0Z"/>',
				'thumbnail'        => '<path d="M.86,0,0,1V0ZM6.67,1l.85-1H5.81L0,6.54V8.46ZM79.14,0,25.81,60h1.71L80,1V0ZM6.67,8.46,14.19,0H12.48L0,14V16ZM73.33,14,32.48,60h1.71L80,8.46V6.54ZM6.67,16,20.86,0H19.14L0,21.54v1.92Zm66.66,5.58L39.14,60h1.72L80,16V14ZM6.67,23.46,27.52,0H25.81L0,29V31ZM73.33,29,45.81,60h1.71L80,23.46V21.54ZM6.67,31,34.19,0H32.48L0,36.54v1.92Zm66.66,5.58L52.48,60h1.71L80,31V29ZM6.67,38.46,40.86,0H39.14L0,44V46ZM73.33,44,59.14,60h1.72L80,38.46V36.54ZM6.67,46,47.52,0H45.81L0,51.54v1.92Zm66.66,5.58L65.81,60h1.71L80,46V44Zm-65.81,1L54.19,0H52.48L0,59v1H.86Zm6.67,0L60.86,0H59.14L5.81,60H7.52Zm6.67,0L67.52,0H65.81L12.48,60h1.71Zm6.66,0L74.19,0H72.48L19.14,60h1.72ZM73.33,59l-.85,1h1.71L80,53.46V51.54ZM80,60V59l-.86,1Z"/>',
			),
			'width'      => '11px',
			'height'     => '11px',
		);
	}
}

return new ET_Builder_Pattern_Diagonal_Srtipes_2();

<?php
/**
 * Background Pattern Style - Diamonds.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Diamonds
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Diamonds extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Diamonds', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M20,0,0,32V0ZM40,0H20L40,32ZM0,64H20L0,32ZM40,32,20,64H40Z"/>',
				'default-inverted' => '<polygon points="20 0 0 32 20 64 40 32 20 0"/>',
				'rotated'          => '<path d="M0,20,32,40H0ZM0,0V20L32,0ZM64,40V20L32,40ZM32,0,64,20V0Z"/>',
				'rotated-inverted' => '<polygon points="0 20 32 40 64 20 32 0 0 20"/>',
				'thumbnail'        => '<path d="M13.33,10,6.67,20,0,10,6.67,0ZM20,0,13.33,10,20,20l6.67-10ZM33.33,0,26.67,10l6.66,10L40,10ZM46.67,0,40,10l6.67,10,6.66-10ZM60,0,53.33,10,60,20l6.67-10ZM73.33,0,66.67,10l6.66,10L80,10ZM6.67,20,0,30,6.67,40l6.66-10ZM20,20,13.33,30,20,40l6.67-10Zm13.33,0L26.67,30l6.66,10L40,30Zm13.34,0L40,30l6.67,10,6.66-10ZM60,20,53.33,30,60,40l6.67-10Zm13.33,0L66.67,30l6.66,10L80,30ZM6.67,40,0,50,6.67,60l6.66-10ZM20,40,13.33,50,20,60l6.67-10Zm13.33,0L26.67,50l6.66,10L40,50Zm13.34,0L40,50l6.67,10,6.66-10ZM60,40,53.33,50,60,60l6.67-10Zm13.33,0L66.67,50l6.66,10L80,50Z"/>',
			),
			'width'      => '40px',
			'height'     => '64px',
		);
	}
}

return new ET_Builder_Pattern_Diamonds();

<?php
/**
 * Background Pattern Style - Diagonal Stripes.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Diagonal_Stripes
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Diagonal_Stripes extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Diagonal Stripes', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M32,0,0,32V0Zm0,64L64,32V0L0,64Z"/>',
				'default-inverted' => '<path d="M32,64,64,32V64ZM32,0,0,32V64L64,0Z"/>',
				'rotated'          => '<path d="M0,32,32,64H0Zm64,0L32,0H0L64,64Z"/>',
				'rotated-inverted' => '<path d="M64,32,32,0H64ZM0,32,32,64H64L0,0Z"/>',
				'thumbnail'        => '<path d="M6.67,0,0,7.5V0Zm6.66,7.5L20,0H13.33L0,15v7.5ZM66.67,15l-40,45h6.66L80,7.5V0ZM13.33,22.5,33.33,0H26.67L0,30v7.5Zm0,15L46.67,0H40L0,45v7.5Zm40,7.5L40,60h6.67L80,22.5V15ZM20,45,60,0H53.33L0,60H6.67Zm13.33,0,40-45H66.67L13.33,60H20Zm20,15H60L80,37.5V30Zm20,0L80,52.5V45L66.67,60Z"/>',
			),
			'width'      => '64px',
			'height'     => '64px',
		);
	}
}

return new ET_Builder_Pattern_Diagonal_Stripes();

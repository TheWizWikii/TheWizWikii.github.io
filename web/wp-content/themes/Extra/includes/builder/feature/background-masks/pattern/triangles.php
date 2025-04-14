<?php
/**
 * Background Pattern Style - Triangles.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Triangles
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Triangles extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Triangles', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M70,120H0L35,60ZM70,0,35,60H70ZM0,0V60H35Z"/>',
				'default-inverted' => '<path d="M0,0H70L35,60ZM0,120,35,60H0Zm70,0V60H35Z"/>',
				'rotated'          => '<path d="M120,0V70L60,35ZM0,0,60,35V0ZM0,70H60V35Z"/>',
				'rotated-inverted' => '<path d="M0,70V0L60,35Zm120,0L60,35V70Zm0-70H60V35Z"/>',
				'thumbnail'        => '<path d="M20,20H0L10,0ZM30,0,20,20H40ZM50,0,40,20H60ZM70,0,60,20H80ZM0,20V40H10Zm20,0L10,40H30Zm20,0L30,40H50Zm20,0L50,40H70Zm20,0L70,40H80ZM10,40,0,60H20Zm20,0L20,60H40Zm20,0L40,60H60Zm20,0L60,60H80Z"/>',
			),
			'width'      => '70px',
			'height'     => '120px',
		);
	}
}

return new ET_Builder_Pattern_Triangles();

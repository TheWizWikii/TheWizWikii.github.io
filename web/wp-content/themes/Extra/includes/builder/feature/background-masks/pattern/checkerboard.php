<?php
/**
 * Background Pattern Style - Checkerboard.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Checkerboard
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Checkerboard extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Checkerboard', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M28,28H56V56H28ZM0,0H28V28H0Z"/>',
				'default-inverted' => '<path d="M28,28V56H0V28ZM56,0V28H28V0Z"/>',
				'rotated'          => '<path d="M28,28V56H0V28ZM56,0V28H28V0Z"/>',
				'rotated-inverted' => '<path d="M28,28H56V56H28ZM0,0H28V28H0Z"/>',
				'thumbnail'        => '<path d="M0,0H10V10H0ZM0,20H10V30H0ZM10,10H20V20H10Zm0,20H20V40H10ZM20,0H30V10H20Zm0,20H30V30H20ZM30,10H40V20H30Zm0,20H40V40H30ZM40,0H50V10H40Zm0,20H50V30H40ZM50,10H60V20H50Zm0,20H60V40H50ZM60,0H70V10H60Zm0,20H70V30H60ZM70,10H80V20H70Zm0,20H80V40H70ZM0,40H10V50H0ZM10,50H20V60H10ZM20,40H30V50H20ZM30,50H40V60H30ZM40,40H50V50H40ZM50,50H60V60H50ZM60,40H70V50H60ZM70,50H80V60H70Z"/>',
			),
			'height'     => '56px',
			'width'      => '56px',
		);
	}
}

return new ET_Builder_Pattern_Checkerboard();

<?php
/**
 * Background Pattern Style - Cubes.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Cubes
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Cubes extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Cubes', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M56,129.33,0,97,56,64.67,112,97ZM56,0H0V32.33Zm56,32.33V0H56ZM56,194h56V161.67ZM0,161.67V194H56Z"/>
										<path fill-opacity=".5" d="M56,0V64.67L0,97V32.33Zm0,129.33V194l56-32.33V97Z"/>',
				'default-inverted' => '<path fill-opacity=".5" d="M112,32.33V97L56,64.67V0ZM0,97v64.67L56,194V129.33Z"/>
										<path d="M0,32.33,56,0V64.66L0,97Zm56,97L112,97v64.67L56,194Z"/>',
				'rotated'          => '<path d="M129.33,56,97,112,64.67,56,97,0ZM0,56v56H32.33ZM32.33,0H0V56ZM194,56V0H161.67Zm-32.33,56H194V56Z"/>
										<path fill-opacity=".5" d="M0,56H64.67L97,112H32.33Zm129.33,0H194L161.67,0H97Z"/>',
				'rotated-inverted' => '<path fill-opacity=".5" d="M32.33,0H97L64.67,56H0ZM97,112h64.67L194,56H129.33Z"/>
										<path d="M32.33,112,0,56H64.66L97,112Zm97-56L97,0h64.67L194,56Z"/>',
				'thumbnail'        => '<path d="M10,36,0,42V30Zm0-24L0,18l10,6,10-6ZM0,0V6L10,0ZM30,0H10L20,6Zm0,24,10-6L30,12,20,18Zm0,12,10,6,10-6L40,30ZM40,18l10,6,10-6L50,12ZM50,36l10,6,10-6L60,30ZM50,0H30L40,6ZM70,24l10-6L70,12,60,18ZM80,0H70L80,6ZM70,0H50L60,6ZM10,48,0,54l10,6,10-6Zm10,6,10,6,10-6L30,48ZM30,36,20,30,10,36l10,6ZM40,54l10,6,10-6L50,48Zm30,6,10-6L70,48,60,54ZM80,42V30L70,36Z"/>
										<path fill-opacity=".5" d="M20,30,10,36V24l10-6ZM0,6V18l10-6V0ZM40,18,30,24V36l10-6ZM20,18l10-6V0L20,6Zm20,0,10-6V0L40,6ZM50,36l10-6V18L50,24Zm20,0,10-6V18L70,24ZM60,6V18l10-6V0ZM20,54,10,60H20ZM0,42V54l10-6V36Zm20,0V54l10-6V36ZM40,54,30,60H40Zm20,0L50,60H60ZM40,54l10-6V36L40,42Zm20,0,10-6V36L60,42Zm20,6V54L70,60Z"/>',
			),
			'width'      => '112px',
			'height'     => '194px',
		);
	}
}

return new ET_Builder_Pattern_Cubes();

<?php
/**
 * Background Pattern Style - Inverted Chevrons 2.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Inverted_Chevrons_2
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Inverted_Chevrons_2 extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Inverted Chevrons 2', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M50,140,0,105V70l50,35Zm0-35,50-35H50Zm50,0L50,140h50ZM50,0l50,35V70L50,35Zm0,35L0,70H50ZM0,35,50,0H0Z"/>',
				'default-inverted' => '<path d="M50,105l50-35v35L50,140Zm0-35H0l50,35ZM0,140H50L0,105ZM50,35,0,70V35L50,0Zm0,35h50L50,35ZM100,0H50l50,35Z"/>',
				'rotated'          => '<path d="M140,50l-35,50H70l35-50Zm-35,0L70,0V50Zm0-50,35,50V0ZM0,50,35,0H70L35,50Zm35,0,35,50V50Zm0,50L0,50v50Z"/>',
				'rotated-inverted' => '<path d="M105,50,70,0h35l35,50ZM70,50v50l35-50Zm70,50V50l-35,50ZM35,50l35,50H35L0,50Zm35,0V0L35,50ZM0,0V50L35,0Z"/>',
				'thumbnail'        => '<path d="M13,40,0,30V20L13,30Zm0-10L26.33,20H13Zm14,0L13,40H27ZM13,.25,27,10V20L13,10ZM13,10-.33,20H13ZM0,10,13,0H0ZM40,40,27,30V20L40,30Zm0-10L53,20H40Zm13,0L40,40H53ZM40,.25,53,10V20L40,10ZM40,10,26.67,20H40ZM27,10,40,0H27ZM67,40,53,30V20L67,30Zm0-10L80.33,20H67Zm13,0L66.67,40H80ZM67,.25,80,10V20L67,10ZM67,10,53,20H67ZM53,10,66.33,0H53ZM13,40,27,50v9.75L13,50Zm0,10L-.33,60H13ZM0,50,13,40H0ZM40,40,53,50v9.75L40,50Zm0,10L26.67,60H40ZM27,50,40,40H27ZM67,40,80,50v9.75L67,50Zm0,10L53,60H67ZM53,50,66.33,40H53Z"/>',
			),
			'width'      => '100px',
			'height'     => '140px',
		);
	}
}

return new ET_Builder_Pattern_Inverted_Chevrons_2();

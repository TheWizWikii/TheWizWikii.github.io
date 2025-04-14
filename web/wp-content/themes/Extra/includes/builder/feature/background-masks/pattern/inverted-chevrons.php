<?php
/**
 * Background Pattern Style - Inverted Chevrons.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Inverted_Chevrons
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Inverted_Chevrons extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Inverted Chevrons', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M50,0V40L0,0Zm0,80L0,40V80Zm50-40V0L50,40V80Z"/>',
				'default-inverted' => '<path d="M100,0,50,40V0Zm0,80V40L50,80ZM50,80V40L0,0V40Z"/>',
				'rotated'          => '<path d="M0,50H40L0,100Zm80,0L40,100H80ZM40,0H0L40,50H80Z"/>',
				'rotated-inverted' => '<path d="M0,0,40,50H0ZM80,0H40L80,50Zm0,50H40L0,100H40Z"/>',
				'thumbnail'        => '<path d="M14,0V10L-.33,0ZM0,10.25V20l14,9.75V20Zm27-10L14,10v9.75L27,10ZM40,0H26.67L40,10ZM53,10V.25L40,10v9.75ZM67,0H53.67L67,10ZM53,10.25V20l14,9.75V20ZM80,10V.25L67,10v9.75ZM0,30.25V40l14,9.75V40Zm27-10L14,30v9.75L27,30ZM40,20,27,10.25V20l13,9.75ZM27,30.25V40l13,9.75V40Zm13,9.5L53,30V20.25L40,30ZM53,40l14,9.75V40L53,30.25ZM80,30V20.25L67,30v9.75ZM0,60H14.33L0,50ZM14,50v9.75L27,50V40.25ZM27,60H40.33L27,50ZM40,50v9.75L53,50V40.25ZM53,60H67.33L53,50ZM80,50V40.25L67,50v9.75Z"/>',
			),
			'width'      => '100px',
			'height'     => '80px',
		);
	}
}

return new ET_Builder_Pattern_Inverted_Chevrons();

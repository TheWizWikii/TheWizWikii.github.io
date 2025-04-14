<?php
/**
 * Background Pattern Style - Zig Zag.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Zig_Zag
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Zig_Zag extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Zig Zag', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<path d="M0,0H56L0,28ZM112,0H56l56,28Zm0,56L56,28,0,56Z"/>',
				'default-inverted' => '<path d="M0,28,56,0l56,28V56L56,28,0,56Z"/>',
				'rotated'          => '<path d="M0,112V56l28,56ZM0,0V56L28,0ZM56,0,28,56l28,56Z"/>',
				'rotated-inverted' => '<path d="M28,112,0,56,28,0H56L28,56l28,56Z"/>',
				'thumbnail'        => '<path d="M24.78,7.7,39.61.29,54.44,7.7,69.27.29,80,5.66V0H0V5.27l10-5Zm29.66,7.42L39.61,7.7,24.78,15.12,10,7.7,0,12.68v7.41l10-5,14.83,7.41,14.83-7.41,14.83,7.41,14.83-7.41L80,20.48V13.07L69.27,7.7Zm0,14.83L39.61,22.53,24.78,30,10,22.53,0,27.51v7.41l10-5,14.83,7.41L39.61,30l14.83,7.41L69.27,30,80,35.31V27.9L69.27,22.53Zm0,14.82L39.61,37.36,24.78,44.77,10,37.36,0,42.34v7.41l10-5,14.83,7.42,14.83-7.42,14.83,7.42,14.83-7.42L80,50.14V42.73L69.27,37.36Zm0,14.83L39.61,52.19,24.78,59.6,10,52.19,0,57.16V60H80V57.55L69.27,52.19Z"/>',
			),
			'width'      => '112px',
			'height'     => '56px',
		);
	}
}

return new ET_Builder_Pattern_Zig_Zag();

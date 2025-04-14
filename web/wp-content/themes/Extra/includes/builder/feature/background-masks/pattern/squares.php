<?php
/**
 * Background Pattern Style - Squares.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Squares
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Squares extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Squares', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<rect width="4" height="4"/>',
				'default-inverted' => '<polygon points="8 8 8 0 4 0 4 4 0 4 0 8 8 8"/>',
				'rotated'          => '<rect y="4" width="4" height="4"/>',
				'rotated-inverted' => '<polygon points="8 0 0 0 0 4 4 4 4 8 8 8 8 0"/>',
				'thumbnail'        => '<path d="M0,0H2V4H0ZM6,0h4V4H6Zm8,0h4V4H14Zm8,0h4V4H22Zm8,0h4V4H30Zm8,0h4V4H38Zm8,0h4V4H46Zm8,0h4V4H54Zm8,0h4V4H62Zm8,0h4V4H70ZM0,8H2v4H0ZM6,8h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70ZM0,16H2v4H0Zm6,0h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70ZM0,24H2v4H0Zm6,0h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70ZM0,32H2v4H0Zm6,0h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70ZM0,40H2v4H0Zm6,0h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70ZM0,48H2v4H0Zm6,0h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70ZM0,56H2v4H0ZM78,0h2V4H78Zm0,8h2v4H78Zm0,8h2v4H78Zm0,8h2v4H78Zm0,8h2v4H78Zm0,8h2v4H78Zm0,8h2v4H78Zm0,8h2v4H78ZM6,56h4v4H6Zm8,0h4v4H14Zm8,0h4v4H22Zm8,0h4v4H30Zm8,0h4v4H38Zm8,0h4v4H46Zm8,0h4v4H54Zm8,0h4v4H62Zm8,0h4v4H70Z"/>',
			),
			'width'      => '8px',
			'height'     => '8px',
		);
	}
}

return new ET_Builder_Pattern_Squares();

<?php
/**
 * Background Pattern Style - Crosses.
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_Crosses
 *
 * @since 4.15.0
 */
class ET_Builder_Pattern_Crosses extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'Crosses', 'et-builder' ),
			'svgContent' => array(
				'default'          => '<polygon points="40 24 32 24 32 32 24 32 24 40 32 40 32 48 40 48 40 40 48 40 48 32 40 32 40 24"/>',
				'default-inverted' => '<path d="M0,0V72H72V0ZM48,40H40v8H32V40H24V32h8V24h8v8h8Z"/>',
				'rotated'          => '<polygon points="40 24 32 24 32 32 24 32 24 40 32 40 32 48 40 48 40 40 48 40 48 32 40 32 40 24"/>',
				'rotated-inverted' => '<path d="M0,0V72H72V0ZM48,40H40v8H32V40H24V32h8V24h8v8h8Z"/>',
				'thumbnail'        => '<path d="M28.89,11.11H26.67V8.89h2.22V6.67h2.22V8.89h2.22v2.22H31.11v2.22H28.89Zm-20,2.22h2.22V11.11h2.22V8.89H11.11V6.67H8.89V8.89H6.67v2.22H8.89Zm2.22,20V31.11h2.22V28.89H11.11V26.67H8.89v2.22H6.67v2.22H8.89v2.22Zm20-6.66H28.89v2.22H26.67v2.22h2.22v2.22h2.22V31.11h2.22V28.89H31.11Zm20,0H48.89v2.22H46.67v2.22h2.22v2.22h2.22V31.11h2.22V28.89H51.11ZM68.89,13.33h2.22V11.11h2.22V8.89H71.11V6.67H68.89V8.89H66.67v2.22h2.22Zm-20,0h2.22V11.11h2.22V8.89H51.11V6.67H48.89V8.89H46.67v2.22h2.22Zm20,13.34v2.22H66.67v2.22h2.22v2.22h2.22V31.11h2.22V28.89H71.11V26.67Zm2.22,20H68.89v2.22H66.67v2.22h2.22v2.22h2.22V51.11h2.22V48.89H71.11Zm-60,0H8.89v2.22H6.67v2.22H8.89v2.22h2.22V51.11h2.22V48.89H11.11Zm20,0H28.89v2.22H26.67v2.22h2.22v2.22h2.22V51.11h2.22V48.89H31.11Zm20,0H48.89v2.22H46.67v2.22h2.22v2.22h2.22V51.11h2.22V48.89H51.11Z"/>',
			),
			'width'      => '72px',
			'height'     => '72px',
		);
	}
}

return new ET_Builder_Pattern_Crosses();

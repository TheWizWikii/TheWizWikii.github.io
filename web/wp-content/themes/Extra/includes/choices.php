<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'et_extra_font_style_choices' ) ) :

	/**
 * Returns font style options
 * @return array
 */
	function et_extra_font_style_choices() {
		return apply_filters( 'et_extra_font_style_choices', array(
			'bold'      => esc_html__( 'Bold', 'extra' ),
			'italic'    => esc_html__( 'Italic', 'extra' ),
			'uppercase' => esc_html__( 'Uppercase', 'extra' ),
			'underline' => esc_html__( 'Underline', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_header_style_choices' ) ) :

	/**
 * Returns list of header styles used by Extra
 * @return array
 */
	function et_extra_header_style_choices() {
		return apply_filters( 'et_extra_header_style_choices', array(
			'left-right' => esc_html__( 'Left/Right', 'extra' ),
			'centered'   => esc_html__( 'Centered', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_dropdown_animation_choices' ) ) :

	/**
 * Returns list of dropdown animation
 * @return array
 */
	function et_extra_dropdown_animation_choices() {
		return apply_filters( 'et_extra_dropdown_animation_choices', array(
			'Default'       => esc_html__( 'Fade In', 'extra' ),
			'fadeInTop'     => esc_html__( 'Fade In From Top', 'extra' ),
			'fadeInRight'   => esc_html__( 'Fade In From Right', 'extra' ),
			'fadeInBottom'  => esc_html__( 'Fade In From Bottom', 'extra' ),
			'fadeInLeft'    => esc_html__( 'Fade In From Left', 'extra' ),
			'scaleInRight'  => esc_html__( 'Scale In From Right', 'extra' ),
			'scaleInLeft'   => esc_html__( 'Scale In From Left', 'extra' ),
			'scaleInCenter' => esc_html__( 'Scale In From Center', 'extra' ),
			'flipInY'       => esc_html__( 'Flip In Horizontally', 'extra' ),
			'flipInX'       => esc_html__( 'Flip In Vertically', 'extra' ),
			'slideInX'      => esc_html__( 'Slide In Vertically', 'extra' ),
			'slideInY'      => esc_html__( 'Slide In Horizontally', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_footer_column_choices' ) ) :

	/**
 * Returns list of footer column choices
 * @return array
 */
	function et_extra_footer_column_choices() {
		return apply_filters( 'et_extra_footer_column_choices', array(
			'4'             => esc_html__( '4 Columns', 'extra' ),
			'3'             => esc_html__( '3 Columns', 'extra' ),
			'2'             => esc_html__( '2 Columns', 'extra' ),
			'1'             => esc_html__( '1 Column', 'extra' ),
			'1_4__3_4'      => esc_html__( '1/4 + 3/4 Columns', 'extra' ),
			'3_4__1_4'      => esc_html__( '3/4 + 1/4 Columns', 'extra' ),
			'1_3__2_3'      => esc_html__( '1/3 + 2/3 Columns', 'extra' ),
			'2_3__1_3'      => esc_html__( '2/3 + 1/3 Columns', 'extra' ),
			'1_4__1_4__1_2' => esc_html__( '1/4 + 1/4 + 1/2 Columns', 'extra' ),
			'1_2__1_4__1_4' => esc_html__( '1/2 + 1/4 + 1/4 Columns', 'extra' ),
			'1_4__1_2__1_4' => esc_html__( '1/4 + 1/2 + 1/4 Columns', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_yes_no_choices' ) ) :

	/**
 * Returns yes no choices
 * @return array
 */
	function et_extra_yes_no_choices() {
		return apply_filters( 'et_extra_yes_no_choices', array(
			'yes' => esc_html__( 'Yes', 'extra' ),
			'no'  => esc_html__( 'No', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_left_right_choices' ) ) :

	/**
 * Returns left or right choices
 * @return array
 */
	function et_extra_left_right_choices() {
		return apply_filters( 'et_extra_left_right_choices', array(
			'right' => esc_html__( 'Right', 'extra' ),
			'left'  => esc_html__( 'Left', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_image_animation_choices' ) ) :

	/**
 * Returns image animation choices
 * @return array
 */
	function et_extra_image_animation_choices() {
		return apply_filters( 'et_extra_image_animation_choices', array(
			'left'    => esc_html__( 'Left to Right', 'extra' ),
			'right'   => esc_html__( 'Right to Left', 'extra' ),
			'top'     => esc_html__( 'Top to Bottom', 'extra' ),
			'bottom'  => esc_html__( 'Bottom to Top', 'extra' ),
			'fade_in' => esc_html__( 'Fade In', 'extra' ),
			'off'     => esc_html__( 'No Animation', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_divider_style_choices' ) ) :

	/**
 * Returns divider style choices
 * @return array
 */
	function et_extra_divider_style_choices() {
		return apply_filters( 'et_extra_divider_style_choices', array(
			'solid'  => esc_html__( 'Solid', 'extra' ),
			'dotted' => esc_html__( 'Dotted', 'extra' ),
			'dashed' => esc_html__( 'Dashed', 'extra' ),
			'double' => esc_html__( 'Double', 'extra' ),
			'groove' => esc_html__( 'Groove', 'extra' ),
			'ridge'  => esc_html__( 'Ridge', 'extra' ),
			'inset'  => esc_html__( 'Inset', 'extra' ),
			'outset' => esc_html__( 'Outset', 'extra' ),
		) );
	}

endif;

if ( ! function_exists( 'et_extra_divider_position_choices' ) ) :

	/**
 * Returns divider position choices
 * @return array
 */
	function et_extra_divider_position_choices() {
		return apply_filters( 'et_extra_divider_position_choices', array(
			'top'    => esc_html__( 'Top', 'extra' ),
			'center' => esc_html__( 'Vertically Centered', 'extra' ),
			'bottom' => esc_html__( 'Bottom', 'extra' ),
		) );
	}

endif;

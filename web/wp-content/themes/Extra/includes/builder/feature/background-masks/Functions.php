<?php
/**
 * Functions needed for the Background Masks QF.
 *
 * @since 4.15.0
 *
 * @package Divi
 * @subpackage Builder
 */

/**
 * Get background pattern option instance.
 *
 * @since 4.15.0
 *
 * @return ET_Builder_Background_Pattern_Options
 */
function et_pb_background_pattern_options() {
	return ET_Builder_Background_Pattern_Options::get();
}

/**
 * Get background mask option instance.
 *
 * @since 4.15.0
 *
 * @return ET_Builder_Background_Mask_Options
 */
function et_pb_background_mask_options() {
	return ET_Builder_Background_Mask_Options::get();
}

/**
 * Returns Pattern style options.
 *
 * @since 4.15.0
 *
 * @return array
 */
function et_pb_get_pattern_style_options() {
	// Bail, when AJAX isn't calling for Builder Assets/Data.
	if ( wp_doing_ajax() && ! et_fb_is_builder_ajax() ) {
		return array();
	}

	$cache_key = 'et_pb_get_pattern_style_options';

	if ( ! et_core_cache_has( $cache_key ) ) {
		$options  = array();
		$settings = et_pb_background_pattern_options()->settings();

		if ( ! empty( $settings['styles'] ) ) {
			// Get the style names.
			$names = array_keys( $settings['styles'] );

			// Get Label for the styles.
			$labels = array_column( $settings['styles'], 'label' );

			// Prepare the final style options.
			$options = array_combine( $names, $labels );

			// Cleanup.
			$labels   = null;
			$names    = null;
			$settings = null;
		}

		et_core_cache_set( $cache_key, $options );
	} else {
		$options = et_core_cache_get( $cache_key );
	}

	return $options ? $options : array();
}

/**
 * Returns Mask style options.
 *
 * @since 4.15.0
 *
 * @return array
 */
function et_pb_get_mask_style_options() {
	// Bail, when AJAX isn't calling for Builder Assets/Data.
	if ( wp_doing_ajax() && ! et_fb_is_builder_ajax() ) {
		return array();
	}

	$cache_key = 'et_pb_get_mask_style_options';

	if ( ! et_core_cache_has( $cache_key ) ) {
		$options  = array();
		$settings = et_pb_background_mask_options()->settings();

		if ( ! empty( $settings['styles'] ) ) {
			// Get the style names.
			$names = array_keys( $settings['styles'] );

			// Get Label for the styles.
			$labels = array_column( $settings['styles'], 'label' );

			// Prepare the final style options.
			$options = array_combine( $names, $labels );

			// Cleanup.
			$labels   = null;
			$names    = null;
			$settings = null;
		}

		et_core_cache_set( $cache_key, $options );
	} else {
		$options = et_core_cache_get( $cache_key );
	}

	return $options ? $options : array();
}

if ( ! function_exists( 'et_pb_get_background_field_allowed_units' ) ) :
	/**
	 * Return allowed units for width/height/horizontal offset/vertical offset field.
	 *
	 * @since 4.15.0
	 *
	 * @return string[]
	 */
	function et_pb_get_background_field_allowed_units() {
		return array(
			'%',
			'em',
			'rem',
			'px',
			'cm',
			'mm',
			'in',
			'pc',
			'ex',
			'vh',
			'vw',
		);
	}
endif;

if ( ! function_exists( 'et_pb_get_background_blend_mode_options' ) ) :
	/**
	 * Return blend mode options list.
	 *
	 * @since 4.15.0
	 *
	 * @return array
	 */
	function et_pb_get_background_blend_mode_options() {
		return array(
			'normal'      => et_builder_i18n( 'Normal' ),
			'multiply'    => et_builder_i18n( 'Multiply' ),
			'screen'      => et_builder_i18n( 'Screen' ),
			'overlay'     => et_builder_i18n( 'Overlay' ),
			'darken'      => et_builder_i18n( 'Darken' ),
			'lighten'     => et_builder_i18n( 'Lighten' ),
			'color-dodge' => et_builder_i18n( 'Color Dodge' ),
			'color-burn'  => et_builder_i18n( 'Color Burn' ),
			'hard-light'  => et_builder_i18n( 'Hard Light' ),
			'soft-light'  => et_builder_i18n( 'Soft Light' ),
			'difference'  => et_builder_i18n( 'Difference' ),
			'exclusion'   => et_builder_i18n( 'Exclusion' ),
			'hue'         => et_builder_i18n( 'Hue' ),
			'saturation'  => et_builder_i18n( 'Saturation' ),
			'color'       => et_builder_i18n( 'Color' ),
			'luminosity'  => et_builder_i18n( 'Luminosity' ),
		);
	}
endif;

if ( ! function_exists( 'et_pb_get_background_position_options' ) ) :
	/**
	 * Return Background Position options list.
	 *
	 * @since 4.15.0
	 *
	 * @return array
	 */
	function et_pb_get_background_position_options() {
		return array(
			'top_left'      => et_builder_i18n( 'Top Left' ),
			'top_center'    => et_builder_i18n( 'Top Center' ),
			'top_right'     => et_builder_i18n( 'Top Right' ),
			'center_left'   => et_builder_i18n( 'Center Left' ),
			'center'        => et_builder_i18n( 'Center' ),
			'center_right'  => et_builder_i18n( 'Center Right' ),
			'bottom_left'   => et_builder_i18n( 'Bottom Left' ),
			'bottom_center' => et_builder_i18n( 'Bottom Center' ),
			'bottom_right'  => et_builder_i18n( 'Bottom Right' ),
		);
	}
endif;

if ( ! function_exists( 'et_pb_get_background_repeat_options' ) ) :
	/**
	 * Return Background Repeat options list.
	 *
	 * @since 4.15.0
	 *
	 * @param bool $no_repeat Whether to include no-repeat option.
	 *
	 * @return array
	 */
	function et_pb_get_background_repeat_options( $no_repeat = true ) {
		$options = array(
			'repeat'   => et_builder_i18n( 'Repeat' ),
			'repeat-x' => et_builder_i18n( 'Repeat X (horizontal)' ),
			'repeat-y' => et_builder_i18n( 'Repeat Y (vertical)' ),
			'space'    => et_builder_i18n( 'Repeat with space between' ),
			'round'    => et_builder_i18n( 'Repeat and Stretch' ),
		);

		if ( $no_repeat ) {
			$options['no-repeat'] = et_builder_i18n( 'No Repeat' );
		}

		return $options;
	}
endif;

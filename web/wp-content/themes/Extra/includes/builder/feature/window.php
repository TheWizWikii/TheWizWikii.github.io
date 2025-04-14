<?php
/**
 * Function collection related to window.
 *
 * @package     Divi
 * @sub-package Builder
 * @since 4.6.0
 */

/**
 * Get scroll location of all preview mode of all builder context
 * These are sorted by the time it was added to Divi (older to newer)
 *
 * @since 4.6.0
 *
 * @return array
 */
function et_builder_get_builder_scroll_locations() {
	return array(
		// Frontend - what user sees.
		'fe'  => array(
			'desktop' => 'app',
			'tablet'  => 'app',
			'phone'   => 'app',
		),

		// Visual Builder - The most complex one
		// It used to use "faux responsive" while smaller breakpoints are simulated using more
		// specific CSS; but since true responsive which is derivation of BFB is introduced,
		// builder is rendered inside iframe on actual window width; To keep it seamless it needs
		// some trick and switching scroll location, hence these more complex scroll location.
		'vb'  => array(
			'desktop'   => 'app',
			'tablet'    => 'top',
			'phone'     => 'top',
			'zoom'      => 'top',
			'wireframe' => 'app',
		),

		// New Builder Experience - The Backend Frontend Builder (BFB)
		// Loads builder app inside iframe, but need to avoid the iframe having vertical scroll
		// for UX reason. User only need to scroll the main window's scroll hence the builder
		// app is rendered on its 100vh height and all scroll locations are located on top window.
		'bfb' => array(
			'desktop'   => 'top',
			'tablet'    => 'top',
			'phone'     => 'top',
			'zoom'      => 'top',
			'wireframe' => 'top',
		),

		// Theme Builder
		// Builder is rendered on modal with 100vh on app window; all scroll is on top window.
		'tb'  => array(
			'desktop'   => 'top',
			'tablet'    => 'top',
			'phone'     => 'top',
			'zoom'      => 'top',
			'wireframe' => 'top',
		),

		// Layout Block's Builder
		// Reusing theme builder component, hence the shared characteristics.
		'lbb' => array(
			'desktop'   => 'top',
			'tablet'    => 'top',
			'phone'     => 'top',
			'zoom'      => 'top',
			'wireframe' => 'top',
		),

		// Layout Block Preview
		// Preview Layout Block's frontend appearance inside Gutenberg block; similar to BFB but
		// what is being rendered is frontend component. Hence it displays 100vh preview height
		// for UX reason and all scroll happpens in top window.
		'lbp' => array(
			'desktop' => 'top',
			'tablet'  => 'top',
			'phone'   => 'top',
		),
	);
}

/**
 * Get window scroll location
 *
 * @since 4.6.0
 *
 * @return array
 */
function et_builder_get_window_scroll_locations() {
	return array( 'app', 'top' );
}

/**
 * Get current builder type
 *
 * @since 4.6.0
 *
 * @return string app|top
 */
function et_builder_get_current_builder_type() {
	$type = 'fe';

	if ( ET_GB_Block_Layout::is_layout_block_preview() ) {
		$type = 'lbp';

		// Layout Block builder reuses Theme Builder's modal component.
		if ( et_builder_tb_enabled() ) {
			$type = 'lbb';
		}
	} elseif ( et_builder_tb_enabled() ) {
		$type = 'tb';
	} elseif ( et_builder_bfb_enabled() ) {
		$type = 'bfb';
	} elseif ( et_core_is_fb_enabled() ) {
		$type = 'vb';
	}

	return $type;
}

/**
 * Get scroll location on all breakpoints of current builder type
 *
 * @since 4.6.0
 *
 * @return array
 */
function et_builder_get_onload_scroll_locations() {
	$builder_scroll_locations = et_builder_get_builder_scroll_locations();
	$builder_type             = et_builder_get_current_builder_type();

	return et_()->array_get( $builder_scroll_locations, $builder_type, array( 'desktop' => 'app' ) );
}

/**
 * Get on page load scroll location of current builder type
 *
 * @since 4.6.0
 *
 * @return string app|top
 */
function et_builder_get_onload_scroll_location() {
	$builder_scroll_locations = et_builder_get_builder_scroll_locations();
	$builder_type             = et_builder_get_current_builder_type();

	// Default view mode doesn't change and consistent scroll location on all modes / breakpoint.
	if ( in_array( $builder_type, array( 'fe', 'lbp' ), true ) ) {
		return et_()->array_get( $builder_scroll_locations, "{$builder_type}.desktop" );
	}

	// Default view mode might be changed via app preference modal.
	$app_preferences   = et_fb_app_preferences_settings();
	$default_view_mode = et_()->array_get( $app_preferences, 'view_mode.default' );
	$view_mode         = et_get_option( 'et_fb_pref_view_mode', $default_view_mode );

	return et_()->array_get( $builder_scroll_locations, "{$builder_type}.{$view_mode}", 'app' );
}

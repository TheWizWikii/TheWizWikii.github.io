<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Enable LifterLMS support.
 *
 * @since ??
 *
 * @return void
 */
function extra_setup_lifterlms(){
	add_filter( 'llms_get_theme_default_sidebar', 'et_extra_filter_llms_default_sidebar' );

	add_theme_support( 'lifterlms-sidebars' );
}

add_action( 'after_setup_theme', 'extra_setup_lifterlms' );

/**
 * Filter LifterLMS' default sidebar.
 *
 * @since ??
 *
 * @param string $id
 *
 * @return string
 */
function et_extra_filter_llms_default_sidebar( $id ) {
	return 'sidebar-main';
}

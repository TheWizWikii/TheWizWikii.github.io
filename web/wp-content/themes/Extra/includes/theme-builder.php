<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Output the opening page container div.
 *
 * @since ??
 *
 * @return void
 */
function et_extra_filter_theme_builder_template_before_page_wrappers() {
	get_template_part( 'theme-before-wrappers' );
}
add_action( 'et_theme_builder_template_before_page_wrappers', 'et_extra_filter_theme_builder_template_before_page_wrappers' );

/**
 * Output the header if necessary.
 *
 * @since ??
 *
 * @param integer $layout_id
 * @param bool $layout_enabled
 *
 * @return void
 */
function et_extra_filter_theme_builder_template_before_header( $layout_id, $layout_enabled ) {
	if ( 0 === $layout_id && $layout_enabled ) {
		get_template_part( 'theme-header' );
	}
}
add_action( 'et_theme_builder_template_before_header', 'et_extra_filter_theme_builder_template_before_header', 10, 2 );

/**
 * Output the footer if necessary.
 *
 * @since ??
 *
 * @param integer $layout_id
 * @param bool $layout_enabled
 *
 * @return void
 */
function et_extra_filter_theme_builder_template_after_footer( $layout_id, $layout_enabled ) {
	if ( 0 === $layout_id && $layout_enabled ) {
		get_template_part( 'theme-footer' );
	}
}
add_action( 'et_theme_builder_template_after_footer', 'et_extra_filter_theme_builder_template_after_footer', 10, 2 );

/**
 * Output the closing page container div.
 *
 * @since ??
 *
 * @return void
 */
function et_extra_filter_theme_builder_template_after_page_wrappers() {
	get_template_part( 'theme-after-wrappers' );
}
add_action( 'et_theme_builder_template_after_page_wrappers', 'et_extra_filter_theme_builder_template_after_page_wrappers' );

/**
 * Disable TB hooks in order to be compatible with LearnDash's Focus mode.
 *
 * @since ??
 */
function et_extra_action_theme_builder_compatibility_learndash_focus_mode() {
	remove_action( 'et_theme_builder_template_before_page_wrappers', 'et_extra_filter_theme_builder_template_before_page_wrappers' );
	remove_action( 'et_theme_builder_template_before_header', 'et_extra_filter_theme_builder_template_before_header' );
	remove_action( 'et_theme_builder_template_after_footer', 'et_extra_filter_theme_builder_template_after_footer' );
	remove_action( 'et_theme_builder_template_after_page_wrappers', 'et_extra_filter_theme_builder_template_after_page_wrappers' );
}
add_action( 'et_theme_builder_compatibility_learndash_focus_mode', 'et_extra_action_theme_builder_compatibility_learndash_focus_mode' );

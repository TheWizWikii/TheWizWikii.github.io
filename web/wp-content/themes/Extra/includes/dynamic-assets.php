<?php
/**
 * Handles the dynamic assets list logic for Extra theme.
 *
 * @package Extra
 */

/**
 * Gets a list of global asset files.
 *
 * @param array $global_list List of globally needed assets.
 *
 * @since ??
 *
 * @return array
 */
function et_extra_get_global_assets_list( $global_list ) {
	$assets_list          = array();
	$has_tb_header        = false;
	$has_tb_body          = false;
	$has_tb_footer        = false;
	$layouts              = et_theme_builder_get_template_layouts();
	$shared_assets_prefix = get_template_directory() . '/includes/builder/feature/dynamic-assets/assets';

	if ( ! empty( $layouts ) ) {
		if ( $layouts[ ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ]['override'] ) {
			$has_tb_header = true;
		}
		if ( $layouts[ ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ]['override'] ) {
			$has_tb_body = true;
		}
		if ( $layouts[ ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ]['override'] ) {
			$has_tb_footer = true;
		}
	}

	if ( ! $has_tb_header ) {
		$assets_list['et_extra_header'] = array(
			'css' => array(
				"{$shared_assets_prefix}/css/header_animations.css",
				"{$shared_assets_prefix}/css/header_shared.css",
			),
		);
	}

	return array_merge( $global_list, $assets_list );
}

add_filter( 'et_global_assets_list', 'et_extra_get_global_assets_list' );

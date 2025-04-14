<?php
/**
 * Page Type Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Post Type Condition Trait.
 */
trait PostTypeCondition {

	/**
	 * Processes "Post Types" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_post_type_condition( $condition_settings ) {
		// Only check for Posts.
		if ( ! is_singular() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['postTypeDisplay'] ) ? $condition_settings['postTypeDisplay'] : 'is';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$post_types_raw      = isset( $condition_settings['postTypes'] ) ? $condition_settings['postTypes'] : [];
		$post_types_values   = array_map(
			function( $item ) {
				return $item['value'];
			},
			$post_types_raw
		);
		$is_on_shop_page     = class_exists( 'WooCommerce' ) && is_shop();
		$current_queried_id  = $is_on_shop_page ? wc_get_page_id( 'shop' ) : get_queried_object_id();
		$post_type           = get_post_type( $current_queried_id );

		$should_display = array_intersect( $post_types_values, (array) $post_type ) ? true : false;

		return ( 'is' === $display_rule ) ? $should_display : ! $should_display;
	}

}

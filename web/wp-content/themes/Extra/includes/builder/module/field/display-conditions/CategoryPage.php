<?php
/**
 * Category Page condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Category Page Condition Trait.
 */
trait CategoryPageCondition {

	/**
	 * Processes "Category Page" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_category_page_condition( $condition_settings ) {

		// Only check for Archive pages.
		if ( ! is_archive() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule     = isset( $condition_settings['categoryPageDisplay'] ) ? $condition_settings['categoryPageDisplay'] : 'is';
		$display_rule            = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$categories_raw          = isset( $condition_settings['categories'] ) ? $condition_settings['categories'] : [];
		$queried_object          = get_queried_object();
		$is_queried_object_valid = $queried_object instanceof \WP_Term && property_exists( $queried_object, 'taxonomy' );

		if ( ! $is_queried_object_valid ) {
			return false;
		}

		$queried_taxonomy             = $queried_object->taxonomy;
		$categories_ids               = array_map(
			function( $item ) {
				return $item['value'];
			},
			$categories_raw
		);
		$tax_slugs_of_catch_all_items = [];
		$is_any_catch_all_selected    = false;
		foreach ( $categories_raw as $item ) {
			if ( true === $item['isCatchAll'] ) {
				$tax_slugs_of_catch_all_items[] = $item['groupSlug'];
				$is_any_catch_all_selected      = true;
			}
		}

		// Logic evaluation.
		$current_category_id = get_queried_object_id();
		$is_displayable      = array_intersect( $categories_ids, (array) $current_category_id ) ? true : false;

		if ( ! $is_displayable && $is_any_catch_all_selected ) {
			$is_displayable = array_intersect( $tax_slugs_of_catch_all_items, (array) $queried_taxonomy ) ? true : false;
		}

		// Evaluation output.
		return ( 'is' === $display_rule ) ? $is_displayable : ! $is_displayable;

	}

}

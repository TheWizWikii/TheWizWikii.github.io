<?php
/**
 * Tag Page condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Tag Page Condition Trait.
 */
trait TagPageCondition {

	/**
	 * Processes "Tag Page" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_tag_page_condition( $condition_settings ) {

		// Only check for Archive pages.
		if ( ! is_archive() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule     = isset( $condition_settings['tagPageDisplay'] ) ? $condition_settings['tagPageDisplay'] : 'is';
		$display_rule            = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$tags_raw                = isset( $condition_settings['tags'] ) ? $condition_settings['tags'] : [];
		$queried_object          = get_queried_object();
		$is_queried_object_valid = $queried_object instanceof \WP_Term && property_exists( $queried_object, 'taxonomy' );

		if ( ! $is_queried_object_valid ) {
			return false;
		}

		$queried_taxonomy             = $queried_object->taxonomy;
		$tags_raw_ids                 = array_map(
			function( $item ) {
				return $item['value'];
			},
			$tags_raw
		);
		$tax_slugs_of_catch_all_items = [];
		$is_any_catch_all_selected    = false;
		foreach ( $tags_raw as $item ) {
			if ( true === $item['isCatchAll'] ) {
				$tax_slugs_of_catch_all_items[] = $item['groupSlug'];
				$is_any_catch_all_selected      = true;
			}
		}

		// Logic evaluation.
		$current_tag_id = get_queried_object_id();
		$is_displayable = array_intersect( $tags_raw_ids, (array) $current_tag_id ) ? true : false;

		if ( ! $is_displayable && $is_any_catch_all_selected ) {
			$is_displayable = array_intersect( $tax_slugs_of_catch_all_items, (array) $queried_taxonomy ) ? true : false;
		}

		// Evaluation output.
		return ( 'is' === $display_rule ) ? $is_displayable : ! $is_displayable;

	}

}

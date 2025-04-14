<?php
/**
 * Categories condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Categories Condition Trait.
 */
trait CategoriesCondition {

	/**
	 * Processes "Categories" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all condition settings.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_categories_condition( $condition_settings ) {

		// Only check for Posts.
		if ( ! is_singular() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule          = isset( $condition_settings['categoriesDisplay'] ) ? $condition_settings['categoriesDisplay'] : 'is';
		$display_rule                 = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$categories_raw               = isset( $condition_settings['categories'] ) ? $condition_settings['categories'] : [];
		$categories                   = array_map(
			function( $item ) {
				return (object) [
					'id'            => $item['value'],
					'taxonomy_slug' => $item['groupSlug'],
				];
			},
			$categories_raw
		);
		$current_queried_id           = get_queried_object_id();
		$has_post_specified_term      = false;
		$tax_slugs_of_catch_all_items = [];
		$is_any_catch_all_selected    = false;
		$has_post_specified_taxonomy  = false;

		// Logic evaluation.
		foreach ( $categories_raw as $item ) {
			if ( true === $item['isCatchAll'] ) {
				$tax_slugs_of_catch_all_items[] = $item['groupSlug'];
				$is_any_catch_all_selected      = true;
			}
		}

		foreach ( $categories as $cat ) {
			if ( has_term( $cat->id, $cat->taxonomy_slug, $current_queried_id ) ) {
				$has_post_specified_term = true;
				break;
			}
		}

		$is_displayable = $has_post_specified_term ? true : false;

		if ( ! $is_displayable && $is_any_catch_all_selected ) {
			foreach ( $tax_slugs_of_catch_all_items as $tax_slug ) {
				$has_post_specified_taxonomy = has_term( '', $tax_slug, $current_queried_id );
				if ( $has_post_specified_taxonomy ) {
					break;
				}
			}

			$is_displayable = $has_post_specified_taxonomy ? true : false;
		}

		// Evaluation output.
		return ( 'is' === $display_rule ) ? $is_displayable : ! $is_displayable;

	}

}

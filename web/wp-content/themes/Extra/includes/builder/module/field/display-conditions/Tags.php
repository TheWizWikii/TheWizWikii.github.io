<?php
/**
 * Tags Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Tags Condition Trait.
 */
trait TagsCondition {

	/**
	 * Processes "Tags" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_tags_condition( $condition_settings ) {

		// Only check for Posts.
		if ( ! is_singular() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule          = isset( $condition_settings['tagsDisplay'] ) ? $condition_settings['tagsDisplay'] : 'is';
		$display_rule                 = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$tags_raw                     = isset( $condition_settings['tags'] ) ? $condition_settings['tags'] : [];
		$tags                         = array_map(
			function( $item ) {
				return (object) [
					'id'            => $item['value'],
					'taxonomy_slug' => $item['groupSlug'],
				];
			},
			$tags_raw
		);
		$current_queried_id           = get_queried_object_id();
		$has_post_specified_term      = false;
		$tax_slugs_of_catch_all_items = [];
		$is_any_catch_all_selected    = false;
		$has_post_specified_taxonomy  = false;

		// Logic evaluation.
		foreach ( $tags_raw as $item ) {
			if ( true === $item['isCatchAll'] ) {
				$tax_slugs_of_catch_all_items[] = $item['groupSlug'];
				$is_any_catch_all_selected      = true;
			}
		}

		foreach ( $tags as $tag ) {
			if ( has_term( $tag->id, $tag->taxonomy_slug, $current_queried_id ) ) {
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

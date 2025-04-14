<?php
/**
 * Author Condition's logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Author Condition Trait
 */
trait AuthorCondition {

	/**
	 * Processes "Author" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all condition settings.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_author_condition( $condition_settings ) {
		// Only check for Posts.
		if ( ! is_singular() ) {
			return false;
		}

		$legacy_display_rule    = isset( $condition_settings['authorDisplay'] ) ? $condition_settings['authorDisplay'] : 'is';
		$display_rule           = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$authors_raw            = isset( $condition_settings['authors'] ) ? $condition_settings['authors'] : [];
		$authors_ids            = array_map(
			function( $item ) {
				return $item['value'];
			},
			$authors_raw
		);
		$is_on_shop_page        = class_exists( 'WooCommerce' ) && is_shop();
		$queried_object_id      = $is_on_shop_page ? wc_get_page_id( 'shop' ) : get_queried_object_id();
		$current_post_author_id = get_post_field( 'post_author', (int) $queried_object_id );

		$should_display = array_intersect( $authors_ids, (array) $current_post_author_id ) ? true : false;

		return ( 'is' === $display_rule ) ? $should_display : ! $should_display;
	}

}

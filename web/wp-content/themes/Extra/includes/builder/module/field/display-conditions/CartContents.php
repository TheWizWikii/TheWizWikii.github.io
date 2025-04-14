<?php
/**
 * Cart Contents condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Cart Contents Condition Trait.
 */
trait CartContentsCondition {

	/**
	 * Processes "Cart Contents" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_cart_contents_condition( $condition_settings ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['cartContentsDisplay'] ) ? $condition_settings['cartContentsDisplay'] : 'hasProducts';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$products_raw        = isset( $condition_settings['products'] ) ? $condition_settings['products'] : [];
		$products_ids        = array_map(
			function( $item ) {
				return isset( $item['value'] ) ? $item['value'] : '';
			},
			$products_raw
		);
		$is_cart_empty       = WC()->cart->is_empty();

		switch ( $display_rule ) {
			case 'hasProducts':
				return ! $is_cart_empty;

			case 'isEmpty':
				return $is_cart_empty;

			case 'hasSpecificProduct':
				return $this->_has_specific_product_in_cart( $products_ids );

			case 'doesNotHaveSpecificProduct':
				return ! $this->_has_specific_product_in_cart( $products_ids );

			default:
				return false;
		}
	}

	/**
	 * Checks presence of specified products in the Cart.
	 *
	 * @param Array $products_ids Array of products IDs to check against the Cart's products.
	 * @return boolean Indicating the presence of specified products in the Cart.
	 */
	protected function _has_specific_product_in_cart( $products_ids ) {
		$has_specific_product = false;
		if ( ! WC()->cart->is_empty() ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$cart_item_ids = [ $cart_item['product_id'], $cart_item['variation_id'] ];
				if ( array_intersect( $products_ids, $cart_item_ids ) ) {
					$has_specific_product = true;
					break;
				}
			}
		}
		return $has_specific_product;
	}

}

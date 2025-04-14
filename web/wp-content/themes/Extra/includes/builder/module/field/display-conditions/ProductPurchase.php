<?php
/**
 * Product Purchase Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Product Purchase Condition Trait.
 */
trait ProductPurchaseCondition {

	/**
	 * Processes "Product Purchase" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_product_purchase_condition( $condition_settings ) {
		if ( ! class_exists( 'WooCommerce' ) || ! is_user_logged_in() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['productPurchaseDisplay'] ) ? $condition_settings['productPurchaseDisplay'] : 'hasBoughtProduct';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$products_raw        = isset( $condition_settings['products'] ) ? $condition_settings['products'] : [];
		$current_user        = wp_get_current_user();
		$products_ids        = array_map(
			function( $item ) {
				return isset( $item['value'] ) ? $item['value'] : '';
			},
			$products_raw
		);

		switch ( $display_rule ) {
			case 'hasBoughtProduct':
				$has_bought_product = $this->_has_user_bought_any_product( $current_user->ID );
				return $has_bought_product;

			case 'hasNotBoughtProduct':
				$has_bought_product = $this->_has_user_bought_any_product( $current_user->ID );
				return ! $has_bought_product;

			case 'hasBoughtSpecificProduct':
				return $this->_has_user_bought_specific_product( $current_user, $products_ids );

			case 'hasNotBoughtSpecificProduct':
				return ! $this->_has_user_bought_specific_product( $current_user, $products_ids );

			default:
				return false;
		}
	}

	/**
	 * Checks whether `$current_user` has bought any specified products.
	 *
	 * @param  WP_User $current_user Current user object.
	 * @param  array   $products_ids List of specefied product IDs.
	 *
	 * @return boolean Returns true if `$current_user` has bought any specified products, False otherwise.
	 */
	protected function _has_user_bought_specific_product( $current_user, $products_ids ) {
		$has_bought_specific_product = false;

		foreach ( $products_ids as $product_id ) {
			$has_bought_specific_product = wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id );
			if ( $has_bought_specific_product ) {
				break;
			}
		}

		return $has_bought_specific_product;
	}

	/**
	 * Checks if `$user_id` has bought any product in WooCommerce.
	 *
	 * @param integer $user_id WordPress User ID.
	 *
	 * @return boolean Returns true if `$user_id` has any paid order, False otherwise.
	 */
	protected function _has_user_bought_any_product( $user_id = 0 ) {
		if ( ! class_exists( 'WooCommerce' ) || ! $user_id || ! is_numeric( $user_id ) ) {
			return false;
		}

		$paid_statuses = wc_get_is_paid_statuses();

		$orders = wc_get_orders(
			[
				'limit'       => 1,
				'status'      => $paid_statuses,
				'customer_id' => (int) $user_id,
				'return'      => 'ids',
			]
		);

		return count( $orders ) > 0 ? true : false;
	}

}

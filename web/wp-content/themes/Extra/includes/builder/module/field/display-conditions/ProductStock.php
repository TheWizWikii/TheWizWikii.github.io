<?php
/**
 * Product Stock Condition logic swiftly crafted.
 *
 * @since 4.14.3
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Product Stock Condition Trait.
 */
trait ProductStockCondition {

	/**
	 * Processes "Product Stock" condition.
	 *
	 * @since 4.14.3
	 *
	 * @param  array $condition_settings The Condition settings containing:
	 *                                   'displayRule' => string,
	 *                                   'products' => array.
	 *
	 * @return boolean Returns `true` if the condition evaluation is true, `false` otherwise.
	 */
	protected function _process_product_stock_condition( $condition_settings ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		$display_rule = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : 'isInStock';
		$products_raw = isset( $condition_settings['products'] ) ? $condition_settings['products'] : [];
		$products_ids = array_map(
			function( $item ) {
				return isset( $item['value'] ) ? (int) $item['value'] : '';
			},
			$products_raw
		);
		$products_ids = array_filter( $products_ids );

		$products = wc_get_products(
			[
				'limit'        => -1,
				'include'      => $products_ids,
				'stock_status' => 'instock',
				'return'       => 'ids',
			]
		);

		$output = [
			'isInStock'    => count( $products ) > 0,
			'isOutOfStock' => count( $products ) === 0,
		];

		return isset( $output[ $display_rule ] ) ? $output[ $display_rule ] : false;
	}

}

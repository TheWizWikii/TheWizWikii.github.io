<?php

/**
 * Class ET_Theme_Builder_Woocommerce_Product_Variation_Placeholder
 *
 * Display variation (child of variable) placeholder product on Theme Builder. This needs to be
 * explicitly defined in case WC add-ons relies on any of variation's method.
 */
class ET_Theme_Builder_Woocommerce_Product_Variation_Placeholder extends WC_Product_Variation {
	/**
	 * Get internal type.
	 * Define custom internal type so custom data store can be used to bypass database value retrieval
	 *
	 * @since 4.3.3
	 *
	 * @return string
	 */
	public function get_type() {
		return 'tb-placeholder-variation';
	}
}

<?php
/**
 * Simple Product Placeholder.
 *
 * @package Builder.
 */

/**
 * Class ET_Builder_Woocommerce_Product_Simple_Placeholder
 */
class ET_Builder_Woocommerce_Product_Simple_Placeholder extends WC_Product_Simple {
	/**
	 * Create pre-filled WC Product (variable) object which acts as placeholder generator in TB
	 *
	 * @since 4.0.10 Instead of empty product object that is set later, pre-filled default data properties
	 *
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product = 0 ) {
		/*
		 * Pre-filled default data with placeholder value so everytime this product class is
		 * initialized, it already has sufficient data to be displayed on Theme Builder.
		 */
		$this->data = array(
			'name'               => esc_html__( 'Product 1', 'et_builder' ),
			'slug'               => 'product-1',
			'date_created'       => current_time( 'timestamp' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- Timestamp is used only for reference.
			'date_modified'      => null,
			'status'             => 'publish',
			'featured'           => false,
			'catalog_visibility' => 'visible',
			'description'        => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris bibendum eget dui sed vehicula. Suspendisse potenti. Nam dignissim at elit non lobortis. Cras sagittis dui diam, a finibus nibh euismod vestibulum. Integer sed blandit felis. Maecenas commodo ante in mi ultricies euismod. Morbi condimentum interdum luctus. Mauris iaculis interdum risus in volutpat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Praesent cursus odio eget cursus pharetra. Aliquam lacinia lectus a nibh ullamcorper maximus. Quisque at sapien pulvinar, dictum elit a, bibendum massa. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris non pellentesque urna.', 'et_builder' ),
			'short_description'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris bibendum eget dui sed vehicula. Suspendisse potenti. Nam dignissim at elit non lobortis.', 'et_builder' ),
			'sku'                => 'product-name',
			'price'              => '12',
			'date_on_sale_from'  => null,
			'date_on_sale_to'    => null,
			'total_sales'        => '0',
			'tax_status'         => 'taxable',
			'tax_class'          => '',
			'manage_stock'       => true,
			'stock_quantity'     => 50,
			'stock_status'       => 'instock',
			'backorders'         => 'no',
			'low_stock_amount'   => 2,
			'sold_individually'  => false,
			'weight'             => 2,
			'length'             => '',
			'width'              => 2,
			'height'             => 2,
			'upsell_ids'         => array(),
			'cross_sell_ids'     => array(),
			'parent_id'          => 0,
			'reviews_allowed'    => true,
			'purchase_note'      => '',
			'attributes'         => array(),
			'default_attributes' => array(),
			'menu_order'         => 0,
			'post_password'      => '',
			'virtual'            => false,
			'downloadable'       => false,
			'category_ids'       => array(),
			'tag_ids'            => array(),
			'shipping_class_id'  => 0,
			'downloads'          => array(),
			'image_id'           => '',
			'gallery_image_ids'  => array(),
			'download_limit'     => -1,
			'download_expiry'    => -1,
			'rating_counts'      => array(
				4 => 2,
			),
			'average_rating'     => '4.00',
			'review_count'       => 2,
			'recent_product_ids' => null,
		);

		parent::__construct( $product );
	}

	// phpcs:disable Generic.Commenting.DocComment.MissingShort -- Avoiding repetition.
	// phpcs:disable Squiz.Commenting.FunctionComment.Missing -- Avoiding repetition.
	// phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag -- Avoiding repetition.
	/**
	 * @inheritDoc
	 */
	public function get_image( $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
		return et_builder_wc_placeholder_img();
	}
	// phpcs:enable Generic.Commenting.DocComment.MissingShort
	// phpcs:enable Squiz.Commenting.FunctionComment.Missing
	// phpcs:enable Squiz.Commenting.FunctionComment.MissingParamTag
}

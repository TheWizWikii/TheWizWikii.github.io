<?php

/**
 * Class ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder
 *
 * Variable product class extension for displaying WooCommerce placeholder on Theme Builder
 */
class ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder extends WC_Product_Variable {
	/**
	 * Cached upsells id
	 *
	 * @since 4.0.10
	 *
	 * @var array
	 */
	protected static $tb_upsells_id;

	/**
	 * Cached product category ids
	 *
	 * @since 4.0.10
	 *
	 * @var array
	 */
	protected static $tb_category_ids;

	/**
	 * Cached product tag ids
	 *
	 * @since 4.0.10
	 *
	 * @var array
	 */
	protected static $tb_tag_ids;

	/**
	 * Cached attributes
	 *
	 * @since 4.0.10
	 *
	 * @var array
	 */
	protected static $tb_attributes;

	/**
	 * Create pre-filled WC Product (variable) object which acts as placeholder generator in TB
	 *
	 * @since 4.0.10 Instead of empty product object that is set later, pre-filled default data properties
	 *
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product = 0 ) {
		// Pre-filled default data with placeholder value so everytime this product class is
		// initialized, it already has sufficient data to be displayed on Theme Builder
		$this->data = array(
			'name'               => esc_html( 'Product name', 'et_builder' ),
			'slug'               => 'product-name',
			'date_created'       => current_time( 'timestamp' ),
			'date_modified'      => null,
			'status'             => 'publish',
			'featured'           => false,
			'catalog_visibility' => 'visible',
			'description'        => esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris bibendum eget dui sed vehicula. Suspendisse potenti. Nam dignissim at elit non lobortis. Cras sagittis dui diam, a finibus nibh euismod vestibulum. Integer sed blandit felis. Maecenas commodo ante in mi ultricies euismod. Morbi condimentum interdum luctus. Mauris iaculis interdum risus in volutpat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Praesent cursus odio eget cursus pharetra. Aliquam lacinia lectus a nibh ullamcorper maximus. Quisque at sapien pulvinar, dictum elit a, bibendum massa. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris non pellentesque urna.', 'et_builder' ),
			'short_description'  => esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris bibendum eget dui sed vehicula. Suspendisse potenti. Nam dignissim at elit non lobortis.', 'et_builder' ),
			'sku'                => 'product-name',
			'price'              => '75',
			'regular_price'      => '80',
			'sale_price'         => '65',
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

	/**
	 * Get internal type.
	 * Define custom internal type so custom data store can be used to bypass database value retrieval
	 *
	 * @since 4.0.10
	 *
	 * @return string
	 */
	public function get_type() {
		return 'tb-placeholder';
	}

	/**
	 * Get placeholder product as available variation. The method is basically identical to
	 * `WC_Product_Variable->get_available_variation()` except for the checks which are removed
	 * so placeholder value can be passed
	 *
	 * @since 4.3.3
	 *
	 * @param int|object $variation not needed since it will be overwritten by placeholder variation
	 *                   but it needs to be kept for compatibility with base class' method
	 *
	 * @return array
	 */
	function get_available_variation( $variation = 0 ) {
		$variation            = new ET_Theme_Builder_Woocommerce_Product_Variation_Placeholder();
		$show_variation_price = apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === '' || $this->get_variation_sale_price( 'min' ) !== $this->get_variation_sale_price( 'max' ) || $this->get_variation_regular_price( 'min' ) !== $this->get_variation_regular_price( 'max' ), $this, $variation );

		// Set variation id; Prevent $product->get_id() returns falsey which usually triggers wc_product_get()
		// in WC add ons; Valid $product->get_id() makes global $product being used most of the time
		$variation->set_id( $this->get_id() );

		// Set current product id as variation parent id so $product->get_parent_id() returns
		// valid value (mostly when being called by WC add-ons). The absence of this value (in TB)
		// triggers new `wc_get_product()` which most likely returned unwanted output
		$variation->set_prop( 'parent_id', $this->get_id() );

		// Returned array properties are identical to `WC_Product_Variable->get_available_variation()`
		return apply_filters(
			'woocommerce_available_variation',
			array(
				'attributes'            => $variation->get_variation_attributes(),
				'availability_html'     => wc_get_stock_html( $variation ),
				'backorders_allowed'    => $variation->backorders_allowed(),
				'dimensions'            => $variation->get_dimensions( false ),
				'dimensions_html'       => wc_format_dimensions( $variation->get_dimensions( false ) ),
				'display_price'         => wc_get_price_to_display( $variation ),
				'display_regular_price' => wc_get_price_to_display( $variation, array( 'price' => $variation->get_regular_price() ) ),
				'image'                 => wc_get_product_attachment_props( $variation->get_image_id() ),
				'image_id'              => $variation->get_image_id(),
				'is_downloadable'       => $variation->is_downloadable(),
				'is_in_stock'           => $variation->is_in_stock(),
				'is_purchasable'        => $variation->is_purchasable(),
				'is_sold_individually'  => $variation->is_sold_individually() ? 'yes' : 'no',
				'is_virtual'            => $variation->is_virtual(),
				'max_qty'               => 0 < $variation->get_max_purchase_quantity() ? $variation->get_max_purchase_quantity() : '',
				'min_qty'               => $variation->get_min_purchase_quantity(),
				'price_html'            => $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
				'sku'                   => $variation->get_sku(),
				'variation_description' => wc_format_content( $variation->get_description() ),
				'variation_id'          => $variation->get_id(),
				'variation_is_active'   => $variation->variation_is_active(),
				'variation_is_visible'  => $variation->variation_is_visible(),
				'weight'                => $variation->get_weight(),
				'weight_html'           => wc_format_weight( $variation->get_weight() ),
			),
			$this,
			$variation
		);
	}

	/**
	 * Add to cart's <select> requires variable product type and get_available_variations() method
	 * outputting product->children value. Filtering get_available_variations() can't be done so
	 * extending WC_Product_Variable and set fixed value for get_available_variations() method
	 *
	 * @since 4.5.7 Introduced $return arg to fix compatibility issue {@link https://github.com/elegantthemes/Divi/issues/20985}
	 * @since 4.3.3 `Replaced ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder` with
	 *              `ET_Theme_Builder_Woocommerce_Product_Variation_Placeholder` (which is now
	 *              called at `get_available_variations()` method and similar to
	 *              `WC_Product_Variation`'s method with no check). It has all variation-required
	 *              methods and properties which makes it more reliable when WC add-ons are used
	 * @since 4.0.1
	 *
	 * @return array
	 */
	public function get_available_variations( $return = 'array' ) {
		return array(
			$this->get_available_variation(),
		);
	}

	/**
	 * Display Divi's placeholder image in WC image in TB
	 *
	 * @since 4.0.10
	 *
	 * @param string not used but need to be declared to prevent incompatible declaration error
	 * @param array  not used but need to be declared to prevent incompatible declaration error
	 * @param bool   not used but need to be declared to prevent incompatible declaration error
	 *
	 * @return string
	 */
	public function get_image( $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
		return et_builder_wc_placeholder_img();
	}

	/**
	 * Set product upsells id for TB's woocommerceComponent. This can't be called during class
	 * initialization and need to be called BEFORE `woocommerce_product_class` filter callback
	 * to avoid infinite loop
	 *
	 * @since 4.0.10
	 *
	 * @param array $args
	 */
	public static function set_tb_upsells_ids( $args = array() ) {
		$defaults = array(
			'limit' => 4,
		);
		$args     = wp_parse_args( $args, $defaults );

		// Get recent products for upsells product; Any product will do since its purpose is
		// for visual preview only
		$recent_products_query = new WC_Product_Query( $args );
		$recent_product_ids    = array();

		foreach ( $recent_products_query->get_products() as $recent_product ) {
			$recent_product_ids[] = $recent_product->get_id();
		}

		// Set up upsells id product
		self::$tb_upsells_id = $recent_product_ids;
	}

	/**
	 * Get upsells id
	 *
	 * @since 4.0.10
	 *
	 * @param string not used but need to be declared to prevent incompatible declaration error
	 *
	 * @return array
	 */
	public function get_upsell_ids( $context = 'view' ) {
		// Bypass database value retrieval and simply pulled cached value from property
		return is_array( self::$tb_upsells_id ) ? self::$tb_upsells_id : array();
	}

	/**
	 * Get attributes
	 *
	 * @since 4.0.10
	 *
	 * @param string not used but need to be declared to prevent incompatible declaration error
	 *
	 * @return array
	 */
	public function get_attributes( $context = 'view' ) {
		if ( ! is_null( self::$tb_attributes ) ) {
			return self::$tb_attributes;
		}

		// Initialize color attribute
		$colors = new WC_Product_Attribute();
		$colors->set_id( 1 );
		$colors->set_name( 'color' );
		$colors->set_options( array( 'Black', 'White', 'Gray' ) );
		$colors->set_position( 1 );
		$colors->set_visible( 1 );
		$colors->set_variation( 1 );

		// Initialize size attribute
		$sizes = new WC_Product_Attribute();
		$sizes->set_id( 2 );
		$sizes->set_name( 'size' );
		$sizes->set_options( array( 'S', 'M', 'L', 'XL' ) );
		$sizes->set_position( 1 );
		$sizes->set_visible( 1 );
		$sizes->set_variation( 1 );

		self::$tb_attributes = array(
			'pa_color' => $colors,
			'pa_size'  => $sizes,
		);

		return self::$tb_attributes;
	}

	/**
	 * Get variation price
	 *
	 * @since 4.0.10
	 *
	 * @param bool not used but need to be declared to prevent incompatible declaration error
	 *
	 * @return array
	 */
	public function get_variation_prices( $for_display = false ) {
		return array(
			'price'         => array( $this->data['price'] ),
			'regular_price' => array( $this->data['regular_price'] ),
			'sale_price'    => array( $this->data['sale_price'] ),
		);
	}
}

/**
 * Render default product variable add to cart UI for tb-placeholder product type
 *
 * @since 4.0.10
 */
add_action( 'woocommerce_tb-placeholder_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );

